<?php

class controller_files extends Controller
{

	function action_index($array = array())
	{
		if( $this->getPermissions('files') ){
			if(isset($this->GET['id'])) {
				$file_id = ($this->GET['id'] == 'new')? 'new' : (int)$this->GET['id'];
				$file = ($file_id == 'new')? 'new' : $this->model->get_file($file_id);
				
				if(is_array($file)){
					
					$attributes = $this->model->get_page_attributes($file_id);
					$this->registry->set('title', 'Панель администрирования | Медиафайл "'.apply_filters('name-ru', $page['post_name_ru']).'"');
					$this->view->template = 'admin_view.php';
					$this->view->generate(
											ADMINDIR.'/application/views/file_view.php', 
											ADMINDIR.'/application/views/'.$this->view->template, 
											array('file'=>$file, 'attributes' => $attributes)
										);
				}
				elseif($this->GET['id'] == 'new'){
					$this->registry->set('title', 'Панель администрирования | Загрузить медиафайл');
					$this->view->template = 'admin_view.php';
					$this->view->generate(
											ADMINDIR.'/application/views/file_view.php', 
											ADMINDIR.'/application/views/'.$this->view->template, 
											array('file'=>$file)
										);
				}				
				else { Route::ErrorPage404($this->registry);}

			} else {
				$this->registry->set('title', 'Панель администрирования | Медиафайлы');
				$this->view->template = 'admin_view.php';
				$files = $this->model->get_files(array(
														'limit' => 20,
														'page' => $_GET['page'],
														'search' => $_GET['search'],
														));
				$this->view->generate(ADMINDIR.'/application/views/files_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('files'=>$files));
			}
		}
		else {
				$this->generateLoginForm();
		}
	}
}
?>