<?php
session_start();
include('service_utils.php');

if(isset($_POST['action'])){
	if($_POST['action'] == "nearlocation"){
		$query = "";
		if($_POST['lat'] == 0 || $_POST['lng'] == 0)
			$query = "select *, 0 as calc_distance from facility_master where searchable=1 and city is not null and state is not null and lat is not null and lng is not null limit 10";
		else
			$query = "select *,(3959 * acos(cos(radians(".mysqli_real_escape_string($conn, $_POST['lat']).")) * cos(radians(lat)) * cos(radians(lng)- radians(".mysqli_real_escape_string($conn, $_POST['lng']).")) + sin(radians(".mysqli_real_escape_string($conn, $_POST['lat']).")) * sin(radians(lat)))) as calc_distance from facility_master having calc_distance < 25 and searchable=1  and city is not null and state is not null and lat is not null and lng is not null order by calc_distance limit 10";

		$res = mysqli_query($conn,$query);
		while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
		  $facility_id = htmlspecialchars($arr['id'], ENT_QUOTES);
		  $arr_imgs = fetch_image_url($facility_id);
		  $unit_info_arr = fetch_units($facility_id);
      //$facility_unit_amenities = fetch_consolidate_amenities($facility_id, $unit_info_arr);
      $facility_unit_amenities = fetch_facility_amenities($facility_id, $arr);
      $from_unit_amenities = fetch_priority_unit_amenities($facility_id, $unit_info_arr);
      
      //$facility_unit_amenities = a_unique(a_merge(get23($from_unit_amenities), $facility_unit_amenities));
      
      $priority_amenities = arrange_priority_with_group($facility_unit_amenities);
      
			echo '<table style="font-size: .9em;margin-bottom: 10px;width:100%;box-shadow: 5px 5px 5px #888888;"><tr>';
			echo '<td style="margin:0px;padding:0px;width:120px;vertical-align: top;border-top:1px solid #ddd;border-left:1px solid #ddd;">';
			$image_file_name = extract_image_name($arr_imgs['url_thumbsize']);
			$expected_image_path = "images/".$facility_id."/".$image_file_name;
			echo '<a href="javascript:showMorePhotos('.$facility_id.')">';
			if(file_exists($expected_image_path))
				echo '<img src="'.$expected_image_path.'" style="min-height:120px;width:120px;">';
			else if(strlen($arr_imgs['url_thumbsize']) > 0)
				echo '<img src="https:'.htmlspecialchars($arr_imgs['url_thumbsize'], ENT_QUOTES).'" style="min-height:120px;width:120px;">';
			else
			  echo '<img src="unitimages/pna.jpg" style="min-height:120px;width:120px;">';
			
			echo '</a>';
			echo '<br><a href="javascript:showMorePhotos('.$facility_id.')">More Photos</a>';
////
      echo '<div id="dateday_'.$facility_id.'" class="login-block" name="dateday_'.$facility_id.'" style="margin:0px;text-align:left;padding:0;">';
			echo '<p id="mdatemsg_'.$facility_id.'" style="display:none;color:#BB0000;font-size:.9em;margin:0;margin-left: 10px;padding:0;text-align:left;">Enter Move-In Date</p>';
			echo '<input class="datepicker" id="mdate_'.$facility_id.'" name="mdate_'.$facility_id.'" type="text" placeholder="Move-in Date"  style="width:200px;height:30px;padding:5px;margin:5px;font-size:.8em;"></div><br />';
////
			echo '</td>';

			echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
			
			echo '<table>';
			
			echo '<tr><td><b>'.htmlspecialchars($arr['title']).'</b><br>';
			echo htmlspecialchars($arr['city'].",".$arr['state']." ".$arr['zip'], ENT_QUOTES).'<br />';
			if(isset($arr['calc_distance']) && $arr['calc_distance'] > 0){
			  $rounded = round($arr['calc_distance'], 1);
			  if($rounded > 0)
  			  echo $rounded.' miles away<br />';
  			else
  			  echo '0.1 miles away<br />';
			}
			
			//if(has_priority_amenity($facility_unit_amenities, array('climate')))
			if(fetch_has_facility_cc($facility_id))
	  		  echo '<img src="images/cc.jpg" title="climate control equipped" style="min-height:40px;width:40px;" />';
	  		
	  	if(has_priority_amenity($facility_unit_amenities, array('security', 'camera', 'video camera')))
	  		  echo '<img src="images/secam.png" title="security camera monitoring" style="min-height:40px;width:40px;margin-left:4px" />';
			
			echo '</td>';
//
      echo '<td><div style="float:right;padding:0;margin:0;font-size:.9em;color:#68AE00;">Reservations held for Move-in Date + '.htmlspecialchars($arr['reservationdays'], ENT_QUOTES).' days</div></td>';
//
			echo '</tr>';

      show_amenities($facility_id, $priority_amenities, 5, $arr['title']);
      
      echo '</table>';
      
      ////echo '</td><tr><td colspan=2 style="padding:0;border-left:1px solid #ddd;text-align:left">';
			
			////echo '<div id="dateday_'.$facility_id.'" class="login-block" name="dateday_'.$facility_id.'" style="margin:0px;text-align:left;padding:0;">';
			////echo '<p id="mdatemsg_'.$facility_id.'" style="display:none;color:#BB0000;font-size:.9em;margin:0;margin-left: 10px;padding:0;text-align:left;">Enter Move-In Date</p>';
			////echo '<input class="datepicker" id="mdate_'.$facility_id.'" name="mdate_'.$facility_id.'" type="text" placeholder="Move-in Date"  style="width:200px;height:30px;padding:5px;margin:5px;font-size:.8em;"></div><br />';
						
			echo '</td><tr><td colspan=2 style="padding:0;border-left:1px solid #ddd;">';

			show_units($facility_id, $unit_info_arr, 4, $arr['reservationdays'], $from_unit_amenities, $arr['title'], $facility_unit_amenities);

			echo'</td></tr></table>';
		}
	}
	else if($_POST['action'] == "sessionreserve"){
		$_SESSION['res_fid'] = htmlspecialchars($_POST['fid'], ENT_QUOTES);
		$_SESSION['res_cid'] = htmlspecialchars($_POST['cid'], ENT_QUOTES);
		$_SESSION['res_rdays'] = htmlspecialchars($_POST['rdays'], ENT_QUOTES);
		$_SESSION['res_rdate'] = htmlspecialchars($_POST['rdate'], ENT_QUOTES);
		$_SESSION['res_unit'] = htmlspecialchars($_POST['unit']);
		$_SESSION['res_price'] = htmlspecialchars($_POST['price'], ENT_QUOTES);
		if(isset($_POST['phone']) && strlen($_POST['phone']) == 10)
  		$_SESSION['res_phone'] = htmlspecialchars($_POST['phone'], ENT_QUOTES);
  	else
  	  $_SESSION['res_phone'] = 'unknown';
  	//error_log('service_n session_reserve - '.$_POST['phone'].', session - '.$_SESSION['res_phone']);
		echo "success";
	}
	else if($_POST['action'] == "reserve"){
		$rdateArr = explode("/",$_POST['rdate']);
		$reserveFromDate = mktime(0,0,0,$rdateArr[0],$rdateArr[1],$rdateArr[2]);
		$reserveToDate = strtotime("+".$_POST['rdays']." days",$reserveFromDate); 

		mysqli_query($conn,"insert into reserve(cid, fid, reservefromdate, reservetodate, units) values('".mysqli_real_escape_string($conn, $_POST['cid'])."','".
										mysqli_real_escape_string($conn, $_POST['fid'])."','".
										$reserveFromDate."','".
										$reserveToDate."','".
										mysqli_real_escape_string($conn,",".$_POST['unit']."-$".$_POST['price'].",")."')");
		
		$resC = mysqli_query($conn,"select * from customer where id='".mysqli_real_escape_string($conn, $_POST['cid'])."'");
		$resF = mysqli_query($conn,"select * from facility_master where id='".mysqli_real_escape_string($conn, $_POST['fid'])."'");
    $resO = mysqli_query($conn,"select O.firstname as firstname, O.lastname as lastname, O.emailid as emailid from facility_owner O, facility_master M where M.id='".mysqli_real_escape_string($conn, $_POST['fid'])."' and O.auto_id=M.facility_owner_id limit 1");
		if((mysqli_num_rows($resC) > 0) && (mysqli_num_rows($resF) > 0)){
			$arrC = mysqli_fetch_array($resC, MYSQLI_ASSOC);
			$arrF = mysqli_fetch_array($resF, MYSQLI_ASSOC);
			$arrO = read_owner_data($resO, $arrF['id']);

			$phone = '';
			if((!isset($arrC['phone']) || strlen($arrC['phone']) < 10) && isset($_POST['phone']) && isset($arrC['id'])){
			  save_phone($arrC['id'], $_POST['phone']);
			  $phone = $_POST['phone'];
			}
			else if(isset($arrC['phone'])){
			  $phone = $arrC['phone'];
			}
			//error_log('service_n reserve - '.$phone.', session: '.$_SESSION['res_phone'].', posted: '.$_POST['phone']);
			$img_paths_arr = calc_img_path($arrF['id']);
			$img_path = $img_paths_arr['url_fullsize'];
			$facilityAddress = $arrF['street'].", ".($arrF['locality']==""?"":$arrF['region'].", ").$arrF['city'].", ".$arrF['state']." - ".$arrF['zip'];
			onReserveAdminMail( $arrF['title'],
			                    $facilityAddress,
			                    $arrF['phone'],
			                    $arrO[0],
													$arrO[1]." ".$arrO[2],
													$phone,
													$_POST['unit'],
													$_POST['price'],
													date('m/d/Y',$reserveFromDate),
													date('m/d/Y',$reserveToDate),
													$phone,
													$arrC['emailid'],
													$arrC['firstname']." ".$arrC['lastname']);
			onReserveCustomerMail($arrF['id'], $arrC['emailid'],
													$arrC['firstname']." ".$arrC['lastname'],
													$_POST['unit'],
													$_POST['price'],
													$arrF['title'],
													date('m/d/Y', $reserveFromDate),
													date('m/d/Y', $reserveToDate),
													$img_path,
													$facilityAddress);
			onReserveOwnerMail( $arrF['title'],
			                    $arrO[0],
													$arrO[1]." ".$arrO[2],
													$_POST['unit'],
													$_POST['price'],
													date('m/d/Y',$reserveFromDate),
													date('m/d/Y',$reserveToDate),
													$arrC['firstname']." ".$arrC['lastname']);
		}
		echo "success";
	}
}

