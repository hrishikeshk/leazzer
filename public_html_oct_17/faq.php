<?php
session_start();
?>
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
<nav class="navbar navbar-default">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">

 	  <a href="index.php" style="display:inline;float:left;"><img id="logo" src="images/llogo.png" style="display:inline;width:50px;" alt="Logo"/>
 	  </a>
 	  <h1 style="margin-left:75px; color:#1122dd">FAQ</h1>
 	</div>
	</div>
</nav>

<div class="container">
<?php
include('sql.php');

?>
  <div id="outer_faq_div">
    <div id="inner_faq_div_renter">
      <h4 style="text-align:left; color:#2244dd">
  I am a Renter
      </h4>
      <div style="background-color:#deffde">
1. How do I cancel my reservation ?
<br />
Ans. There is no need to cancel a reservation. You can simply let it expire. To make a new reservation, simply do it using website or Leazzer app.
      </div>
<br />
      <div style="background-color:#eeffee">
2. How do I modify my reservation ?
<br />
Ans. There is no need to modify a reservation. You can simply let it expire. To make a new reservation, simply do it using website or Leazzer app.
    </div>
  </div>
    <br />
  <div id="inner_faq_div_owner">
      <h4 style="text-align:left; color:#2244dd">
  I am an Owner
      </h4>
      <div style="background-color:#deffde">
1. How do I advertize my discounts and/or specials ?
<br />
Ans. Leazzer helps only with reservations. There is no cash transaction done. You can advertize your discounts and/or specials by adding them on the Owner’s dashboard.
      </div>
<br />
      <div style="background-color:#eeffee">
2. I don’t want to advertise my facility on Leazzer
<br />
Ans. Make your facility unsearchable by logging in as Owner.
      </div>
<br />
      <div style="background-color:#deffde">
3. Does Leazzer accept credit cards ?
<br />
Ans. We provide free reservations to consumers. No credit cards are accepted.
      </div>
    </div>
  </div>
</div>

<!--scrolling js-->
		<script src="js/jquery.nicescroll.js"></script>
		<script src="js/scripts.js"></script>
<!--//scrolling js-->
<script src="js/bootstrap.js"> </script>
<br /><br />
<p style="margin-left:75px">
<a href="index.php"><img id="logo" src="images/llogo.png" style="display:inline;width:40px;" alt="Logo"/>
<a href="index.php">Back to home page<a/>
</p>
<br /><br />
<div class="copyright">
	<div class="container">
		<p>© <?php echo date("Y",time());?> Leazzer. All rights reserved | <a href="/global_footer.php">Privacy Policy</a> | <a href="/global_footer_tu.php">Terms of use</a>
    </p>
	</div>
</div>

</body>
</html>

