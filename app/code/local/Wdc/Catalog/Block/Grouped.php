<?php
class Wdc_Catalog_Block_Grouped extends Wdc_Catalog_Block_Options
{
	protected function setCoreBlock()
	{
		return new Mage_Core_Block_Abstract(); 	
	}
	
	protected function setWdcCartBlock()
	{
		return new Wdc_Checkout_Block_Cart();	
	}
	
	public function getRawCartUrl($url)
	{
		//return $this->createProductArray($url);	
		return var_dump($this->createProductArray($url));	
		//return $url;
	}
	
	protected function updateUrlHtml($url)
	{
		//$url = $this->
		$url = str_ireplace('%5B', '[', $url);
		$url = str_ireplace('%5D', ']', $url);
		$url = str_ireplace('&related_product=', '', $url);
		$url = str_ireplace('=', '=\'', $url);
		$url = str_ireplace('&', '\'&', $url);
		$url = str_ireplace('\'\'', '\'0\'', $url);
		$url.= '\'';	
		
		return $url;
	}	
	
	
	public function createProductArray($cartitems)
	{
		foreach ($cartitems as $product)
		{

			$cnt = 0;$_product=0;$_options=null;$_superAttributes=null;$qty=0;
			foreach ($product as $options)
			{
				switch($cnt)
				{
					case 0:
						$_product = $options;	
						break;
					case 1:						
						$_options = $options;
						break;
					case 2:
						$_superAttributes = $options;
						break;
					case 3:
						$qty = $options;
						break;
					default:
						break;
				}
				$cnt++;
			}
			if($_product != 0 && $qty != 0)
			{
							
				$this->WdcAddGroupedCart($_product, $qty, $_superAttributes, $_options);	
			}		
		}
		
		return header('Location: /checkout/cart/');

	}
	
	public function WdcAddGroupedCart($productId, $qty=1, $superAttributes=null, $options=null)
	{
		
		
		$productId = (int)$productId;
		Mage::getSingleton('core/session', array('name'=>'frontend'));			
		$cart = Mage::getSingleton('checkout/cart');


		
		$cart->addProduct($productId, array('qty' =>$qty, 'super_attribute'=>$superAttributes, 'options'=>$options));
				
		$cart->save();
		
	}
		
	protected function getArrayString($_option)
	{
		$var = "";
		reset($_options);
		while (list($key, $val) = each($_options)) {
			$var.= "'".$key."' => '".$val."',";
		}
		
		return $var;
	}
	
	protected function insertCartItem($_group)
	{
		$product = '';
		$productGroup = '';
		$options = '';
		$placeholder = '';
		$count = 1;
		$_product = array();
		$_option = array();
		$_arReturn = array();
		$i = 0;
		foreach ($_group as $item)
		{
			
			if(stripos($item, 'product=') === false)
			{
				$options.= $item;			
			}
			else
			{
				
				$product = $item;
				if($count != 1)
				{
					$count = 1;	
					
					$productGroup.= $product;					
				}
				else
				{
					$productGroup = $product;						
				}
							
			}
					
			$count++;		
		}
			
		return $productGroup;
	}
		
	protected function getProductArrays($_group)
	{
	
		$count = 1;
		$_product = array();
		$_option = array();
		$_arReturn = array();
		
		foreach ($_group as $item)
		{			
			
			echo array_search('product=', $_group);
			if(!empty($item))
			{
					
							if(stripos($item, 'product=') === false)
							{
								if($count != 1)
								{
									$_option[] = $item;
								}		
					else
						{
						$_option = $item;
							}
							}
							else
							{
								
							
								if($count != 1)
									{
									$count = 1;	
									$_arReturn[] = $item;
				
									//$_product = $item;
									}
								else
									{
									//$_arReturn[] = $_option;
									
									//$_arReturn[] = array($_item, $_option);
									$_arReturn[] = $item;				
													
									}				
							}
				
				$count++;		
			}
		}
		//$_arReturn = array($_option, $_product);
		//return $productGroup;
		return $_arReturn;
	}
	
	protected function getItemName($item)
	{
		if(stripos($item, 'product=') > 1)
		{
			return 'product';
		}	
		if(stripos($item, 'options') > 1)
		{
			return 'options';
		}	
		if(stripos($item, 'attribute') > 1)
		{
			return 'attribute';
		}	
		if(stripos($item, 'qty=') > 1)
		{
			return 'qty';
		}	
	}
			
}

?>