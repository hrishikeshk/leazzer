<?php
$GError="";
include('header.php');
$uid=$_SESSION['lfdata']['id'];


?>
<!--inner block start here-->
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="multiple_image_upload/script.js"></script>
		
		<!-------Including CSS File------>
        <link rel="stylesheet" type="text/css" href="multiple_image_upload/style.css">
<div class="inner-block">
    <div class="blank">
    	<h2>Upload Multiple Images</h2>
    	<div class="blankpage-main">
    		<center>
			   <form enctype="multipart/form-data" action="" method="post">
                    First Field is Compulsory. Only JPEG,PNG,JPG Type Image Uploaded. Image Size Should Be Less Than 100KB.
                    <hr/>
                    <div id="filediv"><input name="file[]" type="file" id="file"/></div><br/>
					<input name="uid" type="hidden" id="uid"/>
                    <input type="button" id="add_more" class="upload" value="Add More Files"/>
                    <input type="submit" value="Upload File" name="submit" id="upload" class="upload"/>
                </form>									
				</center>
				 <?php include "upload.php"; ?>
    	</div>
    </div>
</div>
<!--inner block end here-->
<?php
include('footer.php');
?>