<?php
/*

Plugin Name: crons
Plugin URI:
Description:
Version: 1.0
Author: 
Author URI:

*/

if(!class_exists('crons', false)){
	class crons{

		function __construct(){
			/* Крон розыгрыш приза */
			add_action( 'PRIZ_CRON_JOB', array($this, 'contest_priz_random') );
			schedule_event('PRIZ_CRON_JOB', 'Конкурсы. розыгрыш приза.');
			
			/* Крон Очистка просмотров новостей за сегодня */
			add_action( 'NEWS_LOOKS_DAY_CLEAR_CRON_JOB', array($this, 'news_looks_day_clear_cron_job') );
			schedule_event('NEWS_LOOKS_DAY_CLEAR_CRON_JOB', 'Крон Очистка просмотров новостей за сегодня');
			
			/* Крон Накрутка просмотров в рубрику Потребителю */
			add_action( 'NEWS_LOOKS_12_CRON_JOB', array($this, 'news_looks_12_cron_job') );
			schedule_event('NEWS_LOOKS_12_CRON_JOB', 'Крон Накрутка просмотров в рубрику Потребителю ');
			
			/* Крон Синхронизация новостей */
			add_action( 'NEWS_PARSE', array($this, 'parse_news') );
			schedule_event('NEWS_PARSE', 'Синхронизация новостей ');
			
			/* Крон Граб новостей */
			add_action( 'NEWS_GRUB', array($this, 'grub_news') );
			schedule_event('NEWS_GRUB', 'Граб новостей ');
			
			/* Крон Граб новостей */
			add_action( 'NEWS_UP', array($this, 'news_up') );
			schedule_event('NEWS_UP', 'Поднятие новостей');
			
			/* Крон Постинг в соц сети */
			add_action( 'SOCIAL_POSTING', array($this, 'social_posting') );
			schedule_event('SOCIAL_POSTING', 'Постинг в соц сети');
			
			/* Крон Создание автоматического поста в соц сети */
			add_action( 'SOCIAL_AUTO_POSTING', array($this, 'social_auto_posting') );
			schedule_event('SOCIAL_AUTO_POSTING', 'Создание автоматического поста в соц сети');
			
			/* Крон Создание автоматического поста в соц сети */
			add_action( 'WEATHER_GRUB', array($this, 'weather') );
			schedule_event('WEATHER_GRUB', 'Граб погоды');
			
			/* Крон Создание автоматического поста в соц сети */
			add_action( 'WEATHER_PUSH', array($this, 'send_push_weather') );
			schedule_event('WEATHER_PUSH', 'Отправка пуша погоды');
		}

		function contest_priz_random($event){
			//echo __FILE__;
			//echo date('Y-m-d H:i:s');
			$model_contests = new model_contests();
			$model_prizes = new model_prizes();
			$model_apiapps = new model_apiapps();
			$model_nb = new model_nb();
			$model_devicelog = $model_apiapps->_model_devicelog();
			
			$prizes = $model_prizes->getItemsWhere(" (`owner` IS NULL OR `owner`=0 ) AND `unix_end`<='".time()."'");
			
			if(!empty($prizes)){
			foreach($prizes as $prize){
				if($prize['user_base']=='`gorod24.online`.`gorod_devicelog`'){
					$contest = $model_contests->getItem($prize['contest_id']);
					$contest_cities = explode(';', $contest['cities']);
					$p = []; $group_id = $prize['group_id'];
					foreach($contest_cities as $i=>$item){ if($item!=''){ $p[] = $item; } }
					$winner = $model_devicelog->get("uid")->where("`uid`!=1 AND `uid` IS NOT NULL AND '{$contest['start_date']}'<=`date` AND `date`<='{$contest['end_date']}' AND `uid` NOT IN (SELECT `owner` FROM `{$model_prizes->getdatabasename()}`.`{$model_prizes->gettablename()}` WHERE `owner` IS NOT NULL AND `group_id`='{$group_id}') AND `city_id` IN (".implode(',',$p).")")->group('uid')->order('RAND()')->limit(1)->commit('one');
					if(!empty($winner)){
						
						$model_prizes->Update([
							'owner' => $winner
						], $prize['id']);
						
					}
					
				}
				elseif($prize['user_base']=='`thebest`.`contests`'){
					$contest = $model_contests->getItem($prize['contest_id']);
					$contest_cities = explode(';', $contest['cities']);
					$p = []; $group_id = $prize['group_id'];
					foreach($contest_cities as $i=>$item){ if($item!=''){ $p[] = $item; } }
					$nb_contests = $model_nb->getItemsWhere("`con_status`='1' AND con_city IN (".implode(",",$p).")  AND con_start_date<=CURDATE() AND CURDATE()<=con_end_date");
				$con_query = [];
				foreach($nb_contests as $nb_contest) {
					$con_query[] = "
					SELECT `feo_uid` FROM `thebest`.`{$nb_contest['con_table_prefix']}user{$nb_contest['con_table_sufix']}` as `user` WHERE 1 
					AND `created`>='{$contest['start_date']}' 
					AND `created`<='{$contest['end_date']}' 
					AND (SELECT COUNT(*) FROM `thebest`.`{$nb_contest['con_table_prefix']}votes{$nb_contest['con_table_sufix']}` as `votes` WHERE `votes`.`id_user`=`user`.`id_user`)>0
					";
				}
				$old_winners = $model_prizes->get("owner")->where("`owner` IS NOT NULL AND `group_id`='{$group_id}'")->commit('col');
				
				$query = "	
				select `feo_uid` FROM (".implode(" UNION ", $con_query).") as `t1` 
				WHERE  1 ".((!empty($old_winners))?" AND `feo_uid` NOT IN ( ".implode(',', $old_winners)." )":"")."
				GROUP BY `feo_uid`
				ORDER BY RAND()
				LIMIT 1
				";
					
					$row = $model_nb->db()->getRow($query);
					$winner = $row['feo_uid'];
					if(!empty($winner)){
						$model_prizes->Update([
							'owner' => $winner
						], $prize['id']);
						
					}				
				}
				
				
			}}
		}
		
		function news_looks_day_clear_cron_job($event){
			$model_gorod_news = new model_gorod_news();
			$model_gorod_news->_model_news_look_day()->db()->query("DELETE FROM `{$model_gorod_news->_model_news_look_day()->getdatabasename()}`.`{$model_gorod_news->_model_news_look_day()->gettablename()}` WHERE 1");
		}
		
		function news_looks_12_cron_job($event){
			$model_gorod_news = new model_gorod_news();
			
			$model_gorod_news->db()->query("UPDATE `{$model_gorod_news->getdatabasename()}`.`{$model_gorod_news->gettablename()}` SET `looks`=(`looks` + 7) WHERE `razd_id` = 12 AND `on_off`='1'  and `news_date`>=(NOW() - INTERVAL 15 HOUR) AND `look` < '500' ORDER BY `news_date` DESC");
			$model_gorod_news->db()->query("UPDATE `{$model_gorod_news->getdatabasename()}`.`{$model_gorod_news->gettablename()}` SET `looks`=(`looks` + 3) WHERE `razd_id` = 12 AND `on_off`='1'  and `news_date`>=(NOW() - INTERVAL 15 HOUR) AND `look` > '500' AND `look` < '1200' ORDER BY `news_date` DESC");
			$model_gorod_news->db()->query("UPDATE `{$model_gorod_news->getdatabasename()}`.`{$model_gorod_news->gettablename()}` SET `looks`=(`looks` + 2) WHERE `razd_id` = 12 AND `on_off`='1'  and `news_date`>=(NOW() - INTERVAL 15 HOUR) AND `look` > '1200' ORDER BY `news_date` DESC");
		
		}
		
		function parse_news($event){
		echo 'parse_news';
		set_time_limit(3600);
		$page = $_GET['p'];
		if(empty($_GET['p'])) $page = 1;
		
		$limit = 10;
		$start = ($limit*$page) - $limit;
		$model_news = new model_news();
		$news = $model_news->our()->getItemsWhere("1", "news_id DESC", $start, $limit); $img_pref = 'onf';
		//$news = $model_news->kafa()->getItemsWhere("1", null, $start, $limit); $img_pref = 'knf';
		$model_gorod_news = new model_gorod_news();
		$model_countries = new model_countries();
		$model_regions = new model_regions();
		$model_uploads = new model_uploads();
		$model_old_photos = $model_gorod_news->model_photos();
		$model_gorod_photos = $model_gorod_news->model_gorod_photos();
		if(!empty($news)){
			foreach($news as $i=>$new){
			$ch = $model_gorod_news->getItemWhere("`news_id`='{$new['news_id']}' AND `our`='{$new['our']}'");
			if(empty($ch)){
				/**/
				$id = $model_gorod_news->Insert([
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
					//'news_date' => $new['news_date'],
					//'news_up' => $new['news_date'],
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
					 //'on_off' => $new['on_off'],
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
				if(!empty($new['news_razd'])){ $razd_id =(int)$model_gorod_news->model_razd()->get('id')->where("name_razd='{$new['news_razd']}'")->limit(1)->commit('one'); }
				if(!empty($new['town'])){ $city_id =(int)$model_gorod_news->model_cities()->get('city_id')->where("city_title='{$new['town']}'")->limit(1)->commit('one'); }
				$model_gorod_news->Update([
					'news_body' => $new['news_body'],
					'razd_id'=>$razd_id, 
					'country_id' => 1,
					'region_id' => 1500001,
					'city_id' => $city_id,
					//'on_off' => $new['on_off'],
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
			
			//$this->_model_gorod_news->model_news_cities()->Delete("`new_id`='{$id}'");
			//$this->_model_gorod_news->model_news_cities()->Insert(['new_id'=>$id, 'city_id'=>$city_id, 'country_id'=>1, 'region_id'=>1500001, 'add_date'=>date("Y-m-d H:i:s")]);
			
			$audio = $model_gorod_news->_model_news_audio_streams()->getItemWhere("`news_id`='{$new['news_id']}' AND `our`='{$new['our']}'");{
				if($audio and $audio['new_id']==0){
					$file = $audio['file'];
					$src = explode('/', $file);
					$name = end($src);
					if(!file_exists(APPDIR . "/uploads/audio/news/".$name)){
						$stream = file_get_contents("https://feo.ua".$file);
						if(file_put_contents(APPDIR . "/uploads/audio/news/".$name, $stream)){
							$model_gorod_news->_model_news_audio_streams()->Update([
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
		}
		}
		
		public function grub_news($array = array()){
			header("Content-type: text/plain; charset=UTF-8");
			$model_news_grab = new model_news_grab();
			$model_news_grab_photos = new model_news_grab_photos();
			$model_news_grab_settings = new model_news_grab_settings();
			
			$sites = $model_news_grab_settings->getItemsWhere("`status`=1");
			foreach($sites as $site) {
			echo "<h1>{$site['name']}:</h1>";
			$url = $site['domain'];
			$news_page = $site['news'];
			$source_name = $site['name'];
			$city_id = $site['city_id'];
			$protocol = explode(":", $url);
			$protocol = $protocol[0];
			
			$news = $site['container'];
			$news_head = $site['container_head'];
			$news_link = $site['container_link'];
			$head_source = $site['head'];
			$body_source = $site['body'];
			$images_source = $site['photos'];
			$images_type = $site['photos_type'];
			$headers = $site['headers'];
			
			$context = stream_context_create(array('https' => array('header' => ">User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0);\r\nCookie: beget=begetok; Cookie: path=/\r\n")));
			//$response = file_get_contents($url, false, $context);
			//echo $response;
			
			$response = $this->getContent($url.$news_page); # This returns HTML 
			
			$html = str_get_html($response);
			
			$items = $html->find($news);
			foreach($items as $element) {
				if($news_head){ $head = $element->find($news_head)[0]->plaintext; } else { $head = $element->plaintext; } 
				if($news_link){$source = $element->find($news_link)[0]->href; } else { $source = $element->href; }
			
				$ch = $model_news_grab->getCountWhere("`head`='{$head}'");
				if(!$ch){
					if(substr($source, 0, 2)=='//'){ $source = $protocol.':'.$source;}
					elseif(substr($source, 0, strlen($protocol))!=$protocol){ $source = $url.$source;}
					//$response_new = file_get_contents($source, false, $context);
					$response_new = $this->getContent($source);
					$html_new = str_get_html($response_new);
					
					if($html_new){
						
					$head2 = $html_new->find($head_source)[0]->plaintext;
					$body = $html_new->find($body_source)[0]->innertext;
					$images = $html_new->find($images_source);
					
					$body_new = str_get_html($body);
					foreach($body_new->find('img') as $node){
							$node->outertext = '';
					}
					foreach($body_new->find('script') as $node){
							$node->outertext = '';
					}
					foreach($body_new->find('style') as $node){
							$node->outertext = '';
					}
					$body_new->save();
					$body = $body_new;
					
					$new = [
						'head' => ($head2?$head2:$head),
						'body' => ($body?$body:''),
						'city_id' => $city_id,
						'status' => 1,
						'date' => date('Y-m-d H:i:s'),
						'domain' => $url,
						'source' => $source,
						'source_name' => $source_name,
						'confirm_id' => 0,
					];
					//echo "<h3>{$source_name}: <a href=\"{$source}\" target=\"_blank\">{$head}</a></h3>";
					//continue;
					$ch = $model_news_grab->getCountWhere("`head`='{$new['head']}'");
					if(!$ch){
					$id = $model_news_grab->Insert($new);
					
					if(!empty($images)){
						foreach($images as $i=>$image){
							if($images_type==0){
								$image_src = $image->src;
							}
							elseif($images_type==1){
								$image_src = $image->href;
							}
							
							if(substr($image_src, 0, 2)=='//'){ $image_src = $protocol.':'.$image_src;}
							elseif(substr($image_src, 0, strlen($protocol))!=$protocol){ $image_src = $url.$image_src;}
							$ext = getExtension1($image_src);
							$name = "{$id}-{$i}.{$ext}";
							if(substr($image_src, 0, strlen($protocol))!=$protocol){ $image_src = $protocol.':'.$image_src;}
							$image_data = file_get_contents($image_src, false, $context);
							if($image_data){
								file_put_contents(APPDIR . '/uploads/image/news_grub/'.$name, $image_data);
								$model_news_grab_photos->Insert([
									'new_id' => $id,
									'photo' => '/uploads/image/news_grub/'.$name,
									'photo_id' => 0,
								]);
							}
						}
					}
					}
				}
				}
			}
			
			}
			
		}
		
		function getRandom($min=0, $max=10){
			return rand($min, $max);
			return (int)trim(file_get_contents("https://www.random.org/integers/?num=1&min={$min}&max={$max}&col=1&base=10&format=plain&rnd=new"));
		}
		
		public function getContent($url){
			$ch = curl_init(); 
			curl_setopt ($ch, CURLOPT_URL, $url); 
			curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
			curl_setopt ($ch, CURLOPT_COOKIE, "beget=begetok; path=/");​;
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			$store = curl_exec ($ch); 
			$response = curl_exec ($ch); # This returns HTML 
			curl_close ($ch);  
			return $response;
		}
		
		public function news_up($event){
			$model_gorod_news = new model_gorod_news();
			$model_news_time_up = $model_gorod_news->model_news_time_up();
			$model_social = new model_social();
			$ups = $model_news_time_up->getItemsWhere("`date` <=  CURDATE() AND  `time` <  CURTIME() AND  (`do`=0 OR `do` IS NULL)");
			foreach($ups as $up){
				if($up['new_id']){
					$new = $model_gorod_news->getItem($up['new_id']);
					if($new){
						$new_date_news=$up['date']." ".$up['time'];
						$data = [
								'news_date' => $new_date_news,
								'news_up' => $new_date_news,
								'vk_' => 0,
								'vk_feo' => 0,
								'vk_feorf' => 0,
								'vk_feorf' => 0,
								'on_off' => 1,
							];
						$model_gorod_news->Update($data, $up['new_id']);
						$model_social->Update(['upped'=>1], "`new_id`='{$up['new_id']}'");
						echo "Новость {$new['news_head']} - поднята на {$new_date_news}<br>";
						$model_news_time_up->Update(['last_date'=>$new['news_date'], 'do'=>1], $up['id']);
					}
				}
			}
			
		}
		
		public function social_posting($event){
			$model_social = new model_social();
			$publishs = $model_social->getItemsWhere("`is_sended`=0 and `send_date`<=NOW()");
			foreach($publishs as $publish){
				$publisher = $model_social->model_accounts()->getItem($publish['account_id_from']);
				
				switch($publisher['social_type']){
					case 'vk': self::publishToVK($publish, $publisher); break;
					case 'Facebook': self::publishToFacebook($publish, $publisher); break;
					case 'Odnoklassniki': self::publishToOdnoklassniki($publish, $publisher); break;
				}
				
			}
		}
		
		public function social_auto_posting($event){
			$model_social = new model_social();
			$model_gorod_news = new model_gorod_news();
			$h = date("H");
			if( $h<8 or $h>20 ){ return false; }
			$tasks = $model_social->model_auto_posting()->getItemsWhere("`status`=1 AND `next_launch`<=NOW()");
			foreach($tasks as $task){
				if($task['end_date'] >= date('Y-m-d H:i:s') or $task['end_date'] == '0000-00-00 00:00:00'){
					$account = $model_social->model_accounts()->getItem($task['acount_id']);
					$cities = json_decode($task['news_cities']);
					$razds = json_decode($task['news_razds']);
					$tags = json_decode($task['news_tags']);
					$where = '';
					if(!empty($cities)) $where .= " AND (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` IN (".implode(', ', $cities)."))>0 ";
					if(!empty($razds)) $where .= " AND `razd_id` IN (".implode(', ', $razds).") ";
					if(!empty($tags)){ $where .= " AND (`news_tag` LIKE '%;".implode(";%' OR `news_tag` LIKE '%;", $tags).";%')";}
						
						if($task['news_interval']) { $interval = "( now() - INTERVAL {$task['news_interval']} DAY )";} else {  $interval = "now()"; }
						
						$new = $model_gorod_news->getItemWhere(" 1
							{$where}
							AND `id` NOT IN (SELECT `new_id` FROM `gorod_social_publisher` WHERE `upped`=0 AND `auto_id`='{$task['id']}')
							AND `on_off`='1' 
							AND `news_date` >= {$interval}
						", '*', "`news_date` ASC");
						
						if($new){
							$text = '';
							if($new['city_id']=='1483'){
								$text .=" #Новости_Феодосии";
							}
							elseif($new['city_id']=='478'){
								$text.=" #Новости_Керчи";
							}
							$text .=" #{$new['news_razd']}";
							if ($new['news_video']=='1'){
								$text .=" #Видео_репортаж";
							}
							
							file_get_contents("https://gorod24.online/thrumbs/news/new_{$new['id']}_640_320.jpg");
							
							$post = [
								'message_id' => 0,
								'account_id_from' => $account['id'],
								'social_id_to' => $account['social_id'],
								'social_id_type' => $account['social_id_type'],
								'post_type' => $task['post_type'],
								'group_id' => $task['group_id'],
								'album_id' => $task['album_id'],
								'photo' => "https://gorod24.online/uploads/image/news_thrumbs/{$new['id']}/new_{$new['id']}_640_320.jpg",
								'text' => $text,
								'new_id' => $new['id'],
								'auto_id' => $task['id'],
								'send_date' => date('Y-m-d H:i:s'),
								'date' => '0000-00-00 00:00:00',
								'is_sended' => '0',
								'error_code' => '0',
								'error_message' => '',
							];
							$url_ru = urlencode($new['url_ru']);
							switch($task['url']){
								case 'url': $post['link'] = "https://feo.ua/news/{$new['url']}"; break;
								case 'url_ru': $post['link'] = "https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/".($url_ru); break;
								
							}
							
							$description = str_replace('%link%', $post['link'], $task['description']);
							foreach($new as $key => $value){ 
								$value = str_replace([ "<br>", "</br>", "</p>", "</div>","</h1>", ], "\r\n", $value);
								$value = strip_tags($value);
								$description = str_replace("%{$key}%", $value, $description); 
							}
							
							$text .= "\r\n".$description;
							$post['text'] = $text;
							
							$model_social->Insert($post);
							$data = [
								'last_launch' => date('Y-m-d H:i:s'),
								'next_launch' => date('Y-m-d H:i:s', time()+$task['round_period']),
								'sends' => $task['sends'] + 1,
							];
							$model_social->model_auto_posting()->Update($data, $task['id']);
						}
						else {
							echo "Нет новостей";
						}
				}
				elseif($task['end_date']<date('Y-m-d H:i:s') and $task['end_date'] != '0000-00-00 00:00:00'){
					$data = [
						'status' => 0,
					];
					$model_social->model_auto_posting()->Update($data, $task['id']);
				}
				
				
				
			}
		}
		
		private function publishToVK($publish, $publisher){
			$model_social = new model_social();
			$model_gorod_news = new model_gorod_news();
			if($publisher['social_id_type']>0)$pref = '-';
			$vk_work = new vk( $publisher['access_token'], 1, $publisher['app_id'], $pref.$publish['social_id_to'] );
			
			switch($publish['post_type']){
				case 0: 
					$oResponce = $vk_work->post( $publish['text'], "", $publish['link'] );
					break;
				case 1: 
					//file_get_contents("https://gorod24.online/thrumbs/news/new_{$publish['new_id']}_640_320.jpg");
					$publish['photo'] = str_replace("https://gorod24.online",APPDIR, $publish['photo']);
					$photo = $vk_work->upload_photo($publish['photo'], $publish['album_id'],  $text, $publish['group_id']);
					$atach="photo".$photo->owner_id."_".$photo->id;
					$oResponce = $vk_work->post_wall( $pref.$publish['social_id_to'], '1', $publish['text'], $atach );
					break;
			}
			
			
			if(is_int($oResponce)){
				$model_social->model_accounts()->Update(['sends'=>($publisher['sends']+1)], $publisher['id']);
				$data = [
					'id' => $publish['id'],
					'message_id' => $oResponce,
					'access_token' => $publisher['access_token'],
					'is_sended' => 1,
					'date' => date('Y-m-d H:i:s'),
				];
				$model_social->InsertUpdate($data);
				$model_social->Update(['upped'=>0], "`new_id`='{$publish['new_id']}' AND `auto_id`='{$publish['auto_id']}'");
			}
			else {
				$data = [
					'id' => $publish['id'],
					'error_code' => $oResponce->error_code,
					'error_message' => $oResponce->error_msg,
				];
				$model_social->InsertUpdate($data);
			}
		}
		
		private function publishToFacebook($publish, $publisher){
			require_once APPDIR . '/application/core/Facebook/autoload.php';
			$model_social = new model_social();
			$model_gorod_news = new model_gorod_news();
			$app_id = $publisher['app_id']; // ид приложения. берешь в настройках приложения (или копируешь с адресной строки)
			$app_secret = $publisher['app_secret']; // ключ приложения. берешь в настройках приложения
			$access_token = $publisher['access_token']; // токен, который мы получили
			$page_id = $publisher['social_id']; // id группы
			
			$fb = new Facebook\Facebook(array(
				'app_id' => $app_id,
				'app_secret' => $app_secret,
				'default_graph_version' => 'v2.2',
			));
			$fb->setDefaultAccessToken($access_token);
			$link = $publish['link'];
			$text = $publish['text'];
			//return true;
			$photo = $model_gorod_news->model_gorod_photos()->getItemWhere("`new_id`='{$publish['new_id']}'", '*', "`pos`");
			$publish['photo'] = str_replace("https://gorod24.online",APPDIR, $publish['photo']);
			switch($publish['post_type']){
				case 0:
					$data = [
						'link' => $link,
						'message' => $text,
					];
					$page = 'feed';
				break;
				case 1:
					$data = [
						'link' => $link,
						'message' => $text,
						'source' => $fb->fileToUpload($publish['photo']),
					];
					$page = 'photos';
				break;
			}
			try {
				$response = $fb->post("/{$page_id}/{$page}", $data);
			} 
			catch(Facebook\Exceptions\FacebookResponseException $e) {	  echo 'Graph returned an error: ' . $e->getMessage(); return false;		} 
			catch(Facebook\Exceptions\FacebookSDKException $e) {		  echo 'Facebook SDK returned an error: ' . $e->getMessage(); return false;		}
			
			$graphNode = $response->getGraphNode();
			/*
			echo 'Photo ID: ' . $graphNode['id'];
			
			#РЕПОСТ
			$str_page = '/100001269554597/feed';
			$feed = array('message' => $text, 'link'=>'https://www.facebook.com/photo.php?fbid='.$graphNode['id']);
			
			try {
				$response = $fb->post($str_page, $feed, $access_token);
			}
			catch (Facebook\Exceptions\FacebookResponseException $e) { echo 'Graph вернул ошибку: ' . $e->getMessage(); return false; }
			catch (Facebook\Exceptions\FacebookSDKException $e) { echo 'Facebook SDK вернул ошибку: ' . $e->getMessage(); return false; }
							
			$graphNode1 = $response->getGraphNode();
			echo '<BR>REPOST, id: ' . $graphNode1['id'];
			$date=$graphNode['id'].'--'.$graphNode1['id'];
			*/
			if($graphNode['id']){
				$model_social->model_accounts()->Update(['sends'=>($publisher['sends']+1)], $publisher['id']);
				$data = [
					'id' => $publish['id'],
					'message_id' => $graphNode['id'],
					'access_token' => $publisher['access_token'],
					'is_sended' => 1,
					'date' => date('Y-m-d H:i:s'),
				];
				$model_social->InsertUpdate($data);
				$model_social->Update(['upped'=>0], "`new_id`='{$publish['new_id']}' AND `auto_id`='{$publish['auto_id']}'");
			}
		}
		
		private function publishToOdnoklassniki($publish, $publisher){
			$model_social = new model_social();
			$model_gorod_news = new model_gorod_news();
			
			$ok_access_token = $publisher['access_token']; //Наш вечный токен
			$ok_private_key = $publisher['app_secret']; //Секретный ключ приложения
			$ok_public_key = $publisher['app_public']; //Публичный ключ приложения
			
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
			print_r ($step1);
			// Если ошибка
			if (isset($step1['error_code'])) {
				// Обработка ошибки
				echo "step1 [{$step1['error_code']}]";
				//var_dump($step1);
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
				//var_dump($step2);
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
				//var_dump($step3);
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
				//var_dump($step4);
				return false;
			}
			
			//if(is_int($oResponce)){
				$model_social->model_accounts()->Update(['sends'=>($publisher['sends']+1)], $publisher['id']);
				$data = [
					'id' => $publish['id'],
					'message_id' => $oResponce,
					'access_token' => $publisher['access_token'],
					'is_sended' => 1,
					'date' => date('Y-m-d H:i:s'),
				];
				$model_social->InsertUpdate($data);
				$model_social->Update(['upped'=>0], "`new_id`='{$publish['new_id']}' AND `auto_id`='{$publish['auto_id']}'");
			/*}
			else {
				$data = [
					'id' => $publish['id'],
					'error_code' => $oResponce->error_code,
					'error_message' => $oResponce->error_msg,
				];
				$model_social->InsertUpdate($data);
			}
			*/
			
		}
	
		public function weather($event){
			$model_weather = new model_weather();
			$settings = $model_weather->model_settings()->getItemsWhere("`status`='1'");
			foreach($settings as $setting){
				$this->saveWeather($setting['city_id'], $setting['gis_city_index'], $setting['water_city_index']);
			}
		}
	
		function saveWeather($city, $gis, $water){
			$gispars = new GisPars($gis);
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
					
					if($water){
						$str = file_get_contents("http://crimea-map.msk.ru/{$water}.html");
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
					}
					else {
						$water_temp = null;
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
			
			IF($insert){
				$model_weather = new model_weather();
				$model_weather->Insert($insert);
			}

		}
		
		public function send_push_weather($event){
			$model_weather = new model_weather();
			$model_gorod_news = new model_gorod_news();
			$settings = $model_weather->model_settings()->getItemsWhere("`status`='1'");
		
			$hour = (int)date("H");
			$weak_day = date("N");
			// по будням в 8 часов;
			// в субботу в 9 часов;
			// в воскресенье в 10 часов;
			if( (in_array($weak_day, [1,2,3,4,5]) and $hour==8) OR ($weak_day==6 AND $hour==9) OR ($weak_day==7 AND $hour==10) ){
				foreach($settings as $setting){
					$weather = $model_weather->getItemWhere("`on_off`='1' AND `city_id`='{$setting['city_id']}'", "*", "`id` DESC");
					if($weather){
						$new = $model_gorod_news->getItemWhere("`razd_id`='14' AND (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$weather['city_id']}')>0", "*", "`news_date` DESC");
						if($new){
							
							$topic = "/topics/news-{$weather['city_id']}-0";
							//$topic = "/topics/user-14600";
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
		
	}
	
	global $crons;
	$crons = new crons();
}