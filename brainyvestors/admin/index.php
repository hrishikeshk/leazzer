<?php
session_start();
include('../sql.php');
$GError = ""; 
if(isset($_GET['action']))
{
	if($_GET['action'] == "logout")
	{
		unset($_SESSION['ladata']);
		session_destroy();
	}
}
if(isset($_POST['action']))
{		
	if($_POST['action'] == "Login")
	{
			$res = mysqli_query($conn,"SELECT * FROM admin WHERE username='".$_POST['username']."' and password='".$_POST['password']."'");
			if(mysqli_num_rows($res)!=0)
			{
					$arr = mysqli_fetch_array($res,MYSQLI_ASSOC);
					$GError = "Logged in successfully.";
					$_SESSION['ladata']	= $arr;
					header("Location: settings.php");
			}	
			else 
				$GError = "Userid and/or Password may be incorrect";
	}
}
mysqli_close($conn);
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Brainyvestors</title>
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
<body>	
<div class="login-page" style="min-height: 700px;background:none;">
    <div class="login-main">  	
			<div class="login-block">
				<center><h1>Brainyvestors Admin</h1></center>
				<hr>
				<?php 
				if($GError!="")
				{echo '<p style="color:#68AE00;">'.$GError.'</p>';}
				?>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
					<input type="text" name="username" placeholder="Username" required="">
					<input type="password" name="password" class="lock" placeholder="Password">
					<div class="forgot-top-grids">
						<!--<div class="forgot">
							<a href="forgot.php">Forgot password?</a>
						</div>-->
						<div class="clearfix"> </div>
					</div>
					<input type="submit" name="action" value="Login">	
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
</body>
</html>
