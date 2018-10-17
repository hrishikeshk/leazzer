<?php
require_once('../mail/class.phpmailer.php');		
include('../sql.php');
$GError = ""; 
if(isset($_POST['action']))
{		
	if($_POST['action'] == "Forgot")
	{
			$res = mysqli_query($conn,"SELECT * FROM admin WHERE username='".$_POST['username']."'");
			if(mysqli_num_rows($res)!=0)
			{
					$arr = mysqli_fetch_array($res,MYSQLI_ASSOC);
					$GError = forgotmail($arr['password']);
			}	
			else 
				$GError = "Username not found, please contact admin.";
	}
}
function forgotmail($pwd)
{
	global $conn,$GError;
	$fromemail="no-reply@leazzer.com"; 
	$toemail="no-reply@leazzer.com"; //Need to replace with admin emailid
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= 'Hi,';
	$message .= '<br><br>Here is the password <br>';
	$message .= '</td></tr>';
	$message .= '<tr><td><br><center>';
	$message .= '<p style="border-radius: 25px;background: #68AE00; padding:10px;color:#FFF;font-size:20px;">'.$pwd.'</p>';
	$message .= '</center></td></tr>';
	$message .= '<tr><td><br><br>';
	$message .= '&mdash; Leazzer';
	$message .= '</td></tr>';
	$message .= '</table>';
	
	$mail = new PHPMailer(); // defaults to using php "mail()"
	$mail->CharSet = 'UTF-8';
	$mail->AddReplyTo($fromemail,"Leazzer"); 
	$mail->SetFrom($fromemail, "Leazzer");
	$mail->AddAddress($toemail, substr($toemail,0,strpos($toemail,"@")));
	$mail->Subject    = "Leazzer Admin Forgot Password";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->MsgHTML($message);
	$mail->isHTML(true);
	$ret = $mail->Send();
	return "Please check your email for password.";								
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
<div class="login-page" style="min-height:700px;">
    <div class="login-main" style="min-height:300px;">  	
			<div class="login-block">
				<center><h1>Forgot Password</h1></center>
				<hr>
				<?php 
				if($GError!="")
				{echo '<p style="color:#68AE00;">'.$GError.'</p>';}
				?>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
					<input type="text" name="username" placeholder="Username" required="">
					<input type="submit" name="action" value="Forgot">	
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
</body>
</html>


                      
						
