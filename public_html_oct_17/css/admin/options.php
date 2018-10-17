<?php
include('header.php');
if(isset($_GET['action']))
{
		if($_GET['action'] == "delete")
		{
				mysqli_query($conn,"delete from options where id=".$_GET['id']);
				$GError = "Deleted successfully.";
		}
}
if(isset($_POST['action']))
{
	if($_POST['action'] == "create")
	{
		mysqli_query($conn,"insert into options(opt) values (N'".mysqli_real_escape_string($conn,$_POST['optname'])."')");
		$GError = "Created successfully.";
	}
	if($_POST['action'] == "update")
	{		
		$query = "update options set opt=N'".mysqli_real_escape_string($conn,$_POST['optname'])."'";
		$query .= " where id=".$_POST['submit'];
		mysqli_query($conn,$query);
		$GError = "Edited successfully.";
	}
}
?>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
  		<table style="width:100%;"><tr><td style="width:50%;"><h2>Options</h2></td>
				 <td style="width:50%;text-align:right;">
				 		<a href="#" class="hvr-ripple-out" style="background:#68AE00;color:#FFF;" data-toggle="modal" data-target="#myModal">Create</a>
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
													<td style="vertical-align: top;text-align: right;width:30%;"><b>Name :&nbsp;</b></td>
													<td><input class="form-control" placeholder="Option Name" type="text" name="optname" id="optname" required style="margin-bottom:0px;display:inline;width:100%;"><br><br></td>
												</tr>
										</table>
										<input type="hidden" name="action" id="action" value="create">
									</center>
				        </p>
				      </div>
				      <div class="modal-footer">
				      	<button class="btn btn-primary" name="submit" id="submit" value="Create" style="background:#68AE00;border-color:#68AE00;">Create</button>
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
						echo "<div class=\"alert alert-success\" role=\"alert\">";
						echo $GError;
						echo "</div>";
					}
				?>	
    	<div class="blankpage-main" style="padding:1em 1em;">
				<table id="datatable" class="table table-striped table-bordered" width="100%" cellspacing="0">
					<thead>
						<tr>
						<th>Option</th>
						<th width=40px>Edit</th>
						<th width=40px>Delete</th></tr>
					</thead>
					 	<?php 
								$res = mysqli_query($conn,"select * from options");
								while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
								{
									echo "<tr>\n";
				      		echo "<td>".$arr['opt']."</td>\n";
				      		echo "<td style=\"text-align:center;\"><a href=\"#\" data-toggle=\"modal\" data-target=\"#myModal\" onclick=\"editoption(".$arr['id'].")\"><i class=\"fa fa-pencil\"></i></a></td>\n";
									echo "<td style=\"text-align:center;\"><a href=\"options.php?action=delete&id=".$arr['id']."\"><i class=\"fa fa-trash-o\"></i></a></td>\n";
				      		echo "</tr>\n";
								}
							?>
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
    $('#datatable').DataTable();
});
$("#myModal").on("hidden.bs.modal", function () 
{
    resetLayout();
});	
function resetLayout()
{
	document.getElementById("optname").value = "";
	document.getElementById("action").value = "create";
	document.getElementById("submit").innerHTML = "Create";
}
function editoption(id)
{
	var res = ajaxcall("action=getoption&id="+id);
	document.getElementById("optname").value = res;
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