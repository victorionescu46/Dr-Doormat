<?php
class Wdc_Catalog_Block_Bridge extends Mage_Core_Block_Template
{
	public function getUrlbyMidwestId($Id, $type='category')
	{
		
		switch($type){
			case 'category': //Choose by Category:
				$url = $this->getCatUrlbyMidwestId($Id);
				break;
			case 'product': //choose by product
				$url = $this->getProductUrlbybyMidwestId($Id);
				break;
			default:
				$url = 'catalogsearch/advanced?'.$type.'NotFound=1';
				break;		
		}
		
		return $url;		
	}
	
	public function getCatUrlbyMidwestId($Id)
	{
		$Mid = new Wdc_Catalog_Model_Bridge();
		return $Mid->getCategoryUrlbyId($Mid->getEntityIdbyMidwestCatId($Id));
	}
	
	public function getProductIdbyMidwestId($Id)
	{
		$Mid = new Wdc_Catalog_Model_Bridge();
		return $Mid->getProductIdbyMidwestId($Id);
	}
	
	public function getProductUrlbybyMidwestId($Id)
	{
		$_productId = new Wdc_Catalog_Block_Product();
		return $_productId->getProductUrl($this->getProductIdbyMidwestId($Id));	
	}
}

?>