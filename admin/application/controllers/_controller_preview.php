<?php

class controller_preview extends Controller
{

	function action_index($array = array())
	{	
		$this->registry->set('title', 'Панель администрирования');
		$this->view->template = 'admin_view.php';
		
		$this->view->generate(ADMINDIR.'/application/views/preview_view.php', ADMINDIR.'/application/views/'.$this->view->template);
	}
}
?>