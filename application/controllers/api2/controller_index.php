<?php
class controller_index extends Controller
{
	protected $request;
	protected $app;
	protected $_model_apiapps;
	protected $_model_news;

	function __construct(){
		header("Content-type: application/json; charset=UTF-8");
		$this->request = $_REQUEST;
		foreach($this->request as $key=>$val){ $this->request[$key]=trim(addslashes($val));}
		if(empty($this->request['publickey'])){ die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');}
		$this->_model_apiapps = new model_apiapps();
		$this->app = (object)$this->_model_apiapps->getItemWhere("`publickey`='{$this->request['publickey']}' AND `status`=1");
		if(is_null($this->app->id)){ die('{"error":1, "message":"Access denied"}'); }

		$this->_model_cities = new model_cities();
		$this->_model_news = new model_news();
		$this->_model_gorod_news = new model_gorod_news();
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
		if(!$array['error']){
		$result = [
			"response" => $array
		];
		}
		else {
			$result = $array;
		}
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
			if($this->request['src']!=$src) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		}
		else die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
	}

	/* API method deviceLog
	 * Лог устройств
	 * https://gorod24.online/api/deviceLog?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_deviceLog($params = array()){
		self::log('deviceLog', $params);
		
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data=[
			"uid" => $decoded['user_id'],
			"imei" => $decoded['imei'],
			"longitude" => $decoded['longitude'],
			"latitude" => $decoded['latitude'],
			"ip" => getIp(),
			"os" => $decoded['os'],
		];
		
		$result = $this->_model_apiapps->deviceLog($data);
		echo self::getResponse($result);
	}
	
	/* API method android.getCurrentVersion
	 * Возвращает ссылку для скачивания актуальноый версии приложения
	 * https://gorod24.online/api/android.getCurrentVersion?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_android_getCurrentVersion($params = array()){
		$result = [ 'success'=>1, "message"=> "https://gorod24.online/appdownload/" ];
		echo self::getResponse($result);
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
	
	/* API method user.loginHelp
	 * Возвращает подсказку для формы авторизации
	 * https://gorod24.online/api/user.loginHelp/<!city_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_loginHelp($params = array()){
		$city_id=addslashes($params[0]);
		switch($city_id){
			case 1483: $result=[ "success"=>1, "message"=>"Для входа введите данные с портала Фео.РФ" ]; break;
			case 487: $result=[ "success"=>1, "message"=>"Для входа введите данные с портала Фео.РФ" ]; break;
			default: $result=[ "success"=>1, "message"=>"Для входа введите данные с портала Фео.РФ" ]; break;
		}
		echo self::getResponse($result);
	}
	
	/* API method user.editLink
	 * Возвращает ссылку на личный кабинет пользователя
	 * https://gorod24.online/api/user.editLink/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_user_editLink($params = array()){
		$city_id=addslashes($params[0]);
		switch($city_id){
			case 1483: $result=[ "success"=>1, "message"=>"https://xn--e1asq.xn--p1ai/myroot" ]; break;
			case 487: $result=[ "success"=>1, "message"=>"https://xn--e1asq.xn--p1ai/myroot" ]; break;
			default: $result=[ "success"=>1, "message"=>"https://xn--e1asq.xn--p1ai/myroot" ]; break;
		}
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
		if(empty($login) or empty($password) or empty($imei)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result = $this->_accounts->login($login, $password, $imei);
		if($result['id'] and $this->request['publickey']=='ymezaMa5AP'){
			do_action('on_login', $result);
		}
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
		if(empty($login) or empty($password) or empty($imei)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result =  $this->_accounts->login_byTempPassword($login, $password, $imei);
		if($result['id'] and $this->request['publickey']=='ymezaMa5AP'){
			do_action('on_login', $result);
		}
		echo self::getResponse($result);
	}

	/* API method user.sendTempPassword
	 * Высылает временный пароль пользователю в виде SMS (срок жизни пароля 15 минут)
	 * https://gorod24.online/api/user.sendTempPassword/<!login!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_sendTempPassword($params = array()){
		$login=addslashes($params[0]);
		if(empty($login)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		if(empty($id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		if(empty($number) OR empty($id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		if(empty($user_id) or empty($phone_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		if(empty($user_id) or empty($phone_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		if(empty($user_id) or empty($phone_id) or empty($code)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		if(empty($id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		if(empty($user_id) OR empty($email_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		if(empty($id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result = $this->_accounts->get_user($id, $access_token);
		echo self::getResponse($result);
	}

	/* API method user.getPublic
	 * Возвращает публичные данные пользователя по id
	 * https://gorod24.online/api/user.getPublic/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_getPublic($params = array()){
		$id=(int)addslashes($params[0]);
		if(empty($id) and $id!=0) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result = $this->_accounts->get_user_public($id);
		echo self::getResponse($result);
	}

	/* API method user.getVk
	 * Возвращает данные пользователя по id соц сети VK
	 * https://gorod24.online/api/user.getVk/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_getVk($params = array()){
		$id=(int)addslashes($params[0]);
		if(empty($id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			//die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data=[
			"id" => $decoded['id'],
			"age" => $decoded['age'],
			"email" => $decoded['email'],
			"f_name" => $decoded['f_name'],
			"l_name" => $decoded['l_name'],
			"ava" => $decoded['ava'],
			"sex" => $decoded['sex'], //1-жен, 2-муж
			"born_date" => $decoded['born_date'],
			"city_id" => $decoded['city_id'],
			"city_title" => $decoded['city_title'],
		];

		$result = $this->_accounts->get_user_vk($id, $data);
		if($result['account']['id'] and $this->request['publickey']=='ymezaMa5AP'){
			do_action('on_login', $result['account']);
		}
		echo self::getResponse($result);
	}

	/* API method user.getFb
	 * Возвращает данные пользователя по id соц сети Facebook
	 * https://gorod24.online/api/user.getFb/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_getFb($params = array()){
		$id=(int)addslashes($params[0]);
		if(empty($id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			//die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data=[
			"id" => $decoded['id'],
			"age" => $decoded['age'],
			"email" => $decoded['email'],
			"f_name" => $decoded['f_name'],
			"l_name" => $decoded['l_name'],
			"ava" => $decoded['ava'],
			"sex" => $decoded['sex'], //1-жен, 2-муж
			"born_date" => $decoded['born_date'],
			"city_id" => $decoded['city_id'],
			"city_title" => $decoded['city_title'],
		];

		$result = $this->_accounts->get_user_fb($id, $data);
		if($result['account']['id'] and $this->request['publickey']=='ymezaMa5AP'){
			do_action('on_login', $result['account']);
		}
		echo self::getResponse($result);
	}

	/* API method user.getOd
	 * Возвращает данные пользователя по id соц сети Odnoklassniki
	 * https://gorod24.online/api/user.getOd/<!id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_getOd($params = array()){
		$id=(int)addslashes($params[0]);
		if(empty($id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			//die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data=[
			"id" => $decoded['id'],
			"age" => $decoded['age'],
			"email" => $decoded['email'],
			"f_name" => $decoded['f_name'],
			"l_name" => $decoded['l_name'],
			"ava" => $decoded['ava'],
			"sex" => $decoded['sex'], //1-жен, 2-муж
			"born_date" => $decoded['born_date'],
			"city_id" => $decoded['city_id'],
			"city_title" => $decoded['city_title'],
		];

		$result = $this->_accounts->get_user_od($id, $data);
		if($result['account']['id'] and $this->request['publickey']=='ymezaMa5AP'){
			do_action('on_login', $result['account']);
		}
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

	/* API method user.quickRegister
	 * Создает пользователя и отправляет ему временный пароль для быстрого входа
	 * https://gorod24.online/api/user.quickRegister/<!IMEI!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_user_quickRegister($params = array()){
		$imei=addslashes($params[0]);

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data=[
			"email" => $decoded['email'],
			"phone" => $decoded['phone'],
			"name" => $decoded['name'],
			"city" => $decoded['city'],
		];

		$result = $this->_accounts->quickRegister_user($data, $imei);
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
		if(empty($user_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
			"ava_file" => $GLOBALS['CONFIG']['HTTP_HOST'].'/uploads/image/avatars/'.$file_name.'.jpg',
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
		if(empty($id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result = $this->_accounts->user_change_password($password, $id, $access_token);
		echo self::getResponse($result);
	}

	/* API method user.setPushToken
	 * Сохраняет PUSH token устройства
	 * https://gorod24.online/api/user.setPushToken/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_user_setPushToken($params = array()){
		$id=(int)addslashes($params[0]);
		$access_token=$this->request['access_token'];
		
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);

		$data=[
			"os" => $decoded['os'],
			"token" => $decoded['token'],
		];
		
		if(
			empty($id)
			or empty($access_token)
			or empty($data['token'])
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result = $this->_accounts->device_setPushToken($id, $access_token, $data);
		echo self::getResponse($result);
	}

	/* API method user.getBalance
	 * Возвращает текущий баланс пользователя
	 * https://gorod24.online/api/user.getBalance/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_user_getBalance($params = array()){
		$user_id=(int)addslashes($params[0]);
		$access_token=$this->request['access_token'];
		
		if(
			empty($user_id)
			or empty($access_token)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		
		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');
		
		$money = $this->_model_balance->getForUser($user_id);
		$result = ['success'=>1, 'money'=>$money];
		echo self::getResponse($result);
	}

	/* API method user.getBalanceHistory
	 * Возвращает историю платежных операций пользователя
	 * https://gorod24.online/api/user.getBalanceHistory/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>&start=<!start!>&limit=<!limit!>
	 */
	function action_user_getBalanceHistory($params = array()){
		$user_id=(int)addslashes($params[0]);
		$access_token=$this->request['access_token'];
		$start=$this->request['start'];
		$limit=$this->request['limit'];
		
		if(
			empty($user_id)
			or empty($access_token)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		
		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');
		$start = ($start?$start:0); $limit=($limit?$limit:20);
		
		$money = $this->_model_balance->getHistory($user_id, null, null, $start, $limit);
		$result = $money;
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
		} else $order = "`looks` DESC";
		$result = $this->_model_news->razd()->getItemsWhere("`on_off`='1'", $order, null, null, "`id`, `name_razd` as `name`");
		array_unshift($result, ["id"=>"0","name"=>"Все"]);
		echo self::getResponse($result);
	}

	/* API method news.getByRazd
	 * Возвращает список новостей по рубрике
	 * https://gorod24.online/api/news.getByRazd/<city_id>/<!razd_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_getByRazd($params = array()){
		self::log('news.getByRazd', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		$city_id=(int)addslashes($params[0]);
		$param=addslashes(urldecode($params[1]));
		$uid=(int)addslashes(urldecode($params[2]));
		if(empty($city_id) OR is_null($param)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_gorod_news->getByRazd($city_id, $param, $uid, $start, $limit);
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
		if(empty($city_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_gorod_news->getByTown($city_id, $uid, $start, $limit);
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
		if(empty($city_id) OR empty($param)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_gorod_news->getByTag($city_id, $param, $uid, $start, $limit);
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
		if(empty($city_id) OR empty($param)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_gorod_news->getBySearch($city_id, $param, $uid, $start, $limit);
		echo self::getResponse($result);
	}
	
	/* API method news.getDayPlaylist
	 * Возвращает список аудио-озвучек за сегодняшний день
	 * https://gorod24.online/api/news.getDayPlaylist/<city_id>?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_getDayPlaylist($params = array()){
		self::log('news.getDayPlaylist', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		$city_id=(int)addslashes($params[0]);
		if(empty($city_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		
		$result = $this->_model_gorod_news->getDayPlaylist($city_id);
		echo self::getResponse($result);
	}
	
	/* API method news.getRubricPlaylist
	 * Возвращает список аудио-озвучек из рубрики
	 * https://gorod24.online/api/news.getRubricPlaylist/<city_id>/<!rubric_id!>?publickey=<!YOUR_PUBLIC_KEY!>&start=<!start!>&limit=<!limit!>
	 */
	function action_news_getRubricPlaylist($params = array()){
		self::log('news.getRubricPlaylist', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		$city_id=(int)addslashes($params[0]);
		$rubric_id=(int)addslashes($params[1]);
		if(empty($city_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_gorod_news->getRubricPlaylist($city_id, $rubric_id, $start, $limit);
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
		if(empty($city_id) OR empty($user_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');
		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_gorod_news->getFavourite($city_id, $user_id, $start, $limit);
		echo self::getResponse($result);
	}

	/* API method news.get
	 * Возвращает новость подробно
	 * https://gorod24.online/api/news.get/base1/29864?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_news_get($params = array()){
		self::log('news.get', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		$base = addslashes(urldecode($params[0]));
		$id = (int)addslashes(urldecode($params[1]));
		$uid = (int)addslashes(urldecode($params[2]));
		
		if(empty($base) or empty($id)) die('{"error":1, "message":"Новость не найдена"}');
		$result=$this->_model_gorod_news->getOne($base, $id, $uid);
		echo self::getResponse($result);
	}

	/* API method news.addComment
	 * Добавляет комментарий
	 * https://gorod24.online/api/news.addComment/base1/30323/14600?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_news_addComment($params = array()){
		self::log('news.get', $params);
		if($this->app->access_news==0) die('{"error":1, "message":"Access denied"}');
		if(empty($params[0])) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$start = 0; $limit = 20;
		$base = addslashes(urldecode($params[0]));
		$id = (int)addslashes(urldecode($params[1]));
		$user_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');
		
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);
		
		$text=$decoded['text'];
		
		$result = $this->_model_gorod_news->addComment($id, $base, $user_id, $text);
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
		if(empty($base) or empty($id) or empty($uid)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result=$this->_model_gorod_news->likeNew($base, $id, $uid);
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
		if(empty($base) or empty($id) or empty($uid)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result=$this->_model_gorod_news->dislikeNew($base, $id, $uid);
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

	/* API method news.editorTitle
	 * Передает шапку редактора новостей
	 * https://gorod24.online/api/news.editorTitle/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_news_editorTitle($params = array()){
		self::log('news.editorTitle', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		
		$result = ["success"=>1, "message"=>""];
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
		) die('{"error":1, "message":"Упс! Вы не заполнили обязательные поля."}');

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
		) die('{"error":1, "message":"Упс! Вы не заполнили обязательные поля."}');

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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
		$result = $this->_model_biznes->getBiznesRubrics($city_id);
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
		if(empty($id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result = $this->_model_biznes->getRazdel($city_id, $id, $latitude, $longitude);
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
		if(empty($type) OR empty($id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result = $this->_model_biznes->getPodrobno($type, $id, $latitude, $longitude);
		echo self::getResponse($result);
	}

	/* API method biznes.search
	 * Поиск фирмы
	 * https://gorod24.online/api/biznes.search/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>&start=<!start!>&limit=<!limit!>
	 */
	function action_biznes_search($params = array()){
		self::log('biznes.search', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);
		
		$start = !empty($this->request['start'])?$this->request['start']:0;
		$limit = !empty($this->request['limit'])?$this->request['limit']:10;
		
		$data = [
			"text"=>$decoded['text'],
			"latitude"=>$decoded['latitude'],
			"longitude"=>$decoded['longitude'],
			"start"=> $start,
			"limit"=>$limit,
		];

		if(
			empty($city_id)
			or empty($data['text'])
		) die('{"error":1, "message":"Нечего искать"}');

		$result = $this->_model_biznes->search($user_id, $city_id, $data);

		echo self::getResponse($result);
	}
	
	/* API method banners.check
	 * Возвращает Верхний баннер
	 * https://gorod24.online/api/banners.check/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_banners_check($params = array()){
		self::log('banners.getTop', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];
		$result = $this->_model_apiapps->checkBanner($city_id, $user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method banners.getTop
	 * Возвращает Верхний баннер
	 * https://gorod24.online/api/banners.getTop/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>&width=<!width!>
	 */
	function action_banners_getTop($params = array()){
		self::log('banners.getTop', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];
		$width=$this->request['width'];
		$result = $this->_model_apiapps->getTopBanner($city_id, $width, $user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method banners.getList
	 * Возвращает списковый баннер
	 * https://gorod24.online/api/banners.getList/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_banners_getList($params = array()){
		self::log('banners.getList', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];
		$result = $this->_model_apiapps->getListBanner($city_id, $user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method banners.getPopup
	 * Возвращает Всплывающий баннер
	 * https://gorod24.online/api/banners.getPopup/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_banners_getPopup($params = array()){
		self::log('banners.getPopup', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];
		$result = $this->_model_apiapps->getPopupBanner($city_id, $user_id, $access_token);
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
		if(empty($city_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		if(empty($city_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$access_token=$this->request['access_token'];
		$result = $this->_model_radio->getProgramRubrics($city_id, $user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method radio.getPrograms
	 * Возвращает список программ рубрики
	 * https://gorod24.online/api/radio.getPrograms/<!city_id!>/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>&start=0&limit=20
	 */
	function action_radio_getPrograms($params = array()){
		self::log('radio.getPrograms', $params);
		if($this->app->access_radio==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[2]));
		if(empty($city_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$access_token=$this->request['access_token'];
		$start = 0; $limit = 20;
		
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}

		//Receive the RAW post data.
		$content = trim(file_get_contents("php://input"));
		//Attempt to decode the incoming RAW post data from JSON.
		$decoded = json_decode($content, true);

		$filters = [
			'rubric' => $decoded['rubric'],
			'date' => $decoded['date'],
		];
		
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];
		$result = $this->_model_radio->getPrograms($city_id, $filters, $start, $limit, $user_id, $access_token);
		echo self::getResponse($result);
	}

	/* API method radio.sendQuestion
	 * Написать ведущему или передать привет
	 * https://gorod24.online/api/radio.sendQuestion/<!city_id!>/<user_id>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_radio_sendQuestion($params = array()){
		self::log('radio.sendQuestion', $params);
		if($this->app->access_radio==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		if(empty($city_id)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
			'name' => $decoded['name'],
			'phone' => $decoded['phone'],
			'text' => $decoded['text'],
		];
		//if(empty($data['name'])) die('{"error":1, "message":"Вы не представились."}');
		//if(empty($data['phone'])) die('{"error":1, "message":"Вы не ввели номер телефона."}');
		if(empty($data['text'])) die('{"error":1, "message":"Вы не ввели текст сообщения."}');
		//if(empty($data['name']) or empty($data['phone']) or empty($data['text'])) die('{"error":1, "message":"Не хватает данных."}');
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
		if(empty($user_id)){
			die('{"response":{"id":0,"name":"","description":"","anketa":null,"image":"http:\/\/xn--90ax.xn--e1asq.xn--p1ai\/uploads\/image\/jpeg_5a26ab4ab1559.jpg"}}');
		}
		
		if(empty($city_id) or empty($user_id) or empty($access_token)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');
		$user = $this->_accounts->get_user($user_id, $access_token);
		$result = $this->_model_nb->getCurrentContest($city_id, $user);
		echo self::getResponse($result);
	}

	/* API method nb.getContest
	 * Выбирает Анкету для юзера. (Город нужен только для статистики)
	 * https://gorod24.online/api/nb.getContest/<!city_id!>/<user_id>/<!contest_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_getContest($params = array()){
		self::log('nb.getContest', $params);
		if($this->app->access_nb==0) die('{"error":1, "message":"Access denied"}');
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$contest_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];
		if(empty($user_id) or empty($contest_id)){
			die('{"response":{"id":0,"name":"","description":"","anketa":null,"image":"http:\/\/xn--90ax.xn--e1asq.xn--p1ai\/uploads\/image\/jpeg_5a26ab4ab1559.jpg"}}');
		}
		
		if(empty($city_id) or empty($user_id) or empty($access_token)) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');
		$user = $this->_accounts->get_user($user_id, $access_token);
		$result = $this->_model_nb->getAnketaContest($contest_id, $user);
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
		if(empty($user_id)) die('{"error":1, "message":"Ошибка. не выбран пользователь."}');
		if(empty($data['name'])) die('{"error":1, "message":"Вы не вписали свое имя."}');
		if(empty($data['surname'])) die('{"error":1, "message":"Вы не вписали свою фамилию."}');
		if(empty($data['second_name'])) $data['second_name']="";
		if(empty($data['sex'])) die('{"error":1, "message":"Вы не выбрали пол."}');
		if(empty($data['age'])) die('{"error":1, "message":"Вы не выбрали возрастную группу."}');
		if(empty($data['phone'])) die('{"error":1, "message":"Вы не вписали свой телефон."}');
		if(empty($data['email'])) die('{"error":1, "message":"Вы не вписали свой email."}');
		if(
			   empty($city_id)
			or empty($contest_id)
			or empty($user_id)
			or empty($data['name'])
			or empty($data['surname'])
			or empty($data['sex'])
			or empty($data['age'])
			or empty($data['phone'])
			or empty($data['email'])
		) die('{"error":1, "message":"Недостаточно данных. Заполните обязательные поля."}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		$brend=trim($this->request['brend']);
		if(
			   empty($city_id)
			or empty($contest_id)
			or empty($anketa_id)
			or empty($nomination_id)
			or empty($brend)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result = $this->_model_nb->getResultsContestDetails($contest_id, $brend_id);
		echo self::getResponse($result);
	}

	/* API method nb.getAnketaDetails
	 * Возвращает процент заполнения анкеты и верхнюю картинку в шапку
	 * https://gorod24.online/api/nb.getAnketaDetails/<!city_id!>/<!contest_id!>/<!anketa_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_nb_getAnketaDetails($params = array()){
		self::log('nb.getAnketaDetails', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$contest_id = (int)addslashes(urldecode($params[1]));
		$anketa_id = (int)addslashes(urldecode($params[2]));
		if(
			   empty($city_id)
			   or empty($contest_id)
			   or empty($anketa_id)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result = [
			"image" => "https://gorod24.online/uploads/image/png_5a79823b0c47f.png",
			"percent" => $this->_model_nb->getAnketaPercent($contest_id, $anketa_id)
			];
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

		$result = $this->_model_adv->getRubrics($city_id);
		echo self::getResponse($result);
	}

	/* API method adv.getRubricsAll
	 * Возвращает список рубрик и подрубрик с псевдо подрубрикой "Все"
	 * https://gorod24.online/api/adv.getRubrics/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getRubricsAll($params = array()){
		self::log('adv.getRubrics', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];
		if(
			empty($city_id)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

		$result = $this->_model_adv->getRubricsWithAll($city_id);
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

		$result = $this->_model_adv->getFilters($city_id, $main_catid, $sub_catid);
		echo self::getResponse($result);
	}

	/* API method adv.getList
	 * Возвращает список объявлений
	 * https://gorod24.online/api/adv.getList/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&main=<!main_id!>&sub=<!sub_id!>&access_token=<!access_token!>
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
	 * https://gorod24.online/api/adv.get/<!city_id!>/<!adv_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_get($params = array()){
		self::log('adv.get', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$adv_id = (int)addslashes(urldecode($params[1]));
		$user_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];
		if(
			empty($city_id)
			or empty($adv_id)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

		$result = $this->_model_adv->getAdv($city_id, $adv_id, $user_id);
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
				CURLOPT_POSTFIELDS => ['file'=>$content, 'name'=>$name, 'id'=>$result['id'], 'ext'=>$file_ext]
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
			//or empty($sub_id)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result = $this->_model_adv->getOptions($main_id, $sub_id);
		echo self::getResponse($result);
	}
	
	/* API method adv.getStep
	 * Возвращает шаг публикации
	 * https://gorod24.online/api/adv.getStep/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getStep($params = array()){
		self::log('adv.getStep', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$adv_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];
		
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);
		$selected = $decoded['selected'];
		$step = count($selected);
		if(
			empty($city_id)
			or is_null($step)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		
		$user = $this->_accounts->get_user($user_id, $access_token);
		if(!empty($adv_id)){
			$adv = $this->_model_adv->getAdv($city_id, $adv_id, $user_id);
			$json_options = $adv['options'];
			$adv_selected = [
				[ "id" => "0", "value" => $adv['main_catid'] ],
				[ "id" => "-1", "value" => $adv['sub_catid'] ],
			];
			$selected = array_merge($adv_selected, $selected);
			$step = $step + 2;
		}
		switch($step){
			case 0: 
				$items = $this->_model_adv->getMainRubrics(null); 
				$rstep=[];
				$rstep['id'] = '0';
				$rstep['name'] = 'Категория';
				$rstep['prefix'] = '';
				$rstep['unit'] = '';
				$rstep['is_req'] = '1';
				$rstep['type'] = '0';
				$rstep['own_value'] = '0';
				$rstep['show_optid'] = '0';
				$rstep['show_optval'] = '0';
				$rstep['items'] = $items;
				$result = [
					"step" => $rstep,
				];
				break;
			case 1: 
				if($selected[0]['id']==0){
					$main = $selected[0]['value'];
				}
				else { die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}'); }
				if($main) {
					$items = $this->_model_adv->getSubRubrics(null, $main); 
					$rstep=[];
					$rstep['id'] = '-1';
					$rstep['name'] = 'Рубрика';
					$rstep['prefix'] = '';
					$rstep['unit'] = '';
					$rstep['is_req'] = '1';
					$rstep['type'] = '0';
					$rstep['own_value'] = '0';
					$rstep['show_optid'] = '0';
					$rstep['show_optval'] = '0';
					$rstep['items'] = $items;
					$result = [
						"step" => $rstep,
					];
				} 
				else { die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}'); }
				break;
			default:
				$sel = [];
				foreach($selected as $item){
					if($item['id']==0){ $main = $item['value']; }
					elseif($item['id']==-1){ $sub = $item['value']; }
					else { $sel[] = $item;}
				}
				$optionStep = $step - 2 ;
				$rstep = $this->_model_adv->getWindowOptions($main, $sub, $sel, $user_id);
				if(!empty($rstep[$optionStep])){
					if(!empty($adv)){
						$value = null;
						foreach($json_options as $option){
							if($rstep[$optionStep]['id'] == $option['id']){
							//$adv_option_value = $this->_model_adv->adv_values()->getItemWhere("`adv_id`='{$adv_id}' AND `opt_id`='{$rstep[$optionStep]['id']}'");
								$value = (in_array($rstep[$optionStep]['type'], [0,1]))?$option['value_id']:$option['value'];
							}
						}
						$rstep[$optionStep]['value'] = $value;
					}
					$result = [
						"step" => $rstep[$optionStep],
					];
				}
				else {
					$form = $this->_model_adv->getFormOptions($main, $sub, $sel, $user_id);
					if(!empty($adv)){
						foreach($form as $i=>$item){
							$value = null;
							foreach($json_options as $option){
								if($item['id'] == $option['id']){
									$value = (in_array($item['type'], [0,1]))?$option['value_id']:$option['value'];
								}
							}
							$form[$i]['value'] = $value;
						}
						$user_name = $adv['user_name'];
						$user_phone = $adv['user_phone'];
						$user_email = $adv['user_email'];
						$user_email_show = $adv['user_email_show'];
						$region_id = $adv['region_id'];
						$city_id = $adv['city_id'];
						$price = $adv['price'];
						$caption = $adv['caption'];
						$text = $adv['descr'];
					}
					else {
						$user_name = $user['name'];
						$user_phone =  $user['phones'][0];
						$user_email = $user['emails'][0];
						$user_email_show = 1;
						$region_id = 261;
						$city_id = 1483;
						$price = '';
						$caption = '';
						$text = '';
					}
					$globals = [
						[
							"id" =>"-1000",
							"name" => "Фотографии",
							"value" => null,
							"prefix" => "",
							"unit" => "",
							"is_req" => "1",
							"type" => "11",
							"own_value" => "1",
							"show_optid" => "0",
							"show_optval" => "0",
							"items" => []
						],
						[
							"id" =>"-100",
							"name" => "Имя",
							"value" => $user_name,
							"prefix" => "",
							"unit" => "",
							"is_req" => "1",
							"type" => "2",
							"own_value" => "1",
							"show_optid" => "0",
							"show_optval" => "0",
							"items" => []
						],
						[
							"id" =>"-101",
							"name" => "Телефон",
							"value" => $user_phone,
							"prefix" => "",
							"unit" => "",
							"is_req" => "1",
							"type" => "2",
							"own_value" => "1",
							"show_optid" => "0",
							"show_optval" => "0",
							"items" => []
						],
						[
							"id" =>"-102",
							"name" => "E-mail",
							"value" => $user_email,
							"prefix" => "",
							"unit" => "",
							"is_req" => "1",
							"type" => "2",
							"own_value" => "1",
							"show_optid" => "0",
							"show_optval" => "0",
							"items" => []
						],
						[
							"id" =>"-103",
							"name" => "показывать e-mail",
							"value" => $user_email_show,
							"prefix" => "",
							"unit" => "",
							"is_req" => "1",
							"type" => "9",
							"own_value" => "1",
							"show_optid" => "0",
							"show_optval" => "0",
							"items" => []
						],
						[
							"id" =>"-104",
							"name" => "Регион",
							"value" => $region_id,
							"prefix" => "",
							"unit" => "",
							"is_req" => "1",
							"type" => "0",
							"own_value" => "0",
							"show_optid" => "0",
							"show_optval" => "0",
							"items" => [
								[ "id" => "261", "name"=> "Крым" ]
							]
						],
						[
							"id" =>"-105",
							"name" => "Город",
							"value" => $city_id,
							"prefix" => "",
							"unit" => "",
							"is_req" => "1",
							"type" => "0",
							"own_value" => "0",
							"show_optid" => "0",
							"show_optval" => "0",
							"items" => [
								[ "id" => "1483", "name"=> "Феодосия" ],
								[ "id" => "1500537", "name"=> "Береговое" ],
								[ "id" => "1500545", "name"=> "Приморский" ],
								[ "id" => "1500539", "name"=> "Коктебель" ],
							]
						],
						[
							"id" =>"-106",
							"name" => (($main==13)?"Зарплата":"Цена"),
							"value" => $price,
							"prefix" => "",
							"unit" => "",
							"is_req" => "1",
							"type" => "3",
							"own_value" => "1",
							"show_optid" => "0",
							"show_optval" => "0",
							"items" => []
						],
					];
					$globals2 =[
						[
							"id" =>"-107",
							"name" => "Заголовок",
							"value" => $caption,
							"prefix" => "",
							"unit" => "",
							"is_req" => "1",
							"type" => "2",
							"own_value" => "1",
							"show_optid" => "0",
							"show_optval" => "0",
							"items" => []
						],
						[
							"id" =>"-108",
							"name" => "Описание",
							"value" => $text,
							"prefix" => "",
							"unit" => "",
							"is_req" => "1",
							"type" => "10",
							"own_value" => "1",
							"show_optid" => "0",
							"show_optval" => "0",
							"items" => []
						],
					];
					$merge = array_merge($globals, $form, $globals2 );
					
					
					$result = [
						"form" => $merge,
					];
				}
				break;
		}
		
		echo self::getResponse($result);
	}
	
	/* API method adv.getDefaults
	 * Возвращает необходимые значения полей для формы добавления объявления
	 * https://gorod24.online/api/adv.getDefaults/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getDefaults($params = array()){
		self::log('adv.getDefaults', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);
		$selected = $decoded['selected'];
		
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		$result = $this->_model_gazeta->getGazeta($city_id, $limit);
		echo self::getResponse($result);
	}

	/* API method adv.add2
	 * Публикация объявления
	 * https://gorod24.online/api/adv.add2/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_add2($params = array()){
		self::log('adv.add2', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));

		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);
		
		$selected = $decoded['selected'];
		$data = [];
		$options = [];
		foreach($selected as $row){
			switch($row['id']){
				case "-100": {	$data['name'] = $row['value'];		break;	}
				case "-101": {	$data['phone'] = $row['value'];		break;	}
				case "-102": {	$data['email'] = $row['value'];		break;	}
				case "-103": {	$data['email_show'] = $row['value'];break;	}
				case "-104": {	$data['region'] = $row['value'];	break;	}
				case "-105": {	$data['city'] = $row['value'];		break;	}
				case "-106": {	$data['price'] = $row['value'];		break;	}
				case "-107": {	$data['caption'] = $row['value'];	break;	}
				case "-108": {	$data['text'] = $row['value'];		break;	}
				case "0": 	 {	$data['main_cat'] = $row['value'];	break;	}
				case "-1": 	 {	$data['sub_cat'] = $row['value'];	break;	}
				default: {
					$option = [];
					$_option = $this->_model_adv->getOption($row['id']);
					$option = [
						"id" => $row['id'],
						"label" => $_option['name'],
						"value" => null,
						"value_id" => null,
						"type" => $_option['data_type']
					];
					
					if($_option['data_type'] == '0' or  $_option['data_type'] == '1'){
						$value = $this->_model_adv->getOption($row['value']);
						$option['value'] = $value['name'];
						$option['value_id'] = $row['value'];
					}
					elseif($_option['data_type'] == '2' or  $_option['data_type'] == '3') {
						$option['value'] = $row['value'];
						$option['value_id'] = null;
					}
					
					$options[] = $option;
					break;
				}
			}
		}
		$data['options'] = $options;
		$data['photos'] = $decoded['photos'];
		/*
		$data = [
			"email"=>$decoded['user_email'],
			"email_show"=>$decoded['user_email_show'],
			"name"=>$decoded['user_name'],
			"phone"=>$decoded['user_phone'],
			"region"=>$decoded['region_id'],
			"city"=>$decoded['city_id'],
			"main_cat"=>$decoded['main_catid'],
			"sub_cat"=>$decoded['sub_catid'],
			"options"=>$decoded['options'],
			"caption"=>$decoded['caption'],
			"text"=>$decoded['descr'],
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
		*/
		if(empty($data['email'])) die('{"error":1, "message":"Поле Email не может быть пустым"}');
		if(empty($data['name'])) die('{"error":1, "message":"Поле Имя не может быть пустым"}');
		if(empty($data['phone'])) die('{"error":1, "message":"Поле Телефон не может быть пустым"}');
		if(empty($data['caption'])) die('{"error":1, "message":"Вы не заполнили заголовок"}');
		if(empty($data['text'])) die('{"error":1, "message":"Вы не заполнили текст объявления"}');
		if(empty($data['main_cat'])) die('{"error":1, "message":"Вы не выбрали рубрику"}');
		if(empty($data['sub_cat'])) die('{"error":1, "message":"Вы не выбрали категорию"}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

		$result = $this->_model_adv->addAdv($user_id, $data);

		echo self::getResponse($result);
	}

	/* API method adv.update2
	 * Редактирование объявления
	 * https://gorod24.online/api/adv.action_adv_update2/<!city_id!>/<!user_id!>	?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_update2($params = array()){
		self::log('adv.update2', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$id = (int)addslashes(urldecode($params[2]));
		
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);
		
		$selected = $decoded['selected'];
		$data = [];
		$options = [];
		foreach($selected as $row){
			switch($row['id']){
				case "-100": {	$data['name'] = $row['value'];		break;	}
				case "-101": {	$data['phone'] = $row['value'];		break;	}
				case "-102": {	$data['email'] = $row['value'];		break;	}
				case "-103": {	$data['email_show'] = $row['value'];break;	}
				case "-104": {	$data['region'] = $row['value'];	break;	}
				case "-105": {	$data['city'] = $row['value'];		break;	}
				case "-106": {	$data['price'] = $row['value'];		break;	}
				case "-107": {	$data['caption'] = $row['value'];	break;	}
				case "-108": {	$data['text'] = $row['value'];		break;	}
				case "0": 	 {	$data['main_cat'] = $row['value'];	break;	}
				case "-1": 	 {	$data['sub_cat'] = $row['value'];	break;	}
				default: {
					$option = [];
					$_option = $this->_model_adv->getOption($row['id']);
					$option = [
						"id" => $row['id'],
						"label" => $_option['name'],
						"value" => null,
						"value_id" => null,
						"type" => $_option['data_type']
					];
					
					if($_option['data_type'] == '0' or  $_option['data_type'] == '1'){
						if(is_numeric($row['value'])) $value = $this->_model_adv->getOption($row['value']);
						if(empty($value)){
							$option['value'] = $value['name'];
							$option['value_id'] = $row['value'];
						}
						else {
							$option['value'] = $row['value'];
							$option['value_id'] = null;
						}
						
					}
					elseif($_option['data_type'] == '2' or  $_option['data_type'] == '3') {
						$option['value'] = $row['value'];
						$option['value_id'] = null;
					}
					
					$options[] = $option;
					break;
				}
			}
		}
		$data['options'] = $options;
		$data['photos'] = $decoded['photos'];
		/*
		$data = [
			"email"=>$decoded['user_email'],
			"email_show"=>$decoded['user_email_show'],
			"name"=>$decoded['user_name'],
			"phone"=>$decoded['user_phone'],
			"region"=>$decoded['region_id'],
			"city"=>$decoded['city_id'],
			"main_cat"=>$decoded['main_catid'],
			"sub_cat"=>$decoded['sub_catid'],
			"options"=>$decoded['options'],
			"caption"=>$decoded['caption'],
			"text"=>$decoded['descr'],
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
		*/
		if(empty($data['email'])) die('{"error":1, "message":"Поле Email не может быть пустым"}');
		if(empty($data['name'])) die('{"error":1, "message":"Поле Имя не может быть пустым"}');
		if(empty($data['phone'])) die('{"error":1, "message":"Поле Телефон не может быть пустым"}');
		if(empty($data['caption'])) die('{"error":1, "message":"Вы не заполнили заголовок"}');
		if(empty($data['text'])) die('{"error":1, "message":"Вы не заполнили текст объявления"}');
		/*
		if(empty($data['main_cat'])) die('{"error":1, "message":"Вы не выбрали рубрику"}');
		if(empty($data['sub_cat'])) die('{"error":1, "message":"Вы не выбрали категорию"}');
		*/
		if(
			empty($city_id)
			or empty($user_id)
			or empty($id)
			or empty($data['email'])
			or empty($data['name'])
			or empty($data['phone'])
			or empty($data['region'])
			or empty($data['city'])
			/*or empty($data['main_cat'])
			or empty($data['sub_cat'])*/
			or empty($data['caption'])
			or empty($data['text'])
			or (empty($data['gazeta-text']) and !empty($data['gazeta-nums']))
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

		$result = $this->_model_adv->updateAdv($user_id, $data, $id);

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
			"email"=>$decoded['user_email'],
			"email_show"=>$decoded['user_email_show'],
			"name"=>$decoded['user_name'],
			"phone"=>$decoded['user_phone'],
			"region"=>$decoded['region_id'],
			"city"=>$decoded['city_id'],
			"main_cat"=>$decoded['main_catid'],
			"sub_cat"=>$decoded['sub_catid'],
			"options"=>$decoded['options'],
			"caption"=>$decoded['caption'],
			"text"=>$decoded['descr'],
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
		if(empty($data['email'])) die('{"error":1, "message":"Поле Email не может быть пустым"}');
		if(empty($data['name'])) die('{"error":1, "message":"Поле Имя не может быть пустым"}');
		if(empty($data['phone'])) die('{"error":1, "message":"Поле Телефон не может быть пустым"}');
		if(empty($data['caption'])) die('{"error":1, "message":"Вы не заполнили заголовок"}');
		if(empty($data['text'])) die('{"error":1, "message":"Вы не заполнили текст объявления"}');
		if(empty($data['main_cat'])) die('{"error":1, "message":"Вы не выбрали рубрику"}');
		if(empty($data['sub_cat'])) die('{"error":1, "message":"Вы не выбрали категорию"}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
			"email"=>$decoded['user_email'],
			"email_show"=>$decoded['user_email_show'],
			"name"=>$decoded['user_name'],
			"phone"=>$decoded['user_phone'],
			"region"=>$decoded['region_id'],
			"city"=>$decoded['city_id'],
			"main_cat"=>$decoded['main_catid'],
			"sub_cat"=>$decoded['sub_catid'],
			"options"=>$decoded['options'],
			"caption"=>$decoded['caption'],
			"text"=>$decoded['descr'],
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

		if(empty($data['email'])) die('{"error":1, "message":"Поле Email не может быть пустым"}');
		if(empty($data['name'])) die('{"error":1, "message":"Поле Имя не может быть пустым"}');
		if(empty($data['phone'])) die('{"error":1, "message":"Поле Телефон не может быть пустым"}');
		if(empty($data['caption'])) die('{"error":1, "message":"Вы не заполнили заголовок"}');
		if(empty($data['text'])) die('{"error":1, "message":"Вы не заполнили текст объявления"}');
		if(empty($data['main_cat'])) die('{"error":1, "message":"Вы не выбрали рубрику"}');
		if(empty($data['sub_cat'])) die('{"error":1, "message":"Вы не выбрали категорию"}');
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
		
		$simple = false;
		if(!empty($adv_id)){
			$adv = $this->_model_adv->findItem($adv_id);
			$limits = $this->_model_adv->getLimits($adv['id'], $user_id);
			if($limits['portal']['answer']===false OR $limits['gazeta']['answer']===false){
				$simple = true;
			}
		}
		
		$result = $this->_model_payment->getPaymentPackages('adv', $adv['sub_catid'], $simple);
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$result = [];
		$package = $this->_model_payment->packages()->getItem($package_id);
		$adv = $this->_model_adv->findItem($adv_id);
		if(!empty($adv)){
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
				$answer_data['link']=$link.implode('&', $l);
			}
			$answer_data['paid'] = 0;
		}
		else {
			$balance = $this->_model_balance->getForUser($user_id);
			if($balance>=$sum){
				$auto_up = $this->_model_adv->autoup()->getItemWhere("`adv_id`='{$invoice['service_item_id']}'");
				if(!empty($auto_up) and $auto_up['need_count']==$auto_up['upok_count']){
					$this->_model_adv->deleteRule($invoice['service_item_id']);
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
				$answer_data['fields'] = [
					"MrchLogin" =>"obyavlenia_feo_kryma",
					"OutSum" => $invoice['price'],
					"InvId" => $invoice['id'],
					"Desc"=> $invoice['descr'].' '.$package['period']
				];
				$answer_data['link']=$answer_data['target'];
				$answer_data['paid'] = 1;
			}
			else die('{"error":1, "message":""Недостаточно средств на счете"}');

		}

		//$result['package'] = $package;
		$result['adv'] = $adv;
		$result['invoice'] = $invoice;
		$result['transaction'] = $transaction;
		$result['answer_data'] = $answer_data;

		echo self::getResponse($result);
		}
		else {
			die('{"error":1, "message":"Объявление не найдено"}');
		}
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];

		$result = $this->_model_adv->getFor($user_id, $start, $limit);
		echo self::getResponse($result);
	}

	/* API method adv.getFavourites
	 * Возвращает список избранных объявлений пользователя.
	 * https://gorod24.online/api/adv.getFavourites/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_getFavourites($params = array()){
		self::log('adv.getFavourites', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];

		if(
			empty($city_id)
			or empty($user_id)
			or empty($access_token)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$start = 0; $limit = 20;
		if($this->request['start']) $start = (int)$this->request['start'];
		if($this->request['limit']) $limit = (int)$this->request['limit'];

		$result = $this->_model_adv->getFavourites($user_id, $start, $limit);
		echo self::getResponse($result);
	}
	
	/* API method adv.addToFavourite
	 * Добавляет объявление в избранные
	 * https://gorod24.online/api/adv.addToFavourite/<!city_id!>/<!user_id!>/<!adv_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_addToFavourite($params = array()){
		self::log('adv.addToFavourite', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$adv_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];
		
		if(
			empty($city_id)
			or empty($user_id)
			or empty($adv_id)
			or empty($access_token)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$result = $this->_model_adv->addToFavourite($user_id, $adv_id);
		echo self::getResponse($result);
	}
	
	/* API method adv.delFavourite
	 * Удаляет объявление из избранных
	 * https://gorod24.online/api/adv.delFavourite/<!city_id!>/<!user_id!>/<!adv_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	function action_adv_delFavourite($params = array()){
		self::log('adv.delFavourite', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$adv_id = (int)addslashes(urldecode($params[2]));
		$access_token=$this->request['access_token'];
		
		if(
			empty($city_id)
			or empty($user_id)
			or empty($adv_id)
			or empty($access_token)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

		$ch = $this->_accounts->checkToken($user_id, $access_token);
		if(!$ch)  die('{"error":1, "message":"Incorect access token"}');

		$result = $this->_model_adv->delFavourite($user_id, $adv_id);
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
	public function action_app_pay($params = array()){
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
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');

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
				$answer_data['link']=$link.implode('&', $l);
			}
			$answer_data['paid'] = 0;
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
				$answer_data['target'] = 'https://xn--e1asq.xn--p1ai/myroot/';
				$answer_data['fields'] = [
					"MrchLogin" =>"obyavlenia_feo_kryma",
					"OutSum" => $invoice['price'],
					"InvId" => $invoice['id'],
					"Desc"=> $invoice['descr'].' '.$package['period']
				];
				$answer_data['link'] = $answer_data['target'];
				$answer_data['paid'] = 1;
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
	
	/* API method app.checkCode
	 * Проверяет код и определяет, что необходимо выполнить по этому коду.
	 * https://gorod24.online/api/app.checkCode/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	public function action_app_checkCode($params = array()){
		self::log('app.checkCode', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);
		
		
		$code = $decoded['code'];
		
		// example nb/resetnomination/10/2254
		
		$code = explode('/', $code);
		if($code[0]=='nb'){
			if($code[1]=='resetnomination'){
				if(empty($user_id) or empty($city_id)){ die('{"error":1, "message":"Для данной операции необходимо быть авторизированным в приложении."}'); }
				$result = $this->_model_nb->getFirmNominations($code[2], $code[3], $user_id);
				if(!$result){
					$action = [
						"type" => "local-link",
						"description" => "Вызвать создание анкеты.",
						"action" => "city24:nb.voting/{$code[2]}/{$row['nomination_id']}/{$code[3]}",
						//"params" => [(int)$city_id, (int)$code[2], (int)$user_id],
					];
				}
				else {
					foreach($result as $row){
						$action = [
							"type" => "local-link",
							"description" => "Вызвать окно перевыбора номинации.",
							"action" => "city24:nb.voting/{$code[2]}/{$row['nomination_id']}/{$code[3]}",
							//"params" => [(int)$city_id, (int)$code[2], (int)$row['anketa_id'], (int)$row['nomination_id'], (int)$code[3]],
							//"result" => $row,
						];
					}
				}
			}
		}
		
		
		
		
		
		echo self::getResponse([ "action"=>$action]);
	}
	
	/* API method push.getList
	 * Вызов доступных пуш рассылок
	 * https://gorod24.online/api/push.getList/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	public function action_push_getList($params = array()){
		self::log('push.getList', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		
		$razds = $this->_model_news->razd()->getItemsWhere("`on_off`=1 AND `in_push`=1", "looks DESC");
		
		$news_r = null;
		if(!empty($razds)){
			$news_r = [];
			foreach($razds as $razd){
				$news_r[] = [ 'name' => '/topics/news-'.$razd['id'], 'title' => $razd['name_razd'] ];
			}
		}
		
		$result = [
			[ 'name' => '/topics/news-0', 'title' => 'Новости', 'items' => $news_r ],
			[ 'name' => '/topics/adv-0', 'title' => 'Объявления' ], 
		];
		
		if($user_id) {
			$result[] = [ 'name' => '/topics/user-'.$user_id, 'title' => 'Системные' ];
		}
		echo self::getResponse($result);
	}
	
	/* API method push.getList2
	 * Вызов доступных пуш рассылок с разбивкой по городам
	 * https://gorod24.online/api/push.getList2/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	public function action_push_getList2($params = array()){
		self::log('push.getList2', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		
		$razds = $this->_model_news->razd()->getItemsWhere("`on_off`=1 AND `in_push`=1", "looks DESC");
		
		$news_r = null;
		if(!empty($razds)){
			$news_r = [];
			foreach($razds as $razd){
				$news_r[] = [ 'key' => md5('/topics/news-'.$razd['id']), 'name' => '/topics/news-'.$city_id.'-'.$razd['id'], 'title' => $razd['name_razd'] ];
			}
		}
		
		$result = [
			[ 'key' => md5('/topics/news-0'), 'name' => '/topics/news-'.$city_id.'-0', 'title' => 'Новости', 'items' => $news_r ],
			[ 'key' => md5('/topics/adv-0'), 'name' => '/topics/adv-'.$city_id.'-0', 'title' => 'Объявления' ], 
		];
		
		if($user_id) {
			$result[] = [ 'key' => md5('/topics/user'), 'name' => '/topics/user-'.$user_id, 'title' => 'Системные' ];
		}
		echo self::getResponse($result);
	}
	
	/* API method push.setList2
	 * Сохранение пуш рассылок
	 * https://gorod24.online/api/push.setList2/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	public function action_push_setList2($params = array()){
		self::log('push.setList2', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			die('{"error":1, "message":"Content type must be: application/json"}');
		}
		$content = trim(file_get_contents("php://input"));
		$decoded = json_decode($content, true);
		$topics = $decoded['topics'];
		
		$razds = $this->_accounts->topics()->Delete("`uid`='{$user_id}' AND `access_token`='{$access_token}'");
		if(!empty($topics)){
			foreach($topics as $topic){
				$topic_id = $this->_accounts->topics()->Insert([
					'uid' => $user_id,
					'access_token' => $access_token,
					'topic' => $topic,
					'date' => date('Y-m-d'),
					'time' => date('H:i:s')
				]);
			}
		}
		$result = [ 'success'=>1, "message"=> "Сохранено" ];
		echo self::getResponse($result);
	}
	
	/* API method invite.info
	 * Возвращает Информацию для страницы Пригласить друга
	 * https://gorod24.online/api/invite.info/<!city_id!>/<!user_id!>?publickey=<!YOUR_PUBLIC_KEY!>&access_token=<!access_token!>
	 */
	public function action_invite_info($params = array()){
		self::log('invite.info', $params);
		$city_id = (int)addslashes(urldecode($params[0]));
		$user_id = (int)addslashes(urldecode($params[1]));
		$access_token=$this->request['access_token'];
		if(
			empty($city_id)
			or empty($user_id)
			or empty($access_token)
		) die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля."}');
		
		$user = $this->_accounts->getItem($user_id);
		if(empty($user['invite_code'])){
			$data = ['invite_code'=>$this->_accounts->getUniqCode()];
			$this->Update($data, $user['id']);
			$user['invite_code'] = $data['invite_code'];
		}
		$result = [
			'header' => '<h3 style="text-align:center;">Рассказывайте о приложении Город24 и зарабатывайте:</h3>',
			'code' => $user['invite_code'],
			'img' => 'https://gorod24.online/application/views/gorod24/img/soc.jpg',
			//'message' => 'Друзья, рекомендую установить приложение нашего города. В нем очень много полезной информации для Вас. Укажите этот промокод '.$user['invite_code'].' при регистрации, Вы мне очень поможете.',
			'message' => '
			<p style="font-size:15px;">1. Можно поделиться ссылкой. Каждый кто перейдет по ссылке, принесет Вам 10р. на личный счет в приложении.</p>
			<p style="font-size:15px;">2. Раздавайте свой промо код указанный ниже. Каждый человек который введет его при регистрации в приложении. Принесет Вам 500р. наличный счет в приложении.</p>
			<p style="font-size:15px;">Все полученные средства Вы сможете потратить на услуги в приложении.</p>',
			'social' => [
				[ 'icon'=> 'https://gorod24.online/application/views/gorod24/img/soc/fb.png', 'link'=>'https://m.facebook.com/groups/1325174747588070/' ],
				[ 'icon'=> 'https://gorod24.online/application/views/gorod24/img/soc/vk.png', 'link'=>'https://vk.com/gorod24_online' ],
				[ 'icon'=> 'https://gorod24.online/application/views/gorod24/img/soc/inst.png', 'link'=>'https://www.instagram.com/gorod24_online/' ],
				[ 'icon'=> 'https://gorod24.online/application/views/gorod24/img/soc/ok.png', 'link'=>'https://m.ok.ru/group/58791715209271' ],
			],
			'link' => 'https://gorod24.online/appdownload/invite/'.$user['invite_code']
		];
		
		echo self::getResponse($result);
	}
	
	
	public function action_test_push_android($params = array()){
			$topic = '/topics/news-1483-0';
			$data = array(
				'to' => $topic,
				'data' => [
					"title" => "Город24",
					"message" => "Всегда там где ты!",
					"id" => 123,
					"link" => 'city24:settings'
				]
			);
			$options = array(
				'http' => array(
					'header'  => "Authorization: key=AIzaSyBJzJojEluuaslC1IZ03v4nagl-xY3cmyk\r\nContent-Type: application/json\r\n",
					'method'  => 'POST',
					'content' => json_encode($data)
				)
			);

			$context  = stream_context_create($options);
			$push_result = file_get_contents('https://gcm-http.googleapis.com/gcm/send', false, $context);
			$result = json_decode($push_result, true);
			var_dump( $result );
	}
	
	public function action_test_push($params = array()){
		self::log('test.push', $params);
		$payload = create_payload_json("I know how to send push notifications!");
		//$user_mobile_info = ['user_device_type'=>"Android", 'user_mobile_token'=>'/topics/news'];
		//$user_mobile_info = ['user_device_type'=>"iOS", 'user_mobile_token'=>'245321c830516c8dbedf950e6faa55486f2e3884609b3416524bc096aaffc91a//'];
		//$result = send_mobile_notification_request($user_mobile_info, $payload);
		
		/*
		require_once APPDIR . '/application/core/ApnsPHP/Autoload.php';
$push = new ApnsPHP_Push(
	ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
	APPDIR.'/dev.com.mobilemedia.city24.pem'
);
// Set the Provider Certificate passphrase
// $push->setProviderCertificatePassphrase('test');
// Set the Root Certificate Autority to verify the Apple remote peer
$push->setRootCertificationAuthority(APPDIR.'/dev.com.mobilemedia.city24.pem');
// Connect to the Apple Push Notification Service
$push->connect();
// Instantiate a new Message with a single recipient
$message = new ApnsPHP_Message('245321c830516c8dbedf950e6faa55486f2e3884609b3416524bc096aaffc91a');
// Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
// over a ApnsPHP_Message object retrieved with the getErrors() message.
$message->setCustomIdentifier("Message-Badge-3");
// Set badge icon to "3"
$message->setBadge(3);
// Set a simple welcome text
$message->setText('Hello APNs-enabled device!');
// Play the default sound
$message->setSound();
// Set a custom property
$message->setCustomProperty('acme2', array('bang', 'whiz'));
// Set another custom property
$message->setCustomProperty('acme3', array('bing', 'bong'));
// Set the expiry value to 30 seconds
$message->setExpiry(30);
// Add the message to the message queue
$push->add($message);
// Send all messages in the message queue
$push->send();
// Disconnect from the Apple Push Notification Service
$push->disconnect();
// Examine the error message container
$aErrorQueue = $push->getErrors();
if (!empty($aErrorQueue)) {
	var_dump($aErrorQueue);
}
		*/
		
if (!defined('CURL_HTTP_VERSION_2_0')) {
  define('CURL_HTTP_VERSION_2_0', 3);
}
// open connection 
$http2ch = curl_init();
curl_setopt($http2ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
 
// send push
$apple_cert = APPDIR.'/dev.com.mobilemedia.city24.pem';
$message = '{"aps":{"alert":"Hi!","sound":"default"}}';
$token = '245321c830516c8dbedf950e6faa55486f2e3884609b3416524bc096aaffc91a';
$http2_server = 'https://api.development.push.apple.com'; // or 'api.push.apple.com' if production
$app_bundle_id = 'it.tabasoft.samplepush';
 
$status = sendHTTP2Push($http2ch, $http2_server, $apple_cert, $app_bundle_id, $message, $token);
echo "Response from apple -> {$status}\n";
 
// close connection
curl_close($http2ch);
		
		echo self::getResponse($result);
	}


	function action_test_invite($params = array()){
		//$user = $this->_accounts->get_user(14600, 'c73ba7acd3f6e74e09cedd6cd082c472');
		//do_action('on_register', $user, '6f949l');
		/*
		$fix = $this->_model_apiapps->_model_devicelog()->getItemsWhere("`uid` IS NOT NULL AND `city_id` is null");
		foreach($fix as $row){
			$user = $this->_accounts->getItem($row['uid']);
			$city_id = $user['city_id'];
			$this->_model_apiapps->_model_devicelog()->Update(['city_id'=>$city_id],$row['id']);
		}
		*/
		
		$this->_accounts->genInvites();
		

	}

	function action_news_parse($params = array()){
		set_time_limit(3600);
		$page = $_GET['p'];
		if(empty($_GET['p'])) $page = 1;
		
		$limit = 10;
		$start = ($limit*$page) - $limit;
		
		$news = $this->_model_news->our()->getItemsWhere("1", "news_id DESC", $start, $limit); $img_pref = 'onf';
		//$news = $this->_model_news->kafa()->getItemsWhere("1", "news_id DESC", $start, $limit); $img_pref = 'knf';
		$model_countries = new model_countries();
		$model_regions = new model_regions();
		$model_uploads = new model_uploads();
		$model_old_photos = $this->_model_gorod_news->model_photos();
		$model_gorod_photos = $this->_model_gorod_news->model_gorod_photos();
		if(!empty($news)){
			foreach($news as $i=>$new){
			$ch = $this->_model_gorod_news->getItemWhere("`news_id`='{$new['news_id']}' AND `our`='{$new['our']}'");
			if(empty($ch)){
				/**/
				$id = $this->_model_gorod_news->Insert([
					'news_id' => $new['news_id'],
					'news_head' => $new['news_head'],
					'news_lid' => $new['news_lid'],
					'news_body' => $new['news_body'],
					'news_vrez' => $new['news_vrez'],
					'news_author' => $new['news_author'],
					'news_video' => $new['news_video'],
					'news_video_you' => $new['news_video_you'],
					'news_foto' => $new['news_foto'],
					'news_foto_sm' => $new['news_foto_sm'],
					'big_open_foto' => $new['big_open_foto'],
					'news_foto_reportag' => $new['news_foto_reportag'],
					'foto_all' => $new['foto_all'],
					'news_podp' => $new['news_podp'],
					'news_num' => $new['news_num'],
					'news_razd' => $new['news_razd'],
					'razd_id' => $razd_id,
					'news_kto' => $new['news_kto'],
					'news_tag' => $new['news_tag'],
					'town' => $new['town'],
					'country_id' => 1,
					'region_id' => 1500001,
					'city_id' => $city_id,
					'news_key' => $new['news_key'],
					'news_des' => $new['news_des'],
					'look' => $new['look'],
					'news_date' => $new['news_date'],
					'news_up' => $new['news_date'],
					'our' => $new['our'],
					'lock_' => $new['lock_'],
					'looks' => $new['looks'],
					'vk_' => $new['vk_'],
					'vk_feo' => $new['vk_feo'],
					'vk_feorf' => $new['vk_feorf'],
					'vk_g' => $new['vk_g'],
					'fb' => $new['fb'],
					'ok' => $new['ok'],
					'ot_name' => $new['ot_name'],
					'ot_sylka' => $new['ot_sylka'],
					'url' => $new['url'],
					'url_ru' => $new['url_ru'],
					'kay_word' => $new['kay_word'],
					'id_pr' => $new['id_pr'],
					'app_id' => $new['narod_id'],
					'akciya_id' => $new['akciya_id'],
					'on_off' => $new['on_off'],
					'news_lock' => $new['news_lock'],
					'news_lock_for' => $new['news_lock_for'],
					'show_comment' => $new['show_comment'],
					'news_inter_id' => $new['news_inter_id'],
					'news_album_id' => $new['news_album_id'],
					'news_zamer_id' => $new['news_zamer_id'],
					'news_panorama' => $new['news_panorama'],
					'news_panorama_type' => $new['news_panorama_type'],
					'show_in_app' => $new['show_in_app'],
					'news_rating' => $new['news_rating'],
					'nead_stream' => $new['nead_stream'],
				]);
			}
			else {
				$razd_id = 0; $city_id = 0;
				if(!empty($new['news_razd'])){ $razd_id =(int)$this->_model_gorod_news->model_razd()->get('id')->where("name_razd='{$new['news_razd']}'")->limit(1)->commit('one'); }
				if(!empty($new['town'])){ $city_id =(int)$this->_model_gorod_news->model_cities()->get('city_id')->where("city_title='{$new['town']}'")->limit(1)->commit('one'); }
				$this->_model_gorod_news->Update([
					'news_body' => $new['news_body'],
					'razd_id'=>$razd_id, 
					'country_id' => 1,
					'region_id' => 1500001,
					'city_id' => $city_id,
					'news_foto' => $new['news_foto'],
					'news_foto_sm' => $new['news_foto_sm'],
					'on_off' => $new['on_off'],
					'look' => $new['look'],
					'looks' => $new['looks'],
					'vk_' => $new['vk_'],
					'vk_feo' => $new['vk_feo'],
					'vk_feorf' => $new['vk_feorf'],
					'vk_g' => $new['vk_g'],
					'fb' => $new['fb'],
					'ok' => $new['ok'],
				], $ch['id']);
				$id = $ch['id'];
			}
			
			$this->_model_gorod_news->model_news_cities()->Delete("`new_id`='{$id}'");
			$this->_model_gorod_news->model_news_cities()->Insert(['new_id'=>$id, 'country_id'=>1, 'region_id'=>1500001, 'city_id'=>$city_id, 'add_date'=>date("Y-m-d H:i:s")]);
			
			
			$audio = $this->_model_gorod_news->_model_news_audio_streams()->getItemWhere("`news_id`='{$new['news_id']}' AND `our`='{$new['our']}'");{
				if($audio and $audio['new_id']==0){
					$file = $audio['file'];
					$src = explode('/', $file);
					$name = end($src);
					if(!file_exists(APPDIR . "/uploads/audio/news/".$name)){
						$stream = file_get_contents("https://feo.ua".$file);
						if(file_put_contents(APPDIR . "/uploads/audio/news/".$name, $stream)){
							$this->_model_gorod_news->_model_news_audio_streams()->Update([
								'new_id' => $id,
								'audio' => "/uploads/audio/news/".$name
							], $audio['id']);
						}
					}
				}
			}
			/*
			if(!file_exists(APPDIR . "/uploads/image/news_thrumbs/{$img_pref}_{$new['news_id']}_361_240.jpg")){
				$photo_content = file_get_contents("https://feo.ua/upload/news_fotos_thumb/{$img_pref}_{$new['news_id']}_361_240.jpg");
				if($photo_content){
					file_put_contents(APPDIR . "/uploads/image/news_thrumbs/{$img_pref}_{$new['news_id']}_361_240.jpg", $photo_content);
				}
			}
			*/
			
			$old_photos = $model_old_photos->getItemsWhere("`n_id`='{$new['news_id']}' and `our`='{$new['our']}'");
			foreach($old_photos as $old_photo){
				if(!file_exists(APPDIR . '/uploads/image/news/'.$old_photo['foto'])){
					echo "{$old_photo['foto']}<br>";
					$photo_content = file_get_contents("http://feo.ua/news/foto/{$old_photo['foto']}");
					file_put_contents(APPDIR . '/uploads/image/news/'.$old_photo['foto'], $photo_content);
					$size = filesize(APPDIR . '/uploads/image/news/'.$old_photo['foto']);
					$upload_id = $model_uploads->Insert([
						'name' => $old_photo['foto'],
						'original_name' => $old_photo['foto'],
						'ext' => $old_photo['ext'],
						'type' => 'image',
						'size' => $size,
						'destination' => '/uploads/image/news/',
						'author' => '0',
						'date' => date('Y-m-d H:i:s'),
						'modified' => date('Y-m-d H:i:s'),
						'status' => 1,
						'other' => '',
					]);
					
					$photo_id = $model_gorod_photos->Insert([
						'new_id' => $id,
						'img' => "/uploads/image/news/{$old_photo['foto']}",
						'img_id' => $upload_id, 
						'description' => $old_photo['discription'], 
						'title' => $old_photo['title'], 
						'pos' => $old_photo['pos'], 
						'status' => $old_photo['on_off'], 
						'descr_on' => $old_photo['descr_on'], 
					]);
					
				}
			}
			
			
			
			}
			$next = $page + 1;
			header("Content-type: text/html; charset=UTF-8");
			echo "{$page}<script type='text/javascript'>document.location.href = 'https://gorod24.online/api/news.parse/1483/0/14600?publickey=ymezaMa5AP&p={$next}';</script>";
			//header('Location: https://gorod24.online/api/news.parse/1483/0/14600?publickey=ymezaMa5AP&p='.$next);
		}
		
	}

	function action_news_parse_up($params = array()){
		set_time_limit(3600);
		
		$rows = $this->_model_gorod_news->model_news_time_up()->getItemsWhere("`new_id`=0");
		foreach($rows as $item){
			$new = $this->_model_gorod_news->getItemWhere("`news_id`='{$item['id_news']}' AND `our`='{$item['our']}'");
			if($new){
				$this->_model_gorod_news->model_news_time_up()->Update(['new_id'=>$new['id']], $item['id']);
			}
		}
		
	
	}

	function action_news_parse_rotate($params = array()){
		set_time_limit(3600);
		
		$rows = $this->_model_gorod_news->model_gorod_news_rotate()->getItemsWhere("`new_id`=0");
		foreach($rows as $item){
			$new = $this->_model_gorod_news->getItemWhere("`news_id`='{$item['n_id']}' AND `our`='{$item['n_o']}'");
			if($new){
				$this->_model_gorod_news->model_gorod_news_rotate()->Update(['new_id'=>$new['id']], $item['id']);
			}
		}
	}
	
	function action_news_parse_push($params = array()){ 
		set_time_limit(3600);
		$model_push = new model_push();
		$rows = $model_push->getItemsWhere("`new_id`=0 AND `from_new`!=0");
		foreach($rows as $item){
			$new = $this->_model_gorod_news->getItemWhere("`news_id`='{$item['from_new']}' AND `our`='{$item['our']}'");
			if($new){
				$model_push->Update(['new_id'=>$new['id']], $item['id']);
			}
		}
		
	
	}

	function action_news_parse_audios($params = array()){
		set_time_limit(3600);
		
		$rows = $this->_model_gorod_news->_model_news_audio_streams()->getItemsWhere("`new_id`=0");
		foreach($rows as $item){
			$new = $this->_model_gorod_news->getItemWhere("`news_id`='{$item['news_id']}' AND `our`='{$item['our']}'");
			if($new){
				$this->_model_gorod_news->_model_news_audio_streams()->Update(['new_id'=>$new['id']], $item['id']);
			}
		}
		
	
	}

	function action_biznes_parse_towns($params = array()){
		set_time_limit(3600);
		
		$rows = $this->_model_biznes->getItemsWhere("`city_id`=0");
		foreach($rows as $item){
			$city = $this->_model_cities->getItemWhere("TRIM(`city_title`)=TRIM('{$item['town']}')");
			if($city){
				$this->_model_biznes->Update(['city_id' => $city['city_id'] ], $item['id']);
			}
		}
	}

	function action_biznes_export_phones($params = array()){
		set_time_limit(3600);
		$file = APPDIR . "/pred_phone_book_feo.vcf";
		@unlink($file);
		$rows = $this->_model_biznes->getItemsWhere("`phones`!='' AND city_id='1483'");
		foreach($rows as $item){
			$phones = multiexplode([',',';'], $item['phones']);
			foreach($phones as $phone){
				$string = preg_replace('~[^0-9+]+~','',$phone); 
				if(!empty($string) and strlen($string)>=11){
				file_put_contents($file, "BEGIN:VCARD
VERSION:3.0
FN:{$item['name_kat']}
TEL;TYPE=CELL:{$string}
END:VCARD\r\n", FILE_APPEND);
				}
			}
		}
		
		$file = APPDIR . "/pred_phone_book_kerch.vcf";
		@unlink($file);
		$rows = $this->_model_biznes->getItemsWhere("`phones`!='' AND city_id='478'");
		foreach($rows as $item){
			$phones = multiexplode([',',';'], $item['phones']);
			foreach($phones as $phone){
				$string = preg_replace('~[^0-9+]+~','',$phone); 
				if(!empty($string) and strlen($string)>=11){
				file_put_contents($file, "BEGIN:VCARD
VERSION:3.0
FN:{$item['name_kat']}
TEL;TYPE=CELL:{$string}
END:VCARD\r\n", FILE_APPEND);
				}
			}
		}
		
	}

	function action_adv_export_phones($params = array()){
		set_time_limit(3600);
		$page = $_GET['p'];
		if(empty($_GET['p'])) $page = 1;
		
		$limit = 100;
		$start = ($limit*$page) - $limit;
/*
		$file = APPDIR . "/adv_phone_book_feo.vcf";
		//if($page==1)@unlink($file);
		$cities = ["1483", "1500537", "1500545", "1500539", "0"];
		$rows = $this->_model_adv->adv_off()->get("*")->where("`user_phone`!='' AND LENGTH(user_phone)>=12 AND city_id IN (".implode(',',$cities).")")->limit($limit)->offset($start)->group("`user_phone`")->commit();
		foreach($rows as $item){
			$phone = $item['user_phone'];
			$string = preg_replace('~[^0-9+]+~','',$phone); 
			if(!empty($string) and strlen($string)>=11 AND substr($string, 0, 3)!='+38'){
				file_put_contents($file, "BEGIN:VCARD
VERSION:3.0
FN:{$item['user_name']}
TEL;TYPE=CELL:{$string}
END:VCARD\r\n", FILE_APPEND);
			}
		}
*/		
		$file = APPDIR . "/adv_phone_book_kerch.vcf";
		//if($page==1)@unlink($file);
		$rows = $this->_model_adv->adv_off()->get("*")->where("`user_phone`!='' AND LENGTH(user_phone)>=12 AND city_id='478'")->limit($limit)->offset($start)->group("`user_phone`")->commit();
		foreach($rows as $item){
			$phone = $item['user_phone'];
			$string = preg_replace('~[^0-9+]+~','',$phone); 
			if(!empty($string) and strlen($string)>=11 AND substr($string, 0, 3)!='+38'){
				file_put_contents($file, "BEGIN:VCARD
VERSION:3.0
FN:{$item['user_name']}
TEL;TYPE=CELL:{$string}
END:VCARD\r\n", FILE_APPEND);
			}
		}
		
		if(!empty($rows)){
		$next = $page + 1;
		header("Content-type: text/html; charset=UTF-8");
		echo "{$page}<script type='text/javascript'>document.location.href = 'https://gorod24.online/api/adv.export.phones/1483/0/14600?publickey=ymezaMa5AP&p={$next}';</script>";
		}
	}

	function action_adv_parse_city($params = array()){
		set_time_limit(3600);
		$page = $_GET['p'];
		if(empty($_GET['p'])) $page = 1;
		
		$limit = 100;
		$start = ($limit*$page) - $limit;
		
		$rows = $this->_model_adv->adv_off()->getItemsWhere("1", "`id` DESC", $start, $limit);
		foreach($rows as $item){
			if($item['city']){
				$city = $this->_model_cities->getItemWhere("TRIM(`city_title`) = '{$item['city']}'");
				if($city){
					$this->_model_adv->adv_off()->Update(['city_id'=>$city['city_id'], 'region_id'=>1500001], $item['id']);
				}
			}
		}
		$next = $page + 1;
		header("Content-type: text/html; charset=UTF-8");
		echo "{$page}<script type='text/javascript'>document.location.href = 'https://gorod24.online/api/adv.parse.city/1483/0/14600?publickey=ymezaMa5AP&p={$next}';</script>";
		
	
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

	/* API method test.ftp
	 * 
	 * https://gorod24.online/api/test.ftp?publickey=<!YOUR_PUBLIC_KEY!>
	 */
	function action_test_ftp($params = array()){
		self::log('test.ftp', $params);

		$ftp_server = "80.93.183.242";
		// устанавливает соединение или выходит
		$conn_id = ftp_connect($ftp_server, 2123) or die("Не удалось установить соединение с $ftp_server"); 		
		//$ftp = new Ftp;
		// Opens an FTP connection to the specified host
		//$ftp->connect('80.93.183.242', 2123);
		// Login with username and password
		//$ftp->login('websites', 'maximus_1975');
		
	}

	function action_index($params = array()){
		/*
		$methods = get_class_methods('controller_api');
		echo "<pre>";
		var_dump($methods);
		*/
		die('{"error":1, "message":"Недостаточно данных. Заполните все обязательные поля.."}');
	}
}
