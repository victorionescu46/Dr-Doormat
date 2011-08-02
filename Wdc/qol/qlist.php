<?php
include_once('/home/midwestsupplies/public_html/Wdc/qol/class/listClass.php');


$sid = 0;
if(isset($_GET['sid']))
{
	$sid = $_GET['sid'];	
}

if($sid ===0)
	{
	$sid = session_id();	
	}

$obj = new Wdc_QuickOrder_Model_listClass();

$list = $obj->_getArrayList($sid);

echo $obj->getTableHtml($list)


?>