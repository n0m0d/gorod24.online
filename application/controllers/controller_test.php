<?php
class controller_test extends Controller
{
	function __construct(){

	}
	
	public function action_index($routes = array()){
		//$gispars = new GisPars(847);
		//847 фео
		//845 керчь
		$parse = [
			1483 => [ "gis" => 847, "water" => 227859 ],
			478 => [ "gis" => 845, "water" => 227857 ],
		];
		$city = 478;
		
		$gispars = new GisPars($parse[$city]['gis']);
		$res = $gispars->GetParse();

		$times = array('2.40', '8.40', '14.40', '20.40');
		$k = 0;
		$m_time = date('H.i', time());
		foreach($times as $time){    if ($m_time>=$time) $k++;}
		if ($k==4) $k=3;
		$k=0;
		
		
		$c_date=date("Y-m-j H:i:s");
		$c_date_t = strtotime($c_date);

		for ($need_k=0;$need_k<4;$need_k++)		{
				if (isset($res['d'.$need_k])) {
						$f_date = $res['d'.$need_k]['year'].'-'.$res['d'.$need_k]['month'].'-'.$res['d'.$need_k]['day'].' '.$res['d'.$need_k]['hour'].':00:00';
						$f_date_t = strtotime($f_date);
						if ($c_date_t>$f_date_t) $k=$need_k;
				}
				
		}
		
if (isset($res['p'.$k])){
        $res['p'.$k]['c'] = $res['p'.$k]['cloudiness'];
        $res['p'.$k]['p'] = $res['p'.$k]['precipitation'];

        $wind = $res['w'.$k]['dir'];

        $sky = 2;
        if (in_array($res['p'.$k]['c'],array(1,2,3)) and ($res['p'.$k]['p']==4)) $sky = 5;
        if (in_array($res['p'.$k]['c'],array(1,2,3)) and in_array($res['p'.$k]['p'],array(5,8))) $sky = 1;
        if (in_array($res['p'.$k]['c'],array(1,2,3)) and ($res['p'.$k]['p']==6)) $sky = 3;
        if (in_array($res['p'.$k]['c'],array(1,2,3)) and ($res['p'.$k]['p']==7)) $sky = 4;
        if (($res['p'.$k]['c']==0) and ($res['p'.$k]['p']==9)) $sky = 2;
        if (($res['p'.$k]['c']==0) and ($res['p'.$k]['p']==10)) $sky = 2;
        if (($res['p'.$k]['c']==1) and ($res['p'.$k]['p']==10)) $sky = 6;
        if (in_array($res['p'.$k]['c'],array(2,3)) and ($res['p'.$k]['p']==10)) $sky = 7;

        $sky_temp = intval(($res['t'.$k]['min']+$res['t'.$k]['max'])/2);
		
		$str = file_get_contents("http://crimea-map.msk.ru/{$parse[$city]['water']}.html");
		$data = str_get_html($str);

		if($data->innertext!='' and count($data->find('div'))){
			foreach($data->find('div') as $a){
				$i++;
				
				if ($i==5){
					$text_array=explode(" ", $a->plaintext);
					$water_temp = $text_array[1];
				}
			}
		}
		//echo $data;
		$insert = [
			"city_id" => $city,
			"sky" => $sky,
			"temp" => $sky_temp,
			"water" => $water_temp,
			"date" => date("Y-m-d"),
			"time" => date("H:i:s"),
			"on_off" => '1',
			"wind" => $wind,
		];
		
}		
		
		
		echo "<pre>";
		var_dump($insert);
	}
	
	public function action_weather($routes = array()){
		$parse = [
			1483 => [ "gis" => 847, "water" => 227859 ],
			478 => [ "gis" => 845, "water" => 227857 ],
		];
		$model_weather = new model_weather();
		$model_gorod_news = new model_gorod_news();
		$settings = $model_weather->model_settings()->getItemsWhere("`status`='1'");
		echo "<pre>";
		$user_id = 14600;
		foreach($settings as $setting){
			$weather = $model_weather->getItemWhere("`on_off`='1' AND `city_id`='{$setting['city_id']}'", "*", "`id` DESC");
			if($weather){
				$new = $model_gorod_news->getItemWhere("`razd_id`='14' AND (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$weather['city_id']}')>0", "*", "`news_date` DESC");
				if($new){
					
					$topic = "/topics/news-{$weather['city_id']}-0";
					$topic = "/topics/user-14600";
					//$topic = "/topics/user-1"; // тестер
					//$topic = "/topics/user-23208"; // мама
					$link = "city24:news/{$new['id']}/base0";
					$title = $new['news_head'];
					$pv = ($weather['temp']>0?'+':'-'); $pv = ($weather['temp']==0?'':$pv);
					$pw = ($weather['water']>0?'+':'-'); $pw = ($weather['water']==0?'':$pw);
					
					$title = "Воздух: {$pv}{$weather['temp']}°C";
					if($weather['water']!==null){
						$title.=", вода: {$pw}{$weather['water']}°C";
					}
					$data = array(
						'to' => $topic,
						'data' => [
							"title" => "Погода в {$setting['city_title']}",
							"message" => $title,
							"id" => (int)(time() + rand(1,1000000)),
							"link" => $link,
						]
					);
					if(file_exists(APPDIR . "/uploads/weather/weather-{$weather['sky']}.png")){
						$data['data']['image'] = "https://gorod24.online/uploads/weather/weather-{$weather['sky']}.png";
					}
					
					$options = array(
						'http' => array(
							'header'  => "Authorization: key=AIzaSyBJzJojEluuaslC1IZ03v4nagl-xY3cmyk\r\nContent-Type: application/json\r\n",
							'method'  => 'POST',
							'content' => json_encode($data)
						)
					);
					var_dump($data);
					$context  = stream_context_create($options);
					$push_result = file_get_contents('https://gcm-http.googleapis.com/gcm/send', false, $context);
					$result = json_decode($push_result, true);
				}
			}
		}
		
		
	}
	
}
