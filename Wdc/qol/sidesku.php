<?php
include('/home/midwestsupplies/public_html/app/Mage.php');  
Mage::App('default');


if(isset($_GET["sku"]))
{
	$sku=$_GET["sku"];
}
else{
	
	$sku=0;
}



$loader = new Wdc_QuickOrder_Block_Loader();

$skus = $loader->getSkuList($sku);
	if($skus)
	{
		
	$helper = new Wdc_QuickOrder_Helper_Ajax();
	echo $helper->getSkuLookupListLink($skus);
	
		
	/*foreach ($skus as $sku)
		{
		echo $sku['sku'].'<br />';	
		}*/
	}
else{
	
	echo 'Empty List';
}


?>