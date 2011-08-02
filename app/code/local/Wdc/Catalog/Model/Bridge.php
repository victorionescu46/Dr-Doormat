<?php
class Wdc_Catalog_Model_Bridge extends Mage_Catalog_Model_Abstract
{
	public function getEntityIdbyMidwestCatId($SubCat)
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchRow('SELECT entity_id FROM wdc_catalog_category_index where IN_ID ='.$SubCat);
		if($row)
		{
			return $row['entity_id'];	
		}
		else{
			return 0;
		}		
	}	
	
	public function getCategoryUrlbyId($CatId)
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchRow('SELECT value as catUrl FROM catalog_category_entity_varchar where attribute_id = 533 and store_id =0 and entity_id ='.$CatId);
		if($row)
		{
			return $row['catUrl'];	
		}
		else{
			return 'catalogsearch/advanced?NotFound=1';
		}		
	}
	
	public function getProductIdbyMidwestId($MidwestId, $attrib=929)
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql = "SELECT * FROM catalog_product_entity_varchar where attribute_id =".$attrib." and store_id =0 and value ='".$MidwestId."'";
		$row = $readonce->fetchAll($sql);		
		if($row)
		{
			if(count($row) > 1)
				{
				$entity_id = $this->getProductParentIdfromMidwestId($MidwestId, $attrib);	
				if($entity_id ==0)
					{
					$productId = max($row['entity_id']);
					}
				else
					{
					$productId = $entity_id;	
					}	
				}
				else
			{
				foreach ($row as $item)
				{
					$productId = $item['entity_id'];
					break;
				}
			}	
		}
		else{
			$productId =  0;
		}
		
		return $productId;		
	}
	
	public function getProductParentIdfromMidwestId($MidwestId, $attrib=929)
	{
		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$sql = '';
		$sql.= "SELECT parent_id FROM catalog_product_super_link ";
		$sql.= "where parent_id IN ";
		$sql.= "(SELECT entity_id FROM catalog_product_entity_varchar ";
		$sql.= "where attribute_id =".$attrib." and value ='".$MidwestId."') limit 1";		
		$row = $readonce->fetchRow($sql);
		
		if($row)
		{
			return $row['parent_id'];
		}
		else{
			return 0;	
		}				
	}
	
	
	
}
?>