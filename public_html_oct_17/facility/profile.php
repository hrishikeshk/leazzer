<?php
$GError="";
include('header.php');
if(isset($_POST['submit'])){
	if($_POST['submit'] == "Update Profile"){
		$res = mysqli_query($conn,"select * from facility_owner where emailid='".mysqli_real_escape_string($conn, $_POST['emailid'])."'");	
		if(mysqli_num_rows($res) ==  1){
			$query = "update facility_owner set emailid=N'".mysqli_real_escape_string($conn, $_POST['emailid']).
								"',firstname=N'".mysqli_real_escape_string($conn, $_POST['fname']).
								"',lastname=N'".mysqli_real_escape_string($conn, $_POST['lname']).
								"',phone=N'".mysqli_real_escape_string($conn, $_POST['phone'])."'";

			$query .= " where auto_id='".mysqli_real_escape_string($conn, $_SESSION['lfdata']['auto_id'])."'";
			mysqli_query($conn, $query);
			
			$query = "update facility_master set phone='".mysqli_real_escape_string($conn, $_POST['phone'])."'";

			$query .= " where facility_owner_id='".mysqli_real_escape_string($conn, $_SESSION['lfdata']['auto_id'])."'";
			mysqli_query($conn, $query);

			$resC = mysqli_query($conn,"select O.emailid as emailid, M.id as id, O.auto_id as auto_id, M.phone as phone, M.status as status, O.pwd as pwd, O.firstname as firstname, O.lastname as lastname FROM facility_owner O, facility_master M WHERE O.auto_id = M.facility_owner_id and M.facility_owner_id is not null and O.auto_id='".mysqli_real_escape_string($conn, $_SESSION['lfdata']['auto_id'])."'");
			$_SESSION['lfdata'] = mysqli_fetch_array($resC, MYSQLI_ASSOC);

			$GError = "Updated successfully.";
		}
		else
			$GError = "Emailid does not exist.";
	}
}

$resUP = mysqli_query($conn,"select O.emailid as emailid, M.phone as phone, O.firstname as firstname, O.lastname as lastname FROM facility_owner O, facility_master M WHERE O.auto_id = M.facility_owner_id and M.facility_owner_id is not null and O.auto_id='".mysqli_real_escape_string($conn, $_SESSION['lfdata']['auto_id'])."'");
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
					if($GError!="")
						echo "<div class=\"alert alert-info\" role=\"alert\">".$GError."</div>";
					?>
					<form name="updateprofilefrm" id="updateprofilefrm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">	
						<?php
						echo '<table width="90%" border=0>';
						echo '<tr><td class="tdheading">Emailid:</td><td><input type="text" name="emailid" placeholder="EmailID" value="'.$arrUP['emailid'].'" required="" class="form-control"><br></td></tr>';
						echo '<tr><td class="tdheading">First Name:</td><td><input type="text" name="fname" placeholder="First Name" value="'.$arrUP['firstname'].'" required="" class="form-control"><br></td></tr>';
						echo '<tr><td class="tdheading">Last Name:</td><td><input type="text" name="lname" placeholder="Last name" value="'.$arrUP['lastname'].'"required="" class="form-control"><br></td></tr>';
						echo '<tr><td class="tdheading">Facility Phone:</td><td><input type="text" name="phone" placeholder="Facility Phone" required="" value="'.$arrUP['phone'].'" class="form-control"><br></td></tr>';
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

