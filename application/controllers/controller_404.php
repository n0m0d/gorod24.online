<?php
class Controller_404 extends Controller
{
	function action_index($array = array()){
		
		$this->view = new View('404.tpl');
		
	}

}
