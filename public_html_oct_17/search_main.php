<?php
session_start();
//include('service_utils.php');

$GError = "";
$filter = array();

if((!isset($_POST['search'])) && isset($_SESSION['search']))
	$_POST['search']= $_SESSION['search'];
else if(isset($_POST['search']))
	$_SESSION['search']= htmlspecialchars($_POST['search'], ENT_QUOTES);

if(isset($_POST['action'])){

	if($_POST['action'] == "removefilter" && isset($_SESSION['filter'])){
	
		$newFilterArr = array();
 		for($i=0;$i<count($_SESSION['filter']);$i++){
 			if($_SESSION['filter'][$i] != $_POST['id'])
 				array_push($newFilterArr,$_SESSION['filter'][$i]);
		}
		$_SESSION['filter'] = $newFilterArr;
	}
}

if(isset($_POST['action'])){
	if($_POST['action'] == "applyfilter"){
	  if(isset($_POST['options']))
		  $_SESSION['filter'] = $_POST['options'];
		else if(isset($_POST['options_s']))
		  $_SESSION['filter'] = unserialize($_POST['options_s']);
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
	echo '<p style="font-size:.8em">';
	while($arrO = mysqli_fetch_array($resO,MYSQLI_ASSOC))
		echo $arrO['opt'].', ';
	echo "</p>";
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Leazzer" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- -->

<!--//skycons-icons-->
<script type="text/javascript">

function get_ll(position){
  if (navigator.geolocation){
  	navigator.geolocation.getCurrentPosition(showPosition,showError);
  }
	//load_ll(position.coords.latitude,position.coords.longitude);
}

function showPosition(position){
	load_ll(position.coords.latitude,position.coords.longitude);
}

function showError(error){
}

function load_ll(lat, lng){
  var ilat = document.getElementById('slat');
  var ilng = document.getElementById('slng');
  
  if(ilat != null && ilat != undefined && ilng != null && ilng != undefined){
    ilat.value=lat;
    ilng.value=lng;
  }
}

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
   		 	    alert('Failed to invoke serverside function(from search)... Please try again in some time');
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
  function fetch_option_vals($opt_ids){
    global $conn;
    $ret = array();
    if(count($opt_ids) == 0)
      return $ret;
    $id_str = $opt_ids[0];
    for($i = 1; $i < count($opt_ids); $i++)
      $id_str .= ', '.$opt_ids[$i];  
	  $resO = mysqli_query($conn,"select opt from options where id in(".mysqli_real_escape_string($conn, $id_str).")");
	  while($arrO = mysqli_fetch_array($resO, MYSQLI_ASSOC)){
	    $ret[] = $arrO['opt'];
	  }
	  return $ret;
  }

  $filter_vals = array();
  if(isset($_SESSION['filter'])){
    $filter_vals = fetch_option_vals($_SESSION['filter']);
  }

function show_results($arr, $filter_dict_opts){
  $calc_distance = $arr['calc_distance'];
  $facility_id = htmlspecialchars($arr['id'], ENT_QUOTES);
	$facility_unit_amenities = fetch_facility_amenities($facility_id, $arr);

  if(count($filter_dict_opts) == 0 || eval_filters($facility_unit_amenities, $filter_dict_opts) == true){

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
////
	  echo '<div id="dateday_'.$facility_id.'" class="login-block" name="dateday_'.$facility_id.'" style="margin:0px;text-align:left;padding:0;">';
	  echo '<p id="mdatemsg_'.$facility_id.'" style="display:none;color:#BB0000;font-size:.9em;margin:0;margin-left: 10px;padding:0;text-align:left;">Enter Move-In Date</p>';
	  echo '<input class="datepicker" id="mdate_'.$facility_id.'" name="mdate_'.$facility_id.'" type="text" placeholder="Move-in Date"  style="width:200px;height:30px;padding:5px;margin:5px;font-size:.8em;"></div><br />';
////
	  echo '</td>';
    
	  echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
	    		
	  echo '<table>';

	  echo '<tr><td><b>'.htmlspecialchars($arr['title'], ENT_QUOTES).'</b><br>';
	  echo htmlspecialchars($arr['city'].",".$arr['state']." ".$arr['zip'], ENT_QUOTES).'<br />';

	  if($calc_distance > 0)
      echo $calc_distance.' miles away<br />';
    else
      echo '0.1 miles away<br />';

	  if(has_priority_amenity($facility_unit_amenities, array('climate')))
	    echo '<img src="images/cc.jpg" title="climate control equipped" style="min-height:40px;width:40px;" />';

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
}

function cmp($a, $b) {
  if($a['calc_distance'] < $b['calc_distance'])
    return -1;
  return 1;
}

?>
<div class="page-container">	
   <div class="left-content">
	   <div class="mother-grid-inner">
            <!--header start here-->
				<div class="header-main">
					<div class="header-left" style="width:100%;">
							<div class="logo-name login-block"  style="width:100%;padding:0;margin:0;">
								<form method="post" action="search_n.php" enctype="multipart/form-data">
								<center>
								<a href="index.php" style="display:inline;float:left;"><img id="logo" src="images/llogo.png" style="display:inline;width:40px;" alt="Logo"/></a>
								<input name="search" type="text" placeholder="Near me, City or Zip" value="<?php echo (isset($_POST['search'])?$_POST['search']:"");?>" required="" style="width:50%;display:inline;margin:0;">
								<input name="slat" id="slat" type="hidden" value="" />
								<input name="slng" id="slng" type="hidden" value="" />
								<button onClick="fd_show();" type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;width:100%;margin: 0 auto;border-radius: 3px;padding: 0.3em 0.2em;font-size: 1.3em;display: block;font-family: 'Carrois Gothic', sans-serif;width:50px;display:inline;"><i class="fa fa-filter"></i></button>
								<!--button data-toggle="modal" data-target="#myModal" type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;width:100%;margin: 0 auto;border-radius: 3px;padding: 0.3em 0.2em;font-size: 1.3em;display: block;font-family: 'Carrois Gothic', sans-serif;width:50px;display:inline;"><i class="fa fa-filter"></i></button -->
								<button type="submit" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;width:100%;margin: 0 auto;border-radius: 3px;padding: 0.3em 0.2em;font-size: 1.3em;display: block;font-family: 'Carrois Gothic', sans-serif;width:50px;display:inline;"><i class="fa fa-search"></i></button>
								</center>
								</form>
							</div>
							<div class="clearfix"> </div>
						 </div>
				     <div class="clearfix"> </div>	
				</div>
<!--header end here-->
  		<!---START-->
				<div id="myModal" class="modal" role="dialog" style="display:none;">
				  <div class="modal-dialog">
				    <!-- Modal content-->
				    <form method="post" action="search_n.php" enctype="multipart/form-data">
				    <div class="modal-content">
				      <div class="modal-body">
				        <p>
      						<hr style="margin:5px 0px 5px 0px">
      						<b>Filter Features</b>
      						<hr style="margin:5px 0px 5px 0px">
      						<?php
      							$res = mysqli_query($conn,"select * from options");
      							while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
      								$checked = "";
      								if(isset($_SESSION['filter']) && (in_array($arr['id'], $_SESSION['filter'])))
      									$checked = "checked";
      								echo '<input type="checkbox" style="margin-right:5px;" name="options[]" value="'.
      											$arr['id'].'" '.$checked.'>'.htmlspecialchars($arr['opt'], ENT_QUOTES).'<br>';
      							}
      						?>
      						<input type="hidden" name="search" id="search" value="<?php echo (isset($_POST['search'])?htmlspecialchars($_POST['search'], ENT_QUOTES):"");?>">
      						<input type="hidden" name="action" id="action" value="applyfilter">
				        </p>
				      </div>
				      <div class="modal-footer">
				      	<button class="btn btn-primary" name="submit" id="submit" value="Create" style="background:#68AE00;border-color:#68AE00;">Apply Filter</button>
				        <button type="button" class="btn btn-danger" onClick="fd_hide();">Close</button>
				        <!-- button type="button" class="btn btn-danger" data-dismiss="modal">Close</button -->
				      </div>
				    </div>
						</form>
				  </div>
				</div>
			<!---END-->

<!-- script-for sticky-nav -->
		<script>
		$(document).ready(function(){
			 var navoffeset=$(".header-main").offset().top;
			 $(window).scroll(function(){
				var scrollpos=$(window).scrollTop(); 
				if(scrollpos >=navoffeset){
					$(".header-main").addClass("fixed");
				}else{
					$(".header-main").removeClass("fixed");
				}
			 });
			 get_ll();
		});
		</script>
<!-- /script-for sticky-nav -->
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<div class="blankpage-main" style="padding:1em 1em;">
			<?php
			   if(isset($_SESSION['filter'])){
			   		if(count($filter_vals)> 0)
			   		  $filter=array();
			   		for($i=0;$i<count($filter_vals);$i++){
			   			echo '<div style="background:#eee;display:inline-block;padding:5px;margin:2px;">'.
			   						$filter_vals[$i].
			   						' <a href="search_n.php?action=removefilter&id='.htmlspecialchars($_SESSION['filter'][$i], ENT_QUOTES).'" style="color:#68AE00;"><i class="fa fa-close"></i></a></div>';

			   			$filter[] = $_SESSION['filter'][$i];
			   		}

			   		if(count($_SESSION['filter'])> 0)
			   			echo "<br><br>";
			   }
			?>
			<br />
		<table id="datatable" class="table table-striped table-bordered" style="margin:0px;padding:0px;border:0px solid #000;" width="100%" cellspacing="0">
		<thead style="display:none;">
		<tr><th>Content</th></tr>
		</thead>
		<?php
			$query = "";
			if((stristr($_POST['search'], "near") !== FALSE) && (isset($_POST['slat']) && isset($_POST['slng']))){
  			$query = "select *,(3959 * acos(cos(radians(".mysqli_real_escape_string($conn, $_POST['slat']).")) * cos(radians(lat)) * cos(radians(lng)- radians(".mysqli_real_escape_string($conn, $_POST['slng']).")) + sin(radians(".mysqli_real_escape_string($conn, $_POST['slat']).")) * sin(radians(lat)))) as calc_distance from facility_master having calc_distance < 25 and searchable=1  and city is not null and state is not null and lat is not null and lng is not null order by calc_distance limit 10";
			}
			else if(is_numeric(isset($_POST['search'])?trim($_POST['search']):"")){
				$url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&address=".trim($_POST['search'])."&sensor=false";
				$result_string = file_get_contents_curl($url);
    		$result = json_decode($result_string, true);
    		$lat = $result['results'][0]['geometry']['location']['lat'];
    		$lng = $result['results'][0]['geometry']['location']['lng'];
    		$query = "select *,(3959 * acos(cos(radians(".mysqli_real_escape_string($conn, $lat).")) * cos(radians(lat)) * cos(radians(lng)- radians(".mysqli_real_escape_string($conn, $lng).")) + sin(radians(".mysqli_real_escape_string($conn, $lat).")) * sin(radians(lat)))) as calc_distance from facility_master where searchable=1 and lat is not null and lng is not null having calc_distance < 25 order by calc_distance limit 25";
			}
			else if(strpos((isset($_POST['search'])?trim($_POST['search']):""),",") !== false){
				$searchArr = explode(",",trim($_POST['search']));
				$query = "select * from facility_master where searchable=1 and lat is not null and lng is not null and (title LIKE '%".(isset($searchArr[0])?trim(mysqli_real_escape_string($conn, $searchArr[0])):"")."%' OR city LIKE '%".(isset($searchArr[0])?trim(mysqli_real_escape_string($conn, $searchArr[0])):"")."%' or state LIKE '%".(isset($searchArr[0])?trim(mysqli_real_escape_string($conn, $searchArr[0])):"")."%') LIMIT 25";
			}
			else{
				$query = "select * from facility_master where searchable=1 and lat is not null and lng is not null and (title LIKE '%".(isset($_POST['search'])?trim(mysqli_real_escape_string($conn, $_POST['search'])):"")."%' OR city LIKE '%".(isset($_POST['search'])?trim(mysqli_real_escape_string($conn, $_POST['search'])):"")."%' or state LIKE '%".(isset($_POST['search'])?trim(mysqli_real_escape_string($conn, $_POST['search'])):"")."%') order by title LIMIT 25";
			}
			$res = mysqli_query($conn,$query);

			$filter_dict_opts = array();
			if(count($filter) > 0)
			  $filter_dict_opts = calc_from_amenity_dict($filter);

      $results_arr = array();
			while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
			  $calc_distance = 0;
        if(isset($arr['calc_distance']) && $arr['calc_distance'] > 0){
      		$calc_distance = round($arr['calc_distance'], 1);
      	}
      	else if(is_null($arr['lat']) == false && is_null($arr['lng']) == false && is_numeric($arr['lat']) && is_numeric($arr['lng'])){
    		  $calc_distance = calculate_distance_ll($arr['lat'], $arr['lng'], $_POST['search']);
    	  }
			  if($calc_distance >= 25)
			    continue;
        $arr['calc_distance'] = $calc_distance;
        $results_arr[] = $arr;
			}
			
			usort($results_arr, 'cmp');
			
			$num_results = count($results_arr);
			for($i = 0; $i < $num_results; $i++){
			  show_results($results_arr[$i], $filter_dict_opts);
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

