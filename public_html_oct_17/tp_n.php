<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body>

<?php
  session_start();
  include('sql.php');

  function extract_image_name($ss_path){
    // eg- //images.selfstorage.com/large-compress/108715518314b760ec6.jpg
    $lpos = strrpos($ss_path, "/");
    return trim(substr($ss_path, $lpos + 1));
  }

  function fetch_facility_images($facility_id){
    global $conn;
    $res = mysqli_query($conn,"select url_fullsize as url from image where facility_id='".$facility_id."'");
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
        'Store Your Valuables At This Secured And Clean Facility, Try now.',
        'Store Your Beloved Stuff At This Rock Solid Facility, Try Now.',
        'Affordable, Clean, And Secured, this facility Boasts Amenities second to none, Try Now.',
        'Abundant Amenities With NO Pain To Your Pocket, Try Now.',
        'Try this First Class Facility within Affordable Limits, Try Now.',
        'Relax, This Facility will Keep Your Stuff Protected and Preserved, Try Now',
        'Enjoy Your Life While We Work Hard To Protect and Preserve Your Stuff, Try Now',
        'Stop Worrying and Get On WIth Your Life, Try Now'
      );
      $i = 0;
      $arr_len = count($captionsArr);
      $num_images = mysqli_num_rows($res);
      while($arr = mysqli_fetch_array($res, MYSQLI_ASSOC)){
        $img_name = extract_image_name($arr['url']);
        echo '<div class="mySlides">';
        echo '<div class="numbertext">'.($i + 1).' / '.$num_images.'</div>';
        echo '<img src="images/'.$_GET['facility_id'].'/'.$img_name.'" height="400" width="400">';
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

