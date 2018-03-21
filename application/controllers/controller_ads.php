<?php
class controller_ads extends Controller
{
	function __construct()
	{
		$project = Registry::get('PROJECT');
		if (isBot() === false)
		{
			//$_COOKIE['city_id'] ? $this->view = new View('news.tpl') : $this->view = new View('select.tpl');

			if ($_COOKIE['city_id'])
			{
				$this->view = new View('ads.tpl');
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
			$this->view = new View('ads.tpl');
			$this->city_id = 1483;
		}

		$this->view->headers['title'] = 'Объявления | Город 24';
		$this->view->data['menu']['ads'] = 'active';
		$this->view->data['city_name'] = $_COOKIE['city_name'] ? $_COOKIE['city_name'] : getUserCity('name');
		$this->limit = 22;

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
		//Комментарии
		$this->view->comments = new controller_comments();
		//Модели
		$this->model_adv = new model_adventures();

		//VIP
		$query_vip = "SELECT id, user_name, main_catid, caption, descr, price, add_time, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." AND vip = 2 ORDER BY RAND() DESC LIMIT 3";
		$this->view->data['getVipAds'] = $GLOBALS['DB']['80.93.183.242']->getAll($query_vip);
	}
	
	public function action_index($array = array())
	{
		//Выборка категорий
		$this->view->data['getCats'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `name`, `cpu`, `menu_icon`, (SELECT COUNT(*) FROM `new_feo_ua`.`adv_adventures` WHERE `new_feo_ua`.`adv_adventures`.`main_catid` = `new_feo_ua`.`adv_main_category`.`id`) as `count` FROM `new_feo_ua`.`adv_main_category` WHERE `in_center_menu` = 2 AND `on_off` = 2 ORDER BY `pos`");

		//Проверка на раздел объявлений и на открытие страницы конкретного объявления
		if (count($array) >= 1)
		{
			$res = explode('_', end(explode('/', $array[0])))[0];
			$res_1 = explode('_', end(explode('/', $array[1])))[0];
			$our = substr($res,0, 1);

			//Является ли экшен вызовом страницы объявления
			if ($our == "i")
			{
				$this->action_p($array[0]);
			}
			elseif ($res == "add")
			{
				$this->action_add($array[0]);
			}
			else
			{
				//Имеет ли экшен название категории или id группы
				if ($res == "group")
				{
					if ($res_1 && $res_1 == "category")
					{
						$razd[0]['sub_id'] = explode('_', end(explode('_', $array[1])))[0];
						$razd[0]['main_id'] = explode('_', end(explode('_', $array[0])))[0];
					}
					elseif ($res_1 && $res_1 != "category")
					{
						$razd = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id as `sub_id` FROM `new_feo_ua`.`adv_sub_category` WHERE cpu = '".$array[1]."'");
						$razd[0]['main_id'] = explode('_', end(explode('_', $array[0])))[0];
					}
					else
					{
						$razd[0]['main_id'] = explode('_', end(explode('_', $array[0])))[0];
					}
				}
				else
				{
					if ($res_1 && $res_1 == "category")
					{
						$razd = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id as `main_id` FROM `new_feo_ua`.`adv_main_category` WHERE cpu = '".$array[0]."'");
						$razd[0]['sub_id'] = explode('_', end(explode('_', $array[1])))[0];
					}
					elseif ($res_1 && $res_1 != "category")
					{
						$sub_razd = ", (SELECT id FROM `new_feo_ua`.`adv_sub_category` WHERE cpu = '".$array[1]."') as `sub_id`";
						$razd = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id as `main_id` ".$sub_razd." FROM `new_feo_ua`.`adv_main_category` WHERE cpu = '".$array[0]."'");
					}
					else
					{
						$razd = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id as `main_id` FROM `new_feo_ua`.`adv_main_category` WHERE cpu = '".$array[0]."'");
					}
				}

				//Суб-категория если есть
				$razd[0]['sub_id'] ? $sub_cat_id = "AND sub_catid = ".$razd[0]['sub_id'] : $sub_cat_id = "";
				$filter_url =  explode('&page=', end(explode('?', $_SERVER['REQUEST_URI'])))[0];

				//Выборка объявлений + пагинация
				$count = $GLOBALS['DB']['80.93.183.242']->query("SELECT COUNT(*) FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." AND main_catid = ".$razd[0]['main_id']." ".$sub_cat_id);
				$totalItems = $count->fetch_row();
				$urlPattern = $filter_url ? Registry::get('REQUEST_URI').'/?'.$filter_url.'&page=(:num)' : Registry::get('REQUEST_URI').'/?page=(:num)';
				$page = $_GET['page']?$_GET['page']:1;
				$limit = $this->limit;
				$offset = ($page - 1) * $limit;
				$this->view->data['paginator'] = new Paginator($totalItems[0], $limit, $page, $urlPattern);
				$this->view->data['getSubCats'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `pid`, `name`, `cpu`, (SELECT COUNT(*) FROM `new_feo_ua`.`adv_adventures` WHERE `new_feo_ua`.`adv_adventures`.`sub_catid` = `new_feo_ua`.`adv_sub_category`.`id`) as `count` FROM `new_feo_ua`.`adv_sub_category` WHERE `new_feo_ua`.`adv_sub_category`.`pid` = ".$razd[0]['main_id']." AND `new_feo_ua`.`adv_sub_category`.`on_off` = 2 ORDER BY `pos`");

				//Сортировка
				switch ($_GET['filter'])
				{
					case "price":
						$this->view->data['getAds'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." AND main_catid = ".$razd[0]['main_id']." ".$sub_cat_id." ORDER BY price LIMIT ".$offset.",".$limit);
						break;
					case "price-off":
						$this->view->data['getAds'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." AND main_catid = ".$razd[0]['main_id']." ".$sub_cat_id." ORDER BY price DESC LIMIT ".$offset.",".$limit);
						break;
					case "date":
						$this->view->data['getAds'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." AND main_catid = ".$razd[0]['main_id']." ".$sub_cat_id." ORDER BY up_time LIMIT ".$offset.",".$limit);
						break;
					case "date-off":
						$this->view->data['getAds'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." AND main_catid = ".$razd[0]['main_id']." ".$sub_cat_id." ORDER BY up_time DESC LIMIT ".$offset.",".$limit);
						break;
					default:
						$this->view->data['getAds'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." AND main_catid = ".$razd[0]['main_id']." ".$sub_cat_id." ORDER BY up_time DESC LIMIT ".$offset.",".$limit);
						break;
				}

				if (!$_GET['filter'])
				{
					foreach ($_GET as $key => $filter)
					{
						if ($filter['check'])
						{
							foreach ($filter['check'] as $key => $item)
							{
								$filters['filters']['check'] = array("id" => $key, "type" => "check", "name" => "check".$key, "items" => $item);
							}
						}
						elseif ($filter['list'])
						{
							$filters['filters']['list'] = array("id" => key($filter['list']), "type" => "list", "name" => "list".key($filter['list']), "items" => $filter['list']);
						}
						elseif ($filter['range'])
						{
							foreach ($filter['range'] as $key => $item)
							{
								$filters['filters']['range'] = array("id" => $key, "type" => "range", "name" => "range".$key, "from" => $item[0], "to" => $item[1]);
							}
						}
						elseif ($filter['price'])
						{
							$filters['filters']['price'] = array("id" => key($filter['price']), "type" => "range", "name" => "price", "from" => $filter['price'][0], "to" => $filter['price'][1]);
						}
					}

					//var_dump($filters);

					$this->view->data['getAds'] = $this->model_adv->getList($this->city_id, $_SESSION['user']['id'], $razd[0]['main_id'], $razd[0]['sub_id'], $offset, $limit, $filters);
				}

				$this->view->data['getOptions'] = $this->model_adv->getFilters($this->city_id, $razd[0]['main_id'], $razd[0]['sub_id']);
			}
		}
		else
		{
			$filter_url =  explode('&page=', end(explode('?', $_SERVER['REQUEST_URI'])))[0];

			//Выборка объявлений + пагинация
			$count = $GLOBALS['DB']['80.93.183.242']->query("SELECT COUNT(*) FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id);
			$totalItems = $count->fetch_row();
			$urlPattern = $filter_url ? Registry::get('REQUEST_URI').'/?'.$filter_url.'&page=(:num)' : Registry::get('REQUEST_URI').'/?page=(:num)';
			$page = $_GET['page']?$_GET['page']:1;
			$limit = $this->limit;
			$offset = ($page - 1) * $limit;
			$this->view->data['paginator'] = new Paginator($totalItems[0], $limit, $page, $urlPattern);

			switch ($_GET['filter'])
			{
				case "price":
					$this->view->data['getAds'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." ORDER BY price LIMIT ".$offset.",".$limit);
					break;
				case "price-off":
					$this->view->data['getAds'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." ORDER BY price DESC LIMIT ".$offset.",".$limit);
					break;
				case "date":
					$this->view->data['getAds'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." ORDER BY up_time LIMIT ".$offset.",".$limit);
					break;
				case "date-off":
					$this->view->data['getAds'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." ORDER BY up_time DESC LIMIT ".$offset.",".$limit);
					break;
				default:
					$this->view->data['getAds'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." ORDER BY up_time DESC LIMIT ".$offset.",".$limit);
					break;
			}

			if ($_GET && !$_GET['filter'])
			{
				foreach ($_GET as $key => $filter)
				{
					if ($filter['check'])
					{
						foreach ($filter['check'] as $key => $item)
						{
							$filters['filters']['check'] = array("id" => $key, "type" => "check", "name" => "check".$key, "items" => $item);
						}
					}
					elseif ($filter['list'])
					{
						$filters['filters']['list'] = array("id" => key($filter['list']), "type" => "list", "name" => "list".key($filter['list']), "items" => $filter['list']);
					}
					elseif ($filter['range'])
					{
						foreach ($filter['range'] as $key => $item)
						{
							$filters['filters']['range'] = array("id" => $key, "type" => "range", "name" => "range".$key, "from" => $item[0], "to" => $item[1]);
						}
					}
					elseif ($filter['price'])
					{
						$filters['filters']['price'] = array("id" => key($filter['price']), "type" => "range", "name" => "price", "from" => $filter['price'][0], "to" => $filter['price'][1]);
					}
				}

				$this->view->data['getAds'] = $this->model_adv->getList($this->city_id, $_SESSION['user']['id'], null, null, $offset, $limit, $filters);
			}

			$this->view->data['getOptions'] = $this->model_adv->getFilters($this->city_id, null, null);
		}
	}

	public function action_p($id)
	{
		$this->view = new View('ads-page.tpl');
		$this->view->data['menu']['ads'] = 'active';

		//Проекты
		$this->model_projects = new model_projects();
		$this->view->data['projects'] = $this->model_projects->getItemsWhere("1", "id DESC");
		//Товары
		$this->view->merchandise = new controller_merchandise();
		//Комментарии
		$this->view->comments = new controller_comments();

		if ($_SESSION['user']['id'])
		{
			$user_items = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT * FROM `new_feo_ua`.`accounts` WHERE id = ?i", $_SESSION['user']['id']);
			$this->user_ava = ((empty($user_items['ava_file'])) ? 'http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png' : $user_items['ava_file']);
			$this->view->data['user_ava'] = $this->user_ava;
			$this->view->data['user_name'] = $user_items['i_name'] . " " . $user_items['i_fam'];
		}

		//Выборка id и our из url строки объявления
		$res =  explode('/', end(explode('/', $id)))[0];
		$ads_id = substr($res,1);

		$this->view->data['getCats'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `name`, `cpu`, `menu_icon`, (SELECT COUNT(*) FROM `new_feo_ua`.`adv_adventures` WHERE `new_feo_ua`.`adv_adventures`.`main_catid` = `new_feo_ua`.`adv_main_category`.`id`) as `count` FROM `new_feo_ua`.`adv_main_category` WHERE `in_center_menu` = 2 AND `on_off` = 2 ORDER BY `pos`");
		$razd = $GLOBALS['DB']['80.93.183.242']->getOne("SELECT id, center_title FROM `new_feo_ua`.`adv_main_category` WHERE id = (SELECT main_catid FROM `new_feo_ua`.`adv_adventures` WHERE id = ".$ads_id.")");
		$this->view->data['getSubCats'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `pid`, `name`, `cpu`, (SELECT COUNT(*) FROM `new_feo_ua`.`adv_adventures` WHERE `new_feo_ua`.`adv_adventures`.`sub_catid` = `new_feo_ua`.`adv_sub_category`.`id`) as `count` FROM `new_feo_ua`.`adv_sub_category` WHERE `new_feo_ua`.`adv_sub_category`.`pid` = ".$razd." AND `new_feo_ua`.`adv_sub_category`.`on_off` = 2 ORDER BY `pos`");

		$query = "SELECT * FROM `new_feo_ua`.`adv_adventures` WHERE id = ".$ads_id;
		$this->view->data['getAds'] = $GLOBALS['DB']['80.93.183.242']->getAll($query);
		$this->view->headers['title'] = $this->view->data['getAds'][0]['caption'];

		//VIP
		$query_vip = "SELECT id, user_name, main_catid, caption, descr, price, add_time, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." AND vip = 2 ORDER BY RAND() DESC LIMIT 3";
		$this->view->data['getVipAds'] = $GLOBALS['DB']['80.93.183.242']->getAll($query_vip);

		//Похожие объявления
		$this->view->data['getAds4'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT id, user_name, main_catid, caption, descr, price, add_time, json_photos, city, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." AND main_catid = ".$this->view->data['getAds'][0]['main_catid']." AND id != ".$this->view->data['getAds'][0]['id']." ORDER BY add_time DESC LIMIT 0,4");
	}

	public function action_add($step)
	{
		$this->view = new View('add-ads-step'.$step[0].'.tpl');
		$this->view->data['menu']['ads'] = 'active';

		//Проекты
		$this->model_projects = new model_projects();
		$this->view->data['projects'] = $this->model_projects->getItemsWhere("1", "id DESC");
		//Товары
		$this->view->merchandise = new controller_merchandise();
		//Комментарии
		$this->view->comments = new controller_comments();
		$this->model_payment = new model_payment();

		if ($_SESSION['user']['id'])
		{
			$user_items = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT * FROM `new_feo_ua`.`accounts` WHERE id = ?i", $_SESSION['user']['id']);
			$this->user_ava = ((empty($user_items['ava_file'])) ? 'http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png' : $user_items['ava_file']);
			$this->view->data['user_ava'] = $this->user_ava;
			$this->view->data['user_name'] = $user_items['i_name'] . " " . $user_items['i_fam'];
			$this->view->data['user_email'] = $user_items['email'];
			$this->view->data['user_phone'] = substr($user_items['phone'],2, 10);
		}

		//Категории
		$this->view->data['getCats'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `name`, `cpu`, `menu_icon`, (SELECT COUNT(*) FROM `new_feo_ua`.`adv_adventures` WHERE `new_feo_ua`.`adv_adventures`.`main_catid` = `new_feo_ua`.`adv_main_category`.`id`) as `count` FROM `new_feo_ua`.`adv_main_category` WHERE `in_center_menu` = 2 AND `on_off` = 2 ORDER BY `pos`");
		//Единицы измерения
		$this->view->data['edizm'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `name` FROM `main`.`edizm` WHERE `in_adv` = 1");
		//$this->view->data['getSubCats'] = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `pid`, `name`, `cpu`, (SELECT COUNT(*) FROM `new_feo_ua`.`adv_adventures` WHERE `new_feo_ua`.`adv_adventures`.`sub_catid` = `new_feo_ua`.`adv_sub_category`.`id`) as `count` FROM `new_feo_ua`.`adv_sub_category` WHERE `new_feo_ua`.`adv_sub_category`.`pid` = ".$razd[0]['main_id']." AND `new_feo_ua`.`adv_sub_category`.`on_off` = 2 ORDER BY `pos`");

		//Опции
		//$this->view->data['getOptions'] = $this->model_adv->getOptions("16", "157");
		//var_dump($this->view->data['getOptions']);

		if ($step[0] == 2 || $step[0] == 3 || $step[0] == 4)
		{
			if ($_SESSION['ads']['adv_id'] && $_SESSION['user']['id'])
			{
				$this->view->data['adv_info'] = $this->model_adv->getItemWhere("user_id = ".$_SESSION['user']['id']." AND id = ".$_SESSION['ads']['adv_id']);
				$this->view->data['getPaymentPackages'] = $this->model_payment->getPaymentPackages("adv", $this->view->data['adv_info']['sub_catid']);

				var_dump($this->view->data['getPaymentPackages']);

				if (!$this->view->data['adv_info'])
				{
					header('Location: '.$GLOBALS['CONFIG']['HTTP_HOST'].'/404/');
					exit();
				}
			}
			else
			{
				header('Location: '.$GLOBALS['CONFIG']['HTTP_HOST'].'/404/');
				exit();
			}
		}

		//$this->model_payment = new model_payment();

		//$this->view->data['getPaymentPackages'] = $this->model_payment->getPaymentPackages("adv");
	}
}