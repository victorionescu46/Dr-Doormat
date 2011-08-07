<?php
  
  if(isset($_POST['send-message'])) {
      extract($_POST);
      $email = 'info@drdoormat.com';
  		$emailTo = 'info@drdoormat.com';
  		$body = "First Name: $first_name \n\nLast Name: $last_name \n\nTitle: $title \n\nCompany: $company \n\nPhone: $phone \n\nEmail: $email \n\Message:\n $message \n\n$news_letter";
  		$headers = 'From: drdoormat.com <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;

  		mail($emailTo, $body, $headers);
  }
  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	   <meta http-equiv="Content-type" content="text/html; charset=utf-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

     <title>Dr Doormat Delivery</title>
     <meta name="description" content="">
     <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
     <link rel="shortcut icon" href="/favicon.ico">
 
     <link rel="stylesheet" href="css/master.css" />
     <!--[if IE 6]>
    <script src="js/dd_belatedpng.js" type="text/javascript"></script>
    <script>
      /* EXAMPLE */
      DD_belatedPNG.fix('h1#identity a, ul#menu'); 
    </script>
    <![endif]--> 
      <script src="js/cufon-yui.js" type="text/javascript"></script>
       <script src="js/Futura_LT_400-Futura_LT_700.font.js"></script>
       <script type="text/javascript">
       		Cufon.replace('h2, h3'); // Works without a selector engine
       	</script>
       	<style type="text/css" media="screen">
       	  #ok { display: none; padding:10px;margin-bottom: 10px;}
       	  #ok p {color: green !important;}
          
       	</style>
       	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>   
       	<script src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js">	</script>
        <script type="text/javascript">
        		SubmittingForm=function() {
        			$('#ok').show();
              $('#contact-form').submit();
        		}

        		$(document).ready(function() {
        			$("#contact").validate({
        				submitHandler:function(form) {
        					SubmittingForm();
        				},
        				rules: {
        					name: "required",	
        					email: {				
        						required: true,
        						email: true
        					},
        					url: {
        						url: true
        					},
        					comment: {
        						required: true
        					}
        				},
        				message: {
                  required: true,
        					comment: "Please enter a comment."
        				}
        			});
        		});

        		jQuery.validator.addMethod(
        			"selectNone",
        			function(value, element) {
        				if (element.value == "none")
        				{
        					return false;
        				}
        				else return true;
        			},
        			"Please select an option."
        		);

        		$(document).ready(function() {
        			$("#fvujq-form2").validate({
        				submitHandler:function(form) {
        					SubmittingForm();
        				},
        				rules: {
        					sport: {
        						selectNone: true
        					}
        				}
        			});
        		});
        	</script>
</head>

<!--[if lt IE 7 ]> <body class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <body class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <body class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <body class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <body> <!--<![endif]-->

  <div id="container">
    <div id="header">
       <div id="header-outer">
         <div class="inner">
           <h1 id="identity"><a href="index.html">Dr Doormat</a></h1>
           <div id="navigation">
              <ul id="menu">
                 <li><a href="http://beta.drdoormat.com/about.html">about</a></li>
                  <li><a href="http://beta.drdoormat.com/story.html">our story</a></li>
                  <li><a href="http://beta.drdoormat.com/choose-your-mat.html">Choose your mat</a></li>
                  <li><a href="http://beta.drdoormat.com/how-it-works.html">How it works</a></li>
                  <li><a href="http://beta.drdoormat.com/eco-friendly.html">Eco-friendly</a></li>
                  <li><a href="http://beta.drdoormat.com/testimonials.html">testimonials</a></li>
                  <li><a href="http://beta.drdoormat.com/tips.html">home tips</a></li>
                  <li><a href="http://beta.drdoormat.com/faq.html">faq</a></li>
                  <li><a href="http://beta.drdoormat.com/press.html">press</a></li>
                  <li><a href="http://beta.drdoormat.com/contact.html">contact</a></li>
               </ul><!-- /#menu-->

             <ul id="share-links">
				<li>SIGN UP FOR EMAIL UPDATES</li>
				<li id="mail-ico"><a href='http://visitor.r20.constantcontact.com/d.jsp?llr=w6uwdydab&p=oi&m=1103542047670'></a></li>
               	<li>SHARE</li>
               	<li id="fb-ico"><a href="http://www.facebook.com/sharer.php?u=http://beta.drdoormat.com/index.html"></a></li>
               	<li id="tw-ico"><a href="http://twitter.com/home?status=Check this out http://beta.drdoormat.com/index.html"></a></li>
               	<!-- <li id="mail-ico"><a href="#"></a></li> -->
             </ul><!-- /#share-links-->
             <div class="clear"></div>

             <a href="choose-your-mat.html" id="header-buy-btn"></a>
             <ul id="order-menu">
               <li id="order-status"><a href="http://beta.drdoormat.com/customer/account/login/">Order Status</a></li>
               <li id="my-account"><a href="http://beta.drdoormat.com/customer/account">My Account</a></li>
               <li id="view-cart"><a href="http://beta.drdoormat.com/checkout/cart">View Cart</a></li>
             </ul><!-- /order-menu-->
           </div><!-- /#navigation-->
         </div>
       </div>
     </div><!-- /#header-->
  
    <div id="main" >
      <div class="heading">
        <div class="inner">
          <h2>contact us</h2>
        </div><!-- /inner-->
      </div><!--/heading-->
      <div class="inner story" >
        <div id="ok">
          <p>Your message has been sent. Thank you!</p>
        </div>
        <div id="contact-form">
          <h3>contact us by e-mail</h3>
          <p><b>Please use the contact form below for any questions or comments you might have.</b></p>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="contact" method="post" accept-charset="utf-8">
          	<label for="first_name">First Name</label><br />
          	<input type="text" id="first_name" name="first_name" class="txt" style="width: 400px" />
          	<br />
            <label for="last_name">Last Name</label><br />
            <input type="text" id="last_name" name="last_name" class="txt" style="width: 400px;"/>
            <br />
            <label for="title">Title</label><br />
            <input type="text" name="title" id="title" class="txt" style="width: 400px;"/>
            <br />
            <label for="company">Company</label><br /><input type="text" name="company" value="" id="company" class="txt" style="width: 400px;"/>
            <br />
            <label for="phone">Phone</label><br /><input type="text" name="phone" value="" id="phone" class="txt" style="width: 400px;"/>
            <br />
            <label for="e-mail">E-mail</label><br /><input type="text" name="e-mail" value="" id="e-mail"class="txt" style="width: 400px;" />
            <br />
            <label for="message">Message</label><br />
            <textarea name="message" rows="8" cols="40" id="message" style="width: 400px;"></textarea><br />
            <input type="checkbox" name="news_letter" id="news_letter" style="margin-bottom: 5px;" value="Wants Newsletter" checked="checked" /><label for="news_letter"> YES, I would like to receive update information and announcements from Dr. Doormat</label><br /><br />
            <input type="submit" name="send-message" value="" id="send-message"/>
          </form>     
        </div><!-- /contact-form-->
        
        <div id="contact-address">
          <h3>dr. doormat, inc.</h3>
          <p class="clearfix"><span><b>Mail:</b></span>
            <span>Dr. Doormat, Inc <br />  
            11140 Rockville Pike<br />
            Suite 100, #233<br />
            Rockville, MD 20852-3148</span>
          </p><br />
          <p class="clearfix"><span><b>Phone:</b></span>
          <span>301-770-2002</span>  
          </p><br /> 
          
          <p class="clearfix"><span><b>E-mail:</b></span>
            <span>info@drdoormat.com</span></p>
        </div><!-- /contact-address-->
        <div class="clear"></div>
      </div><!--/.inner-->
    </div><!-- /#main-->   
    
    <div id="footer">
      <div class="inner">
        <div class="right">
        <ul id="footer-links">
           <li><a href="index.html">home</a></li>
           <li><a href="about.html">about</a></li>
           <li><a href="story.html">our story</a></li>
           <li><a href="eco-friendly.html">eco-friendly</a></li>
           <li><a href="testimonials.html">testimonials</a></li>
           <li><a href="tips.html">healthy home tips</a></li>
           <li><a href="faq.html">faq</a></li>
           <li><a href="press.html">press</a></li>
           <li><a href="contact.html">contact</a></li>
        </ul><!-- /footer-links-->
        
        <ul id="second-footer-links">
          <li><a href="shipping.html">Shipping &amp; Return Info</a></li>
          <li><a href="security.html">Security &amp; Privacy Policy</a></li>
          <li><a href="terms.html">Terms &amp; Conditions</a></li>
        </ul><!--/second-footer-links-->
        </div>
        <p>Copyright &copy; 2011 Dr. Doormat - All Rights Reserved</p>
      </div><!-- /inner-->
    </div><!-- /#footer-->
  </div> <!--! end of #container -->

  <!--[if lt IE 7 ]>
    <script src="js/dd_belatedpng.js?v=1"></script>
  <![endif]-->

</body>
</html>
