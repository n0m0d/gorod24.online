<?php
class controller_exit extends Controller
{
	function __construct(){
		$this->view = new View('login.tpl');
		$this->view->setTemplatesFolder(ADMINDIR.'/application/views/');
		$this->view->headers['title'] = 'Авторизация | Администрирование Полезного радио';
	}
	
	function action_index($array = array()){
		setcookie('session_id', '', time()-1, '/'); 
		setcookie('session_id', '', 0, '/'); 
		session_unset();
		session_destroy();
		header('Location: '.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/');
	}
	
}