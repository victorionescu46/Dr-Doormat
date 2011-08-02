<?php

include('/home/midwestsupplies/public_html/app/Mage.php');  
Mage::App('default');

$loader = new Wdc_QuickOrder_Block_Loader();

echo $loader->getSessionId()

?>