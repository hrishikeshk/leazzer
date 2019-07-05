<?php
require_once('../mail/class.phpmailer.php');

include('../sql.php');

function onSurveyCustomer($facility_id, $facility_name, $custEmail, $custName, $link){
	$fromemail="no-reply@leazzer.com"; 
	$toemail = $custEmail; 
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= '<center><img src="https://www.leazzer.com/images/emsurvey2.png" height="250px" width="400px" alt="Survey Do" title="uiLogo" style="display:block"></center><br />';
	$message .= 'Hello <b>'.$custName.'</b>,';
	$message .= '<br><br>Please help us with your valuable feedback for '.$facility_name.': ';

	$message .= $link;
	$message .= '</td></tr>';
	$message .= '<tr><td><br><br>';
	$message .= 'Thank You,<br>&mdash; Leazzer';
	$message .= '</td></tr>';
	$message .= '</table>';

	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->AddReplyTo($fromemail,"Leazzer"); 
	$mail->SetFrom($fromemail, "Leazzer");
	$mail->AddAddress($toemail, substr($toemail,0,strpos($toemail,"@")));
	$mail->Subject    = "Leazzer Survey";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->MsgHTML($message);
	$mail->isHTML(true);
	$ret = $mail->Send();
}

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
	    header("Content-Disposition: attachment;filename=facility-expot.csv");
	    header("Content-Transfer-Encoding: binary");			

			ob_start();
		  $df = fopen("php://output", 'w');
	    $res = mysqli_query($conn,"select * from customer");
	    $cnt =0;
			while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
				if($cnt == 0){
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
if(isset($_GET['action'])){
	if($_GET['action'] == "delete"){
			mysqli_query($conn,"delete from customer where id=".$_GET['id']);
			$GError = "Deleted successfully.";
	}
}

if(isset($_POST['action'])){
	if($_POST['action'] == "update"){
		$query = "update customer set status='".$_POST['status']."'";
		$query .= " where id=".$_POST['submit'];
		mysqli_query($conn,$query);
		$GError = "Edited successfully.";
	}
	else if($_POST['action'] == "Survey"){
	  $fac_split = explode('|', $_POST['facility_name']);
	  $code = md5($fac_split[1].'|'.$_POST['cid'].'surveycode');
////	  $link = 'https://www.leazzer.com/review_add.php?code='.$code.'&fid='.$fac_split[1].'&cid='.$_POST['cid'];
    $url = "https://www.leazzer.com/review_add.php?code=".$code."&fid=".$fac_split[1]."&cid=".$_POST['cid'];
    //$link = '<center><table border="0" cellpadding="0" cellspacing="0" align="center" style="background:#3e3547;border-radius:4px;border:1px solid #bbbbbb;color:#ffffff;font-size:14px;letter-spacing:1px;padding:10px 18px"><tr><td><a target="_blank" rel="nofollow" style="color:#ffffff;text-decoration:none;" href="'.$url.'">Submit Survey</a></td></tr></table></center>';
    //$link = '<form target="_blank" action="'.$url.'"><button type="button" id="submit" name="submit">Submit Survey</button></form>';

    $link = '<br /><br /><center><table border="0" cellpadding="0" cellspacing="0" align="center" style="background:#00ff00;border-radius:4px;border:1px solid #bbbbbb;color:#000000;font-size:14px;letter-spacing:1px;padding:10px 18px"><tr><td><a target="_blank" rel="nofollow" style="text-decoration:none;cursor:pointer;color:#000000" href="'.$url.'">Help now</a></td></tr></table></center>';

	  //$link = https://www.leazzer.com/review_add.php?code='.$code.'&fid='.$fac_split[1].'&cid='.$_POST['cid'];
	  onSurveyCustomer($fac_split[1], $fac_split[0], $_POST['emailid'], $_POST['customer_name'], $link);
	  $GError = "Sent Survey Request Successfully.";
	}
	else
	  error_log('None: '.$_POST['action']);
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
			
			
			<!---START SURVEY-->
				<div id="myModalSurvey" class="modal fade" role="dialog">
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
										<td style="vertical-align: top;text-align: right;width:30%;"><b>Email :&nbsp;</b></td>
										<td><input class="form-control" placeholder="Email" type="text" name="emailid" id="emailid" required style="margin-bottom:0px;display:inline;width:100%;" /><br><br></td>
										</tr>													
										<tr><td style="vertical-align: top;text-align: right;width:30%;"><b>Customer Name :&nbsp;</b></td>
										<td><input class="form-control" placeholder="Customer Name" name="customer_name" id="customer_name" 
										style="margin-bottom:0px;display:inline;width:100%;" readonly></input><br><br></td></tr>
										<tr><td style="vertical-align: top;text-align: right;width:30%;"><b>Facility Name(s) :&nbsp;</b></td>
										<td><select class="form-control" name="facility_name" id="facility_name" required style="margin-bottom:0px;display:inline;width:100%;">
										</select></td>
									</tr>
								</table>
								<input type="hidden" name="action" id="saction" value="Survey">
								<input type="hidden" name="cid" id="cid" value="cid">
							</center>
				        </p>
				      </div>
				      <div class="modal-footer">
				      	<button class="btn btn-primary" name="submit" id="submit" value="Send Survey" style="background:#68AE00;border-color:#68AE00;">Send Survey</button>
				        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				      </div>
				    </div>
						</form>
				  </div>
				</div>
			<!---END SURVEY -->
			
			
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
						<th width=50px>FirstName</th>
						<th>Last Name</th>
						<th width=30px>Status</th>
						<th width=20px>Edit</th>
						<th width=30px>Survey</th>
						</tr>
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
                   "render":function(data,type,row,meta){
            					return (data=="Enabled"?"Yes":"No");
				            }
			            },
                  {"data": "id",
                 	 "render":function(data,type,row,meta){
			            		return "<a href=\"#\" data-toggle=\"modal\" data-target=\"#myModal\" onclick=\"editfacility("+data+")\"><i class=\"fa fa-pencil\"></i></a>";
				            }
			            },
			            {"data": "id",
			             "render":function(data,type,row,meta){
			            		return "<a href=\"#\" data-toggle=\"modal\" data-target=\"#myModalSurvey\" onclick=\"surveyCustomer("+data+")\"><i class=\"fa fa-pencil\"></i></a>";
				            }
			            }
			 ]
    });
});
$("#myModal").on("hidden.bs.modal", function (){
    resetLayout();
});

$("#myModalSurvey").on("hidden.bs.modal", function (){
    resetLayoutSurvey();
});

function resetLayout(){
	document.getElementById("email").value = "";
	document.getElementById("details").innerHTML = "";
	document.getElementById("status").value = "Enabled";
	document.getElementById("action").value = "update";
	document.getElementById("submit").innerHTML = "update";
}

function resetLayoutSurvey(){
	document.getElementById("emailid").value = "";
	document.getElementById("customer_name").value = "";
}

function editfacility(id){
	var res = ajaxcall("action=getcustomer&id="+id);
	var resArr = res.split("[-]");
	document.getElementById("email").value = resArr[0];
	document.getElementById("details").innerHTML = resArr[1]+" "+resArr[2]+"\n"+resArr[3];
	document.getElementById("status").value = resArr[4];
	document.getElementById("action").value = "update";
	document.getElementById("submit").value = id;
	document.getElementById("submit").innerHTML = "Update";
}

function surveyCustomer(id){
	var res = ajaxcall("action=getcustomerReserves&customerid="+id);
	var resArr = JSON.parse(res);
	var cn = document.getElementById("customer_name");
	var em = document.getElementById("emailid");
	var fn = document.getElementById("facility_name");
	var cid = document.getElementById("cid");
	for(i = 0; i < resArr.length; i++){
	  var row = resArr[i];
	  fn.options[fn.options.length] = new Option(row[0], row[0] + '|' + row[1]);
	}
	if(resArr.length > 0){
	  if(resArr[0][3].indexOf(resArr[0][2]) == -1)
	    cn.value = resArr[0][2] + " " + resArr[0][3];
	  else
	    cn.value = resArr[0][3];
	  em.value = resArr[0][4];
	  cid.value = resArr[0][5];
	}
	//document.getElementById("facility_name").value = resArr[0];
	//document.getElementById("facility_id").innerHTML = resArr[1];
	//document.getElementById("customer_name").value = resArr[2]+" "+resArr[3];
	//document.getElementById("emailid").value = resArr[4];
	//document.getElementById("submit").value = id;
	//document.getElementById("submit").innerHTML = "Send Survey";
}

function ajaxcall(datastring){
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

