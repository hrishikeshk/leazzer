<?php
$GError="";
include('header.php');

function fetch_pp(){
	global $conn;
	$ret = "";
			
	$res = mysqli_query($conn,"select * from admin_configuration where name='privacypolicy'");
	if(mysqli_num_rows($res) > 0){
		$arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
		$ret .= $arr['data_value'];	
	}
	else{
		$ret = "TODO: Paste here privacy policy (HTML text) ...";
	}
	return htmlentities($ret);
}

function fetch_tu(){
	global $conn;
	$ret = "";
			
	$res = mysqli_query($conn,"select * from admin_configuration where name='termsuse'");
	if(mysqli_num_rows($res) > 0){
		$arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
		$ret .= $arr['data_value'];
	}
	else{
		$ret = "TODO: Paste here terms of use (HTML text) ...";
	}
	return htmlentities($ret);
}

function update_pp(){
  global $conn;		
	mysqli_query($conn,"update admin_configuration set data_value='".$_POST['setpp']."' where name='pp'");
	$GError= "Privacy policy updated successfully.";
}

function update_tu(){
  global $conn;		
	mysqli_query($conn,"update admin_configuration set data_value='".$_POST['settu']."' where name='tu'");
	$GError= "Terms of use updated successfully.";
}

if(isset($_POST['submit']))
{
	if($_POST['submit'] == "Change Password")
	{
		if($_POST['newpwd'] != $_POST['conpwd'])
			$GError= "New password and confirm password mismatch.";		
		else
		{
			$results = mysqli_query($conn,"select * from admin where id=".$_SESSION['ladata']['id']." and password='".$_POST['curpwd']."'");	
			if(mysqli_num_rows($results) == 0)
			{
				$GError= "Wrong current password.";		
			}
			else
			{
				$GError= "Password updated successfully.";		
				mysqli_query($conn,"update admin set password='".$_POST['newpwd']."' where id='".$_SESSION['ladata']['id']."'");
				$_SESSION['ladata']['password'] = $_POST['newpwd'];
			}
		}
	}
	else if($_POST['submit'] == "setpp"){
    update_pp();
  }
  else if($_POST['submit'] == "settu"){
    update_pp();
  }
}
?>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<h2>Settings</h2>
    	<div class="blankpage-main">
    		<center>
    			<h4>Change Password</h4>
    			<hr>
					<?php
					if($GError!=""){
						echo "<div class=\"alert alert-info\" role=\"alert\">".$GError."</div>";
					}
					?>	
					<form name="changepwdfrm" id="changepwdfrm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<input class="form-control" placeholder="Current Password" type="password" name="curpwd" id="curpwd" required style="width:90%;"><br>
			    		<input class="form-control" placeholder="New Password" type="password" name="newpwd" id="newpwd" required style="width:90%;"><br>
			    		<input class="form-control" placeholder="Confirm Password" type="password" name="conpwd" id="conpwd" required style="width:90%;"><br>
			    		<button class="btn btn-success" name="submit" value="Change Password" style="background:#68AE00;border-color:#68AE00;">Change Password</button>							    		
					</form>												
				</center>
    	</div>
    	
    	<div class="blankpage-main">
    		<center>
    			<h4>Change Privacy Policy</h4>
    			<hr>
					<?php
					if($GError!=""){
						echo "<div class=\"alert alert-info\" role=\"alert\">".$GError."</div>";
					}
					?>	
					<form name="changeppfrm" id="changeppfrm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<input class="form-control" placeholder="SetPP" type="textarea" name="setpp" id="setpp" style="width:90%;" value="<?php echo (fetch_pp()); ?>"><br />
			    		<button class="btn btn-success" name="submit" value="setpp" style="background:#68AE00;border-color:#68AE00;">Set Privacy Policy</button>
					</form>
					<b>-- OR -- </b>
					<br /> <a href="pp_upload.php">Upload a .pdf file as Privacy Policy</a>
				</center>
    	</div>
    	
    	<div class="blankpage-main">
    		<center>
    			<h4>Change Terms Of Use</h4>
    			<hr>
					<?php
					if($GError!=""){
						echo "<div class=\"alert alert-info\" role=\"alert\">".$GError."</div>";
					}
					?>	
					<form name="changetermfrm" id="changetermfrm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<input class="form-control" placeholder="SetTerm" type="textarea" name="setterm" id="setterm" style="width:90%;" value="<?php echo (fetch_tu()); ?>"><br />
			    		<button class="btn btn-success" name="submit" value="settu" style="background:#68AE00;border-color:#68AE00;">Set Terms of Use</button>
					</form>
					<b>-- OR -- </b>
					<br /> <a href="tu_upload.php">Upload a .pdf file as Terms Of Use Policy</a>
				</center>
    	</div>
    </div>
</div>
<!--inner block end here-->

<?php
include('footer.php');
?>

