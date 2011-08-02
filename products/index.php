H<?php
include('../app/Mage.php');  
Mage::App('default');

if(isset($_GET['SubCat']))
	{
	$MidCat =$_GET['SubCat'];
	
	$_Helper = new Wdc_Catalog_Helper_CategoryFinder();
		
	$url = "location: /".$_Helper->getCategoryUrl($MidCat);
	
	header($url);
	exit;
	}
	
	
if(isset($_GET['ProdID']))
{
	$MidwestId =$_GET['ProdID'];
	
	$_Helper = new Wdc_Catalog_Helper_CategoryFinder();
	
	//echo $_Helper->Test($MidwestId);
	
	$url = "location: /".$_Helper->getProductUrl($MidwestId);
		
	header($url);
	exit;
}
		
$requestUrl = $_SERVER["REQUEST_URI"];
	
$_linkFinder = new Wdc_Cms_Helper_LinkFinder();


	
	//$url = 'location: /catalogsearch/advanced?PageNotFound=1';
	header($_linkFinder->setUrl($requestUrl));
	exit;

?>