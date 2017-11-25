<?php
class controller_api extends Controller
{
	protected $request;
	protected $app;
	protected $_model_apiapps;
	protected $_model_news;

	function __construct(){
		header("Content-type: application/json; charset=UTF-8");
		$this->request = $_REQUEST;
		foreach($this->request as $key=>$val){ $this->request[$key]=trim(addslashes($val));}
		if(empty($this->request['publickey'])){ die('{"error":1, "message":"The request failed"}');}
		$this->_model_apiapps = new model_apiapps();
		$this->app = (object)$this->_model_apiapps->getItemWhere("`publickey`='{$this->request['publickey']}' AND `status`=1");
		if(is_null($this->app->id)){ die('{"error":1, "message":"Access denied"}'); }

		$this->_model_cities = new model_cities();
		$this->_model_news = new model_news();
		$this->_model_biznes = new model_feo_biznes();
		$this->_accounts = new model_feo_accounts();
		$this->_model_radio = new model_radio();
		$this->_model_nb = new model_nb();
		$this->_model_rules = new model_rules();
		$this->_model_adv = new model_adventures();
		$this->_model_gazeta = new model_gazeta();
		$this->_model_payment = new model_payment();
		$this->_model_balance = new model_balance();
		$this->_model_uploads = new model_uploads();

		//$this->_accounts->genToken();
		//self::checkSRC();
	}

	private static function getResponse($array){
		$result = [
			"response" => $array
		];
		return json_encode($result, JSON_UNESCAPED_UNICODE);
	}

	private function log($method, $params){
		$this->_model_apiapps->log()->Insert([
			"appid" => $this->app->id,
			"src" => $this->request['src'],
			"method" => $method,
			"params" => json_encode($params, JSON_UNESCAPED_UNICODE),
			"fullurl" => urldecode($_SERVER['REQUEST_URI']),
			"uid" => (int)$this->request['user_id'],
			"date" => date('Y-m-d H:i:s'),
		]);
	}

	private function checkSRC(){
		$a = [];
		if(!empty($this->request['src'])){
		foreach($this->request as $key => $val){ if($key!='src')$a[]=$val;}
		$string=Registry::get('REQUEST_URI').'['.implode(":", $a).":{$this->app->secretkey}]";
		$src = md5($string);
			if($this->request['src']!=$src) die('{"error":1, "message":"The request failed"}');
		}
		else die('{"error":1, "message":"The request failed"}');
	}

	/* API method getCities
	 * Возвращает список доступных городов
	 * https://gorod24.online/api/getCities?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_getCities($params = array()){
		self::log('getCities', $params);
		$result = $this->_model_cities->getItemsWhere("`status`=1", "city_title ASC", null, null);
		echo self::getResponse($result);
	}

	/* API method user.loginByPas
	 * Возвращает данные пользователя по логину и паролю
	 * https://gorod24.online/api/user.loginByPassword/<!login!>/<!password!>/<!IMEI!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_loginByPas($params = array()){
		$login=addslashes($params[0]);
		$password=addslashes($params[1]);
		$imei=addslashes($params[2]);
		if(empty($login) or empty($password) or empty($imei)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->login($login, $password, $imei);
		echo self::getResponse($result);
	}

	/* API method user.loginByTempPassword
	 * Возвращает данные пользователя по логину и временному паролю
	 * https://gorod24.online/api/user.loginByTempPassword/<!login!>/<!password!>/<!IMEI!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_loginByTempPassword($params = array()){
		$login=addslashes($params[0]);
		$password=addslashes($params[1]);
		$imei=addslashes($params[2]);
		if(empty($login) or empty($password) or empty($imei)) die('{"error":1, "message":"The request failed"}');
		$result =  $this->_accounts->login_byTempPassword($login, $password, $imei);
		echo self::getResponse($result);
	}

	/* API method user.sendTempPassword
	 * Высылает временный пароль пользователю в виде SMS (срок жизни пароля 15 минут)
	 * https://gorod24.online/api/user.sendTempPassword/<!login!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_sendTempPassword($params = array()){
		$login=addslashes($params[0]);
		if(empty($login)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->send_temp_password($login);
		echo self::getResponse($result);
	}

	/* API method user.getPhones
	 * Возвращает телефоны пользователя
	 * https://gorod24.online/api/user.getPhones/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_getPhones($params = array()){
		$id=(int)addslashes($params[0]);
		$access_token=$this->request['access_token'];
		if(empty($id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->get_phones($id, $access_token);
		echo self::getResponse($result);
	}

	/* API method user.addPhone
	 * Добавляет телефон пользователя
	 * https://gorod24.online/api/user.addPhone/<!phone_number!>/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_addPhone($params = array()){
		$number=addslashes($params[0]);
		$id=(int)addslashes($params[1]);
		$access_token=$this->request['access_token'];
		if(empty($number) OR empty($id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->add_phone($number,$id, $access_token);
		echo self::getResponse($result);
	}

	/* API method user.delPhone
	 * Удаляет телефон пользователя
	 * https://gorod24.online/api/user.delPhone/<!user_id!>/<!phone_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_delPhone($params = array()){
		$user_id=(int)addslashes($params[0]);
		$phone_id=(int)addslashes($params[1]);
		$access_token=$this->request['access_token'];
		if(empty($user_id) or empty($phone_id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->del_phone($user_id, $phone_id,$access_token);
		echo self::getResponse($result);
	}

	/* API method user.phoneSendCode
	 * Отправляет код подтверждения на номер (не чаще 1 раз в сутки)
	 * https://gorod24.online/api/user.phoneSendCode/<!user_id!>/<!phone_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_phoneSendCode($params = array()){
		$user_id=(int)addslashes($params[0]);
		$phone_id=(int)addslashes($params[1]);
		$access_token=$this->request['access_token'];
		if(empty($user_id) or empty($phone_id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->phone_send_code($user_id, $phone_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method user.phoneConfirmCode
	 * Подтверждает номер по присланному ранее коду из смс
	 * https://gorod24.online/api/user.phoneConfirmCode/<!user_id!>/<!phone_id!>/<!code!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_phoneConfirmCode($params = array()){
		$user_id=(int)addslashes($params[0]);
		$phone_id=(int)addslashes($params[1]);
		$code=addslashes($params[2]);
		$access_token=$this->request['access_token'];
		if(empty($user_id) or empty($phone_id) or empty($code)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->confirm_phone($user_id, $phone_id, $code, $access_token);
		echo self::getResponse($result);
	}

	/* API method user.getEmails
	 * Возвращает email-адреса пользователя
	 * https://gorod24.online/api/user.getEmails/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_getEmails($params = array()){
		$id=(int)addslashes($params[0]);
		$access_token=$this->request['access_token'];
		if(empty($id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->get_emails($id, $access_token);
		echo self::getResponse($result);
	}

	/* API method user.delEmail
	 * Удаляет email пользователя
	 * https://gorod24.online/api/user.delEmail/<!user_id!>/<!email_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_delEmail($params = array()){
		$user_id=(int)addslashes($params[0]);
		$email_id=(int)addslashes($params[1]);
		$access_token=$this->request['access_token'];
		if(empty($user_id) OR empty($email_id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->del_email($user_id, $email_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method user.get
	 * Возвращает данные пользователя по id
	 * https://gorod24.online/api/user.get/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_user_get($params = array()){
		$id=(int)addslashes($params[0]);
		$access_token=$this->request['access_token'];
		if(empty($id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->get_user($id, $access_token);
		echo self::getResponse($result);
	}

	/* API method user.getPublic
	 * Возвращает публичные данные пользователя по id
	 * https://gorod24.online/api/user.getPublic/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_getPublic($params = array()){
		$id=(int)addslashes($params[0]);
		if(empty($id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->get_user_public($id);
		echo self::getResponse($result);
	}

	/* API method user.getVk
	 * Возвращает данные пользователя по id соц сети VK
	 * https://gorod24.online/api/user.getVk/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_getVk($params = array()){
		$id=(int)addslashes($params[0]);
		if(empty($id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->get_user_vk($id);
		echo self::getResponse($result);
	}

	/* API method user.getFb
	 * Возвращает данные пользователя по id соц сети Facebook
	 * https://gorod24.online/api/user.getFb/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_getFb($params = array()){
		$id=(int)addslashes($params[0]);
		if(empty($id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->get_user_fb($id);
		echo self::getResponse($result);
	}

	/* API method user.getOd
	 * Возвращает данные пользователя по id соц сети Odnoklassniki
	 * https://gorod24.online/api/user.getOd/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_getOd($params = array()){
		$id=(int)addslashes($params[0]);
		if(empty($id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->get_user_od($id);
		echo self::getResponse($result);
	}

	/* API method user.register
	 * Создает пользователя
	 * https://gorod24.online/api/user.register/<!IMEI!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_register($params = array()){
		$imei=addslashes($params[0]);

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data=[
			"nik" => $decoded['nik'],
			"login" => $decoded['login'],
			"password" => $decoded['password'],
			"email" => $decoded['email'],
			"phone" => $decoded['phone'],
			"name" => $decoded['name'],
			"surname" => $decoded['surname'],
			"bdate" => $decoded['bdate'],
			"city" => $decoded['city'],
		];

		$result = $this->_accounts->register_user($data, $imei);

		if(!empty($decoded['invite_code']) AND !$result['error']){
			do_action('on_register', $result, $decoded['invite_code']);
		}
		echo self::getResponse($result);
	}

	/* API method user.update
	 * Обновляет данные пользователя
	 * https://gorod24.online/api/user.update/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_user_update($params = array()){
		$user_id=(int)addslashes($params[0]);
		$access_token=$this->request['access_token'];

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data=[
			"nik" => $decoded['nik'],
			"name" => $decoded['name'],
			"surname" => $decoded['surname'],
			"bdate" => $decoded['bdate'],
			"city" => $decoded['city'],
		];
		if(empty($user_id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->update_user($data, $user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method user.changeAvatar
	 * Загрузка и изменение аватарки пользователя.
	 * https://gorod24.online/api/user.changeAvatar/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_changeAvatar($params = array()){
		self::log('user.changeAvatar', $params);
		$user_id=(int)addslashes($params[0]);
		$access_token=$this->request['access_token'];

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'image/jpeg') != 0){
			die('{"error":1, "message":"Content type must be: image/jpeg"}');
		}

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		//Receive the RAW post data.
		$content = trim(file_get_contents("php://input"));
		$file_name = uniqid($user_id.'_');
		file_put_contents(APPDIR . '/uploads/image/avatars/'.$file_name.'.jpg',  $content);

		$id = $this->_model_uploads->Insert([
			"name" => $file_name.'.jpg',
			"ext" => 'jpg',
			"type" => 'image',
			"size" => getimagesize(APPDIR . '/uploads/image/avatars/'.$file_name.'.jpg'),
			"destination" => '/uploads/image/avatars/',
			"author" => 0,
			"date" => date("Y-m-d H:i:s"),
			"modified" => date("Y-m-d H:i:s"),
			"status" => 1,
			"other" => '',
		]);

		$data=[
			"ava_file" => $GLOBALS['CONFIG']['HTTP_HOST'].'/uploads/image/thrumb/'.$id.'_100_100.jpg',
		];
		$this->_accounts->update_user($data, $user_id, $access_token);

		$result = [
			'id' => $id,
			'name' => $file_name.'.jpg',
			'original' => $GLOBALS['CONFIG']['HTTP_HOST'].'/uploads/image/avatars/'.$file_name.'.jpg',
			'thrumb_50' => $GLOBALS['CONFIG']['HTTP_HOST'].'/uploads/image/thrumb/'.$id.'_50_50.jpg',
			'thrumb_100' => $GLOBALS['CONFIG']['HTTP_HOST'].'/uploads/image/thrumb/'.$id.'_100_100.jpg',
			'thrumb_150' => $GLOBALS['CONFIG']['HTTP_HOST'].'/uploads/image/thrumb/'.$id.'_150_150.jpg',
		];
		echo self::getResponse($result);
	}

	/* API method user.changePassword
	 * Меняет пароль пользователя
	 * https://gorod24.online/api/user.changePassword/<!user_id!>/<!password!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_user_changePassword($params = array()){
		$id=(int)addslashes($params[0]);
		$password=addslashes($params[1]);
		$access_token=$this->request['access_token'];
		if(empty($id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_accounts->user_change_password($password, $id, $access_token);
		echo self::getResponse($result);
	}


	/* API method news.getRazds
	 * Возвращает список доступных рубрик
	 * https://gorod24.online/api/news.getRazds?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_getRazds($params = array()){
		self::log('news.getRazds', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		if($this->request['order']){
			switch($this->request['order']){
				case "name": $order = "`name_razd` ASC"; break;
				case "name desc": $order = "`name_razd` DESC"; break;
				case "id": $order = "`id` ASC"; break;
				case "id desc": $order = "`id` DESC"; break;
			}
		} else $order = "`name_razd` ASC";
		$result = $this->_model_news->razd()->getItemsWhere("`on_off`='1'", $order, null, null, "`id`, `name_razd` as `name`");
		echo self::getResponse($result);
	}

	/* API method news.getByRazd
	 * Возвращает список новостей по рубрике
	 * https://gorod24.online/api/news.getByRazd/<city_id>/<!razd!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_getByRazd($params = array()){
		self::log('news.getByRazd', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		$city_id=(int)addslashes($params[0]);
		$param=addslashes(urldecode($params[1]));
		$uid=(int)addslashes(urldecode($params[2]));
		if(empty($city_id) OR empty($param)) die('{"error":1, "message":"The request failed"}');
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_news->getByRazd($city_id, $param, $uid, $start, $limit);
		echo self::getResponse($result);
	}

	/* API method news.getAll
	 * Возвращает список новостей по городу
	 * https://gorod24.online/api/news.getAll/<city_id>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_getAll($params = array()){
		self::log('news.getByRazd', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		$city_id=(int)addslashes($params[0]);
		$uid=(int)addslashes(urldecode($params[1]));
		if(empty($city_id)) die('{"error":1, "message":"The request failed"}');
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_news->getByTown($city_id, $uid, $start, $limit);
		echo self::getResponse($result);
	}

	/* API method news.getByTag
	 * Возвращает список новостей по тегу
	 * https://gorod24.online/api/news.getByTag/<city_id>/<!teg!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_getByTag($params = array()){
		self::log('news.getByTag', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		$city_id=(int)addslashes($params[0]);
		$param = addslashes(urldecode($params[1]));
		$uid=(int)addslashes(urldecode($params[2]));
		if(empty($city_id) OR empty($param)) die('{"error":1, "message":"The request failed"}');
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_news->getByTag($city_id, $param, $uid, $start, $limit);
		echo self::getResponse($result);
	}

	/* API method news.getBySearch
	 * Возвращает список новостей по искому слову
	 * https://gorod24.online/api/news.getBySearch/<city_id>/<!search_words!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_getBySearch($params = array()){
		self::log('news.getBySearch', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		$city_id=(int)addslashes($params[0]);
		$param = addslashes(urldecode($params[1]));
		$uid=(int)addslashes(urldecode($params[2]));
		if(empty($city_id) OR empty($param)) die('{"error":1, "message":"The request failed"}');
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_news->getBySearch($city_id, $param, $uid, $start, $limit);
		echo self::getResponse($result);
	}

	/* API method news.getFavourite
	 * Возвращает список избранных новостей
	 * https://gorod24.online/api/news.getFavourite/<city_id>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_news_getFavourite($params = array()){
		self::log('news.getFavourite', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		$city_id=(int)addslashes($params[0]);
		$user_id=(int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];
		if(empty($city_id) OR empty($user_id)) die('{"error":1, "message":"The request failed"}');
		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_news->getFavourite($city_id, $user_id, $start, $limit);
		echo self::getResponse($result);
	}

	/* API method news.get
	 * Возвращает новость подробно
	 * https://gorod24.online/api/news.get/base1/29864?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_get($params = array()){
		self::log('news.get', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		if(empty($params[0])) die('{"error":1, "message":"The request failed"}');
		$start = 0; $limit = 20;
		$base = addslashes(urldecode($params[0]));
		$id = (int)addslashes(urldecode($params[1]));
		$uid = (int)addslashes(urldecode($params[2]));
		$fields = "news_id, news_head, news_lid, news_body, news_video_you, url, url_ru, (concat('http://feo.ua/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, `looks`, (if(`our`=1,'base1','base2')) as `base`";
		switch($base){
			case 'base1': $result=$this->_model_news->our()->getItem($id, $fields);break;
			case 'base2': $result=$this->_model_news->kafa()->getItem($id, $fields);break;
			default: $result=$this->_model_news->our()->getItem($id, $fields);break;
		}
		if(!empty($result)){
			$result['photos'] = $this->_model_news->getPhotos($id, $base);
			$result['comments'] = $this->_model_news->getComments($id, $base);
			if(!empty($uid)){
				$md5_url = md5( 'http://feo.ua/news/'.$result['url'] );
				$md5_url_ru = md5( 'http://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($result['url_ru'] ) );
				$pages = $this->_model_news->db()->getCol("SELECT `id` FROM `new_feo_ua`.`like_pages` WHERE (`md5_url`=?s OR `md5_url`=?s)", $md5_url_ru, $md5_url);
				$result['favorites']=$this->_model_news->db()->getOne("SELECT COUNT(*) FROM `new_feo_ua`.`like_rates` WHERE `uid`=?i and `cid` IN (?a)", $uid, $pages)?1:0;
			}
		}
		//$result = $this->_model_news->getByTag($param, $start, $limit);
		echo self::getResponse($result);
	}

	/* API method news.like
	 * Помечает новость, как Избранное
	 * https://gorod24.online/api/news.like/<!base!>/<!news_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_like($params = array()){
		self::log('news.like', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		$base = addslashes(urldecode($params[0]));
		$id = (int)addslashes(urldecode($params[1]));
		$uid = (int)addslashes(urldecode($params[2]));
		if(empty($base) or empty($id) or empty($uid)) die('{"error":1, "message":"The request failed"}');
		$result=$this->_model_news->likeNew($base, $id, $uid);
		echo self::getResponse($result);
	}

	/* API method news.dislike
	 * Убирает пометку Избранное
	 * https://gorod24.online/api/news.dislike/<!base!>/<!news_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_dislike($params = array()){
		self::log('news.dislike', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		$base = addslashes(urldecode($params[0]));
		$id = (int)addslashes(urldecode($params[1]));
		$uid = (int)addslashes(urldecode($params[2]));
		if(empty($base) or empty($id) or empty($uid)) die('{"error":1, "message":"The request failed"}');
		$result=$this->_model_news->dislikeNew($base, $id, $uid);
		echo self::getResponse($result);
	}


	/* API method news.uploadPhoto
	 * Загрузка фотографии для новости пользователя.
	 * https://gorod24.online/api/user.changeAvatar/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_uploadPhoto($params = array()){
		self::log('news.uploadPhoto', $params);
		$user_id=(int)addslashes($params[0]);
		$access_token=$this->request['access_token'];

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'image/jpeg') != 0){
			die('{"error":1, "message":"Content type must be: image/jpeg"}');
		}

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		//Receive the RAW post data.
		$content = trim(file_get_contents("php://input"));
		$file_name = uniqid($user_id.'_');
		file_put_contents(APPDIR . '/uploads/image/usernews/'.$file_name.'.jpg',  $content);

		$id = $this->_model_uploads->Insert([
			"name" => $file_name.'.jpg',
			"ext" => 'jpg',
			"type" => 'image',
			"size" => getimagesize(APPDIR . '/uploads/image/usernews/'.$file_name.'.jpg'),
			"destination" => '/uploads/image/usernews/',
			"author" => 0,
			"date" => date("Y-m-d H:i:s"),
			"modified" => date("Y-m-d H:i:s"),
			"status" => 1,
			"other" => '',
		]);

		$result = [
			'id' => $id,
			'name' => $file_name.'.jpg',
			'original' => $GLOBALS['CONFIG']['HTTP_HOST'].'/uploads/image/usernews/'.$file_name.'.jpg',
			'thrumb_50' => $GLOBALS['CONFIG']['HTTP_HOST'].'/uploads/image/thrumb/'.$id.'_50_50.jpg',
			'thrumb_100' => $GLOBALS['CONFIG']['HTTP_HOST'].'/uploads/image/thrumb/'.$id.'_100_100.jpg',
			'thrumb_150' => $GLOBALS['CONFIG']['HTTP_HOST'].'/uploads/image/thrumb/'.$id.'_150_150.jpg',
		];
		echo self::getResponse($result);
	}

	/* API method news.addNew
	 * Добавить новость
	 * https://gorod24.online/api/news.addNew/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_news_addNew($params = array()){
		self::log('news.addNew', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data = [
			"name"=>$decoded['name'],
			"text"=>$decoded['text'],
			"tags"=>$decoded['tags'],
			"photos"=>$decoded['photos'],
			"latitude"=>$decoded['latitude'],
			"longitude"=>$decoded['longitude'],
		];

		if(
			empty($city_id)
			or empty($user_id)
			or empty($data['name'])
			or empty($data['text'])
		) die('{"error":1, "message":"The request failed"}');

		$result = $this->_model_news->addNew($user_id, $data);

		echo self::getResponse($result);
	}

	/* API method news.updateNew
	 * Изменить новость
	 * https://gorod24.online/api/news.updateNew/<!city_id!>/<!user_id!>/<!new_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_news_updateNew($params = array()){
		self::log('news.updateNew', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$new_id = (int)addslashes(urldecode($params[2]));

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data = [
			"name"=>$decoded['name'],
			"text"=>$decoded['text'],
			"tags"=>$decoded['tags'],
			"photos"=>$decoded['photos'],
			"latitude"=>$decoded['latitude'],
			"longitude"=>$decoded['longitude'],
		];

		if(
			empty($city_id)
			or empty($user_id)
			or empty($new_id)
			or empty($data['name'])
			or empty($data['text'])
		) die('{"error":1, "message":"The request failed"}');

		$result = $this->_model_news->updateNew($user_id, $data, $new_id);

		echo self::getResponse($result);
	}

	/* API method news.getMyNew
	 * Выводит новость добавленную пользователем
	 * https://gorod24.online/api/news.getMyNew/<!city_id!>/<!user_id!>/<!new_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_news_getMyNew($params = array()){
		self::log('news.getMyNew', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$new_id = (int)addslashes(urldecode($params[2]));

		if(
			empty($city_id)
			or empty($user_id)
			or empty($new_id)
		) die('{"error":1, "message":"The request failed"}');

		$result = $this->_model_news->getCustomNew($new_id);

		echo self::getResponse($result);
	}

	/* API method news.getMyNews
	 * Выводит список новостей добавленных пользователем
	 * https://gorod24.online/api/news.getMyNews/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_news_getMyNews($params = array()){
		self::log('news.getMyNews', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));

		if(
			empty($city_id)
			or empty($user_id)
		) die('{"error":1, "message":"The request failed"}');

		$result = $this->_model_news->getCustomNews($user_id);
		echo self::getResponse($result);
	}

	/* API method biznes.getRubrics
	 * Возвращает список рубрик и подрубрик бизнес каталога
	 * https://gorod24.online/api/biznes.getRubrics/<city_id>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_biznes_getRubrics($params = array()){
		self::log('biznes.getRubrics', $params);
		if($this->app->access_bk==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$result = $this->_model_biznes->getBiznesRubrics();
		echo self::getResponse($result);
	}

	/* API method social.getRubrics
	 * Возвращает список рубрик и подрубрик каталога социальных учереждений
	 * https://gorod24.online/api/social.getRubrics/<city_id>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_social_getRubrics($params = array()){
		self::log('biznes.getRubrics', $params);
		if($this->app->access_bk==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$result = $this->_model_biznes->getSocialRubrics();
		echo self::getResponse($result);
	}

	/* API method biznes.getRazdel
	 * Возвращает список предприятий подрубрики
	 * https://gorod24.online/api/biznes.getRazdel/<city_id>/<!id!>/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_biznes_getRazdel($params = array()){
		self::log('biznes.getRazdel', $params);
		if($this->app->access_bk==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$id = (int)addslashes(urldecode($params[1]));
		$user_id = (int)addslashes(urldecode($params[2]));
		$latitude = $this->request['latitude'];
		$longitude = $this->request['longitude'];
		if(empty($id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_biznes->getRazdel($id, $latitude, $longitude);
		echo self::getResponse($result);
	}

	/* API method biznes.get
	 * Возвращает информацию о предприятии
	 * https://gorod24.online/api/biznes.get/<!type!>/<!id!>/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_biznes_get($params = array()){
		self::log('biznes.get', $params);
		if($this->app->access_bk==0) die('{"error":1, "message":"Access denied"}');
		$type = addslashes(urldecode($params[0]));
		$id = (int)addslashes(urldecode($params[1]));
		$user_id = (int)addslashes(urldecode($params[2]));
		$latitude = $this->request['latitude'];
		$longitude = $this->request['longitude'];
		if(empty($type) OR empty($id)) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_biznes->getPodrobno($type, $id, $latitude, $longitude);
		echo self::getResponse($result);
	}

	/* API method banners.check
	 * Возвращает Верхний баннер
	 * https://gorod24.online/api/banners.check/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_banners_check($params = array()){
		self::log('banners.getTop', $params);
		$user_id = (int)addslashes(urldecode($params[0]));
		$access_token=$this->request['access_token'];
		$result = $this->_model_apiapps->checkBanner($user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method banners.getTop
	 * Возвращает Верхний баннер
	 * https://gorod24.online/api/banners.getTop/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_banners_getTop($params = array()){
		self::log('banners.getTop', $params);
		$user_id = (int)addslashes(urldecode($params[0]));
		$access_token=$this->request['access_token'];
		$result = $this->_model_apiapps->getTopBanner($user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method banners.getList
	 * Возвращает списковый баннер
	 * https://gorod24.online/api/banners.getList/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_banners_getList($params = array()){
		self::log('banners.getList', $params);
		$user_id = (int)addslashes(urldecode($params[0]));
		$access_token=$this->request['access_token'];
		$result = $this->_model_apiapps->getListBanner($user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method banners.getPopup
	 * Возвращает Всплывающий баннер
	 * https://gorod24.online/api/banners.getPopup/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_banners_getPopup($params = array()){
		self::log('banners.getPopup', $params);
		$user_id = (int)addslashes(urldecode($params[0]));
		$access_token=$this->request['access_token'];
		$result = $this->_model_apiapps->getPopupBanner($user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method radio.getAudioStream
	 * Возвращает аудио стрим города
	 * https://gorod24.online/api/radio.getAudioStream/<!city_id!>/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_radio_getAudioStream($params = array()){
		self::log('radio.getAudioStream', $params);
		if($this->app->access_radio==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		if(empty($city_id)) die('{"error":1, "message":"The request failed"}');
		$access_token=$this->request['access_token'];
		$result = $this->_model_radio->getAudioStream($city_id, $user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method radio.getProgramRubrics
	 * Возвращает список рубрик программ
	 * https://gorod24.online/api/radio.getProgramRubrics/<!city_id!>/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_radio_getProgramRubrics($params = array()){
		self::log('radio.getProgramRubrics', $params);
		if($this->app->access_radio==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		if(empty($city_id)) die('{"error":1, "message":"The request failed"}');
		$access_token=$this->request['access_token'];
		$result = $this->_model_radio->getProgramRubrics($city_id, $user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method radio.getPrograms
	 * Возвращает список программ рубрики
	 * https://gorod24.online/api/radio.getPrograms/<!city_id!>/<!rubric_id!>/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>&start=0&limit=20
	 */
	function action_radio_getPrograms($params = array()){
		self::log('radio.getPrograms', $params);
		if($this->app->access_radio==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$rubric_id = (int)addslashes(urldecode($params[1]));
		$user_id = (int)addslashes(urldecode($params[2]));
		if(empty($city_id)) die('{"error":1, "message":"The request failed"}');
		$access_token=$this->request['access_token'];
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_radio->getPrograms($city_id, $rubric_id, $start, $limit, $user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method radio.sendQuestion
	 * Написать ведущему или передать привет
	 * https://gorod24.online/api/radio.sendQuestion/<!city_id!>/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>&name=<!name!>&phone=<!phone!>&text=<!text!>
	 */
	function action_radio_sendQuestion($params = array()){
		self::log('radio.sendQuestion', $params);
		if($this->app->access_radio==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		if(empty($city_id)) die('{"error":1, "message":"The request failed"}');
		$access_token=$this->request['access_token'];
		$data = [
			'name' => $this->request['name'],
			'phone' => $this->request['phone'],
			'text' => $this->request['text'],
		];
		if(empty($this->request['name']) or empty($this->request['phone']) or empty($this->request['text'])) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_radio->sendQuestion($city_id, $data, $user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method nb.getCurrentContest
	 * Выбирает текущий активный конкур для города
	 * https://gorod24.online/api/nb.getCurrentContest/<!city_id!>/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_getCurrentContest($params = array()){
		self::log('nb.getCurrentContest', $params);
		if($this->app->access_nb==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];
		if(empty($city_id) or empty($user_id) or empty($access_token)) die('{"error":1, "message":"The request failed"}');
		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');
		$user = $this->_accounts->get_user($user_id, $access_token);
		$result = $this->_model_nb->getCurrentContest($city_id, $user);
		echo self::getResponse($result);
	}

	/* API method nb.getSex
	 * Выбирает доступные значения поля "пол"
	 * https://gorod24.online/api/nb.getSex?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_getSex($params = array()){
		$result = [
			'1' => 'Мужской',
			'2' => 'Женский',
		];
		echo self::getResponse($result);
	}

	/* API method nb.getAge
	 * Выбирает доступные значения поля "возраст"
	 * https://gorod24.online/api/nb.getAge?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_getAge($params = array()){
		$result = [
			[ 'id' => '1', 'name' => 'до 18' ],
			[ 'id' => '2', 'name' => 'от 18 до 25'],
			[ 'id' => '3', 'name' => 'от 26 до 35'],
			[ 'id' => '4', 'name' => 'от 36 до 45'],
			[ 'id' => '5', 'name' => 'от 46 до 55'],
			[ 'id' => '6', 'name' => 'после 56'],
		];
		echo self::getResponse($result);
	}

	/* API method nb.createAnketa
	 * Создание анкеты голосования если ее нету
	 * https://gorod24.online/api/nb.createAnketa/<!city_id!>/<!contest_id!>/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_createAnketa($params = array()){
		self::log('nb.createAnketa', $params);
		if($this->app->access_nb==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$contest_id = (int)addslashes(urldecode($params[1]));
		$user_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}

		//Receive the RAW post data.
		$content = trim(file_get_contents("php://input"));
		//Attempt to decode the incoming RAW post data from JSON.
		$decoded = json_decode($content, true);

		$data = [
			"name"=>$decoded['name'],
			"surname"=>$decoded['surname'],
			"second_name"=>$decoded['second_name'],
			"sex"=>$decoded['sex'],
			"age"=>$decoded['age'],
			"address"=>$decoded['address'],
			"phone"=>$decoded['phone'],
			"email"=>$decoded['email'],
			"motivation"=>$decoded['motivation'],
		];
		if(
			   empty($city_id)
			or empty($contest_id)
			or empty($user_id)
			or empty($data['name'])
			or empty($data['surname'])
			or empty($data['second_name'])
			or empty($data['sex'])
			or empty($data['age'])
			or empty($data['phone'])
			or empty($data['email'])
		) die('{"error":1, "message":"The request failed data '.$contest_id.'"}');
		$result = $this->_model_nb->createAnketa($contest_id, $data, $user_id);
		echo self::getResponse($result);
	}

	/* API method nb.setStep2
	 * Шаг 2. Запись фирмы Номинация Народный Бренд
	 * https://gorod24.online/api/nb.setStep2/<!city_id!>/<!contest_id!>/<!anketa_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>&brend=<!brend_name!>
	 */
	function action_nb_setStep2($params = array()){
		self::log('nb.setStep2', $params);
		if($this->app->access_nb==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$contest_id = (int)addslashes(urldecode($params[1]));
		$anketa_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];
		$brend=$this->request['brend'];
		if(
			   empty($city_id)
			or empty($contest_id)
			or empty($anketa_id)
			or empty($brend)
		) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_nb->setStep2($contest_id, $anketa_id, $brend);
		echo self::getResponse($result);
	}

	/* API method nb.getNominations
	 * Шаг 3. Возвращает список номинаций и брендов в номинациях
	 * https://gorod24.online/api/nb.getNominations/<!city_id!>/<!contest_id!>/<!anketa_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_getNominations($params = array()){
		self::log('nb.getNominations', $params);
		if($this->app->access_nb==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$contest_id = (int)addslashes(urldecode($params[1]));
		$anketa_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];
		if(
			   empty($city_id)
			or empty($contest_id)
			or empty($anketa_id)
		) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_nb->getNominations($contest_id, $anketa_id);
		echo self::getResponse($result);
	}

	/* API method nb.setNomination
	 * Шаг 3. Записывает выбранный бренд номинации в анкету пользователя
	 * https://gorod24.online/api/nb.setNomination/<!city_id!>/<!contest_id!>/<!anketa_id!>/<!nomination_id!>/<!brend_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_setNomination($params = array()){
		self::log('nb.setNomination', $params);
		if($this->app->access_nb==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$contest_id = (int)addslashes(urldecode($params[1]));
		$anketa_id = (int)addslashes(urldecode($params[2]));
		$nomination_id = (int)addslashes(urldecode($params[3]));
		$brend_id = (int)addslashes(urldecode($params[4]));
		$access_token=$this->request['access_token'];
		if(
			   empty($city_id)
			or empty($contest_id)
			or empty($anketa_id)
			or empty($nomination_id)
			or empty($brend_id)
		) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_nb->setNomination($contest_id, $anketa_id, $nomination_id, $brend_id);
		echo self::getResponse($result);
	}

	/* API method nb.setCustomNomination
	 * Шаг 3. Записывает вписанный (свой вариант) бренд номинации в анкету пользователя
	 * https://gorod24.online/api/nb.setCustomNomination/<!city_id!>/<!contest_id!>/<!anketa_id!>/<!nomination_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>&brend=<!brend_name!>
	 */
	function action_nb_setCustomNomination($params = array()){
		self::log('nb.setCustomNomination', $params);
		if($this->app->access_nb==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$contest_id = (int)addslashes(urldecode($params[1]));
		$anketa_id = (int)addslashes(urldecode($params[2]));
		$nomination_id = (int)addslashes(urldecode($params[3]));
		$access_token=$this->request['access_token'];
		$brend=$this->request['brend'];
		if(
			   empty($city_id)
			or empty($contest_id)
			or empty($anketa_id)
			or empty($nomination_id)
			or empty($brend)
		) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_nb->setCustomNomination($contest_id, $anketa_id, $nomination_id, $brend);
		echo self::getResponse($result);
	}

	/* API method nb.getWeeksIntermediateResults
	 * Промежуточные результаты. Возвращает список недель доступных для конкурса
	 * https://gorod24.online/api/nb.getWeeksIntermediateResults/<!city_id!>/<!contest_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_getWeeksIntermediateResults($params = array()){
		self::log('nb.getWeeksIntermediateResults', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$contest_id = (int)addslashes(urldecode($params[1]));
		if(
			   empty($city_id)
			or empty($contest_id)
		) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_nb->getWeeksIntermediateResults($contest_id);
		echo self::getResponse($result);
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

	/* API method nb.getFinishedContest
	 * Результаты. Возвращает список конкурсов по которым уже есть результаты голосования
	 * https://gorod24.online/api/nb.getFinishedContest/<!city_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_getFinishedContest($params = array()){
		self::log('nb.getFinishedContest', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		if(
			   empty($city_id)
		) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_nb->getFinishedContest($city_id);
		echo self::getResponse($result);
	}

	/* API method nb.getResultsContest
	 * Результаты. Возвращает результаты голосования конкурса
	 * https://gorod24.online/api/nb.getResultsContest/<!city_id!>/<!contest_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_getResultsContest($params = array()){
		self::log('nb.getResultsContest', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$contest_id = (int)addslashes(urldecode($params[1]));
		if(
			   empty($city_id)
			   or empty($contest_id)
		) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_nb->getResultsContest($contest_id);
		echo self::getResponse($result);
	}

	/* API method nb.getResultsContestDetails
	 * Результаты. Возвращает подробности результатов голосования конкурса по бренду (количество голосов по возрастной группе)
	 * https://gorod24.online/api/nb.getResultsContestDetails/<!city_id!>/<!contest_id!>/<!brend_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_getResultsContestDetails($params = array()){
		self::log('nb.getResultsContestDetails', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$contest_id = (int)addslashes(urldecode($params[1]));
		$brend_id = (int)addslashes(urldecode($params[2]));
		if(
			   empty($city_id)
			   or empty($contest_id)
			   or empty($brend_id)
		) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_nb->getResultsContestDetails($contest_id, $brend_id);
		echo self::getResponse($result);
	}

	/* API method rules.getRubrics
	 * Возвращает список рубрик в разделе "Правила" (помощь)
	 * https://gorod24.online/api/rules.getRubrics/<!city_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_rules_getRubrics($params = array()){
		self::log('rules.getRubrics', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		/*
		if(
			   empty($city_id)
		) die('{"error":1, "message":"The request failed"}');
		*/
		$result = $this->_model_rules->getRubrics($city_id);
		echo self::getResponse($result);
	}

	/* API method rules.get
	 * Возвращает текст и дополнительную информацию статьи "Правил" (помощи)
	 * https://gorod24.online/api/rules.get/<!city_id!>/<!item_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_rules_get($params = array()){
		self::log('rules.get', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$item_id = (int)addslashes(urldecode($params[1]));

		if(
			empty($item_id)
		) die('{"error":1, "message":"The request failed"}');

		$result = $this->_model_rules->getRule($city_id, $item_id);
		echo self::getResponse($result);
	}

	/* API method adv.getRubrics
	 * Возвращает список рубрик и подрубрик
	 * https://gorod24.online/api/adv.getRubrics/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getRubrics($params = array()){
		self::log('adv.getRubrics', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];
		if(
			empty($city_id)
		) die('{"error":1, "message":"The request failed"}');

		$result = $this->_model_adv->getRubrics($city_id);
		echo self::getResponse($result);
	}

	/* API method adv.getFilters
	 * Возвращает список Фильтров
	 * https://gorod24.online/api/adv.getFilters/<!city_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>&main=<!main_id!>&sub=<!sub_id!>
	 */
	function action_adv_getFilters($params = array()){
		self::log('adv.getFilters', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$main_catid = (int)$this->request['main'];
		$sub_catid = (int)$this->request['sub'];
		$access_token=$this->request['access_token'];
		if(
			empty($city_id)
		) die('{"error":1, "message":"The request failed"}');

		$result = $this->_model_adv->getFilters($city_id, $main_catid, $sub_catid);
		echo self::getResponse($result);
	}

	/* API method adv.getList
	 * Возвращает список объявлений
	 * https://gorod24.online/api/adv.getList/<!city_id!>?publickey=<!YOUR_PUBLIC_KEY!>&main=<!main_id!>&sub=<!sub_id!>
	 */
	function action_adv_getList($params = array()){
		self::log('adv.getList', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$main_catid = (int)$this->request['main'];
		$sub_catid = (int)$this->request['sub'];
		$access_token=$this->request['access_token'];
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		if(
			empty($city_id)
		) die('{"error":1, "message":"The request failed"}');

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			$filters = null;
		}
		else {
			$content = trim(file_get_contents("php://input"));
			$filters = json_decode($content, true);
		}

		$result = $this->_model_adv->getList($city_id, $user_id, $main_catid, $sub_catid, $start, $limit, $filters);
		echo self::getResponse($result);
	}

	/* API method adv.get
	 * Возвращает подробную информацию об объявлении
	 * https://gorod24.online/api/adv.get/<!city_id!>/<!adv_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_adv_get($params = array()){
		self::log('adv.get', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$adv_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];
		if(
			empty($city_id)
			or empty($adv_id)
		) die('{"error":1, "message":"The request failed"}');

		$result = $this->_model_adv->getAdv($city_id, $adv_id);
		echo self::getResponse($result);
	}

	/* API method adv.uploadPhoto
	 * Загрузка картинок объявления
	 * https://gorod24.online/api/adv.uploadPhoto?publickey=<!YOUR_PUBLIC_KEY!>&name=<!file_name!>
	 */
	function action_adv_uploadPhoto($params = array()){
		self::log('adv.uploadPhoto', $params);
		$name = addslashes(urldecode($this->request['name']));

        $file_name = explode(".",$name);
        $file_ext = strtolower($file_name[count($file_name)-1]);
		if (in_array($file_ext,array('jpg','jpeg'))) {
			$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
			if(strcasecmp($contentType, 'image/jpeg') != 0){
				die('{"error":1, "message":"Content type must be: image/jpeg"}');
			}
			$content = trim(file_get_contents("php://input"));
			$name = 'adv_'.md5(time().$name.uniqid()).".".$file_ext;
			file_put_contents(APPDIR . '/uploads/image/'.$name,  $content);
			$result = $this->_model_adv->insertImage($name, $adv_id);

			$ch = curl_init();
			$curlConfig = array(
				CURLOPT_URL => 'http://feo.ua/obv/ftp/',
				CURLOPT_POST => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_POSTFIELDS => ['file'=>$content, 'name'=>$name, 'id'=>$result['id']]
			);

			curl_setopt_array($ch, $curlConfig);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: text/xml, application/xml, application/xhtml+xml, text/html;q=0.9, text/plain;q=0.8, text/css, image/png, image/jpeg, image/gif;q=0.8, application/x-shockwave-flash, video/mp4;q=0.9, flv-application/octet-stream;q=0.8, video/x-flv;q=0.7, audio/mp4, application/futuresplash, */*;q=0.5,
			Content-Type: application/json"
			));
			$r = curl_exec($ch);
			unlink(APPDIR . '/uploads/image/'.$name);


			echo self::getResponse($result);
		}
		else die('{"error":1, "message":"Incorect file extension"}');
	}

	/* API method adv.getRegions
	 * Возвращает доступные регионы
	 * https://gorod24.online/api/adv.getRegions/<!city_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getRegions($params = array()){
		self::log('adv.getRegions', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$result = [
			[ "id"=>261, "name"=>"Крым" ]
		];
		echo self::getResponse($result);
	}

	/* API method adv.getCities
	 * Возвращает доступные Города для региона
	 * https://gorod24.online/api/adv.getCities/<!city_id!>/<!region_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getCities($params = array()){
		self::log('adv.getRegions', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$region_id = (int)addslashes(urldecode($params[1]));
		$result = [
			[ "id"=>1483, "name"=>"Феодосия" ],
			[ "id"=>1500537, "name"=>"Береговое" ],
			[ "id"=>1500545, "name"=>"Приморский" ],
			[ "id"=>1500539, "name"=>"Коктебель" ],
		];
		echo self::getResponse($result);
	}

	/* API method adv.getUnits
	 * Возвращает доступные Единицы измерений (для цены)
	 * https://gorod24.online/api/adv.getUnits/<!city_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getUnits($params = array()){
		self::log('adv.getUnits', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$result = $this->_model_adv->getUnits();
		echo self::getResponse($result);
	}

	/* API method adv.getOptions
	 * Возвращает доступные опции для подкатегории
	 * https://gorod24.online/api/adv.getOptions/<!city_id!>/<!main_id!>/<!sub_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getOptions($params = array()){
		self::log('adv.getOptions', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$main_id = (int)addslashes(urldecode($params[1]));
		$sub_id = (int)addslashes(urldecode($params[2]));
		if(
			empty($city_id)
			or empty($main_id)
			or empty($sub_id)
		) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_adv->getOptions($main_id, $sub_id);
		echo self::getResponse($result);
	}

	/* API method adv.getGazeta
	 * Возвращает доступные газеты с ближайшей датой выхода
	 * https://gorod24.online/api/adv.getGazeta/<!city_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getGazeta($params = array()){
		self::log('adv.getGazeta', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$limit = 2;
		if(
			empty($city_id)
		) die('{"error":1, "message":"The request failed"}');
		$result = $this->_model_gazeta->getGazeta($city_id, $limit);
		echo self::getResponse($result);
	}

	/* API method adv.add
	 * Публикация объявления
	 * https://gorod24.online/api/adv.add/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_add($params = array()){
		self::log('adv.add', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data = [
			"email"=>$decoded['email'],
			"name"=>$decoded['name'],
			"phone"=>$decoded['phone'],
			"region"=>$decoded['region'],
			"city"=>$decoded['city'],
			"main_cat"=>$decoded['main_cat'],
			"sub_cat"=>$decoded['sub_cat'],
			"options"=>$decoded['options'],
			"caption"=>$decoded['caption'],
			"text"=>$decoded['text'],
			"url"=>$decoded['url'],
			"video"=>$decoded['video'],
			"price-from-to"=>$decoded['price-from-to'],
			"price"=>$decoded['price'],
			"price-izm"=>$decoded['price-izm'],
			"price-discuse"=>$decoded['price-discuse'],
			"price-free"=>$decoded['price-free'],
			"photos"=>$decoded['photos'],
			"gazeta-text"=>$decoded['gazeta-text'],
			"gazeta-nums"=>$decoded['gazeta-nums'],
			"latitude"=>$decoded['latitude'],
			"longitude"=>$decoded['longitude'],
		];

		if(
			empty($city_id)
			or empty($user_id)
			or empty($data['email'])
			or empty($data['name'])
			or empty($data['phone'])
			or empty($data['region'])
			or empty($data['city'])
			or empty($data['main_cat'])
			or empty($data['sub_cat'])
			or empty($data['caption'])
			or empty($data['text'])
			or (empty($data['gazeta-text']) and !empty($data['gazeta-nums']))
		) die('{"error":1, "message":"The request failed"}');

		$result = $this->_model_adv->addAdv($user_id, $data);

		echo self::getResponse($result);
	}

	/* API method adv.update
	 * Изменение объявления
	 * https://gorod24.online/api/adv.update/<!city_id!>/<!user_id!>/<!adv_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_update($params = array()){
		self::log('adv.update', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$id = (int)addslashes(urldecode($params[2]));

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data = [
			"email"=>$decoded['email'],
			"name"=>$decoded['name'],
			"phone"=>$decoded['phone'],
			"region"=>$decoded['region'],
			"city"=>$decoded['city'],
			"main_cat"=>$decoded['main_cat'],
			"sub_cat"=>$decoded['sub_cat'],
			"options"=>$decoded['options'],
			"caption"=>$decoded['caption'],
			"text"=>$decoded['text'],
			"url"=>$decoded['url'],
			"video"=>$decoded['video'],
			"price-from-to"=>$decoded['price-from-to'],
			"price"=>$decoded['price'],
			"price-izm"=>$decoded['price-izm'],
			"price-discuse"=>$decoded['price-discuse'],
			"price-free"=>$decoded['price-free'],
			"photos"=>$decoded['photos'],
			"gazeta-text"=>$decoded['gazeta-text'],
			"gazeta-nums"=>$decoded['gazeta-nums'],
			"latitude"=>$decoded['latitude'],
			"longitude"=>$decoded['longitude'],
		];

		if(
			empty($city_id)
			or empty($user_id)
			or empty($id)
			or empty($data['email'])
			or empty($data['name'])
			or empty($data['phone'])
			or empty($data['region'])
			or empty($data['city'])
			or empty($data['main_cat'])
			or empty($data['sub_cat'])
			or empty($data['caption'])
			or empty($data['text'])
			or (empty($data['gazeta-text']) and !empty($data['gazeta-nums']))
		) die('{"error":1, "message":"The request failed"}');

		$result = $this->_model_adv->updateAdv($user_id, $data, $id);

		echo self::getResponse($result);
	}

	/* API method adv.getPaymentPackages
	 * Возвращает список пакетов оплаты объявлений
	 * https://gorod24.online/api/adv.getPaymentPackages/<!city_id!>/<!user_id!>/<!adv_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getPaymentPackages($params = array()){
		self::log('adv.getPaymentPackages', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$adv_id = (int)addslashes(urldecode($params[2]));

		$adv = $this->_model_adv->findItem($adv_id);
		$result = $this->_model_payment->getPaymentPackages('adv', $adv['sub_catid']);
		echo self::getResponse($result);
	}

	/* API method adv.getPaymentMethods
	 * Возвращает список методов оплаты объявлений
	 * https://gorod24.online/api/adv.getPaymentMethods/<!city_id!>/<!user_id!>/<!package_id!>/<!adv_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getPaymentMethods($params = array()){
		self::log('adv.getPaymentMethods', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$package_id = (int)addslashes(urldecode($params[2]));
		$adv_id = (int)addslashes(urldecode($params[3]));

		$adv = $this->_model_adv->findItem($adv_id);
		$package = $this->_model_payment->packages()->getItem($package_id);
		$price = $this->_model_payment->getPricePackage($package['id'], $adv['sub_catid']);
		$sum = $price - ($price/100)*$package['discount'];
		$acc_discount = $this->_model_balance->getAccDiscount($user_id);
		if(!empty($acc_discount)){ $sum = $price - ($price/100)*$acc_discount; }

		$result = $this->_model_payment->getPaymentMethods($sum, $acc_discount);
		echo self::getResponse($result);
	}

	/* API method adv.pay
	 * Генерирует процесс оплаты. Создает счет. При выборе способа оплаты "PersonalAccount" - проводит оплату и начисляет бонусы при наличии средств на счете.
	 * https://gorod24.online/api/adv.pay/<!city_id!>/<!user_id!>/<!adv_id!>/<!package_id!>/<!method_name!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_pay($params = array()){
		self::log('adv.pay', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$adv_id = (int)addslashes(urldecode($params[2]));
		$package_id = (int)addslashes(urldecode($params[3]));
		$method = addslashes(urldecode($params[4]));
		$access_token=$this->request['access_token'];
		$exdata = array();
		if(
			empty($city_id)
			or empty($user_id)
			or empty($adv_id)
			or empty($package_id)
			or empty($access_token)
		) die('{"error":1, "message":"The request failed"}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$result = [];
		$package = $this->_model_payment->packages()->getItem($package_id);
		$adv = $this->_model_adv->findItem($adv_id);

		$price = $this->_model_payment->getPricePackage($package['id'], $adv['sub_catid']);
		$sum = $price - ($price/100)*$package['discount'];

		if($method=='PersonalAccount'){
			$acc_discount = $this->_model_balance->getAccDiscount($user_id);
			if(!empty($acc_discount)){ $sum = $price - ($price/100)*$acc_discount; }
		}

		$invoice = $this->_model_payment->registerInvoice($user_id, $package['name'], $sum, $package['id'], 0, $adv_id, $package['name'].' '.$package['period']." для объявления №".$adv_id);
		$transaction = $this->_model_payment->transaction_begin($user_id,$invoice['id'], $invoice['price'], $invoice['descr'].' '.$package['period'], $exdata, 'Payment_Render_Service_'.$package['id'], 'https://xn--e1asq.xn--p1ai/%D0%BE%D0%B1%D1%8A%D1%8F%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F/item_'.$adv_id, 'https://xn--e1asq.xn--p1ai/%D0%BE%D0%B1%D1%8A%D1%8F%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F/item_'.$adv_id, $method);

		if($method!='PersonalAccount'){
			$answer_data = $this->_model_payment->transaction_start($transaction['id'], 'Robokassa', $this->request);
			if($method){
				$answer_data['fields']['IncCurrLabel']=$method;
			}
			if($answer_data['target'] and $answer_data['fields']){
				$link = $answer_data['target'].'?'; $l = [];
				foreach($answer_data['fields'] as $key=>$val){ $l[]="{$key}=".urlencode($val); }
				$answer_data['fields']['link']=$link.implode('&', $l);
			}
		}
		else {
			$balance = $this->_model_balance->getForUser($user_id);
			if($balance>=$sum){
				$auto_up = $this->_model_adv->autoup()->getItemWhere("`adv_id`='{$invoice['service_item_id']}'");
				if(!empty($auto_up) and $auto_up['need_count']==$auto_up['upok_count']){
					$this->_model_adv->autoup()->deleteRule($invoice['service_item_id']);
				}
				if($invoice['package_id']!=0){
					$pay_descr = $this->_model_payment->packageProcessPay($invoice['uid'],$invoice['package_id'],$invoice['service_item_id']);
				}
				else {
					$pay_descr = $this->_model_payment->processPay($invoice['uid'],$invoice['service_id'],$invoice['service_item_id']);
				}
				if($transaction){
					$transaction_data = array(
								'id'            => $transaction['id'],
								'status'        => '2',
								'pay_time'      => time()
							);
					$this->_model_payment->transactions()->Update($transaction_data, $transaction['id']);
				}

				$this->_model_balance->registerOut($invoice['uid'],$invoice['price'],$pay_descr,$invoice['package_id'],$invoice['service_id'],$invoice['service_item_id']);
				$answer_data = [];
				$answer_data['target'] = 'https://xn--e1asq.xn--p1ai/%D0%BE%D0%B1%D1%8A%D1%8F%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F/item_'.$adv_id.'?transaction='.$transaction['id'].'#top';
				$answer_data['fields'] = [];
			}
			else die('{"error":1, "message":"Insufficient funds on account", "message_ru":"Недостаточно средств на счете"}');

		}

		//$result['package'] = $package;
		$result['adv'] = $adv;
		$result['invoice'] = $invoice;
		$result['transaction'] = $transaction;
		$result['answer_data'] = $answer_data;

		echo self::getResponse($result);
	}

	/* API method adv.getLimits
	 *
	 * https://gorod24.online/api/adv.getLimits/<!city_id!>/<!user_id!>/<!adv_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getLimits($params = array()){
		self::log('adv.getLimits', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$adv_id = (int)addslashes(urldecode($params[2]));

		$result = $this->_model_adv->getLimits($adv_id, $user_id);
		echo self::getResponse($result);
	}

	/* API method adv.off
	 * Выключает объявление
	 * https://gorod24.online/api/adv.off/<!city_id!>/<!user_id!>/<!adv_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_off($params = array()){
		self::log('adv.off', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$adv_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];

		if(
			empty($city_id)
			or empty($user_id)
			or empty($adv_id)
			or empty($access_token)
		) die('{"error":1, "message":"The request failed"}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$result = $this->_model_adv->AdvToOff($adv_id);
		echo self::getResponse($result);
	}

	/* API method adv.on
	 * Включает объявление
	 * https://gorod24.online/api/adv.on/<!city_id!>/<!user_id!>/<!adv_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_on($params = array()){
		self::log('adv.on', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$adv_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];

		if(
			empty($city_id)
			or empty($user_id)
			or empty($adv_id)
			or empty($access_token)
		) die('{"error":1, "message":"The request failed"}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$result = $this->_model_adv->AdvToOn($adv_id);
		echo self::getResponse($result);
	}

	/* API method adv.delete
	 * Удаляет объявление
	 * https://gorod24.online/api/adv.delete/<!city_id!>/<!user_id!>/<!adv_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_delete($params = array()){
		self::log('adv.delete', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$adv_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];

		if(
			empty($city_id)
			or empty($user_id)
			or empty($adv_id)
			or empty($access_token)
		) die('{"error":1, "message":"The request failed"}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$result = $this->_model_adv->AdvDelete($adv_id);
		echo self::getResponse($result);
	}

	/* API method adv.up
	 * Делает попытку поднятия объявление
	 * https://gorod24.online/api/adv.up/<!city_id!>/<!user_id!>/<!adv_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_up($params = array()){
		self::log('adv.up', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$adv_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];

		if(
			empty($city_id)
			or empty($user_id)
			or empty($adv_id)
			or empty($access_token)
		) die('{"error":1, "message":"The request failed"}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$result = $this->_model_adv->AdvToUp($adv_id, 60*60*24*7);
		echo self::getResponse($result);
	}

	/* API method adv.getMyList
	 * Возвращает список всех объявлений пользователя.
	 * https://gorod24.online/api/adv.getMyList/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getMyList($params = array()){
		self::log('adv.getMyList', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];

		if(
			empty($city_id)
			or empty($user_id)
			or empty($access_token)
		) die('{"error":1, "message":"The request failed"}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];

		$result = $this->_model_adv->getFor($user_id, $start, $limit);
		echo self::getResponse($result);
	}

	/* API method adv.claimTypes
	 * Возвращает список причин для жалобы
	 * https://gorod24.online/api/adv.claimTypes/<!city_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_claimTypes($params = array()){
		self::log('adv.claimTypes', $params);
		$city_id = (int)addslashes(urldecode($params[0]));

		if(
			empty($city_id)
		) die('{"error":1, "message":"The request failed"}');

		$result = $this->_model_adv->claimRules();
		echo self::getResponse($result);
	}

	/* API method adv.claim
	 * Запись жалобы на объявление.
	 * https://gorod24.online/api/adv.claim/<!city_id!>/<!user_id!>/<!adv_id!>/<!claim_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_claim($params = array()){
		self::log('adv.claim', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$adv_id = (int)addslashes(urldecode($params[2]));
		$claim = (int)addslashes(urldecode($params[3]));
		$access_token=$this->request['access_token'];

		if(
			empty($city_id)
			or empty($user_id)
			or empty($adv_id)
			or empty($claim)
			or empty($access_token)
		) die('{"error":1, "message":"The request failed"}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$result = $this->_model_adv->claimAdv($user_id, $adv_id, $claim);
		echo self::getResponse($result);
	}

	/* API method app.getPaymentPackages
	 * Возвращает список пакетов оплаты премиум приложения
	 * https://gorod24.online/api/app.getPaymentPackages/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_app_getPaymentPackages($params = array()){
		self::log('app.getPaymentPackages', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));

		$result = $this->_model_payment->getPaymentPackages('app');
		echo self::getResponse($result);
	}

	/* API method app.getPaymentMethods
	 * Возвращает список методов оплаты премиум приложения
	 * https://gorod24.online/api/app.getPaymentMethods/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_app_getPaymentMethods($params = array()){
		self::log('app.getPaymentMethods', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$package_id = (int)addslashes(urldecode($params[2]));

		/*
		$package = $this->_model_payment->packages()->getItem($package_id);
		$price = $this->_model_payment->getPricePackage($package['id']);
		$sum = $price - ($price/100)*$package['discount'];
		$acc_discount = $this->_model_balance->getAccDiscount($user_id);
		if(!empty($acc_discount)){ $sum = $price - ($price/100)*$acc_discount; }
		*/
		$result = $this->_model_payment->getPaymentMethods();
		echo self::getResponse($result);
	}


	/* API method app.pay
	 * Генерирует процесс оплаты. Создает счет. При выборе способа оплаты "PersonalAccount" - проводит оплату и начисляет бонусы при наличии средств на счете.
	 * https://gorod24.online/api/app.pay/<!city_id!>/<!user_id!>/<!package_id!>/<!method_name!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_app_pay($params = array()){
		self::log('app.pay', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$package_id = (int)addslashes(urldecode($params[2]));
		$method = addslashes(urldecode($params[3]));
		$access_token=$this->request['access_token'];
		$exdata = array();
		if(
			empty($city_id)
			or empty($user_id)
			or empty($package_id)
			or empty($access_token)
		) die('{"error":1, "message":"The request failed"}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$result = [];
		$package = $this->_model_payment->packages()->getItem($package_id);
		$price = $this->_model_payment->getPricePackage($package['id']);
		$sum = $price - ($price/100)*$package['discount'];

		/*
		if($method=='PersonalAccount'){
			$acc_discount = $this->_model_balance->getAccDiscount($user_id);
			if(!empty($acc_discount)){ $sum = $price - ($price/100)*$acc_discount; }
		}
		*/

		$invoice = $this->_model_payment->registerInvoice($user_id, $package['name'], $sum, $package['id'], 0, $user_id, $package['name'].' '.$package['period']." для аккаунта №".$user_id);
		$transaction = $this->_model_payment->transaction_begin($user_id,$invoice['id'], $invoice['price'], $invoice['descr'].' '.$package['period'], $exdata, 'Payment_Render_Service_'.$package['id'], 'https://xn--e1asq.xn--p1ai/myroot/', 'https://xn--e1asq.xn--p1ai/myroot/', $method);

		if($method!='PersonalAccount'){
			$answer_data = $this->_model_payment->transaction_start($transaction['id'], 'Robokassa', $this->request);
			if($method){
				$answer_data['fields']['IncCurrLabel']=$method;
			}
			if($answer_data['target'] and $answer_data['fields']){
				$link = $answer_data['target'].'?'; $l = [];
				foreach($answer_data['fields'] as $key=>$val){ $l[]="{$key}=".urlencode($val); }
				$answer_data['fields']['link']=$link.implode('&', $l);
			}
		}
		else {
			$balance = $this->_model_balance->getForUser($user_id);
			if($balance>=$sum){
				if($invoice['package_id']!=0){
					$pay_descr = $this->_model_payment->packageProcessPay($invoice['uid'],$invoice['package_id'],$invoice['service_item_id']);
				}
				else {
					$pay_descr = $this->_model_payment->processPay($invoice['uid'],$invoice['service_id'],$invoice['service_item_id']);
				}
				if($transaction){
					$transaction_data = array(
								'id'            => $transaction['id'],
								'status'        => '2',
								'pay_time'      => time()
							);
					$this->_model_payment->transactions()->Update($transaction_data, $transaction['id']);
				}

				$this->_model_balance->registerOut($invoice['uid'], $invoice['price'], $pay_descr, $invoice['package_id'], $invoice['service_id'], $invoice['service_item_id']);
				$answer_data = [];
				$answer_data['target'] = 'https://xn--e1asq.xn--p1ai/%D0%BE%D0%B1%D1%8A%D1%8F%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F/myroot/';
				$answer_data['fields'] = [];
			}
			else die('{"error":1, "message":"Insufficient funds on account", "message_ru":"Недостаточно средств на счете"}');

		}

		//$result['package'] = $package;
		$result['adv'] = $adv;
		$result['invoice'] = $invoice;
		$result['transaction'] = $transaction;
		$result['answer_data'] = $answer_data;

		echo self::getResponse($result);
	}





	function action_test_invite($params = array()){
		$user = $this->_accounts->get_user(14600, 'c73ba7acd3f6e74e09cedd6cd082c472');
		do_action('on_register', $user, '6f949l');

	}

	/* API method test.upload
	 * Тест загрузки файлов
	 * https://gorod24.online/api/test.upload/<!file_name!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_test_upload($params = array()){
		self::log('test.upload', $params);
		$file_name = addslashes(urldecode($params[0]));

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'image/jpeg') != 0){
			die('{"error":1, "message":"Content type must be: image/jpeg"}');
		}

		//Receive the RAW post data.
		$content = trim(file_get_contents("php://input"));
		file_put_contents(APPDIR . '/uploads/image/'.$file_name.'.jpeg',  $content);
	}

	function action_index($params = array()){
		/*
		$methods = get_class_methods('controller_api');
		echo "<pre>";
		var_dump($methods);
		*/
		die('{"error":1, "message":"The request failed."}');
	}
}
