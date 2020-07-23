<?php
$sver = 'local';
// $sver = 'sakura';

if($sver == 'local'){
  $conn['db_name'] = "php_contest";
  $conn['db_host'] = "localhost";
  $conn['db_id'] = "root";
  $conn['db_pw'] = "";
  $url = "http://localhost/gs_code/";
}else if($sver == 'sakura'){
  $conn['db_name'] = " add DB name  ";
  $conn['db_host'] = " add DB host name";
  $conn['db_id'] = " add DB id";
  $conn['db_pw'] = " add DB pass word";  
  $url = " add DB location";
};


?>