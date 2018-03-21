<?php
require_once( __DIR__ ."/controller_index.php");
class controller_basket extends controller_index
{
	function __construct(){
		parent::__construct();
	}
	
	function action_index($actions=null){
		$this->action_zamers($actions);
		
	}
	
	/********** Замеры по датам **********/
	function action_zamers($actions=null){
		$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Замеры по датам"=>$this->url.'/basket/zamers/'];
		$this->view->data['header'] = "Продовольственная корзина (Замеры по датам)";
		
		$model = new model_basket();
		$model_basket_items = $model->model_basket_items();
		
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model->Delete($input);
				$model_basket_items->Delete("`bitem_bas_id`='{$input}'");
			}
		}
		$where = "1";
		
		$admin = new AdminList(
			array(
				"model" => $model,
				"where" => $where,
				"order" => "bas_id DESC",
				"multiple" => "true",
				"action" => $this->url.'/basket/zamers/',
				"controls" => [
					["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
					["title"=>"Добавить", "href"=>$this->url."/basket/zamers/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
				],
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"ID", "name"=>"bas_id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Заголовок", "name"=>"bas_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Коментарий к сравнению", "name"=>"bas_comment", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Дата замера", "name"=>"bas_date", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
						echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/basket/zamers/edit/{$row["bas_id"]}\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
						echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/basket/zamers/items/{$row["bas_id"]}\"><em class=\"fa fa-list fa-2\" title=\"Список товаров/услуг с ценами\"></em></a>";
					')]
				],
			)
		);
		/**/
		$result .= $admin;
		$content = $result;
		$this->view->data['content'] = $content;
	}
	
	function action_zamers_items($actions=null){
		$id = (int)$actions[0];
		$model = new model_basket();
		$model_units = $model->model_units();
		$model_basket_items = $model->model_basket_items();
		$model_basket_types_items = $model->model_basket_types_items();
		$model_tovars = $model->model_tovars();
		$model_magazs = $model->model_magazs();
		$model_basket_types_magazs = $model->model_basket_types_magazs();
		if($id){
		$item = $model->getItem($id);
		$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Замеры по датам"=>$this->url.'/basket/zamers/', "Замер"=>$this->url.'/basket/zamers/edit/'.$id, "Цены"=>$this->url.'/basket/zamers/items/'.$id];
		$this->view->data['header'] = "Продовольственная корзина (Замеры по датам)";
		$GLOBALS['controller_basket']['units'] = $model_units->getItemsWhere("1", null, null, null, "`id` as `value`, `name` as `label`");
		
		if($_POST['save']){
			$bitem_id = $_POST['bitem_id'];
			
			foreach($bitem_id as $i=>$_id){
				$data = array(
					'bitem_bas_id' => $id,
					'bitem_tov_id' => $_POST['bitem_tov_id'][$i],
					'bitem_amount' => $_POST['bitem_amount'][$i],
					'bitem_coin' => $_POST['bitem_coin'][$i],
					'bitem_izm' => $_POST['bitem_izm'][$i],
					'bitem_mag_id' => $_POST['bitem_mag_id'][$i],
				);
				if(empty($data['bitem_coin'])) $data['bitem_coin'] = 0;
				if(!empty($_id)){
					$data['bitem_id'] = $_id;
				}
				$model_basket_items->InsertUpdate($data);
			}
			header("Location: {$this->url}/basket/zamers/");
		}
		
		$where = "1";
		$items = 
			$model
				->get("
					*, 
					(SELECT bitem_id FROM `{$model_basket_items->getdatabasename()}`.`{$model_basket_items->gettablename()}` as `items` WHERE `items`.`bitem_bas_id`={$id} AND `items`.`bitem_tov_id`=`{$model_tovars->gettablename()}`.`tov_id` AND `bitem_mag_id`=`{$model_basket_types_magazs->gettablename()}`.`mag_id`) as bitem_id
					, (SELECT bitem_amount FROM `{$model_basket_items->getdatabasename()}`.`{$model_basket_items->gettablename()}` as `items` WHERE `items`.`bitem_bas_id`={$id} AND `items`.`bitem_tov_id`=`{$model_tovars->gettablename()}`.`tov_id` AND `bitem_mag_id`=`{$model_basket_types_magazs->gettablename()}`.`mag_id`) as bitem_amount
					, (SELECT bitem_coin FROM `{$model_basket_items->getdatabasename()}`.`{$model_basket_items->gettablename()}` as `items` WHERE `items`.`bitem_bas_id`={$id} AND `items`.`bitem_tov_id`=`{$model_tovars->gettablename()}`.`tov_id` AND `bitem_mag_id`=`{$model_basket_types_magazs->gettablename()}`.`mag_id`) as bitem_coin
					, (SELECT bitem_izm FROM `{$model_basket_items->getdatabasename()}`.`{$model_basket_items->gettablename()}` as `items` WHERE `items`.`bitem_bas_id`={$id} AND `items`.`bitem_tov_id`=`{$model_tovars->gettablename()}`.`tov_id` AND `bitem_mag_id`=`{$model_basket_types_magazs->gettablename()}`.`mag_id`) as bitem_izm
					, (SELECT bitem_mag_id FROM `{$model_basket_items->getdatabasename()}`.`{$model_basket_items->gettablename()}` as `items` WHERE `items`.`bitem_bas_id`={$id} AND `items`.`bitem_tov_id`=`{$model_tovars->gettablename()}`.`tov_id` AND `bitem_mag_id`=`{$model_basket_types_magazs->gettablename()}`.`mag_id`) as bitem_mag_id
					, (SELECT mag_name FROM `{$model_magazs->getdatabasename()}`.`{$model_magazs->gettablename()}` as `magaz` WHERE `magaz`.`mag_id`=`{$model_basket_types_magazs->gettablename()}`.`mag_id`) as mag_name
				")
				->from([$model_basket_types_items, $model_tovars, $model_basket_types_magazs])
				->where("
					`{$model_basket_types_items->gettablename()}`.`item_tov_id` = `{$model_tovars->gettablename()}`.`tov_id`
					AND `{$model_basket_types_items->gettablename()}`.`item_bas_id` = `{$model_basket_types_magazs->gettablename()}`.`type_id`
					AND `item_bas_id` = '{$item['bas_type']}'")
				->order("mag_id")
				->commit();
			
		$admin = new AdminList(
			array(
				"items" => $items,
				"action" => $this->url.'/basket/zamers/items/'.$id,
				"form_method" => 'post',
				"controls" => [
					["title"=>"Сохранить", "name"=>"save", "value"=>"save", "attrs"=>[], "class"=>'', "button-type"=>'success', "type"=>"submit"],
				],
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"ID", "name"=>"bas_id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
						echo "<input type=\"hidden\" name=\"bitem_tov_id[]\" value=\"".$row["tov_id"]."\" />";
						echo "<input type=\"hidden\" name=\"bitem_id[]\" value=\"".$row["bitem_id"]."\" />";
						echo $row["item_id"];
					')],
					["title"=>"Сохранено", "name"=>"saved", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							if(!empty($row["bitem_id"])){
								echo "<div class=\"green\" style=\"border:1px solid black;background-color:green;width:20px;height:20px;margin:0 auto; \" title=\"Запись сохранена\"></div>";
							}
							else{
								echo "<div class=\"green\" style=\"border:1px solid black;background-color:red;width:20px;height:20px;margin:0 auto; \" title=\"Запись НЕ сохранена\"></div>";
							}
					')],
					["title"=>"Название", "name"=>"tov_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Количество/объем", "name"=>"amount", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							$value = $row["tov_def_amount"];
							$value = (!empty($row["bitem_amount"]))?$row["bitem_amount"]:$row["tov_def_amount"];
							echo "<input type=\"text\" name=\"bitem_amount[]\" value=\"".$value."\" style=\"width:100%;height:100%;box-sizing:border-box;\">";
					')],
					["title"=>"Цена", "name"=>"coin", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							$value = $row["tov_def_coin"];
							$value = (!empty($row["bitem_coin"]))?$row["bitem_coin"]:$row["tov_def_coin"];
							echo "<input type=\"text\" name=\"bitem_coin[]\" value=\"".$value."\" style=\"width:100%;height:100%;box-sizing:border-box;\">";
					')],
					["title"=>"Единица измерения", "name"=>"izm", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
						$value = (!empty($row["bitem_izm"]))?$row["bitem_izm"]:$row["tov_def_izm"];
						echo AdminPage::selectField(["compact"=>true, "name"=>"bitem_izm[]", "value"=>$value, "items"=>$GLOBALS["controller_basket"]["units"] ]);
					')],
					["title"=>"Магазин", "name"=>"mag_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
						echo "<input type=\"hidden\" name=\"bitem_mag_id[]\" value=\"".$row["mag_id"]."\" />";
						echo $cel;
					')],
				],
			)
		);
		/**/
		$result .= $admin;
		$content = $result;
		$this->view->data['content'] = $content;
		}
	}
	
	function action_zamers_add($actions=null){ $this->action_zamers_edit(); }
	
	function action_zamers_edit($actions=null){
		$id = (int)$actions[0];
		$model = new model_basket();
		$model_basket_types = $model->model_basket_types();
		if($id){
			$item = $model->getItem($id);
		
			$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Список замеров"=>$this->url.'/basket/zamers/', "Редактирование"=>$this->url.'/basket/zamers/edit/'];
			$this->view->data['header'] = "Редактирование единицы измерения";
			$action = "{$this->url}/basket/zamers/edit/{$id}";
		}
		else {
			$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Список замеров"=>$this->url.'/basket/zamers/', "Добавление"=>$this->url.'/basket/zamers/add/'];
			$this->view->data['header'] = "Добавление единицы измерения";
			$action = "{$this->url}/basket/zamers/add/";
			$item=[];
			$item['bas_date'] = date("Y-m-d");
		}
		if(isset($_REQUEST['save'])){
			$this->view->notRender();
			
			$data = [
				'bas_name' => $_REQUEST['bas_name'],
				'bas_comment' => $_REQUEST['bas_comment'],
				'bas_type' => $_REQUEST['bas_type'],
				'bas_date' => $_REQUEST['bas_date'],
				'bas_updatedate' => date("Y-m-d- H:i:s"),
			];
			if($id){
				$data['bas_id'] = $id;
			}
			else {
				$data['bas_adddate'] = $data['bas_updatedate'];
			}
			
			$new_id = $model->InsertUpdate($data);
			header("Location: {$this->url}/basket/zamers/");
		}
		
		$admin = new AdminPage(
			array(
				"model" => $model,
				"item" => $item,
				"action" => $action,
				"fields" => [
					["title"=>"Название", "name"=>"bas_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Коментарий", "name"=>"bas_comment", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Тип сравнения", "name"=>"bas_type", "attrs"=>[], "type"=>"select", "items"=>$model_basket_types->getItemsWhere("1", null, null, null, "`type_id` as `value`, `type_name` as `label`")],
					["title"=>"Дата", "name"=>"bas_date", "attrs"=>[], "value"=>$item['bas_date'], "type"=>"date"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	/********** Единицы измерения **********/
	function action_edizms($actions=null){
		$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Единицы измерения"=>$this->url.'/basket/edizms/'];
		$this->view->data['header'] = "Продовольственная корзина (Единицы измерения)";
		
		$model = new model_basket();
		$model = $model->model_units();
		
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model->Delete($input);
			}
		}
		$where = "1";
		
		$admin = new AdminList(
			array(
				"model" => $model,
				"where" => $where,
				"order" => "id DESC",
				"multiple" => "true",
				"action" => $this->url.'/basket/edizms/',
				"controls" => [
					["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
					["title"=>"Добавить", "href"=>$this->url."/basket/edizms/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
				],
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"ID", "name"=>"id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
						echo $cel;
					')],
					["title"=>"Заголовок", "name"=>"name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
						echo $cel;
					')],
					["title"=>"Для объявлений", "name"=>"in_adv", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
						switch($cel){
							case 0: echo "Выключено"; break;
							case 1: echo "Включено"; break;
						}
					')],
					["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
						echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/basket/edizms/edit/{$row["id"]}\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
					')]
				],
			)
		);
		/**/
		$result .= $admin;
		$content = $result;
		$this->view->data['content'] = $content;
	}
	
	function action_edizms_add($actions=null){ $this->action_edizms_edit(); }
	
	function action_edizms_edit($actions=null){
		$id = (int)$actions[0];
		$model = new model_basket();
		$model_units = $model->model_units();
		if($id){
			$item = $model_units->getItem($id);
		
			$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Список единиц измерения"=>$this->url.'/basket/edizms/', "Редактирование"=>$this->url.'/basket/edizms/edit/'];
			$this->view->data['header'] = "Редактирование единицы измерения";
			$action = "{$this->url}/basket/edizms/edit/{$id}";
		}
		else {
			$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Список единиц измерения"=>$this->url.'/basket/edizms/', "Добавление"=>$this->url.'/basket/edizms/add/'];
			$this->view->data['header'] = "Добавление единицы измерения";
			$action = "{$this->url}/basket/edizms/add/";
		}
		if(isset($_REQUEST['save'])){
			$this->view->notRender();
			
			$data = [
				'name' => $_REQUEST['name'],
				'in_adv' => ($_REQUEST['in_adv']=='on'?1:0),
			];
			if($id){
				$data['id'] = $id;
			}
			
			$new_id = $model_units->InsertUpdate($data);
			header("Location: {$this->url}/basket/edizms/");
		}
		
		$admin = new AdminPage(
			array(
				"model" => $model_units,
				"item" => $item,
				"action" => $action,
				"fields" => [
					["title"=>"Название", "name"=>"name", "attrs"=>[], "type"=>"text"],
					["title"=>"В объявлениях", "name"=>"in_adv", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	/********** Товары **********/
	function action_tovars($actions=null){
		$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Список товаров"=>$this->url.'/basket/tovars/'];
		$this->view->data['header'] = "Продовольственная корзина (Список товаров)";
		
		$model = new model_basket();
		$model_units = $model->model_units();
		$model_tovars = $model->model_tovars();
		
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_tovars->Delete($input);
			}
		}
		$where = "1";
		
		$admin = new AdminList(
			array(
				"model" => $model_tovars,
				"model_cols" => "*, (SELECT `name` FROM `{$model_units->getdatabasename()}`.`{$model_units->gettablename()}` as `unit` WHERE `unit`.`id`=`{$model_tovars->gettablename()}`.`tov_def_izm` ) `unit_name`",
				"where" => $where,
				"order" => "tov_id DESC",
				"multiple" => "true",
				"action" => $this->url.'/basket/tovars/',
				"controls" => [
					["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
					["title"=>"Добавить", "href"=>$this->url."/basket/tovars/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
				],
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"ID", "name"=>"tov_id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Заголовок", "name"=>"tov_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Описание", "name"=>"tov_descr", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Количество по умолчанию", "name"=>"tov_def_amount", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Цена по умолчанию", "name"=>"tov_def_coin", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Единица измерения по умолчанию", "name"=>"unit_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Дата редактирования", "name"=>"tov_updatedate", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
						echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/basket/tovars/edit/{$row["tov_id"]}\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
					')]
				],
			)
		);
		/**/
		$result .= $admin;
		$content = $result;
		$this->view->data['content'] = $content;
	}
	
	function action_tovars_add($actions=null){ $this->action_tovars_edit(); }
	
	function action_tovars_edit($actions=null){
		$id = (int)$actions[0];
		$model = new model_basket();
		$model_units = $model->model_units();
		$model_tovars = $model->model_tovars();
		if($id){
			$item = $model_tovars->getItem($id);
		
			$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Список товаров"=>$this->url.'/basket/tovars/', "Редактирование"=>$this->url.'/basket/tovars/edit/'];
			$this->view->data['header'] = "Редактирование товара";
			$action = "{$this->url}/basket/tovars/edit/{$id}";
		}
		else {
			$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Список товаров"=>$this->url.'/basket/tovars/', "Добавление"=>$this->url.'/basket/tovars/add/'];
			$this->view->data['header'] = "Добавление товара";
			$action = "{$this->url}/basket/tovars/add/";
		}
		if(isset($_REQUEST['save'])){
			$this->view->notRender();
			
			$data = [
				'tov_name' => $_REQUEST['tov_name'],
				'tov_descr' => $_REQUEST['tov_descr'],
				'tov_def_amount' => $_REQUEST['tov_def_amount'],
				'tov_def_coin' => $_REQUEST['tov_def_coin'],
				'tov_def_izm' => $_REQUEST['tov_def_izm'],
				'tov_updatedate' => date("Y-m-d H:i:s"),
			];
			if($id){
				$data['tov_id'] = $id;
			}
			else {
				$data['tov_adddate'] = $data['tov_updatedate'];
			}
			
			$new_id = $model_tovars->InsertUpdate($data);
			header("Location: {$this->url}/basket/tovars/");
		}
		
		$admin = new AdminPage(
			array(
				"model" => $model_tovars,
				"item" => $item,
				"action" => $action,
				"fields" => [
					["title"=>"Название", "name"=>"tov_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Описание", "name"=>"tov_descr", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Количество по умолчанию", "name"=>"tov_def_amount", "attrs"=>[], "type"=>"number"],
					["title"=>"Цена по умолчанию", "name"=>"tov_def_coin", "attrs"=>[], "type"=>"number"],
					["title"=>"Единица измерения по умолчанию", "name"=>"tov_def_izm", "attrs"=>[], "type"=>"select", "items"=>$model_units->getItemsWhere("1", null,null,null,"`id` as `value`, `name` as `label`")],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	/********** Магазины/точки **********/
	function action_magazs($actions=null){
		$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Список магазинов"=>$this->url.'/basket/magazs/'];
		$this->view->data['header'] = "Продовольственная корзина (Список магазинов)";
		
		$model = new model_basket();
		$model_units = $model->model_units();
		$model_magazs = $model->model_magazs();
		
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_magazs->Delete($input);
			}
		}
		$where = "1";
		
		$admin = new AdminList(
			array(
				"model" => $model_magazs,
				"model_cols" => "*",
				"where" => $where,
				"order" => "mag_id DESC",
				"multiple" => "true",
				"action" => $this->url.'/basket/magazs/',
				"controls" => [
					["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
					["title"=>"Добавить", "href"=>$this->url."/basket/magazs/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
				],
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"ID", "name"=>"mag_id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Заголовок", "name"=>"mag_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Описание", "name"=>"mag_descr", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Адрес", "name"=>"mag_addres", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Дата редактирования", "name"=>"mag_updatedate", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
						echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/basket/magazs/edit/{$row["mag_id"]}\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
					')]
				],
			)
		);
		/**/
		$result .= $admin;
		$content = $result;
		$this->view->data['content'] = $content;
	}
	
	function action_magazs_add($actions=null){ $this->action_magazs_edit(); }
	
	function action_magazs_edit($actions=null){
		$id = (int)$actions[0];
		$model = new model_basket();
		$model_feo_biznes = new model_feo_biznes();
		$model_buhg_firms = new model_buhg_firms();
		$model_magazs = $model->model_magazs();
		if($id){
			$item = $model_magazs->getItem($id);
		
			$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Список магазинов"=>$this->url.'/basket/magazs/', "Редактирование"=>$this->url.'/basket/magazs/edit/'];
			$this->view->data['header'] = "Редактирование магазина";
			$action = "{$this->url}/basket/magazs/edit/{$id}";
		}
		else {
			$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Список магазинов"=>$this->url.'/basket/magazs/', "Добавление"=>$this->url.'/basket/magazs/add/'];
			$this->view->data['header'] = "Добавление магазина";
			$action = "{$this->url}/basket/magazs/add/";
		}
		if(isset($_REQUEST['save'])){
			$this->view->notRender();
			
			$data = [
				'mag_name' => $_REQUEST['mag_name'],
				'mag_addres' => $_REQUEST['mag_addres'],
				'mag_descr' => $_REQUEST['mag_descr'],
				'mag_pid' => (int)$_REQUEST['mag_pid'],
				'mag_fid' => (int)$_REQUEST['mag_fid'],
				'mag_updatedate' => date("Y-m-d H:i:s"),
			];
			if($id){
				$data['mag_id'] = $id;
			}
			else {
				$data['mag_adddate'] = $data['mag_updatedate'];
			}
			
			$new_id = $model_magazs->InsertUpdate($data);
			header("Location: {$this->url}/basket/magazs/");
		}
		if($item['mag_pid']){ $pred = $model_feo_biznes->getItem($item['mag_pid']); }
		if($item['mag_fid']){ $firm = $model_buhg_firms->getItem($item['mag_fid']); }
		
		$admin = new AdminPage(
			array(
				"model" => $model_magazs,
				"item" => $item,
				"action" => $action,
				"fields" => [
					["title"=>"Название", "name"=>"mag_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Описание", "name"=>"mag_descr", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Адрес", "name"=>"mag_addres", "attrs"=>[], "type"=>"text"],
					["title"=>"Предприятие из каталога 21200", "name"=>"mag_pid", "attrs"=>[], "type"=>"autocomplete", "label"=>$pred['name'], "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/firms"],
					["title"=>"Фирма из бухгалтерии", "name"=>"mag_fid", "attrs"=>[], "type"=>"autocomplete", "label"=>$firm['name'], "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/buhg/firms"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	/********** Типы Замеров **********/
	function action_types($actions=null){
		$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Типы замеров цен"=>$this->url.'/basket/types/'];
		$this->view->data['header'] = "Продовольственная корзина (Типы замеров цен)";
		
		$model = new model_basket();
		$model_basket_types = $model->model_basket_types();
		$model_basket_types_magazs = $model->model_basket_types_magazs();
		
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_basket_types->Delete($input);
				$model_basket_types_magazs->Delete("`type_id`='{$input}'");
			}
		}
		$where = "1";
		
		$admin = new AdminList(
			array(
				"model" => $model_basket_types,
				"model_cols" => "*",
				"where" => $where,
				"order" => "type_id DESC",
				"multiple" => "true",
				"action" => $this->url.'/basket/types/',
				"controls" => [
					["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
					["title"=>"Добавить", "href"=>$this->url."/basket/types/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
				],
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"ID", "name"=>"type_id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Заголовок", "name"=>"type_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Описание", "name"=>"type_descr", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Дата редактирования", "name"=>"type_updatedate", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
						echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/basket/types/edit/{$row["type_id"]}\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
						echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/basket/types/items/{$row["type_id"]}\"><em class=\"fa fa-list fa-2\" title=\"Товары активные для типа замера\"></em></a>";
					')]
				],
			)
		);
		/**/
		$result .= $admin;
		$content = $result;
		$this->view->data['content'] = $content;
	}
	
	function action_types_add($actions=null){ $this->action_types_edit(); }
	
	function action_types_edit($actions=null){
		$id = (int)$actions[0];
		$model = new model_basket();
		$model_basket_types = $model->model_basket_types();
		$model_magazs = $model->model_magazs();
		$model_basket_types_magazs = $model->model_basket_types_magazs();
		$magazs = [];
		if($id){
			$item = $model_basket_types->getItem($id);
			$magazs = $model_basket_types_magazs->get("`mag_id`")->where("`type_id`='{$id}'")->commit('col');
		
			$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Типы замеров цен"=>$this->url.'/basket/types/', "Редактирование"=>$this->url.'/basket/types/edit/'];
			$this->view->data['header'] = "Редактирование типа замеров цен";
			$action = "{$this->url}/basket/types/edit/{$id}";
		}
		else {
			$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Типы замеров цен"=>$this->url.'/basket/types/', "Добавление"=>$this->url.'/basket/types/add/'];
			$this->view->data['header'] = "Добавление типа замеров цен";
			$action = "{$this->url}/basket/types/add/";
		}
		if(isset($_REQUEST['save'])){
			$this->view->notRender();
			
			$data = [
				'type_name' => $_REQUEST['type_name'],
				'type_descr' => $_REQUEST['type_descr'],
				'type_updatedate' => date("Y-m-d H:i:s"),
			];
			if($id){
				$data['type_id'] = $id;
			}
			else {
				$data['type_adddate'] = $data['type_updatedate'];
			}
			
			$new_id = $model_basket_types->InsertUpdate($data);
			if($new_id){
				$model_basket_types_magazs->Delete("`type_id`='{$new_id}'");
				foreach($_POST['magazs'] as $magaz){
					$model_basket_types_magazs->Insert([ 'type_id'=>$new_id, 'mag_id'=>$magaz ]);
				}
			}
			header("Location: {$this->url}/basket/types/");
		}

		
		$admin = new AdminPage(
			array(
				"model" => $model_basket_types,
				"item" => $item,
				"action" => $action,
				"fields" => [
					["title"=>"Название", "name"=>"type_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Описание", "name"=>"type_descr", "attrs"=>[], "type"=>"text"],
					["title"=>"Магазины/точки учавствующие в типе замера", "name"=>"magazs", "attrs"=>[], "type"=>"select", "value"=>$magazs, "multiple"=>true, "items" => $model_magazs->getItemsWhere("1", null, null, null,"`mag_id` as `value`, `mag_name` as `label`")],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	function action_types_items($actions=null){
		$id = (int)$actions[0];
		if($id){
		$this->view->headers['title'] = 'Продовольственная корзина | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Продовольственная корзина"=>$this->url.'/basket/', "Типы замеров цен"=>$this->url.'/basket/types/', "Тип замера"=>$this->url.'/basket/types/edit/'.$id,  "Товары активные для типа замера"=>$this->url.'/basket/types/items/'.$id, ];
		$this->view->data['header'] = "Продовольственная корзина (Товары активные для типа замера)";
		
		$model = new model_basket();
		$model_tovars = $model->model_tovars();
		$model_basket_types_items = $model->model_basket_types_items();
		$page = (int)$_REQUEST['page'];
		$page = ($page?$page:1);
		// Удаление элементов
		if (isset($_REQUEST['status'])){
			switch($_REQUEST['status']){
				case 'off': 
					if($_GET['id']){
						$model_basket_types_items->Delete($_GET['id']);
					}
					break;
				case 'on':
					if($_GET['tov_id'] and $id){
						$model_basket_types_items->Insert(['item_bas_id'=>$id, 'item_tov_id'=>$_GET['tov_id']]);
					}
					break;
			}
		}
		$where = "1";
		
		$admin = new AdminList(
			array(
				"model" => $model_tovars,
				"model_cols" => "*,
						(SELECT COUNT(*) FROM `{$model_basket_types_items->getdatabasename()}`.`{$model_basket_types_items->gettablename()}` WHERE `item_tov_id`=`{$model_tovars->gettablename()}`.`tov_id` AND `item_bas_id`=$id) as `selected`,
						(SELECT item_id FROM `{$model_basket_types_items->getdatabasename()}`.`{$model_basket_types_items->gettablename()}` WHERE `item_tov_id`=`{$model_tovars->gettablename()}`.`tov_id` AND `item_bas_id`=$id) as `item_id`
						",
				"where" => $where,
				"order" => "tov_id DESC",
				//"multiple" => "true",
				"action" => $this->url.'/basket/types/items/'.$id.'/?page='.$page,
				"controls" => [
					["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
					["title"=>"Добавить", "href"=>$this->url."/basket/types/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
				],
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"ID", "name"=>"tov_id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Заголовок", "name"=>"tov_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Описание", "name"=>"tov_descr", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
						if($row["selected"]){
							echo "<span class=\"green\">Включен</span>"; echo "<br><a class=\"ajax-load icons-wrap\" data-center=\"false\" data-history=\"false\"  href=\"'.$this->url.'/basket/types/items/'.$id.'/?status=off&id={$row["item_id"]}&page='.$page.'\"><em class=\"green fa fa-toggle-on fa-2\" title=\"Выключить\"></em></a>";
						}
						else {
							echo "<span class=\"red\">Отключен</span>"; echo "<br><a class=\"ajax-load icons-wrap\" data-center=\"false\" data-history=\"false\"  href=\"'.$this->url.'/basket/types/items/'.$id.'/?status=on&tov_id={$row["tov_id"]}&page='.$page.'\"><em class=\"red fa fa-toggle-off fa-2\" title=\"Включить\"></em></a>";
						}
					')]
				],
			)
		);
		/**/
		$result .= $admin;
		$content = $result;
		$this->view->data['content'] = $content;
		}
	}
	
	
}