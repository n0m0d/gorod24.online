<?php
$menu = array();
$admin_page_hooks = array();
$_registered_pages = array();
$_registered_controllers = array();
$_pages_templates = array();
$_parent_pages = array();
$_cron_events = array();
$_email_workers_events = array();
$locale = "ru_RU";

if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}

function send_mobile_notification_request($user_mobile_info, $payload_info)
{
    //Default result
    $result = -1;
    //Change depending on where to send notifications - either production or development
    $pem_preference = "production";
    $pem_preference = "dev";
    $user_device_type = $user_mobile_info['user_device_type'];
    $user_device_key = $user_mobile_info['user_mobile_token'];

    if ($user_device_type == "iOS") {

        $apns_url = NULL;
        $apns_cert = NULL;
        //Apple server listening port
        $apns_port = 2195;

        if ($pem_preference == "production") {
            $apns_url = 'gateway.push.apple.com';
            $apns_cert = APPDIR.'/cert-prod.pem';
        }
        //develop .pem
        else {
            $apns_url = 'gateway.sandbox.push.apple.com';
            $apns_cert = APPDIR.'/dev.com.mobilemedia.city24.pem';
        }

        $stream_context = stream_context_create();
        stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);

        $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);
        $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $user_device_key)) . chr(0) . chr(strlen($payload_info)) . $payload_info;

        if ($apns) {
            $result = fwrite($apns, $apns_message);
        }
        @socket_close($apns);
        @fclose($apns);

    }
    else if ($user_device_type == "Android") {

        // API access key from Google API's Console
        define('API_ACCESS_KEY', 'AIzaSyBJzJojEluuaslC1IZ03v4nagl-xY3cmyk');

        // prep the bundle
        $msg = array
        (
            'message' 	=> json_decode($payload_info)->aps->alert,
            'title'		=> 'This is a title. title',
            'subtitle'	=> 'This is a subtitle. subtitle',
            'tickerText'=> 'Ticker text here...Ticker text here...Ticker text here',
            'vibrate'	=> 1,
            'sound'		=> 1,
            'largeIcon'	=> 'large_icon',
            'smallIcon'	=> 'small_icon'
        );
        $fields = array
        (
            //'registration_ids' 	=> array($user_device_key),
            'to' 	=> $user_device_key,
            'data' => $msg
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, false );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch);
        curl_close($ch);
    }
    return $result > 0;
}

//Create json file to send to Apple/Google Servers with notification request and body
function create_payload_json($message) {
    //Badge icon to show at users ios app icon after receiving notification
    $badge = "0";
    $sound = 'default';

    $payload = array();
    $payload['aps'] = array('alert' => $message, 'badge' => intval($badge), 'sound' => $sound);
    return json_encode($payload);
}

/**
 * @param $http2ch          the curl connection
 * @param $http2_server     the Apple server url
 * @param $apple_cert       the path to the certificate
 * @param $app_bundle_id    the app bundle id
 * @param $message          the payload to send (JSON)
 * @param $token            the token of the device
 * @return mixed            the status code
 */
function sendHTTP2Push($http2ch, $http2_server, $apple_cert, $app_bundle_id, $message, $token) {
 
    // url (endpoint)
    $url = "{$http2_server}/3/device/{$token}";
 
    // certificate
    $cert = realpath($apple_cert);
    // headers
    $headers = array(
        "apns-topic: {$app_bundle_id}",
        "User-Agent: My Sender"
    );
 
    // other curl options
    curl_setopt_array($http2ch, array(
        CURLOPT_URL => $url,
        CURLOPT_PORT => 443,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => TRUE,
        CURLOPT_POSTFIELDS => $message,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSLCERT => $cert,
        CURLOPT_HEADER => 1
    ));
 
    // go...
    $result = curl_exec($http2ch);
    if ($result === FALSE) {
      throw new Exception("Curl failed: " .  curl_error($http2ch));
    }
 
    // get response
    $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
 
    return $status;
}

function checkTableExist($table_name = null){
	if($table_name){
		$result = $GLOBALS['DB']['localhost']->getRow("SHOW TABLE STATUS WHERE NAME = '{$table_name}';");
		if(!empty($result)) return $result; else return false;
	}
	else return false;
}

function checkTableColumns($table_name = null){
	if($table_name){
		$result = $GLOBALS['DB']['localhost']->getAll("SHOW FULL COLUMNS FROM {$table_name};");
		if(!empty($result)) return $result; else return false;
	}
	else return false;
}

function checkTableIndexes($table_name = null){
	if($table_name){
		$result = $GLOBALS['DB']['localhost']->getAll("SHOW INDEX FROM {$table_name};");
		if(!empty($result)) return $result; else return false;
	}
	else return false;
}


function session_run(){
		//session_save_path(__DIR__ . '/cache/sessions');		
		if ($_COOKIE['session_id']) session_id($_COOKIE['session_id']);
		session_start();
		$id_session = session_id();
		setcookie('session_id', $id_session, 0, '/');
		
			$session_time_left = $_SESSION['session_time'] - $_SESSION['session_start_time'];
		if($_SESSION['session_time'] == time()) {
			$_SESSION['session_conn_count'] = $_SESSION['session_conn_count'] + 1; 
			if ($_SESSION['session_conn_count'] > 10){
				echo  "Колличество запросов в секунду с вашего компьютера превысило максимум. 
				Просьба не превышать колличество обновлений страници в секунду. 
				С вашего компьютера было произведено ".$_SESSION['session_conn_count']." запросов.";
				exit;
			}
		} else { $_SESSION['session_conn_count'] = 0; }
		if(!$_SESSION['session_start_time']) 	$_SESSION['session_start_time'] = time(); 	//Время начала сессии
												$_SESSION['session_time'] = time();			//Текуущее время сессии
}

function toJSON($o) {
	switch (gettype($o)) {
		case 'NULL':
			return 'null';
		case 'integer':return '"' . addslashes($o) . '"';
		case 'double':return '"' . addslashes($o) . '"';
			return strval($o);
		case 'string':
			return '"' . addslashes($o) . '"';
		case 'boolean':
			return $o ? 'true' : 'false';
		case 'object':
			$o = (array) $o;
		case 'array':
			$foundKeys = false;

			foreach ($o as $k => $v) {
				if (!is_numeric($k)) {
					$foundKeys = true;
					break;
				}
			}

			$result = array();

			if ($foundKeys) {
				foreach ($o as $k => $v) {
					$result []= toJSON($k) . ':' . toJSON($v);
				}

				return '{' . implode(',', $result) . '}';
			} else {
				foreach ($o as $k => $v) {
					$result []= toJSON($v);
				}

				return '[' . implode(',', $result) . ']';
			}
	}
}

function rus2translit($string) {
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',
			'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
			'и' => 'i',   'й' => 'y',   'к' => 'k',
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'h',   'ц' => 'c',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
			'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
			
			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
			'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
			'И' => 'I',   'Й' => 'Y',   'К' => 'K',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
			'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
			'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
		);
		return strtr($string, $converter);
}
// Радиус земли
define('EARTH_RADIUS', 6372795);
 
/*
 * Расстояние между двумя точками
 * $φA, $λA - широта, долгота 1-й точки,
 * $φB, $λB - широта, долгота 2-й точки
 * Написано по мотивам http://gis-lab.info/qa/great-circles.html
 * Михаил Кобзарев <mikhail@kobzarev.com>
 *
 */
function calculateTheDistance ($φA, $λA, $φB, $λB) {
 
    // перевести координаты в радианы
    $lat1 = $φA * M_PI / 180;
    $lat2 = $φB * M_PI / 180;
    $long1 = $λA * M_PI / 180;
    $long2 = $λB * M_PI / 180;
 
    // косинусы и синусы широт и разницы долгот
    $cl1 = cos($lat1);
    $cl2 = cos($lat2);
    $sl1 = sin($lat1);
    $sl2 = sin($lat2);
    $delta = $long2 - $long1;
    $cdelta = cos($delta);
    $sdelta = sin($delta);
 
    // вычисления длины большого круга
    $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
    $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;
 
    //
    $ad = atan2($y, $x);
    $dist = $ad * EARTH_RADIUS;
 
    return $dist;
}

/**
 * Возвращает сумму прописью
 * @author runcore
 * @uses morph(...)
 */
function num2str($num) {
	$nul='ноль';
	$ten=array(
		array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
		array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
	);
	$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	$unit=array( // Units
		array('копейка' ,'копейки' ,'копеек',	 1),
		array('рубль'   ,'рубля'   ,'рублей'    ,0),
		array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
		array('миллион' ,'миллиона','миллионов' ,0),
		array('миллиард','милиарда','миллиардов',0),
	);
	//
	list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
	$out = array();
	if (intval($rub)>0) {
		foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
			if (!intval($v)) continue;
			$uk = sizeof($unit)-$uk-1; // unit key
			$gender = $unit[$uk][3];
			list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
			// mega-logic
			$out[] = $hundred[$i1]; # 1xx-9xx
			if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
			else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
			// units without rub & kop
			if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
		} //foreach
	}
	else $out[] = $nul;
	$out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
	$out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
	return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
	$n = abs(intval($n)) % 100;
	if ($n>10 && $n<20) return $f5;
	$n = $n % 10;
	if ($n>1 && $n<5) return $f2;
	if ($n==1) return $f1;
	return $f5;
}

function getExtension1($filename) {
	return strtolower(end(explode(".", $filename)));
}
function getExtension2($filename) {
	$path_info = pathinfo($filename);
	return strtolower($path_info['extension']);
}
function getExtension3($filename) {
	return strtolower(substr($fileName, strrpos($fileName, '.') + 1));
}
function getExtension4($filename) {
	return strtolower(substr(strrchr($fileName, '.'), 1));
}
function getExtension5($filename) {
	return strtolower(array_pop(explode(".", $filename)));
}

function multiexplode($delimiters, $string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

function generatePassword($length=9, $strength=8) {
 $vowels = 'aeuy';
 $consonants = 'bdghjmnpqrstvz';
 if ($strength >= 1) {
 $consonants .= 'BDGHJLMNPQRSTVWXZ';
 }
 if ($strength >= 2) {
 $vowels .= "AEUY";
 }
 if ($strength >= 4) {
 $consonants .= '23456789';
 }
 if ($strength >= 8 ) {
 $vowels .= '@#$%';
 }
// Генерируем пароль
 $password = '';
 $alt = time() % 2;
 for ($i = 0; $i < $length; $i++) {
 if ($alt == 1) {
 $password .= $consonants[(rand() % strlen($consonants))];
 $alt = 0;
 } else {
 $password .= $vowels[(rand() % strlen($vowels))];
 $alt = 1;
 }
 }
 return $password;
}

function genCode($len,$our_alphabet = false) {
	$alphabet = array(
		'1','2','3','4','5','6','7','8','9','0',
		'Q','W','E','R','T','Y','U','I','O','P',
		'A','S','D','F','G','H','J','K','L','Z',
		'X','C','V','B','N','M'
	);
	if ($our_alphabet) $alphabet = $our_alphabet;
	$out = '';
	
	for ($i=0;$i<$len;$i++) {
		$out.=$alphabet[rand(0,count($alphabet)-1)];
	}
	return $out;
}

function check_phone($phone){
	/* +38 (050) 693-75-72*/
	$phone = str_replace('(','',$phone);
	$phone = str_replace(')','',$phone);
	$phone = str_replace(' ','',$phone);
	$phone = str_replace('-','',$phone);
	$phone = str_replace('+','',$phone);
	$phone = '+'.$phone;
	/* +380506937572 */
	if (substr($phone,0,1)!='+') return false;
	if (strlen($phone)!=13 and strlen($phone)!=12) return false;
	for ($i=0;$i<strlen($phone);$i++) {
		if ($i!=0) {
			if (!is_numeric(substr($phone,$i,1))) return false;
		}
	}
	/*
		код страны
		оператор
		номер
	*/
	$code_contry = substr($phone,0,2);
	if($code_contry=="+7" or $code_contry=="+8"){
		$phone = str_replace('+8','+7',$phone);
		$code = substr($phone,0,2);
		$oppe = substr($phone,2,3);
		$numm = substr($phone,5);
	}
	else{
		$code = substr($phone,0,3);
		$oppe = substr($phone,3,3);
		$numm = substr($phone,6);
	}
	
	
	return array(
		'valid' => true,
		'country' => $code,
		'oper' => $oppe,
		'number' => $numm,
		'phone' => $phone
	);
}

function SMS_GW_Send($sign,$number,$message) {
	$message = iconv('utf-8', 'cp1251', $message);
	file_get_contents('https://smsc.ru/sys/send.php?login=feoboss&psw=maximus1975&phones='.$number.'&mes=Feomedia: '.$message);
}

function detect_city($ip = null) {
	if(is_null($ip)) $ip = getIp();
    $apiKey = '5fea8a7ccb94704fc95a2e47067e10ec2caeb02e033c3b9d1a6f8ca827128526';
	$url = "http://api.ipinfodb.com/v3/ip-city/?key=$apiKey&ip=$ip&format=json";
     
	$d = file_get_contents($url);
	$data = json_decode($d , true);
	return $data;
}

function isBot(&$botname = ''){
/* Эта функция будет проверять, является ли посетитель роботом поисковой системы */
  $bots = array(
    'rambler','googlebot','aport','yahoo','msnbot','turtle','mail.ru','omsktele',
    'yetibot','picsearch','sape.bot','sape_context','gigabot','snapbot','alexa.com',
    'megadownload.net','askpeter.info','igde.ru','ask.com','qwartabot','yanga.co.uk',
    'scoutjet','similarpages','oozbot','shrinktheweb.com','aboutusbot','followsite.com',
    'dataparksearch','google-sitemaps','appEngine-google','feedfetcher-google',
    'liveinternet.ru','xml-sitemaps.com','agama','metadatalabs.com','h1.hrn.ru',
    'googlealert.com','seo-rus.com','yaDirectBot','yandeG','yandex',
    'yandexSomething','Copyscape.com','AdsBot-Google','domaintools.com',
    'Nigma.ru','bing.com','dotnetdotcom','OdklBot','odnoklassniki.ru','vk.com','facebook.com','instagram.com'
  );
  foreach($bots as $bot)
    if(stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false){
      $botname = $bot;
      return true;
    }
  return false;
}

function getIp(){
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		return $ip;
}

function getPlugins(){
	define ("PLUGINSDIR", APPDIR ."/application/plugins");
	$files = array();
	$PLUGINSDIR = scandir(PLUGINSDIR);
	if(is_array($PLUGINSDIR)) foreach ($PLUGINSDIR as $i => $name){
		if($name!='.' and $name!='..' and substr($name,0,1) != "_"){
			if(is_dir(PLUGINSDIR. "/". $name)) {
				$dir = PLUGINSDIR. "/". $name;
				$scandir = scandir($dir);
				if(is_array($scandir)) foreach ($scandir as $j => $file_name){
					if($file_name!='.' and $file_name!='..'){
						if(!is_dir($dir. "/". $file_name)) { 
							if(is_plugin($dir. "/". $file_name)) {
								if(substr($file_name,0,1) != "_"){
									try {
										if ( syntax_check_php_file($dir. "/". $file_name) )
											require_once($dir . "/" . $file_name);
									} catch (Exception $e) {
										file_put_contents($dir . "/error_" . $file_name.".log", $e -> getMessage() . "\r\n", FILE_APPEND);
									}
								}
							}
						}
					}
				}
			}
			else {
				if(is_plugin(PLUGINSDIR. "/". $name)) {
					if(substr($name,0,1) != "_"){
						try {
							if ( syntax_check_php_file(PLUGINSDIR. "/". $name) )
								require_once(PLUGINSDIR . "/". $name);
						} catch (Exception $e) {
							file_put_contents(PLUGINSDIR . "/error_" . $name.".log", $e -> getMessage() . "\r\n", FILE_APPEND);			
						}
					}
				}
			}
		}
	}
}

function is_plugin($name){
	$plugin = array();
	$file = file($name);
	foreach($file as $i => $row){
		$par = strtolower(trim(substr($row, 0, strpos($row,":"))));
		$val = strtolower(trim(substr($row, (strpos($row,":")+1), strlen($row))));
		switch($par){
			case strtolower("Plugin Name") : $plugin['Plugin Name']=$val; break;
			case strtolower("Plugin URI") : $plugin['Plugin URI']=$val; break;
			case strtolower("Description") : $plugin['Description']=$val; break;
			case strtolower("Version") : $plugin['Version']=$val; break;
			case strtolower("Author") : $plugin['Author']=$val; break;
			case strtolower("Author URI") : $plugin['Author URI']=$val; break;
		}
	}
	if(count($plugin)>0) return $plugin; else return false;
}

function is_admin(){
	global $is_admin;
	if(isset( $is_admin ) and $is_admin == true) 
		return true; 
	else 
		return false;
}

function register_page(){
	global $_registered_pages;
	$args = func_get_args();
	$_registered_pages[] = array(
		'uri' => $args[0],
		'render' => $args[1],
		'description' => $args[2]
	);
}

function render_page(){
	$args = func_get_args();
	echo call_user_func_array('get_page', $args);
}

function get_page(){
	global $_registered_pages;
	$args = func_get_args();
	$result = '';
	foreach($_registered_pages as $i=>$t){
		$uri1 = (substr($t['uri'],-1) != '/')? $t['uri'].'/' : $t['uri'];
		$uri2 = (substr($args[0],-1) != '/')? $args[0].'/' : $args[0];
		if($uri1 == $uri2){
			$result = call_user_func_array($t['render'], $args);
		}
	}
	return do_shortcode($result);
}

function is_registered_page(){
	global $_registered_pages;
	$args = func_get_args();
	foreach($_registered_pages as $i=>$t){
		$uri1 = (substr($t['uri'],-1) != '/')? $t['uri'].'/' : $t['uri'];
		$uri2 = (substr($args[0],-1) != '/')? $args[0].'/' : $args[0];
		if($uri1 == $uri2){
			return true;
		}
	}
	return false;
}

function register_controller(){
	global $_registered_controllers;
	$args = func_get_args();
	$_registered_controllers[] = array(
		'name' => $args[0],
		'render' => $args[1],
		'description' => $args[2]
	);
}

function render_controller(){
	$args = func_get_args();
	echo call_user_func_array('get_controller', $args);
}

function get_controller(){
	global $_registered_controllers;
	$args = func_get_args();
	$result = '';
	foreach($_registered_controllers as $i=>$t){
		if($t['name'] == $args[0]){
			$result = call_user_func_array($t['render'], $args);
		}
	}
	return do_shortcode($result);
}

function is_registered_controller(){
	global $_registered_controllers;
	$args = func_get_args();
	foreach($_registered_controllers as $i=>$t){
		if($t['name'] == $args[0]){
			return true;
		}
	}
	return false;
}

function register_page_template(){
	global $_pages_templates;
	$args = func_get_args();
	$_pages_templates[] = array(
		'name' => $args[0],
		'title' => $args[1],
		'render' => $args[2]
	);
}

function render_page_template(){
	$args = func_get_args();
	echo do_shortcode(call_user_func_array('get_page_template', $args));
}

function get_page_template(){
	global $_pages_templates;
	$args = func_get_args();
	$result = '';
	foreach($_pages_templates as $i=>$t){
		if($t['name'] == $args[0]['post_template']){
			$result = call_user_func_array($t['render'], $args);
		}
	}
	return do_shortcode($result);
}

function get_page_template_options(){
	global $_pages_templates;
	$args = func_get_args();
	$result = '';
	foreach($_pages_templates as $i=>$t){
		$c = ($t['name'] == $args[0])? 'selected="selected"':'';
		$result .= '<option '.$c.' value="'.$t['name'].'">'.$t['title'].'</option>';
	}
	return do_shortcode($result);
}

function head(){
	$args = func_get_args();
	$path = APPDIR.'/application/views/' . ((!empty($args[0]))?$args[0].'/':'');
	if(file_exists($path.'header.php')){
		
		$meta = $args[1];
		$title = (!empty($meta['title'])? $meta['title']:'Заголовок');
		$description = (!empty($meta['description'])? $meta['description']:'Описание');
		$keywords = (!empty($meta['keywords'])? $meta['keywords']:'Ключевые слова');
		$url = (!empty($meta['url'])? $meta['url']: 'http://'.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);
		$image = (!empty($meta['image'])? $meta['image']:'http://'.$_SERVER["HTTP_HOST"].'/favicon.ico');
		ob_start();
		include $path.'header.php';
		$out=ob_get_clean();
		echo do_shortcode($out);
	}
}

function footer(){
	$args = func_get_args();
	$path = APPDIR.'/application/views/' . ((!empty($args[0]))?$args[0].'/':'');
	if(file_exists($path.'footer.php')){
		include $path.'footer.php';
	}
}

function get_cache($file = ''){
		$cache_dir = APPDIR . '/application/cache/'; 
		$cache_expire = 60 /* sec */ * 10 /* min */; // seconds
		$cache_file_name =  $cache_dir . $file.'_'.md5($_SERVER['REQUEST_URI']).'.html';
		if(empty($_POST) and is_readable($cache_file_name) and ((time() - $cache_expire) < filemtime($cache_file_name))){
			return file_get_contents($cache_file_name);
		} else return false;
	}
	
function set_cache($file = '',$content){
		$cache_dir = APPDIR . '/application/cache/'; 
		$cache_expire = 60 /* sec */ * 10 /* min */; // seconds
		$cache_file_name =  $cache_dir . $file.'_'.md5($_SERVER['REQUEST_URI']).'.html';
		if(empty($_POST))
			file_put_contents($cache_file_name, $content);
		return $content;
	}

function add_menu_page( $page_title, $menu_title, $menu_slug, $action, $icon='ico-home') {
		global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages;
		
		$new_menu = array( 
							'page_title' => $page_title,
							'title' => $menu_title,
							'menu_slug' => $menu_slug,
							'action' => $action,
							'icon' => $icon
						);
		$menu[] = $new_menu;
}

function generate_menu(){
	global $menu;
	echo "<ul>";
	foreach($menu as $i => $m){
		
		$title = $m['title'];
		$menu_slug = $m['menu_slug'];
		$action = $m['action'];
		$icon = $m['icon'];
		//echo ADMINDIR . '/application/controllers/controller_' . $action;
		
		if(file_exists(ADMINDIR . '/application/controllers/controller_' . $action . '.php')) 
		{
			$action = ($action == 'index') ? '' : $action;
			$href = '/admin/' . $action;
			if(substr($href, -1)!='/') 	$href .= '/';
		}
		elseif(is_array($action))
		{
			if(method_exists($action[0], $action[1])) 
				$href = "/admin/?menu=" . $menu_slug; 
		}
		elseif(function_exists($action))
		{
			$href = "/admin/?menu=" . $menu_slug; 
		}
		else 
		{
			$href = '';
		}
		$main_href = explode('?',$_SERVER['REQUEST_URI']);
		
		if((strpos($_SERVER['REQUEST_URI'], $href)!==false AND $href!='/admin/') OR ($_SERVER['REQUEST_URI'] == $href)){
			$curent = 'menu-current';
		}
		else{
			$curent = '';
		}
		
		switch ($icon) {
			case 'ico-home': 	$icon = "<div class='menu-icon $curent ico-home'></div>"; break;
			case 'ico-pages': 	$icon = "<div class='menu-icon $curent ico-pages'></div>"; break;
			case 'ico-preview': 	$icon = "<div class='menu-icon $curent ico-preview'></div>"; break;
			case 'ico-files': 	$icon = "<div class='menu-icon $curent ico-files'></div>"; break;
			case 'ico-users': 	$icon = "<div class='menu-icon $curent ico-users'></div>"; break;
			case 'ico-pin': 	$icon = "<div class='menu-icon $curent ico-pin'></div>"; break;
			case 'ico-hammer': 	$icon = "<div class='menu-icon $curent ico-hammer'></div>"; break;
			default: $icon = "<div class='menu-icon $curent ico-home'></div>"; break;
		}
		
		
		if($href != '')
			echo "<li class='$curent'><a href='$href'><div class='menu-item'>$icon<div class='menu-title'>$title</div></div></a></li>";
		else
			echo "<li><div class='menu-item'>$icon<div class='menu-title'>$title</div></div></li>";
		
		
	}
	echo "</ul>";
}

function setVar($VAR, $val){
	$variables_model = new model_variables();
	return $variables_model->setVar($VAR, $val);
}

function delVar($VAR){
	$variables_model = new model_variables();
	return $variables_model->delVar($VAR);
}

function getVar($VAR){
	$variables_model = new model_variables();
	return $variables_model->getVar($VAR);
}

function getPermissions($res = null){
		$model= new Model();
		$perm = $model->getPermissions($res);
		if($res == null)
			return $perm;
		else 
			return ($perm['ac_val'] == 1)? true : false;
}

function generateLoginForm(){
		$content_view = APPDIR . "/application/views/loginForm.php";
		$data = array('url' => $_SERVER['REQUEST_URI'], 'h1' => 'Панель администрирования');
		include $content_view;
		exit;
}
/*
function get_locale(){
	global $locale;
	return $locale;
}
*/
function set_locale($lang = 'ru_RU'){
	global $locale;
	$locale = $lang;
	run_locale();
	return $locale;
}

function get_locale_dir(){
	return APPDIR."/locale";	
}

function run_locale(){
	$domain = get_locale();
	putenv("LANG=".$domain);
	setlocale (LC_ALL, "Russian");
	bindtextdomain ($domain, get_locale_dir());
	textdomain ($domain);
	bind_textdomain_codeset($domain, 'UTF-8');
}

function get_scripts(){
	ob_start();	wp_print_scripts(); return ob_get_clean();
}

function get_styles(){
	ob_start();	wp_print_styles(); return ob_get_clean();
}

function get_mail_template($name, $replace=array()){
	global $model;
	$tmp = $model->get_mail_template($name);
	if(is_array($replace)){
		foreach($replace as $key=>$val){
			$tmp = str_replace('%'.$key.'%', $val, $tmp);
		}	
	}
	return $tmp;
}

function send_mail($address, $title='', $message='', $headers = '', $who_send='system', $user_id = null, $id_work=null, $post_id=null){
	global $model;
		$data=array(
			'm_address' => trim($address),
			'm_title' => trim($title),
			'm_who_send' => trim($who_send),
			'm_readed' => 0,
			'm_linked' => 0,
			'm_work_id' => $id_work,
			'm_post_id' => $post_id,
		);
		if(!empty($user_id)) $data['m_user_id'] = $user_id;
		$tmp = $model->db->query('INSERT INTO `mvc_mail_log` SET m_date_send=NOW(), ?u', $data);
		//$id = $model->db->insertId();
		
	$message='<img src="http://xn--80aahjj1e.xn--e1asq.xn--p1ai/uploads/rewrited/mail_'.$user_id.'_'.$id_work.'_'.$post_id.'_'.uniqid().'.gif" />'.$message;
	$message = str_replace('%MAILID%', $id, $message);
	$message = str_replace('%USERID%', $user_id, $message);
	$message = str_replace('%WORKERID%', $id_work, $message);
	$message = str_replace('%POSTID%', $id_work, $message);
	$message = apply_filters('mail_text', $message, $user_id, $id_work, $post_id);
	if(mail($address, $title, $message, $headers))
		return true;
	else {
		return false;
	}
}

function isAssoc(array $arr){
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function testIp($ip, $permitted='127.0.0.1'){
	$result = false;
	$results = true;
	if(is_array($permitted)){
		foreach($permitted as $permittedIp){
			$result = (testIp($ip, $permittedIp))?true:$result;
		}
		return $result;
	}
	else{
		preg_match ("/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)/", $ip, $arrayIP);
		preg_match ("/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)/", $permitted, $arrayPERMITTED);
		for($i=4;$i>=1;$i=$i-1){
			$results=($arrayPERMITTED[$i]==0 or $arrayPERMITTED[$i]==$arrayIP[$i])?$results:false;
		}
		return $results;
	}
	
}

function image_resize($source_path, $destination_path, $newwidth, $newheight = FALSE,  $quality = 100) {
    ini_set("gd.jpeg_ignore_warning", 1); // иначе на некотоых jpeg-файлах не работает
    list($oldwidth, $oldheight, $type) = getimagesize($source_path);

    switch ($type) {
        case IMAGETYPE_JPEG: $typestr = 'jpeg'; break;
        case IMAGETYPE_GIF: $typestr = 'gif' ;break;
        case IMAGETYPE_PNG: $typestr = 'png'; break;
    }
    $function = "imagecreatefrom$typestr";if (!function_exists($function)) return false;
    $src_resource = $function($source_path);
    
    if (!$newheight) { $newheight = round($newwidth * $oldheight/$oldwidth); }
    elseif (!$newwidth) { $newwidth = round($newheight * $oldwidth/$oldheight); }
    $destination_resource = imagecreatetruecolor($newwidth,$newheight);
    
    imagecopyresampled($destination_resource, $src_resource, 0, 0, 0, 0, $newwidth, $newheight, $oldwidth, $oldheight);
    
    if ($type = 2) { # jpeg
        imageinterlace($destination_resource, 1); // чересстрочное формирование изображение
        imagejpeg($destination_resource, $destination_path, $quality);      
    }
    else { # gif, png
        $function = "image$typestr";
        $function($destination_resource, $destination_path);
    }
    
    imagedestroy($destination_resource);
    imagedestroy($src_resource);
}

//resize and crop image by center
function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 80){
    $imgsize = getimagesize($source_file);
    $width = $imgsize[0];
    $height = $imgsize[1];
    $mime = $imgsize['mime'];
 
    switch($mime){
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image = "imagegif";
            break;
 
        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image = "imagepng";
            $quality = 7;
            break;
 
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image = "imagejpeg";
            $quality = 80;
            break;
 
        default:
            return false;
            break;
    }
	
    list($oldwidth, $oldheight, $type) = getimagesize($source_file);
	
    if (!$max_height) { $max_height = round($max_width * $oldheight/$oldwidth); }
    elseif (!$max_width) { $max_width = round($max_height * $oldwidth/$oldheight); }
     
    $dst_img = imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($source_file);
     
    $width_new = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
    if($width_new > $width){
        //cut point by height
        $h_point = (($height - $height_new) / 2);
        //copy image
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    }else{
        //cut point by width
        $w_point = (($width - $width_new) / 2);
        imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }
     
    $image($dst_img, $dst_dir, $quality);
 
    if($dst_img)imagedestroy($dst_img);
    if($src_img)imagedestroy($src_img);
}
//usage example
//resize_crop_image(100, 100, "test.jpg", "test.jpg");

function image_crop($source_path, $destination_path, $x, $y, $w, $h, $percent,  $quality = 100) {
    ini_set("gd.jpeg_ignore_warning", 1); // иначе на некотоых jpeg-файлах не работает
    list($oldwidth, $oldheight, $type) = getimagesize($source_path);

    switch ($type) {
        case IMAGETYPE_JPEG: $typestr = 'jpeg'; break;
        case IMAGETYPE_GIF: $typestr = 'gif' ;break;
        case IMAGETYPE_PNG: $typestr = 'png'; break;
    }
    $function = "imagecreatefrom$typestr";if (!function_exists($function)) return false;
    $src_resource = $function($source_path);
    
	$nw = $w * $percent / 100;
	$nh = $h * $percent / 100;
	
    $destination_resource = imagecreatetruecolor($nw,$nh);
    imagecopyresampled($destination_resource, $src_resource, 0, 0, $x, $y, $nw, $nh, $w, $h);

    if ($type = 2) { # jpeg
        imageinterlace($destination_resource, 1); // чересстрочное формирование изображение
        imagejpeg($destination_resource, $destination_path, $quality);      
    }
    else { # gif, png
        $function = "image$typestr";
        $function($destination_resource, $destination_path);
    }
    
    imagedestroy($destination_resource);
    imagedestroy($src_resource);
}

function image_thrumb($source_path, $destination_path, $newwidth = 537, $newheight = 240, $quality=100){
	$source=$source_path; //наш исходник
	$height=$newheight; //параметр высоты превью
	$width=$newwidth ; //параметр ширины превью
	$rgb=0xffffff; //цвет заливки несоответствия
	$size = getimagesize($source);//узнаем размеры картинки (дает нам масив size)
	$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1)); //определяем тип файла
	$icfunc = "imagecreatefrom" . $format;   //определение функции соответственно типу файла
	if (!function_exists($icfunc)) return false;  //если нет такой функции прекращаем работу скрипта
	$x_ratio = $width / $size[0]; //пропорция ширины будущего превью
	$y_ratio = $height / $size[1]; //пропорция высоты будущего превью
	$ratio       = min($x_ratio, $y_ratio);
	$use_x_ratio = ($x_ratio == $ratio); //соотношения ширины к высоте
	$new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio); //ширина превью 
	$new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio); //высота превью
	$new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2); //расхождение с заданными параметрами по ширине
	$new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2); //расхождение с заданными параметрами по высоте
	$img = imagecreatetruecolor($width,$height); //создаем вспомогательное изображение пропорциональное превью
	imagefill($img, 0, 0, $rgb); //заливаем его…
	$photo = $icfunc($source); //достаем наш исходник
	imagecopyresampled($img, $photo, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]); //копируем на него нашу превью с учетом расхождений
	//imagejpeg($img); //выводим результат (превью картинки)
	imagejpeg($img, $destination_path, $quality);
	// Очищаем память после выполнения скрипта
	imagedestroy($img);
	imagedestroy($photo);
}

if(!class_exists("default_plugins")){
	class default_plugins {
		private $REQUEST_URI;
		private $model;
		
		function __construct(){
			$REQUEST_URI = $_SERVER['REQUEST_URI'];
			$this->REQUEST_URI = urldecode( (mb_substr_count( $_SERVER['REQUEST_URI'], '?') > 0) ? substr($REQUEST_URI, 0, strpos($REQUEST_URI,'?')) : $REQUEST_URI );
			
			remove_all_filters('the_editor_content');
			add_filter('the_editor_content', array($this, 'return_editor'), 10, 1);
			
			remove_all_filters('the_admin-name-ru');
			add_filter('the_admin-name-ru', array($this, 'return_the_admin_name_ru'), 10, 1);
			
			remove_all_filters('the_editor_tabs');
			add_filter('the_editor_tabs', array($this, 'return_editor_tabs'), 10, 1);
			
			wp_register_script( 'jquery', '/js/jquery-1.9.1.min.js' );wp_enqueue_script( 'jquery' );
			wp_register_script( 'jquery.ui', '/js/jquery/ui/jquery-ui.min.js' );wp_enqueue_script( 'jquery.ui' );
			wp_register_script( 'jquery.ui.datepicker.ru', '/js/jquery.ui.datepicker-ru.js' );wp_enqueue_script( 'jquery.ui.datepicker.ru' );
			wp_register_style( 'jquery.ui', '/js/jquery/ui/jquery-ui.css' );
			wp_register_style( 'jquery.ui.structure', '/js/jquery/ui/jquery-ui.structure.min.css' );
			wp_register_style( 'jquery.ui.theme', '/js/jquery/ui/jquery-ui.theme.min.css' );
			
			wp_register_style( 'jquery.ui.theme', '/js/jquery/ui/jquery-ui.theme.min.css' );
			
			add_shortcode( 'image', array($this, 'photo_galery'));
			add_shortcode( 'photo', array($this, 'photo_galery'));
			add_shortcode( 'audio', array($this, 'audio_player'));
			add_shortcode( 'yvideo', array($this, 'yvideo'));
			add_shortcode( 'pdf', array($this, 'pdf_viewer'));
			add_shortcode( 'pagenavigation', array($this, 'page_navigation'));
			$this->model = new Model();
			register_controller('mailredirect', array($this, 'controller_mailredirect'), 'rss');
		}
		
		function return_editor($val){
			$r = '';
			$r .= '<div id="editor-text" class="editor-text">' . $val . '</div>';
			return $r;
		}
		
		function return_the_admin_name_ru($val){
			$r = '';
			$r .= '<div class="border"><input id="post-name" name="post_name_ru" type="text" value = "'.$val.'" /></div>';
			return $r;
		}
		
		function return_editor_tabs($val){
			$r 	= '
			';
			return $r;
		}
		
		function yvideo($atts, $content = null){
			extract( shortcode_atts( array(
				'id' => uniqid(),
				'src'=>'',
				'width' => 640,
				'height' => 360,
			), $atts ) );
			
			return '<iframe src="'.$src.'" width="'.$width.'" height="'.$height.'" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
			
		}
		
		function photo_galery($atts, $content = null){
			extract( shortcode_atts( array(
				'id'=>'',
				'limit'=>null,
				'src'=>'',
				'title'=>'',
				'gallery' => '',
				'params' => array(),
				'width' => 'auto',
				'height' => 'auto',
				'maxwidth' => 'auto',
				'maxheight' => 'auto',
				'margin' => '0px',
				'padding' => '5px',
			), $atts ) );
			$script='<script type="text/javascript">$(function(){ showVisible(); $(".fancybox").fancybox('.(!empty($params)?json_encode($params, JSON_UNESCAPED_UNICODE):'').'); });</script>';
			$items='';
			if(!empty($id)){
				$ids=explode(';', $id);
				$files = $this->model->db->GetAll("SELECT * FROM mvc_posts WHERE post_id IN(?a)", $ids);
				foreach($files as $file){
					$file['post_content'] = json_decode($file['post_content'], true);
					$ext = getExtension1($file['post_name']);
						$items.='
						<a class="fancybox" rel="'.$gallery.'" href="/uploads/image/1000/'.$file['post_id'].'.'.$ext.'" title="'.$title.'" style="text-decoration:none;">
							<div class="responsive-container" style="padding:'.$padding.';margin:'.$margin.';max-width:'.$maxwidth.';max-height:'.$maxheight.';overflow:hidden;display:inline-block;">
								<div class="img-container">
									<img src="/img/w1.png" realsrc="/uploads/image/300/'.$file['post_id'].'.'.$ext.'" alt="" width="'.$width.'" height="'.$height.'"/>
								</div>
							</div>
						</a>';
				}
			}
			else{
				$srcs=explode(';', $src);
				foreach($srcs as $src){
					if(!empty($src)){
						$items.='
						<a class="fancybox" rel="'.$gallery.'" href="'.$src.'" title="'.$title.'">
							<div class="responsive-container" style="padding:'.$padding.';margin:'.$margin.';max-width:'.$maxwidth.';max-height:'.$maxheight.';overflow:hidden;display:inline-block;">
								<div class="img-container">
									<img src="/img/w1.png" realsrc="'.$src.'" alt="" width="'.$width.'" height="'.$height.'"/>
								</div>
							</div>
						</a>';
					}
				}
			}
			return $script.$items;
		}
		
		function audio_player($atts, $content = null){
			extract( shortcode_atts( array(
				'src'=>'',
				'title'=>'',
				'class'=>'audio_'.uniqid(),
				'params' => array(),
				'width' => 'auto',
				'height' => 'auto',
			), $atts ) );
			$script='<link href="/css/audioplayer.css" rel="stylesheet" type="text/css" media="all" /><script src="/js/audioplayer.js"></script><script type="text/javascript">$(function(){ $(".'.$class.'").audioPlayer(); });</script>';
			$items='';
			$srcs=explode(';', $src);
			foreach($srcs as $src){
				$items.='<audio class="'.$class.'" title="'.$title.'" src="'.$src.'" alt="" width="'.$width.'" height="'.$height.'" preload="auto" controls></audio>';
			}
			return $script.$items;
			
		}
		
		function pdf_viewer($atts, $content = null){
			extract( shortcode_atts( array(
				'src'=>'',
				'title'=>'',
				'class'=>'pdf_'.uniqid(),
				'params' => array(),
				'width' => '100%',
				'height' => '800px',
			), $atts ) );
			$script='';
			$items='';
			$srcs=explode(';', $src);
			foreach($srcs as $src){
				$items.='<iframe class="'.$class.'" title="'.$title.'" src="'.$src.'" alt="" width="'.$width.'" height="'.$height.'"></iframe>';
			}
			return $script.$items;
			
		}
		
		function page_navigation($atts, $content = null){
			extract( shortcode_atts( array(
				'n' => 0,
				'limit' => 0,
				'page' => (int)$_GET['page'],
				'url' => $this->REQUEST_URI,
				'first'=>'Первая',
				'previos'=>'Предыдущая',
				'next'=>'Следующая',
				'last'=>'Последняя',
			), $atts ));
			$n = (int)$n;
			$limit = (int)$limit;
			$page = (int)$page;
			$get = '';
			foreach($_GET as $i => $g){
				if($i!='page')	$get .=  $i .'='.$g.'&';
			}
			if ($n > $limit){
				$cp = ceil( $n / $limit );
				$result = '<div class="page-navigation">';
				
				if($page != 1){
					$previos_ = $page-1;
					$result .= "<span class='pageLink'><a href='{$url}?{$get}page=1'>{$first}</a></span>";
					$result .= "<span class='pageLink'><a href='{$url}?{$get}page={$previos_}'>{$previos}</a></span>";
				}
				
				if($page - 2 > 0) 	 { 
					$p = $page - 2;
					$result .= "<span class='pageLink'><a href='{$url}?{$get}page={$p}'>{$p}</a></span>"; 
				}
				if($page - 1 > 0) 	 { 
					$p = $page - 1;
					$result .= "<span class='pageLink'><a href='{$url}?{$get}page={$p}'>{$p}</a></span>"; 
				}
					$result .= "<span class='pageLink'><span class='currentPage'>{$page}</span></span>"; 
					
				if($page + 1 <= $cp) { 
					$p = $page + 1;
					$result .= "<span class='pageLink'><a href='{$url}?{$get}page={$p}'>{$p}</a></span>"; 
				}
				if($page + 2 <= $cp) { 
					$p = $page + 2;
					$result .= "<span class='pageLink'><a href='{$url}?{$get}page={$p}'>{$p}</a></span>"; 
				}
				
				if($page != $cp){
					$next_ = $page+1;
					$result .= "<span class='pageLink'><a href='{$url}?{$get}page={$next_}'>{$next}</a></span>";
					$result .= "<span class='pageLink'><a href='{$url}?{$get}page={$cp}'>{$last}</a></span>";
				}
				$result .= '</div>';
				return $result;
			}
		}
		
		function controller_mailredirect($controller, $routes){
			if(isset($_GET['uid']) and is_numeric($_GET['uid'])){
				//$this->model->db->query("UPDATE mvc_mail_log SET m_linked=m_linked+1 WHERE m_id=?i", (int)$_GET['mail']);
				$this->model->db->query("UPDATE mvc_email_workers SET `work_readed`=`work_readed`+1 WHERE work_id=?i", (int)$_GET['work']);
				
				$data=array("red_uid"=>(int)$_GET['uid'], "red_work_id"=>(int)$_GET['work'], "red_post_id"=>(int)$_GET['post'], "red_url"=>urldecode($_GET['redirect']), "red_ip"=>getIp());
				$this->model->db->query("INSERT INTO mvc_redirects SET red_date=NOW(), ?u", $data);
			}
			if(isset($_GET['redirect'])){
				header('Location: '.urldecode($_GET['redirect']));
				exit;
			}
		}
	}
	
	//$default_plugins = new default_plugins();
}
