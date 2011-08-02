<?php
class Wdc_Catalog_Block_Configurable extends Wdc_Catalog_Block_Product
{
	
	protected function setConfigModel()
	{
		return new Wdc_Catalog_Model_CatalogJson();	
	}
		
		
	public function decorateProductLinksJson($productId)
	{
		
		$html = '<SCRIPT type="text/javascript">';
		$html.= json_encode($this->setConfigModel()->_getLinkedProductsArray($productId));		
		$html.= '</script>';	
		
		return $html;
	}	
	
	public function setConfigArray()
	{
		if(!isset($_SESSION['ConfigArray']))
		{
			$_SESSION['ConfigArray'] = array('option_id'=>'0', 'title'=>'Please Choose');			
		}					
		return $_SESSION['ConfigArray'];	
	}
	
}


?>