<?php

class Wdc_Catalog_Helper_CategoryFinder extends Mage_Core_Helper_Abstract
{
	public function getCategoryUrl($MidwestId)
	{
		$Midwest = new Wdc_Catalog_Block_Bridge();
		return $Midwest->getUrlbyMidwestId($MidwestId);
	}
	
	public function getProductUrl($MidwestId)
	{
		$Midwest = new Wdc_Catalog_Block_Bridge();
		return $Midwest->getUrlbyMidwestId($MidwestId, 'product');
	}
	
	
	public function Test($MidwestId, $attrib=929)
	{
	
		$Midwest = new Wdc_Catalog_Model_Bridge();		
		return $Midwest->getProductIdbyMidwestId($MidwestId);
	}
		
	public function setOldUrl($url)
	{
		$redir = false;
		switch($url)
		{
			case '/catalog_download.asp':
				$redir = 'Location: more/download-catalog.html';
				break;			
			default:
				$redir = false;
				break;
		}
		
		return $redir;	
	}
}

//$_SERVER["REQUEST_URI"]
?>