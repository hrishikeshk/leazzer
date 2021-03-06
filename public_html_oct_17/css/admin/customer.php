<?php
include('../sql.php');
if(isset($_GET['action']))
{
		if($_GET['action'] == "export")
		{
			$now = gmdate("D, d M Y H:i:s");
	    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
	    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
	    header("Last-Modified: {$now} GMT");
	
	    // force download  
	    header("Content-Type: application/force-download");
	    header("Content-Type: application/octet-stream");
	    header("Content-Type: application/download");
	
	    // disposition / encoding on response body
	    header("Content-Disposition: attachment;filename=facility-expot.csv");
	    header("Content-Transfer-Encoding: binary");			

			ob_start();
		  $df = fopen("php://output", 'w');
	    $res = mysqli_query($conn,"select * from customer");
	    $cnt =0;
			while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
			{
				if($cnt == 0)
				{
					 fputcsv($df, array_keys($arr));
					 $cnt++;
				}
				fputcsv($df, $arr);
			}
		  fclose($df);
		  echo ob_get_clean();
		  die();
		}
}
include('header.php');
if(isset($_GET['action']))
{
	if($_GET['action'] == "delete")
	{
			mysqli_query($conn,"delete from customer where id=".$_GET['id']);
			$GError = "Deleted successfully.";
	}
}
if(isset($_POST['action']))
{
	if($_POST['action'] == "update")
	{
		$query = "update customer set status='".$_POST['status']."'";
		$query .= " where id=".$_POST['submit'];
		mysqli_query($conn,$query);
		$GError = "Edited successfully.";
	}
}

?>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
  		<table style="width:100%;"><tr><td style="width:50%;"><h2>Customer</h2></td>
  			 <td style="width:50%;text-align:right;">
				 		<a href="<?php echo $_SERVER['PHP_SELF']."?action=export"; ?>" class="hvr-ripple-out" style="background:#68AE00;color:#FFF;">Export</a>
				 </td></tr>
  			</table>
  		<!---START-->
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
			<!---END-->
				<?php
					if($GError!="")
					{
						echo "<div class=\"alert alert-info\" role=\"alert\">";
						echo $GError;
						echo "</div>";
					}
				?>	
    	<div class="blankpage-main" style="padding:1em 1em;">
				<table id="datatable" class="table table-striped table-bordered" width="100%" cellspacing="0">
					<thead>
						<tr>
						<th width=50px>FirstName</th>
						<th>Last Name</th>
						<th width=30px>Status</th>
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
$(document).ready(function()
{
    $('#datatable').DataTable({
    	 "responsive": true,
    	 "processing": true,
    	 "serverSide": true,
    	 "ajax": {
            url: 'cservice.php',
            type: 'POST'
        },
       "columns":[            
			{"data": "firstname"},	
            {"data": "lastname"},
            {"data": "status",
            	"render":function(data,type,row,meta)
            	{
					return (data=="Enabled"?"Yes":"No");
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
$("#myModal").on("hidden.bs.modal", function () 
{
    resetLayout();
});			
function resetLayout()
{
	document.getElementById("email").value = "";
	document.getElementById("details").innerHTML = "";
	document.getElementById("status").value = "Enabled";
	document.getElementById("action").value = "update";
	document.getElementById("submit").innerHTML = "update";
}
function editfacility(id)
{
	var res = ajaxcall("action=getcustomer&id="+id);
	var resArr = res.split("[-]");
	document.getElementById("email").value = resArr[0];
	document.getElementById("details").innerHTML = resArr[1]+" "+resArr[2]+"\n"+resArr[3];
	document.getElementById("status").value = resArr[4];
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