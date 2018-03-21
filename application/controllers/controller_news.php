<?php
class controller_news extends Controller
{
	function __construct()
	{
		if (isBot() === false)
		{
			if ($_COOKIE['city_id'])
			{
				$this->view = new View('news.tpl');
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
			$this->view = new View('news.tpl');
			$this->city_id = 1483;
		}

		$this->view->headers['title'] = 'Новости | Город 24';
		$this->view->data['menu']['news'] = 'active';
		$this->limit = 32;
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
		//Комментарии
		$this->view->comments = new controller_comments();
		//Опросы
		$this->view->interviews = new controller_interviews();
		//Модели
		$this->model_news = new model_gorod_news();
		$this->view->model_news = new model_gorod_news();
		$this->model_news_look = $this->model_news->_model_news_look();
		$this->model_news_look_day = $this->model_news->_model_news_look_day();
		$this->model_news_razd = $this->model_news->model_razd();
		$this->model_news_audio = $this->model_news->_model_news_audio_streams();

		//$test = $this->model_news_audio->getItemWhere("new_id = 64802 AND status = 1", "*", "adddate DESC");
		//Хлебные крошки
		$this->view->data['breadcrumbs'] = [ "Главная" => $GLOBALS['CONFIG']['HTTP_HOST'], "Новости: ".$this->view->data['city_name'] => $GLOBALS['CONFIG']['HTTP_HOST'].'/news/'];
	}
	
	public function action_index($array = array())
	{
		//Проверка на раздел новостей и на открытие страницы конкретной новости
		if (count($array) >= 1)
		{
			$res =  explode('-', end(explode('/', $array[0])))[0];
			$our = substr($res,-1);
			$our1 = substr($res,0, 4);
			$news_id = substr($res,0,-1);

			if (($our == "o" || $our == "k") && is_numeric($news_id))
			{
				$this->action_p(array("url" => $array[0], "type" => "old"));
			}
			elseif (($our != "o" && $our != "k") && is_numeric($res))
			{
				$this->action_p(array("url" => $array[0], "type" => "new"));
			}
			elseif ($our1 == "tag=")
			{
				$this->rend_news(array("tag", $array[0]));
			}
			elseif ($array[0] == "top25")
			{
				$this->rend_news(array("top25"));
			}
			else
			{
				$this->rend_news(array("category", $array[0]));
			}
		}
		else
		{
			$this->rend_news(array("last"));
		}
	}

	public function action_p($attr)
	{
		$this->view = new View('news-page.tpl');
		$this->view->data['menu']['news'] = 'active';
		$this->view->data['city_name'] = $_COOKIE['city_name'] ? $_COOKIE['city_name'] : getUserCity('name');

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

		//Комментарии
		$this->view->comments = new controller_comments();
		//Опросы
		$this->view->interviews = new controller_interviews();

		//(КОСЯК)
		if ($attr['type'] == "old")
		{
			//Выборка id и our из url строки новости
			$res =  explode('-', end(explode('/', $attr['url'])))[0];
			$our = substr($res,-1) == "o" ? 1 : 0;
			$news_id = substr($res,0,-1);
			$news_db_id = $this->model_news->getItemWhere("news_id = ".$news_id." AND our = ".$our, "id");
			$this->view->data['getNews'] = $this->model_news->getItemWhere("news_id = ".$news_id." AND our = ".$our);
		}
		else
		{
			//Выборка id и our из url строки новости
			$news_id =  explode('-', end(explode('/', $attr['url'])))[0];
			$news_db_id['id'] = $news_id;
			$this->view->data['getNews'] = $this->model_news->getItemWhere("id = ".$news_id);
		}

		$this->view->data['news_parent_id'] = $news_db_id['id'];
		$this->view->headers['title'] = $this->view->data['getNews']['news_head'];
		$this->view->headers['p_id'] = $this->view->data['getNews']['id'];

		//Счетчик просмотров
		$this->model_news->Update(["looks" => ($this->view->data['getNews']['looks']+1)], $this->view->data['getNews']['id']);

		$query = [
			"n_id" => $news_id,
			"our" => ($attr['type'] == "old") ? $our : -1,
			"date" => date("Y-m-d"),
			"time" => date("H:i:s"),
			"ip" => getIp(),
			"cols" => $_SERVER["HTTP_USER_AGENT"],
			"look_from" => "0",
			"uid" => "0"
		];

		$this->model_news->_model_news_look()->InsertUpdate($query);
		$this->model_news->_model_news_look_day()->InsertUpdate($query);

		$GLOBALS['DB']['80.93.183.242']->query("INSERT INTO `main`.`news_look` SET ?u", $query);
		$GLOBALS['DB']['80.93.183.242']->query("INSERT INTO `main`.`news_look_day` SET ?u", $query);

		//Выборка фотографий новости (КОСЯК)
		$this->view->data['getNewsImages'] = $this->model_news->getPhotos($news_db_id['id'],'base0',$this->view->data['getNews']['news_album_id']);

		//Блок новостей "читать так-же" и "новости за сегодня"
		$tags = explode("#", $this->view->data['getNews']['news_tag']);
		$this->view->data['getReadMoreNews'] = $this->model_news->getItemsWhere("on_off='1' and news_date <= NOW() and (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$this->city_id}') > 0 and news_tag LIKE '%".$tags[1]."%' and id != ".$news_db_id['id'], "news_date DESC", 0, 5, "news_head, url");
		$this->view->data['getNewsToday'] = $this->model_news->getItemsWhere("on_off='1' and news_date <= NOW() and (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$this->city_id}') > 0 and id != ".$news_db_id['id'], "news_date DESC", 0, 10, "news_head, news_date, url");
		//Хлебные крошки
		$this->view->data['breadcrumbs'] = [ "Главная" => $GLOBALS['CONFIG']['HTTP_HOST'], "Новости: ".$this->view->data['city_name'] => $GLOBALS['CONFIG']['HTTP_HOST'].'/news/', $this->view->data['getNews']['news_head'] => $GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$this->view->data['getNews']['url']];
	}

	public function rend_news ($array = array())
	{
		switch ($array[0])
		{
			case "tag":
				$tag = substr($array[1],4);
				$this->view->data['breadcrumbs'] = [ "Главная" => $GLOBALS['CONFIG']['HTTP_HOST'], "Новости: ".$this->view->data['city_name'] => $GLOBALS['CONFIG']['HTTP_HOST'].'/news/', "Поиск по тегу: ".$tag => $GLOBALS['CONFIG']['HTTP_HOST'].'/news/tag='.$tag];
				$totalItems = $this->model_news->getCountWhere("on_off='1' and news_date <= NOW() and news_date >= '2015-01-01 00:00:00' and (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$this->city_id}') > 0 and news_tag LIKE '%".$tag."%'");
				$urlPattern = Registry::get('REQUEST_URI').'/?page=(:num)';
				$page = $_GET['page']?$_GET['page']:1;
				$limit = $this->limit;
				$offset = ($page - 1) * $limit;
				$this->view->data['paginator'] = new Paginator($totalItems, $limit, $page, $urlPattern);

				$news = $this->model_news->get("id, news_id, news_head, news_lid, news_tag, url, news_date, our, looks, type, 18plus,
				(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments` WHERE com_main_id = `gorod24.online`.`gorod_news`.`id` AND com_for_table = 'gorod_news' AND com_for_column = 'id') as `comments_sum`,
				(SELECT COUNT(*) FROM `gorod24.online`.`gorod_news_photos` WHERE new_id = `gorod24.online`.`gorod_news`.`id` AND status = 1) as `photos_sum`")
				                                                    ->where("on_off='1' and news_date <= NOW() and news_date >= '2015-01-01 00:00:00' and (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$this->city_id}') > 0 and news_tag LIKE '%".$tag."%'")->order("news_date DESC")->offset($offset)->limit($limit)->commit();

				break;

			case "top25":
				$this->view->data['breadcrumbs'] = [ "Главная" => $GLOBALS['CONFIG']['HTTP_HOST'], "Новости: ".$this->view->data['city_name'] => $GLOBALS['CONFIG']['HTTP_HOST'].'/news/', "Топ 25 новостей за неделю" => $GLOBALS['CONFIG']['HTTP_HOST'].'/news/top25'];
				$totalItems = 24;
				$urlPattern = Registry::get('REQUEST_URI').'/?page=(:num)';
				$page = $_GET['page']?$_GET['page']:1;
				$limit = $this->limit;
				$offset = ($page - 1) * $limit;
				$this->view->data['paginator'] = new Paginator($totalItems, $limit, $page, $urlPattern);
				$query = "
					SELECT  
			          `rot`.`id` AS  `r_id`, 
			          `nt`.*
			         FROM  
			          `gorod_news` AS  `nt` ,  
			          `gorod_news_rotate` AS  `rot` 
			         WHERE  
			          `rot`.`new_id` =  `nt`.`id` AND `nt`.`on_off` = 1 AND `city_id`=".$this->city_id."
			         ORDER BY  `r_id` DESC
			         LIMIT ".$offset.",".$totalItems.";
				";
				$news = $this->model_news->db()->getAll($query);
				break;

			case "category":
				$razd = $this->model_news_razd->getItemWhere("url = '".$array[1]."'", "id, url_ru, url");
				$this->view->data['breadcrumbs'] = [ "Главная" => $GLOBALS['CONFIG']['HTTP_HOST'], "Новости: ".$this->view->data['city_name'] => $GLOBALS['CONFIG']['HTTP_HOST'].'/news/', $razd['url_ru'] => $GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$razd['url']];
				$totalItems = $this->model_news->getCountWhere("on_off='1' and news_date <= NOW() and news_date >= '2015-01-01 00:00:00' and (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$this->city_id}') > 0 and razd_id = ".$razd['id']);
				$urlPattern = Registry::get('REQUEST_URI').'/?page=(:num)';
				$page = $_GET['page']?$_GET['page']:1;
				$limit = $this->limit;
				$offset = ($page - 1) * $limit;
				$this->view->data['paginator'] = new Paginator($totalItems, $limit, $page, $urlPattern);
				$news = $this->model_news->get("id, news_id, news_head, news_lid, news_tag, url, news_date, our, looks, type, 18plus,
				(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments` WHERE com_main_id = `gorod24.online`.`gorod_news`.`id` AND com_for_table = 'gorod_news' AND com_for_column = 'id') as `comments_sum`,
				(SELECT COUNT(*) FROM `gorod24.online`.`gorod_news_photos` WHERE new_id = `gorod24.online`.`gorod_news`.`id` AND status = 1) as `photos_sum`")
				                                                    ->where("on_off='1' and news_date <= NOW() and news_date >= '2015-01-01 00:00:00' and (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$this->city_id}') > 0 and razd_id = ".$razd['id'])->order("news_date DESC")->offset($offset)->limit($limit)->commit();
				break;

			case "last":
				$totalItems = $this->limit * 777;
				$urlPattern = Registry::get('REQUEST_URI').'/?page=(:num)';
				$page = $_GET['page']?$_GET['page']:1;
				$limit = $this->limit;
				$offset = ($page - 1) * $limit;
				$this->view->data['paginator'] = new Paginator($totalItems, $limit, $page, $urlPattern);
				$news = $this->model_news->get("id, news_id, news_head, news_lid, news_tag, url, news_date, our, looks, type, 18plus,
				(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments` WHERE com_main_id = `gorod24.online`.`gorod_news`.`id` AND com_for_table = 'gorod_news' AND com_for_column = 'id') as `comments_sum`,
				(SELECT COUNT(*) FROM `gorod24.online`.`gorod_news_photos` WHERE new_id = `gorod24.online`.`gorod_news`.`id` AND status = 1) as `photos_sum`")
					                         ->where("on_off='1' and news_date <= NOW() and news_date >= '2015-01-01 00:00:00' and (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$this->city_id}') > 0")->order("news_date DESC")->offset($offset)->limit($limit)->commit();
				break;
		}

		//Вспомогательна функция (проверяет тип новости, на условие, что он больше 0)
		function check_type ($var)
		{
			if ($var > 0)
			{
				return $var;
			}
		}

		//Вспомогательная функция меняет местами данные массива по ключам
		function array_swap (array &$array, $key, $key2)
		{
			if (isset($array[$key]) && isset($array[$key2]))
			{
				list($array[$key], $array[$key2]) = array($array[$key2], $array[$key]);
				return true;
			}

			return false;
		}

		$type = array_column($news, "type");
		$keys = array_filter($type, "check_type");
		$position = 1;

		//Формируем новый массив новостей
		foreach ($keys as $key => $value)
		{
			array_swap( $news, $key, $position );
			$position += 3;
		}

		//Функция возвращающая тело новости
		function body_news ($news, $type)
		{
			$m_news = new model_gorod_news();
			$m_audio = $m_news->_model_news_audio_streams();

			//Выборка тегов
			$tags = str_replace(";", "", explode("#",$news['news_tag']));
			$flag = true;
			$tags_items = '';
			array_shift($tags);
			foreach ($tags AS $key => $tag)
			{
				if ($flag)
				{
					$flag = false;
				}
				else
				{
					$tags_items .= "<a href=\"".$GLOBALS['CONFIG']['HTTP_HOST']."/news/tag=".$tag."\">"."#".$tag."</a>";
				}
			}

			if ($news['18plus'] == 1)
			{
				$item_18 = "
					<div class=\"d-item-wrap\">
						<div class=\"item-18\"></div>
					</div>
				";
			}

			if ($news)
			{
				$audio = $m_audio->getItemWhere("new_id = ".$news['id']." AND status = 1", "*", "adddate DESC");
			}

			if ($audio)
			{
				if ($audio['file'])
				{
					$audio_file['audio'] = 'https://xn--e1asq.xn--p1ai' . $audio['file'];
				}
				elseif ($audio['audio'])
				{
					$audio_file['audio'] = 'https://gorod24.online' . $audio['audio'];
				}
				else
				{
					$audio_file['audio'] = NULL;
				}
			}
			else
			{
				$audio_file['audio'] = NULL;
			}

			if ($audio_file['audio'] != NULL)
			{
				$item_audio = "
					<div class=\"d-item-wrap\">
						<div class=\"item-audio audio-off\" data-track-id=\"{$news['id']}\" data-track-status=\"off\"></div>
					</div>
					<audio id=\"track-{$news['id']}\" src=\"{$audio_file['audio']}\" preload=\"none\" style=\"width: 100%;\"></audio>
				";
			}

			switch ($type)
			{
				case 1:
					return '
						<div class="info-block-horizontale grid-item grid-item--width2">
							<div class="info-item">
								<div class="row">
									<div class="col-md-5">
										<div class="img-wrap effect-apollo">
											<a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url'].'" class="info-descr" data-18plus="'.$news['18plus'].'">
												<img src="https://gorod24.online/thrumbs/news/new_'.$news['id'].'_361_240.jpg" alt="alt">
											</a>
											<figcaption></figcaption>
											<div class="img-cont">
												'.$item_18.'
												'.$item_audio.'
											</div>
										</div>
									</div>
									<div class="col-md-7">
										<div class="content">
											<div class="tags">
												'.$tags_items.'
											</div>
											<a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url'].'" class="info-descr" data-18plus="'.$news['18plus'].'">
												<span>'.$news['news_head'].'</span>
											</a>
											<div class="time">
												<span class="icon-clock"></span><time class="text-clock" datetime="'.$news['news_date'].'" title="'.$news['news_date'].'"></time>
											</div>
											<div class="right-block">
												<div class="photos hidden-sm hidden-xs">
													<i class="fa fa-camera" aria-hidden="true"></i></span><span class="text-photos">'.$news['photos_sum'].'</span>
												</div>
												<div class="comments hidden-sm hidden-xs">
													<i class="fa fa-comment" aria-hidden="true"></i><span class="text-comments">'.$news['comments_sum'].'</span>
												</div>
												<div class="views">
													<span class="icon-eye"></span><span class="text-views">'.$news['looks'].'</span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					';
					break;

				case 2:
					return '
						<div class="info-block-verticale grid-item">
							<div class="info-item">
								<div class="img-wrap effect-apollo">
									<a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url'].'" class="info-descr" data-18plus="'.$news['18plus'].'">
										<img src="https://gorod24.online/thrumbs/news/new_'.$news['id'].'_361_240.jpg" alt="alt">
									</a>
									<figcaption></figcaption>
									<div class="img-cont">
										'.$item_18.'
										'.$item_audio.'
									</div>
								</div>
								<div class="content">
									<div class="tags">
										'.$tags_items.'
									</div>
									<a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url'].'" class="info-descr" data-18plus="'.$news['18plus'].'">
										<span>'.$news['news_head'].'</span>
									</a>
									<div class="time">
										<span class="icon-clock"></span><time class="text-clock" datetime="'.$news['news_date'].'" title="'.$news['news_date'].'"></time>
									</div>
									<div class="views">
										<span class="icon-eye"></span><span class="text-views">'.$news['looks'].'</span>
									</div>
								</div>
							</div>
						</div>
					';
					break;

				case 3:
					return '
						<div class="info-block-big grid-item grid-item--width2big">
							<div class="info-item">
								<a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url'].'">
									<div class="bgc-image" style="background-image: url(https://gorod24.online/thrumbs/news/new_'.$news['id'].'_1280_1024.jpg)"></div>
								</a>
								<div class="content">
									<div class="tags">
										'.$tags_items.'
									</div>
									<a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url'].'"><h1>'.$news['news_head'].'</h1></a>
									<p class="hidden-xs">'.$news['news_lid'].'</p>
									<div class="time">
										<span class="icon-clock"></span>
										<time class="text-clock" datetime="'.$news['news_date'].'" title="'.$news['news_date'].'"></time>
									</div>
								</div>
							</div>
						</div>
					';
					break;
				case 4:
					return '
						<div class="banner-wrap grid-item grid-item--width2" style="margin-top: 0;">
							<a href="#">
								<img class="img-responsive" src="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/application/views/gorod24/img/banners/item-'.rand(0, 9).'.gif" alt="alt">
							</a>
						</div>
						<div class="banner-wrap grid-item grid-item--width2" style="margin-top: 0;">
							<a href="#">
								<img class="img-responsive" src="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/application/views/gorod24/img/banners/item-'.rand(0, 9).'.gif" alt="alt">
							</a>
						</div>
					';
					break;
				case 5:
					return '
						<div class="vertical-banners grid-item">
							<div class="banner-wrap" style="margin-top: 0;">
								<a href="#">
									<img class="img-responsive" src="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/application/views/gorod24/img/banners/verticale-'.rand(1, 7).'.gif" alt="alt">
								</a>
							</div>
						</div>
					';
					break;
			}
		}

		$result = '';
		$cicle = '';
		$news_count = 0;

		//Формием тело страницы новостей
		foreach ($news as $item)
		{
			if ($cicle == 2 && $item['type'] > 0)
			{
				$result .= body_news($item, 3);
				$cicle = 3;
				$news_count + 2;
			}
			elseif ($cicle == 2 && $item['type'] == 0)
			{
				if ($news_count == 21)
				{
					$result .= body_news(null, 5); //Баннер вертикальный
					$cicle = 4;
					$news_count ++;
				} else {
					$result .= body_news($item, 2);
					$cicle = 4;
					$news_count++;
				}
			}
			elseif ($cicle == 3)
			{
				$result .= body_news($item, 1);
				$cicle = 1;
				$news_count ++;
				if ($news_count == 8 || $news_count == 16 || $news_count == 24)
				{
					$result .= body_news(null, 4); //Баннер горизонтальный
				}
			}
			elseif ($cicle == 4)
			{
				//var_dump($news_count);
				if ($news_count == 14 || $news_count == 30)
				{
					$result .= body_news(null, 5); //Баннер вертикальный
					$cicle = 3;
					$news_count ++;
				} else {
					$result .= body_news($item, 2);
					$cicle = 3;
					$news_count ++;
				}
			}
			else
			{
				$result .= body_news($item, 1);
				$cicle = 2;
				$news_count ++;
			}
		}

		//var_dump($news_count);

		$this->view->data['getLastNews'] = $result;
	}
}