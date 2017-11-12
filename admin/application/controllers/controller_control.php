<?php

class controller_control extends Controller
{
	function __construct(){
		$this->view = new View('control.tpl');
		$this->view->setTemplatesFolder(ADMINDIR.'/application/views/');
		$this->view->headers['title'] = 'Управление | Администрирование Полезного радио';
		$this->view->data['main-menu']['Управление'] = true;
	}
	
	function action_index($actions=null){
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/'];
		$this->view->data['header'] = "Управление";
	}
	
	function action_users($actions=null){
		$this->view->headers['title'] = 'Пользователи | Администрирование Полезного радио';
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/', "Пользователи"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/'];
		$this->view->data['header'] = "Пользователи";
		if(!$actions){
			$model_users = new model_users();
			$admin = new AdminList(
				array(
					"model" => $model_users,
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"user_id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/{$row["user_id"]}/\">{$cel}</a>";')],
						["title"=>"Логин", "name"=>"user_login", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/{$row["user_id"]}/\">{$cel}</a>";')],
						["title"=>"Имя", "name"=>"user_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Состояние", "name"=>"user_status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
					],
				)
			);
			$content = $admin;
		}
		else {
			if(is_numeric($actions[0])){
				$content = $this->action_users_edit(((int)$actions[0]));
			}
		}
		$this->view->data['content'] = $content;
	}
	
	public function action_users_edit($id){
		$content = '';
		$model_users = new model_users();
		$user = $model_users->getItem($id);
		$this->view->headers['title'] = 'Пользователь '.$user['user_name'].' | Администрирование Полезного радио';
		$this->view->data['header'] = 'Пользователь '.$user['user_name'];
		$this->view->data['breadcrumbs'][$user['user_name']] = '';
			
		$admin = new AdminPage(
			array(
				"model" => $model_users,
				"item" => $user,
				"action" => '/admin/control/users/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"user_id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Логин", "name"=>"user_login", "attrs"=>[], "type"=>"text"],
					["title"=>"Имя", "name"=>"user_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Сосотояние", "name"=>"user_status", "attrs"=>[], "type"=>"switch"],
				],
			)
		);
		$content .= $admin . AdminPage::mediumTextField(["title"=>"Пример", "name"=>"name", "attrs"=>[], "value"=>"test", "type"=>"switch"]);;
		
		return $content;
	}
	
	
	function action_pages($actions=null){
		$this->view->headers['title'] = 'Страницы | Администрирование Полезного радио';
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/', "Страницы"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/pages/'];
		$this->view->data['header'] = "Страницы";
		
		$model = new model_posts();
		$admin = new AdminList(
			array(
				"model" => $model,
				"where" => "`post_type`='page'",
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"Id", "name"=>"post_id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/pages/{$row["post_id"]}/\">{$cel}</a>";')],
					["title"=>"Имя", "name"=>"post_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/pages/{$row["post_id"]}/\">{$cel}</a>";')],
					["title"=>"Краткое описание", "name"=>"post_description", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Сосотояние", "name"=>"post_status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
				],
			)
		);

		$content = $admin;
		$this->view->data['content'] = $content;
		
	}
	
	
}