<?php
class controller_onas extends Controller
{
	function __construct(){
		//$this->view = new View('index.tpl');
		//$this->view->headers['title'] = 'О нас | Полезное радио';
	}
	
	function action_index($array = array()){
		$news = new model_news();
		$test = $news->getTown('Керчь');
		
		var_dump($test);
		//$this->view->data['file'] = __FILE__;
	}
}