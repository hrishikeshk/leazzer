<?php
require_once('mail/class.phpmailer.php');
include('sql.php');

function insert_facility_owner($email){
  global $conn;
	mysqli_query($conn,"insert into facility_owner(emailid, pwd, logintype, companyname, phone, firstname, lastname) values ('".
												mysqli_real_escape_string($conn, $email)."','excited123!','auto','".
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
  return htmlspecialchars(trim(substr($ss_path, $lpos + 1)), ENT_QUOTES);
}

function fetch_image_url($facility_id){
  global $conn;
  $res = mysqli_query($conn,"select * from image where facility_id='".mysqli_real_escape_string($conn, $facility_id)."' limit 1");
  if(mysqli_num_rows($res) > 0)
    return mysqli_fetch_array($res, MYSQLI_ASSOC);
  else
    return false;
}

function fetch_review_count($facility_id){
  global $conn;
  $res = mysqli_query($conn,"select count(*) as rct from review where facility_id='".mysqli_real_escape_string($conn, $facility_id)."'");
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
  mysqli_query($conn,"UPDATE customer set phone = '".mysqli_real_escape_string($conn, $phone)."' where id = '".mysqli_real_escape_string($conn, $cid)."'");
}

function onReserveAdminMail($facilityName, $facilityAddress, $facilityPhone, $ownerEmail, $ownerName, $userPhone, $unit, $price, $resFromDate, $resToDate, $phone){
	global $conn,$GError;
	$fromemail="no-reply@leazzer.com";
	$toemail= 'admin@leazzer.com';
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= '<center><img src="https://www.leazzer.com/images/reservation.png" height="150px" width="150px" alt="Logo" title="Logo" style="display:block"></center><br>';
	$message .= 'Hello Admin !<br />';

	$message .= '<b><u>Facility Name</u> - '.htmlspecialchars($facilityName, ENT_QUOTES).'<br />';
	$message .= '<b><u>Facility Address</u> - '.htmlspecialchars($facilityAddress, ENT_QUOTES).'<br />';
	$message .= '<u>Facility Phone Number</u> - '.htmlspecialchars($facilityPhone, ENT_QUOTES).'<br />';
	$message .= '<u>User Phone Number</u> - '.htmlspecialchars($userPhone, ENT_QUOTES).'<br /></b>';
	
	$message .= '<br><br>A '.htmlspecialchars($unit).' unit has been reserved from ';
	$message .= htmlspecialchars($resFromDate).' to '.htmlspecialchars($resToDate).' for the price of $'.htmlspecialchars($price, ENT_QUOTES).' per month.<br>';
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
	$toemail = $custEmail; 
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= 'Dear <b>'.$custName.'</b>,';
	{
	  //$expected_path = "images/gwc.png";
	  $expected_path = "images/emsurvey.png";
	  $message .= '<center><img src="https://www.leazzer.com/'.$expected_path.'" height="175px" width="240px" alt="Reservation Done" title="uiLogo" style="display:block"></center><br />';
	}
	$message .= '<br><br>Congratulations. A '.htmlspecialchars($unit).' unit reservation has been confirmed at '.htmlspecialchars($companyName, ENT_QUOTES).' for you at the price of $'.htmlspecialchars($price, ENT_QUOTES).' per month. You must move in between dates '.htmlspecialchars($resFromDate).' to '.htmlspecialchars($resToDate);
	
	if(strlen($image) > 0){
	  $image_name = extract_image_name($image);
	  $expected_path = "images/".$facility_id.'/'.$image_name;
	  if(strlen($image_name) > 0 && file_exists($expected_path))
			$message .= '<center><img src="https://www.leazzer.com/'.$expected_path.'" height="180px" width="200px" alt="Reservation Done" title="uiLogo" style="display:block"></center><br />';
		else if(file_exists("unitimages/".$image))
			$message .= '<center><img src="https://www.leazzer.com/unitimages/'.$image.'" height="180px" width="200px" alt="uiLogo" title="uiLogo" style="display:block"></center><br />';
		else if(stristr($image, 'images.selfstorage.com') === FALSE)
		  $message .= '<center><img src="https://www.leazzer.com/unitimages/pna.jpg" height="180px" width="200px" alt="pnaLogo" title="pnaLogo" style="display:block"></center><br />';
		else
			$message .= '<center><img src="https:'.$image.'" height="180px" width="200px" alt="ssLogo" title="ssLogo" style="display:block"></center><br />';
	}
	else
		$message .= '<center><img src="https://www.leazzer.com/unitimages/pna.jpg" height="180px" width="200px" alt="npnaLogo" title="npnaLogo" style="display:block"></center><br>';

	$message .= htmlspecialchars($fAddress, ENT_QUOTES);
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
	$mail->Subject    = "Your reservation is confirmed";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->MsgHTML($message);
	$mail->isHTML(true);
	$ret = $mail->Send();
}

function onReserveOwnerMail($facilityName, $ownerEmail, $ownerName, $unit, $price, $resFromDate, $resToDate, $customerName){
	global $conn,$GError;
	$fromemail="no-reply@leazzer.com"; 
	//$toemail=$ownerEmail;
	$toemail='owners@leazzer.com';
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	//$message .= '<center><img src="https://www.leazzer.com/images/gwo.png" height="150px" width="175px" alt="Logo" title="Logo" style="display:block"></center><br>';
	$message .= '<center><img src="https://www.leazzer.com/images/emsurvey.png" height="175px" width="240px" alt="Logo" title="Logo" style="display:block"></center><br>';
	$message .= 'Hello <b>'.htmlspecialchars($facilityName, ENT_QUOTES).'</b>,';
	$message .= '<br><br>A '.htmlspecialchars($unit).' unit has been reserved by '.htmlspecialchars($customerName, ENT_QUOTES).' from ';
	$message .= htmlspecialchars($resFromDate).' to '.htmlspecialchars($resToDate).' for the price of $'.htmlspecialchars($price, ENT_QUOTES).' per month.<br>';
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
	$res = mysqli_query($conn,"select * from facility_owner where emailid='".mysqli_real_escape_string($conn, $email).($cnt==0?"":$cnt)."@leazzer.com'");	
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
	
	$resFU = mysqli_query($conn, "SELECT A.auto_id as id, A.size as size, A.price as price, B.images as img FROM unit A, units B where A.size=B.units and A.facility_id='".mysqli_real_escape_string($conn, $facility_id)."'");
  
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
	  $amenity = htmlspecialchars($facility_unit_amenities[$i], ENT_QUOTES);
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
  $min_num = min_ints($show_upfront, $arr_len);
  if($min_num > 0){
    echo '<div style="text-align:left;font-weight:bold">Highlights</div>';
  }
	for($i = 0; $i < $min_num; $i++){
	  $amenity_w_g = explode("|", $facility_unit_amenities[$i]);
    $amenity = htmlspecialchars($amenity_w_g[1], ENT_QUOTES);
	  echo '<img src="images/gtick.png" style="vertical-align: left;width:10px;height:10px" />';
		echo '  '.$amenity.'<br />';
	}

	echo '<div id="unit_more_amenities'.$facility_id.'" style="display:none">';
	for($i = $show_upfront; $i < $arr_len; $i++){
	  $amenity_w_g = explode("|", $facility_unit_amenities[$i]);
    $amenity = htmlspecialchars($amenity_w_g[1], ENT_QUOTES);
	  echo '<img src="images/gtick.png" style="vertical-align: left;width:10px;height:10px;" />  '.$amenity.'<br />';
	}
	echo '</div>';
	if($review_count > 0){
	  echo '<img src="images/gtick.png" style="vertical-align: left;width:10px;height:10px" />';
	  echo '  <a href="reviews.php?facility_id='.$facility_id.'">Reviews</a></br>';
	}
	
	if($arr_len > $show_upfront){
	  //echo '<a id="switchMoreLess'.$facility_id.'" href="javascript:showMoreLessAmenities('.$facility_id.')" style="display:block"> &gt;&gt;</a>';
	  echo '<a href="javascript:popupMoreLessAmenities(\''.htmlspecialchars($facilityName, ENT_QUOTES).'\', '.popup_amenities($facility_id, $facility_unit_amenities).')" style="display:block">      &gt;&gt;</a>';
	}
	echo '</td></tr>';
}

function has_unit_priority_amenities($ua_arr, $uid){
  $ret = false;
  for($i = 0; $i < count($ua_arr); $i++){
    $hda = explode("~", $ua_arr[$i]);
    if($hda[0] == $uid)
      return true;
  }
  return $ret;
}

function get_unit_priority_amenities($ua_arr, $uid){
  $ret = array();
  if(array_key_exists($uid, $ua_arr) == false)
    return $ret;
  $inner_a = $ua_arr[$uid];
  
  return ud_construct_addl_discounts($inner_a);
}

function show_unit_detail($arrFU, $facility_id, $rdays, $ua, $facilityName, $facility_unit_amenities){
  echo '<div class="col-md-2" style="width:10%;text-align:center;margin-bottom: 5px">';
	echo '<img src="unitimages/'.($arrFU['img']==""?"pna.jpg":htmlspecialchars($arrFU['img'], ENT_QUOTES)).'" style="vertical-align: top;width:50px;height:50px">';
	echo '<p style="text-align:center;width:80%;display:inline-block;margin:0;font-size:.8em;white-space: nowrap;"><b>'.htmlspecialchars($arrFU['size']).'</b><br>$'.htmlspecialchars($arrFU['price'], ENT_QUOTES).'</p>';
	$show_fire = 'visibility:hidden';
	if(count($ua) > 0){
	  $show_fire = 'visibility:visible';
	}
	echo '<br /><div style='.$show_fire.'><div class="blink-image" style="margin-bottom:10px;"><img src="images/fire.png" style="width:20px;height:20px;visibility:inherit" /></div><div style="visibility:inherit">'.$ua[0].'</div></div>';
	////echo '<div class="blink-image" style="margin-bottom:10px"><a href="javascript:popupMoreLessAmenities(\''.htmlspecialchars($facilityName, ENT_QUOTES).'\', '.popup_amenities($facility_id, $facility_unit_amenities).')" style="'.$show_fire.'"><img src="images/fire.png" title="Click For Unit Amenities !" style="width:20px;height:20px;visibility:inherit"></a></div>';

	$phone = 'unknown';
	if(isset($_SESSION['lcdata']['phone']))
	  $phone = htmlspecialchars($_SESSION['lcdata']['phone'], ENT_QUOTES);
	echo '<button type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;margin: 0 auto;border-radius: 3px;font-size: 1.0em;display:inline;padding:4px;" onClick="onUnitClick(this,'.
										(isset($_SESSION['lcdata'])?$_SESSION['lcdata']['id']:"0").','.
										$facility_id.',\''.htmlspecialchars($rdays, ENT_QUOTES).'\',\''.
										urlencode(htmlspecialchars($arrFU['size'])).'\',\''.
										htmlspecialchars($arrFU['price'], ENT_QUOTES).'\',\''.$phone.'\');">Reserve</button></div>';
}

function show_units($facility_id, $arr_arr_FU, $show_upfront, $rdays, $ua_arr, $facilityName, $facility_unit_amenities){

  echo '<div id="unitstbl_'.$facility_id.'" style="width:100%">';
  $arr_len = count($arr_arr_FU);
	for($i = 0; $i < min_ints($arr_len, $show_upfront); $i++){
	  $arrFU = $arr_arr_FU[$i];
	  show_unit_detail($arrFU, $facility_id, $rdays, get_unit_priority_amenities($ua_arr, $arrFU['id']), $facilityName, $facility_unit_amenities);
	}
	echo '<div id="unit_more_show'.$facility_id.'" style="display:none">';
	for($i = $show_upfront; $i < $arr_len; $i++){
	  $arrFU = $arr_arr_FU[$i];
		show_unit_detail($arrFU, $facility_id, $rdays, get_unit_priority_amenities($ua_arr, $arrFU['id']), $facilityName,  $facility_unit_amenities);
	}
	echo '</div>';
	
	echo "</div>";
	if($arr_len > $show_upfront){
	  echo '<p> <a id="switchMoreLessUnits'.$facility_id.'" href="javascript:showMoreLessUnits('.$facility_id.')" style="display:block"> &gt;&gt;</a></p>';
	}
}

function fetch_priority_unit_amenities($facility_id, $unit_info_arr){
  global $conn;
	$unit_amenities = array();
	if(count($unit_info_arr) == 0)
	  return $unit_amenities;
	$unit_str = '('.$unit_info_arr[0]['id'];
	for($i = 1; $i < count($unit_info_arr); $i++){
	  $unit_str .= ', '.$unit_info_arr[$i]['id'];
	}
	$unit_str .= ')';
	$query_str = "SELECT amenity as ua, unit_id as uid, kind FROM unit_amenity where kind is not null and kind in ('u_pdispc', 'u_pdismo', 'u_pdispcfm', 'u_pdispcfmfd', 'u_pdispcfd', 'u_pdismofd', 'u_pdismofm') and unit_id in ".mysqli_real_escape_string($conn, $unit_str);
	
  $resUA = mysqli_query($conn, $query_str);
  if(mysqli_num_rows($resUA) == 0)
	  return $unit_amenities;

	while($ua = mysqli_fetch_array($resUA, MYSQLI_ASSOC)){
	  //$unit_amenities[] = $ua['uid'].'~Other|'.$ua['ua'];
	  if(array_key_exists($ua['uid'], $unit_amenities) == false)
	    $unit_amenities[$ua['uid']] = array();
	  $unit_amenities[$ua['uid']][$ua['kind']] = $ua['ua'];
	}
	return $unit_amenities;
}

function ud_construct_addl_discounts($arrFM){
  $ret = array();
  //foreach ($arrFM as $key => $value) {
    //error_log( "Key: $key; Value: $value\n" );
  //}
  $pdispc = $arrFM['u_pdispc'];
  $pdismo = $arrFM['u_pdismo'];
  if(isset($pdispc) && $pdispc > 0 && isset($pdismo) && $pdismo > 0)
    $ret[] = $pdispc.' % OFF For '.$pdismo.' Month(s)';
  
  $pdispcfm = $arrFM['u_pdispcfm'];
  if(isset($pdispcfm) && $pdispcfm > 0)
    $ret[] = $pdispcfm.' % OFF First Month';
    
  $pdispcfmfd = $arrFM['u_pdispcfmfd'];
  if(isset($pdispcfmfd) && $pdispcfmfd > 0)
    $ret[] = $pdispcfmfd.' OFF First Month';
    
  $pdispcfd = $arrFM['u_pdispcfd'];
  $pdismofd = $arrFM['u_pdismofd'];
  if(isset($pdispcfd) && $pdispcfd > 0 && isset($pdismofd) && $pdismofd > 0)
    $ret[] = $pdispcfd.' OFF For '.$pdismofd.' Month(s)';
    
  $pdismofm = $arrFM['u_pdismofm'];
  if(isset($pdismofm) && $pdismofm > 0)
    $ret[] = $pdismofm.' OFF First Month';
    
  return $ret;
}

function construct_addl_discounts($arrFM){
  $ret = array();
  //foreach ($arrFM as $key => $value) {
    //error_log( "Key: $key; Value: $value\n" );
  //}
  $pdispc = $arrFM['pdispc'];
  $pdismo = $arrFM['pdismo'];
  if(isset($pdispc) && $pdispc > 0 && isset($pdismo) && $pdismo > 0)
    $ret[] = 'Discounts|'.$pdispc.' % OFF For '.$pdismo.' Month(s)';
  
  $pdispcfm = $arrFM['pdispcfm'];
  if(isset($pdispcfm) && $pdispcfm > 0)
    $ret[] = 'Discounts|'.$pdispcfm.' % OFF First Month';
    
  $pdispcfmfd = $arrFM['pdispcfmfd'];
  if(isset($pdispcfmfd) && $pdispcfmfd > 0)
    $ret[] = 'Discounts|$'.$pdispcfmfd.' OFF First Month';
    
  $pdispcfd = $arrFM['pdispcfd'];
  $pdismofd = $arrFM['pdismofd'];
  if(isset($pdispcfd) && $pdispcfd > 0 && isset($pdismofd) && $pdismofd > 0)
    $ret[] = 'Discounts|$'.$pdispcfd.' OFF For '.$pdismofd.' Month(s)';
    
  $pdismofm = $arrFM['pdismofm'];
  if(isset($pdismofm) && $pdismofm > 0)
    $ret[] = 'Discounts|$'.$pdismofm.' OFF First Month';
    
  return $ret;
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

function arrange_priority_with_group($facility_unit_amenities){
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
      $security_pr[] = $amenity;
    else if($classified_as == 'discount')
      $discount_pr[] = $amenity;
    else if($classified_as == 'administration')
      $admin_pr[] = $amenity;
    else if($classified_as == 'property')
      $property_pr[] = $amenity;
    else
      $low_pr[] = $amenity;
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
  $resFA = mysqli_query($conn, "SELECT amenity as fac_amenity FROM facility_amenity where facility_id='".mysqli_real_escape_string($conn,$facility_id)."'");
  
  $amenities = array();
  while($arrFA = mysqli_fetch_array($resFA, MYSQLI_ASSOC)){
		$amenities[] = $arrFA['fac_amenity'];
	}
	
	$unit_amenities = array();
	for($i = 0; $i < count($unit_info_arr); $i++){
	  $unit_id = $unit_info_arr[$i]['id'];
	  
	  $resUA = mysqli_query($conn, "SELECT amenity as unit_amenity FROM unit_amenity where unit_id='".mysqli_real_escape_string($conn,$unit_id)."'");
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

function get23($arr_ua){
  $ret = array();
  for($i = 0; $i < count($arr_ua); $i++){
    $ae = explode("~", $arr_ua[$i]);
    $ret[] = $ae[1];
  }
  return $ret;
}

function fetch_facility_amenities($facility_id, $arrFM){
  global $conn;
  $resFA = mysqli_query($conn, "SELECT amenity as fac_amenity FROM facility_amenity where facility_id='".mysqli_real_escape_string($conn, $facility_id)."'");
  
  $amenities = array();
  while($arrFA = mysqli_fetch_array($resFA, MYSQLI_ASSOC)){
		$amenities[] = $arrFA['fac_amenity'];
	}
	$arr_dfm = construct_addl_discounts($arrFM);
	for($i = 0; $i < count($arr_dfm); $i++){
	  $amenities[] = $arr_dfm[$i];
	}
	return a_unique($amenities);
}

function calc_from_amenity_dict($filter_opt_ids){
	global $conn;
	$opt = $filter_opt_ids[0];
	for($i = 1; $i < count($filter_opt_ids); $i++){
	  $opt .= ', '.$filter_opt_ids[$i];
	}
		  
	$resO = mysqli_query($conn,"select equivalent from amenity_dictionary where option_id in (".mysqli_real_escape_string($conn, $opt).")");
	$ret = array();
  while($arrO = mysqli_fetch_array($resO,MYSQLI_ASSOC)){
  	$ret[] = $arrO['equivalent'];
  }
  return $ret;
}

function eval_filters($facility_unit_amenities, $filter_dict_opts){
  //if(count($filter_dict_opts) == 0)
    //return true;
  for($i = 0; $i < count($filter_dict_opts); $i++){
    for($j = 0; $j < count($facility_unit_amenities); $j++){
      if((stristr($facility_unit_amenities[$j], $filter_dict_opts[$i]) !== FALSE) || (stristr($filter_dict_opts[$i], $facility_unit_amenities[$j]) !== FALSE))
        return true;
    }
  }
  return false;
}

function file_get_contents_curl($url){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
  curl_setopt($ch, CURLOPT_URL, $url);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

function get_lat_lng($loc){
  $url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&address=".urlencode(trim($loc))."&sensor=false";
	$result_string = file_get_contents_curl($url);
  $result = json_decode($result_string, true);
  $lat = $result['results'][0]['geometry']['location']['lat'];
  $lng = $result['results'][0]['geometry']['location']['lng'];
  return array($lat, $lng);
}

function calculate_distance($loc1, $loc2){
  $ll1 = get_lat_lng($loc1);
  $ll2 = get_lat_lng($loc2);
  
  return round((3959 * acos(cos(deg2rad($ll1[0])) * cos(deg2rad($ll2[0])) * cos(deg2rad($ll2[1])- deg2rad($ll1[1])) + sin(deg2rad($ll1[0])) * sin(deg2rad($ll2[0])))), 1);
}
// (3959 * acos(cos(radians(".$lat.")) * cos(radians(lat)) * cos(radians(lng)- radians(".$lng.")) + sin(radians(".$lat.")) * sin(radians(lat))))
function calculate_distance_ll($lat, $lng, $loc){
  $ll = get_lat_lng($loc);
  //return (3959 * acos(cos(deg2rad($ll[0])) * cos(deg2rad($lat)) * cos(deg2rad($lng)- deg2rad($ll[1])) + sin(deg2rad($ll[0])) * sin(deg2rad($lat))));
  return round((3959 * acos(cos(deg2rad($ll[0])) * cos(deg2rad($lat)) * cos(deg2rad($lng)- deg2rad($ll[1])) + sin(deg2rad($ll[0])) * sin(deg2rad($lat)))), 1);
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
    width: 50%; /* Could be more or less, depending on screen size */
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

/* Fading animation 
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
*/
@keyframes animatetop {
    from {top: 0%; opacity: 0}
    to {top: 20%; opacity: 1}
}

/* On smaller screens, decrease text size */
@media only screen and (max-width: 300px) {
  .prev, .next,.text {font-size: 11px}
}

@-moz-keyframes blink {
    0% {
        opacity:1;
    }
    50% {
        opacity:0;
    }
    100% {
        opacity:1;
    }
} 

@-webkit-keyframes blink {
    0% {
        opacity:1;
    }
    50% {
        opacity:0;
    }
    100% {
        opacity:1;
    }
}
/* IE */
@-ms-keyframes blink {
    0% {
        opacity:1;
    }
    50% {
        opacity:0;
    }
    100% {
        opacity:1;
    }
} 
/* Opera and prob css3 final iteration */
@keyframes blink {
    0% {
        opacity:1;
    }
    50% {
        opacity:0;
    }
    100% {
        opacity:1;
    }
} 
.blink-image {
    -moz-animation: blink normal 2s infinite ease-in-out; /* Firefox */
    -webkit-animation: blink normal 2s infinite ease-in-out; /* Webkit */
    -ms-animation: blink normal 2s infinite ease-in-out; /* IE */
    animation: blink normal 2s infinite ease-in-out; /* Opera and prob css3 final iteration */
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

  function group_by_arr(arr_keys, arr_data){
    var ret = {};
    for(i = 0; i < arr_keys.length; ++i){
      ret[arr_keys[i]] = new Array();
    }
    for(i = 0; i < arr_data.length - 1; ++i){
      var split_data = arr_data[i].split('|');
      if(split_data.length == 1){
          ret['Other'].push(split_data[0]);
          continue;
      }
      for(j = 0; j < arr_keys.length; ++j){
        if(split_data[0].trim().toUpperCase() === arr_keys[j].trim().toUpperCase()){
          ret[arr_keys[j]].push(split_data[1]);
          break;
        }
      }
    }
    return ret;
  }
  
  function popupMoreLessAmenities(facilityName, amenitiesArr){
    setTimeout(function(){
    var y = document.getElementById("modalAmenities");
      var ac = document.getElementById("amenity-container");
      var heading = '<br /><h4 style="text-align: center"><b>' + facilityName + '</b></h4>';
      var tbl_start = '<table><tr><td style="vertical-align:top">';
      var tbl_end = '</td></tr></table>';
      var img = '<img src="images/gtick.png" style="vertical-align: left;width:10px;height:10px" />';
      var data = '';
      
      var arr_groups = ['Climate Control', 'Security Features', 'Discounts', 'Administration', 'Property Coverage', 'Other'];
      var ret = group_by_arr(arr_groups, amenitiesArr);
      var rows = 0;
      for(i = 0; i < arr_groups.length; ++i){
        var ams = ret[arr_groups[i]];
        if(ams.length > 0){
          data += '<br /><b>' + arr_groups[i] + '</b><br />';
          rows++
          for(j = 0; j < ams.length; ++j){
            data += img;
            data += '  ' + ams[j];
            rows++;
          }
          if(rows > 5){
            data += '</td><td style="vertical-align:top; padding-left:10px">';
            rows = 0;
          }
        }
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

