<?php
require_once( __DIR__ ."/controller_index.php");
class controller_grubnews extends controller_index
{
	function __construct(){
		$this->view->data['help'] = $GLOBALS['CONFIG']['HTTP_HOST'].'/support/admin/1/1/';
		parent::__construct();
	}
	
	/********** Сграбленные Новости **********/
	function action_index($actions=null){
		$this->view->headers['title'] = 'Сграбленные Новости | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Сграбленные Новости"=>$this->url.'/grubnews/'];
		$this->view->data['header'] = "Сграбленные Новости";
		
		$model_news_grab = new model_news_grab();
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_news_grab->Update([ 'status'=>0 ], $input);
			}
		}
		
		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model_news_grab->Update([ 'status'=>1 ], $input);
			}
		}
		
		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model_news_grab->Update([ 'status'=>3 ], $input);
			}
		}

		if(!$actions){
			$admin = new AdminList(
				array(
					"action" => "/admin/gorod/grubnews/",
					"model" => $model_news_grab,
					"where" => "`status`!=0",
					"order" => "`status` ASC, id DESC",
					"multiple" => "true",
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить новость", "href"=>"/admin/gorod/news/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
						["title"=>"Сайты", "href"=>"/admin/gorod/grubnews/sites/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'warning', "type"=>"link"],
					],
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/grubnews/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Фото", "name"=>"photo", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row', 'if(file_exists(APPDIR."/uploads/image/news_grub/{$row["id"]}-0.jpg")) {echo "<img src=\"/uploads/image/news_grub/{$row["id"]}-0.jpg\" width=\"120px\" align=left>";}')],
						["title"=>"Заголовок", "name"=>"head", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/grubnews/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Дата", "name"=>"date", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Источник", "name"=>"source_name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Состояние", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 1: echo "<span class=\"blue\">Ожидает правки</span>";break; case 2: echo "<span class=\"green\">Импортировано</span>"; break;case 3: echo "<span class=\"red\">Отключен</span>"; break; }')],
						["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
							echo "<a class=\"ajax-load icons-wrap\" href=\"'.$this->url.'/grubnews/import/{$row["id"]}\"><em class=\"fa fa-floppy-o fa-2\" title=\"Импортировать\"></em></a>";
						')]
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
	
	function action_sites($actions=null){
		$this->view->headers['title'] = 'Сайты для Сграбленных Новостей | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Сграбленные Новости"=>$this->url.'/grubnews/', "Сайты для Сграбленных Новостей"=>$this->url.'/grubnews/sites/'];
		$this->view->data['header'] = "Сайты для Сграбленных Новостей";
		
		$model_news_grab_settings = new model_news_grab_settings();
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_news_grab_settings->Delete($input);
			}
		}
		
		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model_news_grab_settings->Update([ 'status' => 1 ], $input);
			}
		}
		
		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model_news_grab_settings->Update([ 'status' => 0 ], $input);
			}
		}

		if(!$actions){
			$admin = new AdminList(
				array(
					"action" => "/admin/gorod/grubnews/sites/",
					"model" => $model_news_grab_settings,
					"order" => "`id` DESC",
					"multiple" => "true",
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить сайт", "href"=>"/admin/gorod/grubnews/sites/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"]
					],
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/grubnews/sites/edit/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Название", "name"=>"name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/grubnews/sites/edit/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Состояние", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Выключен</span>";break; case 1: echo "<span class=\"green\">Включен</span>"; break;case 3: echo "<span class=\"red\">Отключен</span>"; break; }')],
						["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
							echo "<a class=\"ajax-load icons-wrap\" href=\"'.$this->url.'/grubnews/sites/edit/{$row["id"]}\"><em class=\"fa fa-pencil fa-2\" title=\"Редактировать\"></em></a>";
						')],
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
		}
		else {
			if(is_numeric($actions[0])){
				$content = $this->action_sites_edit(((int)$actions[0]));
			}
		}
		$this->view->data['content'] = $content;
	}
	
	function action_sites_add($actions=null){
		$model_gorod_news = new model_gorod_news();
		$model_news_grab_settings = new model_news_grab_settings();
		$this->view->headers['title'] = 'Сграбленные Новости | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Сграбленные Новости"=>$this->url.'/grubnews/', "Сайты для Сграбленных Новостей"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/grubnews/sites/", "Создание сайта-источника новостей"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/grubnews/sites/add/"];
		$this->view->data['header'] = "Создание сайта-источника новостей";

		if(isset($_POST['save'])){$this->view->notRender();
			$data = [
				"domain"=>$_POST['domain'],
				"news"=>$_POST['news'],
				"city_id"=>$_POST['city_id'],
				"container"=>$_POST['container'],
				"container_head"=>$_POST['container_head'],
				"container_link"=>$_POST['container_link'],
				"name"=>$_POST['name'],
				"head"=>$_POST['head'],
				"body"=>$_POST['body'],
				"photos"=>$_POST['photos'],
				"photos_type"=>$_POST['photos_type'],
				"headers"=>$_POST['headers'],
				"status"=>($_POST['status']=='on'?1:0),
			];
			$model_news_grab_settings->InsertUpdate($data);
			header('Location: /admin/gorod/grubnews/sites/');
		}
		
		$admin = new AdminPage(
			array(
				"model" => $model_news_grab_settings,
				"action" => '/admin/gorod/grubnews/sites/add/',
				"fields" => [
					["title"=>"Заголовок", "name"=>"name", "attrs"=>[], "type"=>"text"],
					["title"=>"Домен", "name"=>"domain", "attrs"=>[], "type"=>"text"],
					["title"=>"Страница новостей (Домен + URL страницы со списком новостей)", "name"=>"news", "attrs"=>[], "type"=>"text"],
					["title"=>"Город", "name"=>"city_id", "attrs"=>[], "type"=>"select", "items" => $model_gorod_news->model_cities()->getItemsWhere("`in_news`=1", "`city_title` ASC", null, null, "`city_id` as `value`, `city_title` as `label`")],
					["title"=>"Селектор новостей в списке", "name"=>"container", "attrs"=>[], "type"=>"text"],
					["title"=>"Селектор шапки в списке", "name"=>"container_head", "attrs"=>[], "type"=>"text"],
					["title"=>"Селектор ссылки в списке", "name"=>"container_link", "attrs"=>[], "type"=>"text"],
					["title"=>"Селектор шапки новости", "name"=>"head", "attrs"=>[], "type"=>"text"],
					["title"=>"Селектор тела новости", "name"=>"body", "attrs"=>[], "type"=>"text"],
					["title"=>"Селектор фотографий новости", "name"=>"photos", "attrs"=>[], "type"=>"text"],
					["title"=>"Тип селектора фотографий новости", "name"=>"photos_type", "attrs"=>[], "type"=>"select", "items"=>[ ["value"=>0,"label"=>"Картинка"], ["value"=>1,"label"=>"Ссылка"]]],
					["title"=>"Заголовки", "name"=>"headers", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
		/**/
	}
	
	function action_sites_edit($actions=null){
		$id = $actions[0];
		$model_gorod_news = new model_gorod_news();
		$model_news_grab_settings = new model_news_grab_settings();
		$site = $model_news_grab_settings->getItem($id);
		$this->view->headers['title'] = 'Сграбленные Новости | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Сграбленные Новости"=>$this->url.'/grubnews/', "Сайты для Сграбленных Новостей"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/grubnews/sites/", "{$site['name']}"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/grubnews/sites/edit/{$id}/"];
		$this->view->data['header'] = "Настройка сайта - {$site['name']}";

		if(isset($_POST['save'])){$this->view->notRender();
			$data = [
				"id"=>$this->varChek($_POST['id']),
				"domain"=>$_POST['domain'],
				"news"=>$_POST['news'],
				"city_id"=>$_POST['city_id'],
				"container"=>$_POST['container'],
				"container_head"=>$_POST['container_head'],
				"container_link"=>$_POST['container_link'],
				"name"=>$_POST['name'],
				"head"=>$_POST['head'],
				"body"=>$_POST['body'],
				"photos"=>$_POST['photos'],
				"photos_type"=>$_POST['photos_type'],
				"headers"=>$_POST['headers'],
				"status"=>($_POST['status']=='on'?1:0),
			];
			$model_news_grab_settings->InsertUpdate($data);
			header('Location: /admin/gorod/grubnews/sites/');
		}
		
		$admin = new AdminPage(
			array(
				"model" => $model_news_grab_settings,
				"item" => $site,
				"action" => '/admin/gorod/grubnews/sites/edit/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Заголовок", "name"=>"name", "attrs"=>[], "type"=>"text"],
					["title"=>"Домен (URL страницы со списком новостей)", "name"=>"domain", "attrs"=>[], "type"=>"text"],
					["title"=>"Страница новостей (Домен + URL страницы со списком новостей)", "name"=>"news", "attrs"=>[], "type"=>"text"],
					["title"=>"Город", "name"=>"city_id", "attrs"=>[], "type"=>"select", "items" => $model_gorod_news->model_cities()->getItemsWhere("`in_news`=1", "`city_title` ASC", null, null, "`city_id` as `value`, `city_title` as `label`")],
					["title"=>"Селектор новостей в списке", "name"=>"container", "attrs"=>[], "type"=>"text"],
					["title"=>"Селектор шапки в списке", "name"=>"container_head", "attrs"=>[], "type"=>"text"],
					["title"=>"Селектор ссылки в списке", "name"=>"container_link", "attrs"=>[], "type"=>"text"],
					["title"=>"Селектор шапки новости", "name"=>"head", "attrs"=>[], "type"=>"text"],
					["title"=>"Селектор тела новости", "name"=>"body", "attrs"=>[], "type"=>"text"],
					["title"=>"Селектор фотографий новости", "name"=>"photos", "attrs"=>[], "type"=>"text"],
					["title"=>"Тип селектора фотографий новости", "name"=>"photos_type", "attrs"=>[], "type"=>"select", "items"=>[ ["value"=>0,"label"=>"Картинка"], ["value"=>1,"label"=>"Ссылка"]]],
					["title"=>"Заголовки", "name"=>"headers", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
		/**/
	}
	
	function action_edit($id){
		$model_news_grab = new model_news_grab();
		$model_news_grab_photos = new model_news_grab_photos();
		$new = $model_news_grab->getItem($id);
		$this->view->headers['title'] = 'Сграбленные Новости | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Сграбленные Новости"=>$this->url.'/grubnews/', "{$new['head']}"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/grubnews/{$id}/"];
		$this->view->data['header'] = "Сграбленные Новости - {$new['head']}";

		if(isset($_POST['save'])){$this->view->notRender();
			$data = [
				"id"=>$this->varChek($_POST['id']),
				"head"=>$_POST['head'],
				"body"=>$_POST['body'],
				"domain"=>$this->varChek($_POST['domain']),
				"source"=>$this->varChek($_POST['source']),
			];
			$model_news_grab->InsertUpdate($data);
			header('Location: /admin/gorod/grubnews/');
		}
		
		$photos = $model_news_grab_photos->getItemsWhere("`new_id`='{$id}'");
		$ptotos_html = '';
		$ptotos_html .= AdminPage::prepareJs('
			$(".photos-img-container img.insert-img").dblclick(function(e){
				var $alt = $(this).attr("alt");
				var $src = $(this).attr("src");
				tinymce.activeEditor.insertContent(\'<img alt="\' + $alt + \'" width="auto;" style="max-width:100%" height="auto" src="\' + $src + \'"/>\');
			});
		');
		
		$ptotos_html .= '
		<style>
		.photos-img-container { border: 1px solid transparent; width:200px; height:130px; overflow:hidden; float:left; margin:5px;}
		.photos-img-container:hover { border: 1px dotted grey;}
		.insert-img { width:100%; height:auto;}
		</style>
		<header>Фотографии (кликните дважды и фото добаится в текст):</header>
		<div class="row">
		';
		foreach($photos as $photo){
			$ptotos_html.='<div class="photos-img-container"><img class="insert-img" src="'.$photo['photo'].'" /></div>';
		}
		$ptotos_html.='</div>';
		
		$admin = new AdminPage(
			array(
				"model" => $model_news_grab,
				"item" => $new,
				"action" => '/admin/gorod/grubnews/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Заголовок", "name"=>"head", "attrs"=>[], "type"=>"text"],
					["title"=>"Фото",  "content"=>$ptotos_html, "type"=>"line"],
					["title"=>"Текст", "name"=>"body", "attrs"=>[], "type"=>"editor"],
					["title"=>"Источник", "name"=>"domain", "attrs"=>[], "type"=>"text"],
					["title"=>"Ссылка на оригинал", "name"=>"source", "attrs"=>[], "type"=>"text"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
		/**/
	}
	
	function action_import($actions=null){
		$id = (int) $actions[0];
		$model_gorod_news = new model_gorod_news();
		$model_news_grab = new model_news_grab();
		$model_news_grab_photos = new model_news_grab_photos();
		$model_uploads = new model_uploads();
		$model_gorod_photos = $model_gorod_news->model_gorod_photos();
		
		$new = $model_news_grab->getItem($id);
		$this->view->headers['title'] = 'Новости | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Сграбленные Новости"=>$this->url.'/grubnews/', "{$new['head']}"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/grubnews/{$id}/"];
		$this->view->data['header'] = "Сграбленные Новости с сайта ({$new['domain']}) - {$new['head']} <a href='{$new['source']}' target='_blank'>Источник</a>";
	
		if(isset($_POST['save'])){$this->view->notRender();
			$razd = $model_gorod_news->model_razd()->getItem($_POST['razd_id']);
			$name_razd = $razd['name_razd'];
			
			$cities = $model_gorod_news->model_cities()->getItem($_POST['city_id'][0]);
			$town = $cities['city_title'];
			
			$_tags = explode(',', $_POST['news_tag-input']); $tags = ';'; foreach($_tags as $tag){ $tags .= trim($tag).';'; }
			
			
			$data = [
				"news_head"=>$_POST['head'],
				"news_lid"=>$_POST['news_lid'],
				"news_body"=>$_POST['body'],
				"news_vrez"=>'',
				"news_author"=>$_POST['news_author'],
				"news_video"=>$_POST['news_video'],
				"news_video_you"=>$_POST['news_video_you'],
				"news_foto"=>'0.jpg',
				"news_foto_sm"=>'0.jpg',
				"big_open_foto"=>0,
				"news_foto_reportag"=>($_POST['news_foto_reportag']=='on'?1:0),
				"foto_all"=>($_POST['foto_all']=='on'?1:0),
				"news_podp"=>'',
				"news_num"=>'',
				"news_razd"=>$name_razd,
				"razd_id"=>(int)$_POST['razd_id'],
				"news_kto"=>$_POST['news_kto'],
				"news_tag"=>$tags,
				"town"=>$town,
				"country_id"=>(int)$_POST['country_id'],
				"region_id"=>(int)$_POST['region_id'],
				"city_id"=>(int)$_POST['city_id'][0],
				"news_key"=>'',
				"news_des"=>'',
				"look"=>'',
				"news_date"=>$_POST['news_date'],
				"news_up"=>$_POST['news_date'],
				"our"=>(int)$_POST['our'],
				"type"=>$this->varChek($_POST['type']),
				"lock_"=>0,
				"looks"=>0,
				"vk_"=>0,
				"vk_feo"=>0,
				"vk_feorf"=>0,
				"vk_g"=>0,
				"fb"=>0,
				"ok"=>0,
				"ot_name"=>$_POST['ot_name'],
				"ot_sylka"=>$_POST['ot_sylka'],
				"url"=>'',
				"url_ru"=>'',
				"kay_word"=>'',
				"id_pr"=>$_POST['id_pr'],
				"app_id"=>0,
				"akcia_id"=>0,
				"on_off"=>($_POST['on_off']=='on'?1:0),
				"news_lock"=>$_POST['news_lock'],
				"news_lock_for"=>$_POST['news_lock_for'],
				"show_comment"=>($_POST['show_comment']=='on'?1:0),
				"news_inter_id"=>$_POST['news_inter_id'],
				"news_album_id"=>$_POST['news_album_id'],
				"news_zamer_id"=>$_POST['news_zamer_id'],
				"news_panorama"=>$_POST['news_panorama'],
				"news_panorama_type"=>$_POST['news_panorama_type'],
				"show_in_app"=>($_POST['show_in_app']=='on'?1:0),
				"news_rating"=>0,
				"nead_stream"=>($_POST['nead_stream']=='on'?1:0),
			];
			
			
			$errors = [];
			if(empty($data['news_head'])){ $errors[]="Вы не ввели Заголовок"; }
			if(!empty($data['ot_name']) OR !empty($data['ot_sylka'])){
				if(empty($data['ot_name'])) { $errors[]="Вы не ввели Название источника"; }
				if(empty($data['ot_sylka'])) { $errors[]="Вы не ввели ссылку на источник"; }
			}
			//if(empty($data['news_lid'])){ $errors[]="Вы не ввели Подзоголовок"; }
			if(empty($data['news_body'])){ $errors[]="Вы не ввели Текст"; }
			if(count($_POST['city_id'])==0){ $errors[]="Вы не выбрали Город"; }
			if(empty($data['news_kto'])){ $errors[]="Вы не ввели Автора"; }
			
			if(count($errors)==0){
				$new_id = $model_gorod_news->Insert($data);
				$url_data = [
					'url_ru' => $new_id . '-'.$this->new_url_ru($data['news_head']),
					'url' => $new_id . '-'.$this->new_url($data['news_head'])
				];
				$model_gorod_news->Update($url_data, $new_id);
				
				$model_gorod_news->model_news_cities()->Delete("`new_id`='{$new_id}'");
				foreach($_POST['city_id'] as $city_id){
					$model_gorod_news->model_news_cities()->Insert(['new_id'=>$new_id, 'country_id'=>(int)$_POST['country_id'], 'region_id'=>(int)$_POST['region_id'], 'city_id'=>(int)$city_id, 'add_date'=>date("Y-m-d H:i:s")]);
				}
				$search = urlencode(trim($_POST['head']));
				$model_news_grab->Update([ 'status'=>2, 'confirm_id'=>$new_id ], $id);
				
				$photos = $model_news_grab_photos->getItemsWhere("`new_id`='{$id}'");
				
				foreach($photos as $photo){
					$file = APPDIR . $photo['photo'];
					if(file_exists($file)){
						//$content = 
						$orig_name = end(explode('/', $photo['photo']));
						$ext = getExtension1($orig_name);
						$size = filesize($file);
						$fileName = uniqid($new_id.'_').'.'.$ext;
						
						$upload_id = $model_uploads->Insert([
							'name' => $fileName,
							'original_name' => $orig_name,
							'ext' => $ext,
							'type' => 'image',
							'size' => $size,
							'destination' => '/uploads/image/news/',
							'author' => '0',
							'date' => date('Y-m-d H:i:s'),
							'modified' => date('Y-m-d H:i:s'),
							'status' => 1,
							'other' => '',
						]);
						
						@copy($file, APPDIR . "/uploads/image/news/{$fileName}");
						
						$pos = $model_gorod_photos->getCountWhere("`new_id`='{$new_id}'");
						
						$photo_id = $model_gorod_photos->Insert([
							'new_id' => $new_id,
							'img' => "/uploads/image/news/{$fileName}",
							'img_id' => $upload_id, 
							'description' => '', 
							'title' => $orig_name, 
							'pos' => $pos, 
							'status' => 1, 
							'descr_on' => 0, 
						]);
					}
				}
				
				header("Location: /admin/gorod/news/?search=$search");
			}
			else {
				$this->view->yesRender();
				foreach($errors as $error){
					$content .= "<h3 style='color:red;'>{$error}</h3>";
				}
				$new = $data;
			}
			$cities = $_POST['city_id'];
			$new['head'] = $data['news_head'];
			$new['body'] = $data['news_body'];
		}
		else {
			$cities = [];
			$cities[] = $new['city_id'];
			$new['foto_all'] = 1;
			$new['show_comment'] = 1;
			$new['show_in_app'] = 1;
			$new['nead_stream'] = 1;
			$new['news_date'] = date('Y-m-d H:i:s');
			$new['news_kto'] = $new['source_name'];
			$new['ot_name'] = $new['source_name'];
			$new['ot_sylka'] = $new['source'];
		}
		/**/
		$tags = [];
		$_tags = explode(';', $new['news_tag']);
		foreach($_tags as $tag){if(!empty($tag))$tags[]=$tag; };
		
		$model_gorod_pred = new model_gorod_pred();
		$firm = $model_gorod_pred->getItem($new['id_pr']);
		if($new['news_album_id'])
		$album = $GLOBALS['DB']['80.93.183.242']->getOne("SELECT `al_name` FROM new_feo_ua.feo_albums WHERE `al_id` = '{$new['news_album_id']}' LIMIT 1");
		if($new['news_inter_id'])
		$inter = $GLOBALS['DB']['80.93.183.242']->getOne("SELECT `inter_name` FROM new_feo_ua.feo_interviews WHERE `inter_id` = '{$new['news_inter_id']}' LIMIT 1");
		if($new['news_zamer_id'])
		$zamer = $GLOBALS['DB']['80.93.183.242']->getOne("SELECT `bas_name` FROM main.feo_basket WHERE `bas_id` = '{$new['news_zamer_id']}' LIMIT 1");
		if($new['news_panorama'])
		$panorama = $GLOBALS['DB']['80.93.183.242']->getOne("SELECT `title` FROM new_feo_ua.panorama_photos WHERE `id` = '{$new['news_panorama']}' LIMIT 1");
		
		$types =[
			[ "value"=>0, "Не указано"],
			[ "value"=>1, "Новость дня"],
			[ "value"=>2, "Новость часа"],
			[ "value"=>3, "Сенсация"],
			[ "value"=>4, "Интервью"]
		];
		
		$model_countries = new model_countries();
		$model_regions = new model_regions();
		
		$photos = $model_news_grab_photos->getItemsWhere("`new_id`='{$id}'");
		$ptotos_html = '';
		$ptotos_html .= AdminPage::prepareJs('
			$(".photos-img-container img.insert-img").dblclick(function(e){
				var $alt = $(this).attr("alt");
				var $src = $(this).attr("src");
				tinymce.activeEditor.insertContent(\'<img alt="\' + $alt + \'" width="auto;" style="max-width:100%" height="auto" src="\' + $src + \'"/>\');
			});
		');
		
		$ptotos_html .= '
		<style>
		.photos-img-container { border: 1px solid transparent; width:200px; height:130px; overflow:hidden; float:left; margin:5px;}
		.photos-img-container:hover { border: 1px dotted grey;}
		.insert-img { width:100%; height:auto;}
		</style>
		<header>Фотографии (кликните дважды и фото добаится в текст):</header>
		<div class="row">
		';
		foreach($photos as $photo){
			$ptotos_html.='<div class="photos-img-container"><img class="insert-img" src="'.$photo['photo'].'" alt="" /></div>';
		}
		$ptotos_html.='</div>';
		$admin = new AdminPage(
			array(
				"model" => $model_news_grab,
				"item" => $new,
				"action" => '/admin/gorod/grubnews/import/'.$id.'/',
				"fields" => [
					["title"=>"Заголовок", "name"=>"head", "attrs"=>[], "type"=>"text"],
					["title"=>"Подзаголовок", "name"=>"news_lid", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Фото",  "content"=>$ptotos_html, "type"=>"line"],
					["title"=>"Текст", "name"=>"body", "attrs"=>[], "type"=>"editor", "css"=>["https://gsp1.feomedia.ru/application/views/gorod24/libs/bootstrap/css/bootstrap.min.css", "https://gsp1.feomedia.ru/application/views/gorod24/libs/simple-line-icons/css/simple-line-icons.css", "https://gsp1.feomedia.ru/application/views/gorod24/libs/font-awesome/css/font-awesome.min.css", "https://gsp1.feomedia.ru/application/views/gorod24/libs/feorflogin/css/feorflogin.css","https://gsp1.feomedia.ru/application/views/gorod24/libs/swal/css/sweetalert.css", "https://gsp1.feomedia.ru/application/views/gorod24/css/main.min.css"]],
					//["title"=>"Врез", "name"=>"news_vrez", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"База новостей", "name"=>"our", "attrs"=>[], "type"=>"select", "items" => [[ "value"=>0, "Другие сайты"],[ "value"=>1, "Наши новости"]]],
					["title"=>"Тип новости", "name"=>"type", "attrs"=>[], "type"=>"select", "items" => $types],
					["title"=>"Раздел", "name"=>"razd_id", "attrs"=>[], "type"=>"select", "items" => $model_gorod_news->model_razd()->getItemsWhere("`on_off`=1", "`name_razd` ASC", null, null, "`id` as `value`, `name_razd` as `label`")],
					["title"=>"Страна", "name"=>"country_id", "attrs"=>[], "multiple"=>false, "type"=>"select", "items" => $model_countries->getItemsWhere("`in_news`=1", "`country_id` ASC", null, null, "`country_id` as `value`, `country_title` as `label`")],
					["title"=>"Регион", "name"=>"region_id", "attrs"=>[], "multiple"=>false, "type"=>"select", "items" => $model_regions->getItemsWhere("`in_news`=1", "`region_id` ASC", null, null, "`region_id` as `value`, `region_title` as `label`")],
					["title"=>"Город", "name"=>"city_id", "attrs"=>[], "multiple"=>true, "value"=>$cities, "type"=>"select", "items" => $model_gorod_news->model_cities()->getItemsWhere("`in_news`=1", "`city_title` ASC", null, null, "`city_id` as `value`, `city_title` as `label`")],
					["title"=>"Автор", "name"=>"news_kto", "attrs"=>[], "type"=>"text"],
					["title"=>"Автор новости (скрытый)", "name"=>"news_author", "attrs"=>[], "null"=> true, "type"=>"select", "items" => $model_gorod_news->_model_new_authors()->getItemsWhere("`on_off`=1", "`author_name` ASC", null, null, "`author_id` as `value`, `author_name` as `label`")],
					["title"=>"Фирма", "name"=>"id_pr", "attrs"=>[], "type"=>"autocomplete", "label"=>$firm['name'], "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/firms"],
					
					["title"=>"Видео", "name"=>"news_video", "attrs"=>[], "type"=>"text"],
					["title"=>"Видео (YOUTUBE)", "name"=>"news_video_you", "attrs"=>[], "type"=>"text"],
					
					["title"=>"Название источника", "name"=>"ot_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Ссылка на источник", "name"=>"ot_sylka", "attrs"=>[], "type"=>"text"],
					
					["title"=>"Дата и время публикации", "name"=>"news_date", "attrs"=>[], "type"=>"datetime"],
					
					["title"=>"Показывать как фоторепортаж", "name"=>"news_foto_reportag", "attrs"=>[], "type"=>"switch"],
					["title"=>"Показывать коментрарии", "name"=>"show_comment", "attrs"=>[], "type"=>"switch"],
					["title"=>"Фото и видео материалы", "name"=>"foto_all", "attrs"=>[], "type"=>"switch"],
					["title"=>"Показывать в приложении", "name"=>"show_in_app", "attrs"=>[], "type"=>"switch"],
					["title"=>"Нужна озвучка", "name"=>"nead_stream", "attrs"=>[], "type"=>"switch"],
					["title"=>"Теги к новости", "name"=>"news_tag", "attrs"=>[], "type"=>"autocomplete", "values"=>$tags, "label"=>implode(', ',$tags), "multiple"=>true, "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/tags"],
					["title"=>"Новсть закрыта/открыта", "name"=>"news_lock", "attrs"=>[], "type"=>"select", "items" => [
						[ "value"=>0, "По умолчанию"],
						[ "value"=>1, "Всегда открыта"],
						[ "value"=>2, "Закрыта"],
					]],
					["title"=>"Дата до которой новость закрыта", "name"=>"news_lock_for", "attrs"=>[], "type"=>"datetime"],
					
					["content"=>"<h2>Подключаемые модули</h2>", "attrs"=>[], "type"=>"line"],
					
					["title"=>"Альбом", "name"=>"news_album_id", "attrs"=>[], "type"=>"autocomplete", "label"=>$album,"source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/albums"],
					["title"=>"Опрос", "name"=>"news_inter_id", "attrs"=>[], "type"=>"autocomplete", "label"=>$inter, "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/inters"],
					["title"=>"Замер цен", "name"=>"news_zamer_id", "attrs"=>[], "type"=>"autocomplete", "label"=>$zamer, "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/zamers"],
					["title"=>"Панорамы", "name"=>"news_panorama", "attrs"=>[], "type"=>"autocomplete", "label"=>$panorama, "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/panoramas"],
					["title"=>"Вид панорамы", "name"=>"news_panorama_type", "attrs"=>[], "type"=>"select", "items" => [[ "value"=>0, "Как альбом"],[ "value"=>1, "Как одна панорама"]]],
					["title"=>"Включена/выключена", "name"=>"on_off", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
		/**/
	}
	
}
	