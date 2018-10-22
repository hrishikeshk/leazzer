<?php
session_start();
require_once('../mail/class.phpmailer.php');		
include('../sql.php');
$GError = ""; 
if(isset($_POST['action']))
{		
	if($_POST['action'] == "Register")
	{
		$res = mysqli_query($conn,"select * from facility where emailid='".$_POST['emailid']."'");
		if(mysqli_num_rows($res) > 0)
		{
			$arr = mysqli_fetch_array($res,MYSQLI_ASSOC);
			if($arr['status'] == "PENDING")
				$GError = register();
			else			
				$GError = "This emailid already registered. please login";
		}
		else
				$GError = register();
	}
}
function register()
{
	global $conn,$GError;
	$fromemail="no-reply@leazzer.com"; 
	$toemail=$_POST['emailid']; 
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= '<center><img src="leazzer.com/images/llogo.png" height="120px"><hr style="width:100%;margin-top:10px;margin-bottom:10px;border-top: 2px solid #30B242;"></center><br>';
	$message .= 'Hello <b>'.$_POST['fname'].' '.$_POST['lname'].',';
	$message .= '<br><br><b>Our warm welcome to Leazzer family, your UserID is '.$_POST['emailid'].'</b><br>';
	$message .= '</td></tr>';
	$message .= '<tr><td><br><br>';
	$message .= 'Thank you,<br>&mdash; Leazzer';
	$message .= '</td></tr>';
	$message .= '</table>';
	
	$mail = new PHPMailer(); // defaults to using php "mail()"
	$mail->CharSet = 'UTF-8';
	$mail->AddReplyTo($fromemail,"Leazzer Registration"); 
	$mail->SetFrom($fromemail, "Leazzer Registration");
	$mail->AddAddress($toemail, substr($toemail,0,strpos($toemail,"@")));
	$mail->Subject    = "Leazzer Registration";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->MsgHTML($message);
	$mail->isHTML(true);
	$ret = $mail->Send();
	mysqli_query($conn,"delete from facility where emailid='".$_POST['emailid']."'");
	mysqli_query($conn,"insert into facility(firstname,lastname,companyname,phone,emailid,pwd,reservationdays,status) values(N'".
											$_POST['fname']."','".
											$_POST['lname']."','".
											$_POST['companyname']."','".
											$_POST['phone']."','".
											$_POST['emailid']."','".
											$_POST['password']."','3','Enabled')");
	$resEU = mysqli_query($conn,"select * from facility where emailid='".$_POST['emailid']."'");
	$_SESSION['lfdata'] = mysqli_fetch_array($resEU,MYSQLI_ASSOC);
	header("Location: dashboard.php");
	return "Thanks for registering, please update your profile.";								
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
<body>
<body><a href="../index.php"><img id="logo" src="../images/llogo.png" style="width:40px;margin:20px;" alt="Logo"/></a>
<div class="login-page"  style="background:none;padding:0;">	
    <div class="login-main">  	
			<div class="login-block">
				<center><h1>Leazzer Register</h1></center>
				<hr>
				<?php 
				if($GError!="")
				{echo '<p style="color:#68AE00;">'.$GError.'</p>';}
				?>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
					<input type="text" name="companyname" placeholder="Company Name" required>
					<input type="text" name="fname" placeholder="First Name" required>
					<input type="text" name="lname" placeholder="Last Name" required>
					<input type="text" name="phone" placeholder="Phone" required>
					<input type="text" name="emailid" placeholder="Email" required>
					<input type="text" name="password" class="lock" placeholder="Password" required>
					<input type="submit" name="action" value="Register">
					<h3>Already a member?<a href="index.php"> Login</a></h3>				
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

