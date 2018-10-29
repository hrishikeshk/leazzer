<?php
session_start();
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

function facility_delete($id){
  global $conn;
  $res = mysqli_query($conn, "delete FROM facility_master WHERE id='" . $id. "'") OR die('Failed to delete existing facility by id: '.$id);
}

function unit_delete($id){
  global $conn;
  $res = mysqli_query($conn, "delete FROM unit WHERE facility_id='" . $id. "'") OR die('Failed to delete existing unit by id: '.$id);
}

function review_delete($id){
  global $conn;
  $res = mysqli_query($conn, "delete FROM review WHERE facility_id='" . $id. "'") OR die('Failed to delete existing review by id: '.$id);
}

function unit_amenities_delete($id){
  global $conn;
    $res = mysqli_query($conn, "delete FROM unit_amenity WHERE unit_id in (select auto_id from unit where facility_id='".$id."')") OR die('Failed to delete existing unit amenities by id: '.$id);
}

function facility_amenities_delete($id){
  global $conn;
    $res = mysqli_query($conn, "delete FROM facility_amenity WHERE facility_id='" . $id. "'") OR die('Failed to delete existing facility amenities by id: '.$id);
}

function persist_reviews($id){
  global $conn;
  
  $num_reviews =$_POST['num_reviews'];
  if(num_reviews > 100){
    $num_reviews = 100;
  }
  
  for($i=0; $i < $num_reviews; $i++){
    $listing_avail_id = $_POST["review".$i."listing_avail_id"];
    $rating = $_POST["review".$i."rating"];
    $title = $_POST["review".$i."title"];
    $message = $_POST["review".$i."message"];
    $excerpt = $_POST["review".$i."excerpt"];
    $nickname = $_POST["review".$i."nickname"];
    $timestamp = $_POST["review".$i."timestamp"];
    $stars = $_POST["review".$i."stars"];
    
    $res = mysqli_query($conn, "insert into review(facility_id, listing_avail_id, rating, title, message, excerpt, nickname, timestamp, stars) values('".$id."','".$listing_avail_id."','".$rating."','".$title."','".$message."','".$excerpt."','".$nickname."','".$timestamp."','".$stars."'")) OR die('Failed to insert facility review: ' . $id);
  }
}

function persist_facility_amenities($id){
  global $conn;
  
  $num_facility_amenities =$_POST["facility".$id."num_amenities"];
  if(num_facility_amenities > 10){
    $num_facility_amenities = 10;
  }

  for($i=0; $i < $num_facility_amenities; $i++){
    $facility_id = $_POST[$id];
    $amenity = $_POST["facility".$i."amenity"];
    
    $res = mysqli_query($conn, "insert into facility_amenity(facility_id, amenity) values('".$id."','".$amenity."'")) OR die('Failed to insert facility amenity: ' . $id);
  }
}

function persist_units($id){
  global $conn;
  
  $num_units =$_POST['num_units'];
  if(num_units > 100){
    $num_units = 100;
  }
  
  for($i=0; $i < $num_units; $i++){
    $size = $_POST["unit".$i."size"];
    $price = $_POST["unit".$i."price"];
    $description = $_POST["unit".$i."description"];
    $promo = $_POST["unit".$i."promo"];
    $price_freq = $_POST["unit".$i."price_freq"];
    
    $res = mysqli_query($conn, "insert into unit(facility_id, size, price, description, promo, price_freq) values('".$id."','".$size."','".$price."','".$description."','".$promo."','".$price_freq."'")) OR die('Failed to insert unit for facility: ' . $id);
    
    $unit_id = get_unit_id($id, $size, $price, $description, $promo, $price_freq);
    persist_unit_amenities($id, $unit_id, $i);
  }
}

function persist_unit_amenities($id, $unit_id, $facility_iter){
  global $conn;
  
  $num_unit_amenities =$_POST["unit".$unit_id."num_amenities"];
  if(num_unit_amenities > 10){
    $num_unit_amenities = 10;
  }

  for($i=0; $i < $num_unit_amenities; $i++){
    $unit_id = $_POST[$unit_id];
    $amenity = $_POST["unit".$facility_iter.".".$i."amenity"];
    
    $res = mysqli_query($conn, "insert into unit_amenity(unit_id, amenity) values('".$unit_id."','".$amenity."'")) OR die('Failed to insert unit amenity: ' . $unit_id);
  }
}

function handle_insert_update(){

  global $conn;
  
  $id = $_POST['id'];
  $name = $_POST['name'];
  $about = $_POST['about'];
  $url = $_POST['url'];
  $distance = $_POST['distance'];
  $street = $_POST['street'];
  $locality = $_POST['locality'];
  $region = $_POST['region'];
  $zip = $_POST['zip'];
  $lowest_price = $_POST['lowest_price'];
  
  $existing_facility = facility_exists($id);
  if($existing_faciity != false){
    facility_amenities_delete($id);
    review_delete($id);
    unit_amenities_delete($id);
    unit_delete($id);
    facility_delete($id);
  }
  
  $res = mysqli_query($conn, "insert into facility_master(id, title, description, url, distance, street, locality, region, zip, lowest_price) values('".$id."','".$name."','".$about."','".$url."','".$distance."','".$street."','".$locality."','".$region."','".$zip."','".$lowest_price."'")) OR die('Failed to insert facility: ' . $id);
  
  persist_facility_amenities($id);

  persist_reviews($id);

  persist_units($id);
  
  return facility_exists($id);
}

function upload_image($facility_id){
  $target_dir = "../images/" . $facility_id . "/";
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
