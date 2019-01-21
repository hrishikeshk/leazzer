<?php
session_start(['cookie_lifetime' => 86400,
               'cookie_httponly' => true,
               'cookie_secure' => true
            ]);
if((!isset($_SERVER['HTTPS'])) || ($_SERVER['HTTPS'] != "on")){
	$url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	header("Location: $url");
	exit;
}

if(!isset($_SESSION['lfdata']))
	header("Location: index.php");

$GError = "";
$pos = strpos($_SERVER['REQUEST_URI'],"settings.php");
if($pos === false){
	if(isset($_SESSION['lfdata']) && (trim($_SESSION['lfdata']['pwd']) == "excited123!"))
		header("Location: settings.php");
}
else {
	if(isset($_SESSION['lfdata']) && (trim($_SESSION['lfdata']['pwd']) == "excited123!")){
		$GError = "Please change your password.";
	}
}

$prpos = strpos($_SERVER['REQUEST_URI'],"profile.php");
if($prpos === false){
	//if(isset($_SESSION['lfdata']) && (trim($_SESSION['lfdata']['phone']) == ""))
		//header("Location: profile.php");
}
else {
	if(isset($_SESSION['lfdata']) && (trim($_SESSION['lfdata']['phone']) == ""))
		$GError = "Please enter phone number.";
}

include('../sql.php');

$cdpos = strpos($_SERVER['REQUEST_URI'],"cdinfo.php");
if($cdpos === false && $pos === false){
  $query = "select * from owner_card where owner_id = '".$_SESSION['lfdata']['auto_id']."'";
  $res = mysqli_query($conn, $query);
  if(mysqli_num_rows($res) == 0){
    header("Location: cdinfo.php");
  }
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Leazzer</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Leazzer" />
<script type="application/x-javascript"> addEventListener("load", false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<script src="js/jquery-2.1.1.min.js"></script> 
<link href="css/font-awesome.css" rel="stylesheet"> 
<link href='fonts/fonts.css' rel='stylesheet' type='text/css'>
<script src="js/Chart.min.js"></script>
<!--skycons-icons-->
<script src="js/skycons.js"></script>
<link href="css/demo-page.css" rel="stylesheet" media="all">
<link href="css/hover.css" rel="stylesheet" media="all">
<!--//skycons-icons-->
</head>
<body>	
<div class="page-container">	
   <div class="left-content">
	   <div class="mother-grid-inner">
            <!--header start here-->
				<div class="header-main">
					<div class="header-left">
							<div class="logo-name">
									 <a href="../index.php">  <img id="logo" src="../images/llogo.png" width=40px alt="Logo"/> 
								  </a> 								
							</div>
							<div class="clearfix"> </div>
						 </div>
						 <div class="header-right">
							<!--notification menu end -->
							<div class="profile_details">		
								<ul>
									<li class="dropdown profile_details_drop">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
											<div class="profile_img">
												<span class="prfil-img">
													<i class="fa fa-navicon" style="font-size: 40px;margin-top:5px;color:#68AE00;"></i>
												</span> 
												<div class="clearfix"></div>	
											</div>	
										</a>
										<ul class="dropdown-menu drp-mnu">
											<li> <a href="dashboard.php"><i class="fa fa-th"></i>Dashboard</a></li> 
											<li> <a href="profile.php"><i class="fa fa-user"></i>Profile</a></li> 
											<li> <a href="price.php"><i class="fa fa-money"></i>Upload Prices</a></li> 
											
											<li> <a href="settings.php"><i class="fa fa-cog"></i>Settings</a></li>
											<li> <a href="cdinfo.php"><i class="fa fa-cog"></i>Payment Details</a></li> 
											<li> <a href="index.php?action=logout"><i class="fa fa-sign-out"></i>Logout</a></li>
										</ul>
									</li>
								</ul>
							</div>
							<div class="clearfix"> </div>				
						</div>
				     <div class="clearfix"> </div>	
				</div>
<!--heder end here-->
<!-- script-for sticky-nav -->
		<script>
		$(document).ready(function() {
			 var navoffeset=$(".header-main").offset().top;
			 $(window).scroll(function(){
				var scrollpos=$(window).scrollTop(); 
				if(scrollpos >=navoffeset){
					$(".header-main").addClass("fixed");
				}else{
					$(".header-main").removeClass("fixed");
				}
			 });
			 
		});
		</script>
<!-- /script-for sticky-nav -->

