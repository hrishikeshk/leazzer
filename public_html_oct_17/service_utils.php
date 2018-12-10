<?php
require_once('mail/class.phpmailer.php');
include('sql.php');

function insert_facility_owner($email){
  global $conn;
	mysqli_query($conn,"insert into facility_owner(emailid, pwd, logintype, companyname, phone, firstname, lastname) values ('".
												$email."','excited123!','auto','".
												mysqli_real_escape_string($conn,$_POST['company'])."','".
												mysqli_real_escape_string($conn,$_POST['phone'])."','".
												mysqli_real_escape_string($conn,$_POST['firstname'])."','".
												mysqli_real_escape_string($conn,$_POST['lastname'])."')");
	
	$res = mysqli_query($conn,"select auto_id from facility_owner where emailid='".$email."'");	
	if(mysqli_num_rows($res) > 0){
	  $arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
		return $arr['auto_id'];
	}
	else{
			return false;
	}
}

function extract_image_name($ss_path){
  // eg- //images.selfstorage.com/large-compress/108715518314b760ec6.jpg
  $lpos = strrpos($ss_path, "/");
  return trim(substr($ss_path, $lpos + 1));
}

function fetch_image_url($facility_id){
  global $conn;
  $res = mysqli_query($conn,"select * from image where facility_id='".$facility_id."' limit 1");
  if(mysqli_num_rows($res) > 0)
    return mysqli_fetch_array($res, MYSQLI_ASSOC);
  else
    return false;
}

function fetch_review_count($facility_id){
  global $conn;
  $res = mysqli_query($conn,"select count(*) as rct from review where facility_id='".$facility_id."'");
  $arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
  return $arr['rct'];
}

function calc_img_path($facility_id){
  $img_path_remote = fetch_image_url($facility_id);
  if($img_path_remote == FALSE)
    return '';
  return $img_path_remote;
}

function read_owner_data($resO, $facility_id){
  if(mysqli_num_rows($resO) > 0){
    $arrO = mysqli_fetch_array($resO, MYSQLI_ASSOC);
    return array($arrO['emailid'], $arrO['firstname'], $arrO['lastname']);
  }
  else{
    return array($facility_id.'@leazzer.com', $facility_id, $facility_id);
  }  
}

function has_climate_control($arr_amenities){
  for($i = 0; $i < count($arr_amenities); $i++){
    if(stristr($arr_amenities[$i], 'climate') !== FALSE)
      return true;
  }
  return false;
}

function has_priority_amenity($arr_amenities, $arr_search_any){
  for($i = 0; $i < count($arr_amenities); $i++){
    for($j = 0; $j < count($arr_search_any); $j++){
      if(stristr($arr_amenities[$i], $arr_search_any[$j]) !== FALSE)
        return true;
    }
  }
  return false;
}

function save_phone($cid, $phone){
  global $conn;
  mysqli_query($conn,"UPDATE customer set phone = '".$phone."' where id = '".$cid."'");
}

function onReserveAdminMail($facilityName, $facilityAddress, $facilityPhone, $ownerEmail, $ownerName, $userPhone, $unit, $price, $resFromDate, $resToDate, $phone){
	global $conn,$GError;
	$fromemail="no-reply@leazzer.com"; 
	$toemail= 'admin@leazzer.com';
	//$toemail= 'kv.hrishikesh@gmail.com';
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= '<center><img src="https://www.leazzer.com/images/reservation.png" height="150px" width="150px" alt="Logo" title="Logo" style="display:block"></center><br>';
	$message .= 'Hello Admin !<br />';
	
	$message .= '<b><u>Facility Name</u> - '.$facilityName.'<br />';
	$message .= '<b><u>Facility Address</u> - '.$facilityAddress.'<br />';
	$message .= '<u>Facility Phone Number</u> - '.$facilityPhone.'<br />';
	$message .= '<u>User Phone Number</u> - '.$userPhone.'<br /></b>';
	
	$message .= '<br><br>A '.$unit.' unit has been reserved from ';
	$message .= $resFromDate.' to '.$resToDate.' for the price of $'.$price.' per month.<br>';
	$message .= '</td></tr>';
	$message .= '<tr><td><br><br>';
	$message .= 'Sincerely,<br>&mdash; Leazzer';
	$message .= '</td></tr>';
	$message .= '</table>';
	
	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->AddReplyTo($fromemail,"Leazzer"); 
	$mail->SetFrom($fromemail, "Leazzer");
	$mail->AddAddress($toemail, substr($toemail,0,strpos($toemail,"@")));
	$mail->Subject    = "A reservation has been done";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->MsgHTML($message);
	$mail->isHTML(true);
	$ret = $mail->Send();
}

function onReserveCustomerMail($facility_id, $custEmail, $custName, $unit, $price, $companyName, $resFromDate, $resToDate, $image, $fAddress){
	global $conn,$GError;
	$fromemail="no-reply@leazzer.com"; 
	$toemail=$custEmail; 
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= 'Hello <b>'.$custName.'</b>,';
	$message .= '<br><br>Congratulations. A '.$unit.' unit reservation has been confirmed at '.$companyName.' for you at the price of $'.$price.' per month. You must move in between dates '.$resFromDate.' to '.$resToDate;
	
	if(strlen($image) > 0){
	  $image_name = extract_image_name($image);
	  $expected_path = "images/".$facility_id.'/'.$image_name;
	  if(strlen($image_name) > 0 && file_exists($expected_path))
			$message .= '<center><img src="https://www.leazzer.com/'.$expected_path.'" height="120px" width="125px" alt="uiLogo" title="uiLogo" style="display:block"></center><br>';
		else if(file_exists("unitimages/".$image))
			$message .= '<center><img src="https://www.leazzer.com/unitimages/'.$image.'" height="120px" width="125px" alt="uiLogo" title="uiLogo" style="display:block"></center><br>';
		else if(stristr($image, 'images.selfstorage.com') === FALSE)
		  $message .= '<center><img src="https://www.leazzer.com/unitimages/pna.jpg" height="120px" width="125px" alt="pnaLogo" title="pnaLogo" style="display:block"></center><br>';
		else
			$message .= '<center><img src="https:'.$image.'" height="120px" width="125px" alt="ssLogo" title="ssLogo" style="display:block"></center><br>';
	}
	else
		$message .= '<center><img src="https://www.leazzer.com/unitimages/pna.jpg" height="120px" width="125px" alt="npnaLogo" title="npnaLogo" style="display:block"></center><br>';
		
	$message .= $fAddress;
	$message .= '</td></tr>';
	$message .= '<tr><td><br><br>';
	$message .= 'Sincerely,<br>&mdash; Leazzer';
	$message .= '</td></tr>';
	$message .= '</table>';

	$mail = new PHPMailer(); // defaults to using php "mail()"
	$mail->CharSet = 'UTF-8';
	$mail->AddReplyTo($fromemail,"Leazzer"); 
	$mail->SetFrom($fromemail, "Leazzer");
	$mail->AddAddress($toemail, substr($toemail,0,strpos($toemail,"@")));
	$mail->Subject    = "Your reservation is confirmed";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->MsgHTML($message);
	$mail->isHTML(true);
	$ret = $mail->Send();
}

function onReserveOwnerMail($facilityName, $ownerEmail, $ownerName, $unit, $price, $resFromDate, $resToDate){
	global $conn,$GError;
	$fromemail="no-reply@leazzer.com"; 
	//$toemail=$ownerEmail;
	$toemail='owners@leazzer.com';
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= '<center><img src="https://www.leazzer.com/images/reservation.png" height="150px" width="125px" alt="Logo" title="Logo" style="display:block"></center><br>';
	$message .= 'Hello <b>'.$facilityName.'</b>,';
	$message .= '<br><br>A '.$unit.' unit has been reserved from ';
	$message .= $resFromDate.' to '.$resToDate.' for the price of $'.$price.' per month.<br>';
	$message .= '</td></tr>';
	$message .= '<tr><td><br><br>';
	$message .= 'Sincerely,<br>&mdash; Leazzer';
	$message .= '</td></tr>';
	$message .= '</table>';
	
	$mail = new PHPMailer(); // defaults to using php "mail()"
	$mail->CharSet = 'UTF-8';
	$mail->AddReplyTo($fromemail,"Leazzer"); 
	$mail->SetFrom($fromemail, "Leazzer");
	$mail->AddAddress($toemail, substr($toemail,0,strpos($toemail,"@")));
	$mail->Subject    = "Your have a reservation";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->MsgHTML($message);
	$mail->isHTML(true);
	$ret = $mail->Send();
}

function checkUnits($units){
	global $conn;
	$ret = "";
	$unitsArr = explode(",",$units);
	for($i=0;$i<count($unitsArr);$i++){
		if(trim($unitsArr[$i]) == "")
			continue;
		$unitArr = explode("-",$unitsArr[$i]);
		
		$pos = strpos($unitArr[1],"$");
		if($pos == 0)
			$unitArr[1] = substr($unitArr[1],1);
			
		$res = mysqli_query($conn,"select * from units where units='".mysqli_real_escape_string($conn,$unitArr[0])."'");	
		if(mysqli_num_rows($res) > 0){
			$arr = mysqli_fetch_array($res,MYSQLI_ASSOC);
			$ret .= $arr['id']."-".$unitArr[1].",";
		}
		else{
			mysqli_query($conn,"insert into units(units) values('".mysqli_real_escape_string($conn,$unitArr[0])."')");	
			$ret .= mysqli_insert_id($conn)."-".$unitArr[1].",";	
		}
	}
	return ",".$ret;
}

function checkEmail($email, $cnt){
	global $conn;
	$res = mysqli_query($conn,"select * from facility_owner where emailid='".$email.($cnt==0?"":$cnt)."@leazzer.com'");	
	if(mysqli_num_rows($res) > 0){
		$cnt++;
		return checkEmail($email,$cnt);
	}
	else{
			return $email.($cnt==0?"":$cnt)."@leazzer.com";
	}
}

function fetch_units($facility_id){
	global $conn;
	
	$resFU = mysqli_query($conn, "SELECT A.auto_id as id, A.size as size, A.price as price, B.images as img FROM unit A, units B where A.size=B.units and A.facility_id='".$facility_id."'");
  
  $unit_info_array = array();
  
	while($arrFU = mysqli_fetch_array($resFU, MYSQLI_ASSOC)){
		$unit_info_array[] = $arrFU;
	}
	
	return $unit_info_array;
}

function min_ints($a, $b){
  if($a < $b)
    return $a;
  return $b;
}

function no_climate_control($v){
  return !(stristr($v, 'climate') !== FALSE);
}

function a_filter($arr, $str){
  $res = array();
  for($i = 0; $i < count($arr); $i++){
    if(stristr($arr[$i], $str) !== FALSE){
    
    }
    else
      $res[] = $arr[$i];
  }
  return $res;
}

function popup_amenities($facility_id, $facility_unit_amenities){
  
  $arr_len = count($facility_unit_amenities);
  $inner_data = 'new Array(';
	for($i = 0; $i < $arr_len; $i++){
	  $amenity = $facility_unit_amenities[$i];
		$inner_data .= '\''.'  '.str_replace('"', '&quot;', $amenity).'<br />\',';
	}
	$inner_data .= '\'<br />\')';
	return $inner_data;
}

function show_amenities($facility_id, $facility_unit_amenities, $show_upfront, $facilityName){

  $facility_unit_amenities = a_filter($facility_unit_amenities, "climate");
  
  $arr_len = count($facility_unit_amenities);
  $review_count = fetch_review_count($facility_id);
  echo '<tr><td style="width:900px;padding-left:400px">';

	for($i = 0; $i < min_ints($show_upfront, $arr_len); $i++){
	  $amenity = $facility_unit_amenities[$i];
	  echo '<img src="images/gtick.png" style="vertical-align: left;width:10px;height:10px" />';
		echo '  '.$amenity.'<br />';
	}
	
	echo '<div id="unit_more_amenities'.$facility_id.'" style="display:none">';
	for($i = $show_upfront; $i < $arr_len; $i++){
	  $amenity = $facility_unit_amenities[$i];
	  echo '<img src="images/gtick.png" style="vertical-align: left;width:10px;height:10px;" />  '.$amenity.'<br />';
	}
	echo '</div>';
	if($review_count > 0){
	  echo '<img src="images/gtick.png" style="vertical-align: left;width:10px;height:10px" />';
	  echo '  <a href="reviews.php?facility_id='.$facility_id.'">Reviews</a></br>';
	}
	
	if($arr_len > $show_upfront){
	  //echo '<a id="switchMoreLess'.$facility_id.'" href="javascript:showMoreLessAmenities('.$facility_id.')" style="display:block"> &gt;&gt;</a>';
	  echo '<a href="javascript:popupMoreLessAmenities(\''.$facilityName.'\', '.popup_amenities($facility_id, $facility_unit_amenities).')" style="display:block">      &gt;&gt;</a>';
	}
	echo '</td></tr>';
}

function show_unit_detail($arrFU, $facility_id, $rdays){
  echo '<div class="col-md-1" style="width:9%;text-align:center;padding:10px;border:0px solid #000;box-shadow: 0px 0px 3px #888888;">';
	echo '<img src="unitimages/'.($arrFU['img']==""?"pna.jpg":$arrFU['img']).'" style="vertical-align: top;width:50px;height:50px">';
	echo '<p style="text-align:center;width:80px;display:inline-block;padding:0px 10px 0px 10px;margin:0;font-size:.8em;white-space: nowrap;"><b>'.$arrFU['size'].'</b><br>$'.$arrFU['price'].'</p>';
	$phone = 'unknown';
	if(isset($_SESSION['lcdata']['phone']))
	  $phone = $_SESSION['lcdata']['phone'];
	echo '<button type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;margin: 0 auto;border-radius: 3px;font-size: 1.0em;width:80%;display:inline;padding:0px;" onClick="onUnitClick(this,'.
										(isset($_SESSION['lcdata'])?$_SESSION['lcdata']['id']:"0").','.
										$facility_id.',\''.$rdays.'\',\''.
										urlencode($arrFU['size']).'\',\''.
										$arrFU['price'].'\',\''.$phone.'\');">Reserve</button></div>';
}

function show_units($facility_id, $arr_arr_FU, $show_upfront, $rdays){

  echo '<div id="unitstbl_'.$facility_id.'" style="width:100%">';
  $arr_len = count($arr_arr_FU);
	for($i = 0; $i < min_ints($arr_len, $show_upfront); $i++){
	  $arrFU = $arr_arr_FU[$i];
	  show_unit_detail($arrFU, $facility_id, $rdays);
	}
	echo '<div id="unit_more_show'.$facility_id.'" style="display:none">';
	for($i = $show_upfront; $i < $arr_len; $i++){
	  $arrFU = $arr_arr_FU[$i];
		show_unit_detail($arrFU, $facility_id, $rdays);
	}
	echo '</div>';
	
	echo "</div>";
	if($arr_len > $show_upfront){
	  echo '<p> <a id="switchMoreLessUnits'.$facility_id.'" href="javascript:showMoreLessUnits('.$facility_id.')" style="display:block"> &gt;&gt;</a></p>';
	}
}

function a_intersect($arr1, $arr2){
  $res = array();
  for($i = 0; $i < count($arr1); $i++){
    for($j = 0; $j < count($arr2); $j++){
      if($arr1[$i] == $arr2[$j])
        $res[] = $arr1[$i];
    }
  }
  return $res;
}

function a_merge($arr1, $arr2){
  $res = $arr2;
  for($i = 0; $i < count($arr1); $i++){
    $res[] = $arr1[$i];
  }
  return $res;
}

function a_unique($arr){
  $res = array();
  for($i = 0; $i < count($arr); $i++){
    $add = 1;
    for($j = $i + 1; $j < count($arr); $j++){
      if($arr[$i] == $arr[$j]){
        $add = 0;
        break;
      }
    }
    if($add == 1)
      $res[] = $arr[$i];
  }
  return $res;
}

function is_high_priority($amenity){
  if(stristr($amenity, 'climate') !== FALSE)
	  return 'climate';
	else if(stristr($amenity, 'security features|') !== FALSE)
	  return 'security';
	else if(stristr($amenity, 'discounts|') !== FALSE)
	  return 'discount';
	else if(stristr($amenity, 'administration|') !== FALSE)
	  return 'administration';
	else if(stristr($amenity, 'property coverage|') !== FALSE)
	  return 'property';
	return 'low';
}

function extract_amenity($amenity){
  $pos = stripos($amenity, "|");
  if($pos == FALSE)
    return $amenity;
  return substr($amenity, $pos + 1);
}

function arrange_priority($facility_unit_amenities){
  $high_pr = array();
  $low_pr = array();
  $climate_pr = array();
  $security_pr = array();
  $admin_pr = array();
  $discount_pr = array();
  $property_pr = array();
  
  for($i = 0; $i < count($facility_unit_amenities); $i++){
    $amenity = $facility_unit_amenities[$i];
    $classified_as = is_high_priority($amenity);
    if($classified_as == 'climate')
      $climate_pr[] = $amenity;
    else if($classified_as == 'security')
      $security_pr[] = substr($amenity, 18);
    else if($classified_as == 'discount')
      $discount_pr[] = substr($amenity, 10);
    else if($classified_as == 'administration')
      $admin_pr[] = substr($amenity, 15);
    else if($classified_as == 'property')
      $property_pr[] = substr($amenity, 18);
    else
      $low_pr[] = extract_amenity($amenity);
  }
  $high_pr = a_merge($climate_pr, $high_pr);
  $high_pr = a_merge($security_pr, $high_pr);
  $high_pr = a_merge($discount_pr, $high_pr);
  $high_pr = a_merge($admin_pr, $high_pr);
  $high_pr = a_merge($property_pr, $high_pr);
  
  //return a_merge($low_pr, $high_pr);
  return $high_pr;
}

function fetch_consolidate_amenities($facility_id, $unit_info_arr){
  global $conn;
  $resFA = mysqli_query($conn, "SELECT amenity as fac_amenity FROM facility_amenity where facility_id='".$facility_id."'");
  
  $amenities = array();
  while($arrFA = mysqli_fetch_array($resFA, MYSQLI_ASSOC)){
		$amenities[] = $arrFA['fac_amenity'];
	}
	
	$unit_amenities = array();
	for($i = 0; $i < count($unit_info_arr); $i++){
	  $unit_id = $unit_info_arr[$i]['id'];
	  
	  $resUA = mysqli_query($conn, "SELECT amenity as unit_amenity FROM unit_amenity where unit_id='".$unit_id."'");
	  if(mysqli_num_rows($resUA) == 0){
	    continue;
	  }
	  $unit_amenities_spec_arr = mysqli_fetch_array($resUA, MYSQLI_ASSOC);
	  $unit_amenities_spec = array($unit_amenities_spec_arr['unit_amenity']);
	  while($ua = mysqli_fetch_array($resUA, MYSQLI_ASSOC)){
	    $uas = $ua['unit_amenity'];
	    $unit_amenities_spec[] = $uas;
	    if(stristr($uas, 'climate') !== FALSE && strlen(trim($uas)) > 0)
	      $amenities[] = $uas;
	    else if(stristr($uas, 'security') !== FALSE && strlen(trim($uas)) > 0)
	      $amenities[] = $uas;
	    else if(stristr($uas, 'discount') !== FALSE && strlen(trim($uas)) > 0)
	      $amenities[] = $uas;
	    else if(stristr($uas, 'administration') !== FALSE && strlen(trim($uas)) > 0)
	      $amenities[] = $uas;
	    else if(stristr($uas, 'property') !== FALSE && strlen(trim($uas)) > 0)
	      $amenities[] = $uas;
	  }
	  if(count($unit_amenities) == 0){
      $unit_amenities = $unit_amenities_spec;	  
	  }
	  else{
	    $unit_amenities = a_intersect($unit_amenities, $unit_amenities_spec);
	  }
	}
	return a_unique(a_merge($unit_amenities, $amenities));
}

?>

<style>
.modalAmenities {
    display: none;
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.modal-amenity {
    background-color: #fefefe;
    padding: 20px;
    border: 1px solid #888;
    width: 60%; /* Could be more or less, depending on screen size */
    height: 60%;
    position: fixed;
    z-index: 1;
    left: 10%;
    top: 20%;
    animation-name: animatetop;
    animation-duration: 0.9s;
}

.amenity-container {
  /*max-width: 80%;
  max-height: 80%;*/
  width: 40%;
  height: 70%;
  position: fixed;
  margin: auto;
  
  /*margin-top:0;
  margin-right:0;*/
}

.modal {
    display: none;
    /*position: fixed;*/
    /*z-index: 1;
    left: 0;
    top: 0;*/
    /*width: 100%; /* Full width */
    /*height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.modal-content {
    background-color: #fefefe;
    /*margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 60%; /* Could be more or less, depending on screen size */
    position: sticky;
    z-index: 1;
    left: 10%;
    top: 20%;
    animation-name: animatetop;
    animation-duration: 0.9s;
}

.close,
.close-amenity {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.trynow.hover,
.close:hover,
.close:focus,
.close-amenity:hover {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* slideshowing */

* {box-sizing: border-box}
  .mySlides {display: none}
  img {
    vertical-align: middle;
  }

.slideshow-container {
  /*max-width: 80%;
  max-height: 80%;*/
  width: 70%;
  height: 80%;
  position: sticky;
  margin: auto;
  
  /*margin-top:0;
  margin-right:0;*/
}

.prev, .next {
  cursor: pointer;
  position: relative;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -22px;
  color: black;
  font-weight: bold;
  font-size: 18px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
}

.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}
 
.slidertext {
  color: #000000;
  font-size: 13px;
  padding: 8px 12px;
  position: relative;
  top: 5px;
  /*bottom: 8px;*/
  width: 100%;
  text-align: center;
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: relative;
  top: 0;
}

/* The dots/bullets/indicators */
.dot {
  cursor: pointer;
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.6s ease;
}

.active, .dot:hover {
  background-color: #717171;
}

/* Fading animation */
.fade {
  -webkit-animation-name: fade;
  -webkit-animation-duration: 1.5s;
  animation-name: fade;
  animation-duration: 1.5s;
}

@-webkit-keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

@keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

@keyframes animatetop {
    from {top: 0%; opacity: 0}
    to {top: 20%; opacity: 1}
}

/* On smaller screens, decrease text size */
@media only screen and (max-width: 300px) {
  .prev, .next,.text {font-size: 11px}
}
</style>

<script type="text/javascript">
  var slideIndex = 1;
  function showMoreLessAmenities(facility_id){
    var y = document.getElementById("unit_more_amenities" + facility_id);
    var a_moreless = document.getElementById("switchMoreLess" + facility_id);
    if(y.style.display == "none"){
      y.style.display = "block";
      a_moreless.innerHTML = "&lt;&lt; ";
    }
    else{
      y.style.display = "none";
      a_moreless.innerHTML = " &gt;&gt;";
    }
  }
  
  function popupMoreLessAmenities(facilityName, amenitiesArr){
    setTimeout(function(){
    var y = document.getElementById("modalAmenities");
      var ac = document.getElementById("amenity-container");
      var heading = '<h5 style="text-align: center">Avail these amenities at our excellent facility</h5><br /><h4 style="text-align: center"><b>' + facilityName + '</b></h4><br /><br />';
      var tbl_start = '<table><tr><td>';
      var tbl_end = '</td></tr></table>';
      var img = '<img src="images/gtick.png" style="vertical-align: left;width:10px;height:10px" />';
      var data = '';
      for(i = 0; i < amenitiesArr.length - 1; ++i){
        data += img;
        data += amenitiesArr[i];
      }
      ac.innerHTML = (heading + tbl_start + data + tbl_end);
      y.style.display = "block";
    }, 1000);
  }
  
  function showMoreLessUnits(facility_id){
    var y = document.getElementById("unit_more_show" + facility_id);
    var a_moreless = document.getElementById("switchMoreLessUnits" + facility_id);
    if(y.style.display == "none"){
      y.style.display = "block";
      a_moreless.innerHTML = "&lt;&lt; ";
    }
    else{
      y.style.display = "none";
      a_moreless.innerHTML = " &gt;&gt;";
    }
  }
  
  function ajaxcall_photos(datastring){
    var res;
    $.ajax
    ({	
    		type:"GET",
    		url:"tp_n.php",
    		data:datastring,
    		cache:false,
    		async:false,
    		success: function(result){		
   				 	res=result;
   				 	var modal = document.getElementById('modalPhotos');
   				 	modal.style.display = "block";
   				 	var iht = document.getElementById('innerHTMLTarget');
   				 	iht.innerHTML = res;
            showSlides(slideIndex);
            setTimeout(function() {
              $(document.body).scrollTop(0);
            }, 15);
   		 	}
    });
    return res;
  }
  
  var span = document.getElementById('modal-close');
  span.onclick = function() {
    var modal = document.getElementById('modalPhotos');
    modal.style.display = "none";
  }
  
  var span_amenity = document.getElementById('close-amenity');
  span_amenity.onclick = function() {
    var modal = document.getElementById('modalAmenities');
    modal.style.display = "none";
  }
  
  window.onclick = function(event) {
    var modal = document.getElementById('modalPhotos');
    if (event.target == modal) {
        modal.style.display = "none";
    }
    var modalAmenities = document.getElementById('modalAmenities');
    if (event.target == modalAmenities) {
        modalAmenities.style.display = "none";
    }
  }
  
  var trynow = document.getElementById('trynow');
  trynow.onclick = function() {
    var modal = document.getElementById('modalPhotos');
    modal.style.display = "none";
  }
  
  function showMorePhotos(facility_id){
    var res = ajaxcall_photos("facility_id="+facility_id);
  }
  
  /* slideshowing */
function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  //var dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}

  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
  }
  //for (i = 0; i < dots.length; i++) {
    //  dots[i].className = dots[i].className.replace(" active", "");
  //}
  slides[slideIndex-1].style.display = "block";  
  //dots[slideIndex-1].className += " active";
}

</script>

<div id="modalPhotos" class="modal">
  <div class="modal-content">
    <span class="close" id="modal-close">&times;</span>
    <div class="slideshow-container">
    <table><tr>
    <td><a class="prev" onclick="plusSlides(-1)">&#10094;</a></td>
    <td><div id="innerHTMLTarget">Some text in the Modal..</div>
    </td>
    <td><a class="next" onclick="plusSlides(1)">&#10095;</a></td>
    </tr>
    <tr>
    <td></td>
    <td style="text-align:center"><a class="trynow" style="cursor:pointer" id="trynow">Try Now<a></td>
    <td></td>
    </tr>
    </table>
    </div>
  </div>

</div>

<div id="modalAmenities" class="modalAmenities">
  <div class="modal-amenity">
    <span class="close-amenity" id="close-amenity">&times;</span>
    <div id="amenity-container" class="amenity-container">
    
    </div>
  </div>
</div>

