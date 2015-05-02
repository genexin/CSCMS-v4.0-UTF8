<?php
@header('Content-Type: text/html; charset=utf-8');
require_once('upyun.class.php');
require_once('../../../cscms/lib/Cs_Upyun.php');
$uri = $_SERVER["REQUEST_URI"];
$code = explode("down.php",$uri);
$path = explode("?size",$code[1]);
$upyun = new UpYun(CS_Upy_Bucket, CS_Upy_Name, CS_Upy_Pwd);
header('Content-type: application/force-download');
echo $upyun->readFile($path[0]);
