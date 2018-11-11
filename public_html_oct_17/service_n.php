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
	  $arr = mysqli_fetch_array($res, MYSQLI_ASSOC)
		return $arr['auto_id'];
	}
	else{
			return false;
	}
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
			$query = "select *, 0 as distance from facility_master where searchable=1 limit 10";
		else
			$query = "select *,(6371 * acos(cos(radians(".$_POST['lat'].")) * cos(radians(lat)) * cos(radians(lng)- radians(".$_POST['lng'].")) + sin(radians(".$_POST['lat'].")) * sin(radians(lat)))) as distance from facility_master having distance < 10000 and searchable=1 order by distance limit 10";
		
		$res = mysqli_query($conn,$query);
		while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
			echo '<table style="font-size: .9em;margin-bottom: 10px;width:100%;box-shadow: 5px 5px 5px #888888;"><tr>';
			echo '<td style="margin:0px;padding:0px;width:120px;vertical-align: top;border-top:1px solid #ddd;border-left:1px solid #ddd;">';
			if(file_exists("unitimages/".$arr['image']))
				echo '<img src="unitimages/'.$arr['image'].'" style="min-height:120px;width:120px;">';
			else
				echo '<img src="'.$arr['image'].'" style="min-height:120px;width:120px;">';
			echo '</td>';
			echo '<td class="login-block" style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;"><b>'.$arr['companyname'].'</b><div style="float:right;padding:0;margin:0;font-size:.9em;color:#68AE00;">Reservations held for Move-in Date + '.$arr['reservationdays'].' days</div><br>';
			echo $arr['city'].",".$arr['state']." ".$arr['zipcode'].'<br>';
			
			if($arr['options']!=""){
				showOpt($arr);
			}
			else
				echo "<br>";
			echo '</td><tr><td colspan=2 style="padding:0;border-left:1px solid #ddd;text-align:left">';
			echo '<div id="dateday_'.$arr['id'].'" class="login-block" name="dateday_'.$arr['id'].'" style="margin:0px;text-align:left;padding:0;">';
			echo '<p id="mdatemsg_'.$arr['id'].'" style="display:none;color:#BB0000;font-size:.9em;margin:0;margin-left: 10px;padding:0;text-align:left;">Enter Move-In Date</p>';
			echo '<input class="datepicker" id="mdate_'.$arr['id'].'" name="mdate_'.$arr['id'].'" type="text" placeholder="Move-in Date"  style="width:200px;height:30px;padding:5px;margin:5px;font-size:.8em;"></div>';
			echo '</td><tr><td colspan=2 style="padding:0;border-left:1px solid #ddd;">';
			if($arr['units']!=""){
				showUnit($arr);
			}
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
	
	if($image!=""){
		if(file_exists("unitimages/".$image))
			$message .= '<center><img src="https://www.leazzer.com/unitimages/'.$image.'" height="120px"></center><br>';
		else
			$message .= '<center><img src="'.$image.'" height="120px"></center><br>';
	}
	else
		$message .= '<center><img src="https://www.leazzer.com/unitimages/pna.jpg" height="120px"></center><br>';
		
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
	$message .= '<center><img src="https://www.leazzer.com/images/reservation.png" height="150px"></center><br>';
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

function showOpt($arr){
	global $conn;
	$opt = $arr['options'];
	$pos = strpos($opt,",");
	if($pos ==  0)
		$opt = substr($opt,1,strlen($opt)-2);
	else
		$opt = substr($opt,0,strlen($opt)-1);
	$resO = mysqli_query($conn,"select * from options where id in(".$opt.")");
	if($resO)
	{
		echo '<p style="letter-spacing: 0px;text-align:left;font-size:.8em">';
		while($arrO = mysqli_fetch_array($resO,MYSQLI_ASSOC))
			echo $arrO['opt'].', ';
		echo "</p>";
	}
}

function showUnit($arr){
	global $conn;
	$unitIds="";
	$unitPrice="";
	
	$unitArr = explode(",",$arr['units']);
	for($i=0;$i<count($unitArr);$i++)
	{
		if(trim($unitArr[$i]) == "")
		continue;
		
		$unitSubArr = explode("-",$unitArr[$i]);
		$unitIds .= $unitSubArr[0].",";
		$unitPrice.= $unitSubArr[1].",";
	}
	$unitIds = substr($unitIds,0,strlen($unitIds)-1);
	$unitPrice = substr($unitPrice,0,strlen($unitPrice)-1);
	$unitPriceArr = explode(",",$unitPrice);
	$resU = mysqli_query($conn,"select * from units where id in(".$unitIds.")");
	echo '<div id="unitstbl_'.$arr['id'].'" style="width:100%">';
	$cnt = 0;
	while($arrU = mysqli_fetch_array($resU,MYSQLI_ASSOC))
	{
		echo '<div class="col-md-1" style="text-align:center;padding:10px;border:0px solid #000;box-shadow: 0px 0px 3px #888888;">';
		echo '<img src="unitimages/'.($arrU['images']==""?"pna.jpg":$arrU['images']).'" style="vertical-align: top;width:50px;height:50px">';
		echo '<p style="text-align:center;width:80px;display:inline-block;padding:0px 10px 0px 10px;margin:0;font-size:.8em;white-space: nowrap;"><b>'.$arrU['units'].'</b><br>$'.$unitPriceArr[$cnt].'</p>';
		echo '<button type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;margin: 0 auto;border-radius: 3px;font-size: 1.0em;width:80px;display:inline;padding:0px;" onClick="onUnitClick(this,'.
										(isset($_SESSION['lcdata'])?$_SESSION['lcdata']['id']:"0").','.
										$arr['id'].','.
										$arr['reservationdays'].',\''.
										urlencode($arrU['units']).'\',\''.
										$unitPriceArr[$cnt].'\');">Reserve</button></div>';
		$cnt++;
	}
	echo "</div>";
}
?>

