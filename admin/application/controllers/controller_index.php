<?php

class controller_index extends Controller
{
	function __construct(){
		$this->view = new View('index.tpl');
		$this->view->setTemplatesFolder(ADMINDIR.'/application/views/');
		$this->view->headers['title'] = 'Главная | Администрирование Полезного радио';
		$this->view->data['main']['header'] = 'Администрирование';
	}
	
	function action_index($array = array()){
		/*
		$this->view->template = 'admin_view.php';
		$this->view->generate(ADMINDIR.'/application/views/index_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('rubrics' => $rubrics));
		*/
	}
	
}