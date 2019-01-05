<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body id="innerBody">

<?php
  session_start();
  include('sql.php');

  function extract_image_name($ss_path){
    // eg- //images.selfstorage.com/large-compress/108715518314b760ec6.jpg
    $lpos = strrpos($ss_path, "/");
    return htmlspecialchars(trim(substr($ss_path, $lpos + 1)), ENT_QUOTES);
  }

  function fetch_facility_images($facility_id){
    global $conn;
    $res = mysqli_query($conn,"select url_fullsize as url from image where facility_id='".mysqli_real_escape_string($conn, $facility_id)."'");
    if(mysqli_num_rows($res) > 0){
      return $res;
    }
    return false;
  }

  if(isset($_GET['facility_id'])){
    $res = fetch_facility_images($_GET['facility_id']);
    if($res == false){
      echo '<div class="mySlides fade">';
      echo '<img src="unitimages/pna.png" style="width:100%">';
      echo '<div class="slidertext">Image unavailable. Reserve directly !</div>';
      echo '</div>';
    }
    else{
      $captionsArr = Array(
        'Store your Valuables at this Secured and Clean Facility',
        'Store your Beloved Stuff at this Rock Solid Facility',
        'Affordable, Clean and Secured, this facility Boasts Amenities second to none',
        'Abundant Amenities with NO Pain to your Pocket',
        'Try this First Class Facility within Affordable Limits',
        'Relax, This Facility will keep your Stuff Protected and Preserved',
        'Enjoy your Life while we Work Hard to Protect and Preserve your Stuff',
        'Stop Worrying and Get On with your Life'
      );
      $i = 0;
      $arr_len = count($captionsArr);
      $num_images = mysqli_num_rows($res);
      while($arr = mysqli_fetch_array($res, MYSQLI_ASSOC)){
        $img_name = extract_image_name($arr['url']);
        echo '<div class="mySlides">';
        echo '<div class="numbertext">'.($i + 1).' / '.$num_images.'</div>';
        echo '<img src="images/'.$_GET['facility_id'].'/'.$img_name.'" style="width:100%; height:100%">';
        echo '<div class="slidertext">'.$captionsArr[$i].'</div>';
        $i++;
        $i = $i % $arr_len;
        echo '</div>';
      }
    }
  }
?>

<br>

</body>
</html> 

