<?php
include('../../app/Mage.php');  
Mage::App('default');

$sid=0;
if(isset($_GET['sid']))
{
	$sid = $_GET['sid'];	
}

$deletelist = false;
if(isset($_GET['deletelist']))
{
	$deletelist = $_GET['deletelist'];	
}



try{

	$obj = new Wdc_Checkout_Block_Cart();
	if($obj->addListCart($sid))
	{
		
		if($deletelist){
			$list = new Wdc_QuickOrder_Block_Loader();
			$list->deletelist($sid);	
		}	
		header('Location: /checkout/cart/?QuickOrder=1');	
		
	}
	else{
		header('Location: /QuickOrder/?error=1');
	}
	
}
catch(exception $e)
{
	echo $e->getMessage();	
}



?>