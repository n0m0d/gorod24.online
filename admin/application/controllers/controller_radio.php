<?php

class controller_radio extends Controller
{
	function __construct(){
		$this->view = new View('index.tpl');
		$this->view->setTemplatesFolder(ADMINDIR.'/application/views/');
		$this->view->headers['title'] = 'Радио | Администрирование Полезного радио';
		$this->view->data['main-menu']['Радио'] = true;
		$this->view->data['main']['header'] = 'Радио';
		$this->view->data['main']['menu'] = [
			[ "title" => "Проекты", "url"=>"#", "items" => [
					["title" => "Все проекты", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/radio/projects/"],
				] 
			],
			
		];
	}
	
	function action_index($actions=null){
		$this->view->data['breadcrumbs'] = [ "Радио"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/radio/'];
		$this->view->data['header'] = "Радио";
	}
	
	function action_projects($actions=null){
		$this->view->headers['title'] = 'Проекты | Администрирование Полезного радио';
		$this->view->data['breadcrumbs'] = [ "Радио"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/radio/', "Проекты"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/radio/projects/'];
		$this->view->data['header'] = "Проекты";
		if(!$actions){
			
			$model_projects = new model_projects();
			$admin = new AdminList(
				array(
					"model" => $model_projects,
					"order" => "id DESC",
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/radio/projects/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Название", "name"=>"name", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/radio/projects/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Описание", "name"=>"description", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Состояние", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
					],
				)
			);
			/**/
			$content = $admin;
		}
		else {
			if(is_numeric($actions[0])){
				$content = $this->action_projects_edit(((int)$actions[0]));
			}
		}
		$this->view->data['content'] = $content;
	}
	
	public function action_projects_edit($id){
		$content = '';
		$model_projects = new model_projects();
		$project = $model_projects->getItem($id);
		$this->view->headers['title'] = 'Проект '.$project['name'].' | Администрирование Полезного радио';
		$this->view->data['header'] = 'Проект '.$project['name'];
		$this->view->data['breadcrumbs'][$project['name']] = '';
		
		if(isset($_POST['save'])){$this->view->notRender();
			/**/
			$data = [
				"id"=>$this->varChek($_POST['id']),
				"name"=>$this->varChek($_POST['name']),
				"description"=>$this->varChek($_POST['description']),
				"audio_stream"=>$this->varChek($_POST['audio_stream']),
				"video_stream"=>$this->varChek($_POST['video_stream']),
				"phone"=>$this->varChek($_POST['phone']),
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
			];
			$model_projects->InsertUpdate($data);
			header('Location: /admin/radio/projects/');
		}
		
		$admin = new AdminPage(
			array(
				"model" => $model_projects,
				"item" => $project,
				"action" => '/admin/radio/projects/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Название", "name"=>"name", "attrs"=>[], "type"=>"text"],
					["title"=>"Описание", "name"=>"description", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Аудио поток", "name"=>"audio_stream", "attrs"=>[], "type"=>"text"],
					["title"=>"Видео поток", "name"=>"video_stream", "attrs"=>[], "type"=>"text"],
					["title"=>"Телефон", "name"=>"phone", "attrs"=>[], "type"=>"text"],
					["title"=>"Сосотояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		return $content;
	}
	
	
	
}