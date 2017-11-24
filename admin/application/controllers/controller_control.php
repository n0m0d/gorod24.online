<?php

class controller_control extends Controller
{
	function __construct(){
		$this->view = new View('index.tpl');
		$this->view->setTemplatesFolder(ADMINDIR.'/application/views/');
		$this->view->headers['title'] = 'Управление | Администрирование Полезного радио';
		$this->view->data['main-menu']['Управление'] = true;
		$this->view->data['main']['header'] = 'Управление';
		$this->view->data['main']['menu'] = [
			[ "title" => "Пользователи", "url"=>"#", "items" => [
					["title" => "Все пользователи", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/users/"],
				] 
			],
			[ "title" => "Страницы", "url"=>"#", "items" => [
					["title" => "Все страницы", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/pages/"],
				] 
			],
			[ "title" => "Файлы", "url"=>"#", "items" => [
					["title" => "Все файлы", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/files/"],
					["title" => "Добавить файлы", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/control/files/add/"],
				] 
			],
		];
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
					"order" => "user_id DESC",
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"user_id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/{$row["user_id"]}/\">{$cel}</a>";')],
						["title"=>"Логин", "name"=>"user_login", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/users/{$row["user_id"]}/\">{$cel}</a>";')],
						["title"=>"Имя", "name"=>"user_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Состояние", "name"=>"user_status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
					],
				)
			);
			/**/
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
		$model_permissions = new model_permissions();
		$user = $model_users->getItem($id);
		$this->view->headers['title'] = 'Пользователь '.$user['user_name'].' | Администрирование Полезного радио';
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
					["title"=>"Сосотояние", "name"=>"user_status", "attrs"=>[], "type"=>"switch"],
					["content"=>"Если вы не хотите изменять пароль оставьте эти поля пустыми", "attrs"=>[], "type"=>"line"],
					["title"=>"Пароль", "name"=>"password-1", "attrs"=>[], "type"=>"password"],
					["title"=>"Пароль еще раз", "name"=>"password-2", "attrs"=>[], "type"=>"password"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"button"],
				],
			)
		);
		$content .= $admin;
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
				"order" => "post_id DESC",
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"Id", "name"=>"post_id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/pages/{$row["post_id"]}/\">{$cel}</a>";')],
					["title"=>"Имя", "name"=>"post_name", "attrs"=>[ ], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/pages/{$row["post_id"]}/\">{$cel}</a>";')],
					["title"=>"Краткое описание", "name"=>"post_description", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Сосотояние", "name"=>"post_status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
				],
			)
		);

		$content = $admin;
		$this->view->data['content'] = $content;
		
	}
	
	function action_files($actions=null){
		$this->view->headers['title'] = 'Файлы | Администрирование Полезного радио';
		$this->view->data['breadcrumbs'] = [ "Управление"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/', "Файлы"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/files/'];
		$this->view->data['header'] = "Файлы";
		if(!$actions){
		$model = new model_uploads();
		$admin = new AdminList(
			array(
				"model" => $model,
				"order" => "date DESC",
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/files/{$row["id"]}/\">{$cel}</a>";')],
					["title"=>"Имя", "name"=>"name", "attrs"=>[ ], "content"=>create_function('$cel,$row','echo "<a class=\"ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/files/{$row["id"]}/\">{$cel}</a>";')],
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

		$content = $admin;
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
			$type = mime_content_type($filePath);
			$d = explode('/', $type);
			$http_url = '/uploads/'.$d[0].'/'; 
			$dir = APPDIR . $http_url; 
			if(!is_dir( $dir )){ mkdir($dir); chmod($dir, 0777);}
			$ext = getExtension5($orig_name);
			$fileName = uniqid($d[1].'_').'.'.$ext;
			$fileName = $orig_name;
			$data = [
				"name" => $orig_name,
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
			
			$result = json_encode(array(
				"OK" => 1,
				"info" => "Upload successful.",
				"name" => $fileName,
				"url" => $url,
				"id" => $id,
			));
			die($result);
			
		}
		die('{"OK": 1, "info": "Upload successful."}');
		
	}
	
	public function action_filesadd(){
		$content = '';
		$model_uploads = new model_uploads();
		$this->view->headers['title'] = 'Добавление файлов | Администрирование Полезного радио';
		$this->view->data['header'] = 'Добавление файлов ';
		$this->view->data['breadcrumbs']['Добавление файлов'] = '';
		
		$content .= AdminPage::filesUploaderField([ "title"=>"Загрузка", "url"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/control/files/upload/' ]);
		return $content;
	}
	
	
}