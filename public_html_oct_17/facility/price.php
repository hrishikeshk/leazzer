<?php
$GError="";
include('header.php');
$sid=$_SESSION['lfdata']['id'];
if(isset($_POST['submit'])){
if($_FILES['csvs']['name']){

$arrFileName = explode('.',$_FILES['csvs']['name']);

if($arrFileName[1] == 'csv'){

$handle = fopen($_FILES['csvs']['tmp_name'], "r");

while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

$item1 = mysqli_real_escape_string($conn,$data[0]);
$item2 = mysqli_real_escape_string($conn,$data[1]);
$item3 = mysqli_real_escape_string($conn,$data[2]);
//$item4 = mysqli_real_escape_string($conn,$data[3]);
//$item5 = mysqli_real_escape_string($conn,$data[4]);
//$item6 = mysqli_real_escape_string($conn,$data[5]);

$r="price uploaded";
//$import="INSERT into units(id,units,images,standard,description,price) values('$item1','$item2','$item3','$item4','$item5','$item6')";
//$import="update facility set price='$item2' where units='$item1'";
$import="update facility_master set units='".$item1."-".$item3.','."' where id='".mysqli_real_escape_string($conn, $sid)."'";
if(mysqli_query($conn,$import)){
$r="price uploaded";
};


}
fclose($handle);

}
}
}
if(isset($_POST['submit2'])){
header('Content-Type: text/csv; charset=utf-8');  
header('Content-Disposition: attachment; filename=price_list.csv');  
$output = fopen("php://output", "w");  
     
$sid=$_SESSION['lfdata']['id'];
$res = mysqli_query($conn,"select * from facility_master where id='".mysqli_real_escape_string($conn, $sid)."'");
    		while($row1 = mysqli_fetch_assoc($res))
      {
	$err=$row1['units'];
	$result = preg_replace('/^,+|,+$/', '', $err);
	$marks = explode(",", $result);
	foreach($marks  as $mark_piece) {
        // do something here
	// $unit_id=$mark_piece[0].$mark_piece[1];
	$pieces = explode("-",$mark_piece);
	$unit_id=$pieces[0]; // piece1
    $price=$pieces[1]; // piece2
	 
	   $query = "select * from units where id='".mysqli_real_escape_string($conn, $unit_id)."'";
     $resulti = mysqli_query($conn, $query); 
	   ob_end_clean();
	   while($row = mysqli_fetch_array($resulti)){
	 // echo $row['price'];
	  if($price>0){
	  
	    $arr =array($row['id'],$row['units'], $price);

           fputcsv($output, $arr);
}		  
	  }
      
	}
	
	  fclose($output);  
exit() ;
		
	 
	 }
}

?>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<h2>Price</h2>
    	<div class="blankpage-main">
    		<center>
			<form name="changepwdfrm" id="changepwdfrm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype='multipart/form-data'>

			<h4>Download Prices</h4>
			<button class="btn btn-success" name="submit2" value="Change Password" style="background:#68AE00;border-color:#68AE00;">Download file</button>

    			<hr>
					<?php
					if($GError!=""){
						echo "<div class=\"alert alert-info\" role=\"alert\">".$GError."</div>";
					}
					?>	<font color="red"><?php  echo $r;?></font>
							<h4>Upload Prices</h4><input class="form-control"  type="file" name="csvs" id="userfile"  style="width:20%;height:10%"><br>
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

