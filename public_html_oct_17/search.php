<?php
session_start();
include('sql.php');
$GError = "";
$filter = "";
if((!isset($_POST['search'])) && isset($_SESSION['search']))
{
	$_POST['search']= $_SESSION['search'];
}
else if(isset($_POST['search']))
{
	$_SESSION['search']= $_POST['search'];
}
if(isset($_GET['action']))
{
	if($_GET['action'] == "removefilter" && isset($_SESSION['filter']))
	{		
		$newFilterArr = array();			
 		for($i=0;$i<count($_SESSION['filter']);$i++)
 		{
 			$filterArr = explode("[-]",$_SESSION['filter'][$i]);
 			if($filterArr[0] != $_GET['id'])
 				array_push($newFilterArr,$_SESSION['filter'][$i]);
		}			
		$_SESSION['filter'] = $newFilterArr;
	}
}
if(isset($_POST['action']))
{
	if($_POST['action'] == "applyfilter")
	{
		$_SESSION['filter'] = $_POST['options'];
	}
}
function showUnit($arr)
{
	global $conn;
	$unitIds="";
	$unitPrice="";
	
	$unitArr = explode(",",$arr['units']);
	for($i=0;$i<count($unitArr);$i++)
	{
		if(trim($unitArr[$i]) == "")
		continue;
		
		$unitSubArr = explode("-",$unitArr[$i]);
		$unitIds .= $unitSubArr[0].",";
		$unitPrice.= $unitSubArr[1].",";
	}
	$unitIds = substr($unitIds,0,strlen($unitIds)-1);
	$unitPrice = substr($unitPrice,0,strlen($unitPrice)-1);
	$unitPriceArr = explode(",",$unitPrice);
	$resU = mysqli_query($conn,"select * from units where id in(".$unitIds.")");
	echo '<div style="border:0px solid red;width:70%;height:150px;"  id="unitstbl_'.$arr['id'].'">';
	$cnt = 0;
	while($arrU = mysqli_fetch_array($resU,MYSQLI_ASSOC))
	{
		echo '<div class="col-md-1" style="text-align: center;padding:10px;width="28%";border:0px solid #000;box-shadow: 0px 0px 3px #888888;">';
		echo '<img src="unitimages/'.($arrU['images']==""?"pna.jpg":$arrU['images']).'" style="vertical-align: top;width:50px;height:50px">';
		echo '<p style="width:80px;display:inline-block;padding:0px 10px 0px 10px;margin:0;font-size:.8em;white-space: nowrap;"><b>'.$arrU['units'].'</b><br>$'.$unitPriceArr[$cnt].'</p>';
		echo '<button type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;margin: 0 auto;border-radius: 3px;font-size: 1.0em;width:80px;display:inline;padding:0px;" onClick="onUnitClick(this,'.
										(isset($_SESSION['lcdata'])?$_SESSION['lcdata']['id']:"0").','.
										$arr['id'].','.
										$arr['reservationdays'].',\''.
										urlencode($arrU['units']).'\',\''.
										$unitPriceArr[$cnt].'\');">Reserve</button></div>';
		$cnt++;
	}
	echo "</div>";
}
function showOpt($arr)
{
	global $conn;
	$opt = $arr['options'];
	$pos = strpos($opt,",");
	if($pos ==  0)
		$opt = substr($opt,1,strlen($opt)-2);
	else
		$opt = substr($opt,0,strlen($opt)-1);
	$resO = mysqli_query($conn,"select * from options where id in(".$opt.")");
	echo '<p style="font-size:.8em">';
	while($arrO = mysqli_fetch_array($resO,MYSQLI_ASSOC))
		echo $arrO['opt'].', ';
	echo "</p>";
}
function file_get_contents_curl($url) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
curl_setopt($ch, CURLOPT_URL, $url);
$data = curl_exec($ch);
curl_close($ch);
return $data;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Leazzer" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="facility/css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<link href="facility/css/style.css" rel="stylesheet" type="text/css" media="all"/>
<script src="facility/js/jquery-2.1.1.min.js"></script> 
<link href="facility/css/font-awesome.css" rel="stylesheet"> 
<link href='facility/fonts/fonts.css' rel='stylesheet' type='text/css'>
<script src="facility/js/Chart.min.js"></script>
<!--skycons-icons-->
<script src="facility/js/skycons.js"></script>
<link href="facility/css/demo-page.css" rel="stylesheet" media="all">
<link href="facility/css/hover.css" rel="stylesheet" media="all">
<!--//skycons-icons-->
</head>
<body>	
<div class="page-container">	
   <div class="left-content">
	   <div class="mother-grid-inner">
            <!--header start here-->
				<div class="header-main">
					<div class="header-left" style="width:100%;">
							<div class="logo-name login-block"  style="width:100%;padding:0;margin:0;">
								<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
								<center>
								<a href="index.php" style="display:inline;float:left;"><img id="logo" src="images/llogo.png" style="display:inline;width:40px;" alt="Logo"/></a>
								<input name="search" type="text" placeholder="Zip or Address" value="<?php echo (isset($_POST['search'])?$_POST['search']:"");?>" required="" style="width:50%;display:inline;margin:0;">
								<button data-toggle="modal" data-target="#myModal" type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;width:100%;margin: 0 auto;border-radius: 3px;padding: 0.3em 0.2em;font-size: 1.3em;display: block;font-family: 'Carrois Gothic', sans-serif;width:50px;display:inline;"><i class="fa fa-filter"></i></button>
								<button type="submit" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;width:100%;margin: 0 auto;border-radius: 3px;padding: 0.3em 0.2em;font-size: 1.3em;display: block;font-family: 'Carrois Gothic', sans-serif;width:50px;display:inline;"><i class="fa fa-search"></i></button>
								</center>
								</form>
							</div>
							<div class="clearfix"> </div>
						 </div>
				     <div class="clearfix"> </div>	
				</div>
<!--header end here-->
  		<!---START-->
				<div id="myModal" class="modal fade" role="dialog">
				  <div class="modal-dialog">
				    <!-- Modal content-->
				    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
				    <div class="modal-content">
				      <div class="modal-body">
				        <p>
						<hr style="margin:5px 0px 5px 0px">
						<b>Filter Features</b>
						<hr style="margin:5px 0px 5px 0px">
						<?php
							$res = mysqli_query($conn,"select * from options");
							while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
							{
								$checked = "";
								if(isset($_SESSION['filter']) && (in_array($arr['id'].'[-]'.$arr['opt'],$_SESSION['filter'])))
									$checked = "checked";
								echo '<input type="checkbox" style="margin-right:5px;" name="options[]" value="'.
											$arr['id'].'[-]'.$arr['opt'].'" '.$checked.'>'.$arr['opt'].'<br>';
							}
						?>
						<input type="hidden" name="search" id="search" value="<?php echo (isset($_POST['search'])?$_POST['search']:"");?>">
						<input type="hidden" name="action" id="action" value="applyfilter">
				        </p>
				      </div>
				      <div class="modal-footer">
				      	<button class="btn btn-primary" name="submit" id="submit" value="Create" style="background:#68AE00;border-color:#68AE00;">Apply Filter</button>
				        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				      </div>
				    </div>
						</form>
				  </div>
				</div>
			<!---END-->

<!-- script-for sticky-nav -->
		<script>
		$(document).ready(function() {
			 var navoffeset=$(".header-main").offset().top;
			 $(window).scroll(function(){
				var scrollpos=$(window).scrollTop(); 
				if(scrollpos >=navoffeset){
					$(".header-main").addClass("fixed");
				}else{
					$(".header-main").removeClass("fixed");
				}
			 });
			 
		});
		</script>
<!-- /script-for sticky-nav -->
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<div class="blankpage-main" style="padding:1em 1em;">
			<?php
			   if(isset($_SESSION['filter']))
			   {
			   		if(count($_SESSION['filter'])> 0)
			   		$filter="AND (";
			   		for($i=0;$i<count($_SESSION['filter']);$i++)
			   		{
			   			$filterArr = explode("[-]",$_SESSION['filter'][$i]);
			   			echo '<div style="background:#eee;display:inline-block;padding:5px;margin:2px;">'.
			   						$filterArr[1].
			   						' <a href="search.php?action=removefilter&id='.$filterArr[0].'" style="color:#68AE00;"><i class="fa fa-close"></i></a></div>';
			   						
			   			$filter .= " options LIKE '%,".$filterArr[0].",%' ";
			   			if(($i+1) != count($_SESSION['filter']))
			   				$filter .= " OR ";
			   		}
			   		if(count($_SESSION['filter'])> 0)
			   		{
			   			echo "<br><br>";
			   			$filter .= ")";
			   		}
			   }
			?>
			<br>
		<table id="datatable" class="table table-striped table-bordered" style="margin:0px;padding:0px;border:0px solid #000;" width="100%" cellspacing="0">
		<thead style="display:none;">
		<tr><th>Content</th></tr>
		</thead>
		<?php
			$query = "";
			if(is_numeric(isset($_POST['search'])?trim($_POST['search']):""))
			{
				//$query = "select * from facility where searchable=1 and  zipcode >= '".(isset($_POST['search'])?trim($_POST['search']):"0")."' ".($filter==""?"":$filter)." order by zipcode LIMIT 100";
				$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".trim($_POST['search'])."&sensor=false";
				$result_string = file_get_contents_curl($url);
    		$result = json_decode($result_string, true);
    		//print_r($result['results'][0]['geometry']['location']['lat']);
    		$lat = $result['results'][0]['geometry']['location']['lat'];
    		$lng = $result['results'][0]['geometry']['location']['lng'];
    		$query = "select *,(6371 * acos(cos(radians(".$lat.")) * cos(radians(lat)) * cos(radians(lng)- radians(".$lng.")) + sin(radians(".$lat.")) * sin(radians(lat)))) as distance from facility having distance < 10000 and searchable=1 order by distance limit 100";
			}
			else if(strpos((isset($_POST['search'])?trim($_POST['search']):""),",") !== false)
			{
				$searchArr = explode(",",trim($_POST['search']));
				/*if(count($searchArr) > 1)
					$query = "select * from facility where searchable=1 and companyname LIKE '%".(isset($searchArr[0])?trim($searchArr[0]):"")."%' OR city LIKE '%".(isset($searchArr[0])?trim($searchArr[0]):"")."%' or state LIKE '%".(isset($searchArr[1])?trim($searchArr[1]):"")."%'   ".($filter==""?"":$filter)." LIMIT 100";
				else*/
					$query = "select * from facility where searchable=1 and companyname LIKE '%".(isset($searchArr[0])?trim($searchArr[0]):"")."%' OR city LIKE '%".(isset($searchArr[0])?trim($searchArr[0]):"")."%' or state LIKE '%".(isset($searchArr[0])?trim($searchArr[0]):"")."%'   ".($filter==""?"":$filter)." LIMIT 100";
			}
			else
			{
				$query = "select * from facility where searchable=1 and companyname LIKE '%".(isset($_POST['search'])?trim($_POST['search']):"")."%' OR city LIKE '%".(isset($_POST['search'])?trim($_POST['search']):"")."%' or state LIKE '%".(isset($_POST['search'])?trim($_POST['search']):"")."%'   ".($filter==""?"":$filter)." order by companyname LIMIT 100";
			}
			
			$res = mysqli_query($conn,$query);
			while($arr = mysqli_fetch_array($res,MYSQLI_ASSOC))
			{
				if($arr['companyname'] == "")
					continue;
				
				echo '<tr style="margin:0px;padding:0px;border:0px solid #000;background:none;">';
				echo '<td style="background:none;margin:0px;padding:5px;border:0px solid #000;">';
				echo '<table style="width:100%;box-shadow: 5px 5px 5px #888888;"><tr>';
				echo '<td style="margin:0px;padding:0px;width:120px;vertical-align: top;border-top:1px solid #ddd;border-left:1px solid #ddd;">';
				if($arr['image'] =="")
					echo '<img src="unitimages/pna_1.jpg" style="min-height:120px;width:120px;">';
				else if(file_exists("unitimages/".$arr['image']))
					echo '<img src="unitimages/'.$arr['image'].'" style="min-height:120px;width:120px;">';
				else
					echo '<img src="'.$arr['image'].'" style="min-height:120px;width:120px;">';?>
					<br><a href="photos.php?q=<?php echo $arr['id']?>">More photos</a>
					
					<?php  
				echo '</td>';
				
				echo '<td class="login-block" style="vertical-align:top;text-align:left;width="50%";border-top:1px solid #ddd;padding: 10px 10px 0px 10px;"><b>'.$arr['companyname'].'</b><div style="float:right;padding:0;margin:0;font-size:.9em;color:#68AE00;"></div><br>';
				echo $arr['city'].",".$arr['state']." ".$arr['zipcode'].'<br>';
				echo '<font color="red">'.$arr['coupon_code']."-".$arr['coupon_desc'].'</font><br>';
				if($arr['options']!="")
				{
					showOpt($arr);
				}
				else
					echo "<br>";
					
				//echo '<button type="button" style="border: none;outline: none;cursor: pointer;color: #fff;background: #68AE00;margin: 0 auto;border-radius: 3px;font-size: 1.0em;width:30px;display:inline;" onClick="onShowUnit('.$arr['id'].');"><i class="fa fa-ellipsis-h"></i></button>';
				echo '</td><tr><td colspan=2 style="padding:0;border-left:1px solid #ddd;text-align:left">';
				echo '<p id="mdatemsg_'.$arr['id'].'" style="display:none;color:#BB0000;font-size:.9em;margin:0;margin-left: 10px;padding:0;">Enter Move-In Date</p>';
				echo '<div id="dateday_'.$arr['id'].'" class="login-block" name="dateday_'.$arr['id'].'" style="margin:3px;text-align:left;padding:0;">';
				echo '<input class="datepicker" id="mdate_'.$arr['id'].'" name="mdate_'.$arr['id'].'" type="text" placeholder="Move-in Date"  style="width:200px;height:30px;padding:5px;margin:5px;font-size:.8em;"></div>';
			
				echo '</td><tr><td colspan=2 width="40%";style="padding:0;border-left:1px solid #ddd;">';
				if($arr['units']!="")
				{
					showUnit($arr);
				}	
				
				echo'</td>';
				echo '<td class="login-block" style="vertical-align:top;color:#68AE00;text-align:left;width="25%";border-top:1px solid #ddd;padding: 2px 1px 0px 10px;">Reservations held for Move-in Date + '.$arr['reservationdays'].' days</td>';
				echo '<td class="login-block" style="vertical-align:top;color:rgb(38, 116, 166);text-align:left;width="25%";border-top:1px solid #ddd;padding: 2px 1px 0px 10px;">'.$arr['description'].'</td>';
				echo '</tr></table></td></tr>';
			}
		?>
		</table>		
    	</div>
    </div>
</div>
<!--inner block end here-->
<!--copy rights start here-->
<div class="copyright" style="background-color:#000000;text-align:center;display:block;color:#fff">
		<p>© <?php echo date("Y",time());?> Leazzer. All rights reserved | <a href="/global_footer.php">Privacy Policy and Terms of use</a>
    </p>
</div>	
<!--COPY rights end here-->
</div>
</div>
<link href="admin/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="admin/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="admin/css/datepicker.css" />
<script type="text/javascript" src="admin/js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
    $('#datatable').DataTable({
    		"aaSorting": []
    		});
	$('#datatable').on('draw.dt', function () { 
	$('.datepicker').datepicker({
     	format: 'mm/dd/yyyy',
     	startDate: new Date(),
		autoclose:true
    });
	});
	$('.datepicker').datepicker({
     	format: 'mm/dd/yyyy',
     	startDate: new Date(),
		autoclose:true
    });
});
function onShowUnit(id)
{
	if($('#unitstbl_'+id).is(':hidden'))
	{
		$('#unitstbl_'+id).show();
		$("#dateday_"+id).css("display", "inline");	
		$('#mdate_'+id).datepicker({
     	format: 'mm/dd/yyyy',
     	startDate: new Date(),
		autoclose:true
    });
	}
	else
	{
		$('#unitstbl_'+id).hide();
		$('#dateday_'+id).hide();	
	}
}
function onUnitClick(btn,cid,fid,rdays,unit,price)
{
	if($('#mdate_'+fid).val() == "")
	{
			$('#mdatemsg_'+fid).show();
	}
	else if(cid==0)
	{
			$('#mdatemsg_'+fid).hide();
			var res = ajaxcall("action=sessionreserve&fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price);
			if(res == "success")
			{
				window.location.href='customer/index.php';
			}
	}
	else
	{
			$('#mdatemsg_'+fid).hide();
			var res = ajaxcall("action=reserve&fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price);
			//if(res == "success")
			{
				btn.innerHTML = "<i class=\"fa fa-check\"></i>";
				window.location.href = "thankyou.php?fid="+fid+
									"&cid="+cid+
									"&rdays="+rdays+
									"&rdate="+$('#mdate_'+fid).val()+
									"&unit="+decodeURIComponent(unit.replace(/\+/g, ' '))+
									"&price="+price;
			}
	}
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
<!--scrolling js-->
		<script src="facility/js/jquery.nicescroll.js"></script>
		<script src="facility/js/scripts.js"></script>
		<!--//scrolling js-->
<script src="facility/js/bootstrap.js"> </script>
<!-- mother grid end here-->
</body>
</html>                        
