<?php
session_start();
include('sql.php');
$GError = "";
$filter = "";
if(isset($_GET['q']))
{
$q=$_GET['q'];
$res_fac = mysqli_query($conn,"select * from facility_images where facility_id='$q'");
}
if((!isset($_POST['search'])) && isset($_SESSION['search']))
{
	$_POST['search']= $_SESSION['search'];
}
else if(isset($_POST['search']))
{
	$_SESSION['search']= $_POST['search'];
}
if(isset($_GET['action']))
{
	if($_GET['action'] == "removefilter" && isset($_SESSION['filter']))
	{		
		$newFilterArr = array();			
 		for($i=0;$i<count($_SESSION['filter']);$i++)
 		{
 			$filterArr = explode("[-]",$_SESSION['filter'][$i]);
 			if($filterArr[0] != $_GET['id'])
 				array_push($newFilterArr,$_SESSION['filter'][$i]);
		}			
		$_SESSION['filter'] = $newFilterArr;
	}
}
if(isset($_POST['action']))
{
	if($_POST['action'] == "applyfilter")
	{
		$_SESSION['filter'] = $_POST['options'];
	}
}
function showUnit($arr)
{
	global $conn;
	$unitIds="";
	$unitPrice="";
	
	$unitArr = explode(",",$arr['units']);
	for($i=0;$i<count($unitArr);$i++)
	{
		if(trim($unitArr[$i]) == "")
		continue;
		
		$unitSubArr = explode("-",$unitArr[$i]);
		$unitIds .= $unitSubArr[0].",";
		$unitPrice.= $unitSubArr[1].",";
	}
	$unitIds = substr($unitIds,0,strlen($unitIds)-1);
	$unitPrice = substr($unitPrice,0,strlen($unitPrice)-1);
	$unitPriceArr = explode(",",$unitPrice);
	$resU = mysqli_query($conn,"select * from units where id in(".$unitIds.")");
	echo '<div style="border:0px solid red;width:70%;height:150px;"  id="unitstbl_'.$arr['id'].'">';
	$cnt = 0;
	while($arrU = mysqli_fetch_array($resU,MYSQLI_ASSOC))
	{
		echo '<div class="col-md-1" style="text-align: center;padding:10px;width="28%";border:0px solid #000;box-shadow: 0px 0px 3px #888888;">';
		echo '<img src="unitimages/'.($arrU['images']==""?"pna.jpg":$arrU['images']).'" style="vertical-align: top;width:50px;height:50px">';
		echo '<p style="width:80px;display:inline-block;padding:0px 10px 0px 10px;margin:0;font-size:.8em;white-space: nowrap;"><b>'.$arrU['units'].'</b><br>$'.$unitPriceArr[$cnt].'</p>';
		echo '<button type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;margin: 0 auto;border-radius: 3px;font-size: 1.0em;width:80px;display:inline;padding:0px;" onClick="onUnitClick(this,'.
										(isset($_SESSION['lcdata'])?$_SESSION['lcdata']['id']:"0").','.
										$arr['id'].','.
										$arr['reservationdays'].',\''.
										urlencode($arrU['units']).'\',\''.
										$unitPriceArr[$cnt].'\');">Reserve</button></div>';
		$cnt++;
	}
	echo "</div>";
}
function showOpt($arr)
{
	global $conn;
	$opt = $arr['options'];
	$pos = strpos($opt,",");
	if($pos ==  0)
		$opt = substr($opt,1,strlen($opt)-2);
	else
		$opt = substr($opt,0,strlen($opt)-1);
	$resO = mysqli_query($conn,"select * from options where id in(".$opt.")");
	echo '<p style="font-size:.8em">';
	while($arrO = mysqli_fetch_array($resO,MYSQLI_ASSOC))
		echo $arrO['opt'].', ';
	echo "</p>";
}
function file_get_contents_curl($url) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
curl_setopt($ch, CURLOPT_URL, $url);
$data = curl_exec($ch);
curl_close($ch);
return $data;
}
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
<style>
body {
  font-family: Verdana, sans-serif;
  margin: 0;
}

* {
  box-sizing: border-box;
}

.row > .column {
  padding: 0 8px;
}

.row:after {
  content: "";
  display: table;
  clear: both;
}

.column {
  float: left;
  width: 25%;
}

/* The Modal (background) */
.modal {
  display: none;
  position: fixed;
  z-index: 1;
  padding-top: 100px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: black;
}

/* Modal Content */
.modal-content {
  position: relative;
  background-color: #fefefe;
  margin: auto;
  padding: 0;
  width: 90%;
  max-width: 1200px;
}

/* The Close Button */
.close {
  color: white;
  position: absolute;
  top: 10px;
  right: 25px;
  font-size: 35px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #999;
  text-decoration: none;
  cursor: pointer;
}

.mySlides {
  display: none;
}

.cursor {
  cursor: pointer;
}

/* Next & previous buttons */
.prev,
.next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -50px;
  color: white;
  font-weight: bold;
  font-size: 20px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
  -webkit-user-select: none;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover,
.next:hover {
  background-color: rgba(0, 0, 0, 0.8);
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: absolute;
  top: 0;
}

img {
  margin-bottom: -4px;
}

.caption-container {
  text-align: center;
  background-color: black;
  padding: 2px 16px;
  color: white;
}

.demo {
  opacity: 0.6;
}

.active,
.demo:hover {
  opacity: 1;
}

img.hover-shadow {
  transition: 0.3s;
}

.hover-shadow:hover {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
</style>

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
								<center>
								<a href="index.php" style="display:inline;float:left;"><img id="logo" src="images/llogo.png" style="display:inline;width:40px;" alt="Logo"/></a>
								<input name="search" type="text" placeholder="Zip or Address" value="<?php echo (isset($_POST['search'])?$_POST['search']:"");?>" required="" style="width:50%;display:inline;margin:0;">
								<button data-toggle="modal" data-target="#myModal" type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;width:100%;margin: 0 auto;border-radius: 3px;padding: 0.3em 0.2em;font-size: 1.3em;display: block;font-family: 'Carrois Gothic', sans-serif;width:50px;display:inline;"><i class="fa fa-filter"></i></button>
								<button type="submit" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;width:100%;margin: 0 auto;border-radius: 3px;padding: 0.3em 0.2em;font-size: 1.3em;display: block;font-family: 'Carrois Gothic', sans-serif;width:50px;display:inline;"><i class="fa fa-search"></i></button>
								</center>
								</form>
							</div>
							<div class="clearfix"> </div>
						 </div>
				     <div class="clearfix"> </div>	
				</div>
<!--heder end here-->
  		<!---START-->
				<div id="myModal" class="modal fade" role="dialog">
				  <div class="modal-dialog">
				    <!-- Modal content-->
				    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
				    <div class="modal-content">
				      <div class="modal-body">
				        <p>
						<hr style="margin:5px 0px 5px 0px">
						<b>Filter Features</b>
						<hr style="margin:5px 0px 5px 0px">
						<?php
							$res = mysqli_query($conn,"select * from options");
							while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
							{
								$checked = "";
								if(isset($_SESSION['filter']) && (in_array($arr['id'].'[-]'.$arr['opt'],$_SESSION['filter'])))
									$checked = "checked";
								echo '<input type="checkbox" style="margin-right:5px;" name="options[]" value="'.
											$arr['id'].'[-]'.$arr['opt'].'" '.$checked.'>'.$arr['opt'].'<br>';
							}
						?>
						<input type="hidden" name="search" id="search" value="<?php echo (isset($_POST['search'])?$_POST['search']:"");?>">
						<input type="hidden" name="action" id="action" value="applyfilter">
				        </p>
				      </div>
				      <div class="modal-footer">
				      	<button class="btn btn-primary" name="submit" id="submit" value="Create" style="background:#68AE00;border-color:#68AE00;">Apply Filter</button>
				        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				      </div>
				    </div>
						</form>
				  </div>
				</div>
			<!---END-->

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
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<div class="blankpage-main" style="padding:1em 1em;">
		<div class="row">
<?php 
while($arrT = mysqli_fetch_array($res_fac,MYSQLI_ASSOC))
							{
?>
		<div class="column">
    <img src="<?php echo $arrT['image']?>" style="width:100%" onclick="openModal();currentSlide(1)" class="hover-shadow cursor">
  </div>
  
  <?php } ?>
</div>

<div id="myModal" class="modal">
  <span class="close cursor" onclick="closeModal()">&times;</span>
  <div class="modal-content">

    <div class="mySlides">
      <div class="numbertext">1 / 4</div>
      <img src="images/baner.jpg" style="width:100%">
    </div>

    <div class="mySlides">
      <div class="numbertext">2 / 4</div>
      <img src="images/reservation.png" style="width:100%">
    </div>

 
    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
    <a class="next" onclick="plusSlides(1)">&#10095;</a>

    <div class="caption-container">
      <p id="caption"></p>
    </div>


    <div class="column">
      <img class="demo cursor" src="img_nature_wide.jpg" style="width:100%" onclick="currentSlide(1)" alt="Nature and sunrise">
    </div>
    <div class="column">
      <img class="demo cursor" src="img_snow_wide.jpg" style="width:100%" onclick="currentSlide(2)" alt="Snow">
    </div>
    <div class="column">
      <img class="demo cursor" src="img_mountains_wide.jpg" style="width:100%" onclick="currentSlide(3)" alt="Mountains and fjords">
    </div>
    <div class="column">
      <img class="demo cursor" src="img_lights_wide.jpg" style="width:100%" onclick="currentSlide(4)" alt="Northern Lights">
    </div>
  </div>
</div>

<script>
function openModal() {
  document.getElementById('myModal').style.display = "block";
}

function closeModal() {
  document.getElementById('myModal').style.display = "none";
}

var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("demo");
  var captionText = document.getElementById("caption");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
  captionText.innerHTML = dots[slideIndex-1].alt;
}
</script>
    
			<br>
		
    	</div>
    </div>
</div>
<!--inner block end here-->
<!--copy rights start here-->
<div class="copyrights">
	 <p>© <?php echo date('Y',time());?> Leazzer, All Rights Reserved.</p>
</div>	
<!--COPY rights end here-->
</div>
</div>
<link href="admin/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="admin/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="admin/css/datepicker.css" />
<script type="text/javascript" src="admin/js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
    $('#datatable').DataTable({
    		"aaSorting": []
    		});
	$('#datatable').on('draw.dt', function () { 
	$('.datepicker').datepicker({
     	format: 'mm/dd/yyyy',
     	startDate: new Date(),
		autoclose:true
    });
	});
	$('.datepicker').datepicker({
     	format: 'mm/dd/yyyy',
     	startDate: new Date(),
		autoclose:true
    });
});
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
	else if(cid==0)
	{
			$('#mdatemsg_'+fid).hide();
			var res = ajaxcall("action=sessionreserve&fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price);
			if(res == "success")
			{
				window.location.href='customer/index.php';
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
<!--scrolling js-->
		<script src="facility/js/jquery.nicescroll.js"></script>
		<script src="facility/js/scripts.js"></script>
		<!--//scrolling js-->
<script src="facility/js/bootstrap.js"> </script>
<!-- mother grid end here-->
</body>
</html>                        