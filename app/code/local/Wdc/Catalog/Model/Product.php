<?php

class Wdc_Catalog_Model_Product extends Mage_Catalog_Model_Product
{
	
	
	/**
	 * This is method getOptionLabel
	 *
	 * @param int $optionId from Option  ID
	 * @return string returns value label for option
	 *
	 */
	protected function getOptionLabel($optionId)
	{		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchRow('SELECT value FROM eav_attribute_option_value where option_id ='.$optionId);
		
		if(!empty($row['value']))
		{	
			return $row['value'];
		}
		else
		{
			return '';
		}		
	}
	
	public function _getProductLinkedParentId($productId)
	{
		$var = 0;
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchRow('SELECT product_id FROM catalog_product_link where linked_product_id ='.$productId);
		if($row)
		{
			$var = $row;
		}			
		return $var;	
	}
	
	public function _getProductEntity($productId)
	{
		$var = '!ERROR - Product Not Found';
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchRow('SELECT * FROM catalog_product_entity where entity_id ='.$productId);
		if($row)
			{
			$var = $row;
			}			
		return $var;	
	}
	
	public function _getOptionsbyAttribId($attribId)
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$rows = $readonce->fetchAll('SELECT * FROM catalog_product_entity_int where attribute_id ='.$attribId);
		
		foreach ($rows as $row)
			{
			$ops[] = array($row['entity_id'], $this->getOptionLabel($row['value']).' '.$this->getOptionValue($row['value']));								
			}
			
		return $ops;
	}	
	
	
	/**
	 * This is method _getOptionIdsValues
	 *
	 * @param int $attribId select data by Attribute ID
	 * @return array Returns an array from the PRoduct entity int table
	 *
	 */
	public function _getOptionIdsValues($attribId)
	{
		$resource = Mage::getSingleton('core/resource');
	    $read = $resource->getConnection('catalog_read');
		
		$select = null;
		
		$CatalogProductEntityInt = $resource->getTableName('catalog/product_entity_int');
		
		$select = $read->select()->from(array('cpei'=>$CatalogProductEntityInt))
			->where('cpei.attribute_id=?', $attribId);
			//->where('cpei.attribute_id=928');
		
		$optionIds = $read->fetchAll($select);
		
		foreach ($optionIds as $row)
		{
			$ops[] = array($row['value'], $row['value'].' '.$row['value']);						
		}
		
		return $ops;		
	}

	protected function setOptions($options, $cols)
	{
		$arr = array();
		while($row = mysql_fetch_array($options))
		{
				$arr[] = $row[$cols];			
		}
		
		return $arr;
	}
	
	
	
	public function _getSuperAttribId($productId)
	{
		try{
			
			$resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('catalog_read');
			
			$CatalogProductSuperAttribute = $resource->getTableName('catalog/product_super_attribute');
			$select = $read->select()->from(array('cp'=>$CatalogProductSuperAttribute))->where('product_id=?', $productId);
			
			$attribId = $read->fetchAll($select);
			
			if(!count($attribId) < 1)
			{
				foreach ($attribId as $row)
				{
					$pro = $row['attribute_id'];	
					break;	
				}
			}
			else{
				$pro = 0;	
			}
		}
		catch(exception $e)
		{
			return $e->getMessage();
		}
		
		return $pro;
		
	}
		
	
	
	/**
	 * This is method getOptionsbyProductId
	 *
	 * @param int $productId
	 * @return array
	 *
	 */	
	public function _getOptionsbyProductId($productId)
	{
		try{
		
			$resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('catalog_read');
						
			$pro = $this->_getSuperAttribId($productId);
				
			$select = null;
			
			$eavAttributeOption = $resource->getTableName('eav/attribute_option');
			$eavAttributeOptionValue = $resource->getTableName('eav/attribute_option_value');
		//	$CatalogProductEntityInt = $resource->getTableName('catalog/product_entity_int');
	
			
			$select = $read->select()->from(array('eaov'=>$eavAttributeOptionValue))
				->join(array('eao'=>$eavAttributeOption), 'eaov.option_id = eao.option_id', array())
				->where('eao.attribute_id=?', $pro);
						
			$optionIds = $read->fetchAll($select);
			
			foreach ($optionIds as $row)
				{
				$ops = array($row['option_id'], $row['value'].' '.$this->getOptionValue($row['option_id']));						
				}
			
			return $ops;			
			
		}
		catch(exception $e)
		{
			return $e->getMessage();
		}
	}

	
	public function getOptionID($productId, $attribId)
	{		
		$val = 0;
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$rows = $readonce->fetchRow('SELECT value FROM catalog_product_entity_int where entity_id='.$productId.' and attribute_id ='.$attribId.'');
		
		if($rows)
		{		
			$val = $rows['value'];	
		}							
				
		return $val;
	}
	
	protected function getOptionValue($optionId)
	{
		try{
		$select = null;
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('catalog_read');
		
		$ProductSuperAttributePricing = $resource->getTableName('catalog/product_super_attribute_pricing');
		
		$select = $read->select()->from(array('cpsap'=>$ProductSuperAttributePricing))
			->where('cpsap.value_index=?', $optionId);
		
		$opVal = $read->fetchRow($select);
		
		if(empty($opVal['pricing_value']))
			{
			$val = '';
			}
		else
			{
			$val = '..+'.money_format('%(#10n', $opVal['pricing_value']);
			}
		
		return $val;
		}
		catch(exception $e)
		{
			return $e->getMessage();	
		}
	}
	
	public function _getPrice($productId)
	{
		$attributeId = $this->_getProductPriceAttributeId();
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$opVal = $readonce->fetchRow('SELECT value FROM catalog_product_entity_decimal where entity_id='.$productId.' and attribute_id ='.$attributeId.'');
	
		return $opVal['value'];
		
	}
	
	public function _getProductAttribSet($productId)
	{
		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$opVal= $readonce->fetchRow('SELECT * FROM catalog_product_entity where entity_id='.$productId);
				
		$atsi = $opVal['attribute_set_id'];
		
		$select = null;
		
		$val= $readonce->fetchAll("SELECT * FROM catalog_product_entity where type_id != 'configurable' and attribute_set_id=".$atsi);
	
		foreach ($val as $items)
			{
			$val1[] = array($items['entity_id'], $items['entity_id']);
			}
			
		return $val1;		
	}
	

	
	
	/**
	 * This is method _getDropHtmlValueString
	 *
	 * @param int $productId Product Id
	 * @param int $atid Attribute Id
	 * @return HTML Returns a formated HTML string for drop
	 *
	 */
	protected function _getDropHtmlValueString($productId, $atid)
	{
		
		$optionId = $this->getOptionID($productId, $atid);
		$optionLabel = $this->getOptionLabel($optionId);
		$optionLabel.= '....$'.number_format($this->_getPrice($productId), 2, '.', '');
			
		return $optionLabel;	
	}
	
	protected function _getAttributeSetId($atid)
	{
		
		$eti= $this->_getProductEntityTypeId();
		$val = 0;
		$select = null;
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('catalog_read');
		
		$eavEntityAttribute = $resource->getTableName('eav/entity_attribute');
		
		$select = $read->select('attribute_set_id')->from(array('eea'=>$eavEntityAttribute))
			->where('eea.attribute_id=?', $atid)
			->where('eea.entity_type_id=?', $eti);			
		
		$val = $read->fetchRow($select);
		return (int)$val;	
		 
	}
	
	public function _geteavAttributeLabel($attributeId)
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$rows = $readonce->fetchRow('SELECT frontend_label FROM eav_attribute where attribute_id='.$attributeId);
		
		return $rows['frontend_label'];	
	}
	
	public function _getAttributeLabel($productId)
	{
		$eti = $this->_getProductEntityTypeId();
		$atid = $this->_getSuperAttribId($productId);
					
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$rows = $readonce->fetchRow('SELECT frontend_label FROM eav_attribute where entity_type_id='.$eti.' and attribute_id='.$atid);
				
		return $rows['frontend_label'];		
	}
	
	public function _getProductName($productId)
	{
		$attribId = $this->_getProductNameAttributeId();
		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$rows = $readonce->fetchRow('SELECT value FROM catalog_product_entity_varchar where entity_id='.$productId.' and attribute_id ='.$attribId.'');
		
		return $rows['value'];	
	}
	
	public function _getProductDropLabel($productId, $list=false)
	{
		
		$attribId = $this->_getProductNameAttributeId();
		$productLabel = null;				
		$parentproductId = $this->_getLinkedProductParent($productId);
		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		
		$sql = 'SELECT value FROM catalog_product_entity_varchar where attribute_id ='.$attribId.' and entity_id='.$parentproductId;
		$rows = $readonce->fetchRow($sql);
		if($rows)
			{
				$productLabel = $rows['value'];		
			}
		
		$sql = 'SELECT a.entity_id, a.value as frontend_label FROM catalog_product_entity_varchar a ';
		$sql.= 'inner join catalog_product_entity b on ';
		$sql.= 'a.entity_id = b.entity_id ';
		$sql.= 'where a.attribute_id ='.$attribId.' and a.entity_id='.$productId;
		
		$rows = $readonce->fetchRow($sql);
				
		$dropLabel =  $rows['frontend_label'];	
		$price = $this->_getPrice($productId);
		
		if($list)
		{
			if($productLabel)
			{
				return str_replace($productLabel, '', $dropLabel).'....$'.number_format($price, 2, '.', '');	
									
			}	
			else
			{
				return $dropLabel;		
			}
		}
		else
			{
			return $dropLabel;	
			}	
	}
	
	public function _getAttributeId($productId)
	{
		$eti = $this->_getProductEntityTypeId();
		$atid = $this->_getSuperAttribId($productId);
		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$rows = $readonce->fetchRow('SELECT attribute_id FROM eav_attribute where entity_type_id='.$eti.' and attribute_id='.$atid);
		
		return $rows['attribute_id'];		
	}
	
	
	public function countProductOption($productId)
	{
		$select = null;
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('catalog_read');
		
		$CatalogProductSuperLink = $resource->getTableName('catalog/product_super_link');
		
		$select = $read->select('product_id')->from(array('cpsl'=>$CatalogProductSuperLink))
			->where('cpsl.parent_id=?', $productId);			
		
		$val = $read->fetchAll($select);
	
		return count($val);
					
	}
	
	protected function _getProductEntityTypeId()
	{
		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$rows = $readonce->fetchRow('SELECT entity_type_id FROM eav_entity_type where entity_type_code = \'catalog_product\'');
		
		return $rows['entity_type_id'];	
			
	}
	
	protected function _getProductNameAttributeId()
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$sql = 'SELECT attribute_id FROM eav_attribute where attribute_code = \'name\' and ';
		$sql.= 'entity_type_id = (SELECT entity_type_id FROM eav_entity_type where entity_type_code = \'catalog_product\')';
		
		$rows = $readonce->fetchRow($sql);
		return $rows['attribute_id'];	
	}
	
	protected function _getProductPriceAttributeId()
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$sql = 'SELECT attribute_id FROM eav_attribute where attribute_code = \'price\' and ';
		$sql.= 'entity_type_id = (SELECT entity_type_id FROM eav_entity_type where entity_type_code = \'catalog_product\')';
		
		$rows = $readonce->fetchRow($sql);
		return $rows['attribute_id'];	
	}
	
	protected function _getProductAttributePathId()
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$sql = 'SELECT attribute_id FROM eav_attribute where attribute_code = \'url_path\' and ';
		$sql.= 'entity_type_id = (SELECT entity_type_id FROM eav_entity_type where entity_type_code = \'catalog_product\')';
		$rows = $readonce->fetchRow($sql);
		return $rows['attribute_id'];		
	}
	
	public function _getProductUrl($productId)
	{
		$attribId = $this->_getProductAttributePathId();
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$rows = $readonce->fetchRow('SELECT value FROM catalog_product_entity_varchar where entity_id='.$productId.' and attribute_id ='.$attribId);
		
		return $rows['value'];	
		
	}
	
	public function countProductAttribute($productId, $count=true)
	{
		$val = 0;
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('catalog_read');
		
		$CatalogProductSuperAttribute = $resource->getTableName('catalog/product_super_attribute');
		
		$select = $read->select('product_id')->from(array('cpsa'=>$CatalogProductSuperAttribute))
			->where('cpsa.product_id=?', $productId);			
		
		$val = $read->fetchAll($select);
		
		if($count)
		{
			return count($val);
		}
		else
			{
			return $val;	
			}		
	}
	
	public function _getProductIdSuperAttributeId($superattributeId)
	{
		$val = 0;		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$row = $readonce->fetchRow('SELECT product_id FROM catalog_product_super_attribute where product_super_attribute_id='.$superattributeId);
		
		if($row)
		{
			$val = $row['product_id'];	
		}	
		
		return $val;	
	}
	
	public function _getproductSuperAttributeId($productId, $attributeId)
	{
		$val = 0;		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$val = $readonce->fetchRow('SELECT product_super_attribute_id FROM catalog_product_super_attribute where product_id='.$productId.' and attribute_id ='.$attributeId);
			
		if($row)
		{
			$val = $row['product_super_attribute_id'];	
		}	
		
		return $val;	
	}
	
	protected function _getLinkedProductParent($productId)
	{
		$id = 0;
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('catalog_read');
		
		$CatalogProductSuperLink = $resource->getTableName('catalog/product_super_link');
		
		$select = $read->select('parent_id')->from(array('cpsl'=>$CatalogProductSuperLink))
			->where('cpsl.product_id=?', $productId);			
		
		$val = $read->fetchRow($select);
		if($val)
			{
			foreach ($val as $item)
				{
				$id = $item;	
				}	
			}
		return $id;	
	}
	
	public function _getLinkedProducts($productId)
	{
		$select = null;
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('catalog_read');
		
		$CatalogProductSuperLink = $resource->getTableName('catalog/product_super_link');
		
		$select = $read->select('product_id')->from(array('cpsl'=>$CatalogProductSuperLink))
			->where('cpsl.parent_id=?', $productId);			
		
		return $val = $read->fetchAll($select);
	}
	
	public function _getLowPrice($productId)
	{
		$pa = array();
		$price = 'No price available';
		$val = $this->_getLinkedProducts($productId);
		
		if(count($val) > 0)
		{
			foreach	($val as $item)
			{
				$pa[] = $this->_getPrice($item['product_id']);
			}
			
			$price = min($pa);			
		}	
		
		return $price;		
	}
	
	public function _getCategoryIds($productId)
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$row = $readonce->fetchRow('SELECT category_ids FROM catalog_product_entity where entity_id='.$productId);
		
		if($row)
		{
			return $row['category_ids'];	
		}
		else
			{
			return 0;
			}
				
	}
	
	
	/**
	 * This is method _getLinkedAtribProductsHtml
	 *
	 * @param int $productId Product entity id
	 * @return array Returns a configured array for HTML dropdowns
	 *
	 */
	public function _getLinkedAtribProductsHtml($productId, $atid=0)
	{
		$var = array();
				
		$val = $this->_getLinkedProducts($productId);
		
		if($val)
		{
		
		if($atid == 0)
			{
				$atid = $this->_getSuperAttribId($productId);
			}			
		
		foreach ($val as $items)
		{
			$var[] = array($items['product_id'], $this->_getDropHtmlValueString($items['product_id'], $atid));
		}	
		}	
		return $var;	
	}
	
	public function _getLinkedProductsHtml($productId)
	{
		$var = array();
				
		$val = $this->_getLinkedProducts($productId);
		
		if($val)
		{
				
		foreach ($val as $items)
		{
			$var[] = array($items['product_id'], $this->_getProductDropLabel($items['product_id'],true));
		}	
		}	
		return $var;			
	}
	
		
	/**
	 * This is method is_ProductAvailable
	 *
	 * @param int $productId Product Id
	 * @param bool $qty Get Qty true or false
	 * @return mixed Returns either 1 for instock or 0 for out, or Qty available
	 *
	 */
	public function is_ProductAvailable($productId, $qty=false)
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$row = $readonce->fetchRow('SELECT stock_status, qty FROM cataloginventory_stock_status where product_id='.$productId);
		if($qty){
			return $row['qty'];	
		}
		else{
			return $row['stock_status'];	
		}			
	}
	
	public function getCategoryParentId($catId)
	{		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$row = $readonce->fetchRow('SELECT parent_id, level FROM catalog_category_entity where entity_id ='.$catId);
		if($row)
			{
			return $row;	
			}
		else
			{
			return array('parent_id'=>0,'level'=>0);	
			}		
	}
}



?>