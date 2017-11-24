<?php

class controller_users extends Controller
{

	function action_index($array = array())
	{
		if( $this->getPermissions('users') ){
			if(isset($this->GET['id'])) {
				$user_id = ($this->GET['id'] == 'new')? 'new' : (int)$this->GET['id'];
				$user = ($user_id == 'new')? 'new' : $this->model->get_user_by_id($user_id);
				
				if(is_array($user)){
					$contacts = $this->model->get_contacts($user['user_id']);
					$permissions = $this->model->getUserPermissions($user['user_id']);
					$this->registry->set('title', 'Панель администрирования | Пользователь "'.apply_filters('name-ru', $user['user_name']).'"');
					$this->view->template = 'admin_view.php';
					$this->view->generate(
											ADMINDIR.'/application/views/user_view.php', 
											ADMINDIR.'/application/views/'.$this->view->template, 
											array(	
													'user'=>$user, 
													'contacts'=>$contacts,
													'permissions'=>$permissions,
												)
										);
				}
				elseif($this->GET['id'] == 'new'){
					$this->registry->set('title', 'Панель администрирования | Создать нового пользователя');
					$permissions = $this->model->getUserPermissions(0);
					$this->view->template = 'admin_view.php';
					$this->view->generate(
											ADMINDIR.'/application/views/user_view.php', 
											ADMINDIR.'/application/views/'.$this->view->template, 
											array(	
													'user'=>$user, 
													'contacts'=>$contacts,
													'permissions'=>$permissions,
												)
										);
				}				
				else { Route::ErrorPage404($this->registry);}

			} 
			else {
				$this->registry->set('title', 'Панель администрирования | Пользователи');
				$this->view->template = 'admin_view.php';
				$users = $this->model->get_users(array(
														'limit' => 20,
														'page' => $_GET['page'],
														'search' => $_GET['search'],
														));
				$this->view->generate(ADMINDIR.'/application/views/users_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('users'=>$users));
			}
		}
		else {
				$this->generateLoginForm();
		}
	}
}
?>