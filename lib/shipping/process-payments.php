<?php

require_once(dirname(__FILE__) . '/../cim/cimutils.php');

define('PRICEPERLBS', false);

$__payment_notifications = array();
$__payment_errors = array();
$__payment_error_header = '<strong>ERROR: </strong>';

// TODO: Ideally should work on Magento objects and not in raw DB

function initGlobalData() {
  global $__payment_errors;
  global $__payment_notifications;
  global $__payment_error_header;

  $__payment_notifications = array();
  $__payment_errors = array();
}

function initMagento() {
  require_once(dirname(__FILE__) . '/../../app/Mage.php');

  if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
  }
  
  $_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
  $_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);
  Mage::app('admin')->setUseSessionInUrl(false);
  Mage::getConfig()->init();
}

function getStoreIdFromOrderId($order_id) {
  $order = mageGetRows("select * from sales_flat_order where increment_id = " . __sanitize($order_id));
  if(count($order) > 0) {
    $order = $order[0];
    $store_id = $order['store_id'];

    return $store_id;
  }

  return null;
}

function getRealOrderId($order_id) {
  $order = mageGetRows("select * from sales_flat_order where increment_id = " . __sanitize($order_id));
  if(count($order) > 0) {
    $order = $order[0];
    $entity_id = $order['entity_id'];

    return $entity_id;
  }

  return null;
}

function getCustomerEmailFromOrderId($order_id) {
  $order = mageGetRows("select * from sales_flat_order where increment_id = " . __sanitize($order_id));
  if(count($order) > 0) {
    $order = $order[0];

    return $order['customer_email'];
  }

  return null;

}

function getTrackingLink($code) {
  return '<a href="http://www.google.com/search?q=' . rawurlencode($code) . '">' . rawurlencode($code) . '</a>';
}

function mageSendNewOrderEmail($order_id, $track_order_link) {

  $content_details = 'Thank you for placing your order with The Kosher Express. Your order is on its way!
<br/><br/>
Tracking number(s): ' . $track_order_link . '
<br/><br/>
If you have any questions regarding your order, please contact us at info@thekosherexpress.com.
<br/><br/>
As mentioned, The Kosher Express provided estimated prices at the time of purchase. The actual price provided in this receipt may have changed slightly from the original cost estimate due to standard variations in the weight of the product(s) ordered.
';

  try {
    // storeid, recipient_email, recipient_name, order_object, billing_address, paymentBlock
    $XML_PATH_EMAIL_IDENTITY = 'sales_email/order/identity';
    $XML_PATH_EMAIL_TEMPLATE = 'sales_email/order/template';
    $store_id = getStoreIdFromOrderId($order_id);
      
    $order_obj = Mage::getModel('sales/order');
    $order_obj->loadByIncrementId($order_id);
    $recipient_name = $order_obj->getCustomerEmail();
    $recipient_email = getCustomerEmailFromOrderId($order_id);
    $billing_address_obj = $order_obj->getBillingAddress();
    $payment_obj = $order_obj->getPayment();
  
    $paymentBlock = Mage::helper('payment')->getInfoBlock($payment_obj)->setIsSecureMode(true);
    $paymentBlock->getMethod()->setStore($store_id);
  
    $template = Mage::getStoreConfig($XML_PATH_EMAIL_TEMPLATE, $store_id);
    $template = '1';
    $mailTemplate = Mage::getModel('core/email_template');
    $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store_id))->sendTransactional(
        $template,
        Mage::getStoreConfig($XML_PATH_EMAIL_IDENTITY, $store_id),
        $recipient_email,
        $recipient_name,
        array(
            'order'         => $order_obj,
            'billing'       => $billing_address_obj,
            'payment_html'  => $paymentBlock->toHtml(),
            'extra_info_subj' => '[FINAL PRICE]',
            'content_details' => $content_details
        )
    );
  } catch(Exception $e) {
    $error_msg = sprintf("Exception sending a customer the final email regarding her order %s while processing the EGS XML; the customer might not have been notified by email about the final price.", strval($order_id));
    $__payment_notifications []= $__payment_error_header . $error_msg;
    $__payment_errors []=  $error_msg;
  }
}

function mageQuery($query) {
  $w = Mage::getSingleton('core/resource')->getConnection('core_write');
  $query_result = $w->query($query);

  return $query_result;
}

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

function __sanitize($value) {
  $value = strval($value);
  // return preg_replace('/\'|"|;|\n|\r|\x00|\x1a|\\\\/i','', $value);
  return "'" . mysql_escape_string($value) . "'";
}

function getTransIdByOrderId($order_id) {
  $order = mageGetRows("select * from sales_flat_order where increment_id = " . __sanitize($order_id));
  if(count($order) > 0) {
    $order = $order[0];
    $entity_id = $order['entity_id'];

    $payment = mageGetRows("select * from sales_flat_order_payment where parent_id = " . __sanitize($entity_id));
    if(count($payment) > 0) {
      return $payment[0]['cc_trans_id'];
    }
  }

  return null;
}

function getCapturedAmountByOrderId($order_id) {
  $order = mageGetRows("select * from sales_flat_order where increment_id = " . __sanitize($order_id));
  if(count($order) > 0) {
    $order = $order[0];
    $entity_id = $order['entity_id'];

    $payment = mageGetRows("select * from sales_flat_order_payment where parent_id = " . __sanitize($entity_id));
    if(count($payment) > 0) {
      return $payment[0]['amount_ordered'];
    }
  }

  return null;
}

function getProductUnitPriceByOrderIdAndCode($order_id, $code) {
  $order = mageGetRows("select * from sales_flat_order where increment_id = " . __sanitize($order_id));
  if(count($order) > 0) {
    $order = $order[0];
    $entity_id = $order['entity_id'];
    
    $item_price = mageGetRows("select * from sales_flat_order_item where order_id = " . __sanitize($entity_id) . ' and sku = ' . __sanitize($code));
    if(count($item_price) > 0) {
      $the_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $code);
      if(!empty($the_product)) {
        $product_weight = $the_product->getLbsPerPkg();
        if(!empty($product_weight)) {
          if(PRICEPERLBS) {
            return (floatval($item_price[0]['base_price_incl_tax']) - (floatval($item_price[0]['base_discount_amount']) / floatval($item_price[0]['qty_ordered'])));
          } else {
            return (floatval($item_price[0]['base_price_incl_tax']) - (floatval($item_price[0]['base_discount_amount']) / floatval($item_price[0]['qty_ordered']))) / floatval($product_weight);
          }
        }
      }
    }
  }

  return null;
}

function getProductPriceByOrderIdAndCode($order_id, $code) {
  $order = mageGetRows("select * from sales_flat_order where increment_id = " . __sanitize($order_id));
  if(count($order) > 0) {
    $order = $order[0];
    $entity_id = $order['entity_id'];
    
    $item_price = mageGetRows("select * from sales_flat_order_item where order_id = " . __sanitize($entity_id) . ' and sku = ' . __sanitize($code));
    if(count($item_price) > 0) {
      return floatval($item_price[0]['base_price_incl_tax']) - (floatval($item_price[0]['base_discount_amount']) / floatval($item_price[0]['qty_ordered']));
    }
  }

  return null;
}

function getShippingCostByOrderId($order_id) {
  $order = mageGetRows("select * from sales_flat_order where increment_id = " . __sanitize($order_id));
  if(count($order) > 0) {
    $order = $order[0];
    $entity_id = $order['entity_id'];

    // Yes, duplicate query 
    $shipping = mageGetRows("select * from sales_flat_order where entity_id = " . __sanitize($entity_id));
    if(count($shipping) > 0) {
      $shipping_raw = $shipping[0]['shipping_amount'];
      if(!empty($shipping_raw)) {
        return doubleval($shipping_raw);
      }
    }
  }

  return null;
}

function getItemQtyByWeight($code, $weight) {
  $the_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $code);
  if(!empty($the_product)) {
    $product_weight = $the_product->getLbsPerPkg();
    if(!empty($product_weight)) {
      return floatval($weight) / floatval($product_weight);
    }
  }

  return 0;
}

function getWeightByItemQty($code, $item_qty) {
  $the_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $code);
  if(!empty($the_product)) {
    $product_weight = $the_product->getLbsPerPkg();
    if(!empty($product_weight)) {
      return floatval($item_qty) * floatval($product_weight);
    }
  }

  return 0;
}

function updateOrderItemSum($order_id, $item_id, $sum, $item_qty, $weight) {

  $discount = 0;
  $sum = doubleval($sum);
  $sum_no_discount = $sum;

  $order = mageGetRows("select * from sales_flat_order where increment_id = " . __sanitize($order_id));
  if(count($order) > 0) {
    $order = $order[0];
    $entity_id = $order['entity_id'];

    $discount_rows = mageGetRows('select discount_percent from sales_flat_order_item where order_id = ' . __sanitize($entity_id) . ' and sku = ' . __sanitize($item_id));

    if(!empty($discount_rows)) {
      $discount_percent = doubleval($discount_rows[0]['discount_percent']);
      if(!empty($discount_percent)) {
        $sum_no_discount = $sum / (1 - ($discount_percent / 100));
        $discount = $sum_no_discount * ($discount_percent / 100);
      }
    }

    $formatted_sum = sprintf("%.4f", $sum);
    $formatted_sum_no_discount = sprintf("%.4f", $sum_no_discount);
  
    $formatted_item_qty = sprintf("%.4f", $item_qty);
  
    $formatted_weight = sprintf("%.4f", $weight);

    mageQuery("update sales_flat_order_item set row_weight = " . __sanitize($formatted_weight) . ' where order_id = ' . __sanitize($entity_id) . ' and sku = ' . __sanitize($item_id));


    mageQuery("update sales_flat_order_item set discount_amount = " . $discount . ' where order_id = ' . __sanitize($entity_id) . ' and sku = ' . __sanitize($item_id));
    mageQuery("update sales_flat_order_item set base_discount_amount = " . $discount . ' where order_id = ' . __sanitize($entity_id) . ' and sku = ' . __sanitize($item_id));


    mageQuery("update sales_flat_order_item set qty_ordered = " . __sanitize($formatted_item_qty) . ' where order_id = ' . __sanitize($entity_id) . ' and sku = ' . __sanitize($item_id));
    mageQuery("update sales_flat_order_item set qty_shipped = " . __sanitize($formatted_item_qty) . ' where order_id = ' . __sanitize($entity_id) . ' and sku = ' . __sanitize($item_id));

    mageQuery("update sales_flat_order_item set is_qty_decimal = 1 where order_id = " . __sanitize($entity_id) . ' and sku = ' . __sanitize($item_id));

    mageQuery("update sales_flat_order_item set base_row_total_incl_tax = " . __sanitize($formatted_sum_no_discount) . ' where order_id = ' . __sanitize($entity_id) . ' and sku = ' . __sanitize($item_id));
    mageQuery("update sales_flat_order_item set row_total_incl_tax = " . __sanitize($formatted_sum_no_discount) . ' where order_id = ' . __sanitize($entity_id) . ' and sku = ' . __sanitize($item_id));
    mageQuery("update sales_flat_order_item set base_row_total = " . __sanitize($formatted_sum_no_discount) . ' where order_id = ' . __sanitize($entity_id) . ' and sku = ' . __sanitize($item_id));
    mageQuery("update sales_flat_order_item set row_total = " . __sanitize($formatted_sum_no_discount) . ' where order_id = ' . __sanitize($entity_id) . ' and sku = ' . __sanitize($item_id));
    $quote = mageGetRows('select * from sales_flat_quote where reserved_order_id = ' . __sanitize($order_id));
    if(count($quote) > 0) {
      $quote_id = $quote[0]['entity_id'];
      mageQuery("update sales_flat_quote_item set base_row_total_incl_tax = " . __sanitize($formatted_sum_no_discount) . ' where quote_id = ' . __sanitize($quote_id) . ' and sku = ' . __sanitize($item_id));
      mageQuery("update sales_flat_quote_item set row_total_incl_tax = " . __sanitize($formatted_sum_no_discount) . ' where quote_id = ' . __sanitize($quote_id) . ' and sku = ' . __sanitize($item_id));
      mageQuery("update sales_flat_quote_item set base_row_total = " . __sanitize($formatted_sum_no_discount) . ' where quote_id = ' . __sanitize($quote_id) . ' and sku = ' . __sanitize($item_id));
      mageQuery("update sales_flat_quote_item set row_total = " . __sanitize($formatted_sum_no_discount) . ' where quote_id = ' . __sanitize($quote_id) . ' and sku = ' . __sanitize($item_id));

      mageQuery("update sales_flat_quote_item set is_qty_decimal = 1 where quote_id = " . __sanitize($quote_id) . ' and sku = ' . __sanitize($item_id));
      mageQuery("update sales_flat_quote_item set qty = " . __sanitize($formatted_item_qty) . ' where quote_id = ' . __sanitize($quote_id) . ' and sku = ' . __sanitize($item_id));



      mageQuery("update sales_flat_quote_item set discount_amount = " . $discount . ' where quote_id = ' . __sanitize($quote_id) . ' and sku = ' . __sanitize($item_id));
      mageQuery("update sales_flat_quote_item set base_discount_amount = " . $discount . ' where quote_id = ' . __sanitize($quote_id) . ' and sku = ' . __sanitize($item_id));
    }
  }
}

function updateOrderSum($order_id, $sum_shipping, $sum_no_shipping, $total_qty) {
  $real_discount = null;
  $real_discount_sum = null;

  $sum_no_discount = $sum_no_shipping;

  $formatted_sum_shipping = sprintf("%.4f", $sum_shipping);
  $formatted_sum_no_shipping = sprintf("%.4f", $sum_no_shipping);
  $formatted_total_qty = sprintf("%.4f", $total_qty);

  $order = mageGetRows("select * from sales_flat_order where increment_id = " . __sanitize($order_id));
  if(count($order) > 0) {
    $order = $order[0];
    $entity_id = $order['entity_id'];

    $discount_rows = mageGetRows('select base_discount_amount from sales_flat_order where entity_id = ' . __sanitize($entity_id));

    if(!empty($discount_rows)) {
      $discount = doubleval($discount_rows[0]['base_discount_amount']);
      if(!empty($discount)) {
        $discount = abs($discount);

        $original_sum_rows = mageGetRows('select base_subtotal_incl_tax from sales_flat_order where entity_id = ' . __sanitize($entity_id));

        if(!empty($original_sum_rows)) {
          $original_sum = doubleval($original_sum_rows[0]['base_subtotal_incl_tax']);
          if(!empty($original_sum)) {
            $real_discount = $discount / $original_sum;
            $sum_no_discount = doubleval($sum_no_shipping) / (1 - $real_discount);
            $real_discount_sum = $sum_no_discount - $sum_no_shipping;
          }
        }
      }
    }
  
    $formatted_sum_no_discount = sprintf("%.4f", $sum_no_discount);

    if(!empty($real_discount)) {
      mageQuery("update sales_flat_order set base_discount_amount = " . (-$real_discount_sum) . " where entity_id = " . __sanitize($entity_id));
      mageQuery("update sales_flat_order set discount_amount = " . (-$real_discount_sum) . " where entity_id = " . __sanitize($entity_id));
    }

    mageQuery("update sales_flat_order set state = 'complete', status = 'complete' where entity_id = " . __sanitize($entity_id));
    mageQuery("update sales_flat_order_grid set status = 'complete' where entity_id = " . __sanitize($entity_id));

    mageQuery("update sales_flat_order set total_qty_ordered = " . __sanitize($formatted_total_qty) . ' where entity_id = ' . __sanitize($entity_id));

    mageQuery("update sales_flat_order set base_subtotal = " . __sanitize($formatted_sum_no_discount) . ' where entity_id = ' . __sanitize($entity_id));
    mageQuery("update sales_flat_order set subtotal = " . __sanitize($formatted_sum_no_discount) . ' where entity_id = ' . __sanitize($entity_id));
    mageQuery("update sales_flat_order set base_subtotal_incl_tax = " . __sanitize($formatted_sum_no_discount) . ' where entity_id = ' . __sanitize($entity_id));
    mageQuery("update sales_flat_order set subtotal_incl_tax = " . __sanitize($formatted_sum_no_discount) . ' where entity_id = ' . __sanitize($entity_id));
    mageQuery("update sales_flat_order set base_grand_total = " . __sanitize($formatted_sum_shipping) . ' where entity_id = ' . __sanitize($entity_id));
    mageQuery("update sales_flat_order set grand_total = " . __sanitize($formatted_sum_shipping) . ' where entity_id = ' . __sanitize($entity_id));

    $quote = mageGetRows('select * from sales_flat_quote where reserved_order_id = ' . __sanitize($order_id));
    if(count($quote) > 0) {
      $quote_id = $quote[0]['entity_id'];

      mageQuery("update sales_flat_quote set items_qty = " . __sanitize($formatted_total_qty) . ' where entity_id = ' . __sanitize($quote_id));

      mageQuery("update sales_flat_quote set subtotal = " . __sanitize($formatted_sum_no_discount) . ' where entity_id = ' . __sanitize($quote_id));
      mageQuery("update sales_flat_quote set base_subtotal = " . __sanitize($formatted_sum_no_discount) . ' where entity_id = ' . __sanitize($quote_id));
      mageQuery("update sales_flat_quote set subtotal_with_discount = " . __sanitize($formatted_sum_no_shipping) . ' where entity_id = ' . __sanitize($quote_id));
      mageQuery("update sales_flat_quote set base_subtotal_with_discount = " . __sanitize($formatted_sum_no_shipping) . ' where entity_id = ' . __sanitize($quote_id));
      mageQuery("update sales_flat_quote set grand_total = " . __sanitize($formatted_sum_shipping) . ' where entity_id = ' . __sanitize($quote_id));
      mageQuery("update sales_flat_quote set base_grand_total = " . __sanitize($formatted_sum_shipping) . ' where entity_id = ' . __sanitize($quote_id));

      mageQuery("update sales_flat_quote_address set subtotal = " . __sanitize($formatted_sum_no_discount) . ' where quote_id = ' . __sanitize($quote_id));
      mageQuery("update sales_flat_quote_address set base_subtotal = " . __sanitize($formatted_sum_no_discount) . ' where quote_id = ' . __sanitize($quote_id));
      mageQuery("update sales_flat_quote_address set subtotal_incl_tax = " . __sanitize($formatted_sum_no_discount) . ' where quote_id = ' . __sanitize($quote_id));
      mageQuery("update sales_flat_quote_address set grand_total = " . __sanitize($formatted_sum_shipping) . ' where quote_id = ' . __sanitize($quote_id));
      mageQuery("update sales_flat_quote_address set base_grand_total = " . __sanitize($formatted_sum_shipping) . ' where quote_id = ' . __sanitize($quote_id));
    }

    mageQuery("update sales_flat_order_grid set grand_total = " . __sanitize($formatted_sum_shipping) . ' where entity_id = ' . __sanitize($entity_id));
    mageQuery("update sales_flat_order_grid set base_grand_total = " . __sanitize($formatted_sum_shipping) . ' where entity_id = ' . __sanitize($entity_id));


    mageQuery("update sales_flat_order_payment set base_amount_ordered = " . __sanitize($formatted_sum_shipping) . ' where parent_id = ' . __sanitize($entity_id));
    mageQuery("update sales_flat_order_payment set amount_ordered = " . __sanitize($formatted_sum_shipping) . ' where parent_id = ' . __sanitize($entity_id));
    mageQuery("update sales_flat_order_payment set base_amount_authorized = " . __sanitize($formatted_sum_shipping) . ' where parent_id = ' . __sanitize($entity_id));
    mageQuery("update sales_flat_order_payment set amount_authorized = " . __sanitize($formatted_sum_shipping) . ' where parent_id = ' . __sanitize($entity_id));
  }
}

function getOriginalSumForOrder($order_id) {
  $order = mageGetRows("select * from sales_flat_order where increment_id = " . __sanitize($order_id));
  if(count($order) > 0) {
    $order = $order[0];
    $sum = floatval($order['subtotal_incl_tax']);
    $discount = abs(floatval($order['base_discount_amount']));

    return $sum - $discount;
  }

  return null;
}

function getPercentageFromOriginal($new, $original) {
  $original = floatval($original);
  $new = floatval($new);

  if($original == 0) {
    return 'infinity';
  }

  $diff = abs($new - $original);
  $result = (floatval($diff) / $original) * 100;

  return sprintf("%.1f", $result);
}

// Processing funcs --------------------------------------->

function processOrder($order_id, $subtotal = null, $shipping = null, $can_change = false) {
  global $__payment_errors;
  global $__payment_notifications;
  global $__payment_error_header;

  $update_success = false;

  $order_number = $order_id;

  $price_error = false;

  $the_original_amount = getOriginalSumForOrder($order_id);
  $the_shipping_amount = getShippingCostByOrderId($order_id);
  $the_original_total = $the_original_amount + $the_shipping_amount;
  $the_original_captured = getCapturedAmountByOrderId($order_id);
  $total_sum = $the_original_total - $the_original_captured;
  $shipping = 0;

  $__payment_notifications []= "Debug info: (order_number: " . strval($order_number) . "; trans_id: " . strval($trans_id) . "; total_sum: " . $total_sum . "; price_error: " . $price_error . "; shipping: " . $shipping . ")";

  if(!empty($order_number) && ($total_sum > 0) && (!$price_error)) {
    $sum_without_shipping = $total_sum;

    $total_sum += $shipping;

    $formatted_sum = sprintf("%.4f", $total_sum);
  
    $__payment_notifications []= (sprintf("For (order: %s, transaction: %s) will void and charge: %s (of which shipping is %s); tracking nums: %s", $order_number, $trans_id, $formatted_sum, $shipping, implode(',', $order['tracking_ids'])));

    $original_sum = getOriginalSumForOrder($order_number);

    if(AuthnetCIMHelper::haveCimDataForOrder(getRealOrderId($order_number))) {
      if($can_change) {
        $void_result = AuthnetCIMHelper::voidTransaction($trans_id);
        if(!empty($void_result)) {
          $__payment_notifications []= sprintf("Transaction (%s) voiding successful.", $trans_id);
  
          $capture_result = AuthnetCIMHelper::authAndCapture(getRealOrderId($order_number), $formatted_sum);
          if(!empty($capture_result)) {
            $update_success = true;
            $__payment_notifications []= "New capture successful.";
          } else {
            $error_msg = sprintf("Unable to capture %s for order %s.", $formatted_sum, $order_number);
            $__payment_notifications []= $__payment_error_header . $error_msg;
            $__payment_errors []= ($file_name . ': ' . $error_msg);
          }
        } else {
          $error_msg = sprintf("Could not void transaction %s", $trans_id);
          $__payment_notifications []= $__payment_error_header . $error_msg;
          $__payment_errors []= ($file_name . ': ' . $error_msg);
        }
      }
    } else {
      $error_msg = sprintf("No cim data for trans_id: %s and order_id: %s", $trans_id, $order_number);
      $__payment_notifications []= $__payment_error_header . $error_msg;
      $__payment_errors []= ($file_name . ': ' . $error_msg);
    }
  } else {
    $error_msg = sprintf("Failure because of missing DB data!!!!!");
    $__payment_notifications []= $__payment_error_header . $error_msg;
    $__payment_errors []= ($file_name . ': ' . $error_msg);
  }

  if($update_success && $can_change) {
    //mageSendNewOrderEmail($order_number, implode(', ', $new_tracking_ids));
  }
}

?>
