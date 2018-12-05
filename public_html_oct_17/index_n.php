<?php
/*
if((!isset($_SERVER['HTTPS'])) || ($_SERVER['HTTPS'] != "on"))
{
	$url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	header("Location: $url");
	exit;
}
*/
session_start();
if(isset($_GET['action']) && ($_GET['action'] == "logout"))
{
	if(isset($_SESSION['lfdata']))
		unset($_SESSION['lfdata']);
	if(isset($_SESSION['lcdata']))
		unset($_SESSION['lcdata']);
		session_destroy();
}
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

<!-- 
https://developers.google.com/maps/documentation/javascript/places
https://developers.google.com/maps/documentation/javascript/get-api-key
https://developers.google.com/maps/api-key-best-practices
https://cloud.google.com/maps-platform/user-guide/account-changes/
https://developers.google.com/maps/documentation/javascript/usage-and-billing

https://www.w3schools.com/howto/howto_js_slideshow.asp
https://www.w3schools.com/howto/howto_js_popup.asp
https://www.w3schools.com/howto/howto_css_modals.asp
https://developer.mozilla.org/en-US/docs/Web/API/Element/innerHTML
https://www.w3schools.com/bootstrap/bootstrap_carousel.asp
https://getbootstrap.com/docs/3.3/javascript/
https://www.jqueryscript.net/lightbox/Lightbox-Carousel-Plugin-jQuery-slideBox.html
https://www.w3schools.com/cssref/pr_class_position.asp

-->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&libraries=places"
        async defer></script>

<!-- <script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&language=gb"></script> -->
</head>
<body>
<!-- Banner-->
<div class="w3_banner" style="background:none;">
<nav class="navbar navbar-default">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <h1 style="color:#68AE00;">LEAZZER</h1>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    	<ul class="nav navbar-nav navbar-right">
		<li><a href="facility/register.php">Add Facility</a></li>
		<li><a href="facility/dashboard.php"><?php echo (isset($_SESSION['lfdata'])?$_SESSION['lfdata']['firstname']:"Owner Login");?></a></li>
    <li><a href="customer/dashboard.php"><?php echo (isset($_SESSION['lcdata'])?$_SESSION['lcdata']['firstname']:"Login");?></a></li>
    <?php 
    if(isset($_SESSION['lcdata']) || isset($_SESSION['lfdata']))
    	echo '<li><a href="index_n.php?action=logout">Logout</a></li>';
    ?>
        </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container -->
</nav>
<div class="w3_bannerinfo">
<img src="images/llogo.png" height=120px>
<h2 style="margin:0;">Reserve Selfstorage For Free</h2>
<!-- Search-->
  <div class="w3l_banser" style="margin-top: 20px;">
	  <div id="search_form" class="search_top">
		<form action="search_n.php" method="post">

	  	<div id="pac-card">

			  <input class="text" name="search" id="searchTextField" type="text" placeholder="Zip or City" required="">
				<input type="submit" value="Find Storage" style="margin-left: 10px;">
				  <div class="clearfix"></div>
	  			<br><a href="helpmechoose.php" style="color:#000;text-shadow: 1px 1px 1px #555;">Help me choose units</a><br><br>
	  			<p>Search And Compare Selfstorage Near You</p>
				  <div class="clearfix"></div>
			</div>
		  </form>
	  </div>
  </div>
<!--/Search-->
  </div>
</div>
<!-- /Banner-->
<div class="inner-block" style="padding:2em;">
	<center><h3 style="padding-bottom:10px;">Nearest Self Storage</h3></center>
    <div class="blank" style="min-height: 0;">
    	<div class="blankpage-main" id="searchmain"style="padding:1em 1em;">
		</div>
	</div>
</div>
<div class="copyright">
	<div class="container">
		<p>© <?php echo date("Y",time());?> Leazzer. All rights reserved | <a href="/global_footer.php">Privacy Policy</a> | <a href="/global_footer_tu.php">Terms of use</a>
    </p>
	</div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.easing.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="admin/css/datepicker.css" />
<script type="text/javascript" src="admin/js/bootstrap-datepicker.js"></script>
<script>
$(document).ready(function(){
	if (navigator.geolocation){
  	navigator.geolocation.getCurrentPosition(showPosition,showError);
  } 
	else 
		nearStorage(0,0);
});

function showPosition(position){
	nearStorage(position.coords.latitude,position.coords.longitude);
}

function showError(error){
	nearStorage(0,0);
}

function nearStorage(lat,lng){
	var res = ajaxcall("action=nearlocation&lat="+lat+"&lng="+lng);
	$('#searchmain').html(res);
	$('.datepicker').datepicker({
     	format: 'mm/dd/yyyy',
     	startDate: new Date(),
		autoclose:true
    });
}

function onShowUnit(id){
	if($('#unitstbl_'+id).is(':hidden')){
		$('#unitstbl_'+id).show();
		$("#dateday_"+id).css("display", "inline");	
		$('#mdate_'+id).datepicker({
     	format: 'mm/dd/yyyy',
     	startDate: new Date(),
		autoclose:true
    });
    
	}
	else{
		$('#unitstbl_'+id).hide();
		$('#dateday_'+id).hide();	
	}
}

function validatePhone(phone){
  if(phone == undefined || phone == null || phone.length < 10)
    return false;
  return true;
}

function onUnitClick(btn, cid, fid, rdays, unit, price, hasPhone){
  var phone = 'unknown';
  if(validatePhone(hasPhone) == true)
    phone = hasPhone;
	if($('#mdate_'+fid).val() == ""){
		$('#mdatemsg_'+fid).show();
	}
	else if(cid == 0){
			var res = ajaxcall("action=sessionreserve&fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price+
									"&phone="+phone);
			if(res !== false){
				window.location.href='customer/index_n.php?action=search';
			}
	}
	else if(validatePhone(phone) == false){
	  $('#mdatemsg_'+fid).hide();
			window.location.href = "askphone.php?ref=index&fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price+
									"&phone="+phone;
	}
	else{
			$('#mdatemsg_'+fid).hide();
			var res = ajaxcall("action=reserve&fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price+
									"&phone="+phone);
			//if(res == "success")
			{
				btn.innerHTML = "<i class=\"fa fa-check\"></i>";
				window.location.href = "thankyou_n.php?fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price+
									"&phone="+phone;
			}
	}
}

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
   				 	res = result;
   		 	},
   		 	error: function(err){
   		 	    alert('Failed to invoke serverside function... Please try again in some time' + err);
   		 	    res = false;
   		 	}
    });
    return res;
}

function ajaxcall_ap(datastring){
    var res;
    $.ajax
    ({	
    		type:"GET",
    		url:"askphone.php",
    		data:datastring,
    		cache:false,
    		async:false,
    		success: function(result){		
   				 	res = result;
   		 	},
   		 	error: function(err){
   		 	    alert('Failed to invoke serverside function( to ask phone)... Please try again in some time' + err);
   		 	    res = false;
   		 	}
    });
    return res;
}

</script>

<script>
var input = document.getElementById('searchTextField');
var autocomplete = new google.maps.places.Autocomplete(input);
</script>

<!-- <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script> 
leazzer2018 - AIzaSyDP-JrlGW5feBEicUQUa8xhrHxRygBvsfE

facilities dashboard - AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA

existing - AIzaSyCvc_18HEgG7qB9nWPE9KlxVOFW0r4RJPM

-->

<!-- Start of HubSpot Embed Code -->
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/5051579.js"></script>
<!-- End of HubSpot Embed Code -->

</body>
</html>

