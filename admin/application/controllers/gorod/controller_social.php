<?php
require_once( __DIR__ ."/controller_index.php");
class controller_social extends controller_index
{
	function __construct(){
		parent::__construct();
		$this->model_social = new model_social();
	}
	
	/********** Рассылки в соц.сети **********/
	function action_index($actions=null){
		$this->action_publish($actions);
	}
	
	function action_publish($actions=null){
		$this->view->headers['title'] = 'Рассылки в соц.сети | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Рассылки в соц.сети"=>$this->url.'/social/'];
		$this->view->data['header'] = "Рассылки в соц.сети";
		
		$page = (int)$_REQUEST['page']; $page = ($page?$page:1);
		$search = trim($_REQUEST['search']);
		
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$this->model_social->Delete($input);
			}
		}
		
		$where = "1";
		$websites = $this->websites;
			$admin = new AdminList(
				array(
					"model" => $this->model_social,
					"where" => $where,
					"model_cols" => "*, 
						(select `name` FROM `{$this->model_social->model_accounts()->getdatabasename()}`.`{$this->model_social->model_accounts()->gettablename()}` as `accounts` WHERE `accounts`.`id` = `account_id_from` LIMIT 1) as `account_name`,
						(select `social_type` FROM `{$this->model_social->model_accounts()->getdatabasename()}`.`{$this->model_social->model_accounts()->gettablename()}` as `accounts` WHERE `accounts`.`id` = `account_id_from` LIMIT 1) as `account_type`
						",
					"order" => "id DESC",
					"multiple" => "true",
					"action" => $this->url.'/social/publish/',
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Добавить рассылку", "href"=>$this->url."/social/publish/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
					],
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"ID", "name"=>"id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Страница", "name"=>"account_name", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
							echo "<div style=\"width:200px;overflow:auto;\">{$cel}</div>";
						')],
						["title"=>"Соц. сеть", "name"=>"account_type", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Тело", "name"=>"text", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo "<div style=\"width:300px;overflow:auto;\">{$cel}</div>";
						')],
						["title"=>"Ссылка","name"=>"link", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo "<div style=\"width:300px;overflow:auto;\">{$cel}</div>";
						')],
						["title"=>"Дата отправки", "name"=>"send_date", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Статус", "name"=>"is_sended", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							if($cel==0){
								if($row["error_code"]){
									echo "<span class=\"red\">[{$row["error_code"]}] {$row["error_message"]}</span>";
								}
								else {
									echo "<span class=\"red\">Ожидает отправки</span>";
								}
							}
							else {
								echo "<span class=\"green\">Отправка запущена</span>";
							}
						')],
						["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
							echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/social/publish/edit/{$row["id"]}/?page='.$page.'&search='.$search.'\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
						')]
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
		$this->view->data['content'] = $content;
	}
	
	function action_publish_add($actions=null){ $this->action_publish_edit($actions); }
	
	function action_publish_edit($actions=null){
		$id = (int)$actions[0];
		$page = (int)$_REQUEST['page']; $page = ($page?$page:1);
		$search = trim($_REQUEST['search']);
		if($id){
			$item = $this->model_social->getItem($id);
			$this->view->headers['title'] = 'Рассылки в соц.сети | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Рассылки в соц.сети"=>$this->url.'/social/publish/', "Редактирование"=>$this->url."/social/publish/edit/{$id}"];
			$this->view->data['header'] = "Редактирование Рассылки";
			$action = "{$this->url}/social/publish/edit/{$id}/?page={$page}&search={$search}";
			$multiple = false;
		}
		else {
			$this->view->headers['title'] = 'Рассылки в соц.сети | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Рассылки в соц.сети"=>$this->url.'/social/publish/', "Добавление"=>$this->url."/social/publish/add/"];
			$this->view->data['header'] = "Добавление Рассылки";
			$action = "{$this->url}/social/publish/add/?page={$page}&search={$search}";
			$item=[];
			$item['bas_date'] = date("Y-m-d");
			$multiple = true;
		}
		if(isset($_REQUEST['save'])){
			$this->view->notRender();
			if($id){
				$ids = [$_REQUEST['account_id_to']];
			}
			else {
			$ids = $_REQUEST['account_id_to'];
			}
			if(!empty($ids)){
			foreach($ids as $account_id_to){
				
				$to = $this->model_social->model_accounts()->getItem($account_id_to); 
				$account_id_from = $this->model_social->model_accounts()->getItemWhere("`social_type`='{$to['social_type']}' AND `is_main`=1"); 
				
				if($_REQUEST['src-photo']){ 
					if(stripos($_REQUEST['src-photo'], 'https://gorod24.online')!==false){
						$photo = $_REQUEST['src-photo'];
					}
					else {
					$photo = "https://gorod24.online{$_REQUEST['src-photo']}";
					}
				}
				$social_to = $this->model_social->model_accounts()->getItem($account_id_to);
				$data = [
					//'account_id_from' => trim($_REQUEST['account_id_from']),
					'account_id_from' => $account_id_from['id'],
					'account_id_to' => $account_id_to,
					'social_id_to' => $social_to['social_id'],
					'social_id_type' => $social_to['social_id_type'],
					'text' => trim($_REQUEST['text']),
					'link' => trim($_REQUEST['link']),
					'post_type' => trim($_REQUEST['post_type']),
					'album_id' => $account_id_from['album_id'],
					'photo' => trim( $photo ),
					'send_date' => trim($_REQUEST['send_date']),
				];
				if($account_id_from['social_id_type']==1){ $data['group_id']=$account_id_from['social_id'];} else {$data['group_id']=0;}
				//echo "<pre>"; print_r($data); exit;
				if($id){
					$data['id'] = $id;
				}
				else {
					$data['date'] = '0000-00-00 00:00:00';
					$data['is_sended'] = '0';
					$data['access_token'] = '0';
					$data['message_id'] = '0';
				}
				
				$new_id = $this->model_social->InsertUpdate($data);
			}
			header("Location: {$this->url}/social/publish/?page={$page}&search={$search}");
			} else {
				echo "<h1>Вы не выбрали на какие аккаунты выполнять отправку.</h1>";
			}
		}
		
		$accounts = $this->model_social->model_accounts()->getItemsWhere("`status` ='1'", null, null, null, "`id` as `value`, `name` as `label`");
		//$accounts_to = $this->model_social->model_accounts()->getItemsWhere("`status` ='1'", null, null, null, "`social_id` as `value`, `name` as `label`");
		
		$admin = new AdminPage(
			array(
				"model" => $this->model_social,
				"item" => $item,
				"action" => $action,
				"fields" => [
					//["title"=>"От кого", "name"=>"account_id_from", "attrs"=>[], "type"=>"select", "items"=>$accounts],
					["title"=>"На какую страницу", "name"=>"account_id_to", "attrs"=>[], "type"=>"select", "multiple"=>$multiple, "items"=>$accounts],
					//["title"=>"Тип страницы", "name"=>"social_id_type", "attrs"=>[], "type"=>"select", "items"=>[ [ 'value'=>0, 'label'=>'Страница пользователя'], [ 'value'=>2, 'label'=>'Группа'], ]],
					["title"=>"Текст", "name"=>"text", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Ссылка", "name"=>"link", "attrs"=>[], "type"=>"text"],
					["title"=>"Тип публикации", "name"=>"post_type", "attrs"=>[], "type"=>"select", "items"=>[ ['value'=>0, 'label'=>'Публикация ссылки'],['value'=>1, 'label'=>'Публикация картинки'], ]],
					["title"=>"Фото", "name"=>"photo", "attrs"=>[], "type"=>"fileExplorer", "src"=>true, "accept"=>"image/*"],
					["title"=>"Дата отправки", "name"=>"send_date", "attrs"=>[], "type"=>"datetime"],
					
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	function action_accounts($actions=null){
		$this->view->headers['title'] = 'Страницы соц.сетей | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Рассылки в соц.сети"=>$this->url.'/social/', "Страницы соц.сетей"=>$this->url.'/social/accounts/'];
		$this->view->data['header'] = "Страницы соц.сетей";
		$page = (int)$_REQUEST['page']; $page = ($page?$page:1);
		$search = trim($_REQUEST['search']);
		
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$this->model_social->model_accounts()->Delete($input);
			}
		}
		
		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$this->model_social->model_accounts()->Update([ 'status'=>'1' ], $input);
			}
		}
		
		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$this->model_social->model_accounts()->Update([ 'status'=>'0' ], $input);
			}
		}
		
		$where = "1";
		$websites = $this->websites;
			$admin = new AdminList(
				array(
					"model" => $this->model_social->model_accounts(),
					"where" => $where,
					"model_cols" => "*",
					"order" => "id DESC",
					"multiple" => "true",
					"action" => $this->url.'/social/accounts/',
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить", "href"=>$this->url."/social/accounts/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
					],
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"ID", "name"=>"id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Название", "name"=>"name", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Отправок", "name"=>"sends", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Соц. сеть", "name"=>"social_type", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Статус", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							switch($cel){
								case 0: echo "<span class=\"red\">Отключена</span>"; break; 
								case 1: echo "<span class=\"green\">Включена</span>"; break; 
							}
						')],
						["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
							echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/social/accounts/edit/{$row["id"]}/?page='.$page.'&search='.$search.'\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
						')]
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
		$this->view->data['content'] = $content;
	}
	
	function action_accounts_add($actions=null){ $this->action_accounts_edit($actions); }
	
	function action_accounts_edit($actions=null){
		$id = (int)$actions[0];
		$page = (int)$_REQUEST['page']; $page = ($page?$page:1);
		$search = trim($_REQUEST['search']);
		if($id){
			$item = $this->model_social->model_accounts()->getItem($id);
			$this->view->headers['title'] = 'Страницы соц.сетей | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Рассылки в соц.сети"=>$this->url.'/social/publish/', "Страницы соц.сетей"=>$this->url.'/social/accounts/', "Редактирование"=>$this->url."/social/accounts/edit/{$id}"];
			$this->view->data['header'] = "Редактирование Страницы";
			$action = "{$this->url}/social/accounts/edit/{$id}/?page={$page}&search={$search}";
		}
		else {
			$this->view->headers['title'] = 'Страницы соц.сетей | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Рассылки в соц.сети"=>$this->url.'/social/publish/', "Страницы соц.сетей"=>$this->url.'/social/accounts/', "Добавление"=>$this->url."/social/accounts/add/"];
			$this->view->data['header'] = "Добавление Страницы";
			$action = "{$this->url}/social/accounts/add/?page={$page}&search={$search}";
			$item=[];
			$item['bas_date'] = date("Y-m-d");
		}
		if(isset($_REQUEST['save'])){
			$this->view->notRender();
			
			$data = [
				'name' => trim($_REQUEST['name']),
				'description' => trim($_REQUEST['description']),
				'social_type' => trim($_REQUEST['social_type']),
				'social_id_type' => trim($_REQUEST['social_id_type']),
				'social_id' => trim($_REQUEST['social_id']),
				'album_id' => trim($_REQUEST['album_id']),
				'is_main' => ($_REQUEST['is_main']=='on'?1:0),
				'access_token' => trim($_REQUEST['access_token']),
				'app_id' => trim($_REQUEST['app_id']),
				'app_secret' => trim($_REQUEST['app_secret']),
				'app_public' => trim($_REQUEST['app_public']),
				'status' => ($_REQUEST['status']=='on'?1:0),
			];
			if($id){
				$data['id'] = $id;
			}
			else {
				$data['sends'] = '0';
			}
			
			if($data['is_main']){
				$this->model_social->model_accounts()->Update(['is_main'=>0], "`social_type`='{$_REQUEST['social_type']}'");
			}
			
			$new_id = $this->model_social->model_accounts()->InsertUpdate($data);
			
			header("Location: {$this->url}/social/accounts/?page={$page}&search={$search}");
		}
		$soc = [ 
			[ 'value'=>'vk', 'label'=>'vk'], 
			[ 'value'=>'Facebook', 'label'=>'Facebook'], 
			[ 'value'=>'Odnoklassniki', 'label'=>'Odnoklassniki'], 
		];
		$admin = new AdminPage(
			array(
				"model" => $this->model_social->model_accounts(),
				"item" => $item,
				"action" => $action,
				"fields" => [
					["title"=>"Название", "name"=>"name", "attrs"=>[], "type"=>"еуче"],
					["title"=>"Описание", "name"=>"description", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Соц. сеть", "name"=>"social_type", "attrs"=>[], "type"=>"select", "items"=>$soc],
					["title"=>"Тип страницы", "name"=>"social_id_type", "attrs"=>[], "type"=>"select", "items"=>[ [ 'value'=>0, 'label'=>'Страница пользователя'], [ 'value'=>1, 'label'=>'Группа'], ]],
					["title"=>"Id", "name"=>"social_id", "attrs"=>[], "type"=>"text"],
					["title"=>"Id фотоальбома для загрузки фоток", "name"=>"album_id", "attrs"=>[], "type"=>"text"],
					["title"=>"Аккаунт по умолчанию для отправки в соц сети", "name"=>"is_main", "attrs"=>[], "type"=>"switch"],
					["title"=>"access_token", "name"=>"access_token", "attrs"=>[], "type"=>"text"],
					["title"=>"id - приложения", "name"=>"app_id", "attrs"=>[], "type"=>"text"],
					["title"=>"Секретный ключ приложения", "name"=>"app_secret", "attrs"=>[], "type"=>"text"],
					["title"=>"Публичный ключ приложения", "name"=>"app_public", "attrs"=>[], "type"=>"text"],
					["title"=>"Статус (включено)", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	function action_auto($actions=null){
		$this->view->headers['title'] = 'Автоматический постинг | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Рассылки в соц.сети"=>$this->url.'/social/', "Автоматический постинг"=>$this->url.'/social/auto/'];
		$this->view->data['header'] = "Автоматический постинг";
		$page = (int)$_REQUEST['page']; $page = ($page?$page:1);
		$search = trim($_REQUEST['search']);
		
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$this->model_social->model_auto_posting()->Delete($input);
			}
		}
		
		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$this->model_social->model_auto_posting()->Update([ 'status'=>'1' ], $input);
			}
		}
		
		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$this->model_social->model_auto_posting()->Update([ 'status'=>'0' ], $input);
			}
		}
		
		$where = "1";
		$websites = $this->websites;
			$admin = new AdminList(
				array(
					"model" => $this->model_social->model_auto_posting(),
					"where" => $where,
					"model_cols" => "*",
					"order" => "id DESC",
					"multiple" => "true",
					"action" => $this->url.'/social/auto/',
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить", "href"=>$this->url."/social/auto/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
					],
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"ID", "name"=>"id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Название", "name"=>"name", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Отправок", "name"=>"sends", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Статус", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							switch($cel){
								case 0: echo "<span class=\"red\">Отключена</span>"; break; 
								case 1: echo "<span class=\"green\">Включена</span>"; break; 
							}
						')],
						["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
							echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/social/auto/edit/{$row["id"]}/?page='.$page.'&search='.$search.'\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
						')]
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
		$this->view->data['content'] = $content;
	}
	
	function action_auto_add($actions=null){ $this->action_auto_edit($actions); }
	
	function action_auto_edit($actions=null){
		$id = (int)$actions[0];
		$page = (int)$_REQUEST['page']; $page = ($page?$page:1);
		$search = trim($_REQUEST['search']);
		if($id){
			$item = $this->model_social->model_auto_posting()->getItem($id);
			$this->view->headers['title'] = 'Автоматический постинг | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Рассылки в соц.сети"=>$this->url.'/social/publish/', "Автоматический постинг"=>$this->url.'/social/auto/', "Редактирование"=>$this->url."/social/auto/edit/{$id}"];
			$this->view->data['header'] = "Редактирование автоматического постинга";
			$action = "{$this->url}/social/auto/edit/{$id}/?page={$page}&search={$search}";
			$tags = json_decode($item['news_tags']);
		}
		else {
			$this->view->headers['title'] = 'Автоматический постинг | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Рассылки в соц.сети"=>$this->url.'/social/publish/', "Автоматический постинг"=>$this->url.'/social/auto/', "Добавление"=>$this->url."/social/auto/add/"];
			$this->view->data['header'] = "Добавление автоматического постинга";
			$action = "{$this->url}/social/auto/add/?page={$page}&search={$search}";
			$item=[];
			$item['bas_date'] = date("Y-m-d");
		}
		if(isset($_REQUEST['save'])){
			$this->view->notRender();
			$tags = [];
			$_tags = explode(",", $_REQUEST['news_tags-input']);
			foreach($_tags as $tag){ $tag=trim($tag); if(!empty($tag)){$tags[]=$tag;}}
			$acount = $this->model_social->model_accounts()->getItem($_REQUEST['acount_id']);
			$from = $this->model_social->model_accounts()->getItem($_REQUEST['acount_id_from']);
			
			$data = [
				'name' => trim($_REQUEST['name']),
				'description' => trim($_REQUEST['description']),
				'news_cities' => json_encode($_REQUEST['news_cities']),
				'news_razds' => json_encode($_REQUEST['news_razds']),
				'news_tags' => json_encode($tags, JSON_UNESCAPED_UNICODE),
				'news_interval' => $_REQUEST['news_interval'],
				'url' => trim($_REQUEST['url']),
				'post_type' => trim($_REQUEST['post_type']),
				'acount_id' => trim($_REQUEST['acount_id']),
				'acount_id_from' => trim($_REQUEST['acount_id_from']),
				//'group_id' => $_REQUEST['group_id'],
				'album_id' => trim($_REQUEST['album_id']),
				'round_period' => trim($_REQUEST['round_period']),
				'start_date' => trim($_REQUEST['start_date']),
				'next_launch' => trim($_REQUEST['next_launch']),
				'end_date' => trim($_REQUEST['end_date']),
				'status' => ($_REQUEST['status']=='on'?1:0),
			];
			if($from['social_id_type']==1 AND $from['social_id']){
				$data['group_id'] = $from['social_id'];
			}
			else {
				$data['group_id'] = 0;
			}
			if($id){
				$data['id'] = $id;
			}
			else {
				$data['round_launches'] = '0';
				$data['last_launch'] = '0000-00-00 00:00:00';
				$data['next_launch'] = $data['start_date'];
				$data['sends'] = '0';
			}
			//var_dump($data); exit;
			$new_id = $this->model_social->model_auto_posting()->InsertUpdate($data);
			header("Location: {$this->url}/social/auto/?page={$page}&search={$search}");
		}
		
		
		
		$model_gorod_news = new model_gorod_news();
		$accounts = $this->model_social->model_accounts()->getItemsWhere("`status` ='1'", null, null, null, "`id` as `value`, `name` as `label`");
		
		$admin = new AdminPage(
			array(
				"model" => $this->model_social->model_auto_posting(),
				"item" => $item,
				"action" => $action,
				"fields" => [
					["title"=>"Название", "name"=>"name", "attrs"=>[], "type"=>"еуче"],
					["title"=>"Дополнительный текст", "name"=>"description", "attrs"=>[], "type"=>"mediumText"],
					["type"=>"line", "content"=>"В текст можно вставлять колонки из новости, в формате <b>%<название колонки>%</b>.<br>Например: <b>%news_head%</b>, <b>%news_lid%</b>, <b>%news_body%</b>, <b>%url%</b>, <b>%url_ru%</b> <hr>"],
					["title"=>"Отступ обора новостей от текущей даты (в днях)", "name"=>"news_interval", "attrs"=>[], "type"=>"number"],
					["title"=>"Города",  "name"=>"news_cities", "attrs"=>[], "multiple"=>true, "value"=>json_decode($item['news_cities']), "type"=>"select", "items" => $model_gorod_news->model_cities()->getItemsWhere("`in_news`=1", "`city_title` ASC", null, null, "`city_id` as `value`, `city_title` as `label`")],
					["title"=>"Разделы", "name"=>"news_razds",  "attrs"=>[], "multiple"=>true, "value"=>json_decode($item['news_razds']),  "type"=>"select", "items" => $model_gorod_news->model_razd()->getItemsWhere("`on_off`=1", "`name_razd` ASC", null, null, "`id` as `value`, `name_razd` as `label`")],
					["title"=>"Теги к новости", "name"=>"news_tags", "attrs"=>[], "type"=>"autocomplete", "values"=>$tags, "label"=>implode(', ',$tags), "multiple"=>true, "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/tags"],
					["title"=>"Урл новости", "name"=>"url", "attrs"=>[], "type"=>"select", "items"=>[ ['value'=>'url','label'=>'Латиница'], ['value'=>'url_ru', 'label'=>'Кириллица'], ]],
					["title"=>"Тип публикации", "name"=>"post_type", "attrs"=>[], "type"=>"select", "items"=>[ ['value'=>0, 'label'=>'Публикация ссылки'],['value'=>1, 'label'=>'Публикация картинки'], ]],
					["title"=>"На страницу", "name"=>"acount_id", "attrs"=>[], "type"=>"select", "items"=>$accounts],
					["title"=>"С аккаунта", "name"=>"acount_id_from", "attrs"=>[], "type"=>"select", "items"=>$accounts],
					["title"=>"Id фотоальбома", "name"=>"album_id", "attrs"=>[], "type"=>"text"],
					["title"=>"Постить каждые N секунд ( 3600 = 1 час )", "name"=>"round_period", "attrs"=>[], "type"=>"number"],
					["title"=>"Дата старта", "name"=>"start_date", "attrs"=>[], "type"=>"datetime"],
					["title"=>"Дата следующего запуска", "name"=>"next_launch", "attrs"=>[], "type"=>"datetime"],
					["title"=>"Дата окончания", "name"=>"end_date", "attrs"=>[], "type"=>"datetime"],
					["title"=>"Статус (включено)", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	
}