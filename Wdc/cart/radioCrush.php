<?php
include('../../app/Mage.php');  
Mage::App('default');

if(isset($_GET['itemLineNumber']))
{
	$pid = $_GET['itemLineNumber'];
}
else
{
	$pid = 0;	
}

if(isset($_GET['check']))
{
	$check = $_GET['check'];
}
else
{
	$check = false;
}



$cartChecker = new Wdc_Checkout_Block_Cart();

$cartChecker->setOptionCrush($pid, $check);

echo $cartChecker->setCrushRadio($pid);

?>