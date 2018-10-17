<?php
include('header.php');
if(isset($_GET['action']))
{
		if($_GET['action'] == "delete")
		{
				mysqli_query($conn,"delete from units where id=".$_GET['id']);
				$GError = "Deleted successfully.";
		}
}
if(isset($_POST['action']))
{
	if($_POST['action'] == "create")
	{
	  $fileImageName = ""; 
		$ts = time();
		if(isset($_FILES["image"]))
		{
			$file = pathinfo($_FILES["image"]["name"]);
			if(isset($file['filename']) && $file['filename'] != "")
			{
				$fileImageName = $ts.".".$file['extension'];
				move_uploaded_file($_FILES["image"]["tmp_name"], "../unitimages/".$fileImageName);	
			}
		}
		mysqli_query($conn,"insert into units(units,images,standard,description) values (N'".
											mysqli_real_escape_string($conn,$_POST['unitname'])."','".
											$fileImageName."','".
											(isset($_POST['standard'])?"1":"0")."',N'".
											mysqli_real_escape_string($conn,$_POST['description'])."')");
		$GError = "Created successfully.";
	}
	if($_POST['action'] == "update")
	{
		$fileImageName = ""; 
		$ts = time();
		if(isset($_FILES["image"]))
		{
			$file = pathinfo($_FILES["image"]["name"]);
			if(isset($file['filename']) && $file['filename'] != "")
			{
				$fileImageName = $ts.".".$file['extension'];
				move_uploaded_file($_FILES["image"]["tmp_name"], "../unitimages/".$fileImageName);	
			}
		}
		$query = "update units set units=N'".mysqli_real_escape_string($conn,$_POST['unitname'])."',standard='".
						 (isset($_POST['standard'])?1:0)."',description=N'".mysqli_real_escape_string($conn,$_POST['description'])."'";
							
		if($fileImageName != "")
			$query .=",images='".$fileImageName."'";
			
		$query .= " where id=".$_POST['submit'];
		mysqli_query($conn,$query);
		$GError = "Edited successfully.";
	}
}
?>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
  		<table style="width:100%;"><tr><td style="width:50%;"><h2>Units</h2></td>
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
													<td><input class="form-control" placeholder="Unit Name" type="text" name="unitname" id="unitname" required style="margin-bottom:0px;display:inline;width:100%;"><br><br></td>
												</tr>
												<tr>
													<td style="vertical-align: top;text-align: right;width:30%;"><b>Image :&nbsp;</b></td>
													<td><input class="form-control" placeholder="Image" type="file" name="image" id="image" style="margin-bottom:0px;display:inline;width:100%;"><br>
													<img src="" id="unitimage" name="unitimage" style="width:50px;height:50px;display:none;"><br></td>
												</tr>
												<tr>
													<td style="vertical-align: top;text-align: right;width:30%;"><b>Standard :&nbsp;</b></td>
													<td><input class="form-control" placeholder="Standard" type="checkbox" name="standard" id="standard" style="margin-bottom:0px;display:inline;width:100%;"><br></td>
												</tr>
												<tr>
													<td style="vertical-align: top;text-align: right;width:30%;"><b>Description :&nbsp;</b></td>
													<td><textarea class="form-control" placeholder="Description" name="description" id="description" style="margin-bottom:0px;display:inline;width:100%;"></textarea><br></td>
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
						<th width=50px>Image</th>
						<th>Unit Name</th>
						<th width=40px>Standard</th>
						<th width=40px>Edit</th>
						<th width=40px>Delete</th></tr>
					</thead>
					 	<?php 
								$res = mysqli_query($conn,"select * from units");
								while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
								{
									echo "<tr>\n";
									echo "<td><img src=\"../unitimages/".($arr['images']==""?"pna.jpg":$arr['images'])."\" width=40px height=40px></td>\n";
				      		echo "<td>".$arr['units']."</td>\n";
				      		echo "<td>".($arr['standard']==1?"Yes":"No")."</td>\n";
				      		echo "<td style=\"text-align:center;\"><a href=\"#\" data-toggle=\"modal\" data-target=\"#myModal\" onclick=\"editunits(".$arr['id'].")\"><i class=\"fa fa-pencil\"></i></a></td>\n";
									echo "<td style=\"text-align:center;\"><a href=\"units.php?action=delete&id=".$arr['id']."\"><i class=\"fa fa-trash-o\"></i></a></td>\n";
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
	document.getElementById("unitname").value = "";
	document.getElementById('unitimage').style.display = "none";
	document.getElementById("standard").checked = false;
	document.getElementById("description").innerHTML = "";
	document.getElementById("action").value = "create";
	document.getElementById("submit").innerHTML = "Create";
}
function editunits(id)
{
	var res = ajaxcall("action=getunit&id="+id);
	var resArr = res.split("[-]");
	document.getElementById("unitname").value = resArr[0];
	document.getElementById("unitimage").src= "../unitimages/"+(resArr[1]==""?"pna.jpg":resArr[1]);
	document.getElementById('unitimage').style.display = "block";
	document.getElementById("standard").checked = (resArr[2]=="1"?true:false);
	document.getElementById("description").innerHTML = resArr[3];
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