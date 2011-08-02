<?php

require_once(dirname(__FILE__) . '/../../app/Mage.php');
require_once(dirname(__FILE__) . '/../cim/cimutils.php');

if (!Mage::isInstalled()) {
  echo "Application is not installed yet, please complete install wizard first.";
  exit;
}

$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);
Mage::app('admin')->setUseSessionInUrl(false);
Mage::getConfig()->init();

$address_data = array('firstname' => 'Neal', 'lastname' => 'Price', 'street' => 'St. 1', 'city' => 'New York', 'region' => 'NY', 'postcode' => '90210', 'country_id' => 'US', 'telephone' => '15551212222', 'fax' => '15551212222');

$payment_data = array('cc_number' => '4111111111111111', 'cc_exp_year' => '2012', 'cc_exp_month' => '12');

var_dump(AuthnetCimHelper::magentoCreateOrUpdateForOrder(1, $payment_data, $address_data));
