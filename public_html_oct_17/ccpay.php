<?php


?>
<!DOCTYPE html>
<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<meta name="keywords" content="Leazzer" />
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all">
<link href="css/owl.carousel.css" rel="stylesheet">
<link rel="stylesheet" href="css/jquery-ui.css" />
<link rel="stylesheet" href="css/chocolat.css" type="text/css">
<link href="facility/css/style.css" rel="stylesheet" type="text/css" media="all"/>
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<link href="fonts/fonts.css" rel="stylesheet">

</head>
<body>
<div class="w3_banner" style="background:none;">
<nav class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="index.php"><h1 style="color:#68AE00;">LEAZZER</h1></a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    	<ul class="nav navbar-nav navbar-right">
    <?php 
    if(isset($_SESSION['lcdata']) || isset($_SESSION['lfdata']))
    	echo '<li><a href="index.php?action=logout">Logout</a></li>';
    ?>
      </ul>
    </div>
  </div>
</nav>
<div class="w3_bannerinfo">
<a href="index.php"><img src="images/llogo.png" height=120px></a>
<h2 style="margin-left:100px;text-align:left">Payment Details Collection</h2><br />
    <?php
      if(isset($_POST['stripeToken'])){
        echo '<center><u>Successfully charged $9.99 and received Stripe Token: '.$_POST['stripeToken'].' <br /> reference user emailid: '.$_POST['stripeEmail'].'</u></center>';
      }
    ?>
    <br /><br />
    <form action="ccpay.php" method="POST">
    <script
      src="https://checkout.stripe.com/checkout.js" class="stripe-button"
      data-key="pk_test_TYooMQauvdEDq54NiTphI7jx"
      data-amount="999"
      data-name="Leazzer.com"
      data-description="Referral charge"
      data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
      data-locale="auto"
      data-zip-code="true">
    </script>
</form>
</div>

<div class="copyright" style="position:absolute;bottom:0">
	<div class="container">
		<p>© <?php echo date("Y",time());?> Leazzer. All rights reserved | <a href="/global_footer.php">Privacy Policy</a> | <a href="/global_footer_tu.php">Terms of use</a>
    </p>
	</div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.easing.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="admin/css/datepicker.css" />

</body>
</html>

