<?php

class controller_tasks extends Controller
{
	function __construct(){
		$this->view = new View('index.tpl');
		$this->view->setTemplatesFolder(ADMINDIR.'/application/views/');
		$this->view->headers['title'] = 'Задачи | Администрирование Полезного радио';
		$this->view->data['main-menu']['Задачи'] = true;
		$this->view->data['main']['header'] = 'Задачи';
	}
	
	function action_index($array = array()){
		/*
		$this->view->template = 'admin_view.php';
		$this->view->generate(ADMINDIR.'/application/views/index_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('rubrics' => $rubrics));
		*/
	}
	
	
}