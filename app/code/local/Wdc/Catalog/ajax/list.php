<?php

if(isset($_GET["price_id"]))
{
	$q=$_GET["price_id"];
}
else{
	$q=1;	
}

$con = mysql_connect('localhost', 'midwestsupplies', 'center24train');
if (!$con)
{
	die('Could not connect: ' . mysql_error());
}

mysql_select_db("midwestsupplies", $con);

$sql="SELECT * FROM catalog_product_entity_decimal WHERE attribute_id = 99 and entity_id = '".$q."'";

$result = mysql_query($sql);

echo '<span class="list-price">';

while($row = mysql_fetch_array($result))
{
	echo '$'.money_format('%(#10n',$row['value']);
	
}
echo '</span>';
?>
<p><button class="button" onclick="setLocation('/checkout/cart/add/product/<?php echo $q ?>')"><span> Add to Cart</span></button></p>
<?php mysql_close($con) ?>