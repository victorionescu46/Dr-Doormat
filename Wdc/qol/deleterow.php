<?php

//include_once('/home/midwestsupplies/public_html/Wdc/qol/class/listClass.php');
include('/home/midwestsupplies/public_html/app/Mage.php');  
Mage::App('default');

$lid=0;
if(isset($_GET['lid']))
{
	$lid = $_GET['lid'];	
}


$obj = new Wdc_QuickOrder_Block_Loader();

echo $obj->deleteRow($lid);



?>
