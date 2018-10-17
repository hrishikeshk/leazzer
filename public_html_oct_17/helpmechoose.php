<?php
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
<!---START-->
				<div id="myModal" class="modal fade" role="dialog">
				  <div class="modal-dialog">
				    <!-- Modal content-->
				    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
				    <div class="modal-content">
				      <div class="modal-body">
				      	
				      <p>
				      	<?php
								$res = mysqli_query($conn,"select * from units where standard=0");
								while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
								{
									if($arr['units'] == "")
										continue;
									
									echo '<div class="col-md-12" style="display:inline-block;box-shadow: 5px 5px 5px #888888;text-align:center;margin:5px;padding:0px;border-top:1px solid #ddd;border-left:1px solid #ddd;">';
									if($arr['images'] == "")
										echo '<img src="unitimages/pna.jpg" style="min-height:120px;width:120px;float:left;">';
									else
										echo '<img src="unitimages/'.$arr['images'].'" style="min-height:120px;width:120px;float:left;">';
									echo '<p style="font-size:.9em;text-align:left;padding:5px;"><b>'.$arr['units'].'</b><br>'.$arr['description'].'</p></div>';
								}
							?>
						</p>
						<div class="clearfix"> </div>
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				      </div>
				    </div>
						</form>
				  </div>
				</div>
			<!---END-->
			
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<div class="blankpage-main" style="padding:1em 1em;text-align:center;">
		<?php
			$res = mysqli_query($conn,"select * from units where standard=1");
			while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
			{
				if($arr['units'] == "")
					continue;
				
				echo '<div class="col-md-12" style="display:inline-block;box-shadow: 5px 5px 5px #888888;text-align:center;margin:5px;padding:0px;border-top:1px solid #ddd;border-left:1px solid #ddd;">';
				if($arr['images'] == "")
					echo '<img src="unitimages/pna.jpg" style="min-height:120px;width:120px;float:left;">';
				else
					echo '<img src="unitimages/'.$arr['images'].'" style="min-height:120px;width:120px;float:left;">';
				echo '<p style="font-size:.9em;text-align:left;padding:5px;"><b>'.$arr['units'].'</b><br>'.$arr['description'].'</p></div>';
			}
		?>
		<!--<br><a href="#" data-toggle="modal" data-target="#myModal" ><b>Cant find your unit type, please click more.</b></a>-->
		<div class="clearfix"> </div>
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
<!--scrolling js-->
		<script src="facility/js/jquery.nicescroll.js"></script>
		<script src="facility/js/scripts.js"></script>
		<!--//scrolling js-->
<script src="facility/js/bootstrap.js"> </script>
<!-- mother grid end here-->
</body>
</html>                        