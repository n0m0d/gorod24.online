<?php
require_once( __DIR__ ."/controller_index.php");
class controller_push extends controller_index
{
	function __construct(){
		parent::__construct();
		$GLOBALS['controller_push']['websites'] = [
			[ "value" => 29561, "label" => "feo.ua"],
			[ "value" => 49006, "label" => "gsp1.feomedia.ru"],
			[ "value" => 42441, "label" => "kafa-info.com.ua"],
			[ "value" => 29724, "label" => "rabota.kafa-info.com.ua"],
			[ "value" => 42439, "label" => "фео.рф"],
			[ "value" => 46771, "label" => "нб.фео.рф"],
			[ "value" => -1, "label" => "Андроид"],
		];
		
		$GLOBALS['controller_push']['providers'] = [
			"https://api.sendpulse.com", "https://gcm-http.googleapis.com/gcm/send"
		];
	}
	
	/********** Пуши **********/
	function action_index($actions=null){
		$this->view->headers['title'] = 'Пуш рассылки | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Пуш рассылки"=>$this->url.'/news/'];
		$this->view->data['header'] = "Пуш рассылки";
		
		$model = new model_push();
		// Удаление элементов
		if (isset($_POST['del'])){
			foreach ($_POST['options'] as $input)
			{
				$model->Delete($input);
			}
		}
		
		if (isset($_POST['on'])){
			foreach ($_POST['options'] as $input)
			{
				$model->Update([ 'on_off'=>'1' ], $input);
			}
		}
		
		if (isset($_POST['off'])){
			foreach ($_POST['options'] as $input)
			{
				$model->Update([ 'on_off'=>'0' ], $input);
			}
		}
		$where = "1";
		$websites = $this->websites;
			$admin = new AdminList(
				array(
					"model" => $model,
					"where" => $where,
					"order" => "id DESC",
					"multiple" => "true",
					"action" => $this->url.'/push/',
					"controls" => [
						["title"=>"Удалить", "name"=>"delete", "attrs"=>[], "class"=>'ajax-delete', "button-type"=>'danger', "type"=>"button"],
						//["title"=>"Включить", "name"=>"on", "attrs"=>[], "class"=>'ajax-on', "button-type"=>'primary', "type"=>"button"],
						//["title"=>"Выключить", "name"=>"off", "attrs"=>[], "class"=>'ajax-off', "button-type"=>'primary', "type"=>"button"],
						["title"=>"Добавить рассылку", "href"=>$this->url."/push/add/", "attrs"=>['data-ajax'=>'true', 'data-center'=>'false'], "class"=>'ajax-load', "button-type"=>'success', "type"=>"link"],
					],
					"attrs" => ["class"=>"table-adapt"],
					"columns" => [
						["title"=>"ID", "name"=>"id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Сайт", "name"=>"website_id", "attrs"=>[ "data-breakpoints"=>"sm" ], "content"=>create_function('$cel,$row','
							foreach($GLOBALS["controller_push"]["websites"] as $website){
								if($website["value"]==$cel) { echo $website["label"]; break;}
							}
						')],
						["title"=>"Заголовок", "name"=>"title", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Тело", "name"=>"body", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Ссылка","name"=>"link", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo "<div style=\"width:150px;overflow:auto;\">{$cel}</div>";
						')],
						["title"=>"Дата отправки", "name"=>"senddate", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							echo $cel;
						')],
						["title"=>"Статус", "name"=>"is_sended", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row','
							if($row["error_code"]==0 or $row["is_sended"]==1){
								switch($cel){
									case 0: echo "<span class=\"red\">Ожидает отправки</span>"; break; 
									case 1: echo "<span class=\"green\">Отправка запущена</span>"; break; 
								}
							}
							else{
								echo "<span style=\"color:red;\">[".$row["error_code"]."]: ".$row["error_message"]."</span>";
							}
						')],
						["title"=>"Опции", "name"=>"options", "attrs"=>[ "data-breakpoints"=>"xs sm" ], "content"=>create_function('$cel,$row',' 
							
						')]
					],
				)
			);
			/**/
			$result .= $admin;
			$content = $result;
		$this->view->data['content'] = $content;
	}
	
	function action_add($actions=null){
		$model_push = new model_push();
		$this->view->headers['title'] = 'Пуш рассылки - создание | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$this->url, "Пуш рассылки"=>$this->url.'/push/', "Добавление новой рассылки"=>$this->url.'/push/add/'];
		$this->view->data['header'] = "Пуш рассылки - Добавление новой рассылки";
		$website = [29561, 42441, 42439, -1];
		$senddate = date("Y-m-d H:00:00");
		$ttl = 3600 * 5;
		$quick = 0;
		$title = '';
		$body = '';
		$link = '';
		if($_REQUEST['new_id']){ $new_id = trim($_REQUEST['new_id']); }
		if($_REQUEST['title']){ $title = trim($_REQUEST['title']); }
		if($_REQUEST['body']){ $body = trim($_REQUEST['body']); }
		if($_REQUEST['link']){ $link = trim($_REQUEST['link']); }
		
		if($new_id){
			$model_gorod_news = new model_gorod_news();
			$new = $model_gorod_news->getItem($new_id);
		}
		
		
		if(isset($_REQUEST['save'])){ $this->view->notRender();
			$errors =[];
			$pushs = [];
			foreach($_REQUEST['website'] as $i => $site_id){
				if($_REQUEST['quick']!='on'){
					$checkedPustTime = self::checkPushTime($site_id, $_REQUEST['senddate']);
					//if($checkedPustTime != $_REQUEST['senddate']) { $errors[] = "<h1>На данное время уже есть PUSH. Время отправки автоматически сдвинуто на $checkedPustTime</h1>";}
				}
				else {
					$checkedPustTime = $_REQUEST['senddate'];
				}
				if($site_id==-1) {$pid=1; } else {$pid=0;}
				$pushs[] = [
					"provider" =>$GLOBALS['controller_push']['providers'][$pid],
					"push_id" =>0,
					"access_token" =>'',
					"website_id" =>$site_id,
					"title" =>$_REQUEST["title"],
					"body" =>$_REQUEST["body"],
					"ttl" =>$_REQUEST["ttl"],
					"link" =>$_REQUEST["link"],
					"filter_lang" =>'',
					"filter_browser" =>'',
					"sended" =>0,
					"delivered" =>0,
					"redirect" =>0,
					"adddate" =>date('Y-m-d H:i:s'),
					"senddate" =>$checkedPustTime,
					"is_sended" =>0,
					"error_code" =>0,
					"error_message" =>'',
					"new_id" =>(int)$new_id,
					"from_new" =>(int)$new['news_id'],
					"our" =>(int)$new['our'],
				];
				
			}
			
			if(empty($pushs)){
				$errors[] = "<h1>Вы не выбрали сайты для которых запустить рассылку.</h1>";
			}
			
			if(count($errors)==0){
				foreach($pushs as $push_data){
					$model_push->Insert($push_data);
				}
				header("Location: /admin/gorod/push/");
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
				"model" => $model_gorod_news,
				"item" => $new,
				"action" => $this->url.'/push/add/?new_id='.$new_id,
				"fields" => [
					["title"=>"Для сайтов", "name"=>"website", "attrs"=>[], "type"=>"select", "value"=>$website, "multiple"=>true, "items" => $GLOBALS['controller_push']['websites']],
					["title"=>"Заголовок", "name"=>"title", "value"=>$title, "attrs"=>[], "type"=>"text"],
					["title"=>"Тело", "name"=>"body", "value"=>$body, "attrs"=>[], "type"=>"mediumText"],
					["title"=>"Дата и время страрта", "name"=>"senddate", "value"=>$senddate, "stepMinute"=>"15", "minuteGrid"=>"15", "showSecond"=>false, "readonly"=>true, "attrs"=>[], "type"=>"datetime"],
					["title"=>"Время жизни push рассылки (в секундах)", "value"=>$ttl, "name"=>"ttl", "attrs"=>[], "type"=>"number"],
					["title"=>"Ссылка для перехода, если не указана будет взята ссылка сайта", "value"=>$link, "name"=>"link", "attrs"=>[], "type"=>"text"],
					["title"=>"Срочная отправка", "name"=>"quick", "value"=>$quick, "attrs"=>[], "type"=>"switch"],
					["title"=>"Сохранить", "name"=>"save", "attrs"=>[], "button-type"=>'primary', "type"=>"submit"],
				],
			)
		);

		$content .= $admin;
		$this->view->data['content'] = $content;
		return  $content;
	}
	
	function checkPushTime($site_id, $senddate){
		$model_push = new model_push();
		$check = $model_push->getCountWhere("website_id={$site_id} AND senddate='{$senddate}'");
		if($check==0){
			return $senddate;
		}
		else {
			$time = strtotime($senddate);
			return self::checkPushTime($site_id, date("Y-m-d H:i:s", $time + 3600));
		}
	}
	
}