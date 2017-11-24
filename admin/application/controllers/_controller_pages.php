<?php

class controller_pages extends Controller
{

	function action_index($array = array())
	{
		if( $this->getPermissions('pages') ){
			if(isset($this->GET['id'])) {
				$page_id = ($this->GET['id'] == 'new')? 'new' : (int)$this->GET['id'];
				$page = ($page_id == 'new')? 'new' : $this->model->get_page($page_id);
				
				$all_attributes = $this->model->get_attributes();
				$pages = $this->model->get_pages();
				if(is_array($page)){
					
					$attributes = $this->model->get_page_attributes($page_id);
					$childs = $this->model->get_childs($page_id);
					$this->registry->set('title', 'Панель администрирования | Страница "'.apply_filters('name-ru', $page['post_name_ru']).'"');
					$this->view->template = 'admin_view.php';
					$pages = $this->model->get_pages();
					$this->view->generate(
											ADMINDIR.'/application/views/page_view.php', 
											ADMINDIR.'/application/views/'.$this->view->template, 
											array('page'=>$page, 'attributes' => $attributes, 'all_attributes' => $all_attributes,'childs'=> $childs, 'pages' => $pages)
										);
				}
				elseif($this->GET['id'] == 'new'){
					$this->registry->set('title', 'Панель администрирования | Создать новую страницу');
					$this->view->template = 'admin_view.php';
					$this->view->generate(
											ADMINDIR.'/application/views/page_view.php', 
											ADMINDIR.'/application/views/'.$this->view->template, 
											array('page'=>$page, 'attributes' => $attributes, 'all_attributes' => $all_attributes,'childs'=> $childs, 'pages' => $pages)
										);
				}				
				else { Route::ErrorPage404($this->registry);}

			} else {
				$this->registry->set('title', 'Панель администрирования | Страницы');
				$this->view->template = 'admin_view.php';
				$pages = $this->model->get_pages(array(
														'limit' => 20,
														'page' => $_GET['page'],
														'search' => $_GET['search'],
														));
				$this->view->generate(ADMINDIR.'/application/views/pages_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('pages'=>$pages));
			}
		}
		else {
				$this->generateLoginForm();
		}
	}
}
?>