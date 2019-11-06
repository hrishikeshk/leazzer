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

<script src="https://maps.googleapis.com/maps/api/js?key=&libraries=places"
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
    	echo '<li><a href="index.php?action=logout">Logout</a></li>';
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
		<form action="search.php" method="post">

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
$(document).ready(function()
{
	if (navigator.geolocation) 
	{
  	navigator.geolocation.getCurrentPosition(showPosition,showError);
  } 
	else 
		nearStorage(0,0);
});
function showPosition(position) 
{
	nearStorage(position.coords.latitude,position.coords.longitude);
}
function showError(error) 
{
	nearStorage(0,0);
}
function nearStorage(lat,lng)
{
	var res = ajaxcall("action=nearlocation&lat="+lat+"&lng="+lng);
	$('#searchmain').html(res);
	$('.datepicker').datepicker({
     	format: 'mm/dd/yyyy',
     	startDate: new Date(),
		autoclose:true
    });
}

function onShowUnit(id)
{
	if($('#unitstbl_'+id).is(':hidden'))
	{
		$('#unitstbl_'+id).show();
		$("#dateday_"+id).css("display", "inline");	
		$('#mdate_'+id).datepicker({
     	format: 'mm/dd/yyyy',
     	startDate: new Date(),
		autoclose:true
    });
    
	}
	else
	{
		$('#unitstbl_'+id).hide();
		$('#dateday_'+id).hide();	
	}
}
function onUnitClick(btn,cid,fid,rdays,unit,price)
{
	if($('#mdate_'+fid).val() == "")
	{
		$('#mdatemsg_'+fid).show();
	}
	else if(cid == 0)
	{
			var res = ajaxcall("action=sessionreserve&fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price);
			if(res == "success")
			{
				window.location.href='customer/index.php?action=search';
			}
	}
	else
	{		
			$('#mdatemsg_'+fid).hide();
			var res = ajaxcall("action=reserve&fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price);
			//if(res == "success")
			{
				btn.innerHTML = "<i class=\"fa fa-check\"></i>";
				window.location.href = "thankyou.php?fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price;
			}
	}
}
function ajaxcall(datastring)
{
    var res;
    $.ajax
    ({	
    		type:"POST",
    		url:"service.php",
    		data:datastring,
    		cache:false,
    		async:false,
    		success: function(result)
    	 	{		
   				 	res=result;
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

facilities dashboard - 


-->

</body>
</html>

