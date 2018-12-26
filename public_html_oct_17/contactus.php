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
 	  </a>
 	  <h1 style="text-align:center">Contact Us</h1>
 	  <hr style="height:1px;border:none;color:#44f;background-color:#44f;"></hr>
 	</div>
	</div>
</nav>

<div class="container">
<?php
session_start();
include('sql.php');

?>
<table>
  <tr>
    <td>
<a href="index.php" style="display:inline;float:left;"><img id="logo" src="images/llogo.png" style="display:inline;width:50px;" alt="Logo"/>
    </td>
    <td style="padding-left:50px">
      <div id="cu">
  Owners email us at <a href="mailto:admin@leazzer.com">admin@leazzer.com</a> <br />
  Renters email us at <a href="mailto:contact@leazzer.com">contact@leazzer.com</a> <br />
  For General Enquiries call -> 47-Leazzer-7<br />
      </div>
    </td>
  </tr>
</table>
<!--scrolling js-->
		<script src="js/jquery.nicescroll.js"></script>
		<script src="js/scripts.js"></script>
<!--//scrolling js-->
<script src="js/bootstrap.js"> </script>

<br /><br />
<p style="margin-left:25px">
<a href="index.php">Back to home page<a/>
</p>
<br /><br />
<div class="copyright" style="">
	<div class="container">
		<p>© <?php echo date("Y",time());?> Leazzer. All rights reserved | <a href="/global_footer.php">Privacy Policy</a> | <a href="/global_footer_tu.php">Terms of use</a>
    </p>
	</div>
</div>

</body>
</html>

