<?php

class controller_help extends controller
{
	function __construct(){
		$this->view = new View('index.tpl');
		$this->view->setTemplatesFolder(ADMINDIR.'/application/views/');
		$this->view->headers['title'] = 'Помощь | Администрирование Полезного радио';
		$this->view->data['main-menu']['Помощь'] = true;
		$this->view->data['main']['header'] = 'Помощь';
		$this->view->data['main']['menu'] = [
			[ "title" => "Рубрики - помощь", "url"=>"#", "items" => [
					["title" => "Все Рубрики", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/help/rubrics/"],
					["title" => "Добавить Рубрику", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/help/rubrics/add/"],
				]
			],
			[ "title" => "Страницы", "url"=>"#", "items" => [
					["title" => "Все страницы", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/help/pages/"],
					["title" => "Добавить страницу", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/help/pages/add/"],
				]
			],
		];
	}

	function action_index($actions=null){
		$this->view->data['breadcrumbs'] = [ "Помощь"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/'];
		$this->view->data['header'] = "Помощь";
	}
	
	/********** Рубрики **********/
	function action_rubrics($actions=null){
		$this->view->headers['title'] = 'Рубрики | Администрирование Полезного радио';
		$this->view->data['breadcrumbs'] = [ "Помощь"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/', "Рубрики"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/rubrics/'];
		$this->view->data['header'] = "Рубрики";
		
		$model_help_categories = new model_help_categories();
		
		// Удаление элементов
		if (isset($_POST['options']))
		{
			//parse_str($_POST['inputs'], $inputs);

			foreach ($_POST['options'] as $input)
			{
				$model_help_categories->Delete($input);
			}
		}

		if(!$actions){
			$result .= "<button type='button' class='btn btn-danger ajax-delete' style='margin-bottom: 20px;'><b>Удалить</b></button>";
			$admin = new AdminList(
				array(
					"model" => $model_help_categories,
					"order" => "id DESC",
					"multiple" => "true",
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/rubrics/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Название", "name"=>"title", "attrs"=>[ ], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/rubrics/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Сосотояние", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
		}
		else {
			if(is_numeric($actions[0])){
				$content = $this->action_rubrics_edit(((int)$actions[0]));
			}
		}
		$this->view->data['content'] = $content;
	}

	public function action_rubrics_edit($id){
		$content = '';
		$model_help_categories = new model_help_categories();
		$post = $model_help_categories->getItem($id);
		$this->view->headers['title'] = 'Рубрика - '.$post['title'].' | Администрирование Полезного радио';
		$this->view->data['header'] = 'Рубрика - '.$post['title'];
		$this->view->data['breadcrumbs'][$post['title']] = '';

		if(isset($_POST['save'])){$this->view->notRender();
			$data = [
				"id"=>$this->varChek($_POST['id']),
				"title"=>$this->varChek($_POST['title']),
				"author"=>$_SESSION['user_id'],
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
				"site"=>($this->varChek($_POST['site'])=='on'?1:0),
				"admin"=>($this->varChek($_POST['admin'])=='on'?1:0),
			];
			$model_help_categories->InsertUpdate($data);
			header('Location: /admin/help/rubrics/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_help_categories,
				"item" => $post,
				"action" => '/admin/help/rubrics/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Доступно для сайта", "name"=>"site", "attrs"=>[], "type"=>"switch"],
					["title"=>"Доступно для админки", "name"=>"admin", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		return $content;
	}

	public function action_rubrics_add($id){
		$content = '';
		$model_help_categories = new model_help_categories();
		$this->view->headers['title'] = 'Новая Рубрика | Администрирование Полезного радио';
		$this->view->data['breadcrumbs'] = [ "Помощь"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/', "Рубрики"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/rubrics/'];
		$this->view->data['breadcrumbs']['Новая Рубрика'] = '';

		if(isset($_POST['add'])){$this->view->notRender();
			$data = [
				"title"=>$this->varChek($_POST['title']),
				"author"=>$_SESSION['user_id'],
				"date_create"=>date("Y-m-d H:i:s"),
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
				"site"=>($this->varChek($_POST['site'])=='on'?1:0),
				"admin"=>($this->varChek($_POST['admin'])=='on'?1:0),
			];
			$model_help_categories->InsertUpdate($data);
			header('Location: /admin/help/rubrics/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_help_categories,
				"item" => null,
				"action" => '/admin/help/rubrics/add/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Доступно для сайта", "name"=>"site", "attrs"=>[], "type"=>"switch"],
					["title"=>"Доступно для админки", "name"=>"admin", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"add", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		$this->view->data['content'] = $content;
	}

	/********** Страницы **********/
	function action_pages($actions=null){
		$this->view->headers['title'] = 'Страницы | Администрирование Полезного радио';
		$this->view->data['breadcrumbs'] = [ "Помощь"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/', "Страницы"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/pages/'];
		$this->view->data['header'] = "Страницы";
		
		$model_help = new model_help();
		// Удаление элементов
		if (isset($_POST['options'])){
			foreach ($_POST['options'] as $input)
			{
				$model_help->Delete($input);
			}
		}

		if(!$actions){
			$result .= "<button type='button' class='btn btn-danger ajax-delete' style='margin-bottom: 20px;'><b>Удалить</b></button>";
			$admin = new AdminList(
				array(
					"model" => $model_help,
					"order" => "id DESC",
					"multiple" => "true",
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/pages/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Название", "name"=>"title", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/pages/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Состояние", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
		}
		else {
			if(is_numeric($actions[0])){
				$content = $this->action_pages_edit(((int)$actions[0]));
			}
		}
		$this->view->data['content'] = $content;
	}

	public function action_pages_edit($id){
		$content = '';
		$model_help = new model_help();
		$model_help_categories = new model_help_categories();
		$item = $model_help->getItem($id);
		$this->view->headers['title'] = 'Страница '.$item['title'].' | Администрирование Полезного радио';
		$this->view->data['header'] = 'Страница '.$item['title'];
		$this->view->data['breadcrumbs'][$item['title']] = '';

		if(isset($_POST['save'])){$this->view->notRender();
			$data = [
				"id"=>$this->varChek($_POST['id']),
				"cat_id"=>$this->varChek($_POST['cat_id']),
				"title"=>$this->varChek($_POST['title']),
				"text"=>$_POST['text'],
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
				"site"=>($this->varChek($_POST['site'])=='on'?1:0),
				"admin"=>($this->varChek($_POST['admin'])=='on'?1:0),
			];
			$model_help->InsertUpdate($data);
			header('Location: /admin/help/pages/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_help,
				"item" => $item,
				"action" => '/admin/help/pages/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"Рубрика", "name"=>"cat_id", "attrs"=>[], "type"=>"select", "items"=>$model_help_categories->getItemsWhere("status = 1", "title", null, null, "id AS value, title AS label")],
					["title"=>"Текст", "name"=>"text", "attrs"=>[], "type"=>"editor"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Доступно для сайта", "name"=>"site", "attrs"=>[], "type"=>"switch"],
					["title"=>"Доступно для админки", "name"=>"admin", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		return $content;
	}

	function action_pages_add($actions=null){
		$content = '';
		$model_help = new model_help();
		$model_help_categories = new model_help_categories();
		$this->view->headers['title'] = 'Новая Страница | Администрирование Полезного радио';
		$this->view->data['breadcrumbs'] = [ "Помощь"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/', "pages - задачи"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/help/pages/'];
		$this->view->data['breadcrumbs']['Новая Страница'] = '';

		if(isset($_POST['add'])){$this->view->notRender();
			$data = [
				"cat_id"=>$this->varChek($_POST['cat_id']),
				"title"=>$this->varChek($_POST['title']),
				"text"=>$_POST['text'],
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
				"date_create"=>date("Y-m-d H:i:s"),
				"author"=>$_SESSION['user_id'],
				"site"=>($this->varChek($_POST['site'])=='on'?1:0),
				"admin"=>($this->varChek($_POST['admin'])=='on'?1:0),
			];
			$model_help->Insert($data);
			header('Location: /admin/help/pages/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_help,
				"item" => null,
				"action" => '/admin/help/pages/add/',
				"fields" => [
					["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"Рубрика", "name"=>"cat_id", "attrs"=>[], "type"=>"select", "items"=>$model_help_categories->getItemsWhere("status = 1", "title", null, null, "id AS value, title AS label")],
					["title"=>"Текст", "name"=>"text", "attrs"=>[], "type"=>"editor"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Доступно для сайта", "name"=>"site", "attrs"=>[], "type"=>"switch"],
					["title"=>"Доступно для админки", "name"=>"admin", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"add", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		$this->view->data['content'] = $content;
	}

}
