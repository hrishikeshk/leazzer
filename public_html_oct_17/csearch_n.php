
<!DOCTYPE html>
<html>
<head>
<title>Leazzer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<meta name="keywords" content="Leazzer" />
</head>
<body>

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
<link href="facility/css/font-awesome.css" rel="stylesheet">
<link href='facility/fonts/fonts.css' rel='stylesheet' type='text/css'>
<link href="facility/css/demo-page.css" rel="stylesheet" media="all">
<link href="facility/css/hover.css" rel="stylesheet" media="all">

<link href="admin/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="admin/js/jquery.dataTables.min.js"></script>

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
    $qa = false;
  	if(isset($_POST['search'])){
  	  $query_str .= "search=".htmlspecialchars($_POST['search'], ENT_QUOTES);
  	  $qa = true;
  	}
  	if(isset($_POST['action'])){
  	  if($qa === true)
  	    $query_str .= '&';
  	  $query_str .= "action=".htmlspecialchars($_POST['action'], ENT_QUOTES);
  	  $qa = true;
  	}
  	if(isset($_POST['id'])){
  	  if($qa === true)
  	    $query_str .= '&';
  	  $query_str .= "id=".htmlspecialchars($_POST['id'], ENT_QUOTES);
  	  $qa = true;
  	}
  	if(isset($_POST['options'])){
  	  if($qa === true)
  	    $query_str .= '&';
  	  $query_str .= "options_s=".serialize($_POST['options']);
  	  $qa = true;
  	}
  	else if(isset($_POST['options_s'])){
  	  if($qa === true)
  	    $query_str .= '&';
  	  $query_str .= "options_s=".$_POST['options_s'];
  	  $qa = true;
  	}
  	if(isset($_GET['search']))
  	  $query_str .= "search=".htmlspecialchars($_GET['search'], ENT_QUOTES);
  	if(isset($_GET['action'])){
  	  if($qa === true)
  	    $query_str .= '&';
  	  $query_str .= "action=".htmlspecialchars($_GET['action'], ENT_QUOTES);
  	  $qa = true;
  	}
  	if(isset($_GET['id'])){
  	  if($qa === true)
  	    $query_str .= '&';
  	  $query_str .= "id=".htmlspecialchars($_GET['id'], ENT_QUOTES);
  	  $qa = true;
  	}
  	if(isset($_GET['options'])){
  	  if($qa === true)
  	    $query_str .= '&';
  	  $query_str .= "options_s=".serialize($_GET['options']);
  	  $qa = true;
  	}
  	else if(isset($_GET['options_s'])){
  	  if($qa === true)
  	    $query_str .= '&';
  	  $query_str .= "options_s=".$_GET['options_s'];
  	  $qa = true;
  	}
  	
  	if(isset($_POST['slat'])){
  	  if($qa === true)
  	    $query_str .= '&';
  	  $query_str .= "slat=".htmlspecialchars($_POST['slat'], ENT_QUOTES);
  	  $qa = true;
  	}
  	if(isset($_POST['slng'])){
  	  if($qa === true)
  	    $query_str .= '&';
  	  $query_str .= "slng=".htmlspecialchars($_POST['slng'], ENT_QUOTES);
  	}
  	if(isset($_GET['facility_id'])){
  	  if($qa === true)
  	    $query_str .= '&';
  	  $query_str .= "facility_id=".htmlspecialchars($_GET['facility_id'], ENT_QUOTES);
  	}
  	return $query_str;
  }
?>
<?php
  	echo 'var res = ajaxcall_search(\''.relay_posts().'\');';
  ?>
$(document).ready(function(){
});

function ajaxcall_search(datastring){
    var res;
    $.ajax
    ({	
    		type:"POST",
    		url:"csearch_main.php",
    		data:datastring,
    		cache:false,
    		async:true,
    		success: function(result){		
   				 	res = result;
   				 	$('#searchmain').html(res);
	
          	$('.datepicker').datepicker({
               	format: 'mm/dd/yyyy',
               	startDate: new Date(),
            		autoclose:true
            });
            $('#datatable').DataTable({
            		"aaSorting": [],
            		"paging": false,
            		"ordering": false,
            		"searching": false
            });
          	$('#datatable').on('draw.dt', function (){ 
            	$('.datepicker').datepicker({
               	format: 'mm/dd/yyyy',
               	startDate: new Date(),
          	  	autoclose:true
              });
          	});
   		 	},
   		 	error: function(err){
   		 	    alert('Failed to invoke serverside function(in csearch)... Please try again in some time');
   		 	    res = false;
   		 	}
    });
    return res;
}

</script>

</body>
</html>

