<?php
/**
 * Midwest Web and Data Consulting
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Midwest Web and Data Consulting to newer
 * versions in the future. If you wish to customize Midwest Web and Data Consulting for your
 * needs please refer to http://www.webdataconsulting.com for more information.
 *
 * @category   Wdc
 * @package    Wdc_Catalog
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product option model
 *
 * @category   Wdc
 * @package    Wdc_Catalog
 * @author     Midwest Web and Data Consulting 
 */
class Wdc_Catalog_Model_Options extends Mage_Catalog_Model_Product_Option
{
	protected function setDataCon()
	{
		return Mage::getSingleton('core/resource')->getConnection('core_read');
	}
	
	
	public function _getCatalogProductSuperLinkParentId($productId)
	{
		$var = 0;
		//$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $this->setDataCon()->fetchRow('SELECT parent_id FROM catalog_product_super_link where product_id ='.$productId); 
		if($row)
		{
			$var = $row['parent_id'];					
		}
		
		return $var;
	}
	
	public function _isParentCatalogProductSuperLink($productId)
	{
		$var = false;
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchAll('SELECT product_id FROM catalog_product_super_link where parent_id ='.$productId); 
		if($row)
		{
			$var = true;					
		}
		
		return $var;
	}
	
	public function _getCatalogProductOptionArray($productId)
	{		
		$var = 0;
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchAll('select * from catalog_product_option where product_id ='.$productId.' order by sort_order');
		
		if($row)
		{
			$var = $row;				
		}
		
		return $var;
	}
	
	public function _getCatalogProductOptionRow($optionId)
	{		
		$var = 0;
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchRow('select * from catalog_product_option where option_id ='.$optionId);
		
		if($row)
		{
			$var = $row;				
		}
		
		return $var;
	}
	
	
	public function _getCatalogProductOptionCount($productId)
	{		
		$var = 0;
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchRow('select count(option_id) as cnt from catalog_product_option where product_id ='.$productId);
		if($row)
		{			
			$var = $row['cnt'];					
		}
		
		return $var;
	}
	
	public function _getCatalogProductOptionTypeValueArray($optionId)
	{
		$var = array('0');
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchAll('SELECT option_type_id FROM catalog_product_option_type_value where option_id ='.$optionId.' order by sort_order');
		
		if($row)
		{
			foreach ($row as $item)
			{
				$var = $item;	
			}	
		}
		
				
		return $var;	
	}
	
	public function _getCatalogProductOptionTitle($optionId)
	{
		$var = 'Label not defined';
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchRow('SELECT title from catalog_product_option_title where option_id ='.$optionId);	
		if($row)
		{
			$var = $row['title'];	
		}
		
		return $var;
	}
	
	public function _getCatalogProductOptionTypeTitle($optionId)
	{
		$var = 'Items not defined';
		$sql = 'SELECT option_type_id, title FROM catalog_product_option_type_title ';
		$sql.= 'where option_type_id in (SELECT option_type_id FROM catalog_product_option_type_value where option_id ='.$optionId.')';
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$row = $readonce->fetchAll($sql);
		if($row)
			{			
				$var = $row;			
			}
			
		return $var;
	}
	
	public function _getCatalogProductOptionTypePrice($optionTypeId)
	{
		$var = 'price not defined';
		$sql = 'SELECT price FROM catalog_product_option_type_price where option_type_id='.$optionTypeId;			
		$row = $this->setDataCon()->fetchRow($sql);
		if($row)
			{			
				$var = $row['price'];			
			}
			
		return $var;
	}
	
	public function _getCatalogProductOptionTypePriceTitle($optionId)
	{
				
		$var = 'Items not defined';
		$sql = 'SELECT a.option_type_id, a.title, b.price FROM catalog_product_option_type_title a ';
		$sql.= 'inner join catalog_product_option_type_price b ';
		$sql.= 'on a.option_type_id = b.option_type_id ';
		$sql.= 'where a.option_type_id in (SELECT option_type_id FROM catalog_product_option_type_value where option_id ='.$optionId.')';
		$row = $this->setDataCon()->fetchAll($sql);
		if($row)
		{			
			$var = $row;			
		}
		
		return $var;
	}
	
	public function _getOptionsbyAttributeId($attributeId)
	{
		$var = 0;
		$sql = 'SELECT a.option_id, a.value FROM eav_attribute_option_value a ';
		$sql.= 'inner join catalog_product_option_type_price b ';
		$sql.= 'on a.option_id = b.option_type_id ';
		$sql.= 'where option_id in (SELECT option_id FROM eav_attribute_option where attribute_id ='.$attributeId.')';	
		
		$row = $this->setDataCon()->fetchAll($sql);
		if($row)
		{			
			$var = $row;			
		}
		
		return $var;
	}
	
	public function _geteavOptionsbyAttributeIdParentId($attributeId, $parentId)
	{
		$var = 'Items not defined';
		$sql = 'SELECT distinct a.value as option_id, b.value as title FROM catalog_product_entity_int a ';
		$sql.= 'inner join eav_attribute_option_value b ';
		$sql.= 'on a.value = b.option_id ';
		$sql.= 'where (a.attribute_id ='.$attributeId.')  and ';
		$sql.= 'a.entity_id in (SELECT product_id FROM catalog_product_super_link where parent_id ='.$parentId.')';
		
		$row = $this->setDataCon()->fetchAll($sql);
		if($row)
		{			
			$var = $row;			
		}
		
		return $var;
	}
}