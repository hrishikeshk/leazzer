<?php
$GError="";
include('header.php');
if(isset($_POST['submit'])){
	if($_POST['submit'] == "Change Password"){
		if($_POST['newpwd'] != $_POST['conpwd'])
			$GError= "New password and confirm password mismatch.";		
		else{
			$results = mysqli_query($conn,"select * from facility_owner where auto_id='".mysqli_real_escape_string($conn, $_SESSION['lfdata']['auto_id'])."' and pwd='".mysqli_real_escape_string($conn, $_POST['curpwd'])."'");	
			if(mysqli_num_rows($results) == 0){
				$GError= "Wrong current password.";		
			}
			else{
				$GError= "Password updated successfully.";		
				mysqli_query($conn,"update facility_owner set pwd='".mysqli_real_escape_string($conn, $_POST['newpwd'])."' where auto_id='".mysqli_real_escape_string($conn, $_SESSION['lfdata']['auto_id'])."'");
				$_SESSION['lfdata']['pwd'] = $_POST['newpwd'];
				$query = "select * from owner_card where owner_id = '".$_SESSION['lfdata']['auto_id']."'";
        $res = mysqli_query($conn, $query);
        if(mysqli_num_rows($res) == 0){
          header("Location: cdinfo.php");
        }
			}
		}
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
    </div>
</div>
<!--inner block end here-->
<?php
include('footer.php');
?>

