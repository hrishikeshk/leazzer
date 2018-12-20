<?php
session_start();
require_once('../mail/class.phpmailer.php');
include('../sql.php');
$GError = "";

function facility_exists($id){
  global $conn;
  $res = mysqli_query($conn, "SELECT * FROM facility_master WHERE id='" . $id. "'") OR die('Failed to check existing facility by id: '.$id);
	if(mysqli_num_rows($res)!=0){
		$arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
		return $arr;
	}
	else
	  return false;
}

function get_unit_id($id, $size, $price, $description, $promo, $price_freq){
  global $conn;
  $res = mysqli_query($conn, "SELECT * FROM unit WHERE facility_id='" . $id. "' and size='".$size."' and price='".$price."' and description='".$description."' and promo='".$promo."' and price_freq='".$price_freq."'") OR die('Failed to check inserted unit by facility id: '.$id.mysqli_error($conn));
	if(mysqli_num_rows($res)!=0){
		$arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
		return $arr['auto_id'];
	}
	else
	  return false;
}

function facility_delete($id){
  global $conn;
  $res = mysqli_query($conn, "delete FROM facility_master WHERE id='" . $id. "'") OR die('Failed to delete existing facility by id: '.$id.mysqli_error($conn));
}

function image_delete($id){
  global $conn;
  $res = mysqli_query($conn, "delete FROM image WHERE facility_id='" . $id. "'") OR die('Failed to delete existing image by id: '.$id.mysqli_error($conn));
}

function unit_delete($id){
  global $conn;
  $res = mysqli_query($conn, "delete FROM unit WHERE facility_id='" . $id. "'") OR die('Failed to delete existing unit by id: '.$id.mysqli_error($conn));
}

function review_delete($id){
  global $conn;
  $res = mysqli_query($conn, "delete FROM review WHERE facility_id='" . $id. "'") OR die('Failed to delete existing review by id: '.$id.mysqli_error($conn));
}

function unit_amenities_delete($id){
  global $conn;
    $res = mysqli_query($conn, "delete FROM unit_amenity WHERE unit_id in (select auto_id from unit where facility_id='".$id."')") OR die('Failed to delete existing unit amenities by id: '.$id.mysqli_error($conn));
}

function facility_amenities_delete($id){
  global $conn;
    $res = mysqli_query($conn, "delete FROM facility_amenity WHERE facility_id='" . $id. "'") OR die('Failed to delete existing facility amenities by id: '.$id.mysqli_error($conn));
}

function persist_facility_owner($email, $owner_id, $facility_name){
  global $conn;
  
  $res = mysqli_query($conn, "select max(auto_id) as max_auto FROM facility_owner") OR die('Failed to select max owner id: '.$owner_id.mysqli_error($conn));
  
  $arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
  
  $max_auto = $arr['max_auto'] + 1;
  
  $res_insert = mysqli_query($conn, "insert into facility_owner(pwd, emailid, logintype, companyname) values('excited123!','".$email.$max_auto."@leazzer.com','auto', '".$facility_name."')") OR die('Failed to insert facility owner: ' . $owner_id.mysqli_error($conn));
  
  return $max_auto;
}

function persist_reviews($id){
  global $conn;
  
  $num_reviews =$_POST['num_reviews'];
  if($num_reviews > 100){
    $num_reviews = 100;
  }
  
  for($i=0; $i < $num_reviews; $i++){
    $listing_avail_id = $_POST["review".$i."listing_avail_id"];
    $rating = $_POST["review".$i."rating"];
    $title = mysqli_real_escape_string($conn, $_POST["review".$i."title"]);
    $message = mysqli_real_escape_string($conn, $_POST["review".$i."message"]);
    $excerpt = mysqli_real_escape_string($conn, $_POST["review".$i."excerpt"]);
    $nickname = mysqli_real_escape_string($conn, $_POST["review".$i."nickname"]);
    $timestamp = mysqli_real_escape_string($conn, $_POST["review".$i."timestamp"]);
    $stars = mysqli_real_escape_string($conn, $_POST["review".$i."stars"]);
    
    $res = mysqli_query($conn, "insert into review(facility_id, listing_avail_id, rating, title, message, excerpt, nickname, timestamp, stars) values('".$id."','".$listing_avail_id."','".$rating."','".$title."','".$message."','".$excerpt."','".$nickname."','".$timestamp."','".$stars."')") OR die('Failed to insert facility review: ' . $id.mysqli_error($conn));
  }
}

function dict_mapping_exists($str){
  global $conn;
  $res = mysqli_query($conn, "SELECT option_id FROM amenity_dictionary WHERE equivalent='" . $str. "'") OR die('Failed to check existing amenity dictionary mapping: '.$str);
	if(mysqli_num_rows($res) > 0)
    return true;
	else
	  return false;
}

function email_about_missing_dict_mapping($facility_name, $missing_in_dict){
	$fromemail="no-reply@leazzer.com";
	$toemail= 'admin@leazzer.com';
	//$toemail= 'kv.hrishikesh@gmail.com';
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= '<center><img src="https://www.leazzer.com/admin/images/reg.png" height="150px" width="150px" alt="Logo" title="Logo" style="display:block"></center><br>';
	$message .= 'Hello Admin !<br />';
		
	$message .= '<br><br>A facility ('.$facility_name.') has been scraped and the following amenities are found missing from the amenity dictionary mappings -<br />';
  for($i = 0; $i < count($missing_in_dict); $i++){
    $message .= ($i + 1);
    $message .= '/ ';
    $message .= $missing_in_dict[$i];
    $message .= '<br />';
  }
  
	$message .= '<br />They may be added using mapping facility in Options page in admin portal.</td></tr>';
	$message .= '<tr><td><br><br>';
	$message .= 'Sincerely,<br>&mdash; Leazzer';
	$message .= '</td></tr>';
	$message .= '</table>';
	
	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->AddReplyTo($fromemail,"Leazzer"); 
	$mail->SetFrom($fromemail, "Leazzer");
	$mail->AddAddress($toemail, substr($toemail,0,strpos($toemail,"@")));
	$mail->Subject    = "Missing Facility Amenity Dictionary Mappings";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->MsgHTML($message);
	$mail->isHTML(true);
	$ret = $mail->Send();
}

function persist_facility_amenities($id, $name){
  global $conn;
  
  $num_facility_amenities =$_POST["num_amenities"];

  $missing_in_dict = array();
  for($i=0; $i < $num_facility_amenities; $i++){
    $amenity = mysqli_real_escape_string($conn, $_POST["facility".$i."amenity"]);
    
    $res = mysqli_query($conn, "insert into facility_amenity(facility_id, amenity) values('".$id."','".$amenity."')") OR die('Failed to insert facility amenity: ' . $id.mysqli_error($conn));
    if(dict_mapping_exists($amenity) == false)
      $missing_in_dict[] = $amenity;
  }
  if(count($missing_in_dict) > 0)
    email_about_missing_dict_mapping($name, $missing_in_dict);
}

function persist_image_paths($id){
  global $conn;
  
  $num_facility_images =$_POST["num_images"];
  if($num_facility_images > 100){
    $num_facility_images = 100;
  }

  for($i=0; $i < $num_facility_images; $i++){
    $full_url = mysqli_real_escape_string($conn, $_POST["facility".$i."image_full_url"]);
    $tn_url = mysqli_real_escape_string($conn, $_POST["facility".$i."image_tn_url"]);
    
    $res = mysqli_query($conn, "insert into image(facility_id, url_fullsize, url_thumbsize) values('".$id."','".$full_url."','".$tn_url."')") OR die('Failed to insert facility amenity: ' . $id.mysqli_error($conn));
  }
}

function persist_units($id){
  global $conn;
  
  $num_units =$_POST['num_units'];
  if($num_units > 100){
    $num_units = 100;
  }
  
  for($i=0; $i < $num_units; $i++){
    $size = mysqli_real_escape_string($conn, $_POST["unit".$i."size"]);
    $price = $_POST["unit".$i."price"];
    $description = mysqli_real_escape_string($conn, $_POST["unit".$i."description"]);
    $promo = mysqli_real_escape_string($conn, $_POST["unit".$i."promo"]);
    $price_freq = mysqli_real_escape_string($conn, $_POST["unit".$i."price_freq"]);
    
    $res = mysqli_query($conn, "insert into unit(facility_id, size, price, description, promo, price_freq) values('".$id."','".$size."','".$price."','".$description."','".$promo."','".$price_freq."')") OR die('Failed to insert unit for facility: ' . $id.mysqli_error($conn));
    
    $unit_id = get_unit_id($id, $size, $price, $description, $promo, $price_freq);
    persist_unit_amenities($id, $unit_id, $i);
  }
}

function persist_unit_amenities($id, $unit_id, $unit_iter){
  global $conn;
  
  $num_unit_amenities =$_POST["unit".$unit_iter."num_amenities"];
  if($num_unit_amenities > 100){
    $num_unit_amenities = 100;
  }

  for($i=0; $i < $num_unit_amenities; $i++){
    $amenity = mysqli_real_escape_string($conn, $_POST["unit".$unit_iter."_".$i."amenity"]);
    $res = mysqli_query($conn, "insert into unit_amenity(unit_id, amenity) values('".$unit_id."','".$amenity."')") OR die('Failed to insert unit amenity: ' . $unit_id.mysqli_error($conn));
  }
}

function file_get_contents_curl($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
  curl_setopt($ch, CURLOPT_URL, $url);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

function get_lat_lng_from_zip($zip){
  $url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&address=".$zip."&sensor=false";
	$result_string = file_get_contents_curl($url);
  $result = json_decode($result_string, true);
  
  $lat = $result['results'][0]['geometry']['location']['lat'];
  $lng = $result['results'][0]['geometry']['location']['lng'];
  if(is_numeric($lat) && is_numeric($lng))
    return array($lat, $lng);
  else
    return array(0.0, 0.0);
}

function handle_insert_update(){

  global $conn;
  
  $id = $_POST['id'];
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $about = mysqli_real_escape_string($conn, $_POST['about']);
  $url = mysqli_real_escape_string($conn, $_POST['url']);
  $distance = $_POST['distance'];
  $street = mysqli_real_escape_string($conn, $_POST['street']);
  $locality = mysqli_real_escape_string($conn, $_POST['locality']);
  $region = mysqli_real_escape_string($conn, $_POST['region']);
  
  $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
  $city = mysqli_real_escape_string($conn, $_POST['city']);
  $state = mysqli_real_escape_string($conn, $_POST['state']);
  $facility_promo = mysqli_real_escape_string($conn, $_POST['facility_promo']);
  
  $zip = mysqli_real_escape_string($conn, $_POST['zip']);
  $lat_lng_arr = array(0.0, 0.0);
  if(!empty($zip)){
    $lat_lng_arr = get_lat_lng_from_zip($zip);
  }

  $lowest_price = $_POST['lowest_price'];
  
  $owner_id = '0';
  $existing_facility = facility_exists($id);
  if($existing_facility != false){
    $owner_id = $existing_facility['facility_owner_id'];
    facility_amenities_delete($id);
    image_delete($id);
    review_delete($id);
    unit_amenities_delete($id);
    unit_delete($id);
    facility_delete($id);
  }
  if(!isset($owner_id) || $owner_id == '0'){
    $owner_id = persist_facility_owner($_POST['email'], $id."|".$name, $name);
  }
  
  $res = mysqli_query($conn, "insert into facility_master(id, facility_owner_id, title, description, url, distance, street, locality, region, zip, lowest_price, city, state, phone, facility_promo, lat, lng) values('".$id."','".$owner_id."','".$name."','".$about."','".$url."','".$distance."','".$street."','".$locality."','".$region."','".$zip."','".$lowest_price."','".$city."','".$state."','".$phone."','".$facility_promo."','".$lat_lng_arr[0]."','".$lat_lng_arr[1]."')") OR die('Failed to insert facility: ' . $id.mysqli_error($conn));
  
  persist_facility_amenities($id, $name);

  persist_image_paths($id);

  persist_reviews($id);

  persist_units($id);
  
  return facility_exists($id);
}

function upload_image($facility_id){
  $target_dir = $dirpath = realpath(dirname(getcwd())) . "/images/" . $facility_id . "/";
  if(!file_exists($target_dir)){
    mkdir($target_dir, 0755, false);
  }
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  if (!preg_match('/^(?:[a-z0-9_-]|\.(?!\.))+$/iD', $facility_id . basename($_FILES["fileToUpload"]["name"]))){
    die("Bad filename");
  }
  if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
  }

  if (file_exists($target_file)) {
    echo "File already exists. Overwriting ... ";
    unlink($target_file);
  }

  if ($_FILES["fileToUpload"]["size"] > 1024 * 1024) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
  }

  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
  }

  if ($uploadOk == 0){
    echo "Sorry, your file was not uploaded.";
    return 4;
  }
  else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        return $_FILES["fileToUpload"]["error"];
    }
    else{
        echo "Sorry, there was an error uploading your file.";
        return 4;
    }
  }
}

function validate_auth(){
  global $conn;
  $res = mysqli_query($conn,"SELECT * FROM admin WHERE username='".$_POST['username']."' and password='".$_POST['password']."'");
	if(mysqli_num_rows($res)!=0){
		$arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
		$GError = "Logged in successfully.";
		return 0;
	}
	else{
	  $GError = "Userid and/or Password may be incorrect";
	  return 1;
	}
}
if(isset($_POST['action']) && validate_auth() == 0){
	if($_POST['action'] == "insertupdate"){
    handle_insert_update();
	}
	else if($_POST['action'] == "uploadimage"){
    upload_image($_POST['facility_id']);
	}
	else{
    	header("Location: index.php");
	}
}
else{
    	header("Location: index.php");
}

mysqli_close($conn);
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Leazzer" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<script src="js/jquery-2.1.1.min.js"></script> 
<link href="css/font-awesome.css" rel="stylesheet"> 
<link href='fonts/fonts.css' rel='stylesheet' type='text/css'>
</head>
<body>	
<div class="login-page" style="min-height: 700px;background:none;">
    <div class="login-main">  	
			<div class="login-block">
				<center><h1>Leazzer Admin</h1></center>
				<hr>
				<?php 
				if($GError!="")
				{echo '<p style="color:#68AE00;">'.$GError.'</p>';}
				?>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
					<input type="text" name="username" placeholder="Username" required="">
					<input type="password" name="password" class="lock" placeholder="Password">
					<div class="forgot-top-grids">
						<!--<div class="forgot">
							<a href="forgot.php">Forgot password?</a>
						</div>-->
						<div class="clearfix"> </div>
					</div>
					<input type="submit" name="action" value="Login">
				</form>
				<h5><a href="../index.php">Go Back to Home</a></h5>
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
