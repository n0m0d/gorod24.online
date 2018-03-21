<?php
class controller_catalog extends Controller
{
	function __construct()
	{
		if (isBot() === false)
		{
			//$_COOKIE['city_id'] ? $this->view = new View('news.tpl') : $this->view = new View('select.tpl');

			if ($_COOKIE['city_id'])
			{
				$this->view = new View('catalog.tpl');
			}
			else
			{
				$this->view = new View('select.tpl');
				$this->view->setHeader("<html>");
				$this->view->setFooter("</html>");
				exit();
			}

			$this->city_id = $_COOKIE['city_id'] ? $_COOKIE['city_id'] : getUserCity('id');
		}
		else
		{
			$this->view = new View('catalog.tpl');
			$this->city_id = 1483;
		}

		$this->view->headers['title'] = 'Бизнес каталог | Город 24';
		$this->view->data['menu']['catalog'] = 'active';
		$this->view->data['city_name'] = $_COOKIE['city_name'] ? $_COOKIE['city_name'] : getUserCity('name');

		if ($_SESSION['user']['id'])
		{
			$user_items = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT * FROM `new_feo_ua`.`accounts` WHERE id = ?i", $_SESSION['user']['id']);
			$this->user_ava = ((empty($user_items['ava_file'])) ? 'http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png' : $user_items['ava_file']);
			$this->view->data['user_ava'] = $this->user_ava;
			$this->view->data['user_name'] = $user_items['i_name'] . " " . $user_items['i_fam'];
		}

		//Обновляем куки и создаем сессию
		if (isset($_COOKIE['login']) && isset($_COOKIE['password']))
		{
			setcookie("login", $_COOKIE['login'],time()+31556926 ,'/');
			setcookie("password", $_COOKIE['password'],time()+31556926 ,'/');

			if (!$_SESSION['user'])
			{
				$login = new controller_login();
				$login->user_login();
			}
		}

		//Проекты
		$this->model_projects = new model_projects();
		$this->view->data['projects'] = $this->model_projects->getItemsWhere("1", "id DESC");
		//Товары
		$this->view->merchandise = new controller_merchandise();
		//Модели
		//$this->model_pred = new model_gorod_pred();
		//$this->model_pred_photos = $this->model_pred->model_pred_photos();
		//$this->model_pred_otr = $this->model_pred->model_pred_otr();
	}
	
	public function action_index($array = array())
	{
		//Выборка фирм в главной странице бизнес каталога
		if (count($array) >= 1)
		{
			$res =  explode('-', end(explode('/', $array[0])))[0];
			$our = substr($res,-1);
			$our1 = substr($res,0, 6);

			if ($our == "p")
			{
				$this->action_p($array[0]);
			}

			if ($our1 == "search")
			{
				$this->action_search();
			}

			if ($our == "k")
			{
				$this->action_k($array[0]);
			}
		}
		else
		{
			//$main_cat = $this->model_pred_otr->getItemsWhere("`sub_otr` LIKE 'main'", "name", "0", null, "id AS `id_main`, name AS `name_main`, url, icon");
			$main_cat = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id` AS `id_main`, `name` AS `name_main`, `url`, `icon` FROM `main`.`otr` WHERE `sub_otr` LIKE 'main' ORDER BY `name`");
			$this->view->data['main_otr'] = $main_cat;

			foreach ($main_cat AS $main)
			{
				//$sub_cat = $this->model_pred_otr->getItemsWhere("sub_otr like '%;{$main['id_main']};%'", "name", "0", null, "id, name, url, (SELECT count(*) FROM `gorod24.online`.`gorod_pred` AS `pt` WHERE `pt`.otr LIKE CONCAT('%;',`gorod24.online`.`gorod_pred_otr`.id,';%') AND `pt`.`on_off`!='0') AS `sub_count`");
				$sub_cat = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `name`, `url`, (SELECT count(*) FROM `main`.`pred` AS `pt` WHERE `pt`.otr LIKE CONCAT('%;',`main`.`otr`.id,';%') AND `pt`.`on_off`!='0') AS `sub_count` FROM `main`.`otr` WHERE sub_otr like '%;{$main['id_main']};%' ORDER BY `name`");
				$this->view->data['list_otr'][$main['id_main']] = $sub_cat;
			}

			//$this->view->data['getCatalogFirms'] = $this->model_pred->getItemsWhere("town = '".$this->city_name."' AND oplata >= CURDATE() AND status = 3 AND on_off = 1", "RAND()", "0", "4", "id, name, name_kat, adr_f, phones, menag, status, activ, rating, rating_tel, rating_ob, email, web, work, lunch, sunday, url, (SELECT file FROM `gorod24.online`.`gorod_pred_photos` WHERE pid = `gorod24.online`.`gorod_pred`.id AND type = 0 LIMIT 0,1) AS `file`");
			$this->view->data['getCatalogFirms'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, name, name_kat, adr_f, phones, menag, status, activ, rating, rating_tel, rating_ob, email, web, work, lunch, sunday, url, (SELECT file FROM `main`.`pred_photo` WHERE pid = `main`.`pred`.id AND type = 0 LIMIT 0,1) AS `file` FROM `main`.`pred` WHERE town = '".$this->view->data['city_name']."' AND oplata >= CURDATE() AND status = 3 AND on_off = 1 ORDER BY RAND() LIMIT 0,4");
		}
	}

	public function action_p($id)
	{
		$this->view = new View('firm.tpl');

		//Проекты
		$this->model_projects = new model_projects();
		$this->view->data['projects'] = $this->model_projects->getItemsWhere("1", "id DESC");
		//Товары
		$this->view->merchandise = new controller_merchandise();

		if ($_SESSION['user']['id'])
		{
			$user_items = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT * FROM `new_feo_ua`.`accounts` WHERE id = ?i", $_SESSION['user']['id']);
			$this->user_ava = ((empty($user_items['ava_file'])) ? 'http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png' : $user_items['ava_file']);
			$this->view->data['user_ava'] = $this->user_ava;
			$this->view->data['user_name'] = $user_items['i_name'] . " " . $user_items['i_fam'];
		}

		//Выборка id и our из url строки новости
		$res =  explode('-', end(explode('/', $id)))[0];
		$firm_id = substr($res,0,-1);
		$this->view->data['city_name'] = $_COOKIE['city_name'] ? $_COOKIE['city_name'] : getUserCity('name');

		//$this->view->data['getFirm'] = $this->model_pred->getItemWhere("id = ".$firm_id." AND on_off = 1", "id, name, name_kat, adr_f, phones, menag, status, activ, rating, rating_tel, rating_ob, email, web, work, lunch, sunday, url, (SELECT file FROM `gorod24.online`.`gorod_pred_photos` WHERE pid = ".$firm_id." AND type = 0 ORDER BY id DESC LIMIT 0,1) AS `file`");
		$this->view->data['getFirm'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, name, name_kat, adr_f, phones, menag, status, activ, rating, rating_tel, rating_ob, email, web, work, lunch, sunday, url, (SELECT file FROM `main`.`pred_photo` WHERE pid = ".$firm_id." AND type = 0 ORDER BY id DESC LIMIT 0,1) AS `file` FROM `main`.`pred` WHERE id = ".$firm_id." AND on_off = 1");
		$this->view->data['firmId'] = $firm_id;
		$this->view->headers['title'] = $this->view->data['getFirm'][0]['name'];

		//$main_cat = $this->model_pred_otr->getItemsWhere("`sub_otr` LIKE 'main'", "name", "0", null, "id AS `id_main`, name AS `name_main`, url, icon");
		$main_cat = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id` AS `id_main`, `name` AS `name_main`, `url`, `icon` FROM `main`.`otr` WHERE `sub_otr` LIKE 'main' ORDER BY `name`");
		$this->view->data['main_otr'] = $main_cat;

		foreach ($main_cat AS $main)
		{
			//$sub_cat = $this->model_pred_otr->getItemsWhere("sub_otr like '%;{$main['id_main']};%'", "name", "0", null, "id, name, url, (SELECT count(*) FROM `gorod24.online`.`gorod_pred` AS `pt` WHERE `pt`.otr LIKE CONCAT('%;',`gorod24.online`.`gorod_pred_otr`.id,';%') AND `pt`.`on_off`!='0') AS `sub_count`");
			$sub_cat = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `name`, `url`, (SELECT count(*) FROM `main`.`pred` AS `pt` WHERE `pt`.otr LIKE CONCAT('%;',`main`.`otr`.id,';%') AND `pt`.`on_off`!='0') AS `sub_count` FROM `main`.`otr` WHERE sub_otr like '%;{$main['id_main']};%' ORDER BY `name`");
			$this->view->data['list_otr'][$main['id_main']] = $sub_cat;
		}
	}

	public function action_k($id)
	{
		$this->view = new View('catalog-page.tpl');

		//Проекты
		$this->model_projects = new model_projects();
		$this->view->data['projects'] = $this->model_projects->getItemsWhere("1", "id DESC");
		//Товары
		$this->view->merchandise = new controller_merchandise();

		if ($_SESSION['user']['id'])
		{
			$user_items = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT * FROM `new_feo_ua`.`accounts` WHERE id = ?i", $_SESSION['user']['id']);
			$this->user_ava = ((empty($user_items['ava_file'])) ? 'http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png' : $user_items['ava_file']);
			$this->view->data['user_ava'] = $this->user_ava;
			$this->view->data['user_name'] = $user_items['i_name'] . " " . $user_items['i_fam'];
		}

		//Выборка id и our из url строки новости
		$res =  explode('-', end(explode('/', $id)))[0];
		$firm_id = substr($res,0,-1);
		$this->view->data['city_name'] = $_COOKIE['city_name'] ? $_COOKIE['city_name'] : getUserCity('name');

		//$name = $this->model_pred_otr->getItemWhere("id = ".$firm_id, "name");
		$name = $GLOBALS['DB']['80.93.183.242']->getOne("SELECT `name` FROM `main`.`otr` WHERE `id` = ".$firm_id);

		//$this->view->data['getFirm'] = $this->model_pred->getItemsWhere("town = '".$this->city_name."' AND on_off = 1 AND otr LIKE '%;".$firm_id.";%'", "mesto, oplata DESC", null, null, "id, name, name_kat, adr_f, phones, menag, status, activ, rating, rating_tel, rating_ob, email, web, work, lunch, sunday, url, on_off, oplata, (SELECT file FROM `gorod24.online`.`gorod_pred_photos` WHERE pid = `gorod24.online`.`gorod_pred`.id ORDER BY id DESC LIMIT 0,1) AS `file`");
		$this->view->data['getFirm'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, name, name_kat, adr_f, phones, menag, status, activ, rating, rating_tel, rating_ob, email, web, work, lunch, sunday, url, on_off, oplata, (SELECT file FROM `main`.`pred_photo` WHERE pid = `main`.`pred`.id ORDER BY id DESC LIMIT 0,1) AS `file` FROM `main`.`pred` WHERE town = '".$this->view->data['city_name']."' AND on_off = 1 AND otr LIKE '%;".$firm_id.";%' ORDER BY mesto, oplata DESC");
		$this->view->data['firmId'] = $firm_id;
		$this->view->headers['title'] = $name;

		//$main_cat = $this->model_pred_otr->getItemsWhere("`sub_otr` LIKE 'main'", "name", "0", null, "id AS `id_main`, name AS `name_main`, url, icon");
		$main_cat = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id` AS `id_main`, `name` AS `name_main`, `url`, `icon` FROM `main`.`otr` WHERE `sub_otr` LIKE 'main' ORDER BY `name`");
		$this->view->data['main_otr'] = $main_cat;

		foreach ($main_cat AS $main)
		{
			//$sub_cat = $this->model_pred_otr->getItemsWhere("sub_otr like '%;{$main['id_main']};%'", "name", "0", null, "id, name, url, (SELECT count(*) FROM `gorod24.online`.`gorod_pred` AS `pt` WHERE `pt`.otr LIKE CONCAT('%;',`gorod24.online`.`gorod_pred_otr`.id,';%') AND `pt`.`on_off`!='0') AS `sub_count`");
			$sub_cat = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `name`, `url`, (SELECT count(*) FROM `main`.`pred` AS `pt` WHERE `pt`.otr LIKE CONCAT('%;',`main`.`otr`.id,';%') AND `pt`.`on_off`!='0') AS `sub_count` FROM `main`.`otr` WHERE sub_otr like '%;{$main['id_main']};%' ORDER BY `name`");
			$this->view->data['list_otr'][$main['id_main']] = $sub_cat;
		}
	}

	public function action_search()
	{
		//Проекты
		$this->model_projects = new model_projects();
		$this->view->data['projects'] = $this->model_projects->getItemsWhere("1", "id DESC");
		//Товары
		$this->view->merchandise = new controller_merchandise();
		$this->view->data['city_name'] = $_COOKIE['city_name'] ? $_COOKIE['city_name'] : getUserCity('name');
		$search = trim(strip_tags(stripcslashes(htmlspecialchars($_GET['search']))));
		//$search_result = $this->model_pred->getItemsWhere("town = '".$this->city_name."' AND on_off = 1 AND name LIKE '%".$search."%'", "mesto, oplata DESC", null, null, "id, name, name_kat, adr_f, phones, menag, status, activ, rating, rating_tel, rating_ob, email, web, work, lunch, sunday, url, on_off, oplata, (SELECT file FROM `gorod24.online`.`gorod_pred_photos` WHERE pid = `gorod24.online`.`gorod_pred`.id ORDER BY id DESC LIMIT 0,1) AS `file`");
		$search_result = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, name, name_kat, adr_f, phones, menag, status, activ, rating, rating_tel, rating_ob, email, web, work, lunch, sunday, url, on_off, oplata, (SELECT file FROM `main`.`pred_photo` WHERE pid = `main`.`pred`.id ORDER BY id DESC LIMIT 0,1) AS `file` FROM `main`.`pred` WHERE town = '".$this->view->data['city_name']."' AND on_off = 1 AND name LIKE '%".$search."%' ORDER BY mesto, oplata DESC");

		if (!$search_result)
		{
			echo "<script type=\"text/javascript\">alert(\"Ничего не найдено...\");</script>";
		}
		else
		{
			$this->view = new View('catalog-page.tpl');
			$this->view->headers['title'] = "Поиск: ".$search;
			//Проекты
			$this->model_projects = new model_projects();
			$this->view->data['projects'] = $this->model_projects->getItemsWhere("1", "id DESC");
			//Товары
			$this->view->merchandise = new controller_merchandise();

			if ($_SESSION['user']['id'])
			{
				$user_items = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT * FROM `new_feo_ua`.`accounts` WHERE id = ?i", $_SESSION['user']['id']);
				$this->user_ava = ((empty($user_items['ava_file'])) ? 'http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png' : $user_items['ava_file']);
				$this->view->data['user_ava'] = $this->user_ava;
				$this->view->data['user_name'] = $user_items['i_name'] . " " . $user_items['i_fam'];
			}

			$this->view->data['getFirm'] = $search_result;
		}

		//$main_cat = $this->model_pred_otr->getItemsWhere("`sub_otr` LIKE 'main'", "name", "0", null, "id AS `id_main`, name AS `name_main`, url, icon");
		$main_cat = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id` AS `id_main`, `name` AS `name_main`, `url`, `icon` FROM `main`.`otr` WHERE `sub_otr` LIKE 'main' ORDER BY `name`");
		$this->view->data['main_otr'] = $main_cat;

		foreach ($main_cat AS $main)
		{
			//$sub_cat = $this->model_pred_otr->getItemsWhere("sub_otr like '%;{$main['id_main']};%'", "name", "0", null, "id, name, url, (SELECT count(*) FROM `gorod24.online`.`gorod_pred` AS `pt` WHERE `pt`.otr LIKE CONCAT('%;',`gorod24.online`.`gorod_pred_otr`.id,';%') AND `pt`.`on_off`!='0') AS `sub_count`");
			$sub_cat = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `name`, `url`, (SELECT count(*) FROM `main`.`pred` AS `pt` WHERE `pt`.otr LIKE CONCAT('%;',`main`.`otr`.id,';%') AND `pt`.`on_off`!='0') AS `sub_count` FROM `main`.`otr` WHERE sub_otr like '%;{$main['id_main']};%' ORDER BY `name`");
			$this->view->data['list_otr'][$main['id_main']] = $sub_cat;
		}
	}
}