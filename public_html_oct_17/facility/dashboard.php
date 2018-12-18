<?php
include('header.php');
$GInfo = "";

function getBaseUrl(){
    // output: /myproject/index.php
    $currentPath = $_SERVER['PHP_SELF']; 
    
    // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
    $pathInfo = pathinfo($currentPath); 
    
    // output: localhost
    $hostName = $_SERVER['HTTP_HOST']; 
    
    // output: http://
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';
    
    // return: http://localhost/myproject/
    return $protocol.$hostName.$pathInfo['dirname']."/";
}

if(isset($_POST['submit'])){
  $facility_id = $_SESSION['lfdata']['auto_id'].'_lf';
	if($_POST['submit'] == "Save"){
    $ts = time();
    $imageFileName = ""; 
	
  	$targetDir = "../images/".$facility_id."/";
	
  	$allowed_extensions = array(".jpg","jpeg",".png",".gif");
  	if(!empty(array_filter($_FILES['image']['name']))){
  	  $has_error = false;
  		foreach($_FILES['image']['name'] as $k => $v): 
			
  			$fileName = md5(time()).'_'.basename($_FILES['image']['name'][$k]);
  			$extension = substr($fileName,strlen($fileName)-4,strlen($fileName));

  			$targetFilePath = $targetDir . $fileName;
			
  			if(!in_array($extension,$allowed_extensions)){
  				echo "<script>alert('Invalid format(".$extension." in ".basename($_FILES['image']['name'][$k])."). Only jpg / jpeg/ png /gif format allowed');</script>";
  				$has_error = true;
  			}
  		endforeach;
		
  		if($has_error == false){
    		foreach($_FILES['image']['name'] as $k => $v): 
			
  	  		$fileName = md5(time()).'_'.basename($_FILES['image']['name'][$k]);
  	  		$extension = substr($fileName,strlen($fileName)-4,strlen($fileName));
			
  	  		$targetFilePath = $targetDir . $fileName;
			
  				if(move_uploaded_file($_FILES["image"]["tmp_name"][$k], $targetFilePath)){
  					$image_sql = "insert into image (url_fullsize, url_thumbsize, facility_id) values ('".$fileName."', '".$fileName."', '".$facility_id."')";

  					mysqli_query($conn, $image_sql) or die('Failed to insert facility image: '.mysqli_error($conn));
  				}
  		  endforeach;
  		}
    }
	}

	$location = '';

	if(isset($_POST['options']) && is_array($_POST['options']) ){
    ////
	}		
	if( isset($_POST['units']) && is_array($_POST['units']) ){
		foreach($_POST['units'] as $unit){
			////$units.= $unit."-".$_POST['unitval'.$unit].",";	
		}
  }

	$res = mysqli_query($conn,"select O.auto_id from facility_owner O, facility_master M where O.auto_id=M.id and M.id = '".$facility_id."' and O.emailid='".$_POST['emailid']."'");
	if(mysqli_num_rows($res) >=  1){
		if($_SESSION['lfdata']['emailid'] != $_POST['emailid'])
			$GInfo = "Info : Your Userid is ".$_POST['emailid']."<br>You will receive Reservation Confirmations on - ".$_POST['emailid'];
				
		$query = "update facility_master set phone=N'".$_POST['phone'].
								//"',emailid='".$_POST['emailid'].
								"',street=N'".mysqli_real_escape_string($conn,$_POST['address1']).
								"',region=N'".mysqli_real_escape_string($conn,$_POST['address2']).
								"',city=N'".$_POST['city'].
								"',state=N'".$_POST['state'].
								"',zip=N'".$_POST['zipcode'].
								"',lat='".$_POST['lat'].
								"',lng='".$_POST['lng'].
								"',reservationdays='".$_POST['reservationdays'].
								"',searchable='".(isset($_POST['searchable'])?0:1).
								"',receivereserve='".(isset($_POST['receivereserve'])?1:0).
								//"',options=',".$options.
								//"',units=',".$units.
								"',description ='".$_POST['desc'].
								"'";

		$query .= " where id='".$facility_id."'";
			
		mysqli_query($conn,$query) or die('Failed to update facility details. Please try again: '.mysqli_error($conn));
		
		$query = "update facility_owner set phone=N'".$_POST['phone'].
								"',emailid='".$_POST['emailid'].
								"'";
		$query .= " where emailid='".$_SESSION['lfdata']['emailid']."'";
		mysqli_query($conn,$query) or die('Failed to update email and phone. Please try again: '.mysqli_error($conn));
		$_SESSION['lfdata']['emailid'] = $_POST['emailid'];
		
	}
	else{
	  die('Failed to locate facility. Please register.');
	}
}

function generateReservationDays($days){
	$ret = '<select name="reservationdays" id="reservationdays" required class="form-control" style="margin-bottom:5px;">';
	$ret .= '<option value="">Honor Reservations</option>';
	for($i=1;$i<=10;$i++)
		$ret .= '<option '.($i==$days?"selected":"").'>'.$i.'</option>';
	$ret .= '</select>';
	return $ret;						
}

function generateState($state){
	$stateArr = array("AL","AK","AZ","AR","CA","CO","CT","DE","DC","FL","GA","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MA","MI","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VT","VA","WA","WV","WI","WY");
	$ret = '<select name="state" id="state" required class="form-control" style="margin-bottom:5px;">';
	$ret .= '<option value="">Select State</option>';
	for($i=0;$i<count($stateArr);$i++)
		$ret .= '<option '.($stateArr[$i]==$state?"selected":"").'>'.$stateArr[$i].'</option>';

	$ret .= '</select>';
	return $ret;						
}

function sanitize_amenities($facility_id){
  global $conn;
  
  $res = mysqli_query($conn,"select amenity from facility_amenity where facility_id='".$facility_id."'") or die('Failed to fetch facility amenities.');
  $ams = array();
	while($arr = mysqli_fetch_array($res, MYSQLI_ASSOC)){
	  $ams_pa = explode("|", $arr['amenity']);
	  $curr = '';
	  if(count($ams_pa) == 1){
	    $curr = $ams_pa[0];
	  }
	  else{
	    $curr = $ams_pa[1];
	  }
    $res_opts = mysqli_query($conn, "select option_id as oid from amenity_dictionary where equivalent = '".$curr."'") or die('Failed to match facility amenities.');
    while($arr = mysqli_fetch_array($res_opts, MYSQLI_ASSOC)){
      $ams[] = $arr['oid'];
    }
	}	
	return $ams;
}

function fetch_predefined_units(){
  global $conn;
  
  $unit_select_sql 		= "select units, id from units";
  $unit_select_result 	= mysqli_query($conn, $unit_select_sql);

  $size_arr = array(); // $matched_units[1];
  $id_arr = array(); // $matched_units[3];
  
  while($arr = mysqli_fetch_array($unit_select_result, MYSQLI_ASSOC)){
    $size_arr[] = $arr['units'];
    $id_arr[] = $arr['id'];
  }
  $ret = array($size_arr, $id_arr);
  return $ret;
}

function sanitize_units($facility_id){
  global $conn;
  
  $unit_select_sql 		= "select size, price, auto_id from unit where facility_id='".$facility_id."'";
  $unit_select_result 	= mysqli_query($conn, $unit_select_sql);

  $price_arr = array(); // $matched_units[0];
  $size_arr = array(); // $matched_units[1];
  $checked_arr = array(); //$matched_units[2];
  $id_arr = array(); // $matched_units[3];

  $predef_units = fetch_predefined_units();
  
  while($arr = mysqli_fetch_array($unit_select_result, MYSQLI_ASSOC)){
    $checked_arr[] = true;
    $size_arr[] = $arr['size'];
    $price_arr[] = $arr['price'];
    $id_arr[] = $arr['auto_id'];
  }
  
  for($i = 0; $i < count($predef_units[0]); $i++){
    if(in_array($predef_units[0][$i], $size_arr) == false){
      $size_arr[] = $predef_units[0][$i];
      $checked_arr[] = false;
      $id_arr[] = $predef_units[1][$i];
      $price_arr[] = '-';
    }
  }
  
  $ret = array($price_arr, $size_arr, $checked_arr, $id_arr);	  
  return $ret;
}

$res = mysqli_query($conn,"select O.auto_id as auto_id, O.pwd as pwd, M.id as facility_id, O.companyname as companyname, O.phone as phone, M.city as city, M.state as state, M.zip as zip, O.emailid as emailid, M.searchable as searchable, M.lat as lat, M.lng as lng, M.street as street, M.region as region, M.locality as locality, M.receivereserve as receivereserve, M.reservationdays as reservationdays, M.description as description from facility_owner O, facility_master M where O.auto_id=M.facility_owner_id and M.facility_owner_id is not null and O.auto_id ='".$_SESSION['lfdata']['auto_id']."'") or die("Error: " . mysqli_error($conn));

$arrF = mysqli_fetch_array($res,MYSQLI_ASSOC);

$_SESSION['lfdata'] = $arrF;

$facility_id = $arrF['facility_id'];

$image_select_sql 		= "select url_fullsize as path from image where facility_id='".$facility_id."'";
$image_select_result 	= mysqli_query($conn, $image_select_sql);

$facility_images = [];
if (mysqli_num_rows($image_select_result) > 0) {
	while($row = mysqli_fetch_assoc($image_select_result)) {
	   $facility_images[] = $row['path'];
	}
}
?>
<!--inner block start here-->
<div class="inner-block">
    	<div class="blankpage-main" style="padding:.5em .5em;">
    		<?php
    		if($GInfo != ""){
    			echo "<div class=\"alert alert-info\" role=\"alert\">".$GInfo."</div>";
    		}
    		?>
    		<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
    		<div class="col-md-4" style="border:0px solid #000;padding:0px 5px;margin:0;">
    				<h2 style="margin: 0;padding:0;"><?php echo $arrF['companyname'];?></h2>
    				<hr style="margin:5px 0px 5px 0px">
							
							<div id="myCarousel" class="carousel slide" data-ride="carousel">
							
								<?php if(count($facility_images) > 0): ?>
							  <!-- Wrapper for slides -->
							  <div class="carousel-inner">
								
								<?php foreach($facility_images as $key => $image): ?>
								<div class="item <?php if($key == 0) : ?> active <?php endif; ?>">
								  <img src="<?php echo $image; ?>" alt="" height="220">
								</div>
								<?php endforeach; ?>
								
							  </div>

							  <!-- Left and right controls -->
							  <a class="left carousel-control" href="#myCarousel" data-slide="prev">
								<span class="glyphicon glyphicon-chevron-left"></span>
								<span class="sr-only">Previous</span>
							  </a>
							  <a class="right carousel-control" href="#myCarousel" data-slide="next">
								<span class="glyphicon glyphicon-chevron-right"></span>
								<span class="sr-only">Next</span>
							  </a>
							  <?php else: ?>
							  
							  <img src="../unitimages/noimage.jpg" alt="" width="300" height="300">
							  
							  <?php endif; ?>
							</div>
    				<div class="fileUpload btn btn-primary"><span>Choose Image(s)</span><input type="file" class="upload" multiple name="image[]"/>

					</div>
					<center>
    				</center>
    				<input type="text" name="address1" id="address1" placeholder="Street, Locality" value="<?php echo $arrF['street'].', '.$arrF['locality'];?>" required="" class="form-control" style="margin-bottom:5px;margin-top:5px;" onchange="getLatLng()">
    				<input type="text" name="address2" id="address2" placeholder="Region" value="<?php echo $arrF['region'];?>" class="form-control" style="margin-bottom:5px;">
    				<div class="col-md-4" style="text-align:left;padding:0;margin:0;">
    				<input type="text" name="city" id="city" placeholder="City" value="<?php echo $arrF['city'];?>" required="" class="form-control" style="margin-bottom:5px;">
    				</div>
    				<div class="col-md-4" style="text-align:left;padding:0;margin:0;">
    				  <?php echo generateState($arrF['state']);?>
    				</div>
    				<div class="col-md-4" style="text-align:left;padding:0;margin:0;">
    					<input type="text" name="zipcode"  id="zipcode" placeholder="Zipcode" value="<?php echo $arrF['zip'];?>" required="" class="form-control" style="margin-bottom:5px;">
    				</div>
    				<input type="text" name="phone" placeholder="Phone" value="<?php echo $arrF['phone'];?>" required="" class="form-control" style="margin-bottom:5px;">
						<?php echo generateReservationDays($arrF['reservationdays']);?>
					Receive Reservations at <input type="text" name="emailid" placeholder="Email address to receive Reservation Confirmations" value="<?php echo $arrF['emailid'];?>"  class="form-control" style="margin-bottom:5px;width:50%;display:inline;"><br>
    				
			<input type="checkbox" name="searchable" style="margin-bottom:5px;display:inline;" <?php echo ($arrF['searchable']==0?"checked":"");?>> Make This Location Unsearchable<br /><br />
			    <div>
  			    <h4>Discounts</h4><br />
	  		    <div style="text-align:left;padding:0;margin:0;border: 1px solid black; border-radius: 7px">
	  		    <h5 style="text-align:center">Percentage Discounts</h5><br />
			      <input type="number" step = "1" pattern = "\d+" min="0" max = "100" name="pdispc" id="pdispc" placeholder="" value="" style="width:50px; margin-bottom:10px; margin-left: 10px"> <p style="display:inline;font-size:.9em;"> % OFF For </p>
			      <input type="number" step = "1" pattern = "\d+" min="0" max = "12" name="pdismo" id="pdismo" placeholder="" value="" style="width:50px; margin-bottom:10px; margin-left: 10px"> <p style="display:inline;font-size:.9em;"> Month(s) </p>
			      <br />
			      <hr style="margin:5px 0px 5px 0px; border-width: 1px; color: black">
			      <input type="number" step = "1" pattern = "\d+" min="0" max = "100" name="pdispcfm" id="pdispcfm" placeholder="" value="" style="width:50px; margin-bottom:10px; margin-left: 10px"> <p style="display:inline;font-size:.9em;"> % OFF First Month </p></div>
            <br />
	  		    <div style="text-align:left;padding:0;margin:0;border: 1px solid black; border-radius: 7px">
	  		    <h5 style="text-align:center">Fixed Value Discounts</h5><br />
			      <input type="number" step = "1" pattern = "\d+" min="0" max = "100" name="pdispcfmfd" id="pdispcfmfd" placeholder="" value="" style="width:50px; margin-bottom:10px; margin-left: 10px"><p style="display:inline;font-size:.9em;"> % OFF First Month </p>
			      <br />
			      <hr style="margin:5px 0px 5px 0px; border-width: 1px; color: black">
			      <input type="number" step = "1" pattern = "\d+" min="0" max = "100" name="pdispcfd" id="pdispcfd" placeholder="" value="" style="width:50px; margin-bottom:10px; margin-left: 10px"> <p style="display:inline;font-size:.9em;"> % OFF For </p>
			      <input type="number" step = "1" pattern = "\d+" min="0" max = "12" name="pdismofd" id="pdismofd" placeholder="" value="" style="width:50px; margin-bottom:10px; margin-left: 10px"><p style="display:inline;font-size:.9em;"> Month(s)</p></div>
            <br />
			      <div style="text-align:left;padding:0;margin:0;border: 1px solid black; border-radius: 7px">
			      <h5 style="text-align:center">Free Months</h5><br />
			      <input type="number" step = "1" pattern = "\d+" min="0" max = "12" name="pdismofm" id="pdismofm" placeholder="" value="" style="width:50px; margin-bottom:10px; margin-left: 10px"> <p style="display:inline;font-size:.9em;"> Month(s) free </p></div>
			    </div>

				</div>
    			<div class="col-md-4" style="text-align:left;padding:0px 5px;margin:0;">
    				<hr style="margin:5px 0px 5px 0px">
    				<b>Location</b>
    				<hr style="margin:5px 0px 5px 0px">
    				<div id="map" style="height:250px"></div>
    				<input type="hidden" name="lat" id="lat" value="<?php echo $arrF['lat']?>">
    				<input type="hidden" name="lng" id="lng" value="<?php echo $arrF['lng']?>">
					<hr style="margin:5px 0px 5px 0px">
					<b>Choose Features Offered</b>
					<hr style="margin:5px 0px 5px 0px">
					<?php
					$res_amenities = sanitize_amenities($facility_id);
					
					$res = mysqli_query($conn,"select * from options");
					while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
						$checked = "";
						if(in_array($arr['id'], $res_amenities) == true)
							$checked = "checked";
						echo '<input type="checkbox" name="options[]" id="option'.$arr['id'].'" value="'.$arr['id'].'" '.$checked.'> '.$arr['opt'].'<br>';
					}
					?>
					
    			</div>    		
				<div class="col-md-4" style="border:0px solid #000;padding:0px 5px;margin:0;">
    			<hr style="margin:5px 0px 5px 0px">
    			<b>Choose Your Products</b>
    			<hr style="margin:5px 0px 5px 0px">

          <?php
    			$matched_units = sanitize_units($facility_id);
    			$price_arr = $matched_units[0];
    		  $size_arr = $matched_units[1];
    		  $checked_arr = $matched_units[2];
    		  $id_arr = $matched_units[3];
    			for($i = 0; $i < count($matched_units[3]); $i++){
    			  $checked = '';
    				if($checked_arr[$i] == true){
    					$checked = "checked";
    				}
    			  echo '<div class="col-md-6" style="width:50%;float:left;border:0px solid #000;padding:0;margin:0;">';
      			echo '<div class="col-md-3" style="width:70%;float:left;border:0px solid #000;padding:0;margin:0;">';
	  				echo '<input type="checkbox" name="units[]" id="unit'.$id_arr[$i].'" value="'.$id_arr[$i].'" style="margin:5px;" '.$checked.'>';
						echo '<p style="display:inline;font-size:.9em;">'.$size_arr[$i]."</p>";
	  				echo '</div>';
	  				echo '<div class="col-md-3" style="width:30%;float:left;border:0px solid #000;padding:0;margin:0;">';
	  				echo '<input type="number" step="0.01" name="unitval'.$id_arr[$i].'" id="unitval'.$id_arr[$i].'" value="'.$price_arr[$i].'" class="form-control"  style="display:inline;width:90%;margin:2px;padding:2px;height:25px;">';
	  				echo '</div>';
	  				echo '</div>';
    			}
    			?>

    			</div>
    			<div class="clearfix"> </div>
				
    			<center>
    			<!-- 
    				<button class="btn btn-success" name="submit" value="Save" style="background:#68AE00;border-color:#68AE00;padding-left:60px;padding-right:60px;margin-top:5px;">Save</button>
    				-->
    			</center>
    			</form>
    			<div class="clearfix"> </div>
    	</div>
    </div>
</div>
<!--inner block end here-->
<?php
include('footer.php');
?>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&callback=initMap"  async defer></script>
<script type="text/javascript">
var map;
var marker;
<?php 
  echo 'var lat = "'.$arrF['lat'].'";';
  echo 'var lng = "'.$arrF['lng'].'";';
?>

function initMap(){
  map = new google.maps.Map(document.getElementById('map'), 
  {
    center: new google.maps.LatLng(lat,lng),
    zoom: 14
  });
  marker = new google.maps.Marker({
          position:  new google.maps.LatLng(lat,lng),
          map: map,
        });
   google.maps.event.addListener(map, 'click', function(event)
   {
   		document.getElementById("lat").value = parseFloat(event.latLng.lat()).toFixed(7);
	  	document.getElementById("lng").value = parseFloat(event.latLng.lng()).toFixed(7);
   		map.panTo(event.latLng);
	    marker.setPosition(event.latLng);
	    map.setCenter(event.latLng);
   });
}

function getLatLng(){
	var address = document.getElementById("address1").value;
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode( { 'address': address}, function(results, status) 
	{
		if (status == google.maps.GeocoderStatus.OK){
	  	document.getElementById("lat").value = parseFloat(results[0].geometry.location.lat()).toFixed(7);
	  	document.getElementById("lng").value = parseFloat(results[0].geometry.location.lng()).toFixed(7);
	    marker.setPosition(new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng()));
	    map.panTo(new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng()));
	    map.setCenter(new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng()));
	  } 
	}); 
}
</script>
<style>
.fileUpload 
{
    position: relative;
    overflow: hidden;
    height : 30px;
    width: 100%;
    outline: none;
		padding: 1em;
		margin-top : 5px;
		margin-bottom : 5px;
    background-color:#68AE00;
    border: none;
    border-radius:5px;
    line-height: 1px;
}
.fileUpload:hover
{
	background-color:#1d6e05;
}
.fileUpload input.upload 
{
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    padding: 0;
    cursor: pointer;
    opacity: 0;
    height : 30px;
    width:100%;
    filter: alpha(opacity=0);
}

</style>

