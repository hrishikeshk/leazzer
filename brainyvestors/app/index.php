<?php
include ('sql.php');

function fetch_bv_app_url(){
	global $conn;
	$ret = "";
	$res = mysqli_query($conn,"select * from admin_configuration where name='bv_app_url'");
	if(mysqli_num_rows($res) > 0){
		$arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
		$ret .= $arr['data_value'];	
	}
	else{
		$ret = "TODO: Paste here privacy policy (HTML text) ...";
	}
	return htmlentities($ret);
}

header("Location: ".fetch_bv_app_url());
/*
if((!isset($_SERVER['HTTPS'])) || ($_SERVER['HTTPS'] != "on"))
{
	$url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	header("Location: $url");
	exit;
}
*/

?>
<!-- 
<!DOCTYPE html>
<html>
<head>
<title>Brainyvestors</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<meta name="keywords" content="Brainyvestors" />
</head>
<body>

We, at brainyvestors, provide quick feasibility for Commercial Real Estate such as Multifamily, Self Storage, Retail and/or Office Buildings.


Keywords - Feasibility study, Commercial Real Estate, Multifamily, Self Storage, Office, Retail complex

</body>
</html>
-->

