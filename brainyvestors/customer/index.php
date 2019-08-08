<?php
if((!isset($_SERVER['HTTPS'])) || ($_SERVER['HTTPS'] != "on")){
	$url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	header("Location: $url");
	exit;
}
session_start();
include('../sql.php');
$GError = ""; 
if(isset($_GET['action'])){
	if($_GET['action'] == "logout"){
		unset($_SESSION['lcdata']);
	}
}
if(isset($_POST['action'])){
	if($_POST['action'] == "OLogin"){
			$res = mysqli_query($conn,"SELECT * FROM customer WHERE emailid='".mysqli_real_escape_string($conn, $_POST['email'])."'");
			if(mysqli_num_rows($res)!=0){
				processLogin($res);
			}
			else{
				mysqli_query($conn,"INSERT INTO customer(firstname,lastname,emailid,pwd,logintype,phone,status) values('".
													 mysqli_real_escape_string($conn, $_POST['fname'])."','".
													 mysqli_real_escape_string($conn, $_POST['lname'])."','".
													 mysqli_real_escape_string($conn, $_POST['email'])."','','".
													 mysqli_real_escape_string($conn, $_POST['acctype'])."','','Enabled')");
				$res = mysqli_query($conn,"SELECT * FROM customer WHERE emailid='".mysqli_real_escape_string($conn, $_POST['email'])."'");
				processLogin($res);
			}
	}
	else if($_POST['action'] == "Sign In"){
			$res = mysqli_query($conn,"SELECT * FROM customer WHERE emailid='".mysqli_real_escape_string($conn, $_POST['emailid'])."' and pwd='".mysqli_real_escape_string($conn, $_POST['password'])."' and status='Enabled'");
			if(mysqli_num_rows($res)!=0){
					processLogin($res);
			}	
			else 
				$GError = "Userid and/or Password may be incorrect";
	}
}

function processLogin($res){
	global $conn;
	$arr = mysqli_fetch_array($res,MYSQLI_ASSOC);
	$GError = "Logged in successfully.";
	$_SESSION['lcdata']	= $arr;
	
	header("Location: ../index.php");
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Brainyvestors</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Brainyvestors" />
<meta name="google-signin-client_id" content="437321147965-9c30s89biesj0e5sm45r56ahkpkcgn29.apps.googleusercontent.com">
<meta charset="UTF-8">
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<script src="js/jquery-2.1.1.min.js"></script> 
<script src="https://apis.google.com/js/client:platform.js?onload=renderButton" async defer></script>
<link href="css/font-awesome.css" rel="stylesheet"> 
<link href='fonts/fonts.css' rel='stylesheet' type='text/css'>
<script>
	window.fbAsyncInit = function(){
    FB.init({
      appId      : '347743159096213',
      cookie     : true,  
      xfbml      : true,  
      version    : 'v2.8'
    });
  };
  // Load the SDK asynchronously
  (function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
  
  function onFBLogin(){
  	FB.login(function(response) {
	    if (response.authResponse){
	    	getFBUserData();
	  	} 
    	else{
    		 console.log('User cancelled login or did not fully authorize.');
    	}
		},{scope:'email'});
  }
  function getFBUserData(){
  	FB.api('/me',{locale:'en_US',fields:'id,first_name,last_name,email'},
  	function(response){
  		//console.log(response.first_name+" --- "+response.last_name+" --- "+response.email);
  		if(response.email == "" || response.email == null || response.email == "undefined"){
  			$("#status").html("Unable to get required details contact admin.");
  		}
  		else{
  			$("#hfname").val(response.first_name);
  			$("#hlname").val(response.last_name);
  			$("#hemail").val(response.email);
  			$("#hacctype").val("fb");
  			$("#haction").val("OLogin");
  			$("#hiddenform").submit();
  		}
  	});
  }
</script>
<script>
function onSuccess(googleUser){
    var profile = googleUser.getBasicProfile();
    gapi.client.load('plus', 'v1', function (){
        var request = gapi.client.plus.people.get({
            'userId': 'me'
        });
        request.execute(function (resp){
        	if(resp.emails[0].value == "" || resp.emails[0].value == null || resp.emails[0].value == "undefined"){
		  			$("#status").html("Unable to get required details contact admin.");
		  		}
		  		else{
       			$("#hfname").val(resp.name.givenName);
	  				$("#hlname").val(resp.displayName);
	  				$("#hemail").val(resp.emails[0].value);
	  				$("#hacctype").val("google");
	  				$("#haction").val("OLogin");
	  				$("#hiddenform").submit();
	  			}
        });
    });
}
function onFailure(error){
    //alert("Error -- "+error);
}

function renderButton(){
    gapi.signin2.render('gSignIn', {
        'scope': 'profile email',
        'width': 250,
        'height': 50,
        'longtitle': true,
        'theme': 'dark',
        'onsuccess': onSuccess,
        'onfailure': onFailure
    });
}

function signOut(){
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
        //$('.userContent').html('');
        //$('#gSignIn').slideDown('slow');
    });
}

window.onbeforeunload = function(e){
  gapi.auth2.getAuthInstance().signOut();
};
</script>

</head>
<body>	
<body><a href="../index.php"><img id="logo" src="../images/llogo.jpg" style="width:40px;margin:20px;" alt="Logo"/></a>
<div class="login-page"  style="background:none;padding:0;">
    <div class="login-main">  	
			<div class="login-block">
				<center><h1>Brainyvestors Sign In</h1></center>
				<hr>
				<?php 
				if($GError!="")
				{echo '<p id="status" style="color:#68AE00;">'.$GError.'</p>';}
				?>
				<form method="post" name="hiddenform" id="hiddenform" action="<?php echo $_SERVER['PHP_SELF']."?action=".(isset($_GET['action'])?$_GET['action']:"")?>" enctype="multipart/form-data">
					<input type="hidden" name="fname" id="hfname">
					<input type="hidden" name="lname" id="hlname">
					<input type="hidden" name="email" id="hemail">
					<input type="hidden" name="acctype" id="hacctype" >
					<input type="hidden" name="action"  id="haction">
					<input type="hidden" name="reffer" value="<?php echo (isset($_POST["reffer"])?$_POST["reffer"]:$_SERVER["HTTP_REFERER"]);?>">	
				</form>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']."?action=".(isset($_GET['action'])?$_GET['action']:"")?>" enctype="multipart/form-data">
					<input type="text" name="emailid" placeholder="Email" required="">
					<input type="password" name="password" class="lock" placeholder="Password">
					<div class="forgot-top-grids">
						<div class="forgot">
							<a href="forgot.php">Forgot password?</a>
						</div>
						<div class="clearfix"> </div>
					</div>
					<input type="submit" name="action" value="Sign In" style="width:250px;height:50px;">	
					<center>
					<hr style="margin:10px;">
					<a href="#" onclick="onFBLogin();" style="display: inline-flex;"><img src="../images/fbimg.png" border="0" alt="" style="width:250px;height:50px;margin-top:10px"></a><br>
					<div id="gSignIn" style="width:250px;height:50px;margin:10px"></div>
					</center>
					<input type="hidden" name="reffer" value="<?php echo (isset($_POST["reffer"])?$_POST["reffer"]:$_SERVER["HTTP_REFERER"]);?>">	
					<h3>Not a member?<a href="register.php"> Register now</a></h3>				
				</form>
				
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

