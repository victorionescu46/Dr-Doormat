<?php
class Wdc_Catalog_Model_OptionBridgeData extends Wdc_Catalog_Model_Options
{

	protected function setDataCon()
	{
		return Mage::getSingleton('core/resource')->getConnection('core_read');
	}
	
	public function _getAitocAitbrandsGroup($attributeId)
	{	
		$var = 'Not Defined';
		$row = $this->setDataCon()->fetchAll('SELECT * FROM aitoc_aitbrands_group where attribute_id ='.$attributeId); 
		if($row)
		{
			$var = $row;					
		}		
		return $var;		
	}
	
	public function _getAitocAitbrandsGroupBrand($groupId)
	{}
	
	public function _getAitocCatalogProductOptionTemplate($templateId)
	{
		$var = 'Not Defined';
		$row = $this->setDataCon()->fetchAll('SELECT * FROM aitoc_catalog_product_option_template where template_id ='.$templateId); 
		if($row)
		{
			$var = $row;					
		}		
		return $var;			
	}
	
	public function _getAitocCatalogProductOption2Template($optionId)
	{
		$var = 'Not Defined';
		//$row = $this->setDataCon()->fetchAll('SELECT * FROM aitoc_catalog_product_option2template where option_id ='.$optionId); 
		$row = $this->setDataCon()->fetchAll('SELECT * FROM aitoc_catalog_product_option2template'); 
		
		if($row)
		{
			$var = $row;					
		}		
		return $var;		
	}
	
	public function _getAitocCatalogProductProduct2Required($productId)
	{
		$var = 'Not Defined';
		$row = $this->setDataCon()->fetchAll('SELECT * FROM aitoc_catalog_product_product2required where product_id ='.$productId); 
		if($row)
		{
			$var = $row;					
		}		
		return $var;
	}
	
	public function _getAitocCatalogProductProduct2Template($productId=0, $templateId=0)
	{
		if($productId !=0)
		{
			$var = 'Not Defined';
			$row = $this->setDataCon()->fetchAll('SELECT * FROM aitoc_catalog_product_product2template where product_id='.$productId); 
			
			if($row)
			{
				$var = $row;					
			}	
		}
		else{
			$var = 'Not Defined';
			$row = $this->setDataCon()->fetchAll('SELECT * FROM aitoc_catalog_product_product2template where template_id='.$templateId); 
			if($row)
			{
				$var = $row;					
			}	
		}	
		return $var;	
		
	}
	
	public function _getOptionsUnionbyProductId($productId)
	{
		
		$var = 0;
		$sql = 'SELECT option_id, type, is_require, sort_order FROM catalog_product_option where product_id ='. $productId;
		$sql.= ' union ';
		$sql.= 'SELECT a.option_id, c.type, c.is_require, c.sort_order FROM aitoc_catalog_product_option2template a ';
		$sql.= 'inner join aitoc_catalog_product_product2template b ';
		$sql.= 'on a.template_id = b.template_id ';
		$sql.= 'right join catalog_product_option c ';
		$sql.= 'on a.option_id = c.option_id ';
		$sql.= 'where b.product_id ='. $productId;	
		
		$row = $this->setDataCon()->fetchAll($sql);
		
		if($row)
		{
			$var = $row;					
		}
		
		return $var;
		
	}
		
}

?>