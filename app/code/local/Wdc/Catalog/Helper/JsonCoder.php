<?php

class Wdc_Catalog_Helper_JsonCoder extends Mage_Core_Helper_Abstract
{
	protected function setOptionsBlock()
	{
		return new Wdc_Catalog_Block_Options();
	}	
	protected function setConfigurableBlock()
	{
		return new Wdc_Catalog_Block_Configurable();	
	}
	
	protected function setOptionHelper()
	{
		return new Wdc_Catalog_Helper_OptionsHtml();	
	}
	
	public function WdcJsonEcode($array)
	{
		return json_encode($array);	
	}
	
	public function WdcJsonDecode($array)
	{
		return json_encode($array);	
	}
	
	public function WdcJsonConfigurableEncode($productId, $attributeId)
	{
		$html = '<SCRIPT type="text/javascript">';
		$html.= json_encode($this->setOptionsBlock()->geteavOptionsbyAttributeIdParentId($attributeId, $productId));	
		$html.= '</script>';	
		
		return $html;
	}
}

?>