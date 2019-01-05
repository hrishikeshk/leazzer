<?php
include('../sql.php');
if(isset($_POST['action'])){
  /*
	if($_POST['action'] == "getparticular"){
		$res = mysqli_query($conn,"select * from particular where id='".$_POST['id']."'");
		if($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
		{
			echo $arr['name'];
		}	
	}
	if($_POST['action'] == "getallparticulars")
	{
			$ret = '<select id="particular'.$_POST['pcnt'].'" name="particular'.$_POST['pcnt'].'" required="" class="form-control">';
			$res = mysqli_query($conn,"select * from particular where uid=".$_POST['uid']);	
			while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
			{
				$ret .= '<option value="'.$arr['id'].'">'.$arr['name'].'</option>';
			}
			$ret .= '</select>';
			echo $ret;
			echo '<input type="text" name="amount'.$_POST['pcnt'].'" id="amount'.$_POST['pcnt'].'" placeholder="Amount" required="" class="form-control" style="width:90%;display:inline;margin-bottom:10px;">';
			echo '<i id="minus'.$_POST['pcnt'].'"  class="fa fa-minus-circle" style="color:#5461ab;font-weight:bold;font-size:30px;" onclick="removeParticular('.$_POST['pcnt'].');"></i>';
	}
	if($_POST['action'] == "getboookings")
	{
		$dateArr = explode("/",$_POST['date']);
		$dt = mktime(0,0,0,$dateArr[1],$dateArr[0],$dateArr[2]);
		$res = mysqli_query($conn,"select * from booking where timestamp='".$dt."' and userid='".$_POST['id']."'");
		if(mysqli_num_rows($res) < 1)
		{
			mysqli_query($conn,"insert into booking(userid,timestamp) values ('".$_POST['id']."','".$dt."')");
			echo "booked";
		}
		else
		{
			mysqli_query($conn,"delete from booking where userid='".$_POST['id']."' and timestamp='".$dt."'");
			echo "cleared";
		}
	}
	if($_POST['action'] == "getmonthboookings")
	{
		$dateArr = explode("/",$_POST['date']);
		$startDate = mktime(0,0,0,$dateArr[1],$dateArr[0],$dateArr[2]);
		$endDate = strtotime('+1 month',$startDate);
		$res = mysqli_query($conn,"select * from booking where userid='".$_POST['id']."' and timestamp between ".$startDate." and ".$endDate);
		$eventStr = "";
		while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
		{
			$eventStr.= date('j/n/Y',$arr['timestamp'])."[-]".date('Y-m-d',$arr['timestamp'])."[,]";
        }
		echo $eventStr;
	}
	*/
}
?>

