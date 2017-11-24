<?php

class Controller_404 extends Controller
{
	
	function action_index($array = array())
	{
		if ($this->view) $this->view->generate('404_view.php', $this->view->template); else{
			$error404 = new View();
			$error404->generate('404_view.php',  $error404->template);
		}
	}

}
