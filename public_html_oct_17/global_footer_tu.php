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
<nav class="navbar navbar-default">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">

 	  <a href="index.php" style="display:inline;float:left;"><img id="logo" src="images/llogo.png" style="display:inline;width:40px;" alt="Logo"/>
 	  </a>
 	  <h1 style="float: right">Terms of use</h1>
 	</div>
	</div>
</nav>

<div class="container">
<?php
session_start();
include('sql.php');

function fetch_footer(){
	global $conn;
	$ret = "";
			
	$res = mysqli_query($conn,"select * from admin_configuration where name='termsuse'");
	if(mysqli_num_rows($res) > 0){
		$arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
		$ret .= $arr['data_value'];	
	}
	else{
		$ret = "TODO: Add terms of use...";
	}
	return $ret;
}

echo fetch_footer();
?>

</div>

<!--scrolling js-->
		<script src="js/jquery.nicescroll.js"></script>
		<script src="js/scripts.js"></script>
<!--//scrolling js-->
<script src="js/bootstrap.js"> </script>

<p>
<a href="index.php"><img id="logo" src="images/llogo.png" style="display:inline;width:40px;" alt="Logo"/>
<a href="index.php">Back to home page<a/>
</p>

</body>
</html>

