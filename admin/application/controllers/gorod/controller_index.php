<?php

class controller_index extends Controller
{
	function __construct(){
		$this->domain = $GLOBALS['CONFIG']['HTTP_HOST'].'/admin/';
		$this->controller = 'gorod';
		$this->url = $this->domain.$this->controller;
		
		$this->view = new View('index.tpl');
		$this->view->setTemplatesFolder(ADMINDIR.'/application/views/');
		$this->view->headers['title'] = 'Город24 | Администрирование Город 24';
		$this->view->data['main-menu']['Город24'] = true;
		$this->view->data['main']['header'] = 'Сайт';
		$this->view->data['main']['menu'] = [
			[ "title" => "Новости", "url"=>"#", "items" => [
					["title" => "Новости", "url"=>$this->url."/news/"],
					["title" => "Сграбленные новости", "url"=>$this->url."/grubnews/"],
				]
			],
			[ "title" => "Пуш рассылки", "url"=>$this->url."/push/"],
			["title" => "Продовольственная корзина", "url"=>$this->url."/basket/", "items" => [
					["title" => "Замеры по датам", "url"=>$this->url."/basket/zamers/"],
					["title" => "Единицы измерения", "url"=>$this->url."/basket/edizms/"],
					["title" => "Список товаров", "url"=>$this->url."/basket/tovars/"],
					["title" => "Список магазинов/точек", "url"=>$this->url."/basket/magazs/"],
					["title" => "Типы замеров", "url"=>$this->url."/basket/types/"],
				]
			],
			["title" => "Бизнес каталог", "url"=>$this->url."/biznes/", "items" => [
					["title" => "Фирмы", "url"=>$this->url."/biznes/preds/"],
				]
			],
			["title" => "Рассылки в соц.сети", "url"=>$this->url."/social/", "items" => [
					["title" => "Все рассылки", "url"=>$this->url."/social/publish/"],
					["title" => "Аккаунты", "url"=>$this->url."/social/accounts/"],
					["title" => "Автоматическое расписание", "url"=>$this->url."/social/auto/"],
				]
			],
		];
	}
	
	function action_index($actions=null){
		$this->view->data['breadcrumbs'] = [ "Сайт"=>$GLOBALS['CONFIG']['HTTP_HOST'].'/admin/gorod/'];
		$this->view->data['header'] = "Сайт";
	}
	
	function action_json_firms(){
		$this->view->notRender();
		header("Content-type: application/json; charset=UTF-8");
		$term = trim($_GET['term']);
		/*
		$model_gorod_pred = new model_gorod_pred();
		$data = $model_gorod_pred->getItemsWhere("`name` LIKE '%{$term}%'", "`name`", 0, 10, "`id`, `name` as `value`");
		*/
		
		$model_feo_biznes = new model_feo_biznes();
		$data = $model_feo_biznes->getItemsWhere("`name` LIKE '%{$term}%'", "`name`", 0, 10, "`id`, `name` as `value`");
		
		echo json_encode($data);
	}
	
	function action_json_buhg_firms(){
		$this->view->notRender();
		header("Content-type: application/json; charset=UTF-8");
		$term = trim($_GET['term']);
		$model_buhg_firms = new model_buhg_firms();
		$data = $model_buhg_firms->getItemsWhere("`name` LIKE '%{$term}%'", "`name`", 0, 10, "`id`, `name` as `value`");
		
		echo json_encode($data);
	}
	
	function action_json_tags(){
		$this->view->notRender();
		header("Content-type: application/json; charset=UTF-8");
		$term = trim($_GET['term']);
		$model_gorod_news = new model_gorod_news();
		$data = $model_gorod_news->_model_news_teg()->getItemsWhere("`tag` LIKE '%{$term}%'", "`tag`", 0, 10, "`id`, `tag` as `value`");
		echo json_encode($data);
	}
	
	function action_json_albums(){
		$this->view->notRender();
		header("Content-type: application/json; charset=UTF-8");
		$term = trim($_GET['term']);
		
		$data = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `al_id` as `id`, `al_name` as `value` FROM new_feo_ua.feo_albums WHERE `al_name` LIKE '%{$term}%' ORDER BY `al_name` ASC LIMIT 10 ");
		echo json_encode($data);
	}
	
	function action_json_inters(){
		$this->view->notRender();
		header("Content-type: application/json; charset=UTF-8");
		$term = trim($_GET['term']);
		
		$data = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `inter_id` as `id`, `inter_name` as `value` FROM new_feo_ua.feo_interviews WHERE `inter_name` LIKE '%{$term}%' ORDER BY `inter_name` ASC LIMIT 10 ");
		echo json_encode($data);
	}
	
	function action_json_zamers(){
		$this->view->notRender();
		header("Content-type: application/json; charset=UTF-8");
		$term = trim($_GET['term']);
		
		$data = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `bas_id` as `id`, `bas_name` as `value` FROM main.feo_basket WHERE `bas_name` LIKE '%{$term}%' ORDER BY `bas_id` DESC LIMIT 10 ");
		echo json_encode($data);
	}
	
	function action_json_panoramas(){
		$this->view->notRender();
		header("Content-type: application/json; charset=UTF-8");
		$term = trim($_GET['term']);
		
		$data = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id` as `id`, `title` as `value` FROM new_feo_ua.panorama_photos WHERE `title` LIKE '%{$term}%' ORDER BY `title` ASC LIMIT 10 ");
		echo json_encode($data);
	}
	
	function action_json_gazeta(){
		$this->view->notRender();
		header("Content-type: application/json; charset=UTF-8");
		$term = trim($_GET['term']);
		$model_adventures = new model_adventures();
		$data = $model_adventures->model_gazeta_nums()->getItemsWhere("(`num` LIKE '%{$term}%' OR `date` LIKE '%{$term}%') and `stop_date`>= CURDATE()", "`num`", 0, 10, "`id`, CONCAT(`num`, ' от ', `date`) as `value`");
		echo json_encode($data);
	}

	public function translitIt($str){
		$tr = array(
			"А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
			"Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
			"Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
			"О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
			"У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
			"Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
			"Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
			"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
			"з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
			"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
			"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
			"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
			"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
			" "=> "_", "."=> "", "/"=> "_" , ","=> "" , "-"=> ""
			);
		return strtr($str,$tr);
	}

	public function new_url($urlstr){
		if (preg_match('/[^A-Za-z0-9_\-]/', $urlstr)) {
			$urlstr = str_replace( "&quot", "" , $urlstr );
			$urlstr=strip_tags($urlstr);
			$urlstr=trim($urlstr);
			$urlstr = $this->translitIt($urlstr);
			$urlstr = preg_replace('/[^A-Za-z0-9_\-]/', '', $urlstr);
		}
		return $urlstr;
	}
	
	public function no_translitIt($str)	{
		$tr = array(
			"»"=> "","«"=> "",
			" "=> "_", "."=> "", "/"=> "_" , ","=> "" , "-"=> ""
		);
		return strtr($str,$tr);
	}

	public function new_url_ru($urlstr){
		if (preg_match('/[^A-Za-z0-9_\-]/', $urlstr)) {
			$urlstr = str_replace( "&quot", "" , $urlstr );
			$urlstr=strip_tags($urlstr);
			$urlstr=trim($urlstr);
			$urlstr = mb_strtolower($this->no_translitIt($urlstr));
			$urlstr = preg_replace('/[^A-Za-za-яА-Я0-9_\-]/', '', $urlstr);
		}
		return $urlstr;
	}
	
	
}