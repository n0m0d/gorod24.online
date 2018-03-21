<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define ( 'APPDIR' , dirname(__FILE__));
include_once APPDIR ."/application/includes.php"; // файлы ядра

getPlugins();
cron(4);
?>