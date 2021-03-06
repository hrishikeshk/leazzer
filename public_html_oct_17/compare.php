<?php
session_start();
?>
<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<meta name="keywords" content="Leazzer" />
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/jquery.easing.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all">
<link href="css/owl.carousel.css" rel="stylesheet">
<link rel="stylesheet" href="css/jquery-ui.css" />
<link rel="stylesheet" href="css/chocolat.css" type="text/css">
<link href="facility/css/style.css" rel="stylesheet" type="text/css" media="all"/>
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<link href="fonts/fonts.css" rel="stylesheet">
<link href="admin/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="admin/js/jquery.dataTables.min.js"></script>

</head>
<body>
<nav class="navbar navbar-default">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">

   	  <a href="javascript:history.go(-1)" style="display:inline;float:left;"><img id="logo" src="images/llogo.png" style="display:inline;width:50px;" alt="Logo"/>
 	    </a>
 	    <h1 style="margin-left:75px; color:#1122dd">Compare Facilities</h1>
 	  </div>
 	  <p style="float:right; text-align:right">
      <a href="javascript:history.go(-1)">Back To Search<a/>
    </p>
	</div>
</nav>

<div class="container">
<?php
include('sql.php');
include('service_utils.php');

$option_ids = array();
$option_names = array();

$res = mysqli_query($conn,"select * from options");
$option_ids = array();
while($arr = mysqli_fetch_array($res, MYSQLI_ASSOC)){
  $option_ids[] = $arr['id'];
  $option_names[] = $arr['opt'];
}
$option_ct = count($option_ids);

$facs_arr = array();
$amenity_kinds = array();
$facs_master = array();

function get_rep_image($facility_id, $ct){
  global $conn;
  $query = "select url_fullsize from image where facility_id='".mysqli_real_escape_string($conn, $facility_id)."' limit 1";
  $res = mysqli_query($conn, $query);
  if(mysqli_num_rows($res) == 0)
    return '<img src="unitimages/pna.jpg" style="min-height:90%;width:90%;">';
  else{
    $arr_imgs = mysqli_fetch_array($res, MYSQLI_ASSOC);
    $image_file_name = extract_image_name($arr_imgs['url_fullsize']);
	  $expected_image_path = "images/".$facility_id."/".$image_file_name;
    return '<img src="'.$expected_image_path.'" style="min-height:90%;width:90%;">';
  }
}

function get_facs_master($facility_id){
  global $conn, $facs_master;
  $query = "select * from facility_master where id='".mysqli_real_escape_string($conn, $facility_id)."' limit 1";
  $res = mysqli_query($conn, $query);
  if(mysqli_num_rows($res) == 0)
    return array();
  else{
    $arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
    $facs_master[$facility_id] = $arr;
    return $facs_master;
  }
}

function get_amenities($facility_id){
  global $conn, $facs_arr;
  $query = "select * from facility_master where id='".mysqli_real_escape_string($conn, $facility_id)."' limit 1";
  $res = mysqli_query($conn, $query);
  if(mysqli_num_rows($res) == 0)
    return array();
  else{
    $arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
    $facs_arr[$facility_id] = $arr;
    //error_log('fac id store: '.$arr['title'].' and actual: '.$facs_arr[$facility_id]['title'].' with fac id: '.$facility_id);
    $facility_unit_amenities = fetch_facility_amenities($facility_id, $arr);
    return arrange_priority_with_group($facility_unit_amenities);
  }
}

function sanitize_amenities($facility_id){
  global $conn;

  $res = mysqli_query($conn,"select amenity from facility_amenity where facility_id='".mysqli_real_escape_string($conn, $facility_id)."'") or die('Failed to fetch facility amenities.');
  $ams = array();
	while($arr = mysqli_fetch_array($res, MYSQLI_ASSOC)){
	  $ams_pa = explode("|", $arr['amenity']);
	  $curr = '';
	  if(count($ams_pa) == 1){
	    $curr = $ams_pa[0];
	  }
	  else{
	    $curr = $ams_pa[1];
	  }
    $res_opts = mysqli_query($conn, "select option_id as oid from amenity_dictionary where (equivalent like '%".mysqli_real_escape_string($conn, $curr)."%' OR INSTR('".mysqli_real_escape_string($conn, $curr)."', equivalent) > 0) and equivalent is not null and LENGTH(equivalent) > 0") or die('Failed to match facility amenities.');
    while($arr = mysqli_fetch_array($res_opts, MYSQLI_ASSOC)){
      $ams[] = $arr['oid'];
    }
	}
	return $ams;
}

function get_option_ids(){
  $res = mysqli_query($conn,"select * from options");
  $option_ids = array();
	while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
	  $option_ids[] = $arr['id'];
	}
	return $option_ids;
}

$amenities = array();
$fac_arr = array();
$ct = 0;
if(isset($_GET['facs'])){
  $fac_arr = explode('|', $_GET['facs']);
  $ct = count($fac_arr);
  if($ct > 3)
    $ct = 3;
  for($i = 0; $i < $ct; $i++){
    get_facs_master($fac_arr[$i]);
    $am_arr = get_amenities($fac_arr[$i]);
    $amenity_kinds = array_merge(array_keys($am_arr), $amenity_kinds);
    $amenities[$fac_arr[$i]] = $am_arr;
  }
}

?>
<table id="datatable" style="margin:10px;padding:10px;" width="100%" cellspacing="0">
<thead>
<tr>

  <?php
    for($i = 0; $i < $ct; $i++){
      echo '<th style="margin:10px;padding:10px;width:'.(100/$ct).'%">'.get_rep_image($fac_arr[$i], $ct).'</th>';
      //echo $facs_arr[$fac_arr[$i]]['title'].'</th>';
    }
  ?>
</tr>
<tr>
  <?php
    for($i = 0; $i < $ct; $i++){
      //echo '<th>'.get_rep_image($fac_arr[$i]).'</th>';
      echo '<th class="header-main" style="text-align:center;margin:10px;padding:10px;width:'.(100/$ct).'%">'.$facs_arr[$fac_arr[$i]]['title'].'</th>';
    }
  ?>
</tr>
</thead>
<tbody>
<tr>
<?php
    for($i = 0; $i < $ct; $i++){
      echo '<td style="margin:10px;padding:10px;;width:'.(100/$ct).'%">'.$facs_master[$fac_arr[$i]]['description'].'</td>';
    }
    ?>
</tr>

<?php
    $cc_arr = array();
    $show_cc = false;
    for($i = 0; $i < $ct; $i++){
      if(fetch_has_facility_cc($fac_arr[$i])){
	      $cc_arr[] = true;
	      $show_cc = true;
	    }
	    else
	      $cc_arr[] = false;
    }
    if($show_cc === true){
      echo '<tr>';
      for($i = 0; $i < $ct; $i++){
        if($cc_arr[$i] === true)
          echo '<td style="margin:10px;padding:10px;;width:'.(100/$ct).'%">Climate Control Equipped</td>';
	      else
          echo '<td></td>';
      }
      echo '</tr>';
    }
?>
<!-- tr>
    <td>Security / Surveillance</td>
    <?php
    for($i = 0; $i < $ct; $i++){
      if(has_priority_amenity($amenities[$fac_arr[$i]], array('security', 'camera', 'video camera')))
	      echo '<td><img src="images/gtick.png" title="security enhancements equipped" style="vertical-align: left;width:10px;height:10px" /></td>';
	    else
	      echo '<td><img src="images/rcross.png" style="vertical-align: left;width:10px;height:10px" /></td>';
    }
    ?>
<tr -->
<script>
function res_do(facility_id){
  /*var res;
    $.ajax
    ({	
    		type:"POST",
    		url:"csearch_n.php",
    		data:"facility_id=" + facility_id,
    		cache:false,
    		async:false,
    		success: function(result){		
   				 	res = result;
   		 	},
   		 	error: function(err){
   		 	    alert('Failed to invoke serverside function(from scompare)... Please try again in some time');
   		 	    res = false;
   		 	}
    });
    return res;
    */
  window.location.href='csearch_n.php?facility_id='+facility_id;
}

</script>
<?php
  $fac_ams_markers = array();
  for($i = 0; $i < $ct; $i++){
    $fac_ams_markers[$fac_arr[$i]] = sanitize_amenities($fac_arr[$i]);
  }

  for($i = 0; $i < $option_ct; $i++){
    
    //echo '<td>'.$option_names[$i].'</td>';
    //echo '<td></td>';
    $show_r = false;
    $nm_arr = array();
    for($j = 0; $j < $ct; $j++){
      if(in_array($option_ids[$i], $fac_ams_markers[$fac_arr[$j]])){
        $show_r = true;
        $nm_arr[] = $option_names[$i];
        //echo '<img src="images/gtick.png" title="security enhancements equipped" style="vertical-align: left;width:10px;height:10px" />';
      }
      else{
        //echo '<img src="images/rcross.png" style="vertical-align: left;width:10px;height:10px" />';
        $nm_arr[] = '';
      }
    }
    if($show_r === true){
      echo '<tr>';
      for($j = 0; $j < $ct; $j++){
        echo '<td style="margin:10px;padding:10px;;width:'.(100/$ct).'%">';
          echo $nm_arr[$j];
          //echo '<img src="images/gtick.png" title="security enhancements equipped" style="vertical-align: left;width:10px;height:10px" />';
        echo '</td>';
      }
      echo '</tr>';
    }
  }
  echo '<tr>';
  for($i = 0; $i < $ct; $i++){
    echo '<td>';
    
    echo '<button onClick="res_do('.$fac_arr[$i].');" type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;margin: 0 auto;border-radius: 3px;padding: 0.3em 0.2em;font-size: 1.2em;font-family: \'Carrois Gothic\', sans-serif;"><i class="fa">Reserve</i></button>';
    
    echo '</td>';
  }
  echo '</tr>';
?>
</tbody>
</table>

<br /><br />
<p style="margin-left:75px">
<a href="index.php"><img id="logo" src="images/llogo.png" style="display:inline;width:40px;" alt="Logo"/>
<a href="javascript:history.go(-1)">Back To Search<a/>
</p>
<br /><br />
<div class="copyright">
	<div class="container">
		<p>© <?php echo date("Y",time());?> Leazzer. All rights reserved | <a href="/global_footer.php">Privacy Policy</a> | <a href="/global_footer_tu.php">Terms of use</a>
    </p>
	</div>
</div>

</body>
</html>

