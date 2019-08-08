<?php
session_start();
require_once('../mail/class.phpmailer.php');		
include('../sql.php');
$GError = "";

function recaptcha_curl($url, $posts){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  //curl_setopt($ch, CURLOPT_POST, true); The bugger who wrote this API does not add content-length if this is a POST.
  //$jposts = json_encode($posts);
  $post_str = "secret=".$posts['secret']."&response=".$posts['response'];
  //curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $posts);
  curl_setopt($ch, CURLOPT_HEADER, array('Content-length: ' .strlen($post_str)));
  
  //curl_setopt($ch, CURLOPT_POSTFIELDS, $jposts);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type' => 'application/x-www-form-urlencoded'));
  //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type' => 'application/x-www-form-urlencoded'));
  //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type' => 'application/json'));
  //curl_setopt($ch, CURLOPT_HEADER, 0);
  
  curl_setopt($ch, CURLOPT_HEADER, array('Content-Length: ' .strlen($post_str)));
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

if(isset($_POST['action'])){
	if($_POST['action'] == "Register"){
	  if(isset($_POST['g-recaptcha-response']) === true){
	    
	    $url = 'https://www.google.com/recaptcha/api/siteverify';
      $posts = array('secret' => '6Ld5ErIUAAAAABzmur2ytqT0vEMGnwbI20HJ3FQS',
                     'response' => $_POST['g-recaptcha-response'],
               );
      $ret = recaptcha_curl($url, $posts);
      //error_log($ret);
      //$dev_ret = json_decode($ret, true);
	  ////  
	  //error_log('re-conv: '.json_encode($dev_ret));
	    if(stristr($ret, '"success": true,') !== FALSE){
		    $res = mysqli_query($conn,"select * from customer where emailid='".mysqli_real_escape_string($conn, $_POST['emailid'])."'");
    		if(mysqli_num_rows($res) > 0){
		    	$arr = mysqli_fetch_array($res,MYSQLI_ASSOC);
		    	if($arr['status'] == "PENDING")
		    		$GError = register();
		    	else			
		    		$GError = "This emailid already registered. please login";
		    }
		    else
		    		$GError = register();
		  }
		  else{
        $GError = "Invalid CAPTCHA. Please try again... ";
      }
		}
		else{
		  $GError = "Un-attempted CAPTCHA. Please try again.";
		}
	}
}

function register(){
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
	$message .= 'Thank you,<br>&mdash; Brainyvestors';
	$message .= '</td></tr>';
	$message .= '</table>';

	$mail = new PHPMailer(); // defaults to using php "mail()"
	$mail->CharSet = 'UTF-8';
	$mail->AddReplyTo($fromemail,"Brainyvestors Registration"); 
	$mail->SetFrom($fromemail, "Brainyvestors Registration");
	$mail->AddAddress($toemail, substr($toemail,0,strpos($toemail,"@")));
	$mail->Subject    = "Brainyvestors Registration";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->MsgHTML($message);
	$mail->isHTML(true);
	$ret = $mail->Send();
	mysqli_query($conn,"delete from customer where emailid='".$_POST['emailid']."'");
	mysqli_query($conn,"insert into customer(firstname,lastname,phone,emailid,pwd,status,logintype) values(N'".
											mysqli_real_escape_string($conn, $_POST['fname'])."',N'".
											mysqli_real_escape_string($conn, $_POST['lname'])."',N'".
											mysqli_real_escape_string($conn, $_POST['phone'])."',N'".
											mysqli_real_escape_string($conn, $_POST['emailid'])."','".
											mysqli_real_escape_string($conn, $_POST['password'])."','Enabled','normal')");
	$resEU = mysqli_query($conn,"select * from customer where emailid='".mysqli_real_escape_string($conn, $_POST['emailid'])."'");
	$_SESSION['lcdata'] = mysqli_fetch_array($resEU,MYSQLI_ASSOC);
	header("Location: index.php");
	return "Thanks for registering.";								
}

mysqli_close($conn);
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Brainyvestors</title>
<link rel="icon" href="https://www.brainyvestors.com/images/llogo.jpg" type="image/jpg">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Brainyvestors" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<script src="js/jquery-2.1.1.min.js"></script> 
<link href="css/font-awesome.css" rel="stylesheet"> 
<link href='fonts/fonts.css' rel='stylesheet' type='text/css'>
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<body><a href="../index.php"><img id="logo" src="../images/llogo.jpg" style="width:40px;margin:20px;" alt="Logo"/></a>
<div class="login-page"  style="background:none;padding:0;">
    <div class="login-main">  	
			<div class="login-block">
				<center><h1>Brainyvestors Register</h1></center>
				<hr>
				<?php 
				if($GError!="")
				{echo '<p style="color:#68AE00;">'.$GError.'</p>';}
				?>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
					<input type="text" name="fname" placeholder="First Name" required>
					<input type="text" name="lname" placeholder="Last Name" required>
					<input type="text" name="phone" placeholder="Phone" required>
					<input type="text" name="emailid" placeholder="Email" required>
					<input type="text" name="password" class="lock" placeholder="Password" required>
					<div class="g-recaptcha" data-sitekey="6Ld5ErIUAAAAAGrVWYWmafa38XxWwtfJA-GiBZVC"></div>
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

</body>
</html>

                      
						
