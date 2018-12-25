
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
<div class="inner-block" style="padding:2em;">
    <div class="blank" style="min-height: 0;">
    	<div class="blankpage-main" id="searchmain"style="padding:1em 1em;">
		</div>
	</div>
</div>
<div class="copyright">
	<div class="container">
		<p>© <?php echo date("Y",time());?> Leazzer. All rights reserved | <a href="/global_footer.php">Privacy Policy</a> | <a href="/global_footer_tu.php">Terms of use</a>
    </p>
	</div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.easing.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="admin/css/datepicker.css" />
<script type="text/javascript" src="admin/js/bootstrap-datepicker.js"></script>
<script>

<?php
  
  function relay_posts(){
    $query_str = '';
  	if(isset($_POST['search']))
  	  $query_str .= "search=".$_POST['search'];
  	if(isset($_POST['action'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "action=".$_POST['action'];
  	}
  	if(isset($_POST['id'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "id=".$_POST['id'];
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
  	  $query_str .= "search=".$_GET['search'];
  	if(isset($_GET['action'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "action=".$_GET['action'];
  	}
  	if(isset($_GET['id'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "id=".$_GET['id'];
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
  	  $query_str .= "slat=".$_POST['slat'];
  	}
  	if(isset($_POST['slng'])){
  	  if(strlen($query_str) > 0)
  	    $query_str .= '&';
  	  $query_str .= "slng=".$_POST['slng'];
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
   		 	    alert('Failed to invoke serverside function(in search)... Please try again in some time' + err);
   		 	    res = false;
   		 	}
    });
    return res;
}

</script>

</body>
</html>

