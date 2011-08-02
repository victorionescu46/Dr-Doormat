<?php
include('../../app/Mage.php');  
Mage::App('default');

//$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');

if(isset($_GET["price_id"]))
{
	$productId=$_GET["price_id"];
}
else{
	
	$productId=0;
}

if(isset($_GET["ptype"]))
{
	$pagetype=$_GET["ptype"];
}
else{
	
	$pagetype='1';
}

$obj = new Wdc_Catalog_Block_Product();
?>

<?php if($pagetype == '1'): ?>

<?php echo $obj->getProductHtmlUrl($productId); ?>

<?php else: ?>
	
	
<?php
//$_helper = $this->helper('catalog/output');
$_product = $obj->setProductbyId($productId);	
?>	
	<div id="productLabel<?php echo $productId ?>">
		
		  <h2 class="produt-name">
                <?php echo $_product->getName(); ?>
            </h2>
		
<p><b>Product Number: </b> <?php echo $_product->getSku(); ?>
 </p>
 
<p>
<b>Weight: </b> <?php echo $_product->getWeight(); ?>
</p>

<?php if($_product->isSaleable()): ?>
    <p class="availability">Availability: <span class="in-stock">In stock</span></p>
<?php else: ?>
    <p class="availability">Availability: <span class="out-of-stock">Out of stock</span></p>
<?php endif; ?>
</div>
	
<?php endif; ?>