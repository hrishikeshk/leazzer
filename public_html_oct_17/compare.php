<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<meta name="keywords" content="Leazzer" />
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all">
<link href="css/owl.carousel.css" rel="stylesheet">
<link rel="stylesheet" href="css/jquery-ui.css" />
<link rel="stylesheet" href="css/chocolat.css" type="text/css">
<link href="facility/css/style.css" rel="stylesheet" type="text/css" media="all"/>
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<link href="fonts/fonts.css" rel="stylesheet">

</head>
<body>
<nav class="navbar navbar-default">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">

 	  <a href="javascript:history.go(-1)" style="display:inline;float:left;"><img id="logo" src="images/llogo.png" style="display:inline;width:50px;" alt="Logo"/>
 	  </a>
 	  <h1 style="margin-left:75px; color:#1122dd">Comparing Facilities</h1>
 	</div>
	</div>
</nav>

<div class="container">
<?php
session_start();
include('sql.php');
include('service_utils.php');

$facs_arr = array();
function get_amenities($facility_id){
  global $conn, $facs_arr;
  $query = "select * from facility_master where id='".$facility_id."'";
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

$amenities = array();
$fac_arr = array();
$ct = 0;
if(isset($_GET['facs'])){
  $fac_arr = explode('|', $_GET['facs']);
  $ct = count($fac_arr);
  if($ct > 4)
    $ct = 4;
  for($i = 0; $i < $ct; $i++){
    $amenities[$fac_arr[$i]] = get_amenities($fac_arr[$i]);
  }
}

?>
<table>
<thead>
<tr>
  <th>Quality</th>
  <?php
    for($i = 0; $i < $ct; $i++){
      echo '<th>'.$facs_arr[$fac_arr[$i]]['title'].'</th>';
    }
  ?>
</tr>
</thead>
<tbody>
<tr>
    <td>Climate Control</td>
    <?php
    for($i = 0; $i < $ct; $i++){
      ////if(has_priority_amenity($amenities[$fac_arr[$i]], array('climate')))
      if(fetch_has_facility_cc($fac_arr[$i]))
	      echo '<td><img src="images/gtick.png" title="climate control equipped" style="vertical-align: left;width:10px;height:10px" /></td>';
	    else
	      echo '<td><img src="images/rcross.png" style="vertical-align: left;width:10px;height:10px" /></td>';
    }
    ?>
<tr>
<tr>
    <td>Security / Surveillance</td>
    <?php
    for($i = 0; $i < $ct; $i++){
      if(has_priority_amenity($amenities[$fac_arr[$i]], array('security', 'camera', 'video camera')))
	      echo '<td><img src="images/gtick.png" title="security enhancements equipped" style="vertical-align: left;width:10px;height:10px" /></td>';
	    else
	      echo '<td><img src="images/rcross.png" style="vertical-align: left;width:10px;height:10px" /></td>';
    }
    ?>
<tr>
</tbody>
</table>

<!--scrolling js-->
		<script src="js/jquery.nicescroll.js"></script>
		<script src="js/scripts.js"></script>
<!--//scrolling js-->
<script src="js/bootstrap.js"> </script>
<br /><br />
<p style="margin-left:75px">
<a href="index.php"><img id="logo" src="images/llogo.png" style="display:inline;width:40px;" alt="Logo"/>
<a href="javascript:history.go(-1)">Back<a/>
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

