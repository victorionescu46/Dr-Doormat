<?php

require_once(dirname(__FILE__) . '/../../app/Mage.php');
require_once(dirname(__FILE__) . '/../cim/cimutils.php');

// TODO: Ideally should work on Magento objects and not in raw DB

if (!Mage::isInstalled()) {
  echo "Application is not installed yet, please complete install wizard first.";
  exit;
}

$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);
Mage::app('admin')->setUseSessionInUrl(false);
Mage::getConfig()->init();

function mageGetRows($query) {
  $result = array();

  $w = Mage::getSingleton('core/resource')->getConnection('core_write');
  $query_result = $w->query($query);

  if(!empty($query_result)) {
    while(true) {
      $row = $query_result->fetch();
      if(empty($row)) {
        break;
      } else {
        $result []= $row;
      }
    }
  }

  return $result;
}

$the_id = 45;
$coupon_code = 'ALLABOARD';
$orders = mageGetRows("select count(*) as `count` from sales_flat_order where customer_id = " . intval($the_id) . " and coupon_code = '" . preg_replace('/[^a-z0-9]/i', '', $coupon_code) . "'");
var_dump($orders[0]['count']);
