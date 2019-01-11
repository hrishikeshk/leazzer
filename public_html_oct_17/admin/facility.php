<?php
include('../sql.php');
if(isset($_GET['action'])){
		if($_GET['action'] == "export"){
			$now = gmdate("D, d M Y H:i:s");
	    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
	    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
	    header("Last-Modified: {$now} GMT");
	
	    // force download  
	    header("Content-Type: application/force-download");
	    header("Content-Type: application/octet-stream");
	    header("Content-Type: application/download");
	
	    // disposition / encoding on response body
	    header("Content-Disposition: attachment;filename=facility-export.csv");
	    header("Content-Transfer-Encoding: binary");

			ob_start();
		  $df = fopen("php://output", 'w');
	    $res = mysqli_query($conn,"select * from facility_master");
	    if(mysqli_num_rows($res) > 0){
  	    $arr = mysqli_fetch_array($res, MYSQLI_ASSOC);
	      $arr['options'] = updateOpt($arr['id']);
	  		$arr['units'] = updateUnit($arr['id']);
	  	  fputcsv($df, array_keys($arr));
	  		fputcsv($df, $arr);
	  		while($arr = mysqli_fetch_array($res, MYSQLI_ASSOC)){
	  			$arr['options'] = updateOpt($arr['id']);
	  			$arr['units'] = updateUnit($arr['id']);
	  			fputcsv($df, $arr);
	  		}
			}
		  fclose($df);
		  echo ob_get_clean();
		  die();
		}
}

function updateUnit($facility_id){
	global $conn;
	$resU = mysqli_query($conn,"select size, price from unit where facility_id='".$facility_id."'");
	if(mysqli_num_rows($resU) == 0)
	  return '';
	$arrU = mysqli_fetch_array($resU, MYSQLI_ASSOC);
	$unitStr = $arrU['size']." - ".$arrU['price'];	
	while($arrU = mysqli_fetch_array($resU, MYSQLI_ASSOC)){
		$unitStr .= ', '.$arrU['size'].' - '.$arrU['price'];
	}
  return $unitStr;
}

function updateOpt($facility_id){
	global $conn;
	$resO = mysqli_query($conn,"select amenity from facility_amenity where facility_id = '".$facility_id."'");
	if(mysqli_num_rows($resO) == 0)
	  return '';
	$arrO = mysqli_fetch_array($resO, MYSQLI_ASSOC);
	$opt = $arrO['amenity'];
	while($arrO = mysqli_fetch_array($resO,MYSQLI_ASSOC)){
		$opt .= ', '.$arrO['amenity'];
	}
	return $opt;
}

include('header.php');
if(isset($_GET['action'])){
		if($_GET['action'] == "delete"){
		    mysqli_query($conn, "delete from facility_amenity where facility_id=".$_GET['id']) or die('Failed to delete facility amenity: '.mysqli_error($conn));
		    mysqli_query($conn, "delete from image where facility_id=".$_GET['id']) or die('Failed to delete facility images: '.mysqli_error($conn));
		    mysqli_query($conn, "delete from review where facility_id=".$_GET['id']) or die('Failed to delete facility reviews: '.mysqli_error($conn));
		    mysqli_query($conn, "delete from unit_amenity where unit_id in (select id from unit where facility_id='".$_GET['id']."')") or die('Failed to delete unit amenity: '.mysqli_error($conn));
		    mysqli_query($conn, "delete from unit where facility_id=".$_GET['id']) or die('Failed to delete facility units: '.mysqli_error($conn));
				mysqli_query($conn, "delete from facility_master where id=".$_GET['id']) or die('Failed to delete facility master: '.mysqli_error($conn));
				$GError = "Deleted successfully.";
		}
}

if(isset($_POST['action'])){
	if($_POST['action'] == "update"){
		$query = "update facility_master set status='".(($_POST['status'] == 'Enabled')?1:0)."', searchable='".(isset($_POST['searchable'])?1:0)."'";
		$query .= " where id=".$_POST['submit'];
		error_log('Query in update: '.$query);
		mysqli_query($conn,$query) OR die('Failed to update facility details - '.mysqli_error($conn));
		$GError = "Edited successfully.";
	}
	else if($_POST['action'] == "charge"){
	  
	}
}

?>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
  		<table style="width:100%;"><tr><td style="width:50%;"><h2>Facility</h2></td>
  			 <td style="width:50%;text-align:right;">
				 		<a href="<?php echo $_SERVER['PHP_SELF']."?action=export"; ?>" class="hvr-ripple-out" style="background:#68AE00;color:#FFF;">Export</a>
				 </td></tr>
  			</table>
  		<!---START EDIT-->
				<div id="myModal" class="modal fade" role="dialog">
				  <div class="modal-dialog">
				    <!-- Modal content-->
				    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
				    <div class="modal-content">
				      <div class="modal-body">
				        <p>
									<center>
										<br>
										<table style="width:90%">
												<tr>
													<td style="vertical-align: top;text-align: right;width:30%;"><b>Emailid :&nbsp;</b></td>
													<td><input class="form-control" placeholder="Emailid" type="text" name="email" id="email" required style="margin-bottom:0px;display:inline;width:100%;" readonly><br><br></td>
													</tr>													
													<tr><td style="vertical-align: top;text-align: right;width:30%;"><b>Details :&nbsp;</b></td>
													<td><textarea class="form-control" placeholder="Details" name="details" id="details" 
													style="margin-bottom:0px;display:inline;width:100%;height: 120px;" readonly></textarea><br><br></td></tr>
													<tr><td style="vertical-align: top;text-align: right;width:30%;"><b>Searchable :&nbsp;</b></td>
													<td><input class="form-control" placeholder="Searchable" type="checkbox" name="searchable" id="searchable" style="margin-bottom:0px;display:inline;width:100%;text-align:left;"><br><br></td></tr>
													<tr><td style="vertical-align: top;text-align: right;width:30%;"><b>Status :&nbsp;</b></td>
													<td><select class="form-control" placeholder="Status" name="status" id="status" required style="margin-bottom:0px;display:inline;width:100%;">
													<option>Enabled</option>
													<option>Disabled</option>
													</select></td>
												</tr>
										</table>
										<input type="hidden" name="action" id="action" value="Update">
									</center>
				        </p>
				      </div>
				      <div class="modal-footer">
				      	<button class="btn btn-primary" name="submit" id="submit" value="Update" style="background:#68AE00;border-color:#68AE00;">Create</button>
				        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				      </div>
				    </div>
						</form>
				  </div>
				</div>
			<!---END EDIT-->
			  		<!---START CHARGE-->
				<div id="myModalCharge" class="modal fade" role="dialog">
				  <div class="modal-dialog">
				    <!-- Modal content-->
				    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
				    <div class="modal-content">
				      <div class="modal-body">
				        <p>
									<center>
										<br>
										<table id="chargeFN" style="width:90%">
												<tr>
													<td style="vertical-align: top;text-align: right;width:30%;"><b>Facility Name :&nbsp;</b></td>
													<td ><input class="form-control" placeholder="Facility Name" type="text" name="facility_name" id="facility_name" required style="margin-bottom:0px;display:inline;width:100%;" readonly><br><br></td>
													</tr>
													<tr><td style="vertical-align: top;text-align: right;width:30%;"><b>Amount :&nbsp;</b></td>
													<td><textarea class="form-control" placeholder="Amount" name="amount" id="amount" 
													style="margin-bottom:0px;display:inline;width:70%;height: 25px;"></textarea><br><br></td></tr>
										</table>
										<input type="hidden" name="action" id="action" value="Charge">
										<div id="noCard">
  										<b style=""display:hidden">No credit cards available for this facility !</b>
	  									<br />
	  									Click to add card details for this facility <button class="btn btn-primary" name="addCard" id="addCard" value="Add Card" style="background:#68AE00;border-color:#68AE00;" onClick="return openCard();">Add Card</button>
										</div>
									</center>
				        </p>
				      </div>
				      <div class="modal-footer">
				      	<button class="btn btn-primary" name="submitCharge" id="submitCharge" value="Charge" style="background:#68AE00;border-color:#68AE00;">Charge</button>
				        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				      </div>
				    </div>
						</form>
				  </div>
				</div>
			<!---END CHARGE-->
				<?php
					if($GError!=""){
						echo "<div class=\"alert alert-info\" role=\"alert\">";
						echo $GError;
						echo "</div>";
					}
				?>	
    	<div class="blankpage-main" style="padding:1em 1em;">
				<table id="datatable" class="table table-striped table-bordered" width="100%" cellspacing="0">
					<thead>
						<tr>
						<th width=50px>Id</th>
						<th>Company Name</th>
						<th width=30px>Status</th>
						<th width=30px>Charge</th>
						<th width=20px>Edit</th></tr>
					</thead>
				    </tbody>
					</table>		
    	</div>
    </div>
</div>
<link href="css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#datatable').DataTable({
    	 "responsive": true,
    	 "processing": true,
    	 "serverSide": true,
    	 "ajax": {
            url: 'fservice.php',
            type: 'POST'
        },
       "columns":[            
						{"data": "id",
							"render":function(data,type,row,meta)
							{
									return data;
							}
						},
            {"data": "title"},
            {"data": "status",
            	"render":function(data,type,row,meta)
            	{
								return (data==1?"Yes":"No");
							}
						},
						{"data": "id",
            	"render":function(data,type,row,meta)
            	{
            	  $btn = "<a href=\"#\" data-toggle=\"modal\" data-target=\"#myModalCharge\" onclick=\"chargefacility("+data+")\"><i class=\"fa fa-pencil\"></i></a>";
								return $btn;
							}
						},
            {"data": "id",
            	"render":function(data,type,row,meta)
            	{
								return "<a href=\"#\" data-toggle=\"modal\" data-target=\"#myModal\" onclick=\"editfacility("+data+")\"><i class=\"fa fa-pencil\"></i></a>";
							}
						}]
    });
});
$("#myModal").on("hidden.bs.modal", function (){
    resetLayout();
});
$("#myModalCharge").on("hidden.bs.modal", function (){
    resetLayout();
});

function openCard(){
  var id_source = document.getElementById("addCard");
  if(id_source != undefined && id_source != null){
    //var name_source = document.getElementById("facility_name");
    window.location.href = "cdinfo_a.php?facility_id=" + id_source.value;
  }
  return false;
}

function resetLayout(){
	document.getElementById("email").value = "";
	document.getElementById("details").innerHTML = "";
	document.getElementById("searchable").checked = true;
	document.getElementById("status").value = "Enabled";
	document.getElementById("action").value = "update";
	document.getElementById("submit").innerHTML = "update";
	document.getElementById("facility_name").value = "";
	document.getElementById("amount").innerHTML = "";
	document.getElementById("submitCharge").innerHTML = "update";
}

function chargefacility(id){
	var res = ajaxcall("action=getcard&id="+id);
	var resArr = res.split("[-]");
	if(resArr[0].length == 0){
	  resetLayout();
	  //document.getElementById("facility_name").value = resArr[1];
  	document.getElementById("chargeFN").style.display="none";
  	document.getElementById("noCard").style.display="block";
  	document.getElementById("submitCharge").style.display="none";
  	document.getElementById("addCard").value=id;
	}
	else{
	  document.getElementById("facility_name").value = resArr[1];
	  document.getElementById("action").value = "Charge";
  	document.getElementById("submitCharge").innerHTML = "Charge";
	}
}

function editfacility(id){
	var res = ajaxcall("action=getfacility&id="+id);
	var resArr = res.split("[-]");
	document.getElementById("email").value = resArr[0];
	document.getElementById("details").innerHTML = resArr[2]+"\n"+resArr[4]+"\n"+resArr[5]+","+resArr[6]+" - "+resArr[7]+"\n"+resArr[3];
	document.getElementById("searchable").checked = (resArr[8]=="1"?true:false);
	document.getElementById("status").value = (resArr[9] == "1"?"Enabled":"Disabled");
	document.getElementById("action").value = "update";
	document.getElementById("submit").value = id;
	document.getElementById("submit").innerHTML = "Update";
}

function ajaxcall(datastring)
{
    var res;
    $.ajax
    ({	
    		type:"POST",
    		url:"service.php",
    		data:datastring,
    		cache:false,
    		async:false,
    		success: function(result)
    	 	{		
   				 	res=result;
   		 	}
    });
    return res;
}
</script>
<!--inner block end here-->
<?php
include('footer.php');
?>

