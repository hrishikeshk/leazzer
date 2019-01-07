<?php
include('header.php');

$res = mysqli_query($conn, "select O.auto_id as auto_id, O.pwd as pwd, M.id as facility_id, M.title as companyname, O.phone as phone, M.city as city, M.state as state, M.zip as zip, O.emailid as emailid, M.searchable as searchable, M.lat as lat, M.lng as lng, M.street as street, M.region as region, M.locality as locality, M.receivereserve as receivereserve, M.reservationdays as reservationdays, M.description as description from facility_owner O, facility_master M where O.auto_id=M.facility_owner_id and M.facility_owner_id is not null and O.auto_id ='".mysqli_real_escape_string($conn, $_SESSION['lfdata']['auto_id'])."'") or die("Error: " . mysqli_error($conn));

$arrF = mysqli_fetch_array($res, MYSQLI_ASSOC);

$facility_id = $arrF['facility_id'];

function run_fm_updates($updates){
  global $conn;
  $query = "update facility_master set ".$updates." where facility_owner_id='".mysqli_real_escape_string($conn, $_SESSION['lfdata']['auto_id'])."'";
	mysqli_query($conn, $query) or die('Failed to update'.$updates.'. Please try again: '.mysqli_error($conn));  
}

if(isset($_POST['address1'])){
  $street_locality = explode(",", mysqli_real_escape_string($conn, $_POST['address1']));
  $updates = 'street=\''.trim($street_locality[0]).'\'';
  if(count($street_locality) > 1)
    $updates .= ', locality=\''.trim($street_locality[1]).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['address2'])){
  $updates = 'region=\''.trim(mysqli_real_escape_string($conn, $_POST['address2'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['city'])){
  $updates = 'city=\''.trim(mysqli_real_escape_string($conn, $_POST['city'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['state'])){
  $updates = 'state=\''.trim(mysqli_real_escape_string($conn, $_POST['address2'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['zipcode'])){
  $updates = 'zip=\''.trim(mysqli_real_escape_string($conn, $_POST['zipcode'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['phone'])){
  $updates = 'phone=\''.trim(mysqli_real_escape_string($conn, $_POST['phone'])).'\'';
  run_fm_updates($updates);
}
if(isset($_POST['state'])){
  $updates = 'state=\''.trim(mysqli_real_escape_string($conn, $_POST['state'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['reservationdays'])){
  $updates = 'reservationdays=\''.trim(mysqli_real_escape_string($conn, $_POST['reservationdays'])).'\'';
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
  $updates = 'pdispc=\''.trim(mysqli_real_escape_string($conn, $_POST['pdispc'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdismo'])){
  $updates = 'pdismo=\''.trim(mysqli_real_escape_string($conn, $_POST['pdismo'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdispcfm'])){
  $updates = 'pdispcfm=\''.trim(mysqli_real_escape_string($conn, $_POST['pdispcfm'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdispcfmfd'])){
  $updates = 'pdispcfmfd=\''.trim(mysqli_real_escape_string($conn, $_POST['pdispcfmfd'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdispcfd'])){
  $updates = 'pdispcfd=\''.trim(mysqli_real_escape_string($conn, $_POST['pdispcfd'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdismofd'])){
  $updates = 'pdismofd=\''.trim(mysqli_real_escape_string($conn, $_POST['pdismofd'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['pdismofm'])){
  $updates = 'pdismofm=\''.trim(mysqli_real_escape_string($conn, $_POST['pdismofm'])).'\'';
  run_fm_updates($updates);
}

if(isset($_POST['change']) && $_POST['change'] == 'option'){
  $id = $_POST['id'];
  $query = "select opt from options where id='".mysqli_real_escape_string($conn, $id)."'";
  $res_opt = mysqli_query($conn, $query);
  $arr_opt = mysqli_fetch_array($res_opt, MYSQLI_ASSOC);
  $opt_str = $arr_opt['opt'];
  if($_POST['val'] == "true"){
    $query = "insert into facility_amenity(facility_id, amenity) values ('".mysqli_real_escape_string($conn, $facility_id)."', 'Other|".mysqli_real_escape_string($conn, $opt_str)."')";
	  mysqli_query($conn, $query) or die('Failed to insert options-amenities. Please try again: '.mysqli_error($conn));
  }
  else{
    $query = "delete from facility_amenity where facility_id='".mysqli_real_escape_string($conn, $facility_id)."' and amenity like '%".mysqli_real_escape_string($conn, $opt_str)."'";
	  mysqli_query($conn, $query) or die('Failed to delete options-amenities. Please try again: '.mysqli_error($conn));
	  
	  $query = "select FA.amenity as amenity from amenity_dictionary D, facility_amenity FA, options O where O.id=D.option_id and D.option_id='".$id."' and FA.facility_id='".mysqli_real_escape_string($conn, $facility_id)."' and (INSTR(FA.amenity, D.equivalent) > 0 OR INSTR(D.equivalent, FA.amenity) > 0) and D.equivalent is not null and LENGTH(D.equivalent) > 0";
    $res_opt = mysqli_query($conn, $query);
    $arr_opt = mysqli_fetch_array($res_opt, MYSQLI_ASSOC);
    while($opt_str = $arr_opt['amenity']){
      $query = "delete from facility_amenity where facility_id='".mysqli_real_escape_string($conn, $facility_id)."' and amenity = '".$opt_str."'";
	    mysqli_query($conn, $query) or die('Failed to delete facility-options-amenities. Please try again: '.mysqli_error($conn));
	  }
  }
}

if(isset($_POST['change']) && $_POST['change'] == 'unit'){
  $id = mysqli_real_escape_string($conn, $_POST['id']);
  $size = mysqli_real_escape_string($conn, $_POST['size']);
  $price = mysqli_real_escape_string($conn, $_POST['price']);
  if($_POST['val'] == "true"){
    $query = "insert into unit(facility_id, size, price_freq) values ('".mysqli_real_escape_string($conn, $facility_id)."', '".$size."', 'per month')";
    mysqli_query($conn, $query) or die('Failed to insert facility-units - Please try again: '.mysqli_error($conn));
  }
  else{
    $query = "delete from unit where facility_id='".mysqli_real_escape_string($conn, $facility_id)."' and size='".$size."' and price='".$price."'";
    error_log('del: '.$query.' : '.$size);
    mysqli_query($conn, $query) or die('Failed to delete facility-units - Please try again: '.mysqli_error($conn));
  }
}

if(isset($_POST['change']) && $_POST['change'] == 'unitval'){
  $id = mysqli_real_escape_string($conn, $_POST['id']);
  $size = mysqli_real_escape_string($conn, $_POST['size']);

  $query = "update unit set price='".mysqli_real_escape_string($conn, $_POST['val'])."' where facility_id='".mysqli_real_escape_string($conn, $facility_id)."' and size='".$size."' and auto_id='".$id."'";
  mysqli_query($conn, $query) or die('Failed to insert facility-units-price - Please try again: '.mysqli_error($conn));  
}

function run_ua_updates($updates, $unit_id, $kind){
  global $conn;
  $query = "delete from unit_amenity where unit_id='".mysqli_real_escape_string($conn, $unit_id)."' and kind='".mysqli_real_escape_string($conn, $kind)."' and kind is not null";
	mysqli_query($conn, $query) or die('Failed to apriori delete'.$updates.'. Please try again: '.mysqli_error($conn));  
	
  $query = "insert into unit_amenity (unit_id, kind, amenity) values ('".$unit_id."','".$kind."','".$updates."')";
	mysqli_query($conn, $query) or die('Failed to update'.$updates.'. Please try again: '.mysqli_error($conn));  
}

if(isset($_POST['ud']) && isset($_POST['unit_id']) && isset($_POST['kind'])){
  $updates = trim(mysqli_real_escape_string($conn, $_POST['ud']));
  run_ua_updates($updates, $_POST['unit_id'], $_POST['kind']);
}

if(isset($_POST['ud']) && isset($_POST['unit_id']) && isset($_POST['kind'])){
  $updates = trim(mysqli_real_escape_string($conn, $_POST['ud']));
  run_ua_updates($updates, $_POST['unit_id'], $_POST['kind']);
}

if(isset($_POST['ud']) && isset($_POST['unit_id']) && isset($_POST['kind'])){
  $updates = trim(mysqli_real_escape_string($conn, $_POST['ud']));
  run_ua_updates($updates, $_POST['unit_id'], $_POST['kind']);
}

if(isset($_POST['ud']) && isset($_POST['unit_id']) && isset($_POST['kind'])){
  $updates = trim(mysqli_real_escape_string($conn, $_POST['ud']));
  run_ua_updates($updates, $_POST['unit_id'], $_POST['kind']);
}

if(isset($_POST['ud']) && isset($_POST['unit_id']) && isset($_POST['kind'])){
  $updates = trim(mysqli_real_escape_string($conn, $_POST['ud']));
  run_ua_updates($updates, $_POST['unit_id'], $_POST['kind']);
}

if(isset($_POST['ud']) && isset($_POST['unit_id']) && isset($_POST['kind'])){
  $updates = trim(mysqli_real_escape_string($conn, $_POST['ud']));
  run_ua_updates($updates, $_POST['unit_id'], $_POST['kind']);
}

if(isset($_POST['ud']) && isset($_POST['unit_id']) && isset($_POST['kind'])){
  $updates = trim(mysqli_real_escape_string($conn, $_POST['ud']));
  run_ua_updates($updates, $_POST['unit_id'], $_POST['kind']);
}

