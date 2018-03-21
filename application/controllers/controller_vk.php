<?php
class controller_vk extends Controller
{
	function __construct(){

	}
	
	public function action_index($routes = array()){
		$model_social = new model_social();
		$publish = $model_social->getItem(465);
		$publisher = $model_social->model_accounts()->getItem($publish['account_id_from']);
		
			if($publisher['social_id_type']>0)$pref = '-';
			$vk_work = new vk( $publisher['access_token'], 1, $publisher['app_id'], $pref.$publish['social_id_to'] );
			switch($publish['post_type']){
				case 0: 
					$oResponce = $vk_work->post( $publish['text'], "", $publish['link'] );
					break;
				case 1: 
					//file_get_contents("https://gorod24.online/thrumbs/news/new_{$publish['new_id']}_640_320.jpg");
					$publish['photo'] = str_replace("https://gorod24.online",APPDIR, $publish['photo']);
					echo "{$publish['photo']}<br>";
					echo "{$publish['album_id']}<br>";
					echo "{$text}<br>";
					echo "{$publish['group_id']}<br>";
					//exit;
					$photo = $vk_work->upload_photo($publish['photo'], $publish['album_id'],  $text, $publish['group_id']);
					var_dump($photo);
					$atach="photo".$photo->owner_id."_".$photo->id;
					$oResponce = $vk_work->post_wall( $pref.$publish['social_id_to'], '1', $publish['text'], $atach );
					break;
			}
			var_dump($atach);
			var_dump($oResponce);
			
		
		//работает
		/*$id = 18325059;
		$text .=  $messanger['description'];
		$vk_work = new vk( $messanger['access_token'], 1, $messanger['app_id'], $id );
		$id_album = "242485975";
		$id_gr_="119903571";
		
		$foto_id = $vk_work->upload_photo( APPDIR . "/application/views/gorod24/img/soc.jpg", $id_album,  $text, $id_gr_);
		if(!$foto_id->error_code){
			//var_dump($foto_id); exit;
			$atach="photo".$foto_id->owner_id."_".$foto_id->id.", ".$link;
			$date = $vk_work->post_wall( $id, '1', $text, $atach );
			echo "atach: $atach";
			print "<br><br>";
			echo 'post_wall';
			print_r($date);
			print "<br><br>";
		}
		else {
			var_dump($foto_id);
		}
		*/
		//$oResponce = $vk_work->post( $text, "", $link );
		/*
		if(is_int($oResponce)){
			$model_social->model_accounts()->Update(['sends'=>($messanger['sends']+1)], $messanger['id']);
			$data = [
				'message_id' => $oResponce,
				'account_id_from' => $messanger['id'],
				'social_id_to' => $id,
				'text' => $text,
				'link' => $link,
				'access_token' => $messanger['access_token'],
				'send_date' => date('Y-m-d H:i:s'),
				'date' => date('Y-m-d H:i:s'),
			];
			$model_social->InsertUpdate($data);
			echo $oResponce;
		}
		*/
	}
	
	function action_facebook($routes = array()){
		require_once APPDIR . '/application/core/Facebook/autoload.php';
		//echo 'facebook is start';
		$model_social = new model_social();
		$messanger = $model_social->model_accounts()->getItem(6);
		
		$app_id = '1414142202188046'; // ид приложения. берешь в настройках приложения (или копируешь с адресной строки)
		$app_secret = '97fc2636aafcd8afcc270112e0b606e6'; // ключ приложения. берешь в настройках приложения
		$access_token = 'EAAUGJ6OfkQ4BAJzrBNLSM7nwzzZCEbKNwZBmyJ9hqtP59sxweKtxOiQLZCJEcjbDrJEgQriwD7LH3FvR39d8xWPUrjZBusHsvPHJpNMdNpjb3iwQu3cwWVygeYOP8jy0yJED4ZAcMe0eUpn7pziM6aRQbpZBznrNg9UOMiaaGU0QZDZD'; // токен, который мы получили
		$page_id = '767678316666013'; // id группы

		$fb = new Facebook\Facebook(array(
			'app_id' => $app_id,
			'app_secret' => $app_secret,
			'default_graph_version' => 'v2.2',
		));
		$fb->setDefaultAccessToken($access_token);
		$link = 'https://gorod24.online';
		$text = 'Установи Город24 на свой телефон https://gorod24.online/appdownload И Узнавай новости первый! Делись своим мнением! Узнавай мнения других!';
		$data = [
				'link' => $link,
				'message' => $text,
				//'source' => $fb->fileToUpload(APPDIR. '/application/views/gorod24/img/soc.jpg'),
			];
		
		try {
			$response = $fb->post("/{$page_id}/feed", $data);
		} 
		catch(Facebook\Exceptions\FacebookResponseException $e) {		  echo 'Graph returned an error: ' . $e->getMessage(); return false;		} 
		catch(Facebook\Exceptions\FacebookSDKException $e) {		  echo 'Facebook SDK returned an error: ' . $e->getMessage(); return false;		}
		
		$graphNode = $response->getGraphNode();
		echo 'Photo ID: ' . $graphNode['id'];
		#РЕПОСТ
		
		$str_page = '/100001269554597/feed';
		$feed = array('message' => 'Установи Город24 на свой телефон https://gorod24.online/appdownload И Узнавай новости первый! Делись своим мнением! Узнавай мнения других!','link'=>'https://www.facebook.com/photo.php?fbid='.$graphNode['id']);
		
		try {
			$response = $fb->post($str_page, $feed, $access_token);
		}
		catch (Facebook\Exceptions\FacebookResponseException $e) { echo 'Graph вернул ошибку: ' . $e->getMessage(); return false; }
		catch (Facebook\Exceptions\FacebookSDKException $e) { echo 'Facebook SDK вернул ошибку: ' . $e->getMessage(); return false; }
						
		$graphNode1 = $response->getGraphNode();
		echo '<BR>REPOST, id: ' . $graphNode1['id'];
		$date=$graphNode['id'].'--'.$graphNode1['id'];
		
		//echo 'facebook is end';
	}
	
	public function action_ok($routes = array()){
		$model_social = new model_social();
		$messanger = $model_social->model_accounts()->getItem(7);
		
		$ok_access_token = $messanger['access_token']; //Наш вечный токен
		$ok_private_key = $messanger['app_secret']; //Секретный ключ приложения
		$ok_public_key = $messanger['app_public']; //Публичный ключ приложения
		
		// 1. Получим адрес для загрузки 1 фото
		$params = array(
			"application_key"   =>  $ok_public_key,
			"method"            => "photosV2.getUploadUrl",
			"count"             => 1,  // количество фото для загрузки
			"gid"               => $ok_group_id,
			"format"            =>  "json"
		);

		// Подпишем запрос
		$sig = md5( self::arInStr($params) . md5("{$ok_access_token}{$ok_private_key}") );
		$params['access_token'] = $ok_access_token;
		$params['sig']          = $sig;
		// Выполним
		$step1 = json_decode(self::getUrl("https://api.ok.ru/fb.do", "POST", $params), true);
		//print_r ($step1);
		// Если ошибка
		if (isset($step1['error_code'])) {
			// Обработка ошибки
			echo "step1 [{$step1['error_code']}]";
			var_dump($step1);
			return false;
		}
		// Предполагается, что картинка располагается в каталоге со скриптом
		$params = array(
			"pic1" => "@".$foto,
		);

		// Отправляем картинку на сервер, подписывать не нужно
		$step2 = json_decode( self::getUrl( $step1['upload_url'], "POST", $params, 30, true), true);
		print_r ($step2);
		// Если ошибка
		if (isset($step2['error_code'])) {
			// Обработка ошибки
			echo "step2 [{$step2['error_code']}]";
			var_dump($step2);
			return false;
		}
		
// Токен загруженной фотки
$token = $step2['photos'][$photo_id]['token'];
print "<br>".$token."<br>";

// Заменим переносы строк, чтоб не вываливалась ошибка аттача
$message_json = str_replace("\r\n", "\\n", $message);
// 3. Запостим в группу
$attachment = '{
                    "media": [
                        {
                            "type": "text",
                            "text": "'.$message_json.'"
                        },
                        {
                            "type": "photo",
                            "list": [
                                {
                                    "id": "'.$token.'"
                                }
                            ]
                        }
                    ],
					"onBehalfOfGroup": "true"
                }';

$params = array(
    "application_key"   =>  $ok_public_key,
    "method"            =>  "mediatopic.post",
    "gid"               =>  $ok_group_id,
    "type"              =>  "GROUP_THEME",
    "attachment"        =>  $attachment,
    "format"            =>  "json",
);

// Подпишем
$sig = md5( self::arInStr($params) . md5("{$ok_access_token}{$ok_private_key}") );

$params['access_token'] = $ok_access_token;
$params['sig']          = $sig;

$step3 = json_decode( self::getUrl("https://api.ok.ru/fb.do", "POST", $params, 30, false, false ), true);
print_r ($step3);
// Если ошибка
if (isset($step3['error_code'])) {
    // Обработка ошибки
	echo "step3 [{$step3['error_code']}]";
	var_dump($step3);
    return false;
}

//4. перепостим в группу
print 'https://ok.ru/'.$ok_group_id.'/topic/'.$step3;
$attachment = '{
                    "media": [
                        {
						  "type": "link",
						  "url": "https://ok.ru/group/'.$ok_group_id.'/topic/'.$step3.'"
						}
                        
                    ]
                }';

$params = array(
    "application_key"   =>  $ok_public_key,
    "method"            =>  "mediatopic.post",
    "gid"               =>  "54059011997805",
    "type"              =>  "GROUP_THEME",
    "attachment"        =>  $attachment,
    "format"            =>  "json",
);

// Подпишем
$sig = md5( self::arInStr($params) . md5("{$ok_access_token}{$ok_private_key}") );

$params['access_token'] = $ok_access_token;
$params['sig']          = $sig;

$step4 = json_decode( self::getUrl("https://api.ok.ru/fb.do", "POST", $params, 30, false, false ), true);
print_r ($step4);
// Если ошибка
if (isset($step4['error_code'])) {
    // Обработка ошибки
	echo "step4 [{$step4['error_code']}]";
	var_dump($step4);
    return false;
}
		
		
	}
	
	public function action_callback($routes = array()){
		echo "6f2b4c5e";
	}
	
	function getUrl($url, $type = "GET", $params = array(), $timeout = 30, $image = false, $decode = true){
		if ($ch = curl_init())
		{
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);

			if ($type == "POST")
			{
				curl_setopt($ch, CURLOPT_POST, true);

				// Картинка
				if ($image) {
					curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
				}
				// Обычный запрос       
				elseif($decode) {
					curl_setopt($ch, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
				}
				// Текст
				else {
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
				}
			}

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Bot');
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			$data = curl_exec($ch);
			curl_close($ch);
			// Еще разок, если API завис
			if (isset($data['error_code']) && $data['error_code'] == 5000) {
				$data = self::getUrl($url, $type, $params, $timeout, $image, $decode);
			}

			return $data;

		}
		else {
			return "{}";
		}
	}

	// Массив аргументов в строку
	function arInStr($array){
		ksort($array);
		$string = "";
		foreach($array as $key => $val) {
			if (is_array($val)) {
				$string .= $key."=".self::arInStr($val);
			} else {
				$string .= $key."=".$val;
			}
		}
		return $string;
	}
	
	
}