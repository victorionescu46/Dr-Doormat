<?php

  require_once(dirname(__FILE__) . '/includes/processing.php');



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>Payments</title>
    <link rel="stylesheet" href="master.css" type="text/css" media="screen" title="no title" charset="utf-8" />
  </head>
  <body>
<?php

  if(isset($_POST['prodrun'])) {
    initGlobalData();
    initMagento();

    if(isset($_POST['order_id'])) {
      processOrder($_POST['order_id'], null, null, true);

      $initial = true;
      if(!empty($__payment_notifications)) {
        foreach($__payment_notifications as $notification) {
          if(!$initial && (preg_match('/^Processing/i', $notification) === 1)) {
            echo '<br/><br/>';
          }

          echo $notification . "<br/>";

          $initial = false;
        }
      }
    } else {
      echo 'Incomplete data!';
    }
  }

  if(isset($_POST['testrun'])) {
    initGlobalData();
    initMagento();

    if(isset($_POST['order_id'])) {
      processOrder($_POST['order_id'], null, null, false);

      $initial = true;
      if(!empty($__payment_notifications)) {
        foreach($__payment_notifications as $notification) {
          if(!$initial && (preg_match('/^Processing/i', $notification) === 1)) {
            echo '<br/><br/>';
          }

          echo $notification . "<br/>";

          $initial = false;
        }
      }
    } else {
      echo 'Incomplete data!';
    }
  }

?>
    <!-- <h1>DEV MODE!!!! PLEASE DO NOT USE NOW!!!!</h1> -->

    <h1>BILL USING CIM DATA</h1>

    <a href="javascript:;" onclick="location.href = location.href; return false;">Refresh Page</a>
    <br />
    <br />

    <form method="POST" action="index.php">

      <input type="submit" name="testrun" value="Test Run" />
      <input type="submit" name="prodrun" value="Production Run" />

      <br/>
      <br/>
      <br/>

      <p>
      <label for="order_id">Order Id (e.g. 100000123):</label><br/>
      <input id="order_id" type="text" name="order_id" value="<?php echo @htmlentities($_POST['order_id']); ?>" />
      </p>

    </form>
    
  </body>
</html>
