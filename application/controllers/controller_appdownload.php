<?php
class controller_appdownload extends Controller
{
	function __construct(){
		$this->view = new View('index.tpl');
		$this->view->headers['title'] = 'Приложение | Город 24 онлайн';
	}
	
	public function action_index($array = array()){

	}
	
	
}