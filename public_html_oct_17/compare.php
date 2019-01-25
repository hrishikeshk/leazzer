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

 	  <a href="javascript:history.go(-1)" style="display:inline;float:left;"><img id="logo" src="images/llogo.png" style="display:inline;width:50px;" alt="Logo"/>
 	  </a>
 	  <h1 style="margin-left:75px; color:#1122dd">Comparing Facilities</h1>
 	</div>
	</div>
</nav>

<div class="container">
<?php
session_start();
include('sql.php');

function get_amenities($facility_id){
  global $conn;
  
}

if(isset($_GET['facs'])){
  $fac_arr = explode('|', $_GET['facs']);
  $ct = count($fac_arr);
  $amenities = array();
  for($i = 0; $i < $ct; $i++){
    $amenities[$fac_arr[$i]] = get_amenities($fac_arr[$i]);
  }
  
}
?>

<!--scrolling js-->
		<script src="js/jquery.nicescroll.js"></script>
		<script src="js/scripts.js"></script>
<!--//scrolling js-->
<script src="js/bootstrap.js"> </script>
<br /><br />
<p style="margin-left:75px">
<a href="index.php"><img id="logo" src="images/llogo.png" style="display:inline;width:40px;" alt="Logo"/>
<a href="javascript:history.go(-1)">Back<a/>
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

