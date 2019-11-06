<?php
session_start();
require_once('mail/class.phpmailer.php');		

function email_ss($target_file_url){
	global $conn,$GError;
	$fromemail="no-reply@Brainyvestors.com"; 
	$toemail=$_SESSION['lcdata']['emailid']; 
	$message = '<table width="100%" cellpadding="0" cellspacing="0">';
	$message .= '<tr><td>';
	$message .= '<center><img src="Brainyvestors.com/images/llogo.jpg" height="120px"><hr style="width:100%;margin-top:10px;margin-bottom:10px;border-top: 2px solid #30B242;"></center><br>';
	$message .= 'Hello <b>'.$_SESSION['lcdata']['firstname'].' '.$_SESSION['lcdata']['lastname'].',';
	$message .= '<br><br><b>Emailing your screenshot below, your UserID is '.$_SESSION['lcdata']['emailid'].'</b><br>';
	$message .= '<a href="'.$target_file_url.'">View Map</a>';
	$message .= '<img src="'.$target_file_url.'" style="width:100%"></img>';
	$message .= '</td></tr>';
	$message .= '<tr><td><br><br>';
	$message .= 'Thank you,<br>&mdash; Brainyvestors';
	$message .= '</td></tr>';
	$message .= '</table>';

	$mail = new PHPMailer(); // defaults to using php "mail()"
	$mail->CharSet = 'UTF-8';
	$mail->AddReplyTo($fromemail,"Brainyvestors Screenshot"); 
	$mail->SetFrom($fromemail, "Brainyvestors Screenshot");
	$mail->AddAddress($toemail, substr($toemail,0,strpos($toemail,"@")));
	$mail->Subject    = "Brainyvestors - Your screen record";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->MsgHTML($message);
	$mail->isHTML(true);
	$ret = $mail->Send();
	return "";								
}

function upload_file($emailid, $data, $target_file){
  
  if (file_exists($target_file)) {
    error_log( "File already exists. Overwriting ... " );
    unlink($target_file);
  }

  /*if (move_uploaded_file($data, $target_file)){
      return $_FILES["fileToUpload"]["error"];
  }
  else{
    return 4;
  }*/
  file_put_contents($target_file, $data);
  return $target_file;
}

if(isset($_SESSION['lcdata'])){
  if(isset($_POST['data'])){
    $target_dir = $dirpath = realpath(dirname(getcwd())) . "/public_html/sandbox/";
    if(!file_exists($target_dir)){
      mkdir($target_dir, 0755, false);
    }
    $target_file = $target_dir . $_SESSION['lcdata']['emailid'] . 'ss';
  ////$address = str_replace(';', '&', $address);
    upload_file($_SESSION['lcdata']['emailid'], str_replace(';', '&', $_POST['data']), $target_file);
    //email_ss('https://www.brainyvestors.com/sandbox/'.$_SESSION['lcdata']['emailid'] . 'ss');
    email_ss(str_replace(';', '&', $_POST['data'].'&key='));
  }
  else{
    error_log('Failed to find data in post');
  }
}
else{
  error_log('Failed to find session in post');
}

?>

