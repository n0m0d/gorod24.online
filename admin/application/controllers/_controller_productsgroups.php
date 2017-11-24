<?php

class controller_productsgroups extends Controller
{

	function action_index($array = array())
	{
		if( $this->getPermissions('products') ){
			if(isset($this->GET['id'])) {
				$id = ($this->GET['id'] == 'new')? 'new' : (int)$this->GET['id'];
				$group = ($id == 'new')? 'new' : $this->model->get_group_by_id($id);
				
				if(is_array($group)){
					
				$qroups = $this->model->get_groups();
					
					$this->registry->set('title', 'Панель администрирования | группа "'.apply_filters('name-ru', $product['prod_name']).'"');
					$this->view->template = 'admin_view.php';
					$this->view->generate(
											ADMINDIR.'/application/views/productsgroup_view.php', 
											ADMINDIR.'/application/views/'.$this->view->template, 
											array(	
													'group'=>$group, 
													'qroups'=>$qroups, 
												)
										);
				}
				elseif($this->GET['id'] == 'new'){
					$this->registry->set('title', 'Панель администрирования | Создать т/у');
					$qroups = $this->model->get_groups();
					$this->view->template = 'admin_view.php';
					$this->view->generate(
											ADMINDIR.'/application/views/productsgroup_view.php', 
											ADMINDIR.'/application/views/'.$this->view->template, 
											array(	
													'group'=>$group, 
													'qroups'=>$qroups, 
												)
										);
				}				
				else { Route::ErrorPage404($this->registry);}

			} 
			else {
				$this->registry->set('title', 'Панель администрирования | Группы товаров');
				$this->view->template = 'admin_view.php';
				$groups = $this->model->get_groups(array(
														'limit' => 20,
														'page' => $_GET['page'],
														'search' => $_GET['search'],
														));
				$this->view->generate(ADMINDIR.'/application/views/productsgroups_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('groups'=>$groups));
			}
		}
		else {
				$this->generateLoginForm();
		}
	}
}
?>