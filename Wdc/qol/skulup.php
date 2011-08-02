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

//$gettheSku = new Wdc_QuickOrder_Block_Loader();
//
//echo $gettheSku->getSku($sku);


$sku = '\''.$sku.'\'';


$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');

$sql="SELECT a.entity_id, b.value as des, c.value as price FROM catalog_product_entity a ";
$sql=$sql."inner join catalog_product_entity_varchar b ";
$sql=$sql."on a.entity_id = b.entity_id ";
$sql=$sql."left join catalog_product_entity_decimal c ";
$sql=$sql."on a.entity_id = c.entity_id ";
$sql=$sql."WHERE (a.sku=".$sku.") and  (b.attribute_id = 96)  and (c.attribute_id =99) and (a.type_id <> 'configurable') ";
$sql=$sql."and (a.entity_id not in (SELECT product_id FROM catalog_category_product_index where visibility =1))";

$result = $readonce->fetchAll($sql);


if(!$result)
{
	echo 'SKU not found';
}
else{
	
	foreach ($result as $row)
	{
		$rowHtml = '<table width="500" border="0"><tr><td width="250">';
		$rowHtml = $rowHtml.$row['des'].'</td><td width="50"><b>$'.money_format('%(#10n',$row['price']).'</b></td>';
		$rowHtml = $rowHtml.'<td width="20"><input type="text" size="5" name="qty" id="qty" value="1" /></td>';
		$rowHtml = $rowHtml.'<td wodth="20"><input type="button" value="Add to list" onclick="getList(document.getElementById(\'qty\').value,'.$row['entity_id'].')"; /></td>';
		$rowHtml = $rowHtml.'</tr></table>';
	}
	
	echo $rowHtml;
}

?>