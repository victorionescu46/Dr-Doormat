<?

define('STORE_ANET_PREFIX', 'doormat3_1031_');
define('NOTIFY_MAIL', 'andreic@gmail.com');

function __stripNonAlphaNum($value) {
  return preg_replace('/[^a-z0-9 _-]+/i', '', $value);
}

class AuthnetCIMHelper {

  public static function voidTransaction($trans_id) {
    try {
      require_once("AuthnetCIM.class.php");

      $cim = new AuthnetCIM();

      $cim->setParameter('transId', strval($trans_id));

      $cim->voidTransaction();
      $directResponse = $cim->getDirectResponse();
      if(strpos($directResponse, "This transaction has been approved.") !== false) {
        return true;
      }
    } catch(Exception $e) {
      // Ignore and return null afterwards
    }

    return null;
  }

  public static function haveCimDataForOrder($order_id) {
    require_once(dirname(__FILE__) . '/../../app/Mage.php');

    if (!Mage::isInstalled()) {
      echo "Application is not installed yet, please complete install wizard first.";
      exit;
    }

    $_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
    $_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);
    Mage::app('admin')->setUseSessionInUrl(false);
    Mage::getConfig()->init();

    $profiles = AuthnetCIMHelper::getProfilesForOrder($order_id);
    if(!empty($profiles)) {
      return true;
    }

    return false;
  }

  // $sum must be like 10.1231 (4 digits max)
  // Called from outside Magento
  public static function authAndCapture($order_id, $sum) {
    require_once(dirname(__FILE__) . '/../../app/Mage.php');

    if (!Mage::isInstalled()) {
      echo "Application is not installed yet, please complete install wizard first.";
      exit;
    }

    $_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
    $_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);
    Mage::app('admin')->setUseSessionInUrl(false);
    Mage::getConfig()->init();

    $profiles = AuthnetCIMHelper::getProfilesForOrder($order_id);
    if(!empty($profiles)) {
      $profile_id = $profiles['profile_id'];
      $payment_profile_id = $profiles['payment_profile_id'];

      try {
        require_once("AuthnetCIM.class.php");
      
        $cim = new AuthnetCIM();
      
        $cim->setParameter('amount', strval($sum));
        $cim->setParameter('customerProfileId', strval($profile_id));
        $cim->setParameter('customerPaymentProfileId', strval($payment_profile_id));
      
        $cim->authAndCapture();
        return $cim->isSuccess();
      } catch(Exception $e) {
        // Ignore and return null afterwards
      }
    }

    return null;
  }

  public static function getProfilesForOrder($id) {
    $w = Mage::getSingleton('core/resource')->getConnection('core_write');
    $result = $w->query('select * from customers_cim where entity_id = ' . intval($id));
    
    if(!empty($result)) {
      $row = $result->fetch();
      if(!empty($row)) {
        return $row;
      }
    }

    return null;
  }

  public static function sanitize($value) {
    $value = strval($value);
    return mysql_escape_string($value);
  }

  public static function insertToDb($id, $profile_id, $payment_profile_id) {
    $w = Mage::getSingleton('core/resource')->getConnection('core_write');
    $query = sprintf("INSERT INTO customers_cim(entity_id, profile_id, payment_profile_id) VALUES ('%s', '%s', '%s');", strval(intval($id)), AuthnetCIMHelper::sanitize(strval($profile_id)), AuthnetCIMHelper::sanitize(strval($payment_profile_id)));
    
    $result = $w->query($query);
    return true;
  }

  public static function updateToDb($id, $profile_id, $payment_profile_id) {
    $w = Mage::getSingleton('core/resource')->getConnection('core_write');
    $query = sprintf("UPDATE customers_cim set profile_id = '%s', payment_profile_id = '%s' where entity_id = '%s'", AuthnetCIMHelper::sanitize(strval($profile_id)), AuthnetCIMHelper::sanitize(strval($payment_profile_id)), strval(intval($id)));
    $result = $w->query($query);
    
    return true;
  }

  public static function createForOrder($id, $options) {
    $result = AuthnetCIMHelper::createOrderProfile($options);
    if(!empty($result)) {
      AuthnetCIMHelper::insertToDb($id, $result[0], $result[1]);

      return true;
    }

    return false;
  }

  public static function updateForOrder($id, $profile_id, $payment_profile_id, $options) {
    if(AuthnetCIMHelper::updateOrderProfile($profile_id, $payment_profile_id, $options)) {
      return true;
    }

    return false;
  }
  
  public static function magentoCreateOrUpdateForOrder($id, $payment_data, $address_data) {
    $success = false;
    
    if(!empty($id)) {
      $options_array = array();
      $options_array['custID'] = STORE_ANET_PREFIX . strval($id);

      $options_array['firstName'] = $address_data['firstname'];
      $options_array['lastName'] = $address_data['lastname'];
      $options_array['address'] = $address_data['street'];
      $options_array['city'] = $address_data['city'];
      $options_array['state'] = $address_data['region'];
      $options_array['zip'] = $address_data['postcode'];
      $options_array['country'] = $address_data['country_id'];
      $options_array['phoneNumber'] = $address_data['telephone'];
      $options_array['faxNumber'] = $address_data['fax'];
      $options_array['cardNumber'] = $payment_data['cc_number'];
      $options_array['cardCode'] = $payment_data['cc_cid'];
      $options_array['expirationDate'] = $payment_data['cc_exp_year'] . '-' . sprintf("%02d", $payment_data['cc_exp_month']);

      if(AuthnetCIMHelper::createOrUpdateForOrder($id, $options_array)) {
        $success = true;
      }
    }

    if(empty($success)) {
      // exit(0);
      return false;
    } else {
      return true;
    }
  }

  public static function createOrUpdateForOrder($id, $options) {
    $profiles = AuthnetCIMHelper::getProfilesForOrder($id);
    if(!empty($profiles)) {
      return AuthnetCIMHelper::updateForOrder($id, $profiles['profile_id'], $profiles['payment_profile_id'], $options);
    } else {
      return AuthnetCIMHelper::createForOrder($id, $options);
    }

    return null;
  }

  public static function log_msg($text) {
    $fp = fopen("/tmp/test.txt", "w");
    fwrite($fp, $text . "\n");
    fclose($fp);
  }

  public static function createOrderProfile($options) {
    try {
      require_once("AuthnetCIM.class.php");

      $cim = new AuthnetCIM();

      foreach($options as $key => $value) {
        $value = trim(strval($value));
        if(!empty($value)) {
          $cim->setParameter($key, $value);
        }
      }

      $cim->createCustomerProfile2();
      $profile_id = $cim->getProfileID();
      $payment_profile_id = $cim->getPaymentProfileIdList();

      if(!empty($profile_id) && !empty($payment_profile_id)) {
        return array($profile_id, $payment_profile_id);
      }
    } catch(Exception $e) {
      // Ignore and return null afterwards
    }

    return null;
  }

  public static function updateOrderProfile($profile_id, $payment_profile_id, $options) {
    try {
      require_once("AuthnetCIM.class.php");

      $cim = new AuthnetCIM();

      foreach($options as $key => $value) {
        $value = trim(strval($value));
        if(!empty($value)) {
          $cim->setParameter($key, $value);
        }
      }
      $cim->setParameter('customerProfileId', strval($profile_id));
      $cim->setParameter('customerPaymentProfileId', strval($payment_profile_id));

      $cim->updateCustomerPaymentProfile2();
      return $cim->isSuccess();
    } catch(Exception $e) {
      // Ignore and return null afterwards
    }

    return null;
  }

  public static function send_mail($from_email, $from_name, $to_email, $to_name, $subject, $message) {
    require_once('Mail.php');
  
    $email_regexp = '/^(([^@\s]+)@(([-a-z0-9]+\.)+[a-z]{2,}))$/i';
  
    $from_name = trim(__stripNonAlphaNum($from_name));
    $to_name = trim(__stripNonAlphaNum($to_name));
  
  
    if(preg_match($email_regexp, $from_email) === 1 && preg_match($email_regexp, $to_email) === 1) {
      if($from_name != '') {
        $from_name = $from_name . ' <' . $from_email . '>';
      } else {
        $from_name = $from_email;
      }
      if($to_name != '') {
        $to_name = $to_name . ' <' . $to_email . '>';
      } else {
        $to_name = $to_email;
      }
  
      $recipients = $to_email;
      $headers = array();
      $headers["From"] = $from_name;
      $headers["To"] = $to_name;
      $headers["Subject"] = $subject;
      $smtpinfo = array();
      $smtpinfo["host"] = "ssl://smtp.gmail.com";
      $smtpinfo["port"] = "465";
      $smtpinfo["auth"] = true;
      //$smtpinfo["debug"] = true;
      $smtpinfo["username"] = "info@thekosherexpress.com";
      $smtpinfo["password"] = "brandknew";
      $mail_object =& Mail::factory("smtp", $smtpinfo);
      if($mail_object->send($recipients, $headers, $message) === true) {
        return true;
      }
    }
  
    return false;
  }

  public static function notif_mail($msg) {
    AuthnetCIMHelper::send_mail('info@thekosherexpress.com', 'Kosher Express Notification', NOTIFY_MAIL, '', 'Problem Notification', $msg);
  }

  public static function getCustomerProfileIds() {
    try {
      require_once("AuthnetCIM.class.php");
      $cim = new AuthnetCIM();
      $cim->getCustomerProfileIds();

      return $cim->getApiResponse();
    } catch(Exception $e) {
      // Ignore and return null afterwards
    }

    return null;
  }

  public static function getCustomerProfile($id) {
    try {
      require_once("AuthnetCIM.class.php");
      $cim = new AuthnetCIM();
      $cim->setParameter('customerProfileId', strval($id));
      $cim->getCustomerProfile();

      return $cim->getApiResponse();
    } catch(Exception $e) {
      // Ignore and return null afterwards
    }

    return null;
  }

}
