<?php
include('../sql.php');
mysqli_set_charset($conn,"utf8");

function merge_dcnames($res){
  $ret = '';
  if(mysqli_num_rows($res) == 0)
    return $ret;
  $arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
  $ret = $arr['equivalent'];
  while($arr = mysqli_fetch_array($res, MYSQLI_ASSOC)){
    $ret .= ', '.$arr['equivalent'];
  }
  return $ret;
}

function gen_address($street, $region, $zip, $state, $city){
  return $street.', '.$region.', '.$city.' '.$state.' - '.$zip;
}

if(isset($_POST['action'])){
	if($_POST['action'] == "getfacility"){
		$res = mysqli_query($conn,"select O.emailid as emailid, O.logintype as logintype, M.title as title, O.phone as phone, M.street as street, M.region as region, M.zip as zip, M.state as state, M.city as city, M.status as status, M.searchable as searchable from facility_master M, facility_owner O where M.facility_owner_id=O.auto_id and M.id='".$_POST['id']."'");
		if($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
			echo $arr['emailid']."[-]".$arr['logintype']."[-]".$arr['title']."[-]".$arr['phone']."[-]".$arr['street'].
					 "[-]".$arr['city']."[-]".$arr['state']."[-]".$arr['zip']."[-]".$arr['searchable']."[-]".$arr['status'];
		}
	}
		if($_POST['action'] == "getcard"){
		$res = mysqli_query($conn,"select  M.title as title, C.stripe_id as stripe_id from facility_master M, facility_owner O, owner_card C where M.facility_owner_id=O.auto_id and O.auto_id=C.owner_id and M.id='".$_POST['id']."'");
		if(mysqli_num_rows($res) == 0){
		  echo "[-]";
		}
		else if($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
			echo $arr['stripe_id']."[-]".$arr['title'];
		}
	}
	if($_POST['action'] == "getcustomer"){
		$res = mysqli_query($conn,"select * from customer where id='".$_POST['id']."'");
		if($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
			echo $arr['emailid']."[-]".$arr['firstname']."[-]".$arr['lastname']."[-]".$arr['phone']."[-]".$arr['status'];
		}	
	}
	
	if($_POST['action'] == "getcustomerReserves"){
		$res = mysqli_query($conn, "select distinct M.title as facility_name, M.id as facility_id, C.firstname as firstname, C.lastname as lastname, C.emailid as emailid, C.id as cid from reserve R, customer C, facility_master M where R.cid=C.id and R.fid=M.id and R.cid='".$_POST['customerid']."'");
		$ret = array();
		while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
			//echo $arr['facility_name']."[-]".$arr['facility_id']."[-]".$arr['firstname']."[-]".$arr['lastname']."[-]".$arr['emailid'];
			$row = array($arr['facility_name'], $arr['facility_id'], $arr['firstname'], $arr['lastname'], $arr['emailid'], $arr['cid']);
			$ret[] = $row;
		}
		echo json_encode($ret);
	}
	
	if($_POST['action'] == "getunit"){
		$res = mysqli_query($conn,"select * from units where id='".$_POST['id']."'");
		if($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
			echo $arr['units']."[-]".$arr['images']."[-]".($arr['standard']==""?"0":$arr['standard'])."[-]".$arr['description'];
		}	
	}
	if($_POST['action'] == "getoption"){
		$res = mysqli_query($conn,"select * from options where id='".$_POST['id']."'");
		if($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
		  $res_dic = mysqli_query($conn, "select equivalent from amenity_dictionary where option_id='".$_POST['id']."'");
		  
			echo $arr['opt']."|".merge_dcnames($res_dic);
		}
	}
}
?>
