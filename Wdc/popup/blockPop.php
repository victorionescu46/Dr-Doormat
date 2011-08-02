<?php
include('../../app/Mage.php');  
Mage::App('default');

if(isset($_GET["bid"]))
{
	$bid=$_GET["bid"];
}
else{
	
	$bid=0;
}

$blockReader = new Wdc_Cms_Block_Block();

?>
<h2 class="blockheader"><?php echo $blockReader->getTitle($bid) ?></h2>
<p class="blocktext">
<?php echo $blockReader->getContent($bid) ?>
</p>


