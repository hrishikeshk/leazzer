<?php
session_start();
////  error_log('History fetch'.$_GET['loc']);
  $loc = $_GET['loc'];
?>

<html>
  <head>
    <link rel="icon" href="https://www.brainyvestors.com/images/llogo.jpg" type="image/jpg">
  </head>
  <body>
    <div>
      <?php
      if(isset($_SESSION['lcdata'])){
        echo '<iframe height=100% width=100% src="https://resources.nctcog.org/trans/data/trafficcounts/HistoricTrafficCounts_Report2.asp?id_loc='.$loc.'&unhide=1">';
        echo '</iframe>';
      }
      ?>
    </div>
  </body>
</html>

