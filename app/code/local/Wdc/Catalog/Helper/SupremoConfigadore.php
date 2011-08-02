<?php
class Wdc_Catalog_Helper_SupremoConfigadore extends Mage_Core_Helper_Abstract
{	
	protected function setOptionsBlock()
	{
		return new Wdc_Catalog_Block_Options();
	}	
	
	protected function setConfigurableBlock()
	{
		return new Wdc_Catalog_Block_Configurable();	
	}
	
	protected function setOptionHelper()
	{
		return new Wdc_Catalog_Helper_OptionsHtml();	
	}
	
	protected function setJsonHelper()
	{
		return new Wdc_Catalog_Helper_JsonCoder();
	}
		
	/**
	 * This is method setFormTag
	 *
	 * @param int $productId Product Entity Id
	 * @return array Set the Form tag for either the options product or configurable
	 *
	 */
	protected function setFormTag($productId, $off=false)
	{
		
//		if(!$this->setOptionHelper()->isGrouped($productId))
//		{
			if($this->setOptionsBlock()->isOption($productId) || $this->setOptionsBlock()->getProductAttributeCount($productId) > 1 || $off)
			{
				
				$html1= '<form action="/checkout/cart/add/product/'.$productId.'/" method="post" id="product_addtocart_form" enctype="multipart/form-data">';
				//$html2= $this->setCartButton($productId, false);
				$html2= '</form>';
			}
			else
			{
				//$html1= 'This is the other tag<form method="get">';
				$html1='';	
				//$html2= $this->setCartButton($productId, true);
				$html2= '</form>';
			}
			
		//return array('','');
			
			$var = array($html1, $html2);			
			
			return $var;
//		}		
	}
				
	protected function checkPageType($type)
	{
		switch($type)
		{
			case 1;
				return false;
				break;
			default:
				return true;
				break;					
		}	
	}
	
	public function drawOptionsContainer($productId, $isList=true, $pageType=0, $hasOptions=true)
	{
		if($hasOptions)
		{
			$button = $this->checkPageType($pageType);			
			$html = '';
			$formTag = $this->setFormTag($productId);
			
			$html.= $formTag[0];
			
			$optionBlock = $this->setOptionsBlock();
			$ptype = $this->getPtype($isList);		
			
			// This is the Configurable section, all code goes into here
			$atricount = $optionBlock->getProductAttributeCount($productId);
			
			if($atricount != 0)
			{
				
				// If there is only one configurable, it will go here
				if($atricount == 1)
				{
					$html.= $this->getConfigurableDropdownBlock($productId);
				}
				else
				{
					$html.=$this->getConfigurableDropdownMultiBlock($productId, $atricount);
					
				}
			}
			else{
				// This is the option section, all option code goes here
				// This is for Options only without configurables
				$opCount = $optionBlock->getCatalogProductOptionCount($productId);			
				if($opCount != 0)
				{
					$Wdc_OptionHelper = new Wdc_Catalog_Helper_OptionsHtml();
					
					if($opCount ==1)
					{
						$html.= $Wdc_OptionHelper->OptionsContainer($productId, true, $button);
						$html.= $this->setPriceAjaxBox($productId, true);
					}
					else
					{
						$html.= $Wdc_OptionHelper->OptionsContainer($productId, false, $button);	
					}
				}
			}
			$html.= $formTag[1];
			return $html;	
		}	
	}	
	
	protected function getConfigurableDropdownMultiBlock($productId, $configCount=1)
	{		
		$html = $this->setConfigurableBlock()->decorateProductLinksJson($productId);
		$html.= '<div id="OptionBlockWrapper'.$productId.'">';
		
		$rowCount = 1;
		
		$boxCount = count($this->setOptionsBlock()->getCatalogProductSuperAttribute($productId));
		
		foreach ($this->setOptionsBlock()->getCatalogProductSuperAttribute($productId) as $config)
		{		
			
			$labelArray = array($rowCount => $this->setOptionsBlock()->geteavAttributeLabel($config['attribute_id']));
			$html.= '<div class="block-config-visible" id="AttributeBlock'.$config['attribute_id'].'">';
			$html.= '<h5>'.$labelArray[$rowCount].'</h5>';
						
			if($rowCount == $boxCount)
				{
				$last = true;	
				}
			else
				{
				$last = false;	
				}
			
			$html.= $this->getConfigurableDropdown($productId, $config['attribute_id'], $rowCount, $last); 
			$html.= $this->setJsonHelper()->WdcJsonConfigurableEncode($productId, $config['attribute_id']);
			 
			
			$html.= '</div>';
			$html.= '<div class="block-config-hidden" id="AttributeBlock'.$config['attribute_id'].'">';
			$html.= 'SUPER TEST';
			$html.= '</div>';
			if($rowCount == $configCount)
			{
				$html.= $this->setPriceAjaxBox($productId);	
				$html.= $this->setCartButton($productId, true);	
			}
			else
			{
				$rowCount++;
			}
		}
		
		$html.='</div>';
		return $html;
	}
	
	protected function getConfigurableDropdownBlock($productId, $isList=true)
	{
		$ptype = $this->getPtype($isList);		
		$html = ''; 		
			
		$html.= $this->createDropDownContainer($productId, $ptype);				
		
		//$html.='<div id="txtHint'.$productId.'"><b>Prices starting at $'.number_format($this->setOptionsBlock()->getLowPrice($productId), 2, '.', '').'</b></div>';
		
		$html.= $this->setPriceAjaxBox($productId);
		
		return $html;
	}
	
	public function setCartButton($productId, $configUnique=false)
	{
		
		$button = '<p><input type="submit" value="Add to Cart"></p>';
		
		if($configUnique)
		{
			//return '<p><button class="button" onclick="setLocation(\'/checkout/cart/add/uenc/product/'.$productId.'/?product='.$productId.'&options[10]=21&option_ids=10&super_attribute[928]=126\')"><span> Add to Cart</span></button></p>';
			$button = '<p><button class="button" onclick="setLocation(\'/checkout/cart/add/product/'.$productId.'/\')"><span> Add to Cart</span></button></p>';
		}
		
		return $button;
	}	
	
	protected function setPriceAjaxBox($productId, $optionUnique=false)
	{
		$html = '';
		if($optionUnique)
			{
			$html.= '<b>Base Price $'.number_format($this->setOptionsBlock()->getPrice($productId), 2, '.', '').'</b>';	
			}
		else{
			if($this->setOptionsBlock()->getLowPrice($productId) != 0)
			{			
				$html.='<div id="txtHint'.$productId.'"><b>Prices starting at $'.number_format($this->setOptionsBlock()->getLowPrice($productId), 2, '.', '').'</b></div>';
			}
			else
			{
				$html.= '<div id="txtHint'.$productId.'"><b>Configure your product for pricing</b></div>';		
			}	
		}
		return $html;	
	}
		
	/**
	 * This is method drawDropDowns
	 *
	 * @param int $productId Product entity Id
	 * @param bool $isList Used for AJAX call 
	 * @return html Formated HTML block
	 *
	 */	
	public function drawDropDowns($productId, $isList=true)
	{	
		$productObj = new Wdc_Catalog_Block_Product();
				
		$ptype = $this->getPtype($isList);		
		$html = ''; 
				
		/*if($productObj->isProductAttributeUnique($productId))
		{*/
			
			$op = false;
			if($this->setOptionsBlock()->isOption($productId))
			{
				$html = '<form action="/checkout/cart/add/product/'.$productId.'/" method="post" id="product_addtocart_form" enctype="multipart/form-data">';
				$op = true;
			}
			
			$html.= $this->createDropDownContainer($productId, $ptype);				
						
			$html.='<div id="txtHint'.$productId.'"><b>Prices starting at $'.number_format($productObj->getLowPrice($productId), 2, '.', '').'</b></div>';
			
			/*if($op)
			{
				$html.='</form>';
			}*/	
		/*}
		else
		{			
			$html = '<p><button class="button" onclick="setLocation(\'/';
			$html.=$productObj->getProductUrl($productId).'\')">';
			$html.='<span> Configure Options</span></button></p>';
			$html.= $this->createDropDownContainer($productId, $ptype, true);
		}*/
		return $html;
	}
	
	protected function getPtype($isList)
	{
		if($isList)
		{
			$ptype = 1;	
		}
		else
		{
			$ptype = 0;	
		}	
		
		return $ptype;
	}
		
	/**
	 * This is method createDropDownContainer
	 *
	 * @param int $productId ProductId
	 * @param mixed $ptype I really can't remember right now 071509 18:20
	 * $multi bool This is to tell if the container is multi part or not
	 *   at the moment it is all set to NOT.
	 * @return mixed Completed Dropdown list.
	 *
	 */
	protected function createDropDownContainer($productId, $ptype, $attributeId=0)
	{
		$html = '';
		if($attributeId ==0)
		{
			$html.= $this->getDropdown($productId, $ptype);	
		}
		else
		{
			$html.= $this->getConfigurableItem($productId, $attributeId, $ptype);
		}	
		return $html;
	}
	
	protected function getConfigurableItem($productId, $attributeId, $ptype)
	{
		$html = 'This is the dropdown';
		$productObj = $this->setOptionsBlock();		
		// Determines weather the category meets David Wilhelms criteria(This can be used for any category type)
		if($productObj->isWilhelm($productId))
		{
			$rows = $productObj->getLinkedProductsHtml($productId, $attributeId, true);
		}
		else
		{			
			$rows = $productObj->getLinkedProductsHtml($productId, $attributeId, false);
		}
		
		$html = '<select name="DropDownList'.$productId.'" ';
		$html.= 'onchange="showButton(\''.$ptype.'\',this.value, \''.$productId.'\')" id="DropDownList'.$productId.'">';
		
		$html.='<option value="0">Choose '.$productObj->getAttributeLabel($productId).'</option>';
		$i=1;
		foreach ($rows as $item){
			
			foreach($item as $drops)
			{
				if($i % 2)
				{
					if(!empty($drops)){
						$html = $html.'<option value='.$drops.'>';
					}
				}
				else{
					if(!empty($drops)){
						$html.=$drops.'</option>';
					}
				}	
				
				$i++;
			}
			
		}		
		return $html.='</select>';		
	}
		
	/**
	 * This is method getDropdown
	 *
	 * @param int $productId Product Id for selected product
	 * @param int $ptype Must be product type
	 * @return mixed Returns a completed HTML dropdown
	 *
	 */
	protected function getDropdown($productId, $ptype)
	{
		$productObj = $this->setOptionsBlock();
		
		// Determines weather the category meets David Wilhelms criteria(This can be used for any category type)
		if($productObj->isWilhelm($productId))
		{
			$rows = $productObj->getLinkedProductsHtml($productId, true);
		}
		else
		{			
			$rows = $productObj->getLinkedProductsHtml($productId, false);
		}
		
		$html = '<select name="DropDownList'.$productId.'" ';
		$html.= 'onchange="showButton(\''.$ptype.'\',this.value, \''.$productId.'\')" id="DropDownList'.$productId.'">';
		
		$html.='<option value="0">Choose '.$productObj->getAttributeLabel($productId).'</option>';
		$i=1;
		foreach ($rows as $item){
			
			foreach($item as $drops)
			{
				if($i % 2)
				{
					if(!empty($drops)){
						$html = $html.'<option value='.$drops.'>';
					}
				}
				else{
					if(!empty($drops)){
						$html.=$drops.'</option>';
					}
				}	
				
				$i++;
			}
			
		}		
		return $html.='</select>';		
	}
	
	public function getProductHtmlUrl($productId)
	{
		$html = '';
		$_product = new Wdc_Catalog_Block_Product();
		$html = '<div id="productLabel'.$productId.'"><h3 class="product-name">';
		
		$html.= '<a href="/'.$_product->getProductUrl($productId).'">';
		$html.= $_product->getProductName($productId);
		$html.= '</a></h3></div>';
		
		
		return $html; 		
	}
	
	public function getWdcAddToCartUrl($product, $productId, $additional = array())
	{
		
		$superId = 928;
		$optionId = 124;
		$pid = $product->getProductId();
		
		if ($product->hasRequiredOptions()) {
			
			if(empty($superId) || empty($optionId))
			{
				$url = $product->getProductUrl();
				$link = (strpos($url, '?') !== false) ? '&' : '?';
				return $url . $link . 'options=cart';			
			}
			else
			{
				return $url = '/checkout/cart/add/product/'.$productId.'/?super_attribute['.$superId.']='.$optionId;
			}
			
		}
		return parent::getAddToCartUrl($product, $additional);
	}
	
	public function getConfigurableDropdown($productId, $attributeId, $required=0, $configNo=1, $last=true)
	{
		
		$html = '';
			
		$html.='<div id="SuperAttribute'.$attributeId .'">';
		if($required != 0)
		{
			$html.='<span class="required">*</span>';
		}
		if(!$last)
		{
			$html.='<select name="super_attribute['.$attributeId.']">';	
		}
		 else
		{
			$html.='<select name="super_attribute['.$attributeId.']">';
		}
				
		foreach ($this->setOptionsBlock()->geteavOptionsbyAttributeIdParentId($attributeId, $productId) as $row)
		{

			$html.='<option value="'.$row['option_id'].'">'.$row['title'].'</option>';
	
		}
		$html.='</select></div>';
			
		
		return $html;
	}
	
	public function setShortDescription($string, $limit, $break=".", $pad="...") { 
		// return with no change if string is shorter than $limit  
		if(strlen($string) <= $limit) 
			return $string;
		// is $break present between $limit and the end of the string? 
		if(false !== ($breakpoint = strpos($string, $break, $limit))) {
			if($breakpoint < strlen($string) - 1) { 
				$string = substr($string, 0, $breakpoint) . $pad; 
			}
		}
		return $string;
	}
		
}

?>