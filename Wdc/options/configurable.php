<?php
include('../../app/Mage.php');  
Mage::App('default');

$Wdc_Helper = new Wdc_Catalog_Helper_OptionsHtml();

if(isset($_GET["price_id"]))
{
	$_productId=$_GET["price_id"];
}
else{
	
	$_productId=0;
}

if(isset($_GET["option_id"]))
{
	$optionId=$_GET["option_id"];
}
else{
	
	$optionId=0;
}

?>