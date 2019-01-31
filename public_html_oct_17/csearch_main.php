<?php
session_start();
//include('service_utils.php');

$GError = "";
if((!isset($_POST['search'])) && isset($_SESSION['search']))
	$_POST['search']= $_SESSION['search'];
else if(isset($_POST['search']))
	$_SESSION['search']= htmlspecialchars($_POST['search'], ENT_QUOTES);
	
if((!isset($_POST['slat'])) && isset($_SESSION['slat']))
	$_POST['slat']= $_SESSION['slat'];
else if(isset($_POST['slat']))
	$_SESSION['slat']= htmlspecialchars($_POST['slat'], ENT_QUOTES);
	
if((!isset($_POST['slng'])) && isset($_SESSION['slng']))
	$_POST['slng']= $_SESSION['slng'];
else if(isset($_POST['slng']))
	$_SESSION['slng']= htmlspecialchars($_POST['slng'], ENT_QUOTES);

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Leazzer" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>

<script type="text/javascript">

function validatePhone(phone){
  if(phone == undefined || phone == null || phone.length != 10)
    return false;
  return true;
}

function onUnitClick(btn, cid, fid, rdays, unit, price, hasPhone){
  var phone = 'unknown';
  if(validatePhone(hasPhone) == true)
    phone = hasPhone;
	if($('#mdate_'+fid).val() == ""){
		$('#mdatemsg_'+fid).show();
	}
	else if(cid == 0){
			var res = ajaxcall("action=sessionreserve&fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price+
									"&phone="+phone);
			if(res !== false){
				window.location.href='customer/index_n.php?action=search';
			}
	}
	else if(validatePhone(phone) == false){
	  $('#mdatemsg_'+fid).hide();
			window.location.href = "askphone.php?ref=index&fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price+
									"&phone="+phone;
	}
	else{
			$('#mdatemsg_'+fid).hide();
			var res = ajaxcall("action=reserve&fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price+
									"&phone="+phone);
			//if(res == "success")
			{
				btn.innerHTML = "<i class=\"fa fa-check\"></i>";
				window.location.href = "thankyou_n.php?fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price+
									"&phone="+phone;
			}
	}
}

function ajaxcall(datastring){
    var res;
    $.ajax
    ({	
    		type:"POST",
    		url:"service_n.php",
    		data:datastring,
    		cache:false,
    		async:false,
    		success: function(result){		
   				 	res = result;
   		 	},
   		 	error: function(err){
   		 	    alert('Failed to invoke serverside function(from csearch)... Please try again in some time');
   		 	    res = false;
   		 	}
    });
    return res;
}

function fd_show(){
  var modal = document.getElementById('myModal');
  modal.style.display = "block";
}

function fd_hide(){
  var modal = document.getElementById('myModal');
  modal.style.display = "none";
}

</script>
</head>
<body>
<?php
  include('service_utils.php');

function show_results($arr){
  $calc_distance = $arr['calc_distance'];
  $facility_id = htmlspecialchars($arr['id'], ENT_QUOTES);

	$facility_unit_amenities = fetch_facility_amenities($facility_id, $arr);

    $arr_imgs = fetch_image_url($facility_id);
	  $unit_info_arr = fetch_units($facility_id);
	  $from_unit_amenities = fetch_priority_unit_amenities($facility_id, $unit_info_arr);

    //$facility_unit_amenities = a_unique(a_merge(get23($from_unit_amenities), $facility_unit_amenities));
    
    $priority_amenities = arrange_priority_with_group($facility_unit_amenities);
    echo '<tr style="margin:0px;padding:0px;border:0px solid #000;background:none;">';
	  echo '<td style="background:none;margin:0px;padding:5px;border:0px solid #000;">';

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

	  echo '<div id="dateday_'.$facility_id.'" class="login-block" name="dateday_'.$facility_id.'" style="margin:0px;text-align:left;padding:0;">';
	  echo '<p id="mdatemsg_'.$facility_id.'" style="display:none;color:#BB0000;font-size:.9em;margin:0;margin-left: 10px;padding:0;text-align:left;">Enter Move-In Date</p>';
	  echo '<input class="datepicker" id="mdate_'.$facility_id.'" name="mdate_'.$facility_id.'" type="text" placeholder="Move-in Date"  style="width:200px;height:30px;padding:5px;margin:5px;font-size:.8em;"></div><br />';

	  echo '</td>';
    
	  echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
	    		
	  echo '<table>';

	  echo '<tr><td><b>'.htmlspecialchars($arr['title'], ENT_QUOTES).'</b><br>';
	  echo htmlspecialchars($arr['city'].",".$arr['state']." ".$arr['zip'], ENT_QUOTES).'<br />';
    
	  if($calc_distance > 0)
      echo $calc_distance.' miles away<br />';
    else
      echo '0.1 miles away<br />';

	  
	  if(fetch_has_facility_cc($facility_id)){
	  //if(has_priority_amenity($facility_unit_amenities, array('climate')))
	    echo '<img src="images/cc.jpg" title="climate control equipped" style="min-height:40px;width:40px;" />';
    }
    
	  if(has_priority_amenity($facility_unit_amenities, array('security', 'camera', 'video camera')))
	    echo '<img src="images/secam.png" title="security camera monitoring" style="min-height:40px;width:40px;;margin-left:4px" />';

	  echo '</td>';
//    
    echo '<td><div style="float:right;padding:0;margin:0;font-size:.9em;color:#68AE00;">Reservations held for Move-in Date + '.$arr['reservationdays'].' days</div></td>';
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
    echo '</td></tr>';
}

?>
<div class="page-container">	
   <div class="left-content">
	   <div class="mother-grid-inner">
  		

<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<div class="blankpage-main" style="padding:1em 1em;">
			<br />
		<table id="datatable" class="table table-striped table-bordered" style="margin:0px;padding:0px;border:0px solid #000;" width="100%" cellspacing="0">
		<thead style="display:none;">
		<tr><th>Content</th></tr>
		</thead>
		<?php
			$query = "";
			$facility_id = $_POST['facility_id'];

			$ll = array();
			if((stristr($_SESSION['search'], "near") !== FALSE) && (isset($_POST['slat']) && isset($_POST['slng']))){
  			$query = "select *,(3959 * acos(cos(radians(".mysqli_real_escape_string($conn, $_POST['slat']).")) * cos(radians(lat)) * cos(radians(lng)- radians(".mysqli_real_escape_string($conn, $_POST['slng']).")) + sin(radians(".mysqli_real_escape_string($conn, $_POST['slat']).")) * sin(radians(lat)))) as calc_distance from facility_master where id='".mysqli_real_escape_string($conn, $facility_id)."'";
			}
			else if(is_numeric(isset($_POST['search'])?trim($_POST['search']):"")){
			  $ll = get_lat_lng($_POST['search']);
				$url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&address=".trim($_POST['search'])."&sensor=false";
				/*$result_string = file_get_contents_curl($url);
    		$result = json_decode($result_string, true);
    		$lat = $result['results'][0]['geometry']['location']['lat'];
    		$lng = $result['results'][0]['geometry']['location']['lng'];
    		$query = "select *,(3959 * acos(cos(radians(".mysqli_real_escape_string($conn, $lat).")) * cos(radians(lat)) * cos(radians(lng)- radians(".mysqli_real_escape_string($conn, $lng).")) + sin(radians(".mysqli_real_escape_string($conn, $lat).")) * sin(radians(lat)))) as calc_distance from facility_master where searchable=1 and lat is not null and lng is not null";*/
    		$query = "select * from facility_master where id='".mysqli_real_escape_string($conn, $facility_id)."'";
			}
			else{
			  $ll = get_lat_lng($_POST['search']);
				$query = "select * from facility_master where id='".mysqli_real_escape_string($conn, $facility_id)."'";
			}
			$res = mysqli_query($conn,$query);
      $results_arr = array();

			while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
			  $calc_distance = 0;
        if(isset($arr['calc_distance'])){
      		$calc_distance = round($arr['calc_distance'], 1);
      	}
        else{
    		  $calc_distance = calculate_distance_ll_ll($arr['lat'], $arr['lng'], $ll[0], $ll[1]);
    	  }

			  if($calc_distance >= 25)
			    continue;
        $arr['calc_distance'] = $calc_distance;
        $results_arr[] = $arr;
			}

			$num_results = count($results_arr);
			//for($i = 0; $i < $num_results; $i++){
			$i_for = min_ints(25, $num_results);
			for($i = 0; $i < $i_for; $i++){
			  show_results($results_arr[$i]);
			}
		?>
		</table>		
    	</div>
    </div>
</div>
<!--inner block end here-->
</div>
</div>

<!--scrolling js-->
		<!-- script src="facility/js/jquery.nicescroll.js"></script>
		<script src="facility/js/scripts.js"></script -->
		<!--//scrolling js-->
<!-- script src="facility/js/bootstrap.js"> </script -->
<!-- mother grid end here-->
</body>
</html>

