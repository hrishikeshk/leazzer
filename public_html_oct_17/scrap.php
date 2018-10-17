<?php 
include('simple_html_dom.php');
$url="https://www.selfstorage.com/self-storage/kansas/cherryvale/big-hill-storage-west-3rd-158463/";
$html=file_get_html($url);
echo $html;
?>