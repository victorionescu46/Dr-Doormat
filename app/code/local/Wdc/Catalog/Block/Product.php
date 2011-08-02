<?php

class Wdc_Catalog_Block_Product extends Mage_Core_Block_Template
{
	
	protected function setProductModel()
	{
		return new Wdc_Catalog_Model_Product();	
	}	
	
	protected function getProductEntity($productId)
	{
		return $this->setProductModel()->_getProductEntity($productId);
	}
	
	public function getProductLinkedParentId($productId)
	{
		return $this->setProductModel()->_getProductLinkedParentId($productId);
	}
	
	public function isGrouped($productId)
	{
		
		$parentId = $this->getProductLinkedParentId($productId);
		if($parentId != 0)
			{
				$productId = (int)$parentId;	
			}
		
		$var = false;
		$_product = $this->getProductEntity($productId);
		if($_product['type_id'] == 'grouped')
			{
			$var = true;	
			}	
		return $var;
	}
		
	public function WDCgetProduct()
	{
		if (!$this->getData('product') instanceof Mage_Catalog_Model_Product) {
			if ($this->getData('product')->getProductId()) {
				$productId = $this->getData('product')->getProductId();
			}
			if ($productId) {
				$product = Mage::getModel('catalog/product')->load($productId);
				if ($product) {
					$this->setProduct($product);
				}
			}
		}
		return $this->getData('product');
	}
	//need to update
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
	
	public function getOptionsbyProductId($productId)
	{
		$obj = new Wdc_Catalog_Model_Product();	
			
		try{
			return $obj->_getOptionsbyProductId($productId);
		}
		catch(exception $e)	{
			return $e->getMessage();
		}
	}
	
	protected function getOptionsforDropdowns($productId)
	{
		$obj = new Wdc_Catalog_Model_Product();	
		$aid = $obj->_getSuperAttribId($productId);	
		
		return $obj->_getOptionsbyAttribId($aid);		
	}
	
	protected function getProductAttributes($productId)
	{
		$obj = new Wdc_Catalog_Model_Product();	
		return $obj->_getProductAttribSet($productId);
	}
	
	public function getProduct()
	{
		if (!Mage::registry('product') && $this->getProductId()) {
			$product = Mage::getModel('catalog/product')->load($this->getProductId());
			Mage::register('product', $product);
		}
		return Mage::registry('product');
	}
	
	public function getProductId()
	{
		if ($product = Mage::registry('current_product')) {
			return $product->getId();
		}
		return false;
	}
	
	protected function getCategoryLevel($catId)
	{
		$obj = new Wdc_Catalog_Model_Product();
		$rows = $obj->getCategoryParentId($catId);
		
		$level = $rows['level'];
					
		return $level;
	}
	
	protected function getCategoryParentId($catId)
	{
		
		$obj = new Wdc_Catalog_Model_Product();
		$rows = $obj->getCategoryParentId($catId);
		
		$parentId = $rows['parent_id'];
					
		return $parentId;
	}
	
	protected function setCategoryId($catId)
	{
		$obj = new Wdc_Catalog_Model_Product();
		return $obj->getCategoryParentId($catId);		
	}
	
	
	/**
	 * This is method isWilhelm
	 *
	 * @param int $productId Checks to see if predetermined categories are in the scope
	 * @return bool 
	 *
	 */
	
	public function isWilhelm($productId)
	{
		$var = true;
		$catid = $this->getTopLevelParentCatId($productId);
		if($catid == 37 || $catid  == 259)
			{
			$var = false;	
			}
			
		return $var;
	}
	
	protected function getTopLevelParentCatId($productId)
	{
		$obj = new Wdc_Catalog_Model_Product();	
		$cats =  $obj->_getCategoryIds($productId);	
		
		if(!strpos($cats, ","))
		{
		 $catid = $this->getParentCategoryId($cats);					
		}
		else
			{
			$catids = explode(",", $cats);
			/**Need to make getParent Cat function here**/
			/**Need to find the parent Cat ID **/
			foreach ($catids as $item)
			{
				$catid = $this->getParentCategoryId($item);
				break;	
			}
		}
		return $catid;
	}
	
	protected function getParentCategoryId($catids, $targetLevel=4)
	{
		
		 $level = $this->getCategoryLevel($catids);
	     $parentId = $this->getCategoryParentId($catids);
		
		if($level > $targetLevel){
			
			
			while ($level > $targetLevel)
			{
				$catdata = $this->setCategoryId($parentId);
				$level = $catdata['level'];
				$parentId = $catdata['parent_id'];
				if($level == $targetLevel)
				{				
					break;
				}
			}
		}
		
		return $parentId;
	}
	
	
	public function getLinkedProductsHtml($productId, $attribute_id=0, $cat=false)
	{
		$obj = new Wdc_Catalog_Model_Product();	
		if($cat)
		{
			return $obj->_getLinkedAtribProductsHtml($productId, $attribute_id);
		}
		else
			{
			
			return $obj->_getLinkedProductsHtml($productId);			
			}
	}
	
	public function getSuperAttributeId($productId)
	{
		return $this->setProductModel()->_getSuperAttribId($productId);
	}
	
	public function isProductAttributeUnique($productId)
	{
		$var = false;
		$obj = new Wdc_Catalog_Model_Product();
		if($obj->countProductAttribute($productId) < 2)
			{
			$var = true;	
			}
		return $var;			
	}
	
	public function getProductAttributeCount($productId)
	{
		return $this->setProductModel()->countProductAttribute($productId);
	}
	
	public function getCatalogProductSuperAttribute($productId)
	{
		return $this->setProductModel()->countProductAttribute($productId, false);	
	}
	
	public function getproductSuperAttributeId($productId, $attributeId)
	{
		return $this->setProductModel()->_getproductSuperAttributeId($productId, $attributeId);
	}
	
	public function getProductIdSuperAttributeId($superattributeId)
	{
		return $this->setProductModel()->_getProductIdSuperAttributeId($superattributeId);
	}
	
	public function getProductHtmlUrl($productId)
	{
		$html = '<div id="productLabel'.$productId.'"><h3 class="product-name">';
		$obj = new Wdc_Catalog_Model_Product();
		$html.= '<a href="/'.$this->getProductUrl($productId).'">';
		$html.= $obj->_getProductName($productId);
		$html.= '</a></h3></div>';
		
		return $html; 		
	}
	
	public function getProductName($productId)
	{
		return $this->setProductModel()->_getProductName($productId);
	}
	
	public function getViewProduct($productId)
	{
		$html = '<div id="productLabel'.$productId.'"><h3 class="product-name">';
		$obj = new Wdc_Catalog_Model_Product();
		$html.= $obj->_getProductName($productId);
		$html.= '</h3></div>';		
		return $html;	
	}
	
	public function geteavAttributeLabel($attributeId)
	{
		return $this->setProductModel()->_geteavAttributeLabel($attributeId);
	}
	
	public function getProductUrl($_productid)
	{
		$obj = new Wdc_Catalog_Model_Product();
		return $obj->_getProductUrl($_productid);
	}
	
	public function getLowPrice($_productid)
	{
		$obj = new Wdc_Catalog_Model_Product();
		return (double)$obj->_getLowPrice($_productid);
		 
	}
	
	public function getPrice($_productid)
	{
		$obj = new Wdc_Catalog_Model_Product();
		return $obj->_getPrice($_productid);
	}
	
	public function getAttributeLabel($_productid)
	{
		$obj = new Wdc_Catalog_Model_Product();
		//return $obj->_getProductDropLabel($_productid);	
		return $obj->_getAttributeLabel($_productid);	
	}
	
		
	public function getOptionsHtml($_productid, $isList=true)
	{
		
		if($isList)
			{
			$ptype = 1;	
			}
		else
			{
			$ptype = 0;	
			}
			
		$optionsHtml = ''; 			
		$optionsval = $this->getLinkedProductsHtml($_productid);
				
		if($this->isProductAttributeUnique($_productid))
		{
			$optionsHtml = '<form name="update'.$_productid.'"><select name="DropDownList'.$_productid.'" ';
			$optionsHtml.= 'onchange="showButton(\''.$ptype.'\',this.value, \''.$_productid.'\')" id="DropDownList'.$_productid.'">';
		
			$optionsHtml.='<option value="0">Choose '.$this->getAttributeLabel($_productid).'</option>';
			$i=1;
			foreach ($optionsval as $item){
				
			foreach($item as $drops)
				{
					if($i % 2)
					{
						if(!empty($drops)){
							$optionsHtml = $optionsHtml.'<option value='.$drops.'>';
						}
					}
					else{
						if(!empty($drops)){
							$optionsHtml.=$drops.'</option>';
						}
					}	
					
					$i++;
				}
				
			}			
			
			$optionsHtml.='</select></form>';			
			$optionsHtml.='<div id="txtHint'.$_productid.'"><b>Prices starting at $'.number_format($this->getLowPrice($_productid), 2, '.', '').'</b></div>';
		}
		else
			{			
				$optionsHtml = '<p><button class="button" onclick="setLocation(\'/';
				$optionsHtml.=$this->getProductUrl($_productid).'\')">';
				$optionsHtml.='<span> Configure Options</span></button></p>';
			}
		return $optionsHtml;
	}
	
	public function setProductbyId($productId)
	{
		$_product = Mage::getModel('catalog/product')->load($productId);
	
		return $_product;
	}
	

	
}

?>