<?php
class Wdc_Catalog_Model_CatalogJson extends Wdc_Catalog_Model_Product
{
	protected function setConn()
	{
		return Mage::getSingleton('core/resource')->getConnection('core_read');	
	}
	
	
	public function _getLinkedProductsArray($parentId)
	{
		$sql = 'SELECT distinct entity_id, attribute_id FROM catalog_product_entity_int ';
		$sql.= 'where entity_id in (SELECT product_id FROM catalog_product_super_link where parent_id ='.$parentId.') ';
		$sql.= 'and attribute_id in (SELECT attribute_id FROM catalog_product_super_attribute where product_id ='.$parentId.')';		
		$rows = $this->setConn()->fetchAll($sql);
		if($rows)
			{
			$json = $rows;
			}
		
		return $json;		
	}	
}


?>