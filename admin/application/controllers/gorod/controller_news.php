<?php
require_once( __DIR__ ."/controller_index.php");
class controller_news extends controller_index
{
	function __construct(){
		parent::__construct();
		$this->view->data['help'] = $GLOBALS['CONFIG']['HTTP_HOST'].'/support/admin/1/1/';
	}
	
	/********** Новости на ФЕО.РФ **********/
	function action_index($actions=null){
		$this->view->headers['title'] = 'Новости | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/'];
		$this->view->data['header'] = "Новости";
		
		$model_gorod_news = new model_gorod_news();
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_gorod_news->Delete($input);
				$photos = $model_gorod_news->model_gorod_photos()->getItemsWhere("`new_id`='{$input}'");
				foreach($photos as $photo){
					$file = APPDIR . $photo['img'];
					if(file_exists($file)){ @unlink($file); }
				}
				$model_gorod_news->model_gorod_photos()->Delete("`new_id`='{$input}'");
				$model_gorod_news->model_news_cities()->Delete("`new_id`='{$input}'");
			}
		}
		
		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model_gorod_news->Update([ 'on_off'=>'1' ], $input);
			}
		}
		
		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model_gorod_news->Update([ 'on_off'=>'0' ], $input);
			}
		}
		$where = "1";
		if($_GET['search']){
			$search = trim($_GET['search']);
			if($_GET['like']){
			$where .= "
				AND ( 
					`news_head` LIKE '%{$search}%'
					OR `news_lid` LIKE '%{$search}%'
					OR `news_body` LIKE '%{$search}%'
					)
			";
			$checked = true;
			}
			else {
			$where .= "
				AND (MATCH (news_head, news_lid, news_body) AGAINST ('{$search}') > 0)
			";
			}
		}
		if($_GET['city_id']){
			$where .= "
				AND ( (SELECT COUNT(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` IN (".implode(', ',$_GET['city_id']).")) > 0 )
			";
		}
		
		if (isset($_GET['trashphotos']) and isset($_GET['id'])){
			$thrumbs_directory = APPDIR . "/uploads/image/news_thrumbs/{$_GET['id']}/";
			if(is_dir($thrumbs_directory)){
				$dir = scandir($thrumbs_directory);
				foreach($dir as $name) {
					if(is_file($thrumbs_directory.$name) == TRUE) {
						unlink($thrumbs_directory.$name);
					}
				}
			}
		}
		
		if (isset($_GET['main']) and isset($_GET['id'])){
			$new = $model_gorod_news->getItem($_GET['id']);
			switch($_GET['main']){
				case 'on': 
					$rotate_data = ['new_id'=>$_GET['id']];
					if($new['news_id']){
						$rotate_data['n_id'] = $new['news_id'];
						$rotate_data['n_o'] = $new['our'];
					}
					$model_gorod_news->model_gorod_news_rotate()->Insert($rotate_data);
					break;
				case 'off': 
					$main_where_del = false;
					if($new['news_id']){
						$main_where_del = "`n_id`='{$new['news_id']}' AND `n_o`='{$new['our']}'";
					}
					else {
						$main_where_del = "`new_id`='{$new['id']}'";
					}
					if($main_where_del)
					$model_gorod_news->model_gorod_news_rotate()->Delete($main_where_del);
					break;
			}
		}		
		
		if (isset($_GET['status']) and isset($_GET['id'])){
			switch($_GET['status']){
				case 'on': 
					$data = ['on_off'=>1];
					$model_gorod_news->Update($data, $_GET['id']);
					break;
				case 'off': 
					$data = ['on_off'=>0];
					$model_gorod_news->Update($data, $_GET['id']);
					break;
			}
		}		

		if(!$actions){
			$admin = '';
		$admin .= 
		''
		.AdminPage::prepareJs("
		$('.in_gazeta').on('click', function(e){
				e.preventDefault();
				swal({
					html: '<div id=\"in_gazeta-content\"><p>Начните вводить номер или дату выхода газеты:</p><input type=\"hidden\" value=\"\" id=\"in_gazeta-value\" /><input type=\"text\" id=\"in_gazeta-search\"  class=\"autocomplete\"/><div style=\"height:200px;overflow:auto;\" id=\"autocomplete-results\"></div></div>',
					showCloseButton: true,
					showCancelButton: true,
					showConfirmButton: false,
					cancelButtonText: 'Закрыть',
					cancelButtonAriaLabel: 'Закрыть',
					width: 'auto',
					onOpen: function () {
						console.log('onOpen');
						console.log($( '#in_gazeta-search' ));
						$( '#in_gazeta-search' ).autocomplete({
							source:  \"{$GLOBALS['CONFIG']['HTTP_HOST']}/admin/gorod/json/gazeta\",
							minLength: 2,
							appendTo: \"#autocomplete-results\",
							focus: function() {
								// prevent value inserted on focus
								return false;
							},
							select: function( event, ui ) {
								console.log( \"Selected: \" + ui.item.id + \" label: \" + ui.item.value );
								$(\"#in_gazeta-value\").val(ui.item.id);
								window.location.href=\"/admin/gorod/news/ingazeta/\"+ui.item.id;
							}
						});
					}
				});
				
			});
		");
			$admin .= new AdminList(
				array(
					"model" => $model_gorod_news,
					"model_cols" => "*, (SELECT COUNT(*) FROM `gorod_push` WHERE `gorod_push`.`new_id`=`gorod_news`.`id`) as `pushs`, (SELECT COUNT(*) FROM `gorod_news_audio_streams` WHERE `gorod_news_audio_streams`.`new_id`=`gorod_news`.`id`) as `audios`",
					"where" => $where,
					"order" => "id DESC",
					"multiple" => "true",
					"action" => '/admin/gorod/news/',
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить новость", "href"=>"/admin/gorod/news/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
						["title"=>"Поднятия новостей", "href"=>"/admin/gorod/news/ups/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'warning', "type"=>"link"],
						["title"=>"Новости в газете", "name"=>"in_gazeta", "attrs"=>[], "class"=>'in_gazeta', "button-type"=>'success', "type"=>"button"],
						["title"=>"Города", "name"=>"city_id", "attrs"=>[], "type"=>"select", "width"=>"25%", "value"=>$_GET['city_id'], "compact"=>true, "multiple"=>true, "type"=>"select", "items" => $model_gorod_news->model_cities()->getItemsWhere("`in_news`=1", "`city_title` ASC", null, null, "`city_id` as `value`, `city_title` as `label`")],
						["title"=>"Поиск", "name"=>"search", "attrs"=>[], "type"=>"search", "value"=>$search],
						["title"=>"Поиск по точной фразе", "name"=>"like", "attrs"=>[], "type"=>"check", "checked"=>$checked, "value"=>"on"],
					],
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						
						["title"=>"Фото", "name"=>"news_head", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							//$ver = uniqid();
							echo "<a href=\"{$GLOBALS[\'CONFIG\'][\'HTTP_HOST\']}/thrumbs/news/new_{$row["id"]}_1000_0.jpg?ver={$ver}\" data-fancybox=\"gallery-{$row["id"]}\"><img src=\"{$GLOBALS[\'CONFIG\'][\'HTTP_HOST\']}/thrumbs/news/new_{$row["id"]}_361_240.jpg?ver={$ver}\" width=\"120px\" align=left></a>";
							echo "<br><a class=\"ajax-load icons-wrap size-small\" href=\"'.$this->url.'/news/eskizs/{$row["id"]}\">Эскизы</a>";
							return true;
							echo "<br><a class=\"ajax-load icons-wrap size-small\" href=\"'.$this->url.'/news/mainphoto/{$row["id"]}/552/225?title=Эскиз для прокрутки\">Для прокрутки (552x225)</a>";
							echo "<br><a class=\"ajax-load icons-wrap size-small\" href=\"'.$this->url.'/news/mainphoto/{$row["id"]}/361/240?title=Эскиз для главной страницы новости\">Главное (361x240)</a>";
							echo "<br><a class=\"ajax-load icons-wrap size-small\" href=\"'.$this->url.'/news/mainphoto/{$row["id"]}/234/158?title=Эскиз для главной страницы сайта\">Для главной (234x158)</a>";
							echo "<br><a class=\"ajax-load icons-wrap size-small\" href=\"'.$this->url.'/news/mainphoto/{$row["id"]}/640/320?title=Эскиз для соц сетей\">Для соц.сетей (640x320)</a>";
						')],
						/*
						["title"=>"Превью", "name"=>"news_head", "attrs"=>[ "data-breakpoints"=>"xs sm", "style"=>"width:150px;" ], "content"=>create_function('$cel,$row','
							echo "<br><a class=\"ajax-load icons-wrap size-small\" href=\"'.$this->url.'/news/mainphoto/{$row["id"]}/361/240?title=Эскиз для главной страницы новости\">Главное (361x240)</a>";
							echo "<br><a class=\"ajax-load icons-wrap size-small\" href=\"'.$this->url.'/news/mainphoto/{$row["id"]}/234/158?title=Эскиз для главной страницы сайта\">Для главной (234x158)</a>";
							echo "<br><a class=\"ajax-load icons-wrap size-small\" href=\"'.$this->url.'/news/mainphoto/{$row["id"]}/640/320?title=Эскиз для соц сетей\">Для соц.сетей (640x320)</a>";
						')],
						*/
						["title"=>"Главное", "name"=>"main", "attrs"=>["data-breakpoints"=>"xs sm"], "content"=>create_function('$cel,$row','
							$model_gorod_news = new model_gorod_news();
							$model_gorod_news_rotate = $model_gorod_news->model_gorod_news_rotate();
							$main = 0;
							if($row["news_id"]){
								$main = $model_gorod_news_rotate->getCountWhere("`n_id`={$row["news_id"]} AND `n_o`={$row["our"]}");
							}
							else {
								$main = $model_gorod_news_rotate->getCountWhere("`new_id`={$row["id"]}");
							}
							if($main==0){
								echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" data-confirm=\"Вы действительно хотите сделать новость главной?\" data-history=\"false\" href=\"'.$this->url.'/news/?main=on&id={$row["id"]}&search='.$_GET['search'].'&like='.$_GET['like'].'\"><em class=\"fa fa-check-circle-o fa-2\"></em></a>";
							}
							else {
								echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" data-confirm=\"Вы действительно хотите убрать новость из главных?\" data-history=\"false\" href=\"'.$this->url.'/news/?main=off&id={$row["id"]}&search='.$_GET['search'].'&like='.$_GET['like'].'\"><em class=\"green fa fa-check-circle-o fa-2\"></em></a>";
							}
						')],
						["title"=>"Заголовок", "name"=>"news_head", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo "<div style=\"text-align:left;\">(ID: <a href=\"https://фео.рф/новости/{$row["url_ru"]}\" target=\"_blank\">{$row["id"]}</a>) <a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/news/{$row["id"]}/\">{$cel}</a></div>";
							$un = uniqid("new_");
							echo "<div id=\"{$un}\" class=\"hidden new-text-details\">
								<h2 style=\"text-align:left;font-weight:bold;\">{$row["news_head"]}</h2>
								<p style=\"text-align:left;margin:15px 0px;\">Подзаголовок: {$row["news_lid"]}</p>
								<p style=\"text-align:left;font-weight:bold;\">Текст:</p>
								<div style=\"text-align:left;\">{$row["news_body"]}</div>
							</div>";
							echo "<div style=\"text-align:right;\"><a href=\"#\" class=\"show-new-more-{$un}\" data-id=\"{$un}\">Полный текст</a></div>";
								echo AdminPage::prepareJs("
									$(\'.show-new-more-{$un}\').on(\'click\', function(e){
										e.preventDefault();
										var id = $(this).data(\'id\');
										swal({
											html: $(\'#\'+id).html(),
											showCloseButton: true,
											showCancelButton: true,
											showConfirmButton: false,
											cancelButtonText: \'Закрыть\',
											cancelButtonAriaLabel: \'Закрыть\',
											width: \'850px\'
										});
										
									});
								");
						')],
						/*
						["title"=>"Краткое описание", "name"=>"news_lid", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
							$un = uniqid("new_");
							echo "<div id=\"{$un}\" class=\"hidden new-text-details\">
								<h2 style=\"text-align:left;font-weight:bold;\">{$row["news_head"]}</h2>
								<p style=\"text-align:left;margin:15px 0px;\">Подзаголовок: {$row["news_lid"]}</p>
								<p style=\"text-align:left;font-weight:bold;\">Текст:</p>
								<div style=\"text-align:left;\">{$row["news_body"]}</div>
							</div>";
							echo "<div style=\"text-align:right;\"><a href=\"#\" class=\"show-new-more-{$un}\" data-id=\"{$un}\">Подробнее...</a></div>";
								echo AdminPage::prepareJs("
									$(\'.show-new-more-{$un}\').on(\'click\', function(e){
										e.preventDefault();
										var id = $(this).data(\'id\');
										swal({
											html: $(\'#\'+id).html(),
											showCloseButton: true,
											showCancelButton: true,
											showConfirmButton: false,
											cancelButtonText: \'Закрыть\',
											cancelButtonAriaLabel: \'Закрыть\',
											width: \'850px\'
										});
										
									});
								");
						')],
						*/
						//["title"=>"База", "name"=>"our", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Другие сайты</span>"; break; case 1: echo "<span class=\"green\">Наша</span>"; break; }')],
						["title"=>"Состояние", "name"=>"on_off", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
						switch($cel){
								case 0: echo "<span class=\"red\">Отключен</span>"; echo "<br><a class=\"ajax-load icons-wrap\" data-center=\"false\" data-history=\"false\"  href=\"'.$this->url.'/news/?status=on&id={$row["id"]}&search='.$_GET['search'].'&like='.$_GET['like'].'\"><em class=\"red fa fa-toggle-off fa-2\" title=\"Включить\"></em></a>";break; 
								case 1: echo "<span class=\"green\">Включен</span>"; echo "<br><a class=\"ajax-load icons-wrap\" data-center=\"false\" data-history=\"false\"  href=\"'.$this->url.'/news/?status=off&id={$row["id"]}&search='.$_GET['search'].'&like='.$_GET['like'].'\"><em class=\"green fa fa-toggle-on fa-2\" title=\"Выключить\"></em></a>";break; 
							}
						')],
						["title"=>"Информация", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
							$model_gorod_news = new model_gorod_news();
							$ups = $model_gorod_news->model_news_time_up()->getItemsWhere("`new_id`=\'{$row["id"]}\'", "`date` DESC, `time` DESC", 0, 2);
							if($ups){
							echo "<div><a href=\"'.$this->url.'/news/ups/{$row["id"]}\">Поднятия:</a></div>";
							foreach($ups as $up){
								$color = ($up["do"]==1)?"green":"grey";
								echo "<div class=\"{$color} size-small\">{$up["date"]} {$up["time"]}</div>";
							}}
						')],
						["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" , "style"=>"width:130px;"], "content"=>create_function('$cel,$row',' 
							echo "<a class=\"ajax-load icons-wrap\" href=\"'.$this->url.'/news/ups/add/{$row["id"]}\"><em class=\"fa fa-clock-o fa-2\" title=\"Запланировать поднятие\"></em></a>";
							echo "<a class=\"ajax-load icons-wrap\" href=\"'.$this->url.'/news/photos/{$row["id"]}\"><em class=\"fa fa-picture-o fa-2\" title=\"Картинки/Фото\"></em></a>";
							echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" data-confirm=\"Вы действительно хотите удалить все превью фотографий?\" data-history=\"false\"  href=\"'.$this->url.'/news/?trashphotos=1&id={$row["id"]}&search='.$_GET['search'].'&like='.$_GET['like'].'\"><em class=\"fa fa-trash-o fa-2\" title=\"Удалить превью фотографий\"></em></a>";
							
							if($row["pushs"]) $pushs_color = "green"; else $pushs_color = "red";
							echo "<a class=\"ajax-load icons-wrap\" href=\"'.$this->url.'/push/add/?new_id={$row["id"]}&title={$row["news_head"]}&body={$row["news_lid"]}&link=https://feo.ua/news/{$row["url"]}&body={$row["news_lid"]}\">
								<span class=\"fa-stack fa-2x\"><em class=\"fa fa-mobile fa-stack-2x\"></em><strong class=\"fa-stack-1x mobile-text\" style=\"color:{$pushs_color}; font-size:12px;\" title=\"Создать push (Уже создано: {$row["pushs"]} раз)\">{$row["pushs"]}</strong></span>
							</a>";
							
							if($row["nead_stream"]){
								if($row["audios"]) $color = "green"; else  $color = "red";
								echo "<a class=\"ajax-load icons-wrap\" href=\"'.$this->url.'/news/audio/{$row["id"]}\"><em style=\"color:{$color};\" class=\"fa fa-file-audio-o fa-2\" title=\"Аудио озвучка\"></em></a>";
							}
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
	
	function action_add($actions=null){
		$model_gorod_news = new model_gorod_news();
		$model_adventures = new model_adventures();
		$this->view->headers['title'] = 'Новости | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Добавление новой новости"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/news/add/"];
		$this->view->data['header'] = "Новости - Добавление новой новости";
		
		if(isset($_POST['save'])){ $this->view->notRender();
			$razd = $model_gorod_news->model_razd()->getItem($_POST['razd_id']);
			$name_razd = trim($razd['name_razd']);
			
			$cities = $model_gorod_news->model_cities()->getItem($_POST['city_id'][0]);
			$town = trim($cities['city_title']);
			$new_tags =[];
			$_tags = explode(',', $_POST['news_tag-input']); $tags = ';'; foreach($_tags as $tag){ $tag = trim($tag); if(!empty($tag)){$tags .= $tag.';'; $new_tags[]=$tag;} }
			foreach($new_tags as $tag) {
				$model_gorod_news->_model_news_teg()->Insert([ 'tag' => $tag ]);
			}
			$data = [
				"news_id"=>0,
				"news_head"=>trim($_POST['news_head']),
				"news_lid"=>trim($_POST['news_lid']),
				"news_body"=>trim($_POST['news_body']),
				"news_vrez"=>'',
				"news_author"=>$_POST['news_author'],
				"news_video"=>trim($_POST['news_video']),
				"news_video_you"=>trim($_POST['news_video_you']),
				"news_foto"=>'0.jpg',
				"news_foto_sm"=>'0.jpg',
				"big_open_foto"=>0,
				"news_foto_reportag"=>($_POST['news_foto_reportag']=='on'?1:0),
				"foto_all"=>($_POST['foto_all']=='on'?1:0),
				"news_podp"=>'',
				"news_num"=>'',
				"news_razd"=>$name_razd,
				"razd_id"=>(int)$_POST['razd_id'],
				"news_kto"=>trim($_POST['news_kto']),
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
				"18plus"=>($_POST['18plus']=='on'?1:0),
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
			if($_POST['watermark']){
				$data['watermark'] = $_POST['watermark'];
			} 
			else {
				$data['watermark'] = null;
			}
			if($_POST['watermark_big']){
				$data['watermark_big'] = $_POST['watermark_big'];
			} 
			else {
				$data['watermark_big'] = null;
			}
			
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
				//var_dump($data); exit;
				$id = $model_gorod_news->Insert($data);
				$model_gorod_news->model_news_cities()->Delete("`new_id`='{$id}'");
				foreach($_POST['city_id'] as $city_id){
					$model_gorod_news->model_news_cities()->Insert(['new_id'=>$id, 'country_id'=>(int)$_POST['country_id'], 'region_id'=>(int)$_POST['region_id'], 'city_id'=>(int)$city_id, 'add_date'=>date("Y-m-d H:i:s")]);
				}
				$new = $model_gorod_news->getItem($id);
				
				$model_gorod_news->model_news_in_gazeta()->Delete("`new_id`='{$id}'");
				if(!empty($_POST['news_in_gazeta']) AND !empty($_POST['news_in_gazeta-input'])){
					$num = $model_adventures->model_gazeta_nums()->getItem($_POST['news_in_gazeta']);
					$model_gorod_news->model_news_in_gazeta()->Insert([
						'new_id' => $id,
						'gazeta_id' => $num['pid'],
						'num_id' => $num['id'],
						'num' => $num['num'],
						'date' => $num['date'],
						'adddate' => date("Y-m-d H:i:s"),
					]);
				}
				
				if(empty($new['url']) or empty($new['url_ru'])){
					$update_data = [];
					if(empty($new['url'])){
						$url = $id . '-'.$this->new_url($_POST['news_head']) . ".html";
						$update_data['url'] = $url;
					}
					if(empty($new['url_ru'])){
						$url_ru = $id . '-'.$this->new_url_ru($_POST['news_head']);
						$update_data['url_ru'] = $url_ru;
					}
					$model_gorod_news->Update($update_data, $id);
				}
				
				//var_dump($_POST); exit;
				$search = urlencode(trim($_POST['news_head']));
				header("Location: /admin/gorod/news/?search=$search");
			}
			else {
				$this->view->yesRender();
				foreach($errors as $error){
					$content .= "<h3 style='color:red;'>{$error}</h3>";
				}
				$new = $data;
				$new['city_id']=$_POST['city_id'];
			}
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
		
		$watermarks = []; 
		$watermarks_html='<h3><strong>Водяной знак (только для своих фотографий):</strong></h3>';
		if(empty($new['watermark'])) { $ch='checked="checked"';}
		if(empty($new['watermark_big'])) { $ch_big='checked="checked"';}
		$watermarks_html .= "<div class=\"sectright-filters-form-label\">
				<label>
					<input name=\"watermark\" type=\"radio\" style=\"width:auto;min-width:auto;\" {$ch} value=\"0\"><span>Не устанавливать на эскизы</span>
				</label>
				<label>
					<input name=\"watermark_big\" type=\"radio\" style=\"width:auto;min-width:auto;\" {$ch_big} value=\"0\"><span>Не устанавливать на большие фото</span>
				</label>
				<hr/>	
			</div>";
		$dir = scandir(APPDIR . "/uploads/image/watermarks/");
		foreach($dir as $file){
			if(is_file(APPDIR . "/uploads/image/watermarks/{$file}")){
				$watermarks[]=$file;
			}
		}
		foreach($watermarks as $watermark){
			$ch = ($new['watermark']==$watermark)?'checked="checked"':'';
			$ch_big = ($new['watermark_big']==$watermark)?'checked="checked"':'';
			$watermarks_html .= "<div class=\"sectright-filters-form-label\">
				<div style=\"background-color:#ececec;float: left;padding: 5px;\"><img src=\"{$GLOBALS['CONFIG']['HTTP_HOST']}/uploads/image/watermarks/{$watermark}\"/></div>
				<label>
					<input name=\"watermark\" type=\"radio\" style=\"width:auto;min-width:auto;\" {$ch} value=\"{$watermark}\"> <span>Установить для эскиза</span>
				</label>
				<label>
					<input name=\"watermark_big\" type=\"radio\" style=\"width:auto;min-width:auto;\" {$ch_big} value=\"{$watermark}\"> <span>Установить для большой фотографии</span>
				</label>	
			<hr/>	
			</div>";
		}

		$admin = new AdminPage(
			array(
				"model" => $model_gorod_news,
				"item" => $new,
				"action" => '/admin/gorod/news/add/',
				"fields" => [
					["title"=>"Заголовок", "name"=>"news_head", "attrs"=>[], "type"=>"text"],
					["title"=>"Подзаголовок", "name"=>"news_lid", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Текст", "name"=>"news_body", "attrs"=>[], "type"=>"editor", "css"=>["https://gsp1.feomedia.ru/application/views/gorod24/libs/bootstrap/css/bootstrap.min.css", "https://gsp1.feomedia.ru/application/views/gorod24/libs/simple-line-icons/css/simple-line-icons.css", "https://gsp1.feomedia.ru/application/views/gorod24/libs/font-awesome/css/font-awesome.min.css", "https://gsp1.feomedia.ru/application/views/gorod24/libs/feorflogin/css/feorflogin.css","https://gsp1.feomedia.ru/application/views/gorod24/libs/swal/css/sweetalert.css", "https://gsp1.feomedia.ru/application/views/gorod24/css/main.min.css"]],
					//["title"=>"Врез", "name"=>"news_vrez", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"База новостей", "name"=>"our", "attrs"=>[], "value"=>1, "type"=>"select", "items" => [[ "value"=>0, "Другие сайты"],[ "value"=>1, "Наши новости"]]],
					["title"=>"Тип новости", "name"=>"type", "attrs"=>[], "type"=>"select", "items" => $types],
					["title"=>"Раздел", "name"=>"razd_id", "attrs"=>[], "type"=>"select", "items" => $model_gorod_news->model_razd()->getItemsWhere("`on_off`=1", "`name_razd` ASC", null, null, "`id` as `value`, `name_razd` as `label`")],
					["title"=>"Страна", "name"=>"country_id", "attrs"=>[], "multiple"=>false, "type"=>"select", "items" => $model_countries->getItemsWhere("`in_news`=1", "`country_id` ASC", null, null, "`country_id` as `value`, `country_title` as `label`")],
					["title"=>"Регион", "name"=>"region_id", "attrs"=>[], "multiple"=>false, "type"=>"select", "items" => $model_regions->getItemsWhere("`in_news`=1", "`region_id` ASC", null, null, "`region_id` as `value`, `region_title` as `label`")],
					["title"=>"Город", "name"=>"city_id", "attrs"=>[], "multiple"=>true, "type"=>"select", "items" => $model_gorod_news->model_cities()->getItemsWhere("`in_news`=1", "`city_title` ASC", null, null, "`city_id` as `value`, `city_title` as `label`")],
					["title"=>"Автор", "name"=>"news_kto", "attrs"=>[], "type"=>"text"],
					["title"=>"Автор новости (скрытый)", "name"=>"news_author", "attrs"=>[], "null"=> true, "type"=>"select", "items" => $model_gorod_news->_model_new_authors()->getItemsWhere("`on_off`=1", "`author_name` ASC", null, null, "`author_id` as `value`, `author_name` as `label`")],
					["title"=>"Фирма", "name"=>"id_pr", "attrs"=>[], "type"=>"autocomplete", "label"=>$firm['name'], "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/firms"],
					
					["title"=>"Видео", "name"=>"news_video", "attrs"=>[], "type"=>"text"],
					["title"=>"Видео (YOUTUBE)", "name"=>"news_video_you", "attrs"=>[], "type"=>"text"],
					
					["title"=>"Название источника", "name"=>"ot_name", "attrs"=>[], "type"=>"text"],
					["title"=>"Ссылка на источник", "name"=>"ot_sylka", "attrs"=>[], "type"=>"text"],
					
					["title"=>"Дата и время публикации", "name"=>"news_date", "value"=>date("Y-m-d H:i:s"), "attrs"=>[], "type"=>"datetime"],
					
					["title"=>"18+", "name"=>"18plus", "attrs"=>[], "type"=>"switch"],
					["title"=>"Показывать как фоторепортаж", "name"=>"news_foto_reportag", "attrs"=>[], "type"=>"switch"],
					["title"=>"Показывать коментрарии", "name"=>"show_comment", "value"=>1, "attrs"=>[], "type"=>"switch"],
					["title"=>"Фото и видео материалы", "name"=>"foto_all", "value"=>1, "attrs"=>[], "type"=>"switch"],
					["title"=>"Показывать в приложении", "name"=>"show_in_app", "value"=>1, "attrs"=>[], "type"=>"switch"],
					["title"=>"Нужна озвучка", "name"=>"nead_stream", "value"=>1, "attrs"=>[], "type"=>"switch"],
					["title"=>"Теги к новости", "name"=>"news_tag", "attrs"=>[], "type"=>"autocomplete", "values"=>$tags, "label"=>implode(', ',$tags), "multiple"=>true, "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/tags"],
					["title"=>"Новсть закрыта/открыта", "name"=>"news_lock", "attrs"=>[], "type"=>"select", "items" => [
						[ "value"=>0, "По умолчанию"],
						[ "value"=>1, "Всегда открыта"],
						[ "value"=>2, "Закрыта"],
					]],
					["title"=>"Дата до которой новость закрыта", "name"=>"news_lock_for", "value"=>'0000-00-00 00:00:00', "attrs"=>[], "type"=>"datetime"],
					
					["content"=>"<h2>Подключаемые модули</h2>", "attrs"=>[], "type"=>"line"],
					
					["title"=>"Альбом", "name"=>"news_album_id", "attrs"=>[], "type"=>"autocomplete", "label"=>$album,"source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/albums"],
					["title"=>"Опрос", "name"=>"news_inter_id", "attrs"=>[], "type"=>"autocomplete", "label"=>$inter, "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/inters"],
					["title"=>"Замер цен", "name"=>"news_zamer_id", "attrs"=>[], "type"=>"autocomplete", "label"=>$zamer, "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/zamers"],
					["title"=>"Панорамы", "name"=>"news_panorama", "attrs"=>[], "type"=>"autocomplete", "label"=>$panorama, "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/panoramas"],
					["title"=>"Вид панорамы", "name"=>"news_panorama_type", "attrs"=>[], "type"=>"select", "items" => [[ "value"=>0, "Как альбом"],[ "value"=>1, "Как одна панорама"]]],
					["title"=>"Включена/выключена", "name"=>"on_off", "attrs"=>[], "type"=>"switch"],
					
					["title"=>"Новость в газете", "name"=>"news_in_gazeta", "attrs"=>[], "type"=>"autocomplete", "value"=>$num['num_id'], "label"=>$num_label, "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/gazeta"],
					["type"=>"line", "content"=>$watermarks_html],
					
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
		$model_gorod_news = new model_gorod_news();
		$model_adventures = new model_adventures();
		$new = $model_gorod_news->getItem($id);
		$this->view->headers['title'] = 'Новости | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "{$new['news_head']}"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/news/{$id}/"];
		$this->view->data['header'] = "Новости - {$new['news_head']}";

		
		if(isset($_POST['save'])){$this->view->notRender();
			$razd = $model_gorod_news->model_razd()->getItem($_POST['razd_id']);
			$name_razd = $razd['name_razd'];
			
			$cities = $model_gorod_news->model_cities()->getItem($_POST['city_id'][0]);
			$town = $cities['city_title'];
			
			$new_tags =[];
			$_tags = explode(',', $_POST['news_tag-input']); $tags = ';'; foreach($_tags as $tag){ $tag = trim($tag); if(!empty($tag)){$tags .= $tag.';'; $new_tags[]=$tag;} }
			foreach($new_tags as $tag) {
				$model_gorod_news->_model_news_teg()->Insert([ 'tag' => $tag ]);
			}
			
			
			$data = [
				"id"=>$_POST['id'],
				//"news_id"=>(int)$_POST['news_id'],
				"news_head"=>$_POST['news_head'],
				"news_lid"=>$_POST['news_lid'],
				"news_body"=>$_POST['news_body'],
				//"news_vrez"=>'',
				"news_author"=>$_POST['news_author'],
				"news_video"=>$_POST['news_video'],
				"news_video_you"=>$_POST['news_video_you'],
				//"news_foto"=>'0.jpg',
				//"news_foto_sm"=>'0.jpg',
				//"big_open_foto"=>0,
				"news_foto_reportag"=>($_POST['news_foto_reportag']=='on'?1:0),
				"foto_all"=>($_POST['foto_all']=='on'?1:0),
				//"news_podp"=>'',
				//"news_num"=>'',
				"news_razd"=>$name_razd,
				"razd_id"=>(int)$_POST['razd_id'],
				"news_kto"=>$_POST['news_kto'],
				"news_tag"=>$tags,
				"town"=>$town,
				"country_id"=>(int)$_POST['country_id'],
				"region_id"=>(int)$_POST['region_id'],
				"city_id"=>(int)$_POST['city_id'][0],
				//"news_key"=>'',
				//"news_des"=>'',
				//"look"=>'',
				"news_date"=>$_POST['news_date'],
				"news_up"=>$_POST['news_date'],
				"our"=>(int)$_POST['our'],
				"type"=>$this->varChek($_POST['type']),
				//"lock_"=>0,
				//"looks"=>0,
				//"vk_"=>0,
				//"vk_feo"=>0,
				//"vk_feorf"=>0,
				//"vk_g"=>0,
				//"fb"=>0,
				//"ok"=>0,
				"ot_name"=>$_POST['ot_name'],
				"ot_sylka"=>$_POST['ot_sylka'],
				//"url"=>'',
				//"url_ru"=>'',
				//"kay_word"=>'',
				"id_pr"=>$_POST['id_pr'],
				//"app_id"=>0,
				//"akcia_id"=>0,
				"18plus"=>($_POST['18plus']=='on'?1:0),
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
				//"news_rating"=>0,
				"nead_stream"=>($_POST['nead_stream']=='on'?1:0),
			];
			if($_POST['watermark']){
				$data['watermark'] = $_POST['watermark'];
			} 
			else {
				$data['watermark'] = null;
			}
			
			if($_POST['watermark_big']){
				$data['watermark_big'] = $_POST['watermark_big'];
			} 
			else {
				$data['watermark_big'] = null;
			}
			
			if(empty($new['url'])){
				$url = $_POST['id'] . '-'.$this->new_url($_POST['news_head']) . ".html";
				$data['url'] = $url;
			}
			if(empty($new['url_ru'])){
				$url_ru = $_POST['id'] . '-'.$this->new_url_ru($_POST['news_head']);
				$data['url_ru'] = $url_ru;
			}
			$model_gorod_news->model_news_in_gazeta()->Delete("`new_id`='{$id}'");
			if(!empty($_POST['news_in_gazeta']) AND !empty($_POST['news_in_gazeta-input'])){
				$num = $model_adventures->model_gazeta_nums()->getItem($_POST['news_in_gazeta']);
				$model_gorod_news->model_news_in_gazeta()->Insert([
					'new_id' => $id,
					'gazeta_id' => $num['pid'],
					'num_id' => $num['id'],
					'num' => $num['num'],
					'date' => $num['date'],
					'adddate' => date("Y-m-d H:i:s"),
				]);
			}
			
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
				//var_dump($_POST); exit;
				$model_gorod_news->Update($data, $_POST['id']);
				$model_gorod_news->model_news_cities()->Delete("`new_id`='{$id}'");
				foreach($_POST['city_id'] as $city_id){
					$model_gorod_news->model_news_cities()->Insert(['new_id'=>$id, 'country_id'=>(int)$_POST['country_id'], 'region_id'=>(int)$_POST['region_id'], 'city_id'=>(int)$city_id, 'add_date'=>date("Y-m-d H:i:s")]);
				}
				$search = urlencode(trim($_POST['news_head']));
				header("Location: /admin/gorod/news/?search=$search");
			}
			else {
				$this->view->yesRender();
				foreach($errors as $error){
					$content .= "<h3 style='color:red;'>{$error}</h3>";
				}
				$new = $data;
				$new['city_id']=$_POST['city_id'];
			}
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
		$cities = $model_gorod_news->model_news_cities()->get("city_id")->where("`new_id`='{$id}'")->commit('col');
		$model_countries = new model_countries();
		$model_regions = new model_regions();
		$model_gorod_photos = $model_gorod_news->model_gorod_photos();
		
		$photos = $model_gorod_photos->getItemsWhere("`new_id`='{$id}'");
		$ptotos_html = '';
		$ptotos_html .= AdminPage::prepareJs('
			$(".photos-img-container img.insert-img").dblclick(function(e){
				var $alt = $(this).attr("alt");
				var $src = $(this).attr("src");
				tinymce.activeEditor.insertContent(\'<img alt="\' + $alt + \'" width="auto;" style="max-width:100%" height="auto" src="https://gorod24.online\' + $src + \'"/>\');
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
			$ptotos_html.='<div class="photos-img-container"><img class="insert-img" src="'.$photo['img'].'" alt="'.$photo['description'].'" /></div>';
		}
		$ptotos_html.='</div>';
		
		$num = $model_gorod_news->model_news_in_gazeta()->getItemWhere("`new_id`='{$id}'");
		if($num) { $num_label = "{$num['num']} от {$num['date']}"; }
		
		$watermarks = []; 
		$watermarks_html='<h3><strong>Водяной знак (только для своих фотографий):</strong></h3>';
		if(empty($new['watermark'])) { $ch='checked="checked"';}
		if(empty($new['watermark_big'])) { $ch_big='checked="checked"';}
		$watermarks_html .= "<div class=\"sectright-filters-form-label\">
				<label>
					<input name=\"watermark\" type=\"radio\" style=\"width:auto;min-width:auto;\" {$ch} value=\"0\"><span>Не устанавливать на эскизы</span>
				</label>
				<label>
					<input name=\"watermark_big\" type=\"radio\" style=\"width:auto;min-width:auto;\" {$ch_big} value=\"0\"><span>Не устанавливать на большие фото</span>
				</label>
				<hr/>	
			</div>";
		$dir = scandir(APPDIR . "/uploads/image/watermarks/");
		foreach($dir as $file){
			if(is_file(APPDIR . "/uploads/image/watermarks/{$file}")){
				$watermarks[]=$file;
			}
		}
		foreach($watermarks as $watermark){
			$ch = ($new['watermark']==$watermark)?'checked="checked"':'';
			$ch_big = ($new['watermark_big']==$watermark)?'checked="checked"':'';
			$watermarks_html .= "<div class=\"sectright-filters-form-label\">
				<div style=\"background-color:#ececec;float: left;padding: 5px;\"><img src=\"{$GLOBALS['CONFIG']['HTTP_HOST']}/uploads/image/watermarks/{$watermark}\"/></div>
				<label>
					<input name=\"watermark\" type=\"radio\" style=\"width:auto;min-width:auto;\" {$ch} value=\"{$watermark}\"> <span>Установить для эскиза</span>
				</label>
				<label>
					<input name=\"watermark_big\" type=\"radio\" style=\"width:auto;min-width:auto;\" {$ch_big} value=\"{$watermark}\"> <span>Установить для большой фотографии</span>
				</label>	
			<hr/>	
			</div>";
		}
		
		$admin = new AdminPage(
			array(
				"model" => $model_gorod_news,
				"item" => $new,
				"action" => '/admin/gorod/news/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Заголовок", "name"=>"news_head", "attrs"=>[], "type"=>"text"],
					["title"=>"Подзаголовок", "name"=>"news_lid", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Фото",  "content"=>$ptotos_html, "type"=>"line"],
					["title"=>"Текст", "name"=>"news_body", "attrs"=>[], "type"=>"editor", "css"=>["https://gsp1.feomedia.ru/application/views/gorod24/libs/bootstrap/css/bootstrap.min.css", "https://gsp1.feomedia.ru/application/views/gorod24/libs/simple-line-icons/css/simple-line-icons.css", "https://gsp1.feomedia.ru/application/views/gorod24/libs/font-awesome/css/font-awesome.min.css", "https://gsp1.feomedia.ru/application/views/gorod24/libs/feorflogin/css/feorflogin.css","https://gsp1.feomedia.ru/application/views/gorod24/libs/swal/css/sweetalert.css", "https://gsp1.feomedia.ru/application/views/gorod24/css/main.min.css"]],
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
					
					["title"=>"18+", "name"=>"18plus", "attrs"=>[], "type"=>"switch"],
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
					
					["title"=>"Новость в газете", "name"=>"news_in_gazeta", "attrs"=>[], "type"=>"autocomplete", "value"=>$num['num_id'], "label"=>$num_label, "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/gazeta"],
					["type"=>"line", "content"=>$watermarks_html],
					
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
		/**/
	}

	function action_ups($actions=null){
		$id = (int)$actions[0];
		$model_gorod_news = new model_gorod_news();
		$model_news_time_up = $model_gorod_news->model_news_time_up();
		if($id) {
			$new = $model_gorod_news->getItem($id);
			$this->view->headers['title'] = 'Поднятия | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Поднятия новости"=>$this->url.'/news/ups/'.$id];
			$this->view->data['header'] = "Поднятия новости: {$new['news_head']}";
			$where = "`new_id`='{$id}'";
			$action = "/admin/gorod/news/ups/{$id}";
			$controls = [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Добавить поднятие", "href"=>"/admin/gorod/news/ups/add/{$id}", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
					];
		}
		else {
			$this->view->headers['title'] = 'Поднятия | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Поднятия новостей"=>$this->url.'/news/ups/'];
			$this->view->data['header'] = "Поднятия новостей";
			$where = "1";
			$action = "/admin/gorod/news/ups/";
			$controls = [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
					];
		}
		
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model_news_time_up->Delete($input);
			}
		}
		
			$admin = new AdminList(
				array(
					"model" => $model_news_time_up,
					"where" => $where,
					"order" => "`date` DESC, `time` DESC",
					"multiple" => "true",
					"action" => $action,
					"controls" => $controls,
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"new_id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/news/ups/{$row["new_id"]}/\">{$cel}</a>";')],
						["title"=>"Новость", "name"=>"new_id", "attrs"=>[], "content"=>create_function('$cel,$row','
							$model_gorod_news = new model_gorod_news();
							$new = $model_gorod_news->getItem($cel);
							echo $new["news_head"];
						')],
						["title"=>"Дата", "name"=>"do", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo "{$row["date"]} {$row["time"]}";
						')],
						["title"=>"Состояние", "name"=>"do", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							switch($cel){	
								case 0: echo "<span class=\"red\">Ожидает поднятия</span>"; break; 
								case 1: echo "<span class=\"green\">Выполнено</span>"; break; 
							}
						')],
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
			
		$this->view->data['content'] = $content;	
		
	}
	
	function action_ups_add($actions=null){
		$id = (int)$actions[0];
		$model_gorod_news = new model_gorod_news();
		$model_news_time_up = $model_gorod_news->model_news_time_up();
		if($id) {
			$new = $model_gorod_news->getItem($id);
			$this->view->headers['title'] = 'Поднятия | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Запланировать поднятие"=>$this->url.'/news/ups/'.$id];
			$this->view->data['header'] = "Запланировать поднятие новости: {$new['news_head']}";
			$where = "`new_id`='{$id}'";
			$action = "/admin/gorod/news/ups/add/{$id}";
		}
		if($_POST['save'] and $new){
			$datetime = explode(" ", $_POST['datetime']);
			$data = [
				'new_id' => $new['id'],
				'id_news' => $new['news_id'],
				'our' => $new['our'],
				'date' => $datetime[0],
				'time' => $datetime[1],
				'lastdate' => '0000-00-00 00:00:00',
				'do' => 0,
			];
			$model_news_time_up->Insert($data);
			header('Location: /admin/gorod/news/ups/'.$new['id']);
		}
		
		$admin = new AdminPage(
			array(
				"model" => $model_news_time_up,
				"action" => $action,
				"fields" => [
					["title"=>"Id", "name"=>"new_id", "value"=>$id,"attrs"=>[], "type"=>"hidden"],
					["title"=>"Дата", "name"=>"datetime", "attrs"=>[], "value"=>date("Y-m-d H:i:s"), "type"=>"datetime"],
					["title"=>"Добавить расписание", "name"=>"save", "value"=>1, "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		
		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
		
	}
	
	function action_photos($actions=null){
		$id = (int)$actions[0];
		$model_gorod_news = new model_gorod_news();
		$model_gorod_photos = $model_gorod_news->model_gorod_photos();
		$new = $model_gorod_news->getItem($id);
		$this->view->headers['title'] = 'Фото | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Фото новости"=>$this->url.'/news/photos/'.$id];
		$this->view->data['header'] = "Фото новости: {$new['news_head']}";
		
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input){
				$photo = $model_gorod_photos->getItem($input);
				if(file_exists( APPDIR . $photo['img'] )){ 
					@unlink( APPDIR . $photo['img'] ); 
					@unlink( APPDIR . "/uploads/image/news_thrumbs/new_{$photo['new_id']}_361_240_w.jpg" ); 
					@unlink( APPDIR . "/uploads/image/news_thrumbs/new_{$photo['new_id']}_361_240.jpg" ); 
				}
				$model_gorod_photos->Delete($input);
			}
		}
		
		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model_gorod_photos->Update([ 'status'=>'1' ], $input);
			}
		}
		
		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model_gorod_photos->Update([ 'status'=>'0' ], $input);
			}
		}
			
		if (isset($_GET['move']) and isset($_GET['id'])){
			switch($_GET['move']){
				case 'top': $model_gorod_photos->MoveRowTop($_GET['id'], "`new_id`='{$id}'", "pos"); break;
				case 'up': $model_gorod_photos->MoveRowUp($_GET['id'], "`new_id`='{$id}'", "pos"); break;
				case 'down': $model_gorod_photos->MoveRowDown($_GET['id'], "`new_id`='{$id}'", "pos"); break;
				case 'bottom': $model_gorod_photos->MoveRowBottom($_GET['id'], "`new_id`='{$id}'", "pos"); break;
			}
		}		
		
		if (isset($_GET['main']) and isset($_GET['id'])){
			switch($_GET['main']){
				case 'on': 
					$model_gorod_photos->Update(['main'=>0], "`new_id`='{$id}'");
					$model_gorod_photos->Update(['main'=>1], $_GET['id']);
					$model_gorod_photos->MoveRowTop($_GET['id'], "`new_id`='{$id}'", "pos"); break;
			}
		}		
			
			$admin = new AdminList(
				array(
					"model" => $model_gorod_photos,
					"where" => "`new_id`='{$id}'",
					"order" => "`pos` ASC",
					"multiple" => "true",
					"action" => '/admin/gorod/news/photos/'.$id,
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить фото", "href"=>"/admin/gorod/news/photos/add/{$id}", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"]
					],
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/news/photos/edit/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Главное", "name"=>"main", "attrs"=>[], "content"=>create_function('$cel,$row','
							if($cel==0){
								echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" data-confirm=\"Вы действительно хотите сделать фото главным?\" data-history=\"false\" href=\"'.$this->url.'/news/photos/{$row["new_id"]}/?main=on&id={$row["id"]}\"><em class=\"fa fa-check-circle-o fa-2\"></em></a>";
							}
							else {
								echo "<span class=\"red\">Главное</span>";
							}
						')],
						["title"=>"Фото", "name"=>"img", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo "<img src=\"{$GLOBALS[\'CONFIG\'][\'HTTP_HOST\']}/thrumbs/news/photo_{$row["new_id"]}_{$row["id"]}_361_240_w.jpg\" width=\"120px\" align=left>";
						')],
						["title"=>"Заголовок", "name"=>"title", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/news/photos/edit/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Краткое описание", "name"=>"description", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Позиция", "name"=>"pos", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Состояние", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
						["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
							echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" data-confirm=\"Вы действительно хотите переместить фото на самый верх?\" data-history=\"false\" href=\"'.$this->url.'/news/photos/{$row["new_id"]}/?move=top&id={$row["id"]}\" title=\"Сделать главной\"><em class=\"fa fa-arrow-circle-up fa-2\"></em></a><br>";
							echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" data-confirm=\"Вы действительно хотите переместить фото на 1 позицию верх?\" data-history=\"false\" href=\"'.$this->url.'/news/photos/{$row["new_id"]}/?move=up&id={$row["id"]}\" title=\"Переместить вверх на 1 позицию\"><em class=\"fa fa-arrow-up fa-2\"></em></a><br>";
							echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" data-confirm=\"Вы действительно хотите переместить фото на 1 позицию вниз?\" data-history=\"false\" href=\"'.$this->url.'/news/photos/{$row["new_id"]}/?move=down&id={$row["id"]}\" title=\"Переместить вниз на 1 позицию\"><em class=\"fa fa-arrow-down fa-2\"></em></a><br>";
							echo "<a class=\"ajax-load icons-wrap\" data-center=\"false\" data-confirm=\"Вы действительно хотите переместить фото в самый низ?\" data-history=\"false\" href=\"'.$this->url.'/news/photos/{$row["new_id"]}/?move=bottom&id={$row["id"]}\" title=\"Переместить в конец\"><em class=\"fa fa-arrow-circle-down fa-2\"></em></a>";
						')]
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
			
		$this->view->data['content'] = $content;	
	}

	function action_photos_add($actions=null){
		$id = (int)$actions[0];
		$model_gorod_news = new model_gorod_news();
		$model_gorod_photos = $model_gorod_news->model_gorod_photos();
		$new = $model_gorod_news->getItem($id);
		$this->view->headers['title'] = 'Добавление Фото к новости | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Фото новости"=>$this->url.'/news/photos/'.$id, "Добавление Фото к новости"=>$this->url.'/news/photos/add/'.$id];
		$this->view->data['header'] = "Добавление Фото к новости: {$new['news_head']}";
	
		$admin = new AdminPage(
			array(
				"model" => $model_gorod_photos,
				"item" => $photo,
				"action" => '/admin/gorod/news/photos/edit/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"new_id", "value"=>$id,"attrs"=>[], "type"=>"hidden"],
					["title"=>"Загрузчик", "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/news/photos/upload/{$id}", "filters"=>[ ["title"=>"Image files", "extensions"=>"jpeg,jpg,gif,png" ] ], "attrs"=>[], "type"=>"filesUploader"],
				],
			)
		);
		
		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	function action_photos_upload($actions=null){
		$id = (int)$actions[0];
		$model_gorod_news = new model_gorod_news();
		$model_gorod_photos = $model_gorod_news->model_gorod_photos();
		$new = $model_gorod_news->getItem($id);
		$model_uploads = new model_uploads();
		$this->view->notRender();
		
		if (empty($_FILES) || $_FILES['file']['error']) {
		  die('{"OK": 0, "info": "Failed to move uploaded file."}');
		}
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];
		$orig_name = $_REQUEST["name"];

		$filePath = APPDIR . "/uploads/image/news/$fileName";

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
			
			$http_url = '/uploads/image/news/';
			$dir = APPDIR . $http_url;
			$fileName = uniqid($id.'_').'.'.$ext;
			rename($filePath, $dir.$fileName); $filePath = $dir.$fileName;
			$size = filesize(APPDIR . '/uploads/image/news/'.$fileName);
			
			
			
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
			
			$pos = $model_gorod_photos->getCountWhere("`new_id`='{$id}'");
			
			$photo_id = $model_gorod_photos->Insert([
				'new_id' => $id,
				'img' => "/uploads/image/news/{$fileName}",
				'img_id' => $upload_id, 
				'description' => '', 
				'title' => $orig_name, 
				'pos' => $pos, 
				'status' => 1, 
				'descr_on' => 0, 
			]);
			
			$result = [
				"OK" => 1,
				"info" => "Upload successful.",
				"type" => 'image',
				"id" => $upload_id,
				"name" =>$fileName,
				"original_name" =>$orig_name,
				"ext" => $ext,
				"size" => $size,
				"destination" => '/uploads/image/news/',
				"url" => "/uploads/image/news/{$fileName}",
			];
			
			if($result['type'] == 'image'){
				list($width, $height, $type) = getimagesize(APPDIR . "/uploads/image/news/{$fileName}");
				$result['image'] = [
					'width' => $width,
					'height' => $height,
				];
			}
			
			die(json_encode($result));
			
		}
	}
	
	function action_photos_edit($actions=null){
		$id = (int)$actions[0];
		$model_gorod_news = new model_gorod_news();
		$model_gorod_photos = $model_gorod_news->model_gorod_photos();
		$photo = $model_gorod_photos->getItem($id);
		$this->view->headers['title'] = 'Фото | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Фото новости"=>$this->url.'/news/photos/'.$photo['new_id'], "Фото"=>$this->url.'/news/photos/edit/'.$id];
		$this->view->data['header'] = "Редактирование Фото: {$photo['title']}";
		
		/**/
		if(isset($_POST['save'])){$this->view->notRender();
			$data = [
				"id"=>$this->varChek($_POST['id']),
				"title"=>$_POST['title'],
				"description"=>$_POST['description'],
				"status"=>($_POST['status']=='on'?1:0),
			];
			$model_gorod_photos->InsertUpdate($data);
			header('Location: /admin/gorod/news/photos/'.$photo['new_id']);
		}
		
		
		$admin = new AdminPage(
			array(
				"model" => $model_gorod_photos,
				"item" => $photo,
				"action" => '/admin/gorod/news/photos/edit/'.$id.'/',
				"fields" => [
					["title"=>"Id", "name"=>"id", "attrs"=>[], "type"=>"hidden"],
					["title"=>"Id", "name"=>"new_id", "attrs"=>[], "type"=>"hidden"],
					["content"=>"<img src='{$photo['img']}' style='max-width:600px; width:100%;'/>", "attrs"=>[], "type"=>"line"],
					["title"=>"Заголовок", "name"=>"title", "attrs"=>[], "type"=>"text"],
					["title"=>"Краткое описание", "name"=>"description", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		
		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	function action_eskizs($actions=null){
		$id = (int)$actions[0];
		$model_gorod_news = new model_gorod_news();
		if($id) {
			$new = $model_gorod_news->getItem($id);
			$this->view->headers['title'] = 'Эскизы новости | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Редактирование новости"=>$this->url.'/news/'.$id, "Эскизы новости"=>$this->url.'/news/eskizs/'.$id];
			$this->view->data['header'] = "Эскизы новости: {$new['news_head']}";
		
		$items = [
			[ "width"=>552, "height"=>225, "name"=>"Эскиз для прокрутки"],
			[ "width"=>361, "height"=>240, "name"=>"Для списка"],
			[ "width"=>234, "height"=>158, "name"=>"Для главной (топ)"],
			[ "width"=>640, "height"=>320, "name"=>"Для соц сетей"],
			[ "width"=>1280, "height"=>1024, "name"=>"Город24 Для прокрутки"],
		];
			$admin = new AdminList(
				array(
					"model" => $model_gorod_news,
					"items" => $items,
					"action" => $action,
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Изображение", "name"=>"img", "attrs"=>["style"=>"width:150px;"], "content"=>create_function('$cel,$row','
							$ver = uniqid();
							echo "<a href=\"{$GLOBALS[\'CONFIG\'][\'HTTP_HOST\']}/thrumbs/news/new_'.$id.'_{$row["width"]}_{$row["height"]}.jpg?ver={$ver}\" data-fancybox=\"gallery\"><img src=\"{$GLOBALS[\'CONFIG\'][\'HTTP_HOST\']}/thrumbs/news/new_'.$id.'_{$row["width"]}_{$row["height"]}.jpg?ver={$ver}\" width=\"120px\" align=left></a>";
						')],
						["title"=>"Название", "name"=>"name", "align"=>"left", "attrs"=>[], "content"=>create_function('$cel,$row','
							echo "<a class=\"ajax-load icons-wrap\" href=\"'.$this->url.'/news/mainphoto/'.$id.'/{$row["width"]}/{$row["height"]}?title={$cel}\">{$cel} ({$row["width"]}x{$row["height"]})</a>";
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
	
	
	function action_mainphoto($actions=null){
		$id = (int)$actions[0];
		$width = (int)$actions[1];
		$height = (int)$actions[2];
		$model_gorod_news = new model_gorod_news();
		$model_gorod_photos = $model_gorod_news->model_gorod_photos();
		$new = $model_gorod_news->getItem($id);
		$main = $model_gorod_photos->getItemWhere("`new_id`='{$new['id']}'", "*", "`pos` ASC");
		$this->view->headers['title'] = 'Главное Фото | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Редактирование новости"=>$this->url.'/news/'.$new['id'], "Фото новости"=>$this->url.'/news/photos/'.$new['id'], "Эскизы новости"=>$this->url.'/news/eskizs/'.$new['id'], "Главное фото"=>$this->url.'/news/mainphoto/'.$id];
		$this->view->data['header'] = "Редактирование главного Фото: {$photo['title']} ({$_GET['title']})";
		
		$watermarks = []; $watermarks_html='';
		$dir = scandir(APPDIR . "/uploads/image/watermarks/");
		foreach($dir as $file){
			if(is_file(APPDIR . "/uploads/image/watermarks/{$file}")){
				$watermarks[]=$file;
			}
		}
		foreach($watermarks as $watermark){
			$watermarks_html .= "<div class=\"sectright-filters-form-label\">
				<label style=\"background-color:#ececec;\">
					<input name=\"watermark\" type=\"radio\" style=\"width:auto;min-width:auto;\" value=\"{$watermark}\">
					<img src=\"{$GLOBALS['CONFIG']['HTTP_HOST']}/uploads/image/watermarks/{$watermark}\"/>
				</label>
			</div>";
		}
		
		if($_POST['save']){
			
			$src = $_POST['src'];
			$x1 = $_POST['x1'];
			$x2 = $_POST['x2'];
			$y1 = $_POST['y1'];
			$y2 = $_POST['y2'];
			$w = $_POST['w'];
			$h = $_POST['h'];
			$watermark = $_POST['watermark'];
			$jpeg_quality = 100;
			
			$dir_to_up = APPDIR . "/uploads/image/news_thrumbs/{$id}/";
			if(!is_dir($dir_to_up)) mkdir($dir_to_up); chmod($dir_to_up, 0777);
			if($watermark) { $_w = '_w';} $_w='';
			$thrumb_name = "new_{$new['id']}_{$width}_{$height}{$_w}.jpg";
			if (file_exists($dir_to_up.$thrumb_name)){ unlink($dir_to_up.$thrumb_name); }
			
			$targ_w = $width; $targ_h = $height;
			
			$src = APPDIR . $src;
			
			$img_r = imagecreatefromjpeg($src);
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
			imagecopyresampled($dst_r, $img_r, 0, 0, $x1, $y1,$targ_w, $targ_h, $w, $h);
			imagejpeg($dst_r, $dir_to_up.$thrumb_name, $jpeg_quality);
			
			if($watermark) {
				$pic = ImageCreateFromjpeg($dir_to_up.$thrumb_name);
				$im = imagecreatefrompng(APPDIR . "/uploads/image/watermarks/{$watermark}");
				list($water_width, $water_height, $water_type) = getimagesize(APPDIR . "/uploads/image/watermarks/{$watermark}");
				
				$color=ImageColorAllocate($pic, 250, 250, 250); //получаем идентификатор цвета
				$grey = imagecolorallocate($im, 128, 128, 128);
				
				imagecopy($pic, $im, ($targ_w-$water_width-5), ($targ_h-$water_height-5), 0, 0, imagesx($im), imagesy($im));
				unlink($dir_to_up.$thrumb_name);
				Imagejpeg($pic, $dir_to_up.$thrumb_name, $jpeg_quality); //сохраняем рисунок в формате JPEG
				ImageDestroy($pic); //освобождаем память и закрываем изображение
			}
			$search = $new['news_head'];
			header("Location: /admin/gorod/news/eskizs/{$id}");
		}
		
		
		
		$content = "
		<div class=\"img-container crop\">
			<img style=\"max-width:100%;\" src=\"{$GLOBALS['CONFIG']['HTTP_HOST']}{$main['img']}\"/>
		</div>	
		<div class=\"row\">
			<form action=\"{$GLOBALS['CONFIG']['HTTP_HOST']}/admin/gorod/news/mainphoto/{$id}/{$width}/{$height}\" method=\"POST\">
				<input type=\"hidden\" id=\"src\" name=\"src\" value=\"{$main['img']}\" />
				<input type=\"hidden\" id=\"x1\" name=\"x1\" value=\"\" />
				<input type=\"hidden\" id=\"y1\" name=\"y1\" value=\"\" />
				<input type=\"hidden\" id=\"x2\" name=\"x2\" value=\"\" />
				<input type=\"hidden\" id=\"y2\" name=\"y2\" value=\"\" />
				<input type=\"hidden\" id=\"w\" name=\"w\" value=\"\" />
				<input type=\"hidden\" id=\"h\" name=\"h\" value=\"\" />
				<div class=\"watermarks\">
					<h2>Водяные знаки:</h2>
					{$watermarks_html}
				</div>
				<div class=\"sectright-filters-form-label\">
					<button id=\"save\" class=\"btn btn-primary \" name=\"save\" type=\"submit\" value=\"save\">Сохранить</button>
				</div>	
			</form>
		</div>
		";
		$content .= AdminPage::prepareJs("
			var crop = $('.img-container.crop img').imgAreaSelect({
				aspectRatio: '{$width}:{$height}',
				minHeight: {$height},
				minWidth: {$width},
				instance: true,
				handles: true,
				parent: $('.img-container.crop'),
				setOptions:true,
				onSelectEnd: function(img, selection){ 
					var i = $('.img-container.crop img').get(0);
					var porcX = (i.naturalWidth / i.width);
					var porcY = (i.naturalHeight / i.height);
					var image = {
						'src' : $(i).attr('src'),
						'x1' : Math.round(selection.x1 * porcX),
						'y1' : Math.round(selection.y1 * porcX),
						'x2' : Math.round(selection.x2 * porcX),
						'y2' : Math.round(selection.y2 * porcX),
						'w' : Math.round(selection.width * porcX),
						'h' : Math.round(selection.height * porcY),
					}
					$('#x1').val(image.x1);
					$('#x2').val(image.x2);
					$('#y1').val(image.y1);
					$('#y2').val(image.y2);
					$('#w').val(image.w);
					$('#h').val(image.h);
				},
			});
		");
		
		$this->view->data['content'] = $content;
		return  $content;
	}

	function action_audio($actions=null){
		$id = (int)$actions[0];
		$model_gorod_news = new model_gorod_news();
		$model_news_audio_streams = $model_gorod_news->_model_news_audio_streams();
		$new = $model_gorod_news->getItem($id);
		$this->view->headers['title'] = 'Аудио очвучка | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Аудио очвучка новости"=>$this->url.'/news/photos/'.$id];
		$this->view->data['header'] = "Аудио очвучка новости: {$new['news_head']}";
		$audios = $model_gorod_news->_model_news_audio_streams()->getItemsWhere("`new_id`={$id}");
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$audio = $model_news_audio_streams->getItem($input);
				if(file_exists(APPDIR . $audio['audio'])) unlink(APPDIR . $audio['audio']);
				$model_news_audio_streams->Delete($input);
			}
		}
		
		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model_news_audio_streams->Update([ 'status'=>'1' ], $input);
			}
		}
		
		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model_news_audio_streams->Update([ 'status'=>'0' ], $input);
			}
		}
		$controls = [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
					];
		if(count($audios)==0)	{
			$controls[] = ["title"=>"Добавить", "href"=>"/admin/gorod/news/audio/add/{$id}", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"];
		}
			$admin = new AdminList(
				array(
					"model" => $model_news_audio_streams,
					"where" => "`new_id`='{$id}'",
					"order" => "`id` ASC",
					"multiple" => "true",
					"action" => '/admin/gorod/news/audio/'.$id,
					"controls" => $controls,
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"Id", "name"=>"id", "attrs"=>[], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/news/audio/edit/'.$id.'/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Файл", "name"=>"audio", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							if($cel){
								echo "<audio src=\"{$cel}\" controls width=\"120px\" align=left>";
							}
							else {
								echo "<audio src=\"https://xn--e1asq.xn--p1ai{$row["file"]}\" controls width=\"120px\" align=left>";
							}
						')],
						["title"=>"Заголовок", "name"=>"name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo "<a class=\"spf-link ajax-load\" data-center=\"false\" href=\"'.$this->url.'/news/audio/edit/'.$id.'/{$row["id"]}/\">{$cel}</a>";')],
						["title"=>"Краткое описание", "name"=>"descr", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
						["title"=>"Состояние", "name"=>"status", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','switch($cel){	case 0: echo "<span class=\"red\">Отключен</span>"; break; case 1: echo "<span class=\"green\">Включен</span>"; break; }')],
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
			
		$this->view->data['content'] = $content;	
	}
	
	function action_audio_add($actions=null){
		$id = (int)$actions[0];
		$model_gorod_news = new model_gorod_news();
		$model_news_audio_streams = $model_gorod_news->_model_news_audio_streams();
		$new = $model_gorod_news->getItem($id);
		$this->view->headers['title'] = 'Аудио очвучка | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Аудио очвучки новости"=>$this->url.'/news/audio/'.$id, "Добавление аудио очвучки новости"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/news/audio/add/{$id}"];
		$this->view->data['header'] = "Аудио очвучка новости: {$new['news_head']}";
		$audios = $model_gorod_news->_model_news_audio_streams()->getItemsWhere("`new_id`={$id}");
		
		if(isset($_POST['save'])){$this->view->notRender();
			$ext = getExtension1($_FILES['audio']['name']);
			$file_name = 'audio_'.(uniqid()).'.'.$ext;
			if(move_uploaded_file($_FILES['audio']['tmp_name'],  APPDIR . '/uploads/audio/news/'.$file_name)){
				$model_gorod_news->_model_news_audio_streams()->Insert([
					'name' => $_POST['name'],
					'descr' => $_POST['descr'],
					'audio' => '/uploads/audio/news/'.$file_name,
					'new_id' => $id,
					'news_id' => $new['news_id'],
					'our' => $new['our'],
					'adddate' => date("Y-m-d H:i:s"),
					'status' => ($_POST['status']=='on'?1:0),
				]);
				header('Location: /admin/gorod/news/audio/'.$id);
			}
		}
		
		$admin = new AdminPage(
			array(
				"model" => $model_news_audio_streams,
				"action" => '/admin/gorod/news/audio/add/'.$id,
				"fields" => [
					["title"=>"Заголовок", "name"=>"name", "attrs"=>[], "type"=>"text"],
					["title"=>"Подзоголовок", "name"=>"descr", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Файл", "name"=>"audio", "attrs"=>[], "accept"=>"audio/*", "type"=>"file"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "value"=>1, "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	function action_audio_edit($actions=null){
		$id = (int)$actions[0];
		$audio_id = (int)$actions[1];
		$model_gorod_news = new model_gorod_news();
		$model_news_audio_streams = $model_gorod_news->_model_news_audio_streams();
		$new = $model_gorod_news->getItem($id);
		$audio = $model_news_audio_streams->getItem($audio_id);
		
		$this->view->headers['title'] = 'Аудио очвучка | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Аудио очвучки новости"=>$this->url.'/news/audio/'.$id, "Изменение аудио очвучки новости"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/news/audio/edit/{$id}/{$audio_id}"];
		$this->view->data['header'] = "Аудио очвучка новости: {$new['news_head']}";
		
		if(isset($_POST['save'])){$this->view->notRender();
			
			$data = [
					'name' => $_POST['name'],
					'descr' => $_POST['descr'],
					'new_id' => $id,
					'news_id' => $new['news_id'],
					'our' => $new['our'],
					'adddate' => date("Y-m-d H:i:s"),
					'status' => ($_POST['status']=='on'?1:0),
				]; 
			if($_FILES['newaudio']){
				if($audio['audio']) unlink(APPDIR . $audio['audio']);
				$ext = getExtension1($_FILES['newaudio']['name']);
				$file_name = 'audio_'.(uniqid()).'.'.$ext;
				if(move_uploaded_file($_FILES['newaudio']['tmp_name'],  APPDIR . '/uploads/audio/news/'.$file_name)){
					$data['audio'] = '/uploads/audio/news/'.$file_name;
				}
			}
				$model_gorod_news->_model_news_audio_streams()->Update($data, $audio_id);
				header('Location: /admin/gorod/news/audio/'.$id);
		}
		if($audio['audio']) {
			$src = $audio['audio'];
		}
		elseif($audio['file']){
			$src = 'https://xn--e1asq.xn--p1ai'.$audio['audio'];
		}
		
		$admin = new AdminPage(
			array(
				"model" => $model_news_audio_streams,
				"action" => "/admin/gorod/news/audio/edit/{$id}/{$audio_id}",
				"item" => $audio,
				"fields" => [
					["title"=>"Заголовок", "name"=>"name", "attrs"=>[], "type"=>"text"],
					["title"=>"Подзоголовок", "name"=>"descr", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Файл", "name"=>"audio", "attrs"=>[], "value"=>$src, "type"=>"audio"],
					["title"=>"Новый файл", "name"=>"newaudio", "attrs"=>[], "accept"=>"audio/*", "type"=>"file"],
					["title"=>"Состояние", "name"=>"status", "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);
		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}

	function action_ingazeta($actions=null){
		$id = (int)$actions[0];
		$model_gorod_news = new model_gorod_news();
		$model_adventures = new model_adventures();
		if($id) {
			//$news = $model_gorod_news->getItemsWhere("``='{$id}'");
			$num = $model_adventures->model_gazeta_nums()->getItem($id);
			$news = $model_gorod_news->get("`{$model_gorod_news->gettablename()}`.*")->from([$model_gorod_news, $model_gorod_news->model_news_in_gazeta()])->where("`{$model_gorod_news->gettablename()}`.`id`=`new_id` AND `num_id`='{$id}'")->commit();

			$this->view->headers['title'] = 'Новости в газету | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Город24"=>$this->url, "Новости"=>$this->url.'/news/', "Новости в газету"=>$this->url.'/news/ingazeta/'.$id];
			$this->view->data['header'] = "Новости в газету: №{$num['num']} от {$num['date']}";
			$content = '';
			foreach($news as $new){
				//$news_body = str_replace("","<br>",$new['news_body']);
				//$news_body = strip_tags($new['news_body']);
				$news_body = ($new['news_body']);
				$body = $news_body;
				$content .= "<div>
					<h4><strong>{$new['news_head']}</strong></h4>
					<p>{$new['news_lid']}</p>
					<p>{$body}</p>
				</div><hr/>";
			}
			
		}
		
		
		
			
		$this->view->data['content'] = $content;	
		
	}
	
	
}