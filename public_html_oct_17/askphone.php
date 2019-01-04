<?php
if((!isset($_SERVER['HTTPS'])) || ($_SERVER['HTTPS'] != "on")){
	$url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	header("Location: $url");
	exit;
}

session_start();
include('sql.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Leazzer" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="facility/css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<link href="facility/css/style.css" rel="stylesheet" type="text/css" media="all"/>
<script src="facility/js/jquery-2.1.1.min.js"></script> 
<link href="facility/css/font-awesome.css" rel="stylesheet"> 
<link href='facility/fonts/fonts.css' rel='stylesheet' type='text/css'>
<script src="facility/js/Chart.min.js"></script>
<!--skycons-icons-->
<script src="facility/js/skycons.js"></script>
<link href="facility/css/demo-page.css" rel="stylesheet" media="all">
<link href="facility/css/hover.css" rel="stylesheet" media="all">
<!--//skycons-icons-->
</head>
<body>	
<div class="page-container">	
   <div class="left-content">
	   <div class="mother-grid-inner">
            <!--header start here-->
				<div class="header-main">
					<div class="header-left" style="width:100%;">
							<div class="logo-name login-block"  style="width:100%;padding:0;margin:0;">
								<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
								<a href="index.php" style="display:inline;float:left;"><img id="logo" src="images/llogo.png" style="display:inline;width:40px;" alt="Logo"/></a>
								</form>
								<?php
								if((isset($_SERVER["HTTP_REFERER"]) && (strpos($_SERVER["HTTP_REFERER"],"search_n.php")!==false)) || 
									(isset($_GET['ref']) && ($_GET['ref']=="search")))
									echo '<a href="search_n.php" style="float:right;margin-top:12px;padding:3px;color:#68AE00;">Back to Search</a>';
								else
									echo '<a href="index.php" style="float:right;margin-top:12px;padding:3px;color:#68AE00;">Back to Home</a>';
									?>
							</div>
							<div class="clearfix"> </div>
						 </div>
				     <div class="clearfix"> </div>	
				</div>
<!--heder end here-->
<!-- script-for sticky-nav -->
		<script>
		$(document).ready(function(){
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
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<div class="blankpage-main" style="padding:1em 1em;text-align:center;">
    	<h4><center>Nearly there... </center></h4>
    	<br /><br />
    	<center>
    	
		<?php
		  if(isset($_POST['action']) && $_POST['action'] == 'Complete Reservation' && isset($_POST['phone']) && strlen($_POST['phone']) == 10){
		    //error_log(' askphone: posted: '.$_POST['phone']);
		    $_SESSION['res_phone'] = $_POST['phone'];
		    if(isset($_SESSION['lcdata']))
		      $_SESSION['lcdata']['phone'] = $_POST['phone'];
		    $reserve = "&fid=".$_SESSION['res_fid'].
							"&cid=".$_SESSION['lcdata']['id'].
							"&rdays=".$_SESSION['res_rdays'].
							"&rdate=".$_SESSION['res_rdate'].
							"&unit=".$_SESSION['res_unit'].
							"&price=".$_SESSION['res_price'].
							"&phone=".$_POST['phone'];

				//error_log(' askphone: reserve: '.$reserve);
				header("Location: thankyou_n.php?ref=".$_POST['reffer'].$reserve);
		  }
		  else{
				//$rdateArr = explode("/", $_GET['rdate']);
  			//$reserveFromDate = mktime(0, 0, 0, $rdateArr[0], $rdateArr[1], $rdateArr[2]);
	  		//$reserveToDate = strtotime("+".$_GET['rdays']." days", $reserveFromDate);
        if(isset($_GET['fid']))
          $_SESSION['res_fid'] = htmlspecialchars($_GET['fid'], ENT_QUOTES);
        if(isset($_GET['rdays']))
          $_SESSION['res_rdays'] = htmlspecialchars($_GET['rdays'], ENT_QUOTES);
        if(isset($_GET['rdate']))
          $_SESSION['res_rdate'] = htmlspecialchars($_GET['rdate'], ENT_QUOTES);
        if(isset($_GET['unit']))
          $_SESSION['res_unit'] = htmlspecialchars($_GET['unit']);
        if(isset($_GET['price']))
          $_SESSION['res_price'] = htmlspecialchars($_GET['price'], ENT_QUOTES);
        

        if(isset($_POST['action']) && $_POST['action'] == 'Complete Reservation' && isset($_POST['phone']) && strlen($_POST['phone']) != 10)
          echo '<p style="display:block;color:#BB0000;font-size:.9em;margin:0;margin-left: 10px;padding:0;text-align:center;">Please enter a valid phone number</p>';
        else
          echo '<br />';
				echo 'Enter phone number to Complete Reservation<br /><br />';
		  }

      ?>
      <form method="post" action="askphone.php" enctype="multipart/form-data">
          <input type="text" name="phone" id="phone"> <br />
          <input type="hidden" name="reffer" value="<?php echo (isset($_POST["reffer"])?$_POST["reffer"]:$_SERVER["HTTP_REFERER"]);?>">
          <br />
					<input type="submit" name="action" value="Complete Reservation" style="width:200px;height:50px; border-radius: 10px;">
				</form>
		<br />
		<?php
			if((isset($_SERVER["HTTP_REFERER"]) && (strpos($_SERVER["HTTP_REFERER"],"search.php")!==false)) || 
					(isset($_GET['ref']) && ($_GET['ref']=="search")))
				echo '<h5><a href="search.php" style="color:#68AE00;">Go Back Search</a></h5>';
			else
				echo '<h5><a href="index.php" style="color:#68AE00;">Go Back Home</a></h5>';
		?>
		  <br /><br />
			  
			</center>
		<div class="clearfix"> </div>
    	</div>
    </div>
</div>
<!--inner block end here-->
<!--copy rights start here-->
<div class="copyright" style="background-color:#000000;text-align:center;display:block;color:#fff">
		<p>© <?php echo date("Y",time());?> Leazzer. All rights reserved | <a href="/global_footer.php">Privacy Policy</a> | <a href="/global_footer_tu.php">Terms of use</a>
    </p>
</div>	
<!--COPY rights end here-->
</div>
</div>
<!--scrolling js-->
		<script src="facility/js/jquery.nicescroll.js"></script>
		<script src="facility/js/scripts.js"></script>
		<!--//scrolling js-->
<script src="facility/js/bootstrap.js"> </script>
<!-- mother grid end here-->
<script>
$(document).ready(function(){
	<?php
	?>
});

</script>
</body>
</html>                        
