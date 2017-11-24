<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define ( 'APPDIR' , dirname(__FILE__)."/../../..");

	
include_once APPDIR ."/application/includes.php"; // файлы ядра
$URL = parse_url($_SERVER['REQUEST_URI']);

$path = $URL['path'];
$file = explode('/', $path);
$file = array_pop($file);

$settings = explode('_', $file);
$id = (int)$settings[0];
$w = $settings[1];
$h = $settings[2];
$model_uploads = new model_uploads();
$image = $model_uploads->getItem($id);
if($image){
if(file_exists(APPDIR.'/uploads/image/cash/'.$file)){
	header('Content-Type: image/'.$image['ext']);
	readfile(APPDIR.'/uploads/image/cash/'.$file);
	exit();
}
elseif(file_exists(APPDIR.$image['destination'].$image['name'])){
	
	image_resize(APPDIR.$image['destination'].$image['name'], APPDIR.'/uploads/image/cash/'.$file, $w, ($h==0?false:$h), 100);
	header('Content-Type: image/'.$image['ext']);
	readfile(APPDIR.'/uploads/image/cash/'.$file);
	exit();
}
}
?>