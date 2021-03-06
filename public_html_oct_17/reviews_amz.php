<?php
session_start();
include('sql.php');

function fetch_reviews($facility_id){
  global $conn;
  $res = mysqli_query($conn,"select * from review where facility_id='".$facility_id."'");
  return $res;
}

function show_stars($num_stars){
  $i = 1;
  while($i <= $num_stars){
    echo '<img src="images/bstar.png" height=15px width=15px>';
    $i++;
  }
  if($i - $num_stars < 1)
    echo '<img src="images/bstar_partial.png" height=15px width=15px>';
  
}

function reformat_date($txt){
  //echo '<b style="color:green">';
  $dt_parsed = date_create_from_format('Y-m-d', $txt);
  echo date_format($dt_parsed, 'M-d-Y');
  //echo '</b>';
}

?>
<!DOCTYPE html>
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
<div class="w3_banner" style="background:none;">
<nav class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <h1 style="color:#68AE00;"><a href="index.php">LEAZZER</a></h1>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    	<ul class="nav navbar-nav navbar-right">
    <?php 
    if(isset($_SESSION['lcdata']) || isset($_SESSION['lfdata']))
    	echo '<li><a href="index.php?action=logout">Logout</a></li>';
    ?>
      </ul>
    </div>
  </div>
</nav>
<div class="w3_bannerinfo">
<a href="index.php"><img src="images/llogo.png" height=120px></a>
<h3 style="margin-left:100px;text-align:left">Customer Reviews*</h3>
    <?php
      $res = fetch_reviews($_GET['facility_id']);
      while($arr = mysqli_fetch_array($res, MYSQLI_ASSOC)){
        echo '<table style="font-size: .9em;margin-bottom: 5px;margin-left: 75px;width:80%;box-shadow: 5px 5px 5px #888888;">';
        echo '<tr style="margin-left:0px;text-align:left"><td>';
        echo '<br /><br /><img src="images/anon.png" height=25px width=25px> '.$arr['nickname'].'<br />';
        show_stars($arr['rating']);
        echo $arr['title'].'<br />';
        reformat_date($arr['timestamp']);
        echo ' - <b style="color:green">Published on Leazzer.com</b><br />';
        echo $arr['message'];
        echo '</td></tr>';
        echo '</table>';
      }
    ?>
    
  <div style="font-size: .7em;margin-right:150px;text-align:right">*Based on reviews collected from third-party sites</div>
</div>

<div class="copyright" style="position:absolute;bottom:0">
	<div class="container">
		<p>© <?php echo date("Y",time());?> Leazzer. All rights reserved | <a href="/global_footer.php">Privacy Policy</a> | <a href="/global_footer_tu.php">Terms of use</a>
    </p>
	</div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.easing.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="admin/css/datepicker.css" />

<!-- Start of HubSpot Embed Code -->
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/5051579.js"></script>
<!-- End of HubSpot Embed Code -->

</body>
</html>

