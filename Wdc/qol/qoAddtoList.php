<?php
//include_once('/home/midwestsupplies/public_html/Wdc/qol/class/listClass.php');
include('/home/midwestsupplies/public_html/app/Mage.php');  
Mage::App('default');


$qty = 1;
$eid = 0;

if(isset($_GET['qty']))
{
	$qty = $_GET['qty'];	
}

if(isset($_GET['eid']))
{
	$eid = $_GET['eid'];
}


$obj = new Wdc_QuickOrder_Block_Loader();

echo $obj->addQuickorderList($eid, $qty);

//$obj = new Wdc_QuickOrder_Model_listClass();
//$list = $obj->_addList($eid, $qty, $sessionId);

//echo $obj->getTableHtml($list)

?>

