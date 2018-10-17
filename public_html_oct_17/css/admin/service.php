<?php
include('../sql.php');
mysqli_set_charset($conn,"utf8");
if(isset($_POST['action']))
{
	if($_POST['action'] == "getfacility")
	{
		$res = mysqli_query($conn,"select * from facility where id='".$_POST['id']."'");
		if($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
		{
			echo $arr['emailid']."[-]".$arr['logintype']."[-]".$arr['companyname']."[-]".$arr['phone']."[-]".$arr['address1'].
					 "[-]".$arr['city']."[-]".$arr['state']."[-]".$arr['zipcode']."[-]".$arr['searchable']."[-]".$arr['status'];
		}	
	}
	if($_POST['action'] == "getcustomer")
	{
		$res = mysqli_query($conn,"select * from customer where id='".$_POST['id']."'");
		if($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
		{
			echo $arr['emailid']."[-]".$arr['firstname']."[-]".$arr['lastname']."[-]".$arr['phone']."[-]".$arr['status'];
		}	
	}
	if($_POST['action'] == "getunit")
	{
		$res = mysqli_query($conn,"select * from units where id='".$_POST['id']."'");
		if($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
		{
			echo $arr['units']."[-]".$arr['images']."[-]".($arr['standard']==""?"0":$arr['standard'])."[-]".$arr['description'];
		}	
	}
	if($_POST['action'] == "getoption")
	{
		$res = mysqli_query($conn,"select * from options where id='".$_POST['id']."'");
		if($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
		{
			echo $arr['opt'];
		}	
	}
}
?>