<?php
require_once('../mail/class.phpmailer.php');		
include('../sql.php');
$GError = "";
$uid = ""; 
if(!isset($_GET['code'])){
	header("Location: ../index.php");
}
else if(isset($_GET['code'])){
		$res = mysqli_query($conn,"SELECT * FROM forgotpwd WHERE code='".mysqli_real_escape_string($conn, $_GET['code'])."'");
		if(mysqli_num_rows($res)!=0){
				$arr = mysqli_fetch_array($res,MYSQLI_ASSOC);
				$uid= $arr['uid'];
		}	
		else 
			$GError = "Incorrect code, please contact admin.";
}

if(isset($_POST['action'])){		
	if($_POST['action'] == "Reset Password"){
		if($_POST['pwd'] != $_POST['cpwd'])
			$GError= "New password and confirm password mismatch.";		
		else{
				$GError= "Password updated successfully.";		
				mysqli_query($conn,"update customer set pwd='".mysqli_real_escape_string($conn, $_POST['pwd'])."' where id='".mysqli_real_escape_string($conn, $_POST['uid'])."'");
		}
	}
}

?>
<!DOCTYPE HTML>
<html>
<head>
<link rel="icon" href="https://www.brainyvestors.com/images/llogo.jpg" type="image/jpg">
<title>Brainyvestors</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Brainyvestors" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<script src="js/jquery-2.1.1.min.js"></script> 
<link href="css/font-awesome.css" rel="stylesheet"> 
<link href='fonts/fonts.css' rel='stylesheet' type='text/css'>
</head>
<body>	
<div class="login-page" style="min-height:700px;">
    <div class="login-main" style="min-height:300px;">  	
			<div class="login-block">
				<center><h1>Reset Password</h1></center>
				<hr>
				<?php 
				if($GError!=""){
					echo '<center><p style="color:#68AE00;">'.$GError.'</p></center>';
				}
				if($GError == "" ||
					 $GError == "New password and confirm password mismatch."){
					echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?code='.$_GET['code'].'" enctype="multipart/form-data">
					<input type="password" name="pwd" placeholder="Password" required="">
					<input type="password" name="cpwd" placeholder="Confirm Password" required="">
					<input type="hidden" name="uid" value="'.$uid.'">
					<input type="submit" name="action" value="Reset Password">	
					</form>';
				}
				?>
				<h5><a href="../index.php">Go Back to Home</a></h5>
			</div>
      </div>
</div>
<!--inner block end here-->
<!--scrolling js-->
<!-- 
		<script src="js/jquery.nicescroll.js"></script>
		<script src="js/scripts.js"></script>
		-->
		<!--//scrolling js-->
<script src="js/bootstrap.js"> </script>
<!-- mother grid end here-->

</body>
</html>

