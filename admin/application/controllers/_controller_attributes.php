<?php

class controller_attributes extends Controller
{

	function action_index($array = array())
	{
		if( $this->getPermissions('attributes') ){
			if(isset($this->GET['at'])) {
				$at_id = ($this->GET['at'] == 'new')? 'new' : (int)$this->GET['at'];
				$attribute = ($at_id != 'new')? $this->model->get_attribute($at_id) : 'new';
				if(is_array($attribute)){
					$this->registry->set('title', 'Панель администрирования | Атрибут "'.apply_filters('name-ru', $attribute['at_name']).'"');
					$this->view->template = 'admin_view.php';
						$options = $this->model->get_options($attribute['at_id']);
					$this->view->generate(ADMINDIR.'/application/views/attribute_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('attribute'=>$attribute, 'options' => $options));
				} 
				elseif($attribute == 'new'){
					$this->registry->set('title', 'Панель администрирования | Создать новый атрибут');
					$this->view->template = 'admin_view.php';
					$this->view->generate(ADMINDIR.'/application/views/attribute_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('attribute'=>$attribute));
				}				
				else { Route::ErrorPage404($this->registry);}
			} else {
				$this->registry->set('title', 'Панель администрирования | Атрибуты');
				$this->view->template = 'admin_view.php';
				$attributes = $this->model->get_attributes();
				$this->view->generate(ADMINDIR.'/application/views/attributes_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('attributes'=>$attributes));
			}
		}
		else {
				$this->generateLoginForm();
		}
	}
}
?>