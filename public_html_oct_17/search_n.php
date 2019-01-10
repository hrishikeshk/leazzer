
<!DOCTYPE html>
<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<meta name="keywords" content="Leazzer" />
<!--link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all">
<link href="css/owl.carousel.css" rel="stylesheet">
<link rel="stylesheet" href="css/jquery-ui.css" />
<link rel="stylesheet" href="css/chocolat.css" type="text/css">
<link href="facility/css/style.css" rel="stylesheet" type="text/css" media="all"/>
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<link href="fonts/fonts.css" rel="stylesheet"-->

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATdAW-nZvscm35rSLI8Bu9eGq84odzVLA&libraries=places"
        async defer></script>
</head>
<body>
  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav navbar-right">
  		<li><a href="facility/register.php">Add Facility</a></li>
	  	<li><a href="facility/dashboard.php"><?php echo (isset($_SESSION['lfdata'])?$_SESSION['lfdata']['firstname']:"Owner Login");?></a></li>
      <li><a href="customer/dashboard.php"><?php echo (isset($_SESSION['lcdata'])?$_SESSION['lcdata']['firstname']:"Login");?></a></li>
      <li><a href="faq.php">FAQ</a></li>
      <li><a href="contactus.php">Contact Us</a></li>
      <?php
      if(isset($_SESSION['lcdata']) || isset($_SESSION['lfdata']))
      	echo '<li><a href="index.php?action=logout">Logout</a></li>';
      ?>
    </ul>
  </div><!-- /.navbar-collapse -->
<div class="inner-block" style="padding:2em;">
    <div class="blank" style="min-height: 0;">
    	<div class="blankpage-main" id="searchmain"style="padding:1em 1em;">
		</div>
	</div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/jquery.easing.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="admin/css/datepicker.css" />
<script type="text/javascript" src="admin/js/bootstrap-datepicker.js"></script>

<link href="facility/css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<link href="facility/css/style.css" rel="stylesheet" type="text/css" media="all"/>
<!--script src="facility/js/jquery-2.1.1.min.js"></script -->
<link href="facility/css/font-awesome.css" rel="stylesheet">
<link href='facility/fonts/fonts.css' rel='stylesheet' type='text/css'>
<!-- script src="facility/js/Chart.min.js"></script -->
<!--skycons-icons-->
<!-- script src="facility/js/skycons.js"></script -->
<link href="facility/css/demo-page.css" rel="stylesheet" media="all">
<link href="facility/css/hover.css" rel="stylesheet" media="all">

<!-- script src="js/jquery.min.js"></script -->
<!-- script src="js/jquery.easing.min.js"></script -->
<!-- script src="js/bootstrap.min.js"></script -->
<!-- link rel="stylesheet" type="text/css" href="admin/css/datepicker.css" / -->
<!-- script type="text/javascript" src="admin/js/bootstrap-datepicker.js"></script -->

<link href="admin/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="admin/js/jquery.dataTables.min.js"></script>
<!-- link rel="stylesheet" type="text/css" href="admin/css/datepicker.css" / -->

<div class="copyright">
	<div class="container">
		<center><p>© <?php echo date("Y",time());?> Leazzer. All rights reserved | <a href="/global_footer.php">Privacy Policy</a> | <a href="/global_footer_tu.php">Terms of use</a>
    </p></center>
	</div>
</div>

<script>

<?php
  
  function relay_posts(){
    $query_str = '';
  	if(isset($_POST['search']))
  	  $query_str .= "search=".htmlspecialchars($_POST['search'], ENT_QUOTES);
  	if(isset($_POST['action'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "action=".htmlspecialchars($_POST['action'], ENT_QUOTES);
  	}
  	if(isset($_POST['id'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "id=".htmlspecialchars($_POST['id'], ENT_QUOTES);
  	}
  	if(isset($_POST['options'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "options_s=".serialize($_POST['options']);
  	}
  	else if(isset($_POST['options_s'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "options_s=".$_POST['options_s'];
  	}
  	if(isset($_GET['search']))
  	  $query_str .= "search=".htmlspecialchars($_GET['search'], ENT_QUOTES);
  	if(isset($_GET['action'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "action=".htmlspecialchars($_GET['action'], ENT_QUOTES);
  	}
  	if(isset($_GET['id'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "id=".htmlspecialchars($_GET['id'], ENT_QUOTES);
  	}
  	if(isset($_GET['options'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "options_s=".serialize($_GET['options']);
  	}
  	else if(isset($_GET['options_s'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "options_s=".$_GET['options_s'];
  	}
  	
  	if(isset($_POST['slat'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "slat=".htmlspecialchars($_POST['slat'], ENT_QUOTES);
  	}
  	if(isset($_POST['slng'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "slng=".htmlspecialchars($_POST['slng'], ENT_QUOTES);
  	}
  	return $query_str;
  }
?>

$(document).ready(function(){
  <?php
  	echo 'var res = ajaxcall_search(\''.relay_posts().'\');';
  ?>
	$('#searchmain').html(res);
	
	$('.datepicker').datepicker({
     	format: 'mm/dd/yyyy',
     	startDate: new Date(),
		autoclose:true
  });
  $('#datatable').DataTable({
    		"aaSorting": []
  });
	$('#datatable').on('draw.dt', function (){ 
  	$('.datepicker').datepicker({
     	format: 'mm/dd/yyyy',
     	startDate: new Date(),
	  	autoclose:true
    });
	});
	
});

function ajaxcall_search(datastring){
    var res;
    $.ajax
    ({	
    		type:"POST",
    		url:"search_main.php",
    		data:datastring,
    		cache:false,
    		async:false,
    		success: function(result){		
   				 	res = result;
   		 	},
   		 	error: function(err){
   		 	    alert('Failed to invoke serverside function(in search)... Please try again in some time');
   		 	    res = false;
   		 	}
    });
    return res;
}

</script>

</body>
</html>

