<?php
session_start();
if((!isset($_SERVER['HTTPS'])) || ($_SERVER['HTTPS'] != "on")){
	$url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	header("Location: $url");
	exit;
}

include('../sql.php');
$GError = ""; 
if(isset($_GET['action'])){
	if($_GET['action'] == "logout"){
		unset($_SESSION['lfdata']);
		//session_destroy();
	}
}

if(isset($_POST['action'])){		
	if($_POST['action'] == "Login"){
			$res = mysqli_query($conn, "SELECT O.emailid as emailid, M.id as id,O.auto_id as auto_id, O.phone as phone, M.status as status FROM facility_owner O, facility_master M WHERE O.auto_id = M.facility_owner_id and M.facility_owner_id is not null and O.emailid='".$_POST['emailid']."' and O.pwd='".$_POST['password']."' limit 1");
			if(mysqli_num_rows($res) != 0){
	  			$arr = mysqli_fetch_array($res,MYSQLI_ASSOC);
	  			if($arr['status'] == "1"){
	  				$GError = "Logged in successfully.";
	  				$_SESSION['lfdata']	= $arr;
	  				//header("Location: dashboard.php");
  					header("Location: profile.php");
					}
					else
					  $GError = "Userid and/or Password may be incorrect";
			}
			else{
				$res = mysqli_query($conn, "SELECT emailid, auto_id, phone FROM facility_owner where emailid='".$_POST['emailid']."' and pwd='".$_POST['password']."' limit 1");
  			if(mysqli_num_rows($res) != 0){
	    			$arr = mysqli_fetch_array($res,MYSQLI_ASSOC);
	    			$GError = "Logged in successfully.";
	    			$_SESSION['lfdata']	= $arr;
	    			//header("Location: dashboard.php");
    				header("Location: profile.php");
	  		}
	  		else
	  			$GError = "Userid and/or Password may be incorrect.";
  	}
  }
}

mysqli_close($conn);

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Leazzer" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<script src="js/jquery-2.1.1.min.js"></script> 
<link href="css/font-awesome.css" rel="stylesheet"> 
<link href='fonts/fonts.css' rel='stylesheet' type='text/css'>
</head>
<body><a href="../index.php"><img id="logo" src="../images/llogo.png" style="width:40px;margin:20px;" alt="Logo"/></a>
<div class="login-page"  style="background:none;padding:0;">
    <div class="login-main">  	
			<div class="login-block">
				<center><h1>Leazzer Login</h1></center>
				<hr>
				<?php 
				if($GError!="")
				{echo '<p style="color:#68AE00;">'.$GError.'</p>';}
				?>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
					<input type="text" name="emailid" placeholder="Email" required="">
					<input type="password" name="password" class="lock" placeholder="Password">
					<div class="forgot-top-grids">
						<div class="forgot">
							<a href="forgot.php">Forgot password?</a>
						</div>
						<div class="clearfix"> </div>
					</div>
					<input type="submit" name="action" value="Login">	
					<h3>Not a member?<a href="register.php"> Register now</a></h3>				
				</form>
				<h5><a href="../index.php">Go Back to Home</a></h5>
			</div>
      </div>
</div>
<!--inner block end here-->
<!--scrolling js-->
		<script src="js/jquery.nicescroll.js"></script>
		<script src="js/scripts.js"></script>
		<!--//scrolling js-->
<script src="js/bootstrap.js"> </script>
<!-- mother grid end here-->
<?php
include('footer.php');
?>

