<?php
include('../../app/Mage.php');  
Mage::App('default');

$group = new  Wdc_Catalog_Block_Grouped();

$getCart = $_GET['product'];

print_r($getCart);

echo '<br><br>';

//echo $Wdc_Helper->submitGroupedCart($cart);


echo $group->createProductArray($getCart);




//echo $Wdc_Helper->AddCartGroupURL($_SERVER['QUERY_STRING']);
	
//foreach ($cart as $product)
//{
//
//	$cnt = 0;$_product=0;$_options=null;$_superAttributes=null;$qty=0;
//	foreach ($product as $options)
//	{
//		switch($cnt)
//		{
//			case 0:
//				$_product = $options;	
//				break;
//			case 1:						
//				$_options = $options;
//				break;
//			case 2:
//				$_superAttributes = $options;
//				break;
//			case 3:
//				$qty = $options;
//				break;
//			default:
//				break;
//		}
//		$cnt++;
//	}
//	if($_product != 0 && $qty != 0)
//	{
//		echo 'made<br>';
//		//echo implode(',', $_options) .'<br>';
//		
//		$var = "";
//		reset($_options);
//		while (list($key, $val) = each($_options)) {
//			$var.= "'".$key."' => '".$val."',";
//		}
//		
//		echo $var;
//
//	}	
//	
//	
//}
echo '<h1>end</h1>';	

?>