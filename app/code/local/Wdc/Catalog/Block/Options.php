<?php
class Wdc_Catalog_Block_Options extends Wdc_Catalog_Block_Product
{
	protected function setOptionModel()
	{
		return new Wdc_Catalog_Model_Options();	
	}
	
	protected function setProductModel()
	{
		return new Wdc_Catalog_Model_Product();	
	}
	
	protected function getCatalogProductOptionRow($optionId)
	{
		return $this->setOptionModel()->_getCatalogProductOptionRow($optionId);
	}
	
	public function getOptionType($optionId)
	{
		$optionLabel = 'not defined';
		$optionType = $this->getCatalogProductOptionRow($optionId);
		if($optionType)
		{
		 $optionLabel=$optionType['type'];	
		}
		return $optionLabel;
	}	
		
	/**
	 * This is method getCatalogProductOptionArray
	 *
	 * @param int $productId ProductId
	 * @return Array (option_id, is_require)
	 *
	 */
	public function getCatalogProductOptionArray($productId)
	{		
		return $this->setOptionModel()->_getCatalogProductOptionArray($productId);
	}
	
	public function getCatalogProductOptionCount($productId)
	{		
		return $this->setOptionModel()->_getCatalogProductOptionCount($productId);
	}
	
	public function getCatalogProductOptionTypeValueArray($optionId)
	{
		return $this->setOptionModel()->_getCatalogProductOptionTypeValueArray($optionId);
	}
	
	public function getCatalogProductOptionTitle($optionId)
	{
		return $this->setOptionModel()->_getCatalogProductOptionTitle($optionId);
	}
	
	public function getCountCatalogProductOptionTitle($optionId)
	{
		return count($this->setOptionModel()->_getCatalogProductOptionTitle($optionId));
	}
	
	public function getCatalogProductOptionTypeTitle($optionId)
	{
		return $this->setOptionModel()->_getCatalogProductOptionTypeTitle($optionId);
	}
	
	public function isOption($productId)
	{
		//echo $this->getCatalogProductOptionCount($productId);
		$var = false;
		if($this->getCatalogProductOptionCount($productId) != 0)
			{
			$var = true;	
			}
			
		return $var;
	}
	
	public function getOptionId($productId, $attributeId)
	{
		return $this->setProductModel()->getOptionID($productId, $attributeId);			
	}
	
	
	/**
	 * This is method getCatalogProductOptionTypePrice
	 *
	 * @param int $optionTypeId Option Type Id
	 * @return float Price from database table (Unformated)
	 *
	 */

	public function getCatalogProductOptionTypePrice($optionTypeId)
	{
		return $this->setProductModel()->_getCatalogProductOptionTypePrice($optionTypeId);
	}
	
	public function getCatalogProductOptionTypePriceTitle($optionId)
	{
		return $this->setOptionModel()->_getCatalogProductOptionTypePriceTitle($optionId);
	}
	
	/**
	 * This is method getSuperAttributeParentId
	 *
	 * @param int $productId Product entity Id
	 * @return int Return the Parent ID of the given ProductId (from the superlink table)
	 *
	 */
	public function getSuperAttributeParentId($productId)
	{
		return $this->setProductModel()->_getSuperAttribId($this->setOptionModel()->_getCatalogProductSuperLinkParentId($productId));	
		
	}
	
	public function getCatalogProductSuperLinkParentId($productId)
	{
		if($this->isParentCatalogProductSuperLink($productId))
			{
				$id = $productId;	
			}
		else
			{
				$id = $this->setOptionModel()->_getCatalogProductSuperLinkParentId($productId);	
			}
			
		return $id;
	}
	
	public function isParentCatalogProductSuperLink($productId)
	{
		return $this->setOptionModel()->_isParentCatalogProductSuperLink($productId);
	}
	
	public function getOptionsbyAttributeId($attributeId)
	{
		return $this->setOptionModel()->_getOptionsbyAttributeId($attributeId);
	}
	
	public function geteavOptionsbyAttributeIdParentId($attributeId, $productId)
	{
		//$this->getCatalogProductSuperLinkParentId($productId)
		return $this->setOptionModel()->_geteavOptionsbyAttributeIdParentId($attributeId, $this->getCatalogProductSuperLinkParentId($productId));
	}
	
}

?>