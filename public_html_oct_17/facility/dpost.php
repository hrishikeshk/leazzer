<?php
include('header.php');

$res = mysqli_query($conn, "select O.auto_id as auto_id, O.pwd as pwd, M.id as facility_id, M.title as companyname, O.phone as phone, M.city as city, M.state as state, M.zip as zip, O.emailid as emailid, M.searchable as searchable, M.lat as lat, M.lng as lng, M.street as street, M.region as region, M.locality as locality, M.receivereserve as receivereserve, M.reservationdays as reservationdays, M.description as description from facility_owner O, facility_master M where O.auto_id=M.facility_owner_id and M.facility_owner_id is not null and O.auto_id ='".$_SESSION['lfdata']['auto_id']."'") or die("Error: " . mysqli_error($conn));

$arrF = mysqli_fetch_array($res, MYSQLI_ASSOC);

$facility_id = $arrF['facility_id'];

function run_fm_updates($updates){
  global $conn;
  $query = "update facility_master set ".$updates." where facility_owner_id='".$_SESSION['lfdata']['auto_id']."'";
	mysqli_query($conn, $query) or die('Failed to update'.$updates.'. Please try again: '.mysqli_error($conn));  
}

if(isset($_POST['address1'])){
  $street_locality = explode(",", $_POST['address1']);
  $updates = 'street=\''.trim($street_locality[0]).'\'';
  if(count($street_locality) > 1)
    $updates .= ', locality=\''.trim($street_locality[1]).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['address2'])){
  $updates = 'region=\''.trim($_POST['address2']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['city'])){
  $updates = 'city=\''.trim($_POST['city']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['state'])){
  $updates = 'state=\''.trim($_POST['address2']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['zipcode'])){
  $updates = 'zip=\''.trim($_POST['zipcode']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['phone'])){
  $updates = 'phone=\''.trim($_POST['phone']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['reservationdays'])){
  $updates = 'reservationdays=\''.trim($_POST['reservationdays']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['change']) && $_POST['change'] == 'searchable'){
  $v = 1;
  if($_POST['val'] == "true")
    $v = 0;
  $updates = 'searchable=\''.$v.'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdispc'])){
  $updates = 'pdispc=\''.trim($_POST['pdispc']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdismo'])){
  $updates = 'pdismo=\''.trim($_POST['pdismo']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdispcfm'])){
  $updates = 'pdispcfm=\''.trim($_POST['pdispcfm']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdispcfmfd'])){
  $updates = 'pdispcfmfd=\''.trim($_POST['pdispcfmfd']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdispcfd'])){
  $updates = 'pdispcfd=\''.trim($_POST['pdispcfd']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdismofd'])){
  $updates = 'pdismofd=\''.trim($_POST['pdismofd']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdismofm'])){
  $updates = 'pdismofm=\''.trim($_POST['pdismofm']).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['change']) && $_POST['change'] == 'option'){
  $id = $_POST['id'];
  $query = "select opt from options where id='".$id."'";
  $res_opt = mysqli_query($conn, $query);
  $arr_opt = mysqli_fetch_array($res_opt, MYSQLI_ASSOC);
  $opt_str = $arr_opt['opt'];
  if($_POST['val'] == "true"){
    $query = "insert into facility_amenity(facility_id, amenity) values ('".$facility_id."', 'Other|".$opt_str."')";
	  mysqli_query($conn, $query) or die('Failed to insert options-amenities. Please try again: '.mysqli_error($conn));
  }
  else{
    $query = "delete from facility_amenity where facility_id='".$facility_id."' and amenity like '%".$opt_str."'";
	  mysqli_query($conn, $query) or die('Failed to delete options-amenities. Please try again: '.mysqli_error($conn));
	  
	  
	  $query = "select FA.amenity as amenity from amenity_dictionary D, facility_amenity FA, options O where O.id=D.option_id and D.option_id='".$id."' and FA.facility_id='".$facility_id."' and (INSTR(FA.amenity, D.equivalent) > 0 OR INSTR(D.equivalent, FA.amenity) > 0) and D.equivalent is not null and LENGTH(D.equivalent) > 0";
    $res_opt = mysqli_query($conn, $query);
    $arr_opt = mysqli_fetch_array($res_opt, MYSQLI_ASSOC);
    while($opt_str = $arr_opt['amenity']){
      $query = "delete from facility_amenity where facility_id='".$facility_id."' and amenity = '".$opt_str."'";
	    mysqli_query($conn, $query) or die('Failed to delete facility-options-amenities. Please try again: '.mysqli_error($conn));
	  }
  }
}

if(isset($_POST['change']) && $_POST['change'] == 'unit'){
  $id = $_POST['id'];
  $size = mysqli_real_escape_string($conn, $_POST['size']);
  $price = mysqli_real_escape_string($conn, $_POST['price']);
  if($_POST['val'] == "true"){
    $query = "insert into unit(facility_id, size, price_freq) values ('".$facility_id."', '".$size."', 'per month')";
    error_log('ins: '.$query.' : '.$size);
    mysqli_query($conn, $query) or die('Failed to insert facility-units - Please try again: '.mysqli_error($conn));
  }
  else{
    $query = "delete from unit where facility_id='".$facility_id."' and size='".$size."' and price='".$price."'";
    error_log('del: '.$query.' : '.$size);
    mysqli_query($conn, $query) or die('Failed to delete facility-units - Please try again: '.mysqli_error($conn));
  }
}

if(isset($_POST['change']) && $_POST['change'] == 'unitval'){
  $id = $_POST['id'];
  $size = mysqli_real_escape_string($conn, $_POST['size']);

  $query = "update unit set price='".$_POST['val']."' where facility_id='".$facility_id."' and size='".$size."'";
  mysqli_query($conn, $query) or die('Failed to insert facility-units-price - Please try again: '.mysqli_error($conn));  
}

