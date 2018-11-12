<?php
session_start();
include('sql.php');

function fetch_reviews($facility_id){
  global $conn;
  $res = mysqli_query($conn,"select * from review where facility_id='".$facility_id."'");
  return $res;
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
      <h1 style="color:#68AE00;"><a href="index_n.php">LEAZZER</a></h1>
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
<a href="index_n.php"><img src="images/llogo.png" height=120px></a>
<h3 style="margin:0;">Reviews (* Based on reviews collected from third-party sites)</h3>
  <table style="font-size: .9em;margin-bottom: 50px;margin-left: 75px;width:80%;box-shadow: 5px 5px 5px #888888;">
    <!-- tr>
      <td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">
        Rating
      </td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">
      <td>
        Title
      </td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">
      <td>
        Message
      </td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">
      <td>
        Excerpt
      </td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">
      <td>
        Nickname
      </td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">
      <td>
        Date
      </td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">
      <td>
        Stars
      </td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">
    </tr -->
    <?php
      $res = fetch_reviews($_GET['facility_id']);
      while($arr = mysqli_fetch_array($res, MYSQLI_ASSOC)){
        echo '<tr>';
        
        echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
          echo 'Rated '.$arr['rating'];
        echo '</td>';
        
        echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
          echo $arr['title'];
        echo '</td>';
        
        echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
          echo $arr['nickname'].' ( '.$arr['timestamp'].' ): <br />'.$arr['message'];
        echo '</td>';
        
        //echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
          //echo $arr['Excerpt'];
        //echo '</td>';
        
        //echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
          //echo $arr['nickname'];
        //echo '</td>';
        
        //echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
          //echo $arr['timestamp'];
        //echo '</td>';
        
        //echo '<td style="vertical-align:top;text-align:left;border-top:1px solid #ddd;padding: 10px 10px 0px 10px;">';
          //echo $arr['stars'];
        //echo '</td>';
        
        echo '</tr>';
      }
    ?>
  </table>
</div>

<div class="copyright">
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

