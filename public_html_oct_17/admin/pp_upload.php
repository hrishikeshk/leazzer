<?php
$GError="";
include('header.php');

function upload_pp(){
  $target_dir = "../";

  $target_file = $target_dir.'pp.pdf';
  $uploadOk = 1;

  if ($_FILES["pp"]["size"] > 1024 * 1024 * 1024) {
    $GError = "Sorry, your file is too large.";
    $uploadOk = 0;
  }

  if ($uploadOk == 0){
    $GError = "Sorry, your file was not uploaded.";
    return 4;
  }
  else {
    if (file_exists($target_file)){
      echo "File already exists. Overwriting ... ";
      unlink($target_file);
    }

    if (move_uploaded_file($_FILES["pp"]["tmp_name"], $target_file)){
        //echo "The file ". basename( $_FILES["pp.pdf"]["name"]). " has been uploaded.";
        return $_FILES["pp"]["error"];
    }
    else{
        $GError = "Sorry, there was an error uploading your file.";
        return 4;
    }
  }
}

if(isset($_POST['submit'])){
  if($_FILES['pp']['name']){

    $arrFileName = explode('.', $_FILES['pp']['name']);

    if($arrFileName[count($arrFileName) - 1] == 'pdf'){

      //$handle = fopen($_FILES['pp.pdf']['tmp_name'], "r");
      $ret = upload_pp();
      if($ret == 0)
        $GError = "Successfully uploaded the privacy policy pdf.";
      else
        $GError = "Sorry, there was an error uploading your file.";
      //fclose($handle);

    }
    else{
        $GError = "Sorry, there was an error uploading your file (Not pdf).";
    }
  }
}

?>
<!--inner block start here-->
<div class="inner-block">
    <div class="blank">
    	<h2>Privacy Policy</h2>
    	<div class="blankpage-main">
    		<center>
			<form name="pp_ulfrm" id="pp_ulfrm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype='multipart/form-data'>

    			<hr>
					<?php
					if($GError != ""){
						echo "<div class=\"alert alert-info\" role=\"alert\">".$GError."</div>";
					}
					?>
							<h4>Upload Privacy Policy(.pdf)</h4>
							<input class="form-control" type="file" name="pp" id="userfile"  style="width:20%;height:10%"><br>
			    		<button class="btn btn-success" name="submit" value="pp_upload" style="background:#68AE00;border-color:#68AE00;">Upload File</button>
					</form>												
				</center>
    	</div>
    </div>
</div>
<!--inner block end here-->

<?php
include('footer.php');
?>

