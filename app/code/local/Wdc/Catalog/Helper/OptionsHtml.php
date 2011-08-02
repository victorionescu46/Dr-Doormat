<?php
class Wdc_Catalog_Helper_OptionsHtml extends Wdc_Catalog_Helper_SupremoConfigadore
{
	/**
	 * This is method setOptionBlock
	 *
	 * @return object Returns an Option Object
	 *
	 */
	protected function setOptionBlock()
	{
		return new Wdc_Catalog_Block_Grouped();	
	}
	
	public function AddCartGroupURL($url)
	{
		return $this->setOptionBlock()->getRawCartUrl($url);	
	}
	
	public function getProductHtmlUrl($productId)
	{
		$_product = new Wdc_Catalog_Block_Product();
		$html = '<div id="productLabel'.$productId.'"><h3 class="product-name">';
		$obj = new Wdc_Catalog_Model_Product();
		$html.= '<a href="/'.$_product->getProductUrl($productId).'">';
		$html.= $obj->_getProductName($productId);
		$html.= '</a></h3></div>';
		
		return $html; 		
	}
	
	public function setGroupFormTag($begin=true)
	{
		
		if($begin)
		{
			$html= '<form method="get" action=/Wdc/options/grouped.php?go=1>';
			
		}
		else
		{
			$html1 ='</form>';		
		}
		
					
		
		return $html;		
	}
	
	public function isGrouped($productId)
	{
		return $this->setOptionBlock()->isGrouped($productId);	
	}
	
	
	/**
	 * This is method setOptionsInputHtml
	 *
	 * @param array $option Sets the option value for the hidden input first
	 * @return Html Returns formated html with option values and id
	 *
	 */
	protected function setOptionsInputHtml($option)
	{
		// Need to set the option array values here
		return '<input name="options['.$option['id'].']" value="'.$option['value'].'" type="hidden">';			
	}
	
	
	/**
	 * This is method setSuperAttributeInputHtml
	 *
	 * @param int $productId Product entity Id
	 * @return HTML hidden input field with attribute id and value
	 *
	 */
	protected function setSuperAttributeInputHtml($productId, $grouped=false)
	{
		
		$attributeId = $this->setOptionBlock()->getSuperAttributeParentId($productId);
				
		return '<input name="product['.$productId.'][super_attribute]['.$attributeId.']" id="attribute'.$attributeId.'" value="'.$this->setOptionBlock()->getOptionId($productId, $attributeId).'" type="hidden">'; 
		
	}
	
	protected function setFinialProductInput($productId=0, $grouped=false)
	{
		if(!$grouped)
		{
			$html= '<input name="product['.$productId.'][qty]" type=hidden id="qty" maxlength="12" value="" />';
		}
		$html.= '<input type="hidden" name="product['.$productId.'][related_product]" id="related-products-field" value="" />';		
		return $html;
	}
	
	public function setPriceBlock($productId, $optionId)
	{
		$html = '';
		$configUnique = false;
		$obj = new Wdc_Catalog_Block_Options();
		if($obj->isOption($obj->getCatalogProductSuperLinkParentId($productId)))
		{
			
			//$html.= 'Super Ginormous Test';
			// need to get the Option array for this input (This is not needed if a dropdown is used)
			//$html.= $this->setOptionsInputHtml(12);			

			$html.= $this->getProductOptionsDropdown($productId);		
			$html.= $this->setSuperAttributeInputHtml($productId);
			$html.= $this->setFinialProductInput();
		}	
		else
			{
			
			//$html.= $this->setSuperAttributeInputHtml($productId);
			//$html.= $this->setFinialProductInput();
			$configUnique = true;
			}
		$html.= '<span class="list-price">';		
		$price = $obj->getPrice($productId);
		if($price !=0)
		{
			$html.= '$'.number_format($price, 2, '.', '');
		}
		else
		{
			$html.= '<b>Configure your product for pricing</b>';	
		}
		$html.= '</span> ';	
			
		
		$html.= $this->setCartButton($productId, $configUnique);		
		
		return $html;
	
	}
	
		
	public function getProductOptionsDropdown($productId)
	{
		$html = '';
		$parentId = $this->setOptionBlock()->getCatalogProductSuperLinkParentId($productId);
				
	if($this->setOptionBlock()->getCatalogProductOptionCount($parentId) > 0)
		{
			
			$items = $this->setOptionBlock()->getCatalogProductOptionArray($parentId);
			foreach ($items as $item)
				{
							
				$html.='<div id="option'.$item['option_id'].'">';
				if($item['is_require'] != 0)
					{
						$html.='<span class="required">*</span>';
					}
				$html.='<select name="options['.$item['option_id'].']">';
				foreach ($this->setOptionBlock()->getCatalogProductOptionTypeTitle($item['option_id']) as $row)
						{
						$html.='<option value="'.$row['option_type_id'].'">'.$row['title'].'</option>';
						}
				$html.='</select></div>';
				}
			$html.='</div>';
				
		}
		
		return $html;
	}
	
	protected function checkPosi($price, $curprice)
	{
		$var = '+';
		return $var;	
	}
	
	public function getOptionsDropdown($item, $grouped=false, $productId=0, $required=0)
	{
		if(is_array($item))
		{
			
			$option_id = $item['option_id'];
			$required = $item['is_require'];
		}
		else
			{
			$option_id = $item;	
			}		
		
		$html = '';
		
		$option_title = $this->setOptionBlock()->getCatalogProductOptionTitle($item['option_id']);
			
		$html.='<div id="option'.$option_id .'">';
				if($required != 0)
				{
					$html.='<span class="required">*</span>';
				}
		if($grouped)
			{
			$html.='<select name="product['.$productId.'][options]['.$option_id.']">';	
			}
		else{
			$html.='<select name="options['.$option_id.']">';
		}
				
				$html.='<option value="0" class="first-option">'.$option_title.'...</option>';
			foreach ($this->setOptionBlock()->getCatalogProductOptionTypePriceTitle($option_id) as $row)
				{
			if($row['price'] !=0)
			{
				$html.='<option value="'.$row['option_type_id'].'">'.$row['title'].'...+$'.number_format($row['price'], 2, '.', '').'</option>';
			}
			else
			{
				$html.='<option value="'.$row['option_type_id'].'">'.$row['title'].'</option>';
			}
				}
				$html.='</select></div>';
			
			//$html.='</div>';		
		
		return $html;
	}

	protected function getOptionsCheckbox($item, $grouped=false)
	{
		$html = '';
		
		$html.='<div id="option'.$item['option_id'].'">';
		if($item['is_require'] != 0)
		{
			$html.='<span class="required">*</span>';
		}
		//$html.='<select name="options['.$item['option_id'].']">';
		
		if($grouped)
			{
			$optionLabel = 'product[][options]';
			}
		else
			{
			$optionLabel = 'options';
			}
		
		foreach ($this->setOptionBlock()->getCatalogProductOptionTypePriceTitle($item['option_id']) as $row)
		{
			$html.=' <input type="checkbox" value="'.$row['option_type_id'].'" name="'.$optionLabel.'['.$item['option_id'].']" /> '. $row['title'];
		}
		
		//$html.='</select></div>';
		
		$html.='</div>';		
		
		return $html;
	}
	
	protected function getOptionsRadio($item, $grouped=false)
	{
		$html = '';
		
		$html.='<div id="option'.$item['option_id'].'">';
		if($item['is_require'] != 0)
		{
			$html.='<span class="required">*</span>';
		}
		
		if($grouped)
		{
			$optionLabel = 'product[][options]';
		}
		else
		{
			$optionLabel = 'options';
		}
		//$html.='<select name="options['.$item['option_id'].']">';
		foreach ($this->setOptionBlock()->getCatalogProductOptionTypeTitle($item['option_id']) as $row)
		{
			$html.=' <input type="radio" value="'.$row['option_type_id'].'" name="'.$optionLabel.'['.$item['option_id'].']" /> '. $row['title'];
		}
		//$html.='</select></div>';
		
		$html.='</div>';		
		
		return $html;
	}
	
	protected function getOptionsMultiple($item, $grouped=false)
	{
		$html = '';
		
		$html.='<div id="option'.$item['option_id'].'">';
		if($item['is_require'] != 0)
		{
			$html.='<span class="required">*</span>';
		}
		
		if($grouped)
		{
			$optionLabel = 'product[][options]';
		}
		else
		{
			$optionLabel = 'options';
		}		
		
		$html.='<select name="'.$optionLabel.'['.$item['option_id'].']" MULTIPLE size="'.$this->setOptionBlock()->getCountCatalogProductOptionTypeTitle($item['option_id']).'">';
		foreach ($this->setOptionBlock()->getCatalogProductOptionTypePriceTitle($item['option_id']) as $row)
		{
			if($row['price'] !=0)
			{
				$html.='<option value="'.$row['option_type_id'].'">'.$row['title'].'...+$'.number_format($row['price'], 2, '.', '').'</option>';
			}
			else
			{
				$html.='<option value="'.$row['option_type_id'].'">'.$row['title'].'</option>';
			}
		}
		$html.='</select></div>';
		
		//$html.='</div>';		
		
		return $html;	
	}
	
	protected function getOptionsField($item, $grouped=false)
	{
		$html='<div id="option'.$item['option_id'].'">';
		if($item['is_require'] != 0)
		{
			$html.='<span class="required">*</span>';
		}
		
		if($grouped)
		{
			$optionLabel = 'product[][options]';
		}
		else
		{
			$optionLabel = 'options';
		}	
		
		
		$html.= '<input type="text" name="'.$optionLabel.'['.$item['option_id'].']" MAXLENGTH="'.$item['max_characters'].'"  /></div>';	
		
		return $html;
	}
	
	protected function getOptionsArea($item, $grouped=false)
	{
		if($grouped)
		{
			$optionLabel = 'product[][options]';
		}
		else
		{
			$optionLabel = 'options';
		}
		
		$html='<div id="option'.$item['option_id'].'">';
		if($item['is_require'] != 0)
		{
			$html.='<span class="required">*</span>';
		}
		
		$html.='<textarea name="'.$optionLabel.'['.$item['option_id'].']" COLS="40" ROWS="4" MAXLENGTH="'.$item['max_characters'].'" ></textarea>';		
		return $html;
	}
	
	protected function setGroupProductInput($productId)
	{
		return '<input name="product['.$productId.'][product]" type="hidden" id="product" value="'.$productId.'" />';
	}
	
	protected function setProductInput($productId)
	{
		return '<input name="product" type="hidden" id="product" value="'.$productId.'" />';
	}
	
	public function OptionsContainer($productId, $form=false, $button=false)
	{
		$grouped = $this->isGrouped($productId);
		
		if(!$grouped)
		{
			$html = $this->setProductInput($productId);
		}
		else
		{
			$html = $this->setGroupProductInput($productId);	
		}
		
		foreach ($this->setOptionBlock()->getCatalogProductOptionArray($productId) as $item)
		{
			$html.= '<h5>'.$this->setOptionBlock()->getCatalogProductOptionTitle($item['option_id']).'</h5>';	
					
			// add type code back here		
			
			$html.= $this->getOptionEntities($item, $grouped);					
								
		}
		$html.= $this->setFinialProductInput();
		$html.= $this->setSuperAttributeInputHtml($productId);
		
		if($button)
		{
			$html.= $this->setCartButton($productId, false); // Cart button will not go here
		}
				
		return $html;
	}
	
	protected function getOptionEntities($item, $grouped=false, $productId=0)
	{
		switch($item['type'])
		{
			case 'drop_down':
				return $this->getOptionsDropdown($item, $grouped, $productId);
				break;				
			case 'checkbox':
				return $this->getOptionsCheckbox($item, $grouped);
				break;
			case 'radio':
				return $this->getOptionsRadio($item, $grouped);
				break;					
			case 'multiple':
				return $this->getOptionsMultiple($item, $grouped);
				break;
			case 'field':
				return $this->getOptionsField($item, $grouped);
				break;
			case 'area':
				return $this->getOptionsArea($item, $grouped);
				break;
			default:
				return 'Error, not defined';
				break;
		}
	
	}

}
?>