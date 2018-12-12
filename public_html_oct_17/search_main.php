<?php
session_start();
//include('service_utils.php');

$GError = "";
$filter = array();

if((!isset($_POST['search'])) && isset($_SESSION['search']))
	$_POST['search']= $_SESSION['search'];
else if(isset($_POST['search']))
	$_SESSION['search']= $_POST['search'];

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

function file_get_contents_curl($url){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
  curl_setopt($ch, CURLOPT_URL, $url);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
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
<link href="facility/css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<link href="facility/css/style.css" rel="stylesheet" type="text/css" media="all"/>
<script src="facility/js/jquery-2.1.1.min.js"></script> 
<link href="facility/css/font-awesome.css" rel="stylesheet"> 
<link href='facility/fonts/fonts.css' rel='stylesheet' type='text/css'>
<script src="facility/js/Chart.min.js"></script>
<!--skycons-icons-->
<script src="facility/js/skycons.js"></script>
<link href="facility/css/demo-page.css" rel="stylesheet" media="all">
<link href="facility/css/hover.css" rel="stylesheet" media="all">

<script src="js/jquery.min.js"></script>
<script src="js/jquery.easing.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="admin/css/datepicker.css" />
<script type="text/javascript" src="admin/js/bootstrap-datepicker.js"></script>

<link href="admin/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="admin/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="admin/css/datepicker.css" />
<script type="text/javascript" src="admin/js/bootstrap-datepicker.js"></script>

<!--//skycons-icons-->
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
   		 	    alert('Failed to invoke serverside function(from search)... Please try again in some time' + err);
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
	  $resO = mysqli_query($conn,"select opt from options where id in(".$id_str.")");
	  while($arrO = mysqli_fetch_array($resO, MYSQLI_ASSOC)){
	    $ret[] = $arrO['opt'];
	  }
	  return $ret;
  }

  $filter_vals = array();
  if(isset($_SESSION['filter'])){
    $filter_vals = fetch_option_vals($_SESSION['filter']);
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
								<input name="search" type="text" placeholder="Zip or Address" value="<?php echo (isset($_POST['search'])?$_POST['search']:"");?>" required="" style="width:50%;display:inline;margin:0;">
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
      											$arr['id'].'" '.$checked.'>'.$arr['opt'].'<br>';
      							}
      						?>
      						<input type="hidden" name="search" id="search" value="<?php echo (isset($_POST['search'])?$_POST['search']:"");?>">
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
			   						' <a href="search_n.php?action=removefilter&id='.$_SESSION['filter'][$i].'" style="color:#68AE00;"><i class="fa fa-close"></i></a></div>';
			   						
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
			if(is_numeric(isset($_POST['search'])?trim($_POST['search']):"")){
				$url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&address=".trim($_POST['search'])."&sensor=false";
				$result_string = file_get_contents_curl($url);
    		$result = json_decode($result_string, true);
    		$lat = $result['results'][0]['geometry']['location']['lat'];
    		$lng = $result['results'][0]['geometry']['location']['lng'];
    		$query = "select *,(6371 * acos(cos(radians(".$lat.")) * cos(radians(lat)) * cos(radians(lng)- radians(".$lng.")) + sin(radians(".$lat.")) * sin(radians(lat)))) as calc_distance from facility_master where searchable=1 and title <> '' having calc_distance < 10000 order by calc_distance limit 100";
			}
			else if(strpos((isset($_POST['search'])?trim($_POST['search']):""),",") !== false){
				$searchArr = explode(",",trim($_POST['search']));
				$query = "select * from facility_master where searchable=1 and title <> '' and (title LIKE '%".(isset($searchArr[0])?trim($searchArr[0]):"")."%' OR city LIKE '%".(isset($searchArr[0])?trim($searchArr[0]):"")."%' or state LIKE '%".(isset($searchArr[0])?trim($searchArr[0]):"")."%') LIMIT 100";
			}
			else{
				$query = "select * from facility_master where searchable=1 and title <> '' and (title LIKE '%".(isset($_POST['search'])?trim($_POST['search']):"")."%' OR city LIKE '%".(isset($_POST['search'])?trim($_POST['search']):"")."%' or state LIKE '%".(isset($_POST['search'])?trim($_POST['search']):"")."%') order by title LIMIT 100";
			}
			
			$res = mysqli_query($conn,$query);
			
			$filter_dict_opts = array();
			if(count($filter) > 0)
			  $filter_dict_opts = calc_from_amenity_dict($filter);

			while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
        $facility_id = $arr['id'];				
				$facility_unit_amenities = fetch_facility_amenities($facility_id);

        if(count($filter_dict_opts) == 0 || eval_filters($facility_unit_amenities, $filter_dict_opts) == true){
    		  $arr_imgs = fetch_image_url($facility_id);
	    	  $unit_info_arr = fetch_units($facility_id);
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
	    			echo '<img src="https:'.$arr_imgs['url_thumbsize'].'" style="min-height:120px;width:120px;">';
	    		else
	    		  echo '<img src="unitimages/pna.jpg" style="min-height:120px;width:120px;">';
	  		
	    		echo '</a>';
	    		echo '<br><a href="javascript:showMorePhotos('.$facility_id.')">More Photos</a>';
	    		echo '</td>';
  
	    		echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
	  		
	    		echo '<table>';
	  		
	    		echo '<tr><td><b>'.$arr['title'].'</b><br>';
	    		echo $arr['city'].",".$arr['state']." ".$arr['zip'].'<br />';
	  		
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
        
          echo '</td><tr><td colspan=2 style="padding:0;border-left:1px solid #ddd;text-align:left">';
	  		
	    		echo '<div id="dateday_'.$facility_id.'" class="login-block" name="dateday_'.$facility_id.'" style="margin:0px;text-align:left;padding:0;">';
	    		echo '<p id="mdatemsg_'.$facility_id.'" style="display:none;color:#BB0000;font-size:.9em;margin:0;margin-left: 10px;padding:0;text-align:left;">Enter Move-In Date</p>';
	    		echo '<input class="datepicker" id="mdate_'.$facility_id.'" name="mdate_'.$facility_id.'" type="text" placeholder="Move-in Date"  style="width:200px;height:30px;padding:5px;margin:5px;font-size:.8em;"></div><br />';
						
	    		echo '</td><tr><td colspan=2 style="padding:0;border-left:1px solid #ddd;">';

	    		show_units($facility_id, $unit_info_arr, 5, $arr['reservationdays']);

	    		echo'</td></tr></table>';
  	  		echo '</td></tr>';
	  		}
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
		<script src="facility/js/jquery.nicescroll.js"></script>
		<script src="facility/js/scripts.js"></script>
		<!--//scrolling js-->
<script src="facility/js/bootstrap.js"> </script>
<!-- mother grid end here-->

</body>
</html>

