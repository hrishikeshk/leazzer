<?php
include('header.php');
?>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<div class="blankpage-main" style="padding:1em 1em;">
    			<?php 
							$res = mysqli_query($conn,"select * from reserve where cid='".mysqli_real_escape_string($conn, $_SESSION['lcdata']['id'])."' order by reservefromdate desc limit 100");
							if(mysqli_num_rows($res) <= 0 )
							  echo "<b>No Reservation.</b>";
							else{
  							echo '<table id="datatable" class="table table-striped table-bordered" width="100%" cellspacing="0">
	  						<thead>
	  							<tr>
	  							<th>Date</th>
	  							<th>Storage Name</th>
	  							<th>Units</th>
	  							<th>Price</th>
	  						</thead>';
	  						$res = mysqli_query($conn,"select * from reserve where cid='".mysqli_real_escape_string($conn, $_SESSION['lcdata']['id'])."' order by reservefromdate desc limit 100");
	  						while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC)){
  								echo "<tr>\n";
  								echo "<td>".date("d/m/Y",$arr['reservefromdate'])." - ".date("d/m/Y",$arr['reservetodate'])."</td>\n";
  								$resF = mysqli_query($conn,"select * from facility_master where id='".mysqli_real_escape_string($conn, $arr['fid'])."'");
  								$arrF = mysqli_fetch_array($resF,MYSQLI_ASSOC);
  								echo "<td>".$arrF['title']."</td>\n";
  								$unitArr = explode(",",$arr['units']);
  								$unitDet = explode("-",$unitArr[0]);
  								for($i=0;$i<count($unitArr);$i++){
  									if($unitArr[$i] =="")
	  									continue;
	  								$unitDet = explode("-",$unitArr[$i]);
	  								//echo $unitDet[0]." (".$unitDet[1].")<br>";
	  								echo "<td>".$unitDet[0]."</td><td>".$unitDet[1]."</td>";
	  								break;
	  							}
	  							//echo "</td>\n";
	  							echo "</tr>\n";
								}
							}
							?>
				    </tbody>
					</table>		
    		<div class="clearfix"> </div>
    	</div>
    </div>
</div>
<link href="css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#datatable').DataTable({"aaSorting": []});
});
</script>
<!--inner block end here-->
<?php
include('footer.php');
?>

