<?php
class controller_index extends Controller
{
	function __construct()
	{
		if (isBot() === false)
		{
			//$_COOKIE['city_id'] ? $this->view = new View('news.tpl') : $this->view = new View('select.tpl');

			if ($_COOKIE['city_id'])
			{
				$this->view = new View('index.tpl');
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
			$this->view = new View('index.tpl');
			$this->city_id = 1483;
		}

		$this->view->headers['title'] = 'Главная | Город 24';
		$this->view->data['menu']['index'] = 'active';
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
		//Опросы
		$this->view->interviews = new controller_interviews();
		//Товары
		$this->view->merchandise = new controller_merchandise();
		//Новости
		$this->model_news = new model_gorod_news();
		//Объявления
		$this->model_adv = new model_adventures();
	}
	
	public function action_index($array = array())
	{
		//Новости слайдер
		$this->view->data['getNewsSlider'] = $this->model_news->db()->getAll("
			SELECT  
	          `rot`.`id` AS  `r_id`, 
	          `nt`.*
	         FROM  
	          `gorod_news` AS  `nt` ,  
	          `gorod_news_rotate` AS  `rot` 
	         WHERE  
	          `rot`.`new_id` =  `nt`.`id` AND `nt`.`on_off` = 1 AND `city_id`=".$this->city_id."
	         ORDER BY  `r_id` DESC 
	         LIMIT 6;
		");

		//Топ читаемых за сегодня
		$this->view->data['getTopNews'] = $this->model_news->db()->getAll("
			SELECT  
					`nld_top`.`c`,
					`nld_top`.`n_id`,
					`nt`.*
			FROM 
					`gorod24.online`.`gorod_news` AS `nt`,
					(
							SELECT  
									COUNT(`id`) AS `c`,
									`nld`.*
							FROM 
									`gorod24.online`.`gorod_news_look_day` AS `nld` 
							WHERE 
									`nld`.`our`='1'
							GROUP BY `n_id` 
							ORDER BY `c` DESC 
							LIMIT 5
					) AS `nld_top`
			WHERE
					`nt`.`news_id`=`nld_top`.`n_id` AND `city_id`=".$this->city_id." /*AND date_format(news_date, '%Y%m') = date_format(now(), '%Y%m')*/
			ORDER BY `c` DESC
		    LIMIT 3;
		");

		//Последние новости города
		$this->view->data['getLastNews'] = $this->model_news->get("id, news_id, news_head, news_lid, news_tag, url, news_date, our, looks, type,
				(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments` WHERE com_main_id = `gorod24.online`.`gorod_news`.`id` AND com_for_table = 'gorod_news' AND com_for_column = 'id') as `comments_sum`,
				(SELECT COUNT(*) FROM `gorod24.online`.`gorod_news_photos` WHERE new_id = `gorod24.online`.`gorod_news`.`id` AND status = 1) as `photos_sum`")
		                         ->where("on_off='1' and news_date <= NOW() and news_date >= '2015-01-01 00:00:00' and (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$this->city_id}') > 0")->order("news_date DESC")->offset(0)->limit(6)->commit();

		//Последние новости крыма
		$this->view->data['getLastNewsRegion'] = $this->model_news->get("id, news_id, news_head, news_lid, news_tag, url, news_date, our, looks, type,
				(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments` WHERE com_main_id = `gorod24.online`.`gorod_news`.`id` AND com_for_table = 'gorod_news' AND com_for_column = 'id') as `comments_sum`,
				(SELECT COUNT(*) FROM `gorod24.online`.`gorod_news_photos` WHERE new_id = `gorod24.online`.`gorod_news`.`id` AND status = 1) as `photos_sum`")
		                                                    ->where("on_off='1' and news_date <= NOW() and news_date >= '2015-01-01 00:00:00' and (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` !='{$this->city_id}') > 0")->order("news_date DESC")->offset(0)->limit(6)->commit();

		//Последние объявления города
		$this->view->data['getAds'] = $this->model_adv->db()->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, view_count, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." AND main_catid != 13 ORDER BY up_time DESC LIMIT 6");

		//Последние объявления города (работа)
		$this->view->data['getAdsWork'] = $this->model_adv->db()->getAll("SELECT id, user_name, main_catid, caption as name, descr, price, up_time as date, json_photos, city, view_count, on_off FROM `new_feo_ua`.`adv_adventures` WHERE on_off = 2 AND city_id = ".$this->city_id." AND main_catid = 13 ORDER BY up_time DESC LIMIT 6");
	}
}