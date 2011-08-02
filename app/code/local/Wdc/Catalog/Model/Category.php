<?php
class Wdc_Catalog_Model_Category extends Mage_Catalog_Model_Category
{
	public function _getChildCount($catId)
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchRow('SELECT children_count FROM catalog_category_entity where entity_id ='.$catId);
		if($row)
		{
			return $row['children_count'];	
		}
		else{
			return 0;
			}	
	}
	
	
}

?>