<?php
	include('../sql.php');
	mysqli_set_charset($conn,"utf8");
	$draw = $_POST["draw"];//counter used by DataTables to ensure that the Ajax returns from server-side processing requests are drawn in sequence by DataTables
	$orderByColumnIndex  = $_POST['order'][0]['column'];// index of the sorting column (0 index based - i.e. 0 is the first record)
	$orderBy = $_POST['columns'][$orderByColumnIndex]['data'];//Get name of the sorting column from its index
	$orderType = $_POST['order'][0]['dir']; // ASC or DESC
	$start  = $_POST["start"];//Paging first record indicator.
	$length = $_POST['length'];//Number of records that the table can display in the current draw
	
	$query = "SELECT * FROM facility ";
	if(isset($_POST['search']['value']) && ($_POST['search']['value'] != ""))
		$query .= "where companyname like '%".$_POST['search']['value']."%' ";
	
	$query .= "ORDER BY ".$orderBy." ".$orderType." limit ".$start." , ".$length;
	//echo  "Query --".$query;
	$data = getData($query);
	$cnt = 0;
	$cntQuery = "SELECT count(*) as cnt FROM facility ";
	if(isset($_POST['search']['value']) && ($_POST['search']['value'] != ""))
		$cntQuery .= "where companyname like '%".$_POST['search']['value']."%' ";
		
	$res = mysqli_query($conn, $cntQuery) OR DIE ("Can't get Data from DB , check your SQL Query " );
	if($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
			$cnt = $arr['cnt'];
	$response = array(
        "draw" => intval($draw),
        "recordsTotal" => $cnt,
        "recordsFiltered" => $cnt,
        "data" => $data
    );
 	echo json_encode($response);
	function getData($sql)
	{
        global $conn;
        $query = mysqli_query($conn, $sql) OR DIE ("Can't get Data from DB , check your SQL Query " );
        $data = array();
        foreach ($query as $row ) 
				{
            $data[] = $row ;
        }
        return $data;
    }
?>