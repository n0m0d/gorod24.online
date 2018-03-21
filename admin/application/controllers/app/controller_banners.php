<?php
require_once( __DIR__ ."/controller_index.php");
class controller_banners extends controller_index
{
	
	/********** БАННЕРЫ **********/
	function action_index($actions=null){
		$this->view->headers['title'] = 'Баннеры | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Баннеры"=>$this->url.'/banners/'];
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
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/banners/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Название", "name"=>"name", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'//banners/{$row["id"]}/\">{$cel}</a>";')],
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
				$content = $this->action_edit(((int)$actions[0]));
			}
		}
		$this->view->data['content'] = $content;
	}

	public function action_edit($id){
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

	function action_add($actions=null){
		$content = '';
		$model_banners = new model_banners();
		$this->view->headers['title'] = 'Добавить баннер | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Баннеры"=>$this->url.'/banners/', "Добавить баннер"=>$this->url.'/banners/add/'];
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

}