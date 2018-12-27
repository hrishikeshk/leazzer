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
									echo '<a href="search_n.php" style="float:right;margin-top:12px;padding:3px;color:#68AE00;">Search</a>';
								else
									echo '<a href="index.php" style="float:right;margin-top:12px;padding:3px;color:#68AE00;">Home Page</a>';
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

  		function validate($code, $fid, $cid){
	  	  global $conn;
	  	  $code_gen = md5($fid.'|'.$cid.'surveycode');
	  	  if($code_gen !== $code)
	  	    header("Location: index.php");
	  	  else{
	  	    $res = mysqli_query($conn, "select C.firstname as fn, C.lastname as ln, M.title as title, M.id as fid from reserve R, customer C, facility_master M where R.fid='".$fid."' and R.cid='".$cid."' and M.id=R.fid and C.id=R.cid");
		      if($arr = mysqli_fetch_array($res, MYSQLI_ASSOC)){
      			return $arr;
		      }
		      else
		        return 1;
	  	  }
	  	}
	
		  if(isset($_GET['code']) && isset($_GET['fid']) && isset($_GET['cid'])){
        $r = validate($_GET['code'], $_GET['fid'], $_GET['cid']);
        if($r == 1)
          header("Location: index.php");
        else
          $_SESSION['rakey'] = $r;
		  }
		  else if(isset($_POST['comment']) && isset($_SESSION['rakey'])){
		    $customer_name = $_SESSION['rakey']['ln'];
		    if(stristr($_SESSION['rakey']['ln'], $_SESSION['rakey']['fn']) == FALSE)
		      $customer_name = $_SESSION['rakey']['ln']." ".$_SESSION['rakey']['ln'];

		    $res = mysqli_query($conn, "insert into review(facility_id, listing_avail_id, rating, message, nickname, timestamp) values ('".$_SESSION['rakey']['fid']."', '".$_SESSION['rakey']['fid']."', '".$_POST['stars']."', '".$_POST['comment']."', '".$customer_name."', now())") or die('Failed to submit Review. Please try again after some time.');
		    header("Location: reviews.php?facility_id=".$_SESSION['rakey']['fid']);

		  }
		  else{
        header("Location: index.php");
		  }

      ?>
      <style>
        .checked {
          color: orange;
        }
      </style>
      <script type="text/javascript">
        function star(r){
          for(i = 1; i <= r; i++){
            $("#s" + i).addClass('checked');
          }
          for(i = r + 1; i <= 5; i++){
            $("#s" + i).removeClass('checked');
          }
          $("#stars").val(r);
        }
      </script>
      <h4><center>Customer Feedback: <?php echo $_SESSION['rakey']['title']; ?> </center></h4>
    	<br /><br />
    	<center>
    	
      <form method="post" action="review_add.php" enctype="multipart/form-data">
      
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
          <div>
          <a href="#" onclick="star(1);"><span class="fa fa-star checked" id="s1" style="font-size:40px"></span></a>
          <a href="#" onclick="star(2);"><span class="fa fa-star checked" id="s2" style="font-size:40px"></span></a>
          <a href="#" onclick="star(3);"><span class="fa fa-star checked" id="s3" style="font-size:40px"></span></a>
          <a href="#" onclick="star(4);"><span class="fa fa-star" id="s4" style="font-size:40px"></span></a>
          <a href="#" onclick="star(5);"><span class="fa fa-star" id="s5" style="font-size:40px"></span></a>
          
          </div>
          <br />
          <input type="hidden" name="stars" id="stars" value="3" />
          <textarea class="form-control" placeholder="Comments and Feedback (max 200 characters)" name="comment" id="comment" 
										style="margin-bottom:0px;display:inline;width:40%;height:50px;" required onkeypress="return event.keyCode != 13;" maxlength="200" spellcheck="true" autofocus="true"></textarea>
          <!-- input type="textarea" name="comment" id="comment" style="width:200px;height:50px; border-radius: 20px;" required--> 
          <br />
          <!-- input type="hidden" name="reffer" value="<?php echo (isset($_POST["reffer"])?$_POST["reffer"]:$_SERVER["HTTP_REFERER"]);?>" -->
          <br />
					<input type="submit" name="action" value="Submit" style="width:100px;height:40px; border-radius: 10px;">
				</form>
		<br />
		<?php
			if((isset($_SERVER["HTTP_REFERER"]) && (strpos($_SERVER["HTTP_REFERER"],"search.php")!==false)) || 
					(isset($_GET['ref']) && ($_GET['ref']=="search")))
				echo '<h5><a href="search.php" style="color:#68AE00;">Search</a></h5>';
			else
				echo '<h5><a href="index.php" style="color:#68AE00;">Home Page</a></h5>';
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
