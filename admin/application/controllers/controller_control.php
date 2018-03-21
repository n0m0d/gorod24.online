<?php

class controller_control extends Controller
{
	function __construct(){
		$this->view = new View('index.tpl');
		$this->view->setTemplatesFolder(ADMINDIR.'/application/views/');
		$this->view->headers['title'] = 'Управление | Администрирование Город 24';
		$this->view->data['main-menu']['Управление'] = true;
		$this->view->data['main']['header'] = 'Управление';
		$this->view->data['main']['menu'] = [
			[ "title" => "Пользователи", "url"=>"#", "items" => [
					["title" => "Все пользователи", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/users/"],
					["title" => "Добавить пользователя", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/users/add/"],
				]
			],
			[ "title" => "Страницы", "url"=>"#", "items" => [
					["title" => "Все страницы", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/pages/"],
					["title" => "Добавить страницу", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/pages/add/"],
				]
			],
			[ "title" => "Файлы", "url"=>"#", "items" => [
					["title" => "Все файлы", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/files/"],
					["title" => "Добавить файлы", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/files/add/"],
				]
			],
			[ "title" => "Cron - задачи", "url"=>"#", "items" => [
					["title" => "Все задачи", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/cron/"],
					["title" => "Добавить задачу", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/cron/add/"],
				]
			],
		];
	}

	function action_index($actions=null){
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/'];
		$this->view->data['header'] = "Управление";
	}

	/********** ПОЛЬЗОВАТЕЛИ **********/
	function action_users($actions=null){
		$this->view->headers['title'] = 'Пользователи | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/', "Пользователи"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/'];
		$this->view->data['header'] = "Пользователи";
		
		$model_users = new model_users();
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_users->Delete($input);
			}
		}
		
		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model_users->Update([ 'user_status'=>1 ], $input);
			}
		}
		
		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model_users->Update([ 'user_status'=>0 ], $input);
			}
		}
		
		if(!$actions){
			$admin = new AdminList(
				array(
					"action" => '/admin/control/users/',
					"model" => $model_users,
					"order" => "user_id DESC",
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить", "href"=>"/admin/control/users/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"]
					],
					"multiple" => "true",
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"user_id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/{$row["user_id"]}/\">{$cel}</a>";')],
						["title"=>"Логин", "name"=>"user_login", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/{$row["user_id"]}/\">{$cel}</a>";')],
						["title"=>"Имя", "name"=>"user_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Состояние", "name"=>"user_status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
						["title"=>"Права", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/access/{$row["user_id"]}/\">{$cel}<em class=\"icon-1\" title=\"Права\"></em></a>";')],
					],
				)
			);
			
			$result .= $admin;
			$content = $result;
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
		$model_permissions = new model_permissions();
		$user = $model_users->getItem($id);
		$this->view->headers['title'] = 'Пользователь '.$user['user_name'].' | Администрирование Город 24';
		$this->view->data['header'] = 'Пользователь '.$user['user_name'];
		$this->view->data['breadcrumbs'][$user['user_name']] = '';

		if(isset($_POST['save'])){$this->view->notRender();
			$data = [
				"user_id"=>$this->varChek($_POST['user_id']),
				"user_login"=>$this->varChek($_POST['user_login']),
				"user_name"=>$this->varChek($_POST['user_name']),
				"user_status"=>($this->varChek($_POST['user_status'])=='on'?1:0),
			];
			$pass1 = $this->varChek($_POST['password-1']);
			$pass2 = $this->varChek($_POST['password-2']);
			if(!empty($pass1) AND !empty($pass2) AND $pass1==$pass2){
				$data['user_password'] = md5($pass1);
			}
			$model_users->InsertUpdate($data);
			header('Location: /admin/control/users/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_users,
				"item" => $user,
				"action" => '/admin/control/users/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"user_id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Логин", "name"=>"user_login", "attrs"=>[], "type"=>"text"],
					["title"=>"Имя", "name"=>"user_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Состояние", "name"=>"user_status", "attrs"=>[], "type"=>"switch"],
					["content"=>"Если вы не хотите изменять пароль оставьте эти поля пустыми", "attrs"=>[], "type"=>"line"],
					["title"=>"Пароль", "name"=>"password-1", "attrs"=>[], "type"=>"password"],
					["title"=>"Пароль еще раз", "name"=>"password-2", "attrs"=>[], "type"=>"password"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		return $content;
	}

	function action_users_add($actions=null){
		$content = '';
		$model_users = new model_users();
		//$model_permissions = new model_permissions();
		$this->view->headers['title'] = 'Новый пользователь | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/', "Пользователи"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/'];
		$this->view->data['breadcrumbs']['Новый пользователь'] = '';

		if(isset($_POST['add'])){$this->view->notRender();
			$data = [
				"user_registered"=>$this->varChek($_POST['user_registered']),
				"user_login"=>$this->varChek($_POST['user_login']),
				"user_name"=>$this->varChek($_POST['user_name']),
				"user_status"=>($this->varChek($_POST['user_status'])=='on'?1:0),
			];
			$pass1 = $this->varChek($_POST['password-1']);
			$pass2 = $this->varChek($_POST['password-2']);
			if(!empty($pass1) AND !empty($pass2) AND $pass1==$pass2){
				$data['user_password'] = md5($pass1);
			} else {
				die("<p style='color: red;'>Пароли не совпадают</p><br><a href='/admin/control/users/'>Назад</a>");
			}
			$model_users->InsertUpdate($data);
			header('Location: /admin/control/users/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_users,
				"item" => null,
				"action" => '/admin/control/users/add/',
				"fields" => [
					["title"=>"Дата регистрации", "name"=>"user_registered", "attrs"=>[], "type"=>"hidden", "value"=>date("Y-m-d H:i:s")],
					["title"=>"Логин", "name"=>"user_login", "attrs"=>[], "type"=>"text"],
					["title"=>"Имя", "name"=>"user_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Пароль", "name"=>"password-1", "attrs"=>[], "type"=>"password"],
					["title"=>"Пароль еще раз", "name"=>"password-2", "attrs"=>[], "type"=>"password"],
					["title"=>"Состояние", "name"=>"user_status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Добавить", "name"=>"add", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		$this->view->data['content'] = $content;
	}

	/********** Права пользователей **********/
	public function action_users_access($actions){
		$content = '';
		$id = $actions[0];
				
		$model_users = new model_users();
		$model_access = new model_access();
		$model_permissions = new model_permissions();

		$user = $model_users->getItem($id);
		$this->view->headers['title'] = 'Права '.$user['user_name'].' | Администрирование Город 24';
		$this->view->data['header'] = $user['user_name'];
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/', "Пользователи"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/', $user['user_name'] => $GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/'.$user['user_id'].'/', "Права" => ''];
		
		$permissions = $model_permissions->getItemsWhere();
		
		$fields = [];
		foreach($permissions as $i=>$item){
			$ch = $model_access->getPermission($item['perm_name'], $id);
			$fields[] = ["title"=>$item['perm_comment'], "name"=>"perm[{$item['perm_id']}]", "attrs"=>[], "value"=>($ch?1:0), "type"=>"check"];
		}
		
		if(isset($_POST['save'])){$this->view->notRender();
			$model_access->Delete("`user_id`='{$id}'");
			foreach($_POST['perm'] as $ac_res=>$val){
				$model_access->Insert([
					"user_id" => $id,
					"ac_res" => $ac_res,
					"ac_val" => 1
				]);
			}
			header('Location: /admin/control/users/');
		}
		
		$fields = array_merge($fields, [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				]);
		$admin = new AdminPage(
			array(
				"action" => '/admin/control/users/access/'.$id.'/',
				"fields" => $fields,
			)
		);
		
		$content .= $admin;
		$this->view->data['content'] = $content;
	}
	
	/********** СТРАНИЦЫ **********/
	function action_pages($actions=null){
		$this->view->headers['title'] = 'Страницы | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/', "Страницы"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/pages/'];
		$this->view->data['header'] = "Страницы";
		$model_posts = new model_posts();
		
		// Удаление элементов
		if (isset($_POST['del']))
		{
			foreach ($_POST['options'] as $input)
			{
				$model_posts->Delete($input);
			}
		}

		if(!$actions){

			$result .= "<button type='button' class='btn btn-danger ajax-delete' style='margin-bottom: 20px;'><b>Удалить</b></button>";
			$admin = new AdminList(
				array(
					"action" => '/admin/control/pages/',
					"model" => $model_posts,
					"where" => "`post_type`='page'",
					"order" => "post_id DESC",
					"multiple" => "true",
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"post_id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/pages/{$row["post_id"]}/\">{$cel}</a>";')],
						["title"=>"Имя", "name"=>"post_name", "attrs"=>[ ], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/pages/{$row["post_id"]}/\">{$cel}</a>";')],
						["title"=>"Краткое описание", "name"=>"post_description", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Сосотояние", "name"=>"post_status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
					],
				)
			);
			
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
		$model_posts = new model_posts();
		$post = $model_posts->getItem($id);
		$this->view->headers['title'] = 'Страница - '.$post['post_name'].' | Администрирование Город 24';
		$this->view->data['header'] = 'Страница - '.$post['post_name'];
		$this->view->data['breadcrumbs'][$post['post_name']] = '';

		if(isset($_POST['save'])){$this->view->notRender();
			
			$data = [
				"post_id"=>$this->varChek($_POST['post_id']),
				"post_url"=>$this->varChek($_POST['post_url']),
				"post_name"=>$this->varChek($_POST['post_name']),
				"post_description"=>$_POST['post_description'],
				"post_content"=>$_POST['post_content'],
				"post_meta"=>json_encode([ "title"=>$_POST['title'], "description"=>$_POST['description'], "keywords"=>$_POST['keywords'], "image"=>$_POST['image'],], JSON_UNESCAPED_UNICODE),
				"post_author"=>$this->varChek($_POST['post_author']),
				"post_modified"=>$this->varChek($_POST['post_date']),
				"post_type"=>$this->varChek($_POST['post_type']),
				"post_status"=>($this->varChek($_POST['post_status'])=='on'?1:0),
			];
			$model_posts->InsertUpdate($data);
			header('Location: /admin/control/pages/');
		}
		
		$meta = json_decode($post['post_meta'], true);
		$post['title'] = $meta['title'];
		$post['description'] = $meta['description'];
		$post['keywords'] = $meta['keywords'];
		$post['image'] = $meta['image'];
		
		
		$admin = new AdminPage(
			array(
				"model" => $model_posts,
				"item" => $post,
				"action" => '/admin/control/pages/'.$id.'/',
				"fields" => [
					["title"=>"Дата изменения", "name"=>"post_modified", "attrs"=>[], "type"=>"hidden", "value"=>date("Y-m-d H:i:s")],
					["title"=>"Автор", "name"=>"post_author", "attrs"=>[], "type"=>"hidden", "value"=>$_SESSION['user_id']],
					["title"=>"Id", "name"=>"post_id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"URL", "name"=>"post_url", "attrs"=>[], "type"=>"text"],
					["title"=>"Название", "name"=>"post_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Описание", "name"=>"post_description", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Контент", "name"=>"post_content", "attrs"=>[], "type"=>"editor"],
					["title"=>"[SEO] Заголовок (title)", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"[SEO] Описание (description)", "name"=>"description", "attrs"=>[], "type"=>"text"],
					["title"=>"[SEO] Ключевые слова (keywords)", "name"=>"keywords", "attrs"=>[], "type"=>"text"],
					["title"=>"[SEO] Картинка (image)", "name"=>"image", "attrs"=>[], "type"=>"fileExplorer", "accept"=>"image", "src"=>true],
					["title"=>"Тип", "name"=>"post_type", "attrs"=>[], "type"=>"select", "items"=>[ ['value'=>'page','label'=>'Страница'], ['value'=>'post','label'=>'Статья']]],
					["title"=>"Состояние", "name"=>"post_status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		return $content;
	}

	public function action_pages_add($id){
		$content = '';
		$model_posts = new model_posts();
		$this->view->headers['title'] = 'Новая страница | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/', "Страницы"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/pages/'];
		$this->view->data['breadcrumbs']['Новая страница'] = '';

		if(isset($_POST['add'])){$this->view->notRender();
			$data = [
				"post_url"=>$this->varChek($_POST['post_url']),
				"post_name"=>$this->varChek($_POST['post_name']),
				"post_description"=>$_POST['post_description'],
				"post_content"=>$_POST['post_content'],
				"post_meta"=>json_encode([ "title"=>$_POST['title'], "description"=>$_POST['description'], "keywords"=>$_POST['keywords'], "image"=>$_POST['image'],], JSON_UNESCAPED_UNICODE),
				"post_author"=>$this->varChek($_POST['post_author']),
				"post_date"=>$this->varChek($_POST['post_date']),
				"post_type"=>$this->varChek($_POST['post_type']),
				"post_status"=>($this->varChek($_POST['post_status'])=='on'?1:0),
			];
			$model_posts->InsertUpdate($data);
			header('Location: /admin/control/pages/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_posts,
				"item" => null,
				"action" => '/admin/control/pages/add/',
				"fields" => [
					["title"=>"Дата добавления", "name"=>"post_date", "attrs"=>[], "type"=>"hidden", "value"=>date("Y-m-d H:i:s")],
					["title"=>"Автор", "name"=>"post_author", "attrs"=>[], "type"=>"hidden", "value"=>$_SESSION['user_id']],
					["title"=>"URL", "name"=>"post_url", "attrs"=>[], "type"=>"text"],
					["title"=>"Название", "name"=>"post_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Описание", "name"=>"post_description", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Контент", "name"=>"post_content", "attrs"=>[], "type"=>"editor"],
					["title"=>"[SEO] Заголовок (title)", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"[SEO] Описание (description)", "name"=>"description", "attrs"=>[], "type"=>"text"],
					["title"=>"[SEO] Ключевые слова (keywords)", "name"=>"keywords", "attrs"=>[], "type"=>"text"],
					["title"=>"[SEO] Картинка (image)", "name"=>"image", "attrs"=>[], "type"=>"fileExplorer", "accept"=>"image", "src"=>true],
					["title"=>"Тип", "name"=>"post_type", "attrs"=>[], "type"=>"select", "items"=>[ ['value'=>'page','label'=>'Страница'], ['value'=>'post','label'=>'Статья']]],
					["title"=>"Состояние", "name"=>"post_status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Добавить", "name"=>"add", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		$this->view->data['content'] = $content;
	}

	/********** ФАЙЛЫ **********/
	function action_files($actions=null){
		$this->view->headers['title'] = 'Файлы | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/', "Файлы"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/files/'];
		$this->view->data['header'] = "Файлы";

		// Удаление элементов
		if (isset($_POST['del']))
		{
			$model_uploads = new model_uploads();

			foreach ($_POST['options'] as $input)
			{
				$item = $model_uploads->getItem($input);
				$link = APPDIR . $item['destination'] . $item['name'];
				unlink($link);
				$model_uploads->Delete($input);
			}
		}

		if(!$actions){
			$model = new model_uploads();
			$result .= "<button type='button' class='btn btn-danger ajax-delete' style='margin-bottom: 20px;'><b>Удалить</b></button>";
			$admin = new AdminList(
				array(
					"action" => '/admin/control/files/',
					"model" => $model,
					"order" => "date DESC",
					"multiple" => "true",
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/files/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Имя", "name"=>"name", "attrs"=>[ ], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/files/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Расоложение", "name"=>"destination", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo "<a href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'{$cel}{$row["name"]}\" target=\"_blank\">'.$GLOBALS['CONFIG']['HTTP_HOST'].'{$cel}{$row["name"]}</a>";')],
						["title"=>"Превью", "name"=>"prev", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
								switch ($row["type"]){
									case "image": echo "<img src=\"/uploads/image/cash/".$row["id"]."_100_0.".$row["ext"]."\" />"; break;
								}
							')],
						["title"=>"Сосотояние", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
					],
				)
			);

			$result .= $admin;
			$content = $result;
		}
		else {
			if(is_numeric($actions[0])){
				$content = $this->action_files_edit(((int)$actions[0]));
			}
			elseif($actions[0]=='add'){
				$content = $this->action_filesadd();
			}
		}
		$this->view->data['content'] = $content;
	}

	public function action_files_upload(){
		$this->view->notRender();
		if (empty($_FILES) || $_FILES['file']['error']) {
		  die('{"OK": 0, "info": "Failed to move uploaded file."}');
		}
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];
		$orig_name = $_REQUEST["name"];

		if(!is_dir('uploads')) mkdir('uploads');
		$filePath = APPDIR . "/uploads/$fileName";

		$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
		if ($out) {
			$in = @fopen($_FILES['file']['tmp_name'], "rb");
			if ($in) {
				while ($buff = fread($in, 4096)) fwrite($out, $buff);
			} else die('{"OK": 0, "info": "Failed to open input stream."}');
		@fclose($in);
		@fclose($out);
		@unlink($_FILES['file']['tmp_name']);
		} else die('{"OK": 0, "info": "Failed to open output stream."}');

		if (!$chunks || $chunk == $chunks - 1) {
			rename("{$filePath}.part", $filePath);
			$ext = getExtension5($orig_name);
			
			$type = mime_content_type($filePath);
			
			switch($ext){
				case 'jpeg':
				case 'jpg':
				case 'png':
				case 'gif':
				case 'tif':
				case 'tiff': 
				{
					$d = ['image', $ext];
					break;
				}
				case 'aac': 
				case 'ac3': 
				case 'aif': 
				case 'amr': 
				case 'm4a': 
				case 'm4b': 
				case 'm4p': 
				case 'm4r': 
				case 'mp3': 
				case 'ogg': 
				case 'wav': 
				case 'wave': 
				case 'wm': 
				{
					$d = ['audio', $ext];
					break;
				}
				default:
				{
					$d = explode('/', $type);
					break;
				}
			}
			
			$http_url = '/uploads/'.$d[0].'/';
			$dir = APPDIR . $http_url;
			if(!is_dir( $dir )){ mkdir($dir); chmod($dir, 0777);}
			$fileName = uniqid($d[1].'_').'.'.$ext;
			$data = [
				"name" => $fileName,
				"original_name" => $orig_name,
				"ext" => $ext,
				"type" => $d[0],
				"size" => filesize($filePath),
				"destination" => $http_url,
				"author" => $_SESSION['user_id'],
				"date" => date('Y-m-d H:i:s'),
				"modified" => date('Y-m-d H:i:s'),
				"status" => 1,
				"other" => '',
			];
			$model_uploads = new model_uploads();
			$id = $model_uploads->InsertUpdate($data);
			$url = 'http://'.$_SERVER['HTTP_HOST'].$http_url.$fileName;

			rename($filePath, $dir.$fileName); $filePath = $dir.$fileName;

			$result = [
				"OK" => 1,
				"info" => "Upload successful.",
				"type" => $data['type'],
				"id" => $id,
				"name" =>$data['name'],
				"original_name" =>$data['original_name'],
				"ext" => $ext,
				"size" => $data['size'],
				"destination" => $data['destination'],
				"url" => $data['destination'].$data['name'],
			];
			
			if($data['type'] == 'image'){
				list($width, $height, $type) = getimagesize(APPDIR . $data['destination'].$data['name']);
				$result['image'] = [
					'width' => $width,
					'height' => $height,
				];
			}
			
			die(json_encode($result));

		}
		die('{"OK": 1, "info": "Upload successful."}');

	}

	public function action_filesadd(){
		$content = '';
		$model_uploads = new model_uploads();
		$this->view->headers['title'] = 'Добавление файлов | Администрирование Город 24';
		$this->view->data['header'] = 'Добавление файлов ';
		$this->view->data['breadcrumbs']['Добавление файлов'] = '';

		$content .= AdminPage::filesUploaderField([ "title"=>"Загрузка", "url"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/files/upload/' ]);
		return $content;
	}

	/********** CRON задачи **********/
	function action_cron($actions=null){
		$this->view->headers['title'] = 'Cron | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/', "Cron - задачи"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/cron/'];
		$this->view->data['header'] = "Cron - задачи";
		
		$model_cron_tasks = new model_cron_tasks();
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_cron_tasks->Delete($input);
			}
		}

		if(!$actions){
			$result .= "<button type='button' class='btn btn-danger ajax-delete' style='margin-bottom: 20px;'><b>Удалить</b></button>";
			$admin = new AdminList(
				array(
					"model" => $model_cron_tasks,
					"order" => "task_id DESC",
					"multiple" => "true",
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"task_id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/cron/{$row["task_id"]}/\">{$cel}</a>";')],
						["title"=>"Название", "name"=>"task_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/cron/{$row["task_id"]}/\">{$cel}</a>";')],
						["title"=>"Последний запуск", "name"=>"task_last_launch", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Следующий запуск", "name"=>"task_next_launch", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Выполнено раз", "name"=>"task_launches", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Состояние", "name"=>"task_status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
					],
				)
			);
			
			$result .= $admin;
			$content = $result;
		}
		else {
			if(is_numeric($actions[0])){
				$content = $this->action_cron_edit(((int)$actions[0]));
			}
		}
		$this->view->data['content'] = $content;
	}

	public function action_cron_edit($id){
		$content = '';
		$model_cron_tasks = new model_cron_tasks();
		$item = $model_cron_tasks->getItem($id);
		$this->view->headers['title'] = 'Задача '.$item['task_name'].' | Администрирование Город 24';
		$this->view->data['header'] = 'Задача '.$item['task_name'];
		$this->view->data['breadcrumbs'][$item['task_name']] = '';

		if(isset($_POST['save'])){$this->view->notRender();
			$data = [
				"task_id"=>$this->varChek($_POST['task_id']),
				"task_name"=>$this->varChek($_POST['task_name']),
				"task_descr"=>$this->varChek($_POST['task_descr']),
				"task_job"=>$this->varChek($_POST['task_job']),
				"task_start_date"=>$this->varChek($_POST['task_start_date']),
				"task_start_time"=>$this->varChek($_POST['task_start_time']),
				"task_next_launch"=>$this->varChek($_POST['task_start_date']).' '.$this->varChek($_POST['task_start_time']),
				"task_next_launch_important"=>$this->varChek($_POST['task_start_date']).' '.$this->varChek($_POST['task_start_time']),
				"task_round"=>$this->varChek($_POST['task_round']),
				"task_round_period"=>$this->varChek($_POST['task_round_period']),
				"task_end_date"=>$this->varChek($_POST['task_end_date']),
				"task_end_time"=>$this->varChek($_POST['task_end_time']),
				"task_finished"=>($this->varChek($_POST['task_finished'])=='on'?1:0),
				"task_status"=>($this->varChek($_POST['task_status'])=='on'?1:0),
				"task_updatedate"=>date("Y-m-d H:i:s"),
			];
			$model_cron_tasks->InsertUpdate($data);
			header('Location: /admin/control/cron/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_cron_tasks,
				"item" => $item,
				"action" => '/admin/control/cron/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"task_id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Название", "name"=>"task_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Краткое описание", "name"=>"task_descr", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Задача", "name"=>"task_job", "attrs"=>[], "type"=>"select", "null"=>true, "items" => get_events()],
					["title"=>"Дата старта", "name"=>"task_start_date", "attrs"=>[], "type"=>"date"],
					["title"=>"Время старта", "name"=>"task_start_time", "attrs"=>[], "type"=>"time"],
					["title"=>"Количество выполнений (0-бесконечно)", "name"=>"task_round", "attrs"=>[], "type"=>"number"],
					["title"=>"Интервал перед повторением (в секундах, например: 3600 сек = 1 час )", "name"=>"task_round_period", "attrs"=>[], "type"=>"number"],
					["title"=>"Дата окончания", "name"=>"task_end_date", "attrs"=>[], "type"=>"date"],
					["title"=>"Время окончания", "name"=>"task_end_time", "attrs"=>[], "type"=>"time"],
					["title"=>"Выполнено", "name"=>"task_finished", "attrs"=>[], "type"=>"switch"],
					["title"=>"Состояние", "name"=>"task_status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		return $content;
	}

	function action_cron_add($actions=null){
		$content = '';
		$model_cron_tasks = new model_cron_tasks();
		$this->view->headers['title'] = 'Новая задача | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/', "Cron - задачи"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/cron/'];
		$this->view->data['breadcrumbs']['Новая задача'] = '';

		if(isset($_POST['add'])){$this->view->notRender();
			$data = [
				"task_name"=>$this->varChek($_POST['task_name']),
				"task_descr"=>$this->varChek($_POST['task_descr']),
				"task_job"=>$this->varChek($_POST['task_job']),
				"task_start_date"=>$this->varChek($_POST['task_start_date']),
				"task_start_time"=>$this->varChek($_POST['task_start_time']),
				"task_next_launch"=>$this->varChek($_POST['task_start_date']).' '.$this->varChek($_POST['task_start_time']),
				"task_next_launch_important"=>$this->varChek($_POST['task_start_date']).' '.$this->varChek($_POST['task_start_time']),
				"task_round"=>$this->varChek($_POST['task_round']),
				"task_round_period"=>$this->varChek($_POST['task_round_period']),
				"task_end_date"=>$this->varChek($_POST['task_end_date']),
				"task_end_time"=>$this->varChek($_POST['task_end_time']),
				"task_finished"=>($this->varChek($_POST['task_finished'])=='on'?1:0),
				"task_status"=>($this->varChek($_POST['task_status'])=='on'?1:0),
				"task_adddate"=>date("Y-m-d H:i:s"),
				"task_updatedate"=>date("Y-m-d H:i:s"),
			];
			$model_cron_tasks->Insert($data);
			header('Location: /admin/control/cron/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_cron_tasks,
				"item" => null,
				"action" => '/admin/control/cron/add/',
				"fields" => [
					["title"=>"Название", "name"=>"task_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Краткое описание", "name"=>"task_descr", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Задача", "name"=>"task_job", "attrs"=>[], "type"=>"select", "null"=>true, "items" => get_events()],
					["title"=>"Дата старта", "name"=>"task_start_date", "attrs"=>[], "type"=>"date"],
					["title"=>"Время старта", "name"=>"task_start_time", "attrs"=>[], "type"=>"time"],
					["title"=>"Количество выполнений (0-бесконечно)", "name"=>"task_round", "attrs"=>[], "type"=>"number"],
					["title"=>"Интервал перед повторением (в секундах, например: 3600 сек = 1 час )", "name"=>"task_round_period", "attrs"=>[], "type"=>"number"],
					["title"=>"Дата окончания", "name"=>"task_end_date", "attrs"=>[], "type"=>"date"],
					["title"=>"Время окончания", "name"=>"task_end_time", "attrs"=>[], "type"=>"time"],
					["title"=>"Выполнено", "name"=>"task_finished", "attrs"=>[], "type"=>"switch"],
					["title"=>"Состояние", "name"=>"task_status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"add", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		$this->view->data['content'] = $content;
	}

}
