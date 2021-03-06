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
		<?php
			$rdateArr = explode("/", $_GET['rdate']);
			$reserveFromDate = mktime(0, 0, 0, $rdateArr[0], $rdateArr[1], $rdateArr[2]);
			$reserveToDate = strtotime("+".$_GET['rdays']." days", $reserveFromDate);
			$resC = mysqli_query($conn,"select * from customer where id=".mysqli_real_escape_string($conn, $_GET['cid']));
			$resF = mysqli_query($conn,"select * from facility_master where id=".mysqli_real_escape_string($conn, $_GET['fid']));
			$resI = mysqli_query($conn,"select * from image where facility_id=".mysqli_real_escape_string($conn, $_GET['fid']));
			if((mysqli_num_rows($resC) > 0) && (mysqli_num_rows($resF) > 0)){
				$arrC = mysqli_fetch_array($resC, MYSQLI_ASSOC);
				$arrF = mysqli_fetch_array($resF, MYSQLI_ASSOC);
				echo '<b>Congratulations, your unit(s) of '.htmlspecialchars($_GET['unit']).' has been reserved at '.htmlspecialchars($arrF['title'], ENT_QUOTES).' for you from '.date('m/d/Y',$reserveFromDate).
					 ' until '.date('m/d/Y',$reserveToDate).' for the price of $'.htmlspecialchars($_GET['price'], ENT_QUOTES).' at the following location<br>';
				echo '<br>';
				if(mysqli_num_rows($resI) != 0){
				  $arrI = mysqli_fetch_array($resI, MYSQLI_ASSOC);
					if(file_exists("images/".$_GET['fid']."/".$arrI['url_fullsize']))
						echo '<center><img src="//leazzer.com/images/"'.htmlspecialchars($_GET['fid'], ENT_QUOTES).'"/"'.htmlspecialchars($arrI['url_fullsize'], ENT_QUOTES).'" width="250px" height="250px"></center><br>';
					else
						echo '<center><img src="https:'.htmlspecialchars($arrI['url_fullsize'], ENT_QUOTES).'" width="250px" height="250px"></center><br>';
				}
				else
					echo '<center><img src="//leazzer.com/unitimages/pna.jpg" width="250px" height="250px"></center><br>';
				echo '<br>';				
				echo htmlspecialchars($arrF['title'], ENT_QUOTES)."<br>";
				echo htmlspecialchars($arrF['street']." ".$arrF['locality'].", ".$arrF['city']." ".$arrF['state']." - ".$arrF['zip'], ENT_QUOTES);
				if((isset($_SERVER["HTTP_REFERER"]) && (strpos($_SERVER["HTTP_REFERER"],"search.php")!==false)) || 
						(isset($_GET['ref']) && ($_GET['ref']=="search")))
					echo '<h5><a href="search_n.php" style="color:#68AE00;">Go Back Search</a></h5>';
				else
					echo '<h5><a href="index.php" style="color:#68AE00;">Go Back Home</a></h5>';
			}
		?>
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
	//error_log(' ty_n: session: '.$_SESSION['res_phone'].', posted: '.$_POST['phone']);
	if(isset($_SESSION['res_fid']) && isset($_SESSION['lcdata'])){
		echo "var res = ajaxcall(\"action=reserve&fid=".$_SESSION['res_fid']."&cid=".$_SESSION['lcdata']['id'].
						"&rdays=".$_SESSION['res_rdays']."&rdate=".$_SESSION['res_rdate'].
						"&unit=".$_SESSION['res_unit']."&phone=".$_SESSION['res_phone']."&price=".$_SESSION['res_price']."\");\n";
		unset($_SESSION['res_fid']);
		unset($_SESSION['res_cid']);
		unset($_SESSION['res_rdays']);
		unset($_SESSION['res_rdate']);
		unset($_SESSION['res_unit']);
		unset($_SESSION['res_price']);
		unset($_SESSION['res_phone']);
	}
	?>
});

function ajaxcall(datastring){
    var res;
    $.ajax
    ({	
    		type:"POST",
    		url:"service_n.php",
    		data:datastring,
    		cache:false,
    		async:false,
    		success: function(result){		
   				 	res=result;
   		 	},
   		 	error: function(err){
   		 	    alert('Failed to invoke post login reservation serverside function... Please try again in some time');
   		 	    res = false;
   		 	}
    });
    return res;
}

</script>

</body>
</html>                        
