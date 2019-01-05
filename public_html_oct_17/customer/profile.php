<?php
$GUError="";
include('header.php');
if(isset($_POST['submit'])){
	if($_POST['submit'] == "Update Profile"){
		//emailid=N'".$_POST['emailid']."',
		$query = "update customer set firstname=N'".mysqli_real_escape_string($conn, $_POST['fname']).
							"',lastname=N'".mysqli_real_escape_string($conn, $_POST['lname']).
							"',phone=N'".mysqli_real_escape_string($conn, $_POST['phone'])."'";

		$query .= " where id='".mysqli_real_escape_string($conn, $_SESSION['lcdata']['id'])."'";
		mysqli_query($conn,$query);
		$resC = mysqli_query($conn,"select * from customer where id='".mysqli_real_escape_string($conn, $_SESSION['lcdata']['id'])."'");
		$_SESSION['lcdata'] = mysqli_fetch_array($resC,MYSQLI_ASSOC);
		$GUError = "Updated successfully.";
	}
}
$resUP = mysqli_query($conn,"select * from customer where id='".mysqli_real_escape_string($conn, $_SESSION['lcdata']['id'])."'");
$arrUP = mysqli_fetch_array($resUP,MYSQLI_ASSOC);
?>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<h2>My Profile</h2>
    	<div class="blankpage-main">
    		<center>
    			<h4>Update Profile</h4>
    			<hr>
					<?php
					if($GUError!=""){
						echo "<div class=\"alert alert-info\" role=\"alert\">".$GUError."</div>";
					}
					?>	
					<form name="updateprofilefrm" id="updateprofilefrm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">	
						<?php
						echo '<table width="90%" border=0>';
						echo '<tr><td class="tdheading">Emailid:</td><td><input type="text" name="emailid" placeholder="EmailID" value="'.$arrUP['emailid'].'" required="" class="form-control" readonly><br></td></tr>';
						echo '<tr><td class="tdheading">First Name * :</td><td><input type="text" name="fname" placeholder="First Name" value="'.$arrUP['firstname'].'" required="" class="form-control"><br></td></tr>';
						echo '<tr><td class="tdheading">Last Name * :</td><td><input type="text" name="lname" placeholder="Last name" value="'.$arrUP['lastname'].'"required="" class="form-control"><br></td></tr>';
						echo '<tr><td class="tdheading">Phone * :</td><td><input type="text" name="phone" placeholder="Phone" required="" value="'.$arrUP['phone'].'" class="form-control">';
						echo ((trim($arrUP['phone']) == "")?'<p style="color:#FF0000;">Please enter phone number to proceed.':'').'</td></tr>';
						echo '</table>';
						?>
						<br>
						<button class="btn btn-success" name="submit" value="Update Profile" style="background:#68AE00;border-color:#68AE00;">Update Profile</button>
					</form>												
				</center>
    	</div>
    </div>
</div>
<!--inner block end here-->
<style>
.tdheading
{
	width:30%;
	font-weight: bold;
	text-align:right;
	vertical-align: top;
	padding-right:10px;
}
</style>
</script>
<?php
include('footer.php');
?>
