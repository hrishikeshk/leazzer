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
		//session_destroy();
	}
}

if(isset($_POST['action'])){
	if($_POST['action'] == "OLogin"){
			$res = mysqli_query($conn,"SELECT * FROM customer WHERE emailid='".$_POST['email']."'");
			if(mysqli_num_rows($res)!=0){
				processLogin($res);
			}
			else{
				mysqli_query($conn,"INSERT INTO customer(firstname,lastname,emailid,pwd,logintype,phone,status) values('".
													 $_POST['fname']."','".
													 $_POST['lname']."','".
													 $_POST['email']."','','".
													 $_POST['acctype']."','".$_POST['phone']."','Enabled')");
				$res = mysqli_query($conn,"SELECT * FROM customer WHERE emailid='".$_POST['email']."'");
				processLogin($res);
			}
	}
	else if($_POST['action'] == "Sign In"){
			$res = mysqli_query($conn,"SELECT * FROM customer WHERE emailid='".$_POST['emailid']."' and pwd='".$_POST['password']."' and status='Enabled'");
			if(mysqli_num_rows($res)!=0){
					processLogin($res);
			}	
			else 
				$GError = "Userid and/or Password may be incorrect";
	}
}

function save_phone($cid, $phone){
  global $conn;
  mysqli_query($conn,"UPDATE customer set phone = '".$phone."' where id = '".$cid."'");
}

function has_valid_phone($phone){
  if(isset($phone) && strlen($phone) >= 10)
    return true;
  return false;
}

function processLogin($res){
	global $conn;
	$arr = mysqli_fetch_array($res,MYSQLI_ASSOC);
	$GError = "Logged in successfully.";
	$_SESSION['lcdata']	= $arr;
	
	$has_phone_saved = has_valid_phone($arr['phone']);
	$reserve = "";
	if(isset($_SESSION['res_fid'])){
	  $session_has_phone = has_valid_phone($_SESSION['res_phone']);
	  $has_phone_posted = (isset($_POST['phone']) && has_valid_phone($_POST['phone']));
	  $phone = 'unknown';
	  if($has_phone_posted == true && isset($arr['id']) && $has_phone_saved == false){
	    save_phone($arr['id'], $_POST['phone']);
	    $_SESSION['res_phone'] = $_POST['phone'];
	    $phone = $_POST['phone'];
	  }
	  else if($has_phone_saved == false && $session_has_phone == true && isset($arr['id'])){
	    save_phone($arr['id'], $_SESSION['res_phone']);
	    $phone = $_SESSION['res_phone'];
	  }
    
    $_SESSION['res_phone'] = $phone;
    
    error_log('customer index post login success - '.$phone.', session: '.$_SESSION['res_phone'].', posted: '.$_POST['phone']);
		$reserve = "&fid=".$_SESSION['res_fid'].
							"&cid=".$_SESSION['lcdata']['id'].
							"&rdays=".$_SESSION['res_rdays'].
							"&rdate=".$_SESSION['res_rdate'].
							"&unit=".$_SESSION['res_unit'].
							"&price=".$_SESSION['res_price'].
							"&phone=".$phone;
	}
	error_log('customer index post login success - reserve string: '.$reserve);
	if($has_phone_saved == false && $session_has_phone == false && $has_phone_posted == false)
	  header("Location: ../askphone.php?ref=index".$reserve);
	else if(isset($_GET["action"]) && ($_GET["action"] == "search"))
		header("Location: ../thankyou_n.php?ref=index".$reserve);
	else if(isset($_POST["reffer"]) && (strpos($_POST["reffer"],"search_n.php")!==false))
		header("Location: ../thankyou_n.php?ref=search".$reserve);
	else
		header("Location: dashboard.php");
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Leazzer" />
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
    /*FB.getLoginStatus(function(response)
    {
    	if(response.status == 'connected')
    	{
    		getFBUserData();
    	}
    });*/
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
  	FB.login(function(response){
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
<body><a href="../index.php"><img id="logo" src="../images/llogo.png" style="width:40px;margin:20px;" alt="Logo"/></a>
<div class="login-page"  style="background:none;padding:0;">
    <div class="login-main">  	
			<div class="login-block">
				<center><h1>Leazzer Sign In</h1></center>
				<hr>
				<?php 
				  if($GError!=""){
				    echo '<p id="status" style="color:#68AE00;">'.$GError.'</p>';
				  }
				?>
				<form method="post" name="hiddenform" id="hiddenform" action="<?php echo $_SERVER['PHP_SELF']."?action=".(isset($_GET['action'])?$_GET['action']:"")?>" enctype="multipart/form-data">
					<input type="hidden" name="fname" id="hfname">
					<input type="hidden" name="lname" id="hlname">
					<input type="hidden" name="email" id="hemail">
					<input type="hidden" name="phone" id="hphone">
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
				<?php
				if(isset($_SERVER["HTTP_REFERER"]) && (strpos($_SERVER["HTTP_REFERER"],"search.php")!==false))
					echo '<h5><a href="../search.php">Go Back Search</a></h5>';
				else{
					if(isset($_POST["reffer"]) && (strpos($_POST["reffer"],"search.php")!==false))
						echo '<h5><a href="../search.php">Go Back Search</a></h5>';
					else
						echo '<h5><a href="../index.php">Go Back Home</a></h5>';
				}
				?>
				
			</div>
      </div>
</div>
<!--inner block end here-->

<!--copy rights start here-->
<div class="copyright" style="background-color:#000000;text-align:center;display:block;color:#fff">
		<p>© <?php echo date("Y",time());?> Leazzer. All rights reserved | <a href="../global_footer.php">Privacy Policy</a> | <a href="../global_footer_tu.php">Terms of use</a>
    </p>
</div>	
<!--COPY rights end here-->

<!--scrolling js-->
		<script src="js/jquery.nicescroll.js"></script>
		<script src="js/scripts.js"></script>
		<!--//scrolling js-->
<script src="js/bootstrap.js"> </script>
<!-- mother grid end here-->

<!-- Start of HubSpot Embed Code -->
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/5051579.js"></script>
<!-- End of HubSpot Embed Code -->

</body>
</html>


                      
						
				
