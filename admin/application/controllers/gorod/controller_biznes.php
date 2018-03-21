<?php
require_once( __DIR__ ."/controller_index.php");
class controller_biznes extends controller_index
{
	function __construct(){
		parent::__construct();
		$this->model_biznes = new model_feo_biznes();
		$this->model_basket = new model_basket();
	}
	
	function action_index($actions=null){
		$this->action_preds($actions);
	}
	
	/********** Фирмы **********/
	function action_preds($actions=null){
		$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/'];
		$this->view->data['header'] = "Бизнес каталог";
		
		$page = (int)$_REQUEST['page']; $page = ($page?$page:1);
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$this->model_biznes->register_query()->Delete($input);
			}
		}
		$where = "1";
		$model_cols = "
		*, (
									SELECT `id` 
									FROM  `main`.`Adv_portal_vip` 
									WHERE  `id_pred` =`pred`.`id`
									LIMIT 1
								) as `id_vip`
								,
								(
									SELECT COUNT(  `id` ) 
									FROM  `new_feo_ua`.`adv_adventures` 
									WHERE  `vip_id` =`id_vip`
									AND   `vip` =  '1'
								) as `col_on`
								,
								(
									SELECT COUNT(  `id` ) 
									FROM  `new_feo_ua`.`adv_adventures_off` 
									WHERE  `vip_id` =`id_vip`
									AND   `vip` =  '1'
								) as `col_off`
								,
								(
									SELECT COUNT(  `id` ) 
									FROM  `new_feo_ua`.`adv_adventures_off` 
									WHERE  `user_phone` =`pred`.`phones_contakt`
								) as `col_off_p`
								,
								(
									SELECT COUNT(  `id` ) 
									FROM  `new_feo_ua`.`adv_adventures_off` 
									WHERE  `user_email` =`pred`.`mail_contakt`
								) as `col_off_e`
								,
								(
									SELECT COUNT(  `id` ) 
									FROM  `new_feo_ua`.`adv_adventures` 
									WHERE  `user_phone` =`pred`.`phones_contakt`
								) as `col_on_p`
								,
								(
									SELECT COUNT(  `id` ) 
									FROM  `new_feo_ua`.`adv_adventures` 
									WHERE  `user_email` =`pred`.`mail_contakt`
								) as `col_on_e` ,(
									SELECT MAX(  `date` ) 
									FROM  `main`.`buhg_agent_otchet_day` 
									WHERE  `id_f` =`pred`.`id_buhg`
								) as `date_ot`
								,(
									SELECT MAX(  `date_acc` ) 
									FROM  `main`.`buhg_acc_212` 
									WHERE  `id_f` =`pred`.`id_buhg`
								) as `date_acc`
								,(
									SELECT COUNT(  `id` ) 
									FROM  `main`.`pred_photo` 
									WHERE  `pred_photo`.`pid` =`pred`.`id` AND `type`=0
								) as `logos`
								,(
									SELECT COUNT(  `id` ) 
									FROM  `main`.`pred_photo` 
									WHERE  `pred_photo`.`pid` =`pred`.`id` AND `type`=1
								) as `fasads`
								,(
									SELECT COUNT(  `id` ) 
									FROM  `main`.`pred_photo` 
									WHERE  `pred_photo`.`pid` =`pred`.`id` AND `type`=2
								) as `interers`
		";
		if($_REQUEST['search']){
			$search = trim($_REQUEST['search']);
			$where .= "
				AND ( 
					`name` LIKE '%{$search}%'
					OR `name_kat` LIKE '%{$search}%'
					OR `menag` LIKE '%{$search}%'
					OR `adr_f` LIKE '%{$search}%'
					OR `adr_y` LIKE '%{$search}%'
					OR `phones` LIKE '%{$search}%'
					)
			";
		}
		
		
		$admin = new AdminList(
			array(
				"model" => $this->model_biznes,
				"where" => $where,
				"model_cols" => $model_cols,
				"order" => "`oplata` DESC",
				"multiple" => "true",
				"action" => $this->url."/biznes/preds/?page={$page}&search={$search}",
				"controls" => [
					["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
					["title"=>"Добавить", "href"=>$this->url."/biznes/preds/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
					["title"=>"Поиск", "name"=>"search", "attrs"=>[], "type"=>"search", "value"=>$search],
				],
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"ID", "name"=>"id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Название", "name"=>"name", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
						switch($row["on_off"]){
							case 0: echo "<div style=\"border:1px solid black;background-color:red;width:20px;height:20px;margin:0 10px 0 0;float: left; \" title=\"Понашим данным фтрма не работает\"></div>"; break;
							case 1: echo "<div style=\"border:1px solid black;background-color:green;width:20px;height:20px;margin:0 10px 0 0;float: left; \" title=\"Фирма включена\"></div>"; break;
							case 2: echo "<div style=\"border:1px solid black;background-color:grey;width:20px;height:20px;margin:0 10px 0 0;float: left; \" title=\"Фирма отказалась поддерживать информацию о себе\"></div>"; break;
							case 3: echo "<div style=\"border:1px solid black;background-color:yellow;width:20px;height:20px;margin:0 10px 0 0;float: left; \" title=\"Показываються канкуренты\"></div>"; break;
						}
						echo "<div style=\"text-align:left;\">{$cel}</div>";
					')],
					["title"=>"Оплата", "name"=>"oplata", "attrs"=>[ "data-breakpoints"=>"xs sm", "style"=>"width:90px;"], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Объявления", "name"=>"read", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo "
							<font color=\"green\">".$row["col_on"]."</font> / <font color=\"red\">".$row["col_off"]."</font> / 
							<font color=\"black\">".$row["id_vip"]."</font>
							<font color=\"green\">(".$row["col_on_p"].")--(".$row["col_on_e"].")</font> / 
							<font color=\"red\">(".$row["col_off_p"].")--(".$row["col_off_e"].")</font>
							";
					')],
					["title"=>"Фотографий", "name"=>"read", "attrs"=>[ "data-breakpoints"=>"xs sm", "style"=>"width:130px;" ], "content"=>create_function('$cel,$row','
						echo "<strong>Логотипов: </strong><font color=\"".($row["logos"]==0?"red":"green")."\">".$row["logos"]."</font><br>";
						echo "<strong>Фасадов: </strong><font color=\"".($row["fasads"]==0?"red":"green")."\">".$row["fasads"]."</font><br>";
						echo "<strong>Интерьеров: </strong><font color=\"".($row["interers"]==0?"red":"green")."\">".$row["interers"]."</font><br>";
					')],
					["title"=>"Последняя дата", "name"=>"open", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							if ($row["id_buhg"]>0){
								if($row["id_buhg"]!=777){ 
									$reference_date_ot = strtotime($row["date_ot"]);
									$reference_date_acc = strtotime($row["date_acc"]);
									$now = strtotime("now");
									$date_d_ot = floor (($now - $reference_date_ot)/86400);
									$date_d_acc = floor (($now - $reference_date_acc)/86400);
									if ($date_d_ot>=30){$color_ot="red";}else{$color_ot="green";}
									if ($date_d_acc>=30){$color_acc="red";}else{$color_acc="green";}

									$show_date=date("Y-m-d");
									echo "<font color=\"".$color_ot."\">".$row["date_ot"]."</font>
										<br><font color=\"".$color_acc."\">".$row["date_acc"]."</font>";
								}else{
									print "<font color=\"blue\">Наша</font>";
								}
							}else{echo "<font color=\"red\">Фирма не привязана</font>";}
					')],
					
					["title"=>"Ссылки", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
						echo "<div><a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/biznes/preds/photos/{$row["id"]}/?b_page='.$page.'&search='.$search.'\">Фото</a></div>";
						echo "<div><a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/biznes/preds/address/{$row["id"]}/?b_page='.$page.'&search='.$search.'\">Адреса</a></div>";
						echo "<div><a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/biznes/preds/tovs/{$row["id"]}/?b_page='.$page.'&search='.$search.'\">Товары</a></div>";
					')],
					["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
						echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/biznes/preds/edit/{$row["id"]}/?page='.$page.'&search='.$search.'\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
					')]
				],
			)
		);
		/**/
		$result .= $admin;
		$content = $result;
		$this->view->data['content'] = $content;
	}
	
	function action_preds_add($actions=null){ $this->action_preds_edit($actions); }
	
	function action_preds_edit($actions=null){
		$id = (int)$actions[0];
		$page = (int)$_REQUEST['page']; $page = ($page?$page:1);
		$search = trim($_REQUEST['search']);
		$model_buhg_firms = new model_buhg_firms();
		$model_cities = new model_cities();
		if($id){
			$item = $this->model_biznes->getItem($id);
		
			$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/', "Редактирование"=>$this->url.'/biznes/preds/edit/'];
			$this->view->data['header'] = "Редактирование Фирмы";
			$action = "{$this->url}/biznes/preds/edit/{$id}/?page={$page}&search={$search}";
		}
		else {
			$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/', "Добавление"=>$this->url.'/biznes/preds/add/'];
			$this->view->data['header'] = "Добавление Фирмы";
			$action = "{$this->url}/biznes/preds/add/?page={$page}&search={$search}";
			$item=[];
			$item['bas_date'] = date("Y-m-d");
		}
		if(isset($_REQUEST['save'])){
			$this->view->notRender();
			
			$otr = ';'.implode(';', $_REQUEST['otr']).';';
			$city = $model_cities->getItem($_REQUEST['city_id']);
			$data = [
				'name' => trim($_REQUEST['name']),
				'name_kat' => trim($_REQUEST['name_kat']),
				'adr_f' => trim($_REQUEST['adr_f']),
				'adr_y' => trim($_REQUEST['adr_y']),
				'phones' => trim($_REQUEST['phones']),
				'menag' => trim($_REQUEST['menag']),
				'status' => trim($_REQUEST['status']),
				'show_in_app' => (trim($_REQUEST['show_in_app'])=='on'?1:0),
				'activ' => trim($_REQUEST['activ']),
				'activ_gaz' => trim($_REQUEST['activ_gaz']),
				'otr' => $otr,
				'email' => trim($_REQUEST['email']),
				'web' => trim($_REQUEST['web']),
				'oplata' => trim($_REQUEST['oplata']),
				'oplata_g24' => trim($_REQUEST['oplata_g24']),
				'login' => trim($_REQUEST['login']),
				'passw' => trim($_REQUEST['passw']),
				'katalog' => trim($_REQUEST['katalog']),
				'vh' => (trim($_REQUEST['vh'])=='on'?1:0),
				'mesto' => trim($_REQUEST['mesto']),
				'id_buhg' => trim($_REQUEST['id_buhg']),
				'on_off' => trim($_REQUEST['on_off']),
				'not_in_gazeta' => (trim($_REQUEST['not_in_gazeta'])=='on'?1:0),
				'icq' => trim($_REQUEST['icq']),
				'skype' => trim($_REQUEST['skype']),
				'vkcom' => trim($_REQUEST['vkcom']),
				'twitter' => trim($_REQUEST['twitter']),
				'fecebook' => trim($_REQUEST['fecebook']),
				'odnoklassniki' => trim($_REQUEST['odnoklassniki']),
				'work' => trim($_REQUEST['work']),
				'lunch' => trim($_REQUEST['lunch']),
				'satarday' => trim($_REQUEST['satarday']),
				'sunday' => trim($_REQUEST['sunday']),
				'sunday_work' => trim($_REQUEST['sunday_work']),
				'priem' => trim($_REQUEST['priem']),
				'name_en' => trim($_REQUEST['name_en']),
				'name_kat_en' => trim($_REQUEST['name_kat_en']),
				'adr_f_en' => trim($_REQUEST['adr_f_en']),
				'adr_y_en' => trim($_REQUEST['adr_y_en']),
				'activ_en' => trim($_REQUEST['activ_en']),
				'town' => $city['city_title'],
				'city_id' => trim($_REQUEST['city_id']),
				'h_redir' => trim($_REQUEST['h_redir']),
				'jump_to_site' => trim($_REQUEST['jump_to_site']),
				'feo_domen' => trim($_REQUEST['feo_domen']),
				'yandex' => trim($_REQUEST['yandex']),
				'google' => trim($_REQUEST['google']),
				'url' => trim($_REQUEST['url']),
				'url_ru' => trim($_REQUEST['url_ru']),
				'fio_contakt' => trim($_REQUEST['fio_contakt']),
				'phones_contakt' => trim($_REQUEST['phones_contakt']),
				'mail_contakt' => trim($_REQUEST['mail_contakt']),
				'vip_code' => trim($_REQUEST['vip_code']),
			];
			if($id){
				$data['id'] = $id;
			}
			else {
				$data['viz'] = 0;
				$data['url'] = '';
				$data['url_ru'] = '';
				$data['rating'] = 0;
				$data['rating_tel'] = 0;
				$data['rating_ob'] = 0;
				$data['name_ua'] = '';
				$data['name_kat_ua'] = '';
				$data['adr_f_ua'] = '';
				$data['adr_y_ua'] = '';
				$data['activ_ua'] = '';
			}
			$new_id = $this->model_biznes->register_query()->InsertUpdate($data);
			if(empty($data['url']) or empty($data['url_ru'])){
				$update_data = ['id'=>$new_id];
				if(empty($data['url'])){
					$url = $new_id . 'p-'.$this->new_url($_REQUEST['name']) . ".html";
					$update_data['url'] = $url;
				}
				if(empty($data['url_ru'])){
					$url = $new_id . 'п-'.$this->new_url_ru($_REQUEST['name']) . ".html";
					$update_data['url_ru'] = $url;
				}
				$this->model_biznes->register_query()->InsertUpdate($update_data);
			}
			header("Location: {$this->url}/biznes/preds/?page={$page}&search={$search}");
		}
		$otrs = [];
		$otrs = $this->model_biznes->otr()->getItemsWhere("sub_otr like 'main'", "name", null, null, "`id` as `value`, `name` as `label`");
		foreach($otrs as $i=>$otr){
			$items = $this->model_biznes->otr()->getItemsWhere("sub_otr like '%;{$otr['value']};%'", "name", null, null, "`id` as `value`, `name` as `label`");
			$otrs[$i]['items'] = $items;
		}
		$otr = [];
		$_otr = explode(';', $item['otr']);
		foreach($_otr as $o){ if(!empty($o)){ $otr[]=(int)$o;}}
		
		if($item['id_buhg']){ $firm = $model_buhg_firms->getItem($item['id_buhg']); }
		
		$admin = new AdminPage(
			array(
				"model" => $this->model_biznes,
				"item" => $item,
				"action" => $action,
				"fields" => [
					["title"=>"Название фирмы", "name"=>"name", "attrs"=>[], "type"=>"text"],
					["title"=>"Название фирмы в каталоге", "name"=>"name_kat", "attrs"=>[], "type"=>"text"],
					["title"=>"Адрес физический", "name"=>"adr_f", "attrs"=>[], "type"=>"text"],
					["title"=>"Адрес юридический", "name"=>"adr_y", "attrs"=>[], "type"=>"text"],
					["title"=>"Телефоны", "name"=>"phones", "attrs"=>[], "type"=>"text"],
					["title"=>"Руководитель", "name"=>"menag", "attrs"=>[], "type"=>"text"],
					["title"=>"Пакет", "name"=>"status", "attrs"=>[], "type"=>"select", "items"=>[ ["value"=>3, "label"=>"Полный комплекс"], ["value"=>1, "label"=>"Только телефон"], ["value"=>5, "label"=>"Интернет"], ["value"=>4, "label"=>"Внесение в базу"], ]],
					["title"=>"Показывать в приложении", "name"=>"show_in_app", "attrs"=>[], "type"=>"switch"],
					["title"=>"Описание", "name"=>"activ", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Описание для газеты ФЕО.РФ", "name"=>"activ_gaz", "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Отрасль", "name"=>"otr", "attrs"=>[], "type"=>"select", "multiple"=>true, "value"=>$otr, "items"=>$otrs],
					["title"=>"Электронный адрес", "name"=>"email", "attrs"=>[], "type"=>"text"],
					["title"=>"Сайт", "name"=>"web", "attrs"=>[], "type"=>"text"],
					["title"=>"Оплата до", "name"=>"oplata", "attrs"=>[], "type"=>"date"],
					["title"=>"Оплата Город 24", "name"=>"oplata_g24", "attrs"=>[], "type"=>"date"],
					["title"=>"Имя для входа в консоль упаравления", "name"=>"login", "attrs"=>[], "type"=>"text"],
					["title"=>"Пароль для входа в консоль упаравления", "name"=>"passw", "attrs"=>[], "type"=>"text"],
					["title"=>"Номер ближайшего каталога в ктором будет размещена фирма", "name"=>"katalog", "attrs"=>[], "type"=>"text"],
					["title"=>"Влючен новый способ отображения страницы", "name"=>"vh", "attrs"=>[], "type"=>"switch"],
					["title"=>"Место отображения на службе", "name"=>"mesto", "attrs"=>[], "type"=>"select", "items"=>[ ["value"=>1, "label"=>"1"], ["value"=>2, "label"=>"2"], ["value"=>3, "label"=>"3"], ["value"=>9, "label"=>"Обычное"],  ] ],
					["title"=>"Фирма из бухгалтерии", "name"=>"id_buhg", "attrs"=>[], "type"=>"autocomplete", "label"=>$firm['name'], "source"=>$GLOBALS['CONFIG']['HTTP_HOST']."/admin/gorod/json/buhg/firms"],
					["title"=>"Состояние", "name"=>"on_off", "attrs"=>[], "type"=>"select", "items"=>[ ["value"=>0, "label"=>"Выключена"], ["value"=>1, "label"=>"Влючена"], ["value"=>2, "label"=>"Оказ от подачи информации"], ["value"=>3, "label"=>"Отрываются конкуренты"],  ] ],
					["title"=>"Не печатается в газету", "name"=>"not_in_gazeta", "attrs"=>[], "type"=>"switch"],
					["title"=>"Номер ICQ", "name"=>"icq", "attrs"=>[], "type"=>"text"],
					["title"=>"Skype", "name"=>"skype", "attrs"=>[], "type"=>"text"],
					["title"=>"vk.com", "name"=>"vkcom", "attrs"=>[], "type"=>"text"],
					["title"=>"twitter", "name"=>"twitter", "attrs"=>[], "type"=>"text"],
					["title"=>"fecebook", "name"=>"fecebook", "attrs"=>[], "type"=>"text"],
					["title"=>"Одноклассники", "name"=>"odnoklassniki", "attrs"=>[], "type"=>"text"],
					
					["title"=>"Режим работы (09:30:00;16:00:00)", "name"=>"work", "attrs"=>[], "type"=>"text"],
					["title"=>"Обед (12:00:00;13:00:00)", "name"=>"lunch", "attrs"=>[], "type"=>"text"],
					["title"=>"Режим работы в субботу", "name"=>"satarday", "attrs"=>[], "type"=>"text"],
					["title"=>"Выходные дни", "name"=>"sunday", "attrs"=>[], "type"=>"text"],
					["title"=>"Режим работы в Воскресенье", "name"=>"sunday_work", "attrs"=>[], "type"=>"text"],
					["title"=>"Приемные дни (Пн.;08:25:00;09:30:00|Сб.09:30:00;12:00:00)", "name"=>"priem", "attrs"=>[], "type"=>"text"],
				
					["title"=>"Название (Английский)", "name"=>"name_en", "attrs"=>[], "type"=>"text"],
					["title"=>"Название в каталоге (Английский)", "name"=>"name_kat_en", "attrs"=>[], "type"=>"text"],
					["title"=>"Адрес физический (Английский)", "name"=>"adr_f_en", "attrs"=>[], "type"=>"text"],
					["title"=>"Адрес юридический (Английский)", "name"=>"adr_y_en", "attrs"=>[], "type"=>"text"],
					["title"=>"Сфера деятельности (Английский)", "name"=>"activ_en", "attrs"=>[], "type"=>"mediumText"],
					
					["title"=>"Город", "name"=>"city_id", "attrs"=>[], "type"=>"select", "items"=>$model_cities->getItemsWhere("`in_news`=1", "`city_title` ASC", null, null, "`city_id` as `value`, `city_title` as `label`")],
					
					["title"=>"Скрытый редирект", "name"=>"h_redir", "attrs"=>[], "type"=>"text"],
					["title"=>"Если 1, то из каталога kafa-info сразу открываеться сайт (без промежуточной страницы-визитки)", "name"=>"jump_to_site", "attrs"=>[], "type"=>"select", "items"=>[ ["value"=>0, "label"=>"Отключен редирект"], ["value"=>1, "label"=>"Влючен редирект"] ] ],
					["title"=>"Домен на feo.ua", "name"=>"feo_domen", "attrs"=>[], "type"=>"text"],
					["title"=>"yandex", "name"=>"yandex", "attrs"=>[], "type"=>"text"],
					["title"=>"google", "name"=>"google", "attrs"=>[], "type"=>"text"],
					["title"=>"feo_url", "name"=>"url", "attrs"=>[], "type"=>"text"],
					["title"=>"фео.рф_url", "name"=>"url_ru", "attrs"=>[], "type"=>"text"],
					["title"=>"ФИО (контактное лицо)", "name"=>"fio_contakt", "attrs"=>[], "type"=>"text"],
					["title"=>"Телефон (контактное лицо)", "name"=>"phones_contakt", "attrs"=>[], "type"=>"text"],
					["title"=>"E-mail (контактное лицо)", "name"=>"mail_contakt", "attrs"=>[], "type"=>"text"],
					["title"=>"VIP-code (если нет, стандарный)", "name"=>"vip_code", "attrs"=>[], "type"=>"text"],
					
					
					
					
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	/********** Фото Фирмы **********/
	function action_preds_photos($actions=null){
		$id = (int)$actions[0];
		if($id){
		$item = $this->model_biznes->getItem($id);
		$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/', $item['name']=>$this->url.'/biznes/preds/edit/'.$id, "Фото фирмы \"{$item['name']}\""=>$this->url.'/biznes/preds/photos/'.$id, ];
		$this->view->data['header'] = "Бизнес каталог (Фото фирмы \"{$item['name']}\")";
		
		$page = (int)$_REQUEST['b_page']; $page = ($page?$page:1);
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$this->model_biznes->photo()->register_query()->Delete($input);
			}
		}
		$where = "1 AND `pid`='{$id}'";
		$model_cols = "*, (SELECT CONCAT(`pred_map`.`adr`, ' (', `pred_map`.x, ', ', `pred_map`.y, ')') FROM `site_21200`.`pred_map` WHERE `pred_photo`.`coord_id`=`pred_map`.`id`) as adrxy";
		
		$items =  $this->model_biznes->photo()->get($model_cols)->where($where)->order("`id` DESC")->commit();
		$admin = new AdminList(
			array(
				"items" => $items,
				"multiple" => "true",
				"action" => $this->url."/biznes/preds/photos/{$id}/?b_page={$page}&search={$search}",
				"controls" => [
					["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
					["title"=>"Добавить", "href"=>$this->url."/biznes/preds/photos/add/{$id}/?b_page={$page}&search={$search}", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
				],
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"ID", "name"=>"id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Фото", "name"=>"file", "attrs"=>[ "data-breakpoints"=>"xs sm", "style"=>"width:90px;"], "content"=>create_function('$cel,$row','
						if(strpos($cel, "/upload/pred_photos/")!==false){
							echo "<div><img src=\"https://фео.рф".$cel."\" style=\"max-width:200px;max-height:200px;\" /></div>";
						}
						if(strpos($cel, "/uploads/image/biznes/preds/")!==false){
							echo "<div><img src=\"".$cel."\" style=\"max-width:200px;max-height:200px;\" /></div>";
						}
						elseif(strpos($cel, "http://21200.ru/pred_fasads/")!==false or strpos($cel, "https://21200.ru/pred_fasads/")!==false){
							$link = str_replace("http://","https://",$cel);
							echo "<div><img src=\"".$link."\" style=\"max-width:200px;max-height:200px;\" /></div>";
						}
					')],
					["title"=>"Адрес (координаты)", "name"=>"adrxy", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Тип", "name"=>"type", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
						switch($cel){
							case 0: echo "Логотип"; break;
							case 1: echo "Фасад"; break;
							case 2: echo "Интерьер"; break;
						}
					')],
					["title"=>"Дата редактирования", "name"=>"date_update", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					
					["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
						echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/biznes/preds/photos/edit/'.$id.'/{$row["id"]}/?b_page='.$page.'&search='.$search.'\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
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
	
	function action_preds_photos_add($actions=null){ $this->action_preds_photos_edit($actions); }
	
	function action_preds_photos_edit($actions=null){
		$pid = (int)$actions[0];
		$id = (int)$actions[1];
		$page = (int)$_REQUEST['b_page']; $page = ($page?$page:1);
		$search = trim($_REQUEST['search']);
		$model_buhg_firms = new model_buhg_firms();
		$model_cities = new model_cities();
		$pred = $this->model_biznes->getItem($pid);
		if($id){
			$item = $this->model_biznes->photo()->register_query()->getItem($id);
		
			$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/', "Фирма {$pred['name']}"=>$this->url.'/biznes/preds/edit/'.$pid, "Фото фирмы {$pred['name']}"=>$this->url.'/biznes/preds/photos/'.$pid, "Редактирование"=>$this->url.'/biznes/preds/photos/edit/'.$pid.'/'.$id];
			$this->view->data['header'] = "Редактирование фото фирмы {$pred['name']}";
			$action = "{$this->url}/biznes/preds/photos/edit/{$pid}/{$id}/?b_page={$page}&search={$search}";
		}
		else {
			$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/', "Фирма {$pred['name']}"=>$this->url.'/biznes/preds/edit/'.$pid, "Фото фирмы {$pred['name']}"=>$this->url.'/biznes/preds/photos/'.$pid, "Добавление"=>$this->url.'/biznes/preds/photos/add/'.$pid];
			$this->view->data['header'] = "Добавление Фото";
			$action = "{$this->url}/biznes/preds/photos/add/{$pid}/?b_page={$page}&search={$search}";
			$item=[];
			$item['bas_date'] = date("Y-m-d");
		}
		
		if(strpos($item['file'], "/upload/pred_photos/")!==false){
			$list = explode('/', $item['file']);
			$item['file_name'] = end($list);
			$item['src'] = "/uploads/image/biznes/preds/{$item['file_name']}";
		}
		elseif(strpos($item['file'], "/uploads/image/biznes/preds/")!==false){
			$item['src'] = $item['file'];
		}
		elseif(strpos($item['file'], "http://21200.ru/pred_fasads/")!==false or strpos($item['file'], "https://21200.ru/pred_fasads/")!==false){
			$link = str_replace("http://","https://",$item['file']);
			$item['src'] = $link;
		}
		
		if(isset($_REQUEST['save'])){
			$errors = [];
			$this->view->notRender();
			$data = [
				'name' => $_REQUEST['name'],
				'type' => (int)$_REQUEST['type'],
				'pid' => $pid,
				'coord_id' => (int)$_REQUEST['coord_id'],
				'date_update' => date("Y-m-d H:i:s"),
			];
			if($id){
				$data['id'] = $id;
			}
			else {
				$data['date_create'] = $data['date_update'];
			}
			if(empty($data['coord_id'])){
				$errors[]="Вы не выбрали Адрес. Фото должно быть обязательно привязаны к адрессу. Если список адресов еще пуст, то добавьте новый адрес <a href=\"{$this->url}/biznes/preds/address/add/{$id}?b_page={$page}&search={$search}\">здесь</a>"; 
			}
			if(count($errors)==0){
			if($_FILES['newfile']){
				if($item['file']){
					if(strpos($item['src'], "https://21200.ru/pred_fasads/")===false){
						@unlink(APPDIR . $item['src']);
					}
				}
				$ext = getExtension1($_FILES['newfile']['name']);
				$file_name = $pid.'_'.(uniqid()).'.'.$ext;
				if(move_uploaded_file($_FILES['newfile']['tmp_name'],  APPDIR . '/uploads/image/biznes/preds/'.$file_name)){
					$data['file'] = '/uploads/image/biznes/preds/'.$file_name;
				}
			}
			//var_dump($data);
			$new_id = $this->model_biznes->photo()->register_query()->InsertUpdate($data);
			
			header("Location: {$this->url}/biznes/preds/photos/{$pid}/?b_page={$page}&search={$search}");
			}
			else {
				$this->view->yesRender();
				foreach($errors as $error){
					$content .= "<h3 style='color:red;'>{$error}</h3>";
				}
			}
		}
		
		

		$admin = new AdminPage(
			array(
				"model" => $this->model_biznes->photo(),
				"item" => $item,
				"action" => $action,
				"fields" => [
					["title"=>"Наименование", "name"=>"name", "attrs"=>[], "type"=>"text"],
					["title"=>"Тип", "name"=>"type", "attrs"=>[], "type"=>"select", "items"=>[ ['value'=>0, 'label'=>'Логотип'], ['value'=>1, 'label'=>'Фасад'], ['value'=>2, 'label'=>'Интерьер'] ]],
					["title"=>"Адрес", "name"=>"coord_id", "attrs"=>[], "type"=>"select", "items"=>$this->model_biznes->coords()->get("`id` as `value`, `adr` as `label`")->where("`pid`='{$pid}'")->order("`id` DESC")->commit()],
					["title"=>"Фото", "name"=>"src", "attrs"=>[], "type"=>"image", "width"=>"300px" ],
					["title"=>"Новый файл", "name"=>"newfile", "attrs"=>[], "accept"=>"image/*", "type"=>"file"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	
	/********** Адреса Фирмы **********/
	function action_preds_address($actions=null){
		$id = (int)$actions[0];
		if($id){
		$item = $this->model_biznes->getItem($id);
		$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/', $item['name']=>$this->url.'/biznes/preds/edit/'.$id, "Адреса фирмы \"{$item['name']}\""=>$this->url.'/biznes/preds/address/'.$id, ];
		$this->view->data['header'] = "Бизнес каталог (Адреса фирмы \"{$item['name']}\")";
		
		$page = (int)$_REQUEST['b_page']; $page = ($page?$page:1);
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$this->model_biznes->coords()->Delete($input);
			}
		}
		$where = "1 AND `pid`='{$id}'";
		$model_cols = "*";
		
		$items =  $this->model_biznes->coords()->get($model_cols)->where($where)->order("`id` DESC")->commit();
		$admin = new AdminList(
			array(
				"model" => $this->model_biznes->coords(),
				"items" => $items,
				"multiple" => "true",
				"action" => $this->url."/biznes/preds/address/{$id}?b_page={$page}&search={$search}",
				"controls" => [
					["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
					["title"=>"Добавить", "href"=>$this->url."/biznes/preds/address/add/{$id}?b_page={$page}&search={$search}", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
				],
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"ID", "name"=>"id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Адрес", "name"=>"adr", "align"=>"left", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Координаты", "name"=>"adrxy`", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $row["x"].", ".$row["y"];')],
					["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
						echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/biznes/preds/address/edit/{$row["pid"]}/{$row["id"]}/?b_page='.$page.'&search='.$search.'\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
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
	
	function action_preds_address_add($actions=null){ $this->action_preds_address_edit($actions); }
	
	function action_preds_address_edit($actions=null){
		$pid = (int)$actions[0];
		$id = (int)$actions[1];
		$page = (int)$_REQUEST['b_page']; $page = ($page?$page:1);
		$search = trim($_REQUEST['search']);
		$model_buhg_firms = new model_buhg_firms();
		$model_cities = new model_cities();
		$pred = $this->model_biznes->getItem($pid);
		if($id){
			$item = $this->model_biznes->coords()->getItem($id);
		
			$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/', "Фирма {$pred['name']}"=>$this->url.'/biznes/preds/edit/'.$pid, "Адреса фирмы {$pred['name']}"=>$this->url.'/biznes/preds/address/'.$pid, "Редактирование"=>$this->url.'/biznes/preds/address/edit/'.$pid.'/'.$id];
			$this->view->data['header'] = "Редактирование адреса Фирмы {$pred['name']}";
			$action = "{$this->url}/biznes/preds/address/edit/{$pid}/{$id}/?b_page={$page}&search={$search}";
		}
		else {
			$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/', "Добавление"=>$this->url.'/biznes/preds/add/'];
			$this->view->data['header'] = "Добавление Фирмы";
			$action = "{$this->url}/biznes/preds/address/add/{$pid}?b_page={$page}&search={$search}";
			$item=[];
			$item['bas_date'] = date("Y-m-d");
		}
		
		if(isset($_REQUEST['save'])){
			$this->view->notRender();
			$data = [
				'y' => $_REQUEST['coord-longitude'],
				'x' => $_REQUEST['coord-latitude'],
				'adr' => trim($_REQUEST['coord-address']),
			];
			if($id){
				$data['id'] = $id;
			}
			else {
				$data['pid'] = $pid;
				$data['img'] = '';
			}
			$new_id = $this->model_biznes->coords()->register_query()->InsertUpdate($data);
			header("Location: {$this->url}/biznes/preds/address/{$pid}/?b_page={$page}&search={$search}");
		}

		$admin = new AdminPage(
			array(
				"model" => $this->model_biznes->coords(),
				"item" => $item,
				"action" => $action,
				"fields" => [
					["title"=>"Адрес и координаты", "name"=>"coord", "attrs"=>[], "longitude"=>$item['y'], "latitude"=>$item['x'], "address"=>$item['adr'], "type"=>"map"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	/********** Товары Фирмы **********/
	function action_preds_tovs($actions=null){
		$id = (int)$actions[0];
		if($id){
		$item = $this->model_biznes->getItem($id);
		$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/', $item['name']=>$this->url.'/biznes/preds/edit/'.$id, "Товары и услуги фирмы \"{$item['name']}\""=>$this->url.'/biznes/preds/tovs/'.$id, ];
		$this->view->data['header'] = "Бизнес каталог (Товары и услуги фирмы \"{$item['name']}\")";
		
		$page = (int)$_REQUEST['b_page']; $page = ($page?$page:1);
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$this->model_biznes->tov()->register_query()->Delete($input);
			}
		}
		$where = "1 AND `id_pr`='{$id}'";
		$model_cols = "*";
		
		$items =  $this->model_biznes->tov()->get($model_cols)->where($where)->order("`id`")->commit();
		$admin = new AdminList(
			array(
				"model" => $this->model_biznes->tov(),
				"items" => $items,
				"multiple" => "true",
				"action" => $this->url."/biznes/preds/tovs/{$id}/?b_page={$page}&search={$search}",
				"controls" => [
					["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
					["title"=>"Добавить", "href"=>$this->url."/biznes/preds/tovs/add/{$id}", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
				],
				"attrs" => ["class"=>"table-adapt"],
				"columns" => [
					["title"=>"ID", "name"=>"id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Название", "name"=>"name", "align"=>"left", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Цена", "name"=>"price", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Фото", "name"=>"photo`", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
							if($row["photo"]!=0){
							echo "<a href=\"http://feo.ua/vh/vh_getContent.php?id=".$row["photo"]."\" target=\"_blank\" alt=\"".$row["name"]."\">
									<img src=\"http://feo.ua/vh/vh_getContent.php?id=".$row["photo"]."\" width=\"100px\" alt=\"".$row["name"]."\" title=\"".$row["name"]."\" />
								</a>";
							}
							else{
								echo "<font color=\"RED\">Нет фото</font>";
							}
					')],
					["title"=>"Дата редактирования", "name"=>"datelast", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','echo $cel;')],
					["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
						echo "<a class=\"ajax-load icons-wrap\" data-ajax=\"true\" data-center=\"false\" href=\"'.$this->url.'/biznes/preds/tovs/edit/'.$id.'/{$row["id"]}/?b_page='.$page.'&search='.$search.'\"><em class=\"fa fa-pencil fa-2\" title=\"Редактирование\"></em></a>";
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
	
	function action_preds_tovs_add($actions=null){ $this->action_preds_tovs_edit($actions); }
	
	function action_preds_tovs_edit($actions=null){
		$pid = (int)$actions[0];
		$id = (int)$actions[1];
		$page = (int)$_REQUEST['b_page']; $page = ($page?$page:1);
		$search = trim($_REQUEST['search']);
		$model_buhg_firms = new model_buhg_firms();
		$model_cities = new model_cities();
		$pred = $this->model_biznes->getItem($pid);
		if($id){
			$item = $this->model_biznes->tov()->register_query()->getItem($id);
		
			$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/', "Фирма {$pred['name']}"=>$this->url.'/biznes/preds/edit/'.$pid, "Товары фирмы {$pred['name']}"=>$this->url.'/biznes/preds/tovs/'.$pid, "Редактирование"=>$this->url.'/biznes/preds/tovs/edit/'.$pid.'/'.$id];
			$this->view->data['header'] = "Редактирование товара фирмы {$pred['name']}";
			$action = "{$this->url}/biznes/preds/tovs/edit/{$pid}/{$id}/?b_page={$page}&search={$search}";
		}
		else {
			$this->view->headers['title'] = 'Бизнес каталог | Администрирование Город 24';
			$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Бизнес каталог"=>$this->url.'/biznes/', "Фирмы"=>$this->url.'/biznes/preds/', "Фирма {$pred['name']}"=>$this->url.'/biznes/preds/edit/'.$pid, "Товары фирмы {$pred['name']}"=>$this->url.'/biznes/preds/tovs/'.$pid, "Добавление"=>$this->url.'/biznes/preds/tovs/add/'.$pid];
			$this->view->data['header'] = "Добавление товара";
			$action = "{$this->url}/biznes/preds/tovs/add/{$pid}/?b_page={$page}&search={$search}";
			$item=[];
			$item['bas_date'] = date("Y-m-d");
		}
		
		if(isset($_REQUEST['save'])){
			$this->view->notRender();
			$data = [
				'name' => $_REQUEST['name'],
				'id_pr' => $pid,
				'id_izm' => $_REQUEST['id_izm'],
				'price' => $_REQUEST['price'],
				'datelast' => date("Y-m-d"),
				'descr' => 0,
				'url' => $_REQUEST['url'],
				'url_ru' => $_REQUEST['url_ru'],
			];
			if($id){
				$data['id'] = $id;
			}
			else {
				$data['rating'] = 0;
				$data['rating_tel'] = 0;
				$data['photo'] = 0;
			}
			$new_id = $this->model_biznes->tov()->register_query()->InsertUpdate($data);
			if(empty($data['url']) or empty($data['url_ru'])){
				$update_data = ['id'=>$new_id];
				if(empty($data['url'])){
					$url = $new_id . 't-'.$this->new_url($_REQUEST['name']) . ".html";
					$update_data['url'] = $url;
				}
				if(empty($data['url_ru'])){
					$url = $new_id . 'т-'.$this->new_url_ru($_REQUEST['name']) . ".html";
					$update_data['url_ru'] = $url;
				}
				$this->model_biznes->tov()->register_query()->InsertUpdate($update_data);
			}
			
			header("Location: {$this->url}/biznes/preds/tovs/{$pid}/?b_page={$page}&search={$search}");
		}

		$admin = new AdminPage(
			array(
				"model" => $this->model_biznes->tov(),
				"item" => $item,
				"action" => $action,
				"fields" => [
					["title"=>"Наименование", "name"=>"name", "attrs"=>[], "type"=>"text"],
					["title"=>"Единица измерения", "name"=>"id_izm", "attrs"=>[], "type"=>"select", "items"=>$this->model_basket->model_units()->getItemsWhere("1", null, null, null,"`id` as `value`, `name` as `label`")],
					["title"=>"Цена", "name"=>"price", "attrs"=>[], "type"=>"text"],
					["title"=>"URL-адрес", "name"=>"url", "attrs"=>[], "type"=>"text"],
					["title"=>"URL-адрес русский", "name"=>"url_ru", "attrs"=>[], "type"=>"text"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	
}