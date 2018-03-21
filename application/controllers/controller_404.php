<?php
class Controller_404 extends Controller
{
	function action_index($array = array()){
		
		$this->view = new View('404.tpl');
		$this->view->headers['title'] =  '404 - страница не найдена. Город24: Всегда там где ты!';
		$this->view->notRender();
		$this->view->renderBody();
	}

}
