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

if(isset($_GET['RadioPage']))
{
	$radiopage = $_GET['RadioPage'];
}
else
{
	$radiopage = false;
}

$cartChecker = new Wdc_Checkout_Block_Cart();

$cartChecker->setOptionCrush($pid, $check);
	
/*if(!$radiopage)
{
	echo $cartChecker->setCrushHtml($pid);
}
else
{*/	
	echo $cartChecker->setCrushRadio($pid);
//}

?>