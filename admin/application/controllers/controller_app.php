<?php

class controller_app extends Controller
{
	function __construct(){
		$this->view = new View('index.tpl');
		$this->view->setTemplatesFolder(ADMINDIR.'/application/views/');
		$this->view->headers['title'] = 'Приложение | Администрирование Город 24';
		$this->view->data['main-menu']['Приложение'] = true;
		$this->view->data['main']['header'] = 'Приложение';
		$this->view->data['main']['menu'] = [
			[ "title" => "Сервисы", "url"=>"#", "items" => [
					["title" => "Баннеры", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/app/banners/"],
					["title" => "Конкурсы", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/app/congroups/"],
				]
			],
			[ "title" => "Статистика", "url"=>"#", "items" => [
					["title" => "Устройства", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/app/stat/devices/"],
					["title" => "Бонусы при логине по 1000", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/app/stat/login1000/"],
					["title" => "Бонусы по приглашениям", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/app/stat/invite/"],
				]
			],
		];
	}
	
	function action_index($actions=null){
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/'];
		$this->view->data['header'] = "Приложение";
	}
	/********** БАННЕРЫ **********/
	function action_banners($actions=null){
		$this->view->headers['title'] = 'Баннеры | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Баннеры"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/banners/'];
		$this->view->data['header'] = "Баннеры";

		// Удаление элементов
		if (isset($_POST['del'])){
			$model_banners = new model_banners();
			foreach ($_POST['options'] as $input)
			{
				$model_banners->Delete($input);
			}
		}
		
		if (isset($_POST['on'])){
			$model_banners = new model_banners();
			foreach ($_POST['options'] as $input)
			{
				$model_banners->Update([ 'on_off'=>1 ], $input);
			}
		}
		
		if (isset($_POST['off'])){
			$model_banners = new model_banners();
			foreach ($_POST['options'] as $input)
			{
				$model_banners->Update([ 'on_off'=>0 ], $input);
			}
		}
		
		//var_dump($_POST); exit;
		if(!$actions){

			$model_banners = new model_banners();
			$admin = new AdminList(
				array(
					"action" => '/admin/app/banners/',
					"model" => $model_banners,
					"order" => "id DESC",
					"multiple" => "true",
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить новый баннер", "href"=>"/admin/app/banners/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"]
					],
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/banners/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Название", "name"=>"name", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/banners/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Ссылка", "name"=>"link", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Позиция", "name"=>"position", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){ case 1: echo "Верхний"; break; case 2: echo "В списке"; break; case 3: echo "Всплывающий"; break;  }')],
						["title"=>"Показов", "name"=>"impressions", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Города", "name"=>"cities", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							$cities = explode(";", $cel);
							$_model_cities = new model_cities();
							foreach($cities as $i=>$item){
								if(!empty($item)){
									$city = $_model_cities->getItem($item);
									echo "<p>".$city["city_title"]."</p>";
								}
							}
						')],
						["title"=>"Превью", "name"=>"img", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo "<img style=\"width:100px;\" src=\"{$cel}\">";')],
						["title"=>"Состояние", "name"=>"on_off", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
		}
		else {
			if(is_numeric($actions[0])){
				$content = $this->action_banners_edit(((int)$actions[0]));
			}
		}
		$this->view->data['content'] = $content;
	}

	public function action_banners_edit($id){
		$content = '';
		$model_banners = new model_banners();
		$banners = $model_banners->getItem($id);
		$this->view->headers['title'] = 'Баннер #'.$banners['id'].' | Администрирование Город 24';
		$this->view->data['header'] = 'Баннер #'.$banners['id'];
		$this->view->data['breadcrumbs']['Баннер #'.$banners['id']] = '';


		$this->_model_cities = new model_cities();
		$banners_cities = explode(';', $banners['cities']);
		$p = [];
		foreach($banners_cities as $i=>$item){
			if($i!=0 and $i!=count($banners_cities)){
				$p[] = $item;
			}
		}
		$cities = $this->_model_cities->getItemsWhere("`status`=1", "city_title ASC", null, null);
		
		if(isset($_POST['save'])){$this->view->notRender();
			$cities = ';';
			foreach($_POST['city_id'] as $city=>$val){
				$cities .= $city.';';
			}
			$data = [
				"id"=>$this->varChek($_POST['id']),
				"cities"=>$cities,
				"type"=>$this->varChek($_POST['type']),
				"position"=>$this->varChek($_POST['position']),
				"name"=>$this->varChek($_POST['name']),
				"link"=>$this->varChek($_POST['link']),
				"img"=> (!empty($_POST['src-img_id']))?$GLOBALS['CONFIG']['HTTP_HOST'].$this->varChek($_POST['src-img_id']):'',
				"img_id"=>$this->varChek($_POST['img_id']),
				"img760"=>(!empty($_POST['src-img760_id']))?$GLOBALS['CONFIG']['HTTP_HOST'].$this->varChek($_POST['src-img760_id']):'',
				"img760_id"=>$this->varChek($_POST['img760_id']),
				"img480"=>(!empty($_POST['src-img480_id']))?$GLOBALS['CONFIG']['HTTP_HOST'].$this->varChek($_POST['src-img480_id']):'',
				"img480_id"=>$this->varChek($_POST['img480_id']),
				"html"=>$_POST['html'],
				"date_start"=>$this->varChek($_POST['date_start']),
				"date_end"=>$this->varChek($_POST['date_end']),
				"on_off"=>($this->varChek($_POST['on_off'])=='on'?1:0),
				"impressions"=>$this->varChek($_POST['impressions']),
				"clicks"=>$this->varChek($_POST['clicks']),
				"controller"=>'',
			];
			$model_banners->InsertUpdate($data);

			header('Location: /admin/app/banners/');
		}

		$fields = [];
		foreach($cities as $i=>$city){
			$fields[] = ["title"=>"Город \"{$city['city_title']}\"", "name"=>"city_id[{$city['city_id']}]", "attrs"=>[], "value"=>(in_array($city['city_id'], $p)?1:0), "type"=>"check"];
		}
		
		$fields = array_merge($fields,[
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Тип", "name"=>"type", "attrs"=>[], "type"=>"select", "items"=>[["value"=>"1", "label"=>"Картинка"], ["value"=>"2", "label"=>"HTML"]] ],
					["title"=>"Название", "name"=>"name", "attrs"=>[], "type"=>"text"],
					["title"=>"Ссылка", "name"=>"link", "attrs"=>[], "type"=>"text"],
					["title"=>"Позиция", "name"=>"position", "attrs"=>[], "type"=>"select", "items"=>[["value"=>"1", "label"=>"Верхний"], ["value"=>"2", "label"=>"В списке"], ["value"=>"3", "label"=>"Всплывающий"]]],
					["title"=>"Картинка (большая)", "name"=>"img_id", "attrs"=>["requared"=>"required"], "type"=>"fileExplorer", "accept"=>"image"],
					["title"=>"Картинка (средняя)", "name"=>"img760_id", "attrs"=>["requared"=>"required"], "type"=>"fileExplorer", "accept"=>"image"],
					["title"=>"Картинка (маленькая)", "name"=>"img480_id", "attrs"=>["requared"=>"required"], "type"=>"fileExplorer", "accept"=>"image"],
					["title"=>"HTML", "name"=>"html", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Дата старта", "name"=>"date_start", "attrs"=>[], "type"=>"date"],
					["title"=>"Дата окончания", "name"=>"date_end", "attrs"=>[], "type"=>"date"],
					["title"=>"Кликов", "name"=>"clicks", "attrs"=>[], "type"=>"number"],
					["title"=>"Показано всего", "name"=>"impressions", "attrs"=>[], "type"=>"number"],
					["title"=>"Состояние", "name"=>"on_off", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
			]);
		
		$admin = new AdminPage(
			array(
				"model" => $model_banners,
				"item" => $banners,
				"action" => '/admin/app/banners/'.$id.'/',
				"fields" => $fields,
			)
		);
		$content .= $admin;
		return $content;
	}

	function action_banners_add($actions=null){
		$content = '';
		$model_banners = new model_banners();
		$this->view->headers['title'] = 'Добавить баннер | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Баннеры"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/banners/', "Добавить баннер"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/banners/add/'];
		$this->view->data['header'] = "Добавить баннер";
		

		$this->_model_cities = new model_cities();
		$banners_cities = explode(';', $banners['cities']);
		$p = [];
		foreach($banners_cities as $i=>$item){
			if($i!=0 and $i!=count($banners_cities)){
				$p[] = $item;
			}
		}
		$cities = $this->_model_cities->getItemsWhere("`status`=1", "city_title ASC", null, null);
		
		if(isset($_POST['add'])){$this->view->notRender();
			$cities = ';';
			foreach($_POST['city_id'] as $city=>$val){
				$cities .= $city.';';
			}
			$data = [
				"cities"=>$cities,
				"type"=>$this->varChek($_POST['type']),
				"position"=>$this->varChek($_POST['position']),
				"name"=>$this->varChek($_POST['name']),
				"link"=>$this->varChek($_POST['link']),
				"img"=> (!empty($_POST['src-img_id']))?$GLOBALS['CONFIG']['HTTP_HOST'].$this->varChek($_POST['src-img_id']):'',
				"img_id"=>$this->varChek($_POST['img_id']),
				"img760"=>(!empty($_POST['src-img760_id']))?$GLOBALS['CONFIG']['HTTP_HOST'].$this->varChek($_POST['src-img760_id']):'',
				"img760_id"=>$this->varChek($_POST['img760_id']),
				"img480"=>(!empty($_POST['src-img480_id']))?$GLOBALS['CONFIG']['HTTP_HOST'].$this->varChek($_POST['src-img480_id']):'',
				"img480_id"=>$this->varChek($_POST['img480_id']),
				"html"=>$_POST['html'],
				"date_start"=>$this->varChek($_POST['date_start']),
				"date_end"=>$this->varChek($_POST['date_end']),
				"on_off"=>($this->varChek($_POST['on_off'])=='on'?1:0),
				"impressions"=>$this->varChek($_POST['impressions']),
				"clicks"=>$this->varChek($_POST['clicks']),
				"controller"=>'',
			];
			$model_banners->InsertUpdate($data);
			header('Location: /admin/app/banners/');
		}
		$fields = [];
		foreach($cities as $i=>$city){
			$fields[] = ["title"=>"Город \"{$city['city_title']}\"", "name"=>"city_id[{$city['city_id']}]", "attrs"=>[], "value"=>(in_array($city['city_id'], $p)?1:0), "type"=>"check"];
		}
		
		$fields = array_merge($fields,[
					["title"=>"Тип", "name"=>"type", "attrs"=>[], "type"=>"select", "items"=>[["value"=>"1", "label"=>"Картинка"], ["value"=>"2", "label"=>"HTML"]] ],
					["title"=>"Название", "name"=>"name", "attrs"=>[], "type"=>"text"],
					["title"=>"Ссылка", "name"=>"link", "attrs"=>[], "type"=>"text"],
					["title"=>"Позиция", "name"=>"position", "attrs"=>[], "type"=>"select", "items"=>[["value"=>"1", "label"=>"Верхний"], ["value"=>"2", "label"=>"В списке"], ["value"=>"3", "label"=>"Всплывающий"]]],
					["title"=>"Картинка (большая)", "name"=>"img_id", "attrs"=>["requared"=>"required"], "type"=>"fileExplorer", "accept"=>"image"],
					["title"=>"Картинка (средняя)", "name"=>"img760_id", "attrs"=>["requared"=>"required"], "type"=>"fileExplorer", "accept"=>"image"],
					["title"=>"Картинка (маленькая)", "name"=>"img480_id", "attrs"=>["requared"=>"required"], "type"=>"fileExplorer", "accept"=>"image"],
					["title"=>"HTML", "name"=>"html", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Дата старта", "name"=>"date_start", "attrs"=>[], "type"=>"date"],
					["title"=>"Дата окончания", "name"=>"date_end", "attrs"=>[], "type"=>"date"],
					["title"=>"Кликов", "name"=>"clicks", "attrs"=>[], "type"=>"number"],
					["title"=>"Показано всего", "name"=>"impressions", "attrs"=>[], "type"=>"number"],
					["title"=>"Состояние", "name"=>"on_off", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"add", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
			]);
		
		$admin = new AdminPage(
			array(
				"model" => $model_banners,
				"item" => null,
				"action" => '/admin/app/banners/add/',
				"fields" => $fields,
			)
		);
		$content .= $admin;
		$this->view->data['content'] = $content;
	}


	/********** ГРУППЫ КОНКУРСОВ **********/
	function action_congroups($actions=null){
		$this->view->headers['title'] = 'Группы конкурсов | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Группы конкурсов"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/'];
		$this->view->data['header'] = "Группы конкурсов";

		$model_contests_groups = new model_contests_groups();

		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_contests_groups->Delete($input);
			}
		}

		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model_contests_groups->Update(['status'=>1], $input);
			}
		}

		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model_contests_groups->Update(['status'=>0], $input);
			}
		}

		if(!$actions){
			$admin = new AdminList(
				array(
					"action" => "/admin/app/congroups/",
					"model" => $model_contests_groups,
					"order" => "id DESC",
					"multiple" => "true",
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить группу конкурсов", "href"=>"/admin/app/congroups/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"]
					],
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Название", "name"=>"title", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/{$row["id"]}/contests/\">{$cel}</a>";')],
						["title"=>"Описание", "name"=>"descr", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo strip_tags($cel);')],
						["title"=>"Состояние", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
						["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/{$row["id"]}\">{$cel}<em class=\"fa fa-pencil fa-2\" title=\"Изменить\"></em></a>"; echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/{$row["id"]}/contests/\">{$cel}<em class=\"fa fa-list fa-2\" title=\"Конкурсы\"></em></a>";')],
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
		}
		else {
			if(is_numeric($actions[0]) and count($actions)==1){
				$content = $this->action_congroups_edit(((int)$actions[0]));
			}
			elseif(is_numeric($actions[0]) and $actions[1]=='contests') {
				$content = $this->action_contests($actions);
			}
		}
		$this->view->data['content'] = $content;
	}

	public function action_congroups_edit($id){
		$content = '';
		$model_contests_groups = new model_contests_groups();
		$contest = $model_contests_groups->getItem($id);
		$this->view->headers['title'] = 'Группа конкурсов '.$contest['title'].' | Администрирование Город 24';
		$this->view->data['header'] = 'Группа конкурсов '.$contest['title'];
		$this->view->data['breadcrumbs'][$contest['title']] = '';

		$fields = [];
		if(isset($_POST['save'])){$this->view->notRender();
			$data = [
				"id"=>$this->varChek($_POST['id']),
				"title"=>$this->varChek($_POST['title']),
				"descr"=>$_POST['descr'],
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
			];
			$model_contests_groups->InsertUpdate($data);
			header('Location: /admin/app/congroups/');
		}

		$fields = array_merge($fields, [
			["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
			["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
			["title"=>"Описание", "name"=>"descr", "attrs"=>[], "type"=>"editor"],
			["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
			["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
		]);

		$admin = new AdminPage(
			array(
				"model" => $model_contests_groups,
				"item" => $contest,
				"action" => '/admin/app/congroups/'.$id.'/',
				"fields" => $fields,
			)
		);

		$content .= $admin;
		return $content;
	}

	function action_congroups_add($actions=null){
		$content = '';
		$model_contests_groups = new model_contests_groups();
		$this->view->headers['title'] = 'Добавить конкурс | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Группы конкурсов"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/', "Добавить группу конкурсов"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/add/'];
		$this->view->data['header'] = "Добавить группу конкурсов";

		$fields = [];

		if(isset($_POST['add'])){$this->view->notRender();
			$data = [
				"title"=>$this->varChek($_POST['title']),
				"descr"=>$_POST['descr'],
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
			];
			$model_contests_groups->InsertUpdate($data);

			header('Location: /admin/app/congroups/');
		}

		$fields = array_merge($fields, [
			["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
			["title"=>"Описание", "name"=>"descr", "attrs"=>[], "type"=>"editor"],
			["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
			["title"=>"Добавить", "name"=>"add", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
		]);

		$admin = new AdminPage(
			array(
				"model" => $model_contests_groups,
				"item" => null,
				"action" => '/admin/app/congroups/add/',
				"fields" => $fields,
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
	}

	/********** КОНКУРСЫ **********/
	function action_contests($actions=null){
		$group_id = $actions[0];
		$model_contests_groups = new model_contests_groups();
		$model_contests = new model_contests();
		$model_prizes = new model_prizes();
		$group = $model_contests_groups->getItem($group_id);
		$this->view->headers['title'] = 'Конкурсы | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Группы конкурсов"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/', "Конкурсы"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/'.$group_id.'/contests/'];
		$this->view->data['header'] = "Конкурсы группы \"{$group['title']}\"";

		if (isset($_GET['loter']) and isset($_GET['id'])){
			$model_apiapps = new model_apiapps();
			$model_devicelog = $model_apiapps->_model_devicelog();
			$prizes = $model_prizes->getItemsWhere("(`owner` IS NULL OR `owner`=0 ) AND contest_id='{$_GET['id']}'");
			
			if(!empty($prizes))
			foreach($prizes as $prize){
				if($prize['user_base']=='`gorod24.online`.`gorod_devicelog`'){
					$contest = $model_contests->getItem($prize['contest_id']);
					$contest_cities = explode(';', $contest['cities']);
					foreach($contest_cities as $i=>$item){ if($item!=''){ $p[] = $item; } }
					$group_id = $prize['group_id'];
					$winner = $model_devicelog->get("uid")->where("`uid`!=1 AND `uid` IS NOT NULL AND '{$contest['start_date']}'<=`date` AND `date`<='{$contest['end_date']}' AND `uid` NOT IN (SELECT `owner` FROM `{$model_prizes->getdatabasename()}`.`{$model_prizes->gettablename()}` WHERE `owner` IS NOT NULL AND `group_id`='{$group_id}') AND `city_id` IN (".implode(',',$p).")")->group('uid')->order('RAND()')->limit(1)->commit('one');
					
					if(!empty($winner)){
						$model_prizes->Update([
							'owner' => $winner
						], $prize['id']);
					}
					
				}
			}
		}
		
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_contests->Delete($input);
			}
		}

		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model_contests->Update(['status'=>1], $input);
			}
		}

		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model_contests->Update(['status'=>0], $input);
			}
		}

		if(!$actions[2]){
			$admin = new AdminList(
				array(
					"action" => "/admin/app/congroups/{$group_id}/contests/",
					"model" => $model_contests,
					"where" => "group_id='{$group_id}'",
					"order" => "id DESC",
					"multiple" => "true",
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить новый конкурс", "href"=>"/admin/app/congroups/{$group_id}/contests/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"]
					],
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/'.$group_id.'/contests/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Название", "name"=>"title", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/prizes/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Описание", "name"=>"descr", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo strip_tags($cel);')],
						["title"=>"Состояние", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
						["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
							$model_contests = new model_contests();
							$contest = $model_contests->getItem($row["id"]);
							echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/'.$group_id.'/contests/{$row["id"]}/\">{$cel}<em class=\"fa fa-pencil fa-2\" title=\"Изменить\"></em></a>"; 
							echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/prizes/{$row["id"]}\">{$cel}<em class=\"fa fa-list fa-2\" title=\"Призы\"></em></a>";
							if($contest["start_date"]<=date("Y-m-d")){
							echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" data-confirm=\"Вы действительно хотите разыграть все призы конкурса?\" data-history=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/'.$group_id.'/contests/?id={$row["id"]}&loter=1\">{$cel}<em class=\"fa fa-gift fa-2\" title=\"Разыграть все призы немедленно\"></em></a>"; 
							}
						')],
					],
				)
			);
			$content = $admin;
		
		}
		else {
			if(is_numeric($actions[2])){
				$content = $this->action_contests_edit($actions);
			}
			elseif($actions[2]=='add'){
				$content = $this->action_contests_add($actions);
			}
		}
		
		return $content;
	}

	public function action_contests_edit($actions=null){
		$content = '';
		$group_id = $actions[0];
		$id = $actions[2];
		$model_contests_groups = new model_contests_groups();
		$model_contests = new model_contests();
		$group = $model_contests_groups->getItem($group_id);
		
		$contest = $model_contests->getItem($id);
		$this->view->headers['title'] = 'Конкурс '.$contest['title'].' | Администрирование Город 24';
		$this->view->data['header'] = 'Конкурс '.$contest['title'];
		$this->view->data['breadcrumbs'][$contest['title']] = '';

		$fields = [];
		
		$this->_model_cities = new model_cities();
		$contest_cities = explode(';', $contest['cities']);
		$p = [];
		foreach($contest_cities as $i=>$item){
			if($i!=0 and $i!=count($contest_cities)){
				$p[] = $item;
			}
		}
		$_cities = $this->_model_cities->getItemsWhere("`status`=1", "city_title ASC", null, null);

		foreach($_cities as $i=>$city){
			$fields[] = ["title"=>"Город \"{$city['city_title']}\"", "name"=>"city_id[{$city['city_id']}]", "attrs"=>[], "value"=>(in_array($city['city_id'], $p)?1:0), "type"=>"check"];
		}
		
		if(isset($_POST['save'])){$this->view->notRender();
			$cities = ';';
			foreach($_POST['city_id'] as $city=>$val){
				$cities .= $city.';';
			}
			$data = [
				"id"=>$this->varChek($_POST['id']),
				"cities"=>$cities,
				"title"=>$this->varChek($_POST['title']),
				"descr"=>$_POST['descr'],
				"start_date"=>$this->varChek($_POST['start_date']),
				"end_date"=>$this->varChek($_POST['end_date']),
				"news_id"=>(int)($_POST['news_id']),
				"news_base"=>$this->varChek($_POST['news_base']),
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
			];
			$model_contests->InsertUpdate($data);
			header('Location: /admin/app/congroups/'.$group_id.'/contests/');
		}

		$fields = array_merge($fields, [
			["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
			["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
			["title"=>"Описание", "name"=>"descr", "attrs"=>[], "type"=>"editor"],
			["content"=>"Временной промежуток с которого будут отбираться участники", "attrs"=>[], "type"=>"line"],
			["title"=>"Дата старта", "name"=>"start_date", "attrs"=>[], "type"=>"date"],
			["title"=>"Дата окончания", "name"=>"end_date", "attrs"=>[], "type"=>"date"],
			["title"=>"ID новости", "name"=>"news_id", "attrs"=>[], "type"=>"number"],
			["title"=>"База новостей", "name"=>"news_base", "attrs"=>[], "type"=>"select", "items"=>[ [ 'value'=>'base1', 'title'=>'Наши новости' ], [ 'value'=>'base2', 'title'=>'Другие' ]] ],
			["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
			["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
		]);

		$admin = new AdminPage(
			array(
				"model" => $model_contests,
				"item" => $contest,
				"action" => '/admin/app/congroups/'.$group_id.'/contests/'.$id.'/',
				"fields" => $fields,
			)
		);

		$content .= $admin;
		return $content;
	}

	function action_contests_add($actions=null){
		$content = '';
		$group_id = $actions[0];
		$model_contests_groups = new model_contests_groups();
		$model_contests = new model_contests();
		$group = $model_contests_groups->getItem($group_id);
		$this->view->headers['title'] = 'Добавить конкурс | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Группы конкурсов"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/', "Конкурсы"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/'.$group_id.'/contests/', "Добавить конкурс"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/contests/add/'];
		$this->view->data['header'] = "Добавить конкурс в группу \"{$group['title']}\"";

		$fields = [];
		$this->_model_cities = new model_cities();
		$contest_cities = explode(';', $contest['cities']);
		$p = [];
		foreach($contest_cities as $i=>$item){
			if($i!=0 and $i!=count($contest_cities)){
				$p[] = $item;
			}
		}
		$_cities = $this->_model_cities->getItemsWhere("`status`=1", "city_title ASC", null, null);

		foreach($_cities as $i=>$city){
			$fields[] = ["title"=>"Город \"{$city['city_title']}\"", "name"=>"city_id[{$city['city_id']}]", "attrs"=>[], "value"=>(in_array($city['city_id'], $p)?1:0), "type"=>"check"];
		}
		
		if(isset($_POST['add'])){$this->view->notRender();
			$cities = ';';
			foreach($_POST['city_id'] as $city=>$val){
				$cities .= $city.';';
			}
			$data = [
				"group_id"=>$group_id,
				"cities"=>$cities,
				"title"=>$this->varChek($_POST['title']),
				"descr"=>$_POST['descr'],
				"start_date"=>$this->varChek($_POST['start_date']),
				"end_date"=>$this->varChek($_POST['end_date']),
				"news_id"=>(int)$_POST['news_id'],
				"news_base"=>$this->varChek($_POST['news_base']),
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
			];
			$model_contests->InsertUpdate($data);

			header('Location: /admin/app/congroups/'.$group_id.'/contests/');
		}

		$fields = array_merge($fields, [
			["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
			["title"=>"Описание", "name"=>"descr", "attrs"=>[], "type"=>"editor"],
			["content"=>"Временной промежуток с которого будут отбираться участники", "attrs"=>[], "type"=>"line"],
			["title"=>"Дата старта", "name"=>"start_date", "attrs"=>[], "type"=>"date", "value"=>date('Y-m-d')],
			["title"=>"Дата окончания", "name"=>"end_date", "attrs"=>[], "type"=>"date", "value"=>date('Y-m-d')],
			["title"=>"ID новости", "name"=>"news_id", "attrs"=>[], "type"=>"number"],
			["title"=>"База новостей", "name"=>"news_base", "attrs"=>[], "type"=>"select", "items"=>[ [ 'value'=>'base1', 'title'=>'Наши новости' ], [ 'value'=>'base2', 'title'=>'Другие' ]] ],
			["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
			["title"=>"Добавить", "name"=>"add", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
		]);

		$admin = new AdminPage(
			array(
				"model" => $model_contests,
				"item" => null,
				"action" => '/admin/app/congroups/'.$group_id.'/contests/add/',
				"fields" => $fields,
			)
		);

		$content .= $admin;
		return $content;
	}

	/********** ПРИЗЫ **********/
	function action_prizes($actions=null){
		$model_contests_groups = new model_contests_groups();
		$model_contests = new model_contests();
		$model_prizes = new model_prizes();
		
		$id = (int)$actions[0];
		$contest = $model_contests->getItem($id);
		$group_id = $contest['group_id'];
		$group = $model_contests_groups->getItem($group_id);
		
		$this->view->headers['title'] = 'Приз конкурса "'.$contest['title'].'" | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Группы конкурсов"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/', "Конкурсы"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/'.$group_id.'/contests/', "Призы конкурса {$contest['title']}"=>$prize['contest_id']];
		$this->view->data['header'] = 'Приз конкурса "'.$contest['title'].'"';

		if (isset($_GET['loter']) and isset($_GET['id'])){
			$model_apiapps = new model_apiapps();
			$model_devicelog = $model_apiapps->_model_devicelog();
			echo "1"; exit;
			$prize = $model_prizes->getItem($_GET['id']);
				if($prize['user_base']=='`gorod24.online`.`gorod_devicelog`'){
					$contest = $model_contests->getItem($prize['contest_id']);
					$contest_cities = explode(';', $contest['cities']);
					foreach($contest_cities as $i=>$item){ if($item!=''){ $p[] = $item; } }
					$group_id = $prize['group_id'];
					$winner = $model_devicelog->get("uid")->where("`uid`!=1 AND `uid` IS NOT NULL AND '{$contest['start_date']}'<=`date` AND `date`<='{$contest['end_date']}' AND `uid` NOT IN (SELECT `owner` FROM `{$model_prizes->getdatabasename()}`.`{$model_prizes->gettablename()}` WHERE `owner` IS NOT NULL AND `group_id`='{$group_id}') AND `city_id` IN (".implode(',',$p).")")->group('uid')->order('RAND()')->limit(1)->commit('one');
					
					if(!empty($winner)){
						$model_prizes->Update([
							'owner' => $winner
						], $prize['id']);
					}
					
				}
		}
		if (isset($_GET['push']) and isset($_GET['id'])){
			$prize = $model_prizes->getItemWhere("id = '{$_GET['id']}'");
			$model_prizes->Update(['push_sends'=>$prize['push_sends'] + 1], $_GET['id']);
			
			if(!empty($contest['news_id'])){
				$url = 'https://gcm-http.googleapis.com/gcm/send';

				$data = array(
					'to' => "/topics/user-".$_GET['push'],
					'data' => [
						"title" => 'Поздравляем!',
						"message" => 'Вы выиграли '.$prize['title'],
						"id" => 123,
						"link" => "city24:news/{$contest['news_id']}/{$contest['news_base']}"
					]
				);
				$options = array(
					'http' => array(
						'header'  => "Authorization: key=AIzaSyBJzJojEluuaslC1IZ03v4nagl-xY3cmyk\r\nContent-Type: application/json\r\n",
						'method'  => 'POST',
						'content' => json_encode($data)
					)
				);

				$context  = stream_context_create($options);
				$push_result = file_get_contents($url, false, $context);
			}
		}

		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_prizes->Delete($input);
			}
		}

		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model_prizes->Update(['status'=>1], $input);
			}
		}

		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model_prizes->Update(['status'=>0], $input);
			}
		}

		if(!$actions){
			header('Location: /admin/app/contests/');
		}
		else {
			if(is_numeric($actions[0])){

				$admin = new AdminList(
					array(
						"action" => "/admin/app/prizes/{$actions[0]}",
						"model" => $model_prizes,
						"where" => "contest_id = ".(int)$actions[0],
						"limit" => 100,
						"order" => "unix_end ASC, id DESC",
						"multiple" => "true",
						"controls" => [
							["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
							["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
							["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
							["title"=>"Добавить новый приз", "href"=>"/admin/app/prizes/pole/add/{$actions[0]}", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"]
						],
						"attrs" => ["class"=>"table-adapt"],
						"columns" => [
							["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/prizes/pole/{$row["id"]}/\">{$cel}</a>";')],
							["title"=>"Название", "name"=>"title", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/prizes/pole/{$row["id"]}/\">{$cel}</a>";')],
							["title"=>"Описание", "name"=>"desc", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
							["title"=>"Дата розыгрыша", "name"=>"unix_end", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo date("Y-m-d H:i", $cel);')],
							["title"=>"Победитель", "name"=>"owner", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
								if(!empty($cel)){
									$feo_user=$GLOBALS["DB"]["80.93.183.242"]->getRow("SELECT * FROM new_feo_ua.accounts WHERE id=?i", $cel); 
									$feo_phones=$GLOBALS["DB"]["80.93.183.242"]->getAll("SELECT * FROM new_feo_ua.accounts_phones WHERE uid=?i AND on_off=\'1\'", $cel); 
									$feo_emails=$GLOBALS["DB"]["80.93.183.242"]->getAll("SELECT * FROM new_feo_ua.accounts_emails WHERE uid=?i AND on_off=\'1\'", $cel); 
									
									$vk=$GLOBALS["DB"]["80.93.183.242"]->getRow("SELECT * FROM new_feo_ua.oid_vk WHERE aid=?i", $cel); 
									$od=$GLOBALS["DB"]["80.93.183.242"]->getRow("SELECT * FROM new_feo_ua.oid_od WHERE aid=?i", $cel); 
									$fb=$GLOBALS["DB"]["80.93.183.242"]->getRow("SELECT * FROM new_feo_ua.oid_fb WHERE uid=?i", $cel); 
									
									$name ="{$feo_user["i_fam"]} {$feo_user["i_name"]}"; 
									echo "<p>id: {$cel}</p>";
									echo "<p>{$name}</p>";
									foreach($feo_phones as $phone){ if($phone["checked"]=="1"){$color="green";} else $color="red"; echo "<p style=\"color:{$color};\">{$phone["phone"]}</p>";}
									foreach($feo_emails as $phone){ if($phone["checked"]=="1"){$color="green";} else $color="red"; echo "<p style=\"color:{$color};\">{$phone["email"]}</p>";}
									if($vk){ echo "<p><a href=\"https://vk.com/id{$vk["soc_id"]}\" target=\"_blank\">vk</a></p>"; }
									if($od){ echo "<p><a href=\"https://ok.ru/profile/{$od["soc_id"]}\" target=\"_blank\">odnoklassniki</a></p>"; }
									if($fb){ echo "<p><a href=\"https://www.facebook.com/profile.php?id={$fb["soc_id"]}\" target=\"_blank\">facebook</a></p>"; }
								}
								')],
							["title"=>"Отдан", "name"=>"otdali", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Не отдали</span>"; break; case 1: echo "<span class=\"green\">Отдали</span>"; break; }')],
							["title"=>"Состояние", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
							["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
								echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/prizes/pole/{$row["id"]}/\">{$cel}<em class=\"fa fa-pencil fa-2\" title=\"Изменить\"></em></a>"; 
								if(!empty($row["owner"])){
									echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" data-confirm=\"Вы действительно хотите отправить push?\" data-history=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/prizes/'.$id.'/?id={$row["id"]}&push={$row["owner"]}\">{$cel}<span class=\"fa-stack fa-2x\"><em class=\"fa fa-mobile fa-stack-2x\" ></em><strong class=\"fa-stack-1x mobile-text\" style=\"color:red; font-size:12px;\" title=\"Отправить push (отправлено: {$row["push_sends"]} раз)\">{$row["push_sends"]}</strong></span></a>";
								}
								'.(($contest['start_date']<=date('Y-m-d'))?'
								else {
									echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" data-confirm=\"Вы действительно хотите разыграть приз?\" data-history=\"false\" data-history=\"false\" href=\"'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/prizes/'.$id.'/?id={$row["id"]}&loter=1\">{$cel}<em class=\"fa fa-gift fa-2\" title=\"Разыграть немедленно\"></em></a>"; 
								}':'').'
								
							')],
						],
					)
				);
				/**/
				$result .= $admin;
				$content = $result;
			}
		}
		$this->view->data['content'] = $content;
	}

	public function action_prizes_pole($actions=null){
		$content = '';
		
		$model_contests_groups = new model_contests_groups();
		$model_contests = new model_contests();
		$model_prizes = new model_prizes();
		
		$prize = $model_prizes->getItemWhere("id = ".(int)$actions[0]);
		
		$id = (int)$prize['contest_id'];
		$contest = $model_contests->getItem($id);
		$group_id = $contest['group_id'];
		$group = $model_contests_groups->getItem($group_id);
		
		$this->view->headers['title'] = 'Приз '.$contest['title'].' | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Группы конкурсов"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/', "Конкурсы"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/'.$group_id.'/contests/', "Призы конкурса {$contest['title']}"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/prizes/'.$prize['contest_id'], "Приз {$actions[0]}"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/prizes/pole/'.$actions[0]];
		$this->view->data['header'] = 'Приз "'.$contest['title'].'"';

		if(isset($_POST['save'])){$this->view->notRender();
			$owner = $this->varChek($_POST['owner']);
			$data = [
				"id"=>$this->varChek($_POST['id']),
				"title"=>$_POST['title'],
				"desc"=>$_POST['desc'],
				"who"=>$this->varChek($_POST['who']),
				"who_link"=>$this->varChek($_POST['who_link']),
				"cost"=>$this->varChek($_POST['cost']),
				"start_date"=>$this->varChek($_POST['start_date']),
				"start_time"=>$this->varChek($_POST['start_time']),
				"end_date"=>$this->varChek($_POST['end_date']),
				"end_time"=>$this->varChek($_POST['end_time']),
				"user_base"=>$this->varChek($_POST['user_base']),
				"owner"=>(empty($owner) ? null : $owner),
				"otdali"=>($this->varChek($_POST['otdali'])=='on'?1:0),
				"status"=>($this->varChek($_POST['status'])=='on'?1:0),
			];
			$model_prizes->InsertUpdate($data);
			header('Location: /admin/app/prizes/'.$prize['contest_id'].'/');
		}

		$admin = new AdminPage(
			array(
				"model" => $model_prizes,
				"item" => $prize,
				"action" => '/admin/app/prizes/pole/'.(int)$actions[0].'/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"Описание", "name"=>"desc", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Кто предоставил", "name"=>"who", "attrs"=>[], "type"=>"text"],
					["title"=>"Ссылка на сайт спонсора", "name"=>"who_link", "attrs"=>[], "type"=>"text"],
					["title"=>"Цена", "name"=>"cost", "attrs"=>[], "type"=>"number"],
					
					["content"=>"Дата и время старта розыгрыша призов. Одинаковые призы будут распределены равномерно между датой и временем старта и датой и временем окончания.", "attrs"=>[], "type"=>"line"],
					
					["title"=>"Дата старта", "name"=>"start_date", "attrs"=>[], "type"=>"date"],
					["title"=>"Время старта", "name"=>"start_time", "attrs"=>[], "type"=>"time"],
					
					["content"=>"Дата и время окончания розыгрыша призов. Если указать одинаковые начало и окончание розыгрыша, то все призы будут разыграны в одно время.", "attrs"=>[], "type"=>"line"],
					
					["title"=>"Дата окончания", "name"=>"end_date", "attrs"=>[], "type"=>"date"],
					["title"=>"Время окончания", "name"=>"end_time", "attrs"=>[], "type"=>"time"],
					
					["content"=>"База от куда брать претендентов на розыгрыш", "attrs"=>[], "type"=>"line"],
					
					["title"=>"База пользователя", "name"=>"user_base", "attrs"=>[], "type"=>"select", "items"=>[ [ 'value'=>'`gorod24.online`.`gorod_devicelog`', 'title'=>'Устройства пользователей' ] ]],
					["title"=>"Победитель", "name"=>"owner", "attrs"=>[], "type"=>"text"],
					["title"=>"Отдали", "name"=>"otdali", "attrs"=>[], "type"=>"switch"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
	}

	public function action_prizes_pole_add($actions=null){
		$content = '';
		$model_contests_groups = new model_contests_groups();
		$model_contests = new model_contests();
		$model_prizes = new model_prizes();
		
		$id = (int)$actions[0];
		$contest = $model_contests->getItem($id);
		$group_id = $contest['group_id'];
		$group = $model_contests_groups->getItem($group_id);
		
		$this->view->headers['title'] = 'Добавить приз | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Группы конкурсов"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/', "Конкурсы"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/congroups/'.$group_id.'/contests/', "Призы конкурса {$contest['title']}"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/prizes/'.$actions[0], "Добавить приз"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/prizes/pole/add/'.$actions[0]];
		$this->view->data['header'] = 'Добавить приз';

		if(isset($_POST['add'])){$this->view->notRender();

			$count = $_POST['prize_sum']; //кол-во призов

			$start_date = $_POST['start_date'];
			$end_date = $_POST['end_date'];
			$start_time = $_POST['start_time'];
			$end_time = $_POST['end_time'];

			$start_unix = strtotime($start_date.' '.$start_time);
			$end_unix = strtotime($end_date.' '.$end_time);
			$len_unix = $end_unix-$start_unix;
			$per_unix = $len_unix/$count;

			for ($i=0;$i<$count;$i++)
			{
				$s_u = $start_unix+$i*$per_unix;
				$s_d = date("Y-m-j",$s_u);
				$s_t = date("H:i:s",$s_u);
				$e_u = $s_u+$per_unix;
				$e_d = date("Y-m-j",$e_u);
				$e_t = date("H:i:s",$e_u);

				$data = [
					"group_id"=>$group_id,
					"contest_id"=>$this->varChek($_POST['contest_id']),
					"title"=>$this->varChek($_POST['title']),
					"desc"=>$this->varChek($_POST['desc']),
					"who"=>$this->varChek($_POST['who']),
					"who_link"=>$this->varChek($_POST['who_link']),
					"cost"=>$this->varChek($_POST['cost']),

					"start_date"=>$s_d,
					"start_time"=>$s_t,
					"end_date"=>$e_d,
					"end_time"=>$e_t,

					"unix_start"=>$s_u,
					"unix_end"=>$e_u,
					"unix_len"=>$per_unix,

					"user_base"=>$this->varChek($_POST['user_base']),
				];
				$model_prizes->InsertUpdate($data);

				header('Location: /admin/app/prizes/'.(int)$actions[0].'/');
			}
		}

		$admin = new AdminPage(
			array(
				"model" => $model_prizes,
				"item" => null,
				"action" => '/admin/app/prizes/pole/add/'.(int)$actions[0].'/',
				"fields" => [
					["title"=>"contest_id", "name"=>"contest_id", "attrs"=>[], "type"=>"hidden", "value"=>(int)$actions[0]],
					["title"=>"Название", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"Описание", "name"=>"desc", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Кто предоставил", "name"=>"who", "attrs"=>[], "type"=>"text"],
					["title"=>"Ссылка на сайт спонсора", "name"=>"who_link", "attrs"=>[], "type"=>"text"],
					["title"=>"Цена", "name"=>"cost", "attrs"=>[], "type"=>"number"],
					
					["content"=>"Дата и время старта розыгрыша призов. Одинаковые призы будут распределены равномерно между датой и временем старта и датой и временем окончания.", "attrs"=>[], "type"=>"line"],
					
					["title"=>"Дата старта", "name"=>"start_date", "attrs"=>[], "type"=>"date", "value"=>date('Y-m-d')],
					["title"=>"Время старта", "name"=>"start_time", "attrs"=>[], "type"=>"time", "value"=>date('H:i:s')],
					
					["content"=>"Дата и время окончания розыгрыша призов. Если указать одинаковые начало и окончание розыгрыша, то все призы будут разыграны в одно время.", "attrs"=>[], "type"=>"line"],
					
					["title"=>"Дата окончания", "name"=>"end_date", "attrs"=>[], "type"=>"date", "value"=>date('Y-m-d')],
					["title"=>"Время окончания", "name"=>"end_time", "attrs"=>[], "type"=>"time", "value"=>date('H:i:s')],
					
					["content"=>"База от куда брать претендентов на розыгрыш", "attrs"=>[], "type"=>"line"],
					
					["title"=>"База пользователя", "name"=>"user_base", "attrs"=>[], "type"=>"select", "items"=>[ [ 'value'=>'`gorod24.online`.`gorod_devicelog`', 'title'=>'Устройства пользователей' ] ]],
					["title"=>"Кол-во призов", "name"=>"prize_sum", "attrs"=>[], "type"=>"number", "value"=>1],
					["title"=>"Добавить", "name"=>"add", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
	}

	/********** СТАТИСТИКИ **********/
	public function action_stat_devices($actions=null){
		$this->view->headers['title'] = 'Статистика устройств | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Статистика"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/', "Устройства"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/devices/'];
		$this->view->data['header'] = 'Устройства';
		
		$model_devicelog = new model_devicelog();
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		if($_GET['from'] and $_GET['to']){
			$from  = $_GET['from'];
			$to  = $_GET['to'];
			$devices = $model_devicelog->db()->getAll("
				SELECT 
					`date` as `d_t`, 
					(SELECT COUNT(DISTINCT `imei`) FROM `gorod_devicelog` as `all`  WHERE `date` = `d_t` AND `os` != 'iOS') as `all_Android`,
					(SELECT COUNT(DISTINCT `imei`) FROM `gorod_devicelog` as `all`  WHERE `date` = `d_t` and `date` <= '{$to}' AND `os` = 'iOS') as `all_iOS`,
					(SELECT COUNT(DISTINCT `imei`) FROM `gorod_devicelog` where `date` = `d_t` AND `os` != 'iOS' and `imei` not in (SELECT `imei` FROM `gorod_devicelog` where `date` < `d_t`)) as `new_Android`,
					(SELECT COUNT(DISTINCT `imei`) FROM `gorod_devicelog` where `date` = `d_t` AND `date` <= '{$to}' AND `os` = 'iOS' and `imei` not in (SELECT `imei` FROM `gorod_devicelog` where `date` < `d_t`)) as `new_iOS`
				FROM 
					`gorod_devicelog` 
				where 
					`date` >= '{$from}' and `date` <= '{$to}' 
				group by `date`
					");
			$chart = ''; 
			$series = [
				[ 'name' => 'Всего Количество устройст (Android)', 'data'=>[] ],
				[ 'name' => 'Всего Количество устройст (iOS)', 'data'=>[] ],
				[ 'name' => 'Новые (Android)', 'data'=>[] ],
				[ 'name' => 'Новые (iOS)', 'data'=>[] ],
			];
			$xAxis = [];
			$rows = '';
			if(!empty($devices)){
				
				foreach($devices as $i=>$row){
					$sum =  $row['all_Android'] + $row['all_iOS'];
					$rows .= "<tr><td>{$row['d_t']}</td><td>{$sum}</td><td>{$row['all_Android']}</td><td>{$row['all_iOS']}</td><td>{$row['new_Android']}</td><td>{$row['new_iOS']}</td></tr>";
					$xAxis[] = $row['d_t'];
					$series[0]['data'][] = (int)$row['all_Android'];
					$series[1]['data'][] = (int)$row['all_iOS'];
					$series[2]['data'][] = (int)$row['new_Android'];
					$series[3]['data'][] = (int)$row['new_iOS'];
				}
				$chart = AdminPage::prepareJs("
				Highcharts.chart('container', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Устройства с {$from} по {$to}'
					},
					yAxis: {
						title: {
							text: 'Количество'
						}
					},
					xAxis: {
						categories: ".json_encode($xAxis, JSON_UNESCAPED_UNICODE)."
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle'
					},

					series: ".json_encode($series, JSON_UNESCAPED_UNICODE).",

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
							chartOptions: {
								legend: {
									layout: 'horizontal',
									align: 'center',
									verticalAlign: 'bottom'
								}
							}
						}]
					}

				});
				").'
					<div id="container"></div>
				';
				$log = '
						<table class="table-adapt">
						<thead>
							<tr>
								<th class="left-head"><p>Дата</p></th>
								<th data-breakpoints="xs sm"><p>Всего Количество устройст<br>(Android + iOS)</p></th>
								<th data-breakpoints="xs sm"><p>Всего Количество устройст<br>(Android)</p></th>
								<th data-breakpoints="xs sm"><p>Всего Количество устройст<br>(iOS)</p></th>
								<th data-breakpoints="xs"><p>Новые<br>(Android)</p></th>
								<th data-breakpoints="xs"><p>Новые<br>(iOS)</p></th>
							</tr>
						</thead>
						<tbody>	
							'.$rows.'
						</tbody>
						</table>
				';
			}
		
		}
		$content = '
					<form action="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/devices/" method="GET" class="sectright-filters-form">
						<div class="sectright-filters-form-label">
						'.AdminPage::dateField([ "title"=>"C", "name"=>"from", "class"=>"filter-input", "value"=>$from ], null).'
						'.AdminPage::dateField([ "title"=>"По", "name"=>"to", "class"=>"filter-input", "value"=>$to ], null).'
						</div>
						'.AdminPage::submitField([ "title"=>"Найти", "button-type"=>"success" ], null).'
					</form>
		';
		
		$this->view->data['content'] = $content . $chart . $log;
	}
	
	public function action_stat_login1000($actions=null){
		$this->view->headers['title'] = 'Статистика акционных пополнений | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Статистика"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/', "Пополнения за вход"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/login1000/'];
		$this->view->data['header'] = 'Пополнения за вход';
		
		$model_payment = new model_payment();
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		
		if($_GET['from'] and $_GET['to']){
			$from  = $_GET['from'];
			$to  = $_GET['to'];
			$devices = $GLOBALS['DB']['80.93.183.242']->getAll("
			SELECT 
				from_unixtime(`utx_add`, '%Y-%m-%d') as `date`,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE `service_descr`='Пополнение за вход в приложение' AND from_unixtime(t1.`utx_add`, '%Y-%m-%d')=from_unixtime(t2.`utx_add`, '%Y-%m-%d')  ) as c
			FROM `new_feo_ua`.`adv_payment_invoices` t1
			WHERE 
				from_unixtime(t1.`utx_add`, '%Y-%m-%d')>='{$from}' AND from_unixtime(t1.`utx_add`, '%Y-%m-%d')<='{$to}'
				AND `service_descr`='Пополнение за вход в приложение' GROUP BY `date` ORDER by `date` 
					");
			$chart = ''; 
			$series = [
				[ 'name' => 'Количество устройст', 'data'=>[] ],
			];
			$xAxis = [];
			$rows = '';
			if(!empty($devices)){
				
				foreach($devices as $i=>$row){
					$rows .= "<tr><td><a href=\"{$GLOBALS['CONFIG']['HTTP_HOST']}/admin/app/stat/login1000/detail/?date={$row['date']}\">{$row['date']}</a></td><td>{$row['c']}</td></tr>";
					$xAxis[] = $row['date'];
					$series[0]['data'][] = (int)$row['c'];
				}
				$chart = AdminPage::prepareJs("
				Highcharts.chart('container', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Пополнение на 1000р. за вход с {$from} по {$to}'
					},
					yAxis: {
						title: {
							text: 'Количество'
						}
					},
					xAxis: {
						categories: ".json_encode($xAxis, JSON_UNESCAPED_UNICODE)."
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle'
					},

					series: ".json_encode($series, JSON_UNESCAPED_UNICODE).",

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
							chartOptions: {
								legend: {
									layout: 'horizontal',
									align: 'center',
									verticalAlign: 'bottom'
								}
							}
						}]
					}

				});
				").'
					<div id="container"></div>
				';
				$log = '
						<table class="table-adapt">
						<thead>
							<tr>
								<th class="left-head"><p>Дата</p></th>
								<th data-breakpoints="xs sm"><p>Количество устройст (1000р.)</p></th>
							</tr>
						</thead>
						<tbody>	
							'.$rows.'
						</tbody>
						</table>
				';
			}
		
		}
		$content = '
					<form action="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/login1000/" method="GET" class="sectright-filters-form">
						<div class="sectright-filters-form-label">
						'.AdminPage::dateField([ "title"=>"C", "name"=>"from", "class"=>"filter-input", "value"=>$from ], null).'
						'.AdminPage::dateField([ "title"=>"По", "name"=>"to", "class"=>"filter-input", "value"=>$to ], null).'
						</div>
						'.AdminPage::submitField([ "title"=>"Найти", "button-type"=>"success" ], null).'
					</form>
		';
		
		$this->view->data['content'] = $content . $chart . $log;
	}
	
	public function action_stat_login1000_detail($actions=null){
		$this->view->headers['title'] = 'Статистика акционных пополнений | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Статистика"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/', "Пополнения за вход"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/login1000/'];
		$this->view->data['header'] = 'Пополнения за вход';
		
		$model_payment = new model_payment();
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		
		if($_GET['date']){
			$from  = $_GET['date'];
			$to  = $_GET['date'];
			$devices = $GLOBALS['DB']['80.93.183.242']->getAll("
			SELECT 
				*,
				(SELECT name FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `user_name`,
				(SELECT i_fam FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `i_fam`,
				(SELECT i_name FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `i_name`,
				(SELECT email FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `email`,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE from_unixtime(t2.`utx_add`, '%Y-%m-%d')='{$from}'AND (`service_descr` LIKE '%Пополнение за вход в приложение%') AND t2.uid=t1.uid) as c_oper,
				(SELECT SUM(price) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE from_unixtime(t2.`utx_add`, '%Y-%m-%d')='{$from}'AND (`service_descr` LIKE '%Пополнение за вход в приложение%') AND t2.uid=t1.uid) as c_summ
			FROM `new_feo_ua`.`adv_payment_invoices` t1
			WHERE 
				from_unixtime(t1.`utx_add`, '%Y-%m-%d')='{$from}'
				AND `service_descr`='Пополнение за вход в приложение' ORDER by `utx_add` 
					");
			$chart = ''; 
			$series = [
				[ 'name' => 'Количество устройст', 'data'=>[] ],
			];
			$xAxis = [];
			$rows = '';
			if(!empty($devices)){
				
				foreach($devices as $i=>$row){
					$n = $i + 1 ;
					$rows .= "<tr><td>{$n}</td><td>{$row['id']}</td><td>{$row['service_descr']}</td><td>{$row['user_name']} ({$row['i_fam']} {$row['i_name']} {$row['email']})</td><td>{$row['c_oper']}</td><td>{$row['c_summ']}</td></tr>";
				}
				$log = '
						<table class="table-adapt">
						<thead>
							<tr>
								<th><p>№</p></th>
								<th><p>id</p></th>
								<th><p>Описание</p></th>
								<th><p>Пользователь</p></th>
								<th><p>Операций</p></th>
								<th data-breakpoints="xs sm"><p>Сумма</p></th>
							</tr>
						</thead>
						<tbody>	
							'.$rows.'
						</tbody>
						</table>
				';
			}
		
		}
		$content = '
					<form action="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/login1000/" method="GET" class="sectright-filters-form">
						<div class="sectright-filters-form-label">
						'.AdminPage::dateField([ "title"=>"C", "name"=>"from", "class"=>"filter-input", "value"=>$from ], null).'
						'.AdminPage::dateField([ "title"=>"По", "name"=>"to", "class"=>"filter-input", "value"=>$to ], null).'
						</div>
						'.AdminPage::submitField([ "title"=>"Найти", "button-type"=>"success" ], null).'
					</form>
		';
		
		$this->view->data['content'] = $content . $chart . $log;
	}
	
	public function action_stat_invite($actions=null){
		$this->view->headers['title'] = 'Статистика пополнений по приглашениям | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Статистика"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/', "Пополнения за вход"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/invite/'];
		$this->view->data['header'] = 'Пополнений по приглашениям';
		
		$model_payment = new model_payment();
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		
		if($_GET['from'] and $_GET['to']){
			$from  = $_GET['from'];
			$to  = $_GET['to'];
			$devices = $GLOBALS['DB']['80.93.183.242']->getAll("
			SELECT 
				from_unixtime(`utx_add`, '%Y-%m-%d') as `date`,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE `service_descr` LIKE '%Скачивание по приглашению%' AND from_unixtime(t1.`utx_add`, '%Y-%m-%d')=from_unixtime(t2.`utx_add`, '%Y-%m-%d')  ) as c1,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE `service_descr` LIKE '%Активирован код приглашения%' AND from_unixtime(t1.`utx_add`, '%Y-%m-%d')=from_unixtime(t2.`utx_add`, '%Y-%m-%d')  ) as c2
			FROM `new_feo_ua`.`adv_payment_invoices` t1
			WHERE 
				from_unixtime(t1.`utx_add`, '%Y-%m-%d')>='{$from}' AND from_unixtime(t1.`utx_add`, '%Y-%m-%d')<='{$to}'
				AND (`service_descr` LIKE '%Скачивание по приглашению%' OR `service_descr` LIKE '%Активирован код приглашения%') GROUP BY `date` ORDER by `date` 
					");
			$chart = ''; 
			$series = [
				[ 'name' => 'Скачивание по приглашению', 'data'=>[] ],
				[ 'name' => 'Активирован код приглашения', 'data'=>[] ],
			];
			$xAxis = [];
			$rows = '';
			if(!empty($devices)){
				
				foreach($devices as $i=>$row){
					$rows .= "<tr><td><a href=\"{$GLOBALS['CONFIG']['HTTP_HOST']}/admin/app/stat/invite/detail/?date={$row['date']}\">{$row['date']}</a></td><td>{$row['c1']}</td><td>{$row['c2']}</td></tr>";
					$xAxis[] = $row['date'];
					$series[0]['data'][] = (int)$row['c1'];
					$series[1]['data'][] = (int)$row['c2'];
				}
				$chart = AdminPage::prepareJs("
				Highcharts.chart('container', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Пополнение на 1000р. за вход с {$from} по {$to}'
					},
					yAxis: {
						title: {
							text: 'Количество'
						}
					},
					xAxis: {
						categories: ".json_encode($xAxis, JSON_UNESCAPED_UNICODE)."
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle'
					},

					series: ".json_encode($series, JSON_UNESCAPED_UNICODE).",

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
							chartOptions: {
								legend: {
									layout: 'horizontal',
									align: 'center',
									verticalAlign: 'bottom'
								}
							}
						}]
					}

				});
				").'
					<div id="container"></div>
				';
				$log = '
						<table class="table-adapt">
						<thead>
							<tr>
								<th class="left-head"><p>Дата</p></th>
								<th data-breakpoints="xs sm"><p>Скачивание по приглашению (10р.)</p></th>
								<th data-breakpoints="xs sm"><p>Активирован код приглашения (500р.)</p></th>
							</tr>
						</thead>
						<tbody>	
							'.$rows.'
						</tbody>
						</table>
				';
			}
		
		}
		$content = '
					<form action="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/invite/" method="GET" class="sectright-filters-form">
						<div class="sectright-filters-form-label">
						'.AdminPage::dateField([ "title"=>"C", "name"=>"from", "class"=>"filter-input", "value"=>$from ], null).'
						'.AdminPage::dateField([ "title"=>"По", "name"=>"to", "class"=>"filter-input", "value"=>$to ], null).'
						</div>
						'.AdminPage::submitField([ "title"=>"Найти", "button-type"=>"success" ], null).'
					</form>
		';
		
		$this->view->data['content'] = $content . $chart . $log;
	}
	
	public function action_stat_invite_detail($actions=null){
		$this->view->headers['title'] = 'Статистика пополнений по приглашениям | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/', "Статистика"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/', "Пополнения за вход"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/invite/'];
		$this->view->data['header'] = 'Пополнений по приглашениям';
		
		$model_payment = new model_payment();
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		
		if($_GET['date']){
			$from  = $_GET['date'];
			$to  = $_GET['date'];
			$devices = $GLOBALS['DB']['80.93.183.242']->getAll("
			SELECT 
				*,
				(SELECT name FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `user_name`,
				(SELECT i_fam FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `i_fam`,
				(SELECT i_name FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `i_name`,
				(SELECT email FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `email`,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE from_unixtime(t2.`utx_add`, '%Y-%m-%d')='{$from}'AND (`service_descr` LIKE '%Скачивание по приглашению%') AND t2.uid=t1.uid) as c_oper_10,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE from_unixtime(t2.`utx_add`, '%Y-%m-%d')='{$from}'AND (`service_descr` LIKE '%Активирован код приглашения%') AND t2.uid=t1.uid) as c_oper_500,
				(SELECT SUM(price) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE from_unixtime(t2.`utx_add`, '%Y-%m-%d')='{$from}'AND (`service_descr` LIKE '%Скачивание по приглашению%' OR `service_descr` LIKE '%Активирован код приглашения%') AND t2.uid=t1.uid) as c_summ
			FROM `new_feo_ua`.`adv_payment_invoices` t1
			WHERE 
				from_unixtime(t1.`utx_add`, '%Y-%m-%d')='{$from}'
				AND (`service_descr` LIKE '%Скачивание по приглашению%' OR `service_descr` LIKE '%Активирован код приглашения%') 
			GROUP BY `uid`
			ORDER by `utx_add`
			");

			$rows = '';
			if(!empty($devices)){
				
				foreach($devices as $i=>$row){
					$rows .= "<tr><td>{$row['id']}</td><td>{$row['user_name']} ({$row['i_fam']} {$row['i_name']} {$row['email']})</td><td>{$row['c_oper_10']}</td><td>{$row['c_oper_500']}</td><td>{$row['c_summ']}</td></tr>";
				}
				$log = '
						<table class="table-adapt">
						<thead>
							<tr>
								<th><p>id</p></th>
								<th><p>Пользователь</p></th>
								<th data-breakpoints="xs sm"><p>Пополнений (Скачивание по приглашению 10р.)</p></th>
								<th data-breakpoints="xs sm"><p>Пополнений (Активирован код приглашения 500р.)</p></th>
								<th data-breakpoints="xs sm"><p>Сумма</p></th>
							</tr>
						</thead>
						<tbody>	
							'.$rows.'
						</tbody>
						</table>
				';
			}
		
		}
		$content = '
					<form action="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/app/stat/invite/" method="GET" class="sectright-filters-form">
						<div class="sectright-filters-form-label">
						'.AdminPage::dateField([ "title"=>"C", "name"=>"from", "class"=>"filter-input", "value"=>$from ], null).'
						'.AdminPage::dateField([ "title"=>"По", "name"=>"to", "class"=>"filter-input", "value"=>$to ], null).'
						</div>
						'.AdminPage::submitField([ "title"=>"Найти", "button-type"=>"success" ], null).'
					</form>
		';
		
		$this->view->data['content'] = $content . $chart . $log;
	}
	
}