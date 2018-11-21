<?php
session_start();
include('sql.php');
$GError = "";
$filter = "";

if(isset($_POST['facility_id'])){
  echo "For facility : ".$_POST['facility_id'];
}

if(isset($_GET['q'])){
  $q=$_GET['q'];
  $res_fac = mysqli_query($conn,"select * from image where facility_id='$q'");
}

if((!isset($_POST['search'])) && isset($_SESSION['search'])){
	$_POST['search']= $_SESSION['search'];
}
else if(isset($_POST['search'])){
	$_SESSION['search']= $_POST['search'];
}

if(isset($_GET['action'])){
	if($_GET['action'] == "removefilter" && isset($_SESSION['filter'])){
		$newFilterArr = array();			
 		for($i=0;$i<count($_SESSION['filter']);$i++){
 			$filterArr = explode("[-]",$_SESSION['filter'][$i]);
 			if($filterArr[0] != $_GET['id'])
 				array_push($newFilterArr,$_SESSION['filter'][$i]);
		}			
		$_SESSION['filter'] = $newFilterArr;
	}
}
if(isset($_POST['action'])){
	if($_POST['action'] == "applyfilter"){
		$_SESSION['filter'] = $_POST['options'];
	}
}

function showUnit($arr){
	global $conn;
	$unitIds="";
	$unitPrice="";
	
	$unitArr = explode(",",$arr['units']);
	for($i=0;$i<count($unitArr);$i++)
	{
		if(trim($unitArr[$i]) == "")
		continue;
		
		$unitSubArr = explode("-",$unitArr[$i]);
		$unitIds .= $unitSubArr[0].",";
		$unitPrice.= $unitSubArr[1].",";
	}
	$unitIds = substr($unitIds,0,strlen($unitIds)-1);
	$unitPrice = substr($unitPrice,0,strlen($unitPrice)-1);
	$unitPriceArr = explode(",",$unitPrice);
	$resU = mysqli_query($conn,"select * from units where id in(".$unitIds.")");
	echo '<div style="border:0px solid red;width:70%;height:150px;"  id="unitstbl_'.$arr['id'].'">';
	$cnt = 0;
	while($arrU = mysqli_fetch_array($resU,MYSQLI_ASSOC))
	{
		echo '<div class="col-md-1" style="text-align: center;padding:10px;width="28%";border:0px solid #000;box-shadow: 0px 0px 3px #888888;">';
		echo '<img src="unitimages/'.($arrU['images']==""?"pna.jpg":$arrU['images']).'" style="vertical-align: top;width:50px;height:50px">';
		echo '<p style="width:80px;display:inline-block;padding:0px 10px 0px 10px;margin:0;font-size:.8em;white-space: nowrap;"><b>'.$arrU['units'].'</b><br>$'.$unitPriceArr[$cnt].'</p>';
		echo '<button type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;margin: 0 auto;border-radius: 3px;font-size: 1.0em;width:80px;display:inline;padding:0px;" onClick="onUnitClick(this,'.
										(isset($_SESSION['lcdata'])?$_SESSION['lcdata']['id']:"0").','.
										$arr['id'].','.
										$arr['reservationdays'].',\''.
										urlencode($arrU['units']).'\',\''.
										$unitPriceArr[$cnt].'\');">Reserve</button></div>';
		$cnt++;
	}
	echo "</div>";
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

function file_get_contents_curl($url) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
curl_setopt($ch, CURLOPT_URL, $url);
$data = curl_exec($ch);
curl_close($ch);
return $data;
}
?>

