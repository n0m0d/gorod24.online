<?php

class controller_settings extends Controller
{
	function __construct(){
		$this->view = new View('sett.tpl');
		$this->view->setTemplatesFolder(ADMINDIR.'/application/views/');
		$this->view->headers['title'] = 'Настройки | Администрирование Полезного радио';
		$this->view->data['main-menu']['Настройки'] = true;
	}
	
	function action_index($array = array()){
		/*
		$this->view->template = 'admin_view.php';
		$this->view->generate(ADMINDIR.'/application/views/index_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('rubrics' => $rubrics));
		*/
	}
	
	
}