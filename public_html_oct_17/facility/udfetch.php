<?php
session_start();
if((!isset($_SERVER['HTTPS'])) || ($_SERVER['HTTPS'] != "on")){
	$url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	header("Location: $url");
	exit;
}

if(!isset($_SESSION['lfdata']))
	header("Location: index.php");

$GError = "";
$pwd_change = false;
$pos = strpos($_SERVER['REQUEST_URI'],"settings.php");
if($pos === false){
	if(isset($_SESSION['lfdata']) && (trim($_SESSION['lfdata']['pwd']) == "excited123!"))
		header("Location: settings.php");
}
else {
	if(isset($_SESSION['lfdata']) && (trim($_SESSION['lfdata']['pwd']) == "excited123!")){
		$GError = "Please change your password.";
		$pwd_change = true;
	}
}

$pos = strpos($_SERVER['REQUEST_URI'],"profile.php");
if($pos === false){
	//if(isset($_SESSION['lfdata']) && (trim($_SESSION['lfdata']['phone']) == ""))
		//header("Location: profile.php");
}
else {
	if(isset($_SESSION['lfdata']) && (trim($_SESSION['lfdata']['phone']) == ""))
		$GError = "Please enter phone number.";
}

include('../sql.php');

if(isset($_POST['udfetch']) && isset($_POST['unit_id'])){
  $res_ud = mysqli_query($conn, "select UA.amenity as ud_amenity, UA.kind as ud_kind from unit_amenity UA, unit U where UA.unit_id=U.auto_id and UA.kind is not null and UA.kind in ('u_pdispc', 'u_pdismo', 'u_pdispcfm', 'u_pdispcfmfd', 'u_pdispcfd', 'u_pdismofd', 'u_pdismofm') and U.auto_id='".mysqli_real_escape_string($conn, $_POST['unit_id'])."'") or die("Error: " . mysqli_error($conn));
  
  $ud_arr = array();
  while($arrUD = mysqli_fetch_array($res_ud, MYSQLI_ASSOC)){
    $inner = array("kind" => $arrUD['ud_kind'],
                   "amenity" => $arrUD['ud_amenity']);
    $ud_arr[] = $inner;    
  }
  echo json_encode($ud_arr);
}

