<?php
class Controller_404 extends Controller
{
	function action_index($array = array()){

		if (isBot() === false)
		{
			if ($_COOKIE['city_id'])
			{
				$this->view = new View('404.tpl');
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
			$this->view = new View('404.tpl');
			$this->city_id = 1483;
		}

		$this->view->headers['title'] = 'Страница не найдена | Город 24';
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
		
	}

	/* API method nb.getIntermediateResults
  * Промежуточные результаты. Возвращает список номинаций с брендами набравшие большее количество голосов в указанную неделю
  * https://gorod24.online/api/nb.getIntermediateResults/<!city_id!>/<!contest_id!>/<!week!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
  */
	function action_nb_getIntermediateResults($params = array()){
		self::log('nb.getIntermediateResults', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$contest_id = (int)addslashes(urldecode($params[1]));
		$week = (int)addslashes(urldecode($params[2]));
		if(
			empty($city_id)
			or empty($contest_id)
			or empty($week)
		) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_nb->getIntermediateResults($contest_id, $week);
		echo self::getResponse($result);
	}

}
