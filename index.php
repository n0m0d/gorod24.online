<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define ( 'APPDIR' , dirname(__FILE__));
define ( 'START_TIME' , microtime());
include 'application/bootstrap.php';
