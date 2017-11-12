<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define ( 'ADMINDIR' , dirname(__FILE__));
define ( 'APPDIR' , ADMINDIR.'/..');
$is_admin = true;
include 'application/bootstrap.php';
