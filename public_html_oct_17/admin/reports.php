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

include('header.php');

?>

<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
  		<table style="width:100%;"><tr><td style="width:50%;"><h2>Reports</h2></td></tr>
  		   <tr>
  		   <td style="width:25%;text-align:center;border:1px solid black;border-radius:4px">
  		     
  		     <form name="changedpwdfrm" id="changedpwdfrm" method="post" action="javascript:show_pwd_changes();">
  		       Initial Password Changes in Interval : <select id="chpwd_tf">
  		         <option value="1">Past 1 day</option>
  		         <option value="7">Past 1 week</option>
  		         <option value="15">Past 1 fortnight</option>
  		         <option value="30">Past 1 month</option>
  		         <option value="365">Past 1 year</option>
  		         <option value="10000">All Of Them</option>
  		       </select>
  		       <button id="d0_btn" class="btn btn-success" name="submit" value="View Changed Pwd" style="background:#68AE00;border-color:#68AE00;">View Changes</button>
  		     </form>
  		   </td>
  		   <td style="width:25%;text-align:center;border:1px solid black;border-radius:4px">
  		     
  		     <form name="trxfrm" id="trxfrm" method="post" action="javascript:show_trxs();">
  		       Transactions (Reservations) <br />
  		       From : 
	            <input class="datepicker" id="mdate_from" name="mdate_from" type="text" placeholder="From Date"  style="width:200px;height:30px;padding:5px;margin:5px;font-size:.8em;">
	           <br />To :
	            <input class="datepicker" id="mdate_to" name="mdate_to" type="text" placeholder="To Date"  style="width:200px;height:30px;padding:5px;margin:5px;font-size:.8em;">
  		       <button id="trx_btn" class="btn btn-success" name="submit" value="View Transactions" style="background:#68AE00;border-color:#68AE00;">View</button>
  		     </form>
  		   </td>
  			 <!-- td style="width:25%;text-align:right;">
				 		<a href="<?php echo $_SERVER['PHP_SELF']."?action=export"; ?>" class="hvr-ripple-out" style="background:#68AE00;color:#FFF;">Export</a>
				 </td --></tr>
  			</table>
				<?php
					if($GError!=""){
						echo "<div class=\"alert alert-info\" role=\"alert\">";
						echo $GError;
						echo "</div>";
					}
				?>
    	<div id="cpass_div" class="blankpage-main" style="padding:1em 1em;display:none">
				<table id="datatable0" class="table table-striped table-bordered" width="100%" cellspacing="0">
					<thead>
						<tr>
						<th width=20px>Id</th>
						<th>Company Name</th>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Phone</th>
						<th>Password Change At</th>
						<th>Email</th></tr>
					</thead>
				    </tbody>
					</table>		
    	</div>
    	<div id="trx_div" class="blankpage-main" style="padding:1em 1em;display:none">
				<table id="datatable1" class="table table-striped table-bordered" width="100%" cellspacing="0">
					<thead>
						<tr>
						<th width=20px>Reservation Id</th>
						<th>Customer First Name</th>
						<th>Customer Last Name</th>
						<th>Customer Email</th>
						<th>Reserved From</th>
						<th>Reserved To</th>
						<th>Units</th>
						<th>Facility Name</th>
						<th>Facility Email</th></tr>
					</thead>
				    </tbody>
					</table>		
    	</div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
  $('.datepicker').datepicker({
     format: 'mm/dd/yyyy',
     startDate: new Date('2017/01/01'),
     autoclose:true
  });
});

function show_trxs(){
  var cpass_div = document.getElementById('cpass_div');
  cpass_div.style.display="none";
  var trx_div = document.getElementById('trx_div');
  trx_div.style.display="block";
  var from_date = document.getElementById('mdate_from');
  var to_date = document.getElementById('mdate_to');
  var table = $('#datatable1').DataTable({
       "paging": false,
       "searching": false,
       "destroy" : true,
    	 "responsive": true,
    	 "processing": true,
    	 "serverSide": true,
    	 "ajax": {
            url: 'rtrxs.php?from=' + from_date.value + '&to=' + to_date.value,
            type: 'POST'
        },
       "columns":[
						{"data": "id",
							"render":function(data,type,row,meta)
							{
									return data;
							}
						},
            {"data": "customer_firstname"},
            {"data": "customer_lastname"},
            {"data": "customer_email"},
            {"data": "reservation_from"},
            {"data": "reservation_to",
            	"render":function(data,type,row,meta)
            	{
								return data;
							}
						},
            {"data": "unit",
            	"render":function(data,type,row,meta)
            	{
								return data;
							}
						},
						{"data": "facility_name"},
            {"data": "facility_email",
            	"render":function(data,type,row,meta)
            	{
								return data;
							}
						}]
    });
    
    //$('#d0_btn').on('click', function() {
      //  table.ajax.reload();
    //});
}

function show_pwd_changes(){
  var cpass_div = document.getElementById('cpass_div');
  cpass_div.style.display="block";
  var trx_div = document.getElementById('trx_div');
  trx_div.style.display="none";
  var tf_select = document.getElementById('chpwd_tf');
  var table = $('#datatable0').DataTable({
       "paging": false,
       "searching": false,
       "destroy" : true,
    	 "responsive": true,
    	 "processing": true,
    	 "serverSide": true,
    	 "ajax": {
            url: 'rservice.php?chpwd_tf=' + tf_select.value,
            type: 'POST'
        },
       "columns":[
						{"data": "auto_id",
							"render":function(data,type,row,meta)
							{
									return data;
							}
						},
            {"data": "companyname"},
            {"data": "firstname"},
            {"data": "lastname"},
            {"data": "phone",
            	"render":function(data,type,row,meta)
            	{
								return data;
							}
						},
            {"data": "ch_pwd_ts",
            	"render":function(data,type,row,meta)
            	{
								return data;
							}
						},
           {"data": "emailid",
            	"render":function(data,type,row,meta)
            	{
								return data;
							}
						}]
    });
    
    //$('#d0_btn').on('click', function() {
      //  table.ajax.reload();
    //});
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

