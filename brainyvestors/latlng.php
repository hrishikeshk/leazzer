<?php

function file_get_contents_curl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function get_lat_lng($loc){
    $url = "https://maps.googleapis.com/maps/api/geocode/json?key=&address=".urlencode(trim($loc))."&sensor=false";
    ////$url = "https://maps.googleapis.com/maps/api/geocode/json?key=&address=".urlencode(trim($loc))."&sensor=false";
	  $result_string = file_get_contents_curl($url);
	  //error_log($result_string);
    $result = json_decode($result_string, true);
    $lat = $result['results'][0]['geometry']['location']['lat'];
    $lng = $result['results'][0]['geometry']['location']['lng'];
    return array($lat, $lng);
}

function read_file_json($path){
    $str = file_get_contents($path);
    if($str === FALSE){
        error_log("Failed to read file ... ".$path);
        die("Failed to read file ... ".$path);
    }
    else{
        $str_arr = json_decode($str, true);
        return $str_arr;
    }
}

  $arr = read_file_json("acs5.json");
  for($i = 1; $i < 5; $i++){
    $latlng = get_lat_lng($arr[$i][0]);
    echo $arr[$i][0]." : (".$latlng[0].", ".$latlng[1].")";
    sleep(1);
  }
?>

