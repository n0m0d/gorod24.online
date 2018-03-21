<?php
require_once( __DIR__ ."/controller_index.php");
class controller_info extends controller_index
{

	function action_index($actions=null){
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Информация"=>$this->url.'/info/'];
		$this->view->data['header'] = "Информация";
	}
	
	/********** Рубрики **********/
	function action_rubrics($actions=null){
		$this->view->headers['title'] = 'Рубрики | Администрирование Полезного радио';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Информация"=>$this->url.'/info/', "Рубрики"=>$this->url.'/info/rubrics/'];
		$this->view->data['header'] = "Рубрики";
		
		$model_info_categories = new model_info_categories();
		
		// Удаление элементов
		if (isset($_POST['del']))
		{
			foreach ($_POST['options'] as $input)
			{
				$model_info_categories->Delete($input);
			}
		}
		
		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model_info_categories->Update(['status'=>1], $input);
			}
		}

		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model_info_categories->Update(['status'=>0], $input);
			}
		}

		if(!$actions){
			$admin = new AdminList(
				array(
					"action" => $this->url."/info/rubrics/",
					"model" => $model_info_categories,
					"order" => "id DESC",
					"multiple" => "true",
					"attrs" => ["class"=>"table-adapt"],
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить", "href"=> $this->url."/info/rubrics/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"]
					],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/info/rubrics/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Название", "name"=>"title", "attrs"=>[ ], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/info/rubrics/{$row["id"]}/\">{$cel}</a>";')],
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
		$model_info_categories = new model_info_categories();
		$post = $model_info_categories->getItem($id);
		$this->view->headers['title'] = 'Рубрика - '.$post['title'].' | Администрирование Полезного радио';
		$this->view->data['header'] = 'Рубрика - '.$post['title'];
		$this->view->data['breadcrumbs'][$post['title']] = '';

		if(isset($_POST['save'])){$this->view->notRender();
			$data = [
				"id"=>$this->varChek($_POST['id']),
				"title"=>$this->varChek($_POST['title']),
				"author"=>$_SESSION['user_id'],
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
			];
			$model_info_categories->InsertUpdate($data);
			header('Location: '.$this->url.'/info/rubrics/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_info_categories,
				"item" => $post,
				"action" => $this->url.'/info/rubrics/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		return $content;
	}

	public function action_rubrics_add($id){
		$content = '';
		$model_info_categories = new model_info_categories();
		$this->view->headers['title'] = 'Новая Рубрика | Администрирование Полезного радио';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Информация"=>$this->url.'/info/', "Рубрики"=>$this->url.'/info/rubrics/', 'Новая Рубрика'=>$this->url.'/info/rubrics/add'];
		$this->view->data['breadcrumbs']['Новая Рубрика'] = '';

		if(isset($_POST['add'])){$this->view->notRender();
			$data = [
				"title"=>$this->varChek($_POST['title']),
				"author"=>$_SESSION['user_id'],
				"date_create"=>date("Y-m-d H:i:s"),
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
			];
			$model_info_categories->InsertUpdate($data);
			header('Location: '.$this->url.'/info/rubrics/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_info_categories,
				"item" => null,
				"action" => $this->url.'/info/rubrics/add/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
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
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Информация"=>$this->url.'/info/', "Страницы"=>$this->url.'/info/pages/'];
		$this->view->data['header'] = "Страницы";
		
		$model_info = new model_info();
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_info->Delete($input);
			}
		}
		
		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model_info->Update(['status'=>1], $input);
			}
		}

		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model_info->Update(['status'=>0], $input);
			}
		}

		if(!$actions){
			$admin = new AdminList(
				array(
					"action" => $this->url."/info/pages/",
					"model" => $model_info,
					"order" => "id DESC",
					"multiple" => "true",
					"attrs" => ["class"=>"table-adapt"],
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить", "href"=> $this->url."/info/pages/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"]
					],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/info/pages/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Название", "name"=>"title", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/info/pages/{$row["id"]}/\">{$cel}</a>";')],
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
		$model_info = new model_info();
		$model_info_categories = new model_info_categories();
		$item = $model_info->getItem($id);
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
			];
			$model_info->InsertUpdate($data);
			header('Location: '.$this->url.'/info/pages/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_info,
				"item" => $item,
				"action" => $this->url.'/info/pages/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"Рубрика", "name"=>"cat_id", "attrs"=>[], "type"=>"select", "items"=>$model_info_categories->getItemsWhere("status = 1", "title", null, null, "id AS value, title AS label")],
					["title"=>"Текст", "name"=>"text", "attrs"=>[], "type"=>"editor"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		return $content;
	}

	function action_pages_add($actions=null){
		$content = '';
		$model_info = new model_info();
		$model_info_categories = new model_info_categories();
		$this->view->headers['title'] = 'Новая Страница | Администрирование Полезного радио';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Информация"=>$this->url.'/info/', "Страницы"=>$this->url.'/info/pages/', 'Новая страница'=>$this->url.'/info/pages/add'];
		$this->view->data['breadcrumbs']['Новая Страница'] = '';

		if(isset($_POST['add'])){$this->view->notRender();
			$data = [
				"cat_id"=>$this->varChek($_POST['cat_id']),
				"title"=>$this->varChek($_POST['title']),
				"text"=>$_POST['text'],
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
				"date_create"=>date("Y-m-d H:i:s"),
				"author"=>$_SESSION['user_id'],
			];
			$model_info->Insert($data);
			header('Location: '.$this->url.'/info/pages/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_info,
				"item" => null,
				"action" => $this->url.'/info/pages/add/',
				"fields" => [
					["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"Рубрика", "name"=>"cat_id", "attrs"=>[], "type"=>"select", "items"=>$model_info_categories->getItemsWhere("status = 1", "title", null, null, "id AS value, title AS label")],
					["title"=>"Текст", "name"=>"text", "attrs"=>[], "type"=>"editor"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"add", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		$this->view->data['content'] = $content;
	}

}
