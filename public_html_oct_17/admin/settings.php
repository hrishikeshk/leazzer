<?php
$GError="";
include('header.php');

function fetch_ppterm(){
	global $conn;
	$ret = "";
			
	$res = mysqli_query($conn,"select * from admin_configuration where name='ppterms'");
	if(mysqli_num_rows($res) > 0){
		$arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
		$ret .= $arr['data_value'];	
	}
	else{
		$ret = "TODO: Add policy terms through admin panel...";
	}
	return htmlentities($ret);
}

function update_ppterm(){
  global $conn;		
	mysqli_query($conn,"update admin_configuration set data_value='".$_POST['setppterm']."' where name='ppterms'");
	$GError= "Privacy policy and terms updated successfully.";
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
	else if($_POST['submit'] == "setppterm"){
    update_ppterm();
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
					if($GError!="")
					{
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
					if($GError!="")
					{
						echo "<div class=\"alert alert-info\" role=\"alert\">".$GError."</div>";
					}
					?>	
					<form name="changepptermfrm" id="changepptermfrm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<input class="form-control" placeholder="SetPPTerm" type="textarea" name="setppterm" id="setppterm" style="width:90%;" value="<?php echo (fetch_ppterm()); ?>"><br />
			    		<button class="btn btn-success" name="submit" value="setppterm" style="background:#68AE00;border-color:#68AE00;">Set Privacy Policy and Terms of Use</button>
					</form>												
				</center>
    	</div>
    </div>
</div>
<!--inner block end here-->

<?php
include('footer.php');
?>
