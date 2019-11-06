<?php
session_start();
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
    
    ////$result_string = error_log('CURL call: '.$url);
	  ////$result_string = file_get_contents_curl($url);
    return '';
    ////return $result_string;
}

if(isset($_SESSION['lcdata'])){
  if(isset($_GET['aadtproxy']) && strlen($_GET['aadtproxy']) > 0){
    $address = $_GET['aadtproxy'];
    $address = str_replace(';', '&', $address);
    ////$aadts = error_log('CURL Call: '.$address);
    $aadts = file_get_contents_curl('https://resources.nctcog.org/trans/data/trafficcounts/'.$address);
    echo $aadts;
  }
}

?>

