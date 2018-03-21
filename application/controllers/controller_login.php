<?php
class controller_login extends Controller
{
	protected $request, $key;
	
	function __construct()
	{
		$this->request = $_POST;
		$this->key = 'uuio6shg';
		foreach($this->request as $key=>$val){ $this->request[$key]=trim(addslashes($val));}
	}

	function vk_login($get)
	{
		$appid = $GLOBALS['CONFIG']['SOCIAL']['vk_appid'];
		$appkey = $GLOBALS['CONFIG']['SOCIAL']['vk_appkey'];
		$redirect_uri = $GLOBALS['CONFIG']['HTTP_HOST'].$GLOBALS['CONFIG']['SOCIAL']['vk_redirect_uri'];

		if (!empty($get['code']))
		{
			$token = file_get_contents('https://oauth.vk.com/access_token?client_id='.$appid.'&client_secret='.$appkey.'&redirect_uri='.$redirect_uri.'&code='.$get['code']);
			$token_out = json_decode($token, true);

			$vk = file_get_contents('https://api.vk.com/method/users.get?user_id='.$token_out['user_id'].'&access_token='.$token_out['access_token'].'&fields=uid,photo_50,sex,bdate,city&v=5.52');
			$vk_out = json_decode($vk, true);

			function calculate_age($birthday) {
				$birthday_timestamp = strtotime($birthday);
				$age = date('Y') - date('Y', $birthday_timestamp);
				if (date('md', $birthday_timestamp) > date('md')) {
					$age--;
				}
				return $age;
			}

			$id = $vk_out['response'][0]['id']; //ID пользователя
			$email = $token_out['email']; //Email
			$f_name = $vk_out['response'][0]['first_name']; //Имя
			$l_name = $vk_out['response'][0]['last_name']; //Фамилия
			$ava = $vk_out['response'][0]['photo_50']; //Аватарка 50х50
			$sex = $vk_out['response'][0]['sex']; //Пол (1 - женский / 2 - мужской)
			$date = new DateTime($vk_out['response'][0]['bdate']);
			$born_date = $date->format('Y-m-d');
			$city_id = $vk_out['response'][0]['city']['id']; //ID города
			$city_title = $vk_out['response'][0]['city']['title']; //Название города
			$age = calculate_age($born_date); //Возраст

			/*echo "ID пользователя: " . $id . "<br>";
			echo "Возраст пользователя: " . $age . "<br>";
			echo "Email пользователя: " . $email . "<br>";
			echo "Имя пользователя: " . $f_name . "<br>";
			echo "Фамилия пользователя: " . $l_name . "<br>";
			echo "Аватарка пользователя (50х50): " . $ava . "<br>";
			echo "Пол пользователя: " . $sex . "<br>";
			echo "Дата рождения пользователя: " . $born_date . "<br>";
			echo "ID города: " . $city_id . "<br>";
			echo "Название города: " . $city_title . "<br>";*/

			$url = 'https://gorod24.online/api/user.getVk/'.$id.'?publickey='.$this->key;

			$data = array(
				'id' => $id,
				'age' => $age,
				'email' => $email,
				'f_name' => $f_name,
				'l_name' => $l_name,
				'ava' => $ava,
				'sex' => $sex,
				'born_date' => $born_date,
				'city_id' => $city_id,
				'city_title' => $city_title
			);

			$options = array(
				'http' => array(
					'header'  => "Content-Type: application/json\r\n",
					'method'  => 'POST',
					'content' => json_encode($data)
				)
			);

			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			$resultData = json_decode($result, true);

			if (isset($resultData['error']) && $resultData['error'] == 1)
			{
				echo "<script>window.close();</script>";
			}
			else
			{
				$_SESSION['user'] = $resultData['response']['account'];
				echo "<script>window.close();</script>";
			}
		}
	}

	function fb_login($get)
	{
		$appid = $GLOBALS['CONFIG']['SOCIAL']['fb_appid'];
		$appkey = $GLOBALS['CONFIG']['SOCIAL']['fb_appkey'];
		$redirect_uri = $GLOBALS['CONFIG']['HTTP_HOST'].$GLOBALS['CONFIG']['SOCIAL']['fb_redirect_uri'];

		if (!empty($get['code']))
		{
			$token = file_get_contents('https://graph.facebook.com/v2.11/oauth/access_token?client_id='.$appid.'&redirect_uri='.$redirect_uri.'&client_secret='.$appkey.'&code='.$get['code']);
			$token_out = json_decode($token, true);
			$fb = file_get_contents('https://graph.facebook.com/v2.11/me/?client_id='.$appid.'&redirect_uri='.$redirect_uri.'&client_secret='.$appkey.'&code='.$get['code'].'&access_token='.$token_out['access_token'].'&fields=id,first_name,last_name,email,birthday,location,age_range,gender,picture');
			$fb_out = json_decode($fb, true);

			$id = $fb_out['id']; //ID пользователя
			$age = $fb_out['age_range']['min'] + 1; //Возраст
			$email = $fb_out['email']; //Email
			$f_name = $fb_out['first_name']; //Имя
			$l_name = $fb_out['last_name']; //Фамилия
			$ava = $fb_out['picture']['data']['url']; //Аватарка 50х50
			$sex = $fb_out['gender']['male'] ? "2" : "1"; //Пол (1 - женский / 2 - мужской)
			//$date = new DateTime($fb_out['birthday']);
			//$born_date = $date->format('Y-m-d');
			//$city_id = $fb_out['city']['id']; //ID города
			//$city_title = $fb_out['location']['city']; //Название города

			/*echo "ID пользователя: " . $id . "<br>";
			echo "Возраст пользователя: " . $age . "<br>";
			echo "Email пользователя: " . $email . "<br>";
			echo "Имя пользователя: " . $f_name . "<br>";
			echo "Фамилия пользователя: " . $l_name . "<br>";
			echo "Аватарка пользователя (50х50): " . $ava . "<br>";
			echo $sex['male'] ? "Пол пользователя: 2 <br>" : "Пол пользователя: 1 <br>";
			//echo "Дата рождения пользователя: " . $born_date . "<br>";
			//echo "ID города: " . $city_id . "<br>";
			//echo "Название города: " . $city_title . "<br>";*/

			$url = 'https://gorod24.online/api/user.getFb/'.$id.'?publickey='.$this->key;

			$data = array(
				'id' => $id,
				'age' => $age,
				'email' => $email,
				'f_name' => $f_name,
				'l_name' => $l_name,
				'ava' => $ava,
				'sex' => $sex,
				'born_date' => $born_date,
				'city_id' => $city_id,
				'city_title' => $city_title
			);
			$options = array(
				'http' => array(
					'header'  => "Content-Type: application/json\r\n",
					'method'  => 'POST',
					'content' => json_encode($data)
				)
			);

			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			$resultData = json_decode($result, true);
			if (isset($resultData['error']) && $resultData['error'] == 1)
			{
				echo "<script>window.close();</script>";
			}
			else
			{
				$_SESSION['user'] = $resultData['response']['account'];
				echo "<script>window.close();</script>";
			}
		}
	}

	function od_login($get)
	{
		$appid = $GLOBALS['CONFIG']['SOCIAL']['od_appid'];
		$appkey = $GLOBALS['CONFIG']['SOCIAL']['od_appkey'];
		$appsecret = $GLOBALS['CONFIG']['SOCIAL']['od_appsecret'];
		$redirect_uri = $GLOBALS['CONFIG']['HTTP_HOST'].$GLOBALS['CONFIG']['SOCIAL']['od_redirect_uri'];

		if (!empty($get['code']))
		{
			$ch = curl_init();
			$curlConfig = array(
				CURLOPT_URL => 'https://api.ok.ru/oauth/token.do?client_id='.$appid.'&client_secret='.$appsecret.'&redirect_uri='.$redirect_uri.'&grant_type=authorization_code&code='.$get['code'],
				CURLOPT_POST => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_POSTFIELDS => []
			);

			curl_setopt_array($ch, $curlConfig);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Accept: text/xml, application/xml, application/xhtml+xml, text/html;q=0.9, text/plain;q=0.8, text/css, image/png, image/jpeg, image/gif;q=0.8, application/x-shockwave-flash, video/mp4;q=0.9, flv-application/octet-stream;q=0.8, video/x-flv;q=0.7, audio/mp4, application/futuresplash, */*;q=0.5,
			Content-Type: application/json"
			));
			$r = curl_exec($ch);
			$token = json_decode($r, true);

			$sig = MD5("application_key=".$appkey."fields=UID,AGE,BIRTHDAY,EMAIL,FIRST_NAME,GENDER,LAST_NAME,LOCATION,PIC50X50format=jsonmethod=users.getCurrentUser" . MD5("{$token['access_token']}{$appsecret}"));

			$od = file_get_contents('https://api.ok.ru/fb.do?application_key='.$appkey.'&fields=UID,AGE,BIRTHDAY,EMAIL,FIRST_NAME,GENDER,LAST_NAME,LOCATION,PIC50X50&format=json&method=users.getCurrentUser&sig='.$sig.'&access_token='.$token['access_token']);
			$od_out = json_decode($od, true);

			$id = $od_out['uid']; //ID пользователя
			$age = $od_out['age']; //Возраст
			//$email = $od_out['email']; //Email
			$f_name = $od_out['first_name']; //Имя
			$l_name = $od_out['last_name']; //Фамилия
			$ava = $od_out['pic50x50']; //Аватарка 50х50
			$sex = $od_out['gender']['male'] ? "2" : "1"; //Пол (1 - женский / 2 - мужской)
			$date = new DateTime($od_out['birthday']);
			$born_date = $date->format('Y-m-d');
			//$city_id = $od_out['location']['city']; //ID города
			$city_title = $od_out['location']['city']; //Название города

			/*echo "ID пользователя: " . $id . "<br>";
			echo "Возраст пользователя: " . $age . "<br>";
			//echo "Email пользователя: " . $email . "<br>";
			echo "Имя пользователя: " . $f_name . "<br>";
			echo "Фамилия пользователя: " . $l_name . "<br>";
			echo "Аватарка пользователя (50х50): " . $ava . "<br>";
			echo $sex['male'] ? "Пол пользователя: 2 <br>" : "Пол пользователя: 1 <br>";
			echo "Дата рождения пользователя: " . $born_date . "<br>";
			//echo "ID города: " . $city_id . "<br>";
			echo "Название города: " . $city_title . "<br>";*/

			$url = 'https://gorod24.online/api/user.getOd/'.$id.'?publickey='.$this->key;

			$data = array(
				'id' => $id,
				'age' => $age,
				'email' => $email,
				'f_name' => $f_name,
				'l_name' => $l_name,
				'ava' => $ava,
				'sex' => $sex,
				'born_date' => $born_date,
				'city_id' => $city_id,
				'city_title' => $city_title
			);

			$options = array(
				'http' => array(
					'header'  => "Content-Type: application/json\r\n",
					'method'  => 'POST',
					'content' => json_encode($data)
				)
			);

			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			$resultData = json_decode($result, true);

			if (isset($resultData['error']) && $resultData['error'] == 1)
			{
				echo "<script>window.close();</script>";
			}
			else
			{
				$_SESSION['user'] = $resultData['response']['account'];
				echo "<script>window.close();</script>";
			}
		}
	}

	function user_login()
	{
		if (isset($_COOKIE['login']) && isset($_COOKIE['password']))
		{
			$login = $_COOKIE['login'];
			$password = $_COOKIE['password'];
		}
		else
		{
			$login = $this->request['login'];
			$password = $this->request['password'];
		}

		if(!empty($login) and !empty($password)){

			$ch = curl_init();
			$curlConfig = array(
				CURLOPT_URL => 'https://gorod24.online/api/user.loginByPas/'.$login.'/'.$password.'/feo.fm?publickey='.$this->key,
				CURLOPT_POST => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_POSTFIELDS => []
			);

			curl_setopt_array($ch, $curlConfig);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Accept: text/xml, application/xml, application/xhtml+xml, text/html;q=0.9, text/plain;q=0.8, text/css, image/png, image/jpeg, image/gif;q=0.8, application/x-shockwave-flash, video/mp4;q=0.9, flv-application/octet-stream;q=0.8, video/x-flv;q=0.7, audio/mp4, application/futuresplash, */*;q=0.5,
			Content-Type: application/json"
			));
			$r = curl_exec($ch);

			$result = json_decode($r, true);

			if (isset($result['error']) && $result['error'] == 1)
			{
				setcookie("login", $login,time()-1 ,'/');
				setcookie("password", $password,time()-1 ,'/');
				echo "false";
			}
			else
			{
				setcookie("login", $login,time()+31556926 ,'/');
				setcookie("password", $password,time()+31556926 ,'/');
				$_SESSION['user'] = $result['response'];
				echo "true";
			}
		}
	}

	//Проверяем залогинен пользователь в соц. сети или нет
	public function action_soc($array = array())
	{
		if (isset($_POST))
		{
			if (isset($_SESSION['user'])){
				echo "loginTrue";
			}
			else
			{
				echo "loginFalse";
			}
		}
	}

	//Авторизация через логин/пароль
	public function action_index($array = array())
	{
		if (isset($_POST))
		{
			$this->user_login();
		}
	}

	//Авторизация через вк
	public function action_vk($array = array())
	{
		if (isset($_GET))
		{
			$this->vk_login($_GET);
		}
	}

	//Авторизация через фейсбук
	public function action_fb($array = array())
	{
		if (isset($_GET))
		{
			$this->fb_login($_GET);
		}
	}

	//Авторизация через одноклассники
	public function action_od($array = array())
	{
		if (isset($_GET))
		{
			$this->od_login($_GET);
		}
	}

	//Удаляем сессию
	public function action_exit($array = array())
	{
		unset($_SESSION['user']);
		setcookie("login", $_COOKIE['login'],time()-1 ,'/');
		setcookie("password", $_COOKIE['password'],time()-1 ,'/');
		echo "<a href='".$GLOBALS['CONFIG']['HTTP_HOST']."'>Вернуться на сайт</a>";
	}

	//Проверка учавствует ли пользователь в конкурсе
	function contest_valid($contest_id)
	{

		$model = new model_contests_users();

		$user_active = $model->getCountWhere("`contest_id`='{$contest_id}' and `user_id`='{$_SESSION['user']['id']}'");

		if ($user_active[0] <= 0)
		{
			$data = ["user_id"=>$_SESSION['user']['id'], "contest_id"=>$contest_id, "name"=>$_SESSION['user']['i_name'] ." ". $_SESSION['user']['i_fam'], "date_start"=>date("Y-m-d"), "ava"=>$_SESSION['user']['ava_file']];
			$model->InsertUpdate($data);
			echo json_encode(array('result' => "true", 'ava' => $_SESSION['user']['ava_file']));
		}
		else
		{
			echo json_encode(array('result' => "false", 'ava' => $_SESSION['user']['ava_file']));
		}
	}

	public function action_contest($array = array())
	{
		if (isset($_POST))
		{
			$this->contest_valid($_POST['contest_id']);
		}
	}
}