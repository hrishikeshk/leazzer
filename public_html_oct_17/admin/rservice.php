<?php
	include('../sql.php');
	mysqli_set_charset($conn,"utf8");
	$draw = $_POST["draw"];//counter used by DataTables to ensure that the Ajax returns from server-side processing requests are drawn in sequence by DataTables
	$orderByColumnIndex  = $_POST['order'][0]['column'];// index of the sorting column (0 index based - i.e. 0 is the first record)
	$orderBy = $_POST['columns'][$orderByColumnIndex]['data'];//Get name of the sorting column from its index
	$orderType = $_POST['order'][0]['dir']; // ASC or DESC
	$start  = $_POST["start"];//Paging first record indicator.
	$length = $_POST['length'];//Number of records that the table can display in the current draw
	
	$query = "SELECT * FROM facility_owner where pwd <> 'excited123!' ";
	if(isset($_GET['chpwd_tf']) && ($_GET['chpwd_tf'] != ""))
		$query .= ' and ch_pwd_ts is not null and ch_pwd_ts >= TIMESTAMP(DATE_SUB(NOW(), INTERVAL '.$_GET['chpwd_tf'].' day))';
	
	$query .= "ORDER BY ".$orderBy." ".$orderType;
	//$query .= "ORDER BY ".$orderBy." ".$orderType." limit ".$start." , ".$length;
	
	error_log($query);
	//echo  "Query --".$query; created_at < TIMESTAMP(DATE_SUB(NOW(), INTERVAL 5 day))
	$data_arr = getData($query);
	$cnt = $data_arr[1];

	$response = array(
        "draw" => intval($draw),
        "recordsTotal" => $cnt,
        "recordsFiltered" => $cnt,
        "data" => $data_arr[0]
    );
 	echo json_encode($response);

  function getData($sql){
        global $conn;
        $query = mysqli_query($conn, $sql) OR DIE ("Can't get Data from DB for reports" );
        $num_rows = mysqli_num_rows($query);
        $data = array();
        foreach ($query as $row ){
            $data[] = $row ;
        }
        return array($data, $num_rows);
  }
?>

