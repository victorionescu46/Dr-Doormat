<?php

class Wdc_Catalog_Block_Price extends Wdc_Catalog_Block_Product
{
	public function setPrice($price)
	{
		if(!isset($_SESSION['price']))
		{
			$_SESSION['price'] = $price;				
		}					
		return $_SESSION['price'];
	}
}

?>