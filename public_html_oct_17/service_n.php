<?php
session_start();
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
  $arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
  return $arr;
}

function fetch_review_count($facility_id){
  global $conn;
  $res = mysqli_query($conn,"select count(*) as rct from review where facility_id='".$facility_id."'");
  $arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
  return $arr['rct'];
}

if(isset($_POST['action'])){
	if($_POST['action'] == "addfacility"){
		$email  = checkEmail($_POST['email'], 0);
		$units = checkUnits($_POST['units']);									

    $owner_id = insert_facility_owner($email);
    if($email == false){
      echo("Failed to persist facility owner information to DB");
      header("Location: facility/dashboard.php");
      exit;
    }
    else{
      $gen_id = "leazzer".$owner_id;
			mysqli_query($conn,"insert into facility_master(facility_owner_id, id, title, phone, street, locality, region, city, state, zip, lat, lng) values ('".
												$owner_id."','".gen_id."','".
												mysqli_real_escape_string($conn,$_POST['company'])."','".
												mysqli_real_escape_string($conn,$_POST['phone'])."','".
												mysqli_real_escape_string($conn,$_POST['street'])."','".
												mysqli_real_escape_string($conn,$_POST['locality'])."','".
												mysqli_real_escape_string($conn,$_POST['region'])."','".
												$_POST['city']."','".
												$_POST['state']."','".
												$_POST['zip']."','".
												$_POST['lat']."','".
												$_POST['lng']."')");
    }
	}
	if($_POST['action'] == "nearlocation"){
		$query = "";
		if($_POST['lat'] == 0 || $_POST['lng'] == 0)
			$query = "select *, 0 as calc_distance from facility_master where searchable=1 and city is not null and state is not null limit 10";
		else
			$query = "select *,(6371 * acos(cos(radians(".$_POST['lat'].")) * cos(radians(lat)) * cos(radians(lng)- radians(".$_POST['lng'].")) + sin(radians(".$_POST['lat'].")) * sin(radians(lat)))) as calc_distance from facility_master having calc_distance < 10000 and searchable=1  and city is not null and state is not null order by calc_distance limit 10";

		$res = mysqli_query($conn,$query);
		while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
		  $facility_id = $arr['id'];
		  $arr_imgs = fetch_image_url($facility_id);
		  
			echo '<table style="font-size: .9em;margin-bottom: 10px;width:100%;box-shadow: 5px 5px 5px #888888;"><tr>';
			echo '<td style="margin:0px;padding:0px;width:120px;vertical-align: top;border-top:1px solid #ddd;border-left:1px solid #ddd;">';
			$image_file_name = extract_image_name($arr_imgs['url_thumbsize']);
			$expected_image_path = "images/".$facility_id."/".$image_file_name;
			if(file_exists($expected_image_path))
				echo '<img src="'.$expected_image_path.'" style="min-height:120px;width:120px;">';
			else if(strlen($arr_imgs['url_thumbsize']) > 0)
				echo '<img src="https:'.$arr_imgs['url_thumbsize'].'" style="min-height:120px;width:120px;">';
			else
			  echo '<img src="unitimages/pna.jpg" style="min-height:120px;width:120px;">';
			echo '</td>';

			echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
			
			echo '<table>';
			
			echo '<tr><td><b>'.$arr['title'].'</b><br>';
			echo $arr['city'].",".$arr['state']." ".$arr['zip'].'<br /></td></tr>';
			
			$unit_info_arr = fetch_units($facility_id);
      $facility_unit_amenities = fetch_consolidate_amenities($facility_id, $unit_info_arr);
      show_amenities($facility_id, $facility_unit_amenities);
      
      echo '</table>';
      
      echo '</td><tr><td colspan=2 style="padding:0;border-left:1px solid #ddd;text-align:left">';
			
			echo '<div id="dateday_'.$arr['id'].'" class="login-block" name="dateday_'.$arr['id'].'" style="margin:0px;text-align:left;padding:0;">';
			echo '<p id="mdatemsg_'.$arr['id'].'" style="display:none;color:#BB0000;font-size:.9em;margin:0;margin-left: 10px;padding:0;text-align:left;">Enter Move-In Date</p>';
			echo '<input class="datepicker" id="mdate_'.$arr['id'].'" name="mdate_'.$arr['id'].'" type="text" placeholder="Move-in Date"  style="width:200px;height:30px;padding:5px;margin:5px;font-size:.8em;"></div>';
			echo '</td><tr><td colspan=2 style="padding:0;border-left:1px solid #ddd;">';

			show_units($facility_id, $unit_info_arr);

			echo'</td></tr></table>';
		}			
	}
	if($_POST['action'] == "sessionreserve"){
		$_SESSION['res_fid'] = $_POST['fid'];
		$_SESSION['res_cid'] = $_POST['cid'];
		$_SESSION['res_rdays'] = $_POST['rdays'];
		$_SESSION['res_rdate'] = $_POST['rdate'];
		$_SESSION['res_unit'] = $_POST['unit'];
		$_SESSION['res_price'] = $_POST['price'];
		echo "success";
	}
	if($_POST['action'] == "reserve"){
		$rdateArr = explode("/",$_POST['rdate']);
		$reserveFromDate = mktime(0,0,0,$rdateArr[0],$rdateArr[1],$rdateArr[2]);
		$reserveToDate = strtotime("+".$_POST['rdays']." days",$reserveFromDate); 

		mysqli_query($conn,"insert into reserve(cid,fid,reservefromdate,reservetodate,units) values('".$_POST['cid']."','".
										$_POST['fid']."','".
										$reserveFromDate."','".
										$reserveToDate."','".
										mysqli_real_escape_string($conn,",".$_POST['unit']."-$".$_POST['price'].",")."')");
		
		$resC = mysqli_query($conn,"select * from customer where id=".$_POST['cid']);
		$resF = mysqli_query($conn,"select * from facility_master where id=".$_POST['fid']);
		if((mysqli_num_rows($resC) > 0) && (mysqli_num_rows($resF) > 0)){
			$arrC = mysqli_fetch_array($resC,MYSQLI_ASSOC);
			$arrF = mysqli_fetch_array($resF, MYSQLI_ASSOC);
			onReserveCustomerMail($arrC['emailid'],
													$arrC['firstname']." ".$arrC['lastname'],
													$_POST['unit'],
													$_POST['price'],
													$arrF['companyname'],
													date('m/d/Y', $reserveFromDate),
													date('m/d/Y', $reserveToDate),
													$arrF['image'],
													$arrF['street']."<br>".($arrF['locality']==""?"":$arrF['region']."<br>").$arrF['city']." ".$arrF['state']." - ".$arrF['zip']);
			onReserveOwnerMail($arrF['emailid'],
													$arrF['firstname']." ".$arrF['lastname'],
													$_POST['unit'],
													$_POST['price'],
													date('m/d/Y',$reserveFromDate),
													date('m/d/Y',$reserveToDate));
			if($arrF['receivereserve'] == "1"){
				onReserveOwnerText($arrF['phone'],"Congratulations. A ".$_POST['unit']." unit reservation has been confirmed at ".$arrF['companyname']." for you from ".date('m/d/Y',$reserveFromDate)." to ".date('m/d/Y',$reserveFromDate)." for the price of ".$_POST['price']." per month.");
			}
		}
		echo "success";
	}
}

function onReserveOwnerText($toNumber, $message){
	require 'twilio/Twilio/autoload.php';
	$fromNumber = "+18327939577";//"+18323810817";
	$account_sid = 'ACd6cb12f2e7dd554028648e4882b3cf1a';
	$auth_token = '2b2a366ce9be1c7b1daedab4f4939fe9';
	//001-8323810817
	//test : 424de3ff616f82d4ef59e531da52f1e2
	//real : 2b2a366ce9be1c7b1daedab4f4939fe9
	// In production, these should be environment variables. E.g.:
	// $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]

	// A Twilio number you own with SMS capabilities
	//$twilio_number = $fromNumber;
	$client = new Twilio\Rest\Client($account_sid, $auth_token);
	$message = $client->messages->create(
    $toNumber,
    array(
        'from' => $fromNumber,
        'body' => $message
    ));
  //print_r($message);
}

function onReserveCustomerMail($custEmail, $custName, $unit, $price, $companyName, $resFromDate, $resToDate, $image, $fAddress){
	global $conn,$GError;
	$fromemail="no-reply@leazzer.com"; 
	$toemail=$custEmail; 
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= 'Hello <b>'.$custName.'</b>,';
	$message .= '<br><br>Congratulations. A '.$unit.' unit reservation has been confirmed at '.$companyName.' for you from ';
	$message .= $resFromDate.' to '.$resToDate.' for the price of $'.$price.' per month.<br>';
	
	if(strlen($image) > 0){
		if(file_exists("unitimages/".$image))
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

function onReserveOwnerMail($ownerEmail,$ownerName,$unit,$price,$resFromDate,$resToDate){
	global $conn,$GError;
	$fromemail="no-reply@leazzer.com"; 
	$toemail=$ownerEmail; 
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= '<center><img src="https://www.leazzer.com/images/reservation.png" height="150px" width="125px" alt="Logo" title="Logo" style="display:block"></center><br>';
	$message .= 'Hello <b>'.$ownerName.'</b>,';
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

function show_amenities($facility_id, $facility_unit_amenities){
  $arr_len = count($facility_unit_amenities);
  echo '<tr><td style="width:900px;padding-left:400px">';
	for($i = 0; $i < $arr_len; $i++){
	  $amenity = $facility_unit_amenities[$i];
	  echo '<img src="images/gtick.png" style="vertical-align: left;width:10px;height:10px">';
		echo '  '.$amenity.'</br>';
	}
	if(fetch_review_count($facility_id) > 0){
	  echo '<img src="images/gtick.png" style="vertical-align: left;width:10px;height:10px">';
	  echo '  <a href="reviews.php?facility_id='.$facility_id.'">Reviews</a> (* Based on reviews collected from third-party sites)</br>';
	}
	echo '</td></tr>';
}

function show_units($facility_id, $arr_arr_FU){

  echo '<div id="unitstbl_'.$facility_id.'" style="width:100%">';
  $arr_len = count($arr_arr_FU);
	for($i = 0; $i < $arr_len; $i++){
	  $arrFU = $arr_arr_FU[$i];
		echo '<div class="col-md-1" style="text-align:center;padding:10px;border:0px solid #000;box-shadow: 0px 0px 3px #888888;">';
		echo '<img src="unitimages/'.($arrFU['img']==""?"pna.jpg":$arrFU['img']).'" style="vertical-align: top;width:50px;height:50px">';
		echo '<p style="text-align:center;width:80px;display:inline-block;padding:0px 10px 0px 10px;margin:0;font-size:.8em;white-space: nowrap;"><b>'.$arrFU['size'].'</b><br>$'.$arrFU['price'].'</p>';
		echo '<button type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;margin: 0 auto;border-radius: 3px;font-size: 1.0em;width:80px;display:inline;padding:0px;" onClick="onUnitClick(this,'.
										(isset($_SESSION['lcdata'])?$_SESSION['lcdata']['id']:"0").','.
										$arrFU['id'].',\'0\',\''.
										urlencode($arrFU['size']).'\',\''.
										$arrFU['price'].'\');">Reserve</button></div>';
	}
	echo "</div>";
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
      if($arr[$i] == $arr[$j])
        $add = 0;
    }
    if($add == 1)
      $res[] = $arr[$i];
  }
  return $res;
}

function fetch_consolidate_amenities($facility_id, $unit_info_arr){
  global $conn;
  $resFA = mysqli_query($conn, "SELECT amenity as fac_amenity FROM facility_amenity where facility_id='".$facility_id."'");
  
  $amenities = array();
  while($arrFA = mysqli_fetch_array($resFA, MYSQLI_ASSOC)){
		$amenities[] = $arrFU['fac_amenity'];
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
	  }
	  if(count($unit_amenities) == 0){
      $unit_amenities = $unit_amenities_spec;	  
	  }
	  else{
	    $unit_amenities = a_intersect($unit_amenities, $unit_amenities_spec);
	  }
	}
	return a_unique(a_merge($amenities, $unit_amenities));
}

?>

