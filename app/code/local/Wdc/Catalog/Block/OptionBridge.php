<?php
class Wdc_Catalog_Block_OptionBridge extends Wdc_Catalog_Block_Options
{
	protected function setOptionDataBridge()
	{
		return new Wdc_Catalog_Model_OptionBridgeData();
	}
	
	public function getAitocAitbrandsGroup($attributeId)
	{	
		return $this->setOptionDataBridge()->_getAitocAitbrandsGroup($attributeId);		
	}
	
	/*public function getAitocAitbrandsGroupBrand($groupId)
	{}*/
	
	public function getAitocCatalogProductOptionTemplate($templateId)
	{
		return $this->setOptionDataBridge()->_getAitocCatalogProductOptionTemplate($templateId);				
	}
	
	public function getAitocCatalogProductOptionTemplateRow($templateId, $rowName)
	{
		$val = 'Undefined';
		$rows =  $this->setOptionDataBridge()->_getAitocCatalogProductOptionTemplate($templateId);	
		if($rows)
		{
			foreach ($rows as $row)
			{
				$val = $row[$rowName];
				break;	
			}
		}
		return $val;		
	}
	
	public function getAitocCatalogProductOption2Template($optionId)
	{
		return $this->setOptionDataBridge()->_getAitocCatalogProductOption2Template($optionId);		
	}
	
	public function getAitocCatalogProductProduct2Required($productId)
	{
		return $this->setOptionDataBridge()->_getAitocCatalogProductProduct2Required($productId);	
	}
	
	public function getAitocCatalogProductProduct2Template($productId, $templateId)
	{
		return $this->setOptionDataBridge()->_getAitocCatalogProductProduct2Template($productId, $templateId);
	}
	
	public function checkAitocProduct($productId)
	{
		$val = $this->setOptionDataBridge()->_getAitocCatalogProductProduct2Template($productId, 0);
		if($val == 'Not Defined')
			{
			return false;	
			}
		else
			{
			return true;	
			}			
	}
	
	public function getOptionsUnionbyProductId($productId)
	{
		return $this->setOptionDataBridge()->_getOptionsUnionbyProductId($productId);
	}
	
	public function getCatalogProductOptionCountLQ($productId)
	{
		$cnt = $this->setOptionDataBridge()->_getOptionsUnionbyProductId($productId);
				
		if($cnt != 0)
			{
			$cnt = count($cnt);
			}
			
		return $cnt;
		
	}
		
}

?>