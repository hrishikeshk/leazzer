<?php
$GError="";
include('header.php');
if(isset($_POST['submit'])){
if($_FILES['csvs']['name']){

$arrFileName = explode('.',$_FILES['csvs']['name']);

if($arrFileName[1] == 'csv'){
$sql="DELETE FROM units";
$res=mysqli_query($conn,$sql);
$handle = fopen($_FILES['csvs']['tmp_name'], "r");

while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

$item1 = mysqli_real_escape_string($conn,$data[0]);
$item2 = mysqli_real_escape_string($conn,$data[1]);
$item3 = mysqli_real_escape_string($conn,$data[2]);
$item4 = mysqli_real_escape_string($conn,$data[3]);
echo $item5 = mysqli_real_escape_string($conn,$data[4]);
$item6 = mysqli_real_escape_string($conn,$data[5]);

if($res){
$r=$item5;
$import="INSERT into units(id,units,images,standard,description,price) values('$item1','$item2','$item3','$item4','$item5','$item6')";
mysqli_query($conn,$import);
}
else{
$r="p";
}
}
fclose($handle);

}
}
}


?>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<h2>Price</h2>
    	<div class="blankpage-main">
    		<center>
    			<h4>Upload Prices</h4>
    			<hr>
					<?php
					if($GError!="")
					{
						echo "<div class=\"alert alert-info\" role=\"alert\">".$GError."</div>";
					}
					?>	<?php echo $r;?>
					<form name="changepwdfrm" id="changepwdfrm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype='multipart/form-data'>
							<input class="form-control"  type="file" name="csvs" id="userfile" required style="width:20%;height:10%"><br>
			    		<button class="btn btn-success" name="submit" value="Change Password" style="background:#68AE00;border-color:#68AE00;">Upload file</button>							    		
					</form>												
				</center>
    	</div>
    </div>
</div>
<!--inner block end here-->
<?php
include('footer.php');
?>