<?php
class model_gorod_news extends Model
{
	
	protected $monthes;
	
	protected $model_news_cities; 		public function model_news_cities() 	{		return $this->model_news_cities;	}
	protected $model_razd; 		public function model_razd() 	{		return $this->model_razd;	}
	protected $model_cities; 	public function model_cities() 	{		return $this->model_cities;	}
	protected $model_photos; 	public function model_photos() 	{		return $this->model_photos;	}
	protected $model_gorod_photos; 	public function model_gorod_photos() 	{		return $this->model_gorod_photos;	}
	protected $model_gorod_news_rotate; 	public function model_gorod_news_rotate() 	{		return $this->model_gorod_news_rotate;	}
	protected $model_news_time_up; 	public function model_news_time_up() 	{		return $this->model_news_time_up;	}
	
	protected $like_pages;		public function like_pages() 	{		return $this->like_pages;	}
	protected $like_rates;		public function like_rates() 	{		return $this->like_rates;	}
	protected $userNews;		public function userNews() 		{		return $this->userNews;		}
	
	protected $_model_news_look;	public function _model_news_look() { return $this->_model_news_look; }
	protected $_model_news_look_day;	public function _model_news_look_day() { return $this->_model_news_look_day; }
	protected $_model_news_audio_streams;	public function _model_news_audio_streams() { return $this->_model_news_audio_streams; }
	protected $_model_news_in_gazeta;	public function model_news_in_gazeta() { return $this->_model_news_in_gazeta; }
	
	protected $_model_new_authors;	public function _model_new_authors() { return $this->_model_new_authors; }
	protected $_model_news_teg;	public function _model_news_teg() { return $this->_model_news_teg; }
	
	function __construct($config = array()) {
		$config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "news",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'news_id' => "INT(11) NULL DEFAULT NULL",
				'news_head' => "TEXT NOT NULL",
				'news_lid' => "TEXT NOT NULL",
				'news_body' => "TEXT NOT NULL",
				'news_vrez' => "TEXT NOT NULL",
				'news_author' => "INT(11) NULL DEFAULT NULL",
				'news_video' => "TEXT NOT NULL",
				'news_video_you' => "VARCHAR(255) NOT NULL",
				'news_foto' => "TEXT NOT NULL",
				'news_foto_sm' => "TEXT NOT NULL",
				'big_open_foto' => "INT(1) NOT NULL",
				'news_foto_reportag' => "INT(1) NOT NULL DEFAULT '0'",
				'foto_all' => "INT(1) NOT NULL DEFAULT '1'",
				'news_podp' => "TEXT NOT NULL",
				'news_num' => "TEXT NOT NULL",
				'news_razd' => "TEXT NOT NULL",
				'razd_id' => "INT(11) NOT NULL",
				'news_kto' => "TEXT NOT NULL",
				'news_tag' => "VARCHAR(255) NOT NULL",
				'town' => "TEXT NOT NULL",
				'country_id' => "INT(11) NOT NULL",
				'region_id' => "INT(11) NOT NULL",
				'city_id' => "INT(11) NOT NULL",
				'news_key' => "TEXT NOT NULL",
				'news_des' => "TEXT NOT NULL",
				'look' => "TEXT NOT NULL",
				'news_date' => "DATETIME NOT NULL",
				'news_up' => "DATETIME NOT NULL",
				'our' => "int(11) NOT NULL DEFAULT '1'",
				'type' => "INT(11) NOT NULL DEFAULT '0'",
				'lock_' => "INT(11) NOT NULL DEFAULT '0'",
				'looks' => "INT(11) NOT NULL",
				'vk_' => "INT(11) NOT NULL",
				'vk_feo' => "INT(11) NOT NULL",
				'vk_feorf' => "INT(11) NOT NULL",
				'vk_g' => "VARCHAR(200) NOT NULL",
				'fb' => "VARCHAR(255) NOT NULL",
				'ok' => "INT(11) NULL DEFAULT NULL",
				'ot_name' => "VARCHAR(255) NOT NULL",
				'ot_sylka' => "VARCHAR(255) NOT NULL",
				'url' => "TEXT NOT NULL",
				'url_ru' => "TEXT NOT NULL",
				'kay_word' => "TEXT NOT NULL",
				'id_pr' => "VARCHAR(11) NOT NULL",
				'app_id' => "INT(11) NOT NULL",
				'akciya_id' => "INT(11) NOT NULL",
				'on_off' => "INT(2) NOT NULL",
				'news_lock' => "INT(11) NOT NULL",
				'news_lock_for' => "DATETIME NOT NULL",
				'show_comment' => "INT(1) NOT NULL DEFAULT '1'",
				'news_inter_id' => "INT(11) NULL DEFAULT NULL",
				'news_album_id' => "INT(11) NULL DEFAULT NULL",
				'news_zamer_id' => "INT(11) NULL DEFAULT NULL",
				'news_panorama' => "INT(11) NULL DEFAULT NULL",
				'news_panorama_type' => "INT(11) NOT NULL DEFAULT '0'",
				'show_in_app' => "INT(11) NOT NULL DEFAULT '1'",
				'news_rating' => "FLOAT NULL DEFAULT NULL",
				'nead_stream' => "INT(11) NOT NULL DEFAULT '1'",
				'watermark' => "VARCHAR(255) NULL DEFAULT NULL",
				'watermark_big' => "VARCHAR(50) NULL DEFAULT NULL",
				'18plus' => "INT(11) NOT NULL DEFAULT 0",
				'gazeta_text' => "TEXT NULL DEFAULT NULL",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
				'news_head' => array( 'news_head', 'news_lid', 'news_body' ),
				'url_ru' => array( 'url_ru' ),
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		
		parent::__construct($config);
		
		$news_cities_config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "news_cities",
            "engine" => "InnoDB",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'new_id' => "INT(11) NOT NULL",
				'country_id' => "INT(11) NOT NULL",
				'region_id' => "INT(11) NOT NULL",
				'city_id' => "INT(11) NOT NULL",
				'add_date' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->model_news_cities = new Model($news_cities_config);
		
		$razd_config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "news_razd",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name_razd' => "VARCHAR(20) NOT NULL",
				'url_ru' => "TEXT NOT NULL",
				'url' => "TEXT NOT NULL",
				'url_kafa' => "TEXT NOT NULL",
				'looks' => "INT(11) NOT NULL",
				'in_push' => "INT(11) NOT NULL DEFAULT 0",
				'on_off' => "INT(1) NOT NULL DEFAULT 1",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->model_razd = new Model($razd_config);
		
		$photos_config = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "news_photo",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'n_id' => "INT(11) NOT NULL",
				'our' => "INT(11) NOT NULL",
				'foto' => "VARCHAR(254) NOT NULL",
				'ext' => "VARCHAR(10) NOT NULL",
				'discription' => "TEXT NOT NULL",
				'title' => "VARCHAR(255) NOT NULL",
				'pos' => "INT(11) NOT NULL",
				'on_off' => "BIT(1) NOT NULL DEFAULT '1'",
				'descr_on' => "INT(11) NOT NULL DEFAULT '0'",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->model_photos = new Model($photos_config);
		
		$gorod_photos_config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "news_photos",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'new_id' => "INT(11) NOT NULL",
				'img' => "VARCHAR(254) NOT NULL",
				'img_id' => "INT(11) NOT NULL",
				'description' => "TEXT NOT NULL",
				'title' => "VARCHAR(255) NOT NULL",
				'main' => "INT(11) NOT NULL DEFAULT '0'",
				'pos' => "INT(11) NOT NULL",
				'status' => "INT(11) NOT NULL DEFAULT '1'",
				'descr_on' => "INT(11) NOT NULL DEFAULT '0'",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->model_gorod_photos = new Model($gorod_photos_config);
		
		$model_gorod_news_rotate_config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "news_rotate",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'time' => "TIME NOT NULL DEFAULT '00:00:00'",
				'new_id' => "INT(11) NOT NULL",
				'n_id' => "INT(11) NOT NULL",
				'n_o' => "INT(11) NOT NULL",
				'date' => "DATE NOT NULL DEFAULT '0000-00-00'",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->model_gorod_news_rotate = new Model($model_gorod_news_rotate_config);
		
		$this->model_cities = new model_cities();
		
		$like_pages_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "like_pages",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'url' => "TEXT NOT NULL",
				'md5_url' => "VARCHAR(32) NOT NULL",
				'count' => "INT(11) NOT NULL",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->like_pages = new Model($like_pages_config);
		
		$like_rates_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "like_rates",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'cid' => "INT(11) NOT NULL",
				'uid' => "INT(11) NOT NULL",
				'val' => "INT(11) NOT NULL",
				'date' => "DATE NOT NULL",
				'time' => "TIME NOT NULL",
				'ut' => "INT(11) NOT NULL",
				'ip' => "VARCHAR(32) NOT NULL",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->like_rates = new Model($like_rates_config);
		
		$user_news_config = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "news_users",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'user_id' => "INT(11) NOT NULL",
				'name' => "TEXT NOT NULL",
				'text' => "TEXT NOT NULL",
				'tags' => "TEXT NOT NULL",
				'photos' => "TEXT NOT NULL",
				'date' => "DATETIME NOT NULL",
				'status' => "INT(11) NOT NULL",
				'ip' => "VARCHAR(32) NOT NULL",
				'latitude' => "VARCHAR(50) NOT NULL DEFAULT '0'",
				'longitude' => "VARCHAR(50) NOT NULL DEFAULT '0'",
				'news_id' => "INT(11) NULL DEFAULT NULL",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->userNews = new Model($user_news_config);
		
		$_model_news_look = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "news_look",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'new_id' => "INT(11) NOT NULL DEFAULT '0'",
				'n_id' => "INT(11) NOT NULL",
				'our' => "INT(11) NOT NULL",
				'date' => "date NOT NULL",
				'time' => "time NOT NULL",
				'ip	' => "TEXT NOT NULL",
				'cols' => "TEXT NOT NULL",
				'look_from' => "int(11) NOT NULL DEFAULT 0",
				'uid' => "INT(11) NULL DEFAULT NULL",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->_model_news_look = new Model($_model_news_look);
		
		$_model_news_look_day = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "news_look_day",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'new_id' => "INT(11) NOT NULL DEFAULT '0'",
				'n_id' => "INT(11) NOT NULL",
				'our' => "INT(11) NOT NULL",
				'date' => "date NOT NULL",
				'time' => "time NOT NULL",
				'ip	' => "TEXT NOT NULL",
				'cols' => "TEXT NOT NULL",
				'look_from' => "int(11) NOT NULL DEFAULT 0",
				'uid' => "INT(11) NULL DEFAULT NULL",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->_model_news_look_day = new Model($_model_news_look_day);
		
		$config_model_news_audio_streams = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "news_audio_streams",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "varchar(255) NOT NULL",
				'descr' => "TEXT NOT NULL",
				'file' => "varchar(255) NOT NULL",
				'audio' => "varchar(255) NOT NULL",
				'new_id' => "INT(11) NOT NULL",
				'news_id' => "INT(11) NOT NULL",
				'our' => "INT(11) NOT NULL",
				'adddate' => "date NOT NULL",
				'status' => "INT(11) NULL DEFAULT NULL",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->_model_news_audio_streams = new Model($config_model_news_audio_streams);
		
		$config_model_news_in_gazeta = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "news_in_gazeta",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'new_id' => "INT(11) NOT NULL",
				'gazeta_id' => "INT(11) NOT NULL",
				'num_id' => "varchar(255) NOT NULL",
				'num' => "varchar(50) NOT NULL",
				'date' => "date NOT NULL",
				'adddate' => "datetime NOT NULL",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->_model_news_in_gazeta = new Model($config_model_news_in_gazeta);
		
		$config_model_new_authors = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "new_authors",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "author_id",
			"autoinit"  => false,
            "columns" => array(
				'author_name' => "varchar(50) NOT NULL",
				'on_off' => "INT(11) NOT NULL",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->_model_new_authors = new Model($config_model_new_authors);
		
		$config_model_news_teg = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "news_teg",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'tag' => "varchar(255) NOT NULL",
				'looks' => "INT(11) NOT NULL",
				),
			"index" => array(
			),
			"unique" => array(
				'tag' => array('tag'),
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->_model_news_teg = new Model($config_model_news_teg);
		
		$config_model_news_time_up = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "news_time_up",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'new_id' => "INT(11) NOT NULL DEFAULT 0",
				'id_news' => "INT(11) NOT NULL DEFAULT 0",
				'our' => "INT(11) NOT NULL DEFAULT 0",
				'date' => "DATE NOT NULL",
				'time' => "DATE NOT NULL",
				'last_date' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
				'do' => "INT(11) NOT NULL DEFAULT 0",
				),
			"index" => array(
			),
			"unique" => array(
				
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->model_news_time_up = new Model($config_model_news_time_up);
		
		$this->monthes=array(
				1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
				5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
				9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
			);
		$this->monthes=array(
				1 => 'янв', 2 => 'фев', 3 => 'марта', 4 => 'апр',
				5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'авг',
				9 => 'сен', 10 => 'окт', 11 => 'нояб', 12 => 'дек'
			);
			
		$this->customStatus=array(
				0=> [ 'label'=>'на модерации', 'color'=>'#ff0000'],
				1 => [ 'label'=>'в работе', 'color'=>'#ffce1e'],
				2 => [ 'label'=>'добавлена', 'color'=>'#018e2b'],
			);
		//var_dump($kafa);
    }
	
	public function writeLook($n_id, $our=1, $uid=null, $result=null){
		$data = [ 'new_id'=>$result['news_id'],'n_id'=>$result['news_id'], 'our'=>$our, 'date' => date('Y-m-d'), 'time'=>date('H:i:s'), 'ip'=>getIp(), 'cols'=>$_SERVER['HTTP_USER_AGENT'], 'look_from'=>3, 'uid'=>$uid  ];
		$this->_model_news_look->Insert($data);
		$this->_model_news_look_day->Insert($data);
		$looks = $result['looks'] + 1;
		$data = [ "looks" => $looks ];
		$this->Update($data, $result['id']);
		
		
	}
	
	private function getFavourites($result, $uid=null){
		if(!empty($uid)){
		foreach($result as $i=>$new){
			$md5 = [
				md5( 'http://feo.ua/news/'.$new['url'] ),
				md5( 'http://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($new['url_ru'] ) ),
				md5( 'https://feo.ua/news/'.$new['url'] ),
				md5( 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($new['url_ru'] ) ),
			];
			$pages = $GLOBALS['DB']['80.93.183.242']->getCol("SELECT `id` FROM `new_feo_ua`.`like_pages` WHERE (`md5_url` IN (?a))", $md5);
			$result[$i]['favourites']=$GLOBALS['DB']['80.93.183.242']->getOne("SELECT COUNT(*) FROM `new_feo_ua`.`like_rates` WHERE `uid`=?i and `cid` IN (?a)", $uid, $pages)?1:0;
		}}
		return $result;
	}
	
	private function getAudios($result){
		foreach($result as $i=>$new){
			$audio = $this->_model_news_audio_streams->getItemWhere("`new_id`='{$new['news_id']}' AND `status`=1", "*", "`adddate` DESC");
			if($audio){
				if($audio['file']){
					$result[$i]['audio'] = 'https://xn--e1asq.xn--p1ai' . $audio['file'];
				}
				elseif($audio['audio']){
					$result[$i]['audio'] = 'https://gorod24.online' . $audio['audio'];
				}
				else {
					$result[$i]['audio'] = NULL;
				}
			}
			else {
				$result[$i]['audio'] = NULL;
			}
		} 
		return $result;
	}
	
	private function getCountPhotos($result){
		foreach($result as $i=>$new){
			$photos = $this->model_photos->getCountWhere("`n_id`='{$new['news_id']}' AND `our`='{$new['our']}'");
			$result[$i]['photos'] = (int)$photos;
		} 
		return $result;
	}

	private function getCountComments($result, $uid=null){
		foreach($result as $i=>$new){
		$md5 = [
			md5( 'http://feo.ua/news/'.$new['url'] ),
			md5( 'http://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($new['url_ru'] ) ),
			md5( 'https://feo.ua/news/'.$new['url'] ),
			md5( 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($new['url_ru'] ) ),
		];
		$result[$i]['comments'] = (int) $GLOBALS['DB']['80.93.183.242']->getOne("SELECT COUNT(*) FROM `new_feo_ua`.`com_comments` WHERE `on_off`='2' AND `page_id` IN (select `id` from `new_feo_ua`.`com_pages` WHERE `md5_url` IN (?a) ) ORDER BY id DESC", $md5);
		} 
		return $result;
	}

	private function parseDate($result){
		foreach($result as $i=>$new){
			$result[$i]['news_date'] = $this->parseDateOne($result[$i]['news_date']);
		}
		return $result;
	}

	private function parseDateOne($news_date){
		$date = $news_date;
		$time = strtotime($date); 
		$ndate = date("Y-m-d",$time);
		$today = date("Y-m-d");
		$diff = $today - $ndate;
		
		$datetime1 = new DateTime($ndate);
		$datetime2 = new DateTime($today);
		$interval = $datetime1->diff($datetime2);
		$diff = $interval->format('%a');
		if($diff<1){
			$news_date = 'Сегодня в ' .date("H:i", $time);
		}
		elseif($diff>=1 and $diff<2) {
			$news_date = 'Вчера в ' .date("H:i", $time);
		}
		else {
			$news_date = (int)date("d", $time).' ' . $this->monthes[(int)date("m", $time)] . ' ' .date("H:i", $time);
		}
		return $news_date;
	}
	
	private function parseTeg($result){
		return $result;
		foreach($result as $i=>$new){
			$tags = [];
			if(!empty($new['news_tag'])){
				$t = explode(';', $new['news_tag']);
				foreach($t as $j=>$tag){
					if($j!=0 and $j!=count($t)){
						if(!empty($tag))$tags[] = substr($tag,1);
					}
				}
			}
			$result[$i]['news_tag'] = $tags;
		}
		return $result;
	}
	
	public function getByTown(int $city_id, $uid=null, $start=0, $limit=20){
		if(empty($city_id)) return ["error"=>1, "message"=>"City not selected"];
		
		$result = $this->get("id, news_id, news_head, news_lid, news_razd, razd_id, news_video_you, url, url_ru, 
		/*(SELECT concat('https://xn--e1asq.xn--p1ai', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, */
		(concat('https://xn--e1asq.xn--p1ai/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, 
		news_razd, news_tag, town as news_town, news_date, (if(`our`=1,'base1','base2')) as `base`, `looks`, 
		/*(SELECT COUNT(id) FROM `main`.`news_photo` WHERE `n_id` = `t`.`news_id` AND `news_photo`.`our` = `t`.`our` ) AS photos, */
		(if(news_video_you!='',1,0)) as videos")
			->where("'{$city_id}' IN (SELECT `city_id` FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id`) and on_off='1' AND `show_in_app`='1' AND `news_razd`!='Астропрогноз' AND `news_date`<=NOW()")->order("news_date DESC")->offset($start)->limit($limit)->commit();
		
		$result = $this->parseTeg($result);
		$result = $this->getFavourites($result, $uid);
		$result = $this->getAudios($result);
		$result = $this->getCountPhotos($result);
		$result = $this->getCountComments($result);
		$result = $this->parseDate($result);
		return $result;
	}

	public function getByRazd(int $city_id, $razd, $uid=null, $start=0, $limit=20){
		if(empty($city_id)) return ["error"=>1, "message"=>"City not selected"];
		$wq = "`on_off`='1' AND `show_in_app`='1' AND `news_date`<=NOW() AND (SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$city_id}')>0";
		if(!empty($razd)){
			$wq .= " AND `razd_id`='{$razd}'";
		}
		
		$result = $this->get("`id` as `news_id`, news_head, news_lid, news_razd, razd_id, news_video_you, url, url_ru, concat('https://xn--e1asq.xn--p1ai/новости/', url_ru) as link, our,
		/*(SELECT concat('https://xn--e1asq.xn--p1ai', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, */
		(concat('https://gorod24.online/thrumbs/news/new_', id, '_210_177.jpg')) as news_photo, 
		news_tag, town as news_town, news_date, `looks`, 'base0' as `base`,
		/*(if(`our`=1,'base1','base2')) as `base`, */
		(SELECT COUNT(id) FROM `gorod_news_photos` WHERE `gorod_news_photos`.`new_id` = `gorod_news`.`id`) AS photos,
		(if(news_video_you!='',1,0)) as videos")
			->where($wq)->order("news_date DESC")->offset($start)->limit($limit)->commit();
		
		$result = $this->parseTeg($result);
		$result = $this->getFavourites($result, $uid);
		$result = $this->getAudios($result);
		//$result = $this->getCountPhotos($result);
		$result = $this->getCountComments($result);
		$result = $this->parseDate($result);
		return $result;
	}

	public function getByTag(int $city_id, $tag, $uid=null, $start=0, $limit=20){
		if(empty($city_id)) return ["error"=>1, "message"=>"City not selected"];
		$result = $this->get("`id` as news_id, news_head, news_lid, news_razd, razd_id, news_video_you, url, url_ru, concat('https://xn--e1asq.xn--p1ai/новости/', url_ru) as link, 
		/*(SELECT concat('https://xn--e1asq.xn--p1ai', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, */
		(concat('https://gorod24.online/thrumbs/news/new_', id, '_210_177.jpg')) as news_photo, 
		news_tag, town as news_town, news_date, `looks`, 'base0' as `base`,
		/*(if(`our`=1,'base1','base2')) as `base`, */
		(SELECT COUNT(id) FROM `gorod_news_photos` WHERE `gorod_news_photos`.`new_id` = `gorod_news`.`id`) AS photos,
		(if(news_video_you!='',1,0)) as videos")
			->where("(SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$city_id}')>0 AND `news_tag` LIKE '%;#{$tag};%' and on_off='1' AND `show_in_app`='1' AND `news_date`<=NOW()")->order("news_date DESC")->offset($start)->limit($limit)->commit();
		
		$result = $this->parseTeg($result);
		$result = $this->getFavourites($result, $uid);
		$result = $this->getAudios($result);
		//$result = $this->getCountPhotos($result);
		$result = $this->getCountComments($result);
		$result = $this->parseDate($result);
		return $result;
	}

	public function getBySearch(int $city_id, $search_word, $uid=null, $start=0, $limit=20){
		if(empty($city_id)) return ["error"=>1, "message"=>"City not selected"];
		
		$result = $this->get("`id` as news_id, news_head, news_lid, news_razd, razd_id, news_video_you, url, url_ru, concat('https://xn--e1asq.xn--p1ai/новости/', url_ru) as link, 
		/*(SELECT concat('https://xn--e1asq.xn--p1ai', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, */
		(concat('https://gorod24.online/thrumbs/news/new_', id, '_210_177.jpg')) as news_photo, 
		news_tag, town as news_town, news_date, `looks`, 'base0' as `base`,
		/*(if(`our`=1,'base1','base2')) as `base`, */
		(SELECT COUNT(id) FROM `gorod_news_photos` WHERE `gorod_news_photos`.`new_id` = `gorod_news`.`id`) AS photos,
		(if(news_video_you!='',1,0)) as videos")
			->where("(SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news`.`id` AND `city_id` ='{$city_id}')>0 AND on_off='1' AND `show_in_app`='1' AND (MATCH(`news_head`, `news_lid`, `news_body`) AGAINST ('{$search_word}'))>0 AND `news_date`<=NOW()")->order("news_date DESC")->offset($start)->limit($limit)->commit();
		
		$result = $this->parseTeg($result);
		$result = $this->getFavourites($result, $uid);
		$result = $this->getAudios($result);
		//$result = $this->getCountPhotos($result);
		$result = $this->getCountComments($result);
		$result = $this->parseDate($result);
		return $result;
	}
	
	public function getDayPlaylist(int $city_id){
		if(empty($city_id)) return ["error"=>1, "message"=>"City not selected"];
		$city_title = $this->model_cities->get('city_title')->where("city_id={$city_id}")->commit('one');
		
		$playlist = $this->_model_news_audio_streams()->getItemsWhere("
			(select news_date FROM `gorod_news` WHERE `gorod_news`.`id` = `gorod_news_audio_streams`.`new_id`)>=CURDATE()
			AND 
			(SELECT count(*) FROM `gorod_news_cities` WHERE `gorod_news_cities`.`new_id`=`gorod_news_audio_streams`.`new_id` AND `city_id` ='{$city_id}')>0
		", "(select `news_date` FROM `gorod_news` WHERE `gorod_news`.`id` = `gorod_news_audio_streams`.`new_id`)", null, null, "id, name, concat('https://xn--e1asq.xn--p1ai', file) as file ");
		return $playlist;
	}
	
	public function getRubricPlaylist(int $city_id, $rubric_id, $start, $limit){
		if(empty($city_id)) return ["error"=>1, "message"=>"City not selected"];
		$city_title = $this->model_cities->get('city_title')->where("city_id={$city_id}")->commit('one');
		
		$wq = "on_off='1' AND `show_in_app`='1' AND `news_date`<=NOW() AND `town`='{$city_title}' AND (SELECT COUNT(*) FROM `gorod_news_audio_streams` WHERE `gorod_news_audio_streams`.`new_id`=`gorod_news`.`id`)>0";
		
		if(!empty($razd)){
			$razd_object = $this->model_razd()->getItemWhere("`on_off`='1' AND `id`='{$razd}'", "`id`, `name_razd` as `name`");
			$wq .= " AND `news_razd`='{$razd_object['name']}'";
		}
		
		$result = $this->get("
		(SELECT id FROM `gorod_news_audio_streams` as `s` WHERE `s`.`new_id`=`gorod_news`.`id` LIMIT 1) as id, 
		(SELECT name FROM `gorod_news_audio_streams` as `s` WHERE `s`.`new_id`=`gorod_news`.`id` LIMIT 1) as name, 
		(SELECT concat('https://xn--e1asq.xn--p1ai', `file`) FROM `gorod_news_audio_streams` as `s` WHERE `s`.`new_id`=`gorod_news`.`id` LIMIT 1) as file,
		(SELECT concat('https://gorod24.online', `audio`) FROM `gorod_news_audio_streams` as `s` WHERE `s`.`new_id`=`gorod_news`.`id` LIMIT 1) as audio
		")
		->where($wq)->order("news_date DESC")->offset($start)->limit($limit)->commit();
		foreach($result as $i=>$item){
			if($item['file']=='https://xn--e1asq.xn--p1ai' and $item['audio']!='https://gorod24.online'){ $result[$i]['file']=$item['audio']; }
			unset ($result[$i]['audio']);
		}
		return $result;
	}
	
	public function getOne($base, $id, $uid=null){
		$fields = "id, id as news_id, news_head, news_lid, news_body, news_kto as author, 
		ot_name, ot_sylka, news_video_you, url, url_ru, news_album_id, news_panorama, news_zamer_id, 
		concat('https://xn--e1asq.xn--p1ai/новости/', url_ru) as link, 
		(concat('https://gorod24.online/thrumbs/news/new_', id, '_640_0.jpg')) as news_photo,  
		news_razd, news_tag, town as news_town, news_date, `looks`, 
		'base0' as `base`";
		switch($base){
			case 'base0': { $result=$this->getItemWhere("`id`={$id}", $fields); 					$this->writeLook($id, 1, $uid, $result); break; }
			case 'base1': { $result=$this->getItemWhere("`news_id`={$id} AND `our`='1'", $fields); 	$this->writeLook($id, 1, $uid, $result); break; }
			case 'base2': { $result=$this->getItemWhere("`news_id`={$id} AND `our`='0'", $fields); 	$this->writeLook($id, 0, $uid, $result); break; }
			default: $result=$this->getItem($id, $fields);break;
		}
		if(!empty($result)){
			$result['news_body'] = str_replace(["\\r", "\\n"], "", $result['news_body']);
			$result['news_body'] = str_replace(["\\"],'',$result['news_body']);
			$result['news_body'] = '<style>iframe, img, table{width:100%!important;}  td, ht {word-break: break-all;font-size:10px;}</style>'.$result['news_body'];
			if(!empty($result['news_video_you'])){ 
				$url_video = trim($result['news_video_you']);
				if(substr($url_video,0,17)=='https://youtu.be/'){
					$youtube_id=substr($url_video,17,  strlen($url_video));
				}
				else {
					$file = parse_url($url_video, PHP_URL_QUERY);
					parse_str($file, $file);
					foreach($file as $key=>$val){
						if(substr($key,0,4)=='amp;'){ unset($file[$key]); $newKey = substr($key,4, strlen($key)); $file[$newKey]=$val;  }
					}
					$youtube_id=$file['v'];
				}
				$video_plaer='
				<iframe src="https://www.youtube.com/embed/'.$youtube_id.'?rel=0" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
				';
				
				$result['news_body'].= $video_plaer;
				
			}
			if(!empty($result['news_panorama'])){
				$panorama_src='http://xn--e1asq.xn--p1ai/панорамы/p'.$result['news_panorama'].'/g';
				$result['news_body'].='<iframe class="panorama-frame" src="'.$panorama_src.'" allowfullscreen></iframe>';
			}
			if(!empty($result['news_zamer_id'])){
				$result['news_body'].=$this->get_zamer($result['news_zamer_id']);
			}
			
			if(!empty($result['ot_name']) and !empty($result['ot_sylka'])){
				if(substr($result['ot_sylka'], 0, 4)=='http'){
					$result['news_body'].= "<div style='text-align:right;'>Источник: <a href='{$result['ot_sylka']}'>{$result['ot_name']}</a></div>";
				}
				else {
					unset($result['ot_sylka']);
				}
			} else { unset($result['ot_name']); unset($result['ot_sylka']); }
			$result['news_date'] = $this->parseDateOne($result['news_date']);
			
			$result['photos'] = $this->getPhotos($id, $base, $result['news_album_id']);
			$result['comments'] = $this->getComments($id, $base, $uid);
			
			if(!empty($uid)){
				$md5 = [
					md5( 'http://feo.ua/news/'.$result['url'] ),
					md5( 'http://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($result['url_ru'] ) ),
					md5( 'https://feo.ua/news/'.$result['url'] ),
					md5( 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($result['url_ru'] ) ),
				];
				$pages = $GLOBALS['DB']['80.93.183.242']->getCol("SELECT `id` FROM `new_feo_ua`.`like_pages` WHERE (`md5_url` IN (?a))", $md5);
				$result['favourites']=$GLOBALS['DB']['80.93.183.242']->getOne("SELECT COUNT(*) FROM `new_feo_ua`.`like_rates` WHERE `uid`=?i and `cid` IN (?a)", $uid, $pages)?1:0;
			}
			$tags = [];
			if(!empty($result['news_tag'])){
				$t = explode(';', $result['news_tag']);
				foreach($t as $j=>$tag){
					if($j!=0 and $j!=count($t)){
						if(!empty($tag))$tags[] = substr($tag,1);
					}
				}
			}
			unset($result['id']);
			
		}
		return $result;
	}
	
	public function getFavourite(int $city_id, $user_id=null, $start=0, $limit=20){
		$query = 'select `rates`.*, `pages`.*
		from `new_feo_ua`.`like_rates` as `rates`, `new_feo_ua`.`like_pages` as `pages`  
		where `uid` = '.$user_id.' and `rates`.`cid` = `pages`.`id`;';
		$links = $GLOBALS['DB']['80.93.183.242']->GetAll($query);
		if(count($links)>0){
			$arr = array(
						'`site_21200`.`our_news`'=>array(), 
						'`site_21200`.`kafa_news`'=>array(),
						'`gorod_news`'=>array()
						);
			
			foreach($links as $i => $link){
				$url = $link['url'];
				$url = parse_url($url);
				$url = $url['path'];
				$keywords = preg_split("/[-]+/", $url);
				$id = $keywords[0];
				$id=urldecode($id);
				if(substr($id,0, strlen('/news/')) == '/news/'){
					$nid = substr($id,strlen('/news/'));
				}
				elseif(substr($id,0, strlen('/новости/')) == '/новости/'){
					$nid = substr($id,strlen('/новости/'));
				}
				$literal = substr($nid,-1);
				if(is_numeric($literal)){
					$id = $nid;
					$arr['`gorod_news`'][] = $id;
				}
				else {
					$id = substr($nid,0, strlen($nid)-1);
					if(is_numeric($id)){
						$table = (substr($nid,strlen($nid)-1) == 'o' or substr($nid,strlen($nid)-1) == 'о')? '`site_21200`.`our_news`' : '`site_21200`.`kafa_news`';
						$arr[$table][] = $id;
					}	
				}
			}
			
			$where = [];
			if(count($arr['`site_21200`.`our_news`'])>0){
				$our = "select news_id, news_head, news_lid, news_video_you, url, url_ru, concat('https://xn--e1asq.xn--p1ai/новости/', url_ru) as link,  (SELECT concat('https://xn--e1asq.xn--p1ai', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, (concat('https://xn--e1asq.xn--p1ai/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, (if(`our`=1,'base1','base2')) as `base`, `looks`, (SELECT COUNT(*) FROM `new_feo_ua`.`com_comments` WHERE `page_id`=(select `id` from `new_feo_ua`.`com_pages` WHERE `md5_url`=(md5(concat('https://feo.ua/news/',t.url))) )) as comments, (SELECT COUNT(id) FROM `main`.`news_photo` WHERE `n_id` = `t`.`news_id` AND `news_photo`.`our` = `t`.`our` ) AS photos, (if(news_video_you!='',1,0)) as videos from `site_21200`.`our_news` as `t` where ";
				foreach ($arr['`site_21200`.`our_news`'] as $i => $id){
					$our .= "`news_id` = ".$id. " or ";
					$where[] = " (`news_id` = ".$id." AND `our`=1)";
				}
				$our = substr($our, 0, -4);
				
			}
			
			if(count($arr['`site_21200`.`kafa_news`'])>0){
				$kafa = "select news_id, news_head, news_lid, news_video_you, url, url_ru, concat('https://xn--e1asq.xn--p1ai/новости/', url_ru) as link,  (SELECT concat('https://xn--e1asq.xn--p1ai', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, (concat('https://xn--e1asq.xn--p1ai/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, (if(`our`=1,'base1','base2')) as `base`, `looks`, (SELECT COUNT(*) FROM `new_feo_ua`.`com_comments` WHERE `page_id`=(select `id` from `new_feo_ua`.`com_pages` WHERE `md5_url`=(md5(concat('https://feo.ua/news/',t.url))) )) as comments, (SELECT COUNT(id) FROM `main`.`news_photo` WHERE `n_id` = `t`.`news_id` AND `news_photo`.`our` = `t`.`our` ) AS photos, (if(news_video_you!='',1,0)) as videos from `site_21200`.`kafa_news` as `t` where ";
				foreach ($arr['`site_21200`.`kafa_news`'] as $i => $id){
					$kafa .= "`news_id` = ".$id. " or ";
					$where[] = " (`news_id` = ".$id." AND `our`=0)";
				}
				$kafa = substr($kafa, 0, -4);
				
			}
			
			if(count($arr['`gorod_news`'])>0){
				foreach ($arr['`gorod_news`'] as $i => $id){
					$where[] = " (`id` = ".$id.")";
				}
			}
			$q_where = implode(" OR ", $where);
			
			
			//if($user_id==14600){
				$result = $this->get("`id` as `news_id`, news_head, news_lid, news_razd, razd_id, news_video_you, url, url_ru, concat('https://xn--e1asq.xn--p1ai/новости/', url_ru) as link, our,
				/*(SELECT concat('https://xn--e1asq.xn--p1ai', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, */
				(concat('https://gorod24.online/thrumbs/news/new_', id, '_210_177.jpg')) as news_photo, 
				news_tag, town as news_town, news_date, `looks`, 'base0' as `base`,
				/*(if(`our`=1,'base1','base2')) as `base`, */
				(SELECT COUNT(id) FROM `gorod_news_photos` WHERE `gorod_news_photos`.`new_id` = `gorod_news`.`id`) AS photos,
				(if(news_video_you!='',1,0)) as videos")
					->where($q_where)->order("news_date DESC")->offset($start)->limit($limit)->commit();
				
				$result = $this->parseTeg($result);
				$result = $this->getFavourites($result, $user_id);
				$result = $this->getAudios($result);
				//$result = $this->getCountPhotos($result);
				$result = $this->getCountComments($result);
				$result = $this->parseDate($result);
				return $result;
			//}
			
			if(isset($our) and $our !='' and isset($kafa) and $kafa != ''){
				$query = $our.' union '.$kafa;
			}
			else {
				$query = (isset($our) and $our !='')? $our : $kafa;
			}
			if(!empty($dateFrom) or !empty($dateTo)){
				$filterQuery = " where ";
				if(!empty($dateFrom)) {		$filterQuery .= "`news_date`>= '{$dateFrom}'"; }
				if(!empty($dateFrom) and !empty($dateTo)) {$filterQuery .= " and ";  }
				if(!empty($dateTo)) {$filterQuery .= "`news_date`<= '{$dateTo}'";  }
			}
			
			
			$query = "select * from (
				(select * from ({$query}) as `t`) as `t`
			) {$filterQuery} order by `news_date` desc limit {$start}, {$limit}";
			$news = $GLOBALS['DB']['80.93.183.242']->GetAll($query);
			
			$news = $this->parseTeg($news);
			$news = $this->getFavourites($news, $user_id);
			$result = $this->parseDate($news);
			return $news;
		}
	}
	
	public function likeNew($base, $id, $uid){
		$fields = "news_id, news_head, news_lid, news_body, news_video_you, url, url_ru, concat('https://xn--e1asq.xn--p1ai/новости/', url_ru) as link,  (concat('https://xn--e1asq.xn--p1ai/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, `looks`, (if(`our`=1,'base1','base2')) as `base`";
		switch($base){
			case 'base0': $result=$this->getItemWhere("`id`='{$id}'", $fields);break;
			case 'base1': $result=$this->getItemWhere("`news_id`='{$id}' AND `our`='1'", $fields);break;
			case 'base2': $result=$this->getItemWhere("`news_id`='{$id}' AND `our`='0'", $fields);break;
			default: $result=$this->_model_news->our()->getItem($id, $fields);break;
		}
		$md5_url = md5( 'https://feo.ua/news/'.$result['url'] );
		$md5_url_ru = md5( 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($result['url_ru'] ) );
		$pages = $GLOBALS['DB']['80.93.183.242']->getCol("SELECT `id` FROM `new_feo_ua`.`like_pages` WHERE (`md5_url`=?s OR `md5_url`=?s)", $md5_url_ru, $md5_url);
		if(!empty($pages)){
			$this->like_rates->Delete("`cid` IN (".implode(',',$pages).") AND `uid`='{$uid}'");
			foreach($pages as $page){
			$this->like_rates->Insert([
				'cid' => $page,
				'uid' => $uid,
				'val' => 1,
				'date' => date('Y-m-d'),
				'time' => date('H:i:s'),
				'ut' => time(),
				'ip' => getIp(),
			]);
			}
		}
		else {
			$pages[0] = $this->like_pages->Insert([
				'url'=> 'https://feo.ua/news/'.$result['url'],
				'md5_url'=> $md5_url,
				'count'=> 1,
			]);
			$pages[1] = $this->like_pages->Insert([
				'url'=> 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($result['url_ru'] ),
				'md5_url'=> $md5_url_ru,
				'count'=> 1,
			]);
			$this->like_rates->Delete("`cid` IN (".implode(',',$pages).") AND `uid`='{$uid}'");
			foreach($pages as $page){
			$this->like_rates->Insert([
				'cid' => $page,
				'uid' => $uid,
				'val' => 1,
				'date' => date('Y-m-d'),
				'time' => date('H:i:s'),
				'ut' => time(),
				'ip' => getIp(),
			]);
			}
		}
		return ["success"=>1, "message"=>"Добавлено в избранное"];
	}
	
	public function dislikeNew($base, $id, $uid){
		$fields = "news_id, news_head, news_lid, news_body, news_video_you, url, url_ru, concat('https://xn--e1asq.xn--p1ai/новости/', url_ru) as link,  (concat('https://xn--e1asq.xn--p1ai/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, `looks`, (if(`our`=1,'base1','base2')) as `base`";
		switch($base){
			case 'base0': $result=$this->getItemWhere("`id`='{$id}'", $fields);break;
			case 'base1': $result=$this->getItemWhere("`news_id`='{$id}' AND `our`='1'", $fields);break;
			case 'base2': $result=$this->getItemWhere("`news_id`='{$id}' AND `our`='0'", $fields);break;
			default: $result=$this->_model_news->our()->getItem($id, $fields);break;
		}
		$md5_url = md5( 'https://feo.ua/news/'.$result['url'] );
		$md5_url_ru = md5( 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($result['url_ru'] ) );
		$pages = $GLOBALS['DB']['80.93.183.242']->getCol("SELECT `id` FROM `new_feo_ua`.`like_pages` WHERE (`md5_url`=?s OR `md5_url`=?s)", $md5_url_ru, $md5_url);
		if(!empty($pages)){
			$this->like_rates->Delete("`cid` IN (".implode(',',$pages).") AND `uid`='{$uid}'");
		}
		return ["success"=>1, "message"=>"Удалено из избранного"];
	}
	
	function getPhotos($id, $base='base0', $album_id=null){
		//$fields0 = "`id`, (concat('https://gorod24.online', `img`)) as `original`, (concat('https://gorod24.online/thrumbs/news/photo_{$id}_', `id` ,'_361_240.jpg')) as `thrumb`, description, title";
		$fields0 = "`id`, (concat('https://gorod24.online/thrumbs/news/bigphoto_{$id}_', `id` ,'_1000_0.jpg')) as `original`, (concat('https://gorod24.online/thrumbs/news/photo_{$id}_', `id` ,'_361_240.jpg')) as `thrumb`, description, title";
		$fields1 = "`id`, (concat('https://xn--e1asq.xn--p1ai/news/foto/',`foto`)) as `original`, (concat('https://xn--e1asq.xn--p1ai/upload/news_fotos_thumb/onf_', n_id, '_', id,'_361_240.jpg')) as `thrumb`, discription as description, title";
		$fields2 = "`id`, (concat('https://xn--e1asq.xn--p1ai/news/foto/',`foto`)) as `original`, (concat('https://xn--e1asq.xn--p1ai/upload/news_fotos_thumb/knf_', n_id, '_', id,'_361_240.jpg')) as `thrumb`, discription as description, title";
		switch($base){
			case 'base0': $result = $this->model_gorod_photos()->getItemsWhere("`new_id`='{$id}' AND `status`='1'", "`pos` ASC", null, null, $fields0); break;
			case 'base1': $result = $this->model_photos()->getItemsWhere("`n_id`='{$id}' AND `our`='1' AND `on_off`='1'", "`pos` ASC", null, null, $fields1); break;
			case 'base2': $result = $this->model_photos()->getItemsWhere("`n_id`='{$id}' AND `our`='0' AND `on_off`='1'", "`pos` ASC", null, null, $fields2); break;
		}
		if(!empty($album_id)){
		$photos = $GLOBALS['DB']['80.93.183.242']->GetAll(
			"SELECT 
				`ph_id` as `id`,
				(concat('https://xn--e1asq.xn--p1ai', `file_link`, `file_name`)) as `original`,
				(concat('https://xn--e1asq.xn--p1ai', `file_link`, `file_name`)) as `thrumb`,
				ph_description as description,
				al_name as title
			FROM 
				new_feo_ua.feo_photos, new_feo_ua.feo_files, new_feo_ua.feo_albums
			WHERE 
				feo_files.file_id = feo_photos.ph_file_id 
			AND
				feo_photos.ph_album_id = feo_albums.al_id
			AND
				feo_albums.al_visible = 1
			AND 
				feo_photos.ph_visible = 1 
			AND 
				feo_photos.ph_status = 1 
			AND 
				feo_files.file_status = 1
			AND 
				feo_albums.al_id=?i
			ORDER BY feo_files.file_date DESC", $album_id);
			$result = array_merge($result, $photos);
		}
		
		return $result;
	}
	
	function getComments($id, $base='base0', $uid=NULL){
		switch($base){
			case 'base0': $new = $this->getItemWhere("`id`='{$id}'"); break;
			case 'base1': $new = $this->getItemWhere("`news_id`='{$id}' AND `our`='1'"); break;
			case 'base2': $new = $this->getItemWhere("`news_id`='{$id}' AND `our`='0'"); break;
		}
		
		$md5 = [
			md5( 'http://feo.ua/news/'.$new['url'] ),
			md5( 'http://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($new['url_ru'] ) ),
			md5( 'https://feo.ua/news/'.$new['url'] ),
			md5( 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($new['url_ru'] ) ),
		];
		
		$query = $GLOBALS['DB']['80.93.183.242']->parse("SELECT id, pid, uid, text, date, time, rate FROM `new_feo_ua`.`com_comments` WHERE `on_off`='2' AND `page_id` IN (select `id` from `new_feo_ua`.`com_pages` WHERE `md5_url` IN (?a) ) ORDER BY id DESC", $md5);
		$result = $GLOBALS['DB']['80.93.183.242']->getAll($query);
		$accounts = new model_feo_accounts();
		foreach($result as $i => $item){
			$user = $accounts->get_user_public($item['uid']);
			$result[$i]['text'] = html_entity_decode (strip_tags($result[$i]['text']));
			$result[$i]['name'] = !empty($user['name'])?$user['name']:"{$user['i_fam']} {$user['i_name']}";
			$result[$i]['ava_file'] = $user['ava_file'];
		}
		return $result;
	}
	
	function addComment($id, $base='base0', $uid, $text){
		if(!empty($uid) AND !empty($text)){
		switch($base){
			case 'base0': $new = $this->getItem($id); break;
			case 'base1': $new = $this->getItemWhere("`news_id`='{$id}' AND `our`='1'"); break;
			case 'base2': $new = $this->getItemWhere("`news_id`='{$id}' AND `our`='0'"); break;
		}
		$md5 = [
			md5( 'http://feo.ua/news/'.$new['url'] ),
			md5( 'http://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($new['url_ru'] ) ),
			md5( 'https://feo.ua/news/'.$new['url'] ),
			md5( 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($new['url_ru'] ) ),
		];
		
		$query = $GLOBALS['DB']['80.93.183.242']->parse("SELECT * FROM `new_feo_ua`.`com_pages` WHERE `md5_url` IN (?a) ORDER BY id DESC", $md5);
		$pages = $GLOBALS['DB']['80.93.183.242']->getAll($query);
		if(count($pages)){
			$page = $pages[0]['id'];
		}
		else {
			$data = [
				'url' =>  'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($new['url_ru'] ),
				'md5_url' => md5( 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($new['url_ru'] ) ),
				'count' => 0,
			];
			$GLOBALS['DB']['80.93.183.242']->query("INSERT INTO `new_feo_ua`.`com_pages` SET ?u", $data);
			$page = $GLOBALS['DB']['80.93.183.242']->insertId();
		}
		
		$comment_data = [
			'page_id' => $page,
			'pid' => 0,
			'uid' => $uid,
			'text' => $text,
			'date' => date("Y-m-d"),
			'time' => date("H:i:s"),
			'ut' => time(),
			'on_off' => '2',
			'ip' => getIp(),
			'rate' => '0',
			'gazeta_user_id' => null,
			'gazeta_sub_key' => null,
			'user_alias' => null,
		];
		$ch = $GLOBALS['DB']['80.93.183.242']->getOne("SELECT COUNT(*) FROM  `new_feo_ua`.`com_comments` WHERE `page_id`=?i AND `uid`=?i AND `text`=?s", $page, $uid, $text);
		if($ch==0){
			$GLOBALS['DB']['80.93.183.242']->query("INSERT INTO `new_feo_ua`.`com_comments` SET ?u", $comment_data);
		}
		} else return ["error"=>1, "message"=>"Вы не вписали текст комментария."];
		return $this->getComments($id, $base);
	}
	
	function getCustomNews(int $user_id){
		$results = $this->userNews()->getItemsWhere("`user_id`='{$user_id}'");
		foreach($results as $i=>$result){
		$tags = explode(';', $result['tags']);
		$result['tags'] = [];
		foreach($tags as $tag){
			if(!empty($tag)){
				$result['tags'][] = (substr($tag,0,1)=='#'?substr($tag,1):$tag);
			}
		}
		$result['photos'] = json_decode($result['photos'], true);
		$result['status_description'] = $this->customStatus[$result['status']];
		$result['link'] = null;
		
		$results[$i] = $result;
		}
		return $results;
	}
	
	function getCustomNew(int $id){
		$result = $this->userNews()->getItem($id);
		$tags = explode(';', $result['tags']);
		$result['tags'] = [];
		foreach($tags as $tag){
			if(!empty($tag)){
				$result['tags'][] = (substr($tag,0,1)=='#'?substr($tag,1):$tag);
			}
		}
		$result['photos'] = json_decode($result['photos'], true);
		$result['status_description'] = $this->customStatus[$result['status']];
		$result['link'] = null;
		return $result;
	}
	
	function addNew($user_id, $data){
		$tags = ';'; foreach($data['tags'] as $tag){$tags .= "#{$tag};";}
		$count = $this->userNews()->getCountWhere("`user_id`='{$user_id}' AND `name`='{$data['name']}' AND `text`='{$data['text']}'");
		if($count==0){
		$id = $this->userNews()->Insert([
			"user_id" => $user_id,
			"name" => $data['name'],
			"text" => $data['text'],
			"tags" => $tags,
			"photos" => json_encode($data['photos'], JSON_UNESCAPED_UNICODE),
			"date" => date("Y-m-d H:i:s"),
			"status" => 0,
			"ip" => getIp(),
			"latitude" => ($data['latitude']?$data['latitude']:0),
			"longitude" => ($data['longitude']?$data['longitude']:0),
		]);
		return $this->getCustomNew($id);
		}
		else {
			return [ 'error'=>1, 'message'=>'Вы уже добавляли подобную новость' ];
		}
	}
	
	function updateNew($user_id, $data, $new_id){
		$tags = ';'; foreach($data['tags'] as $tag){$tags .= "#{$tag};";}
		$id = $this->userNews()->Update([
			"user_id" => $user_id,
			"name" => $data['name'],
			"text" => $data['text'],
			"tags" => $tags,
			"photos" => json_encode($data['photos'], JSON_UNESCAPED_UNICODE),
			"date" => date("Y-m-d H:i:s"),
			"status" => 0,
			"ip" => getIp(),
			"latitude" => ($data['latitude']?$data['latitude']:0),
			"longitude" => ($data['longitude']?$data['longitude']:0),
		], $new_id);
		return $this->getCustomNew($id);
	}
	
	function get_zamer($zamer_id){
		if(!empty($zamer_id)){
		$zamer = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT * FROM `main`.`feo_basket` WHERE `bas_id`=?i", $zamer_id);
		$tovars = $GLOBALS['DB']['80.93.183.242']->GetAll("SELECT 
								*
												, (SELECT bitem_coin FROM main.feo_basket_items as prev_feo_basket_items, main.feo_basket as prev_feo_basket  WHERE prev_feo_basket_items.bitem_bas_id=prev_feo_basket.bas_id AND prev_feo_basket.bas_date<feo_basket.bas_date AND prev_feo_basket_items.bitem_tov_id=feo_basket_items.bitem_tov_id AND prev_feo_basket_items.bitem_mag_id=feo_basket_items.bitem_mag_id  ORDER by prev_feo_basket.bas_date DESC LIMIT 1) as prev_coin
											FROM 
												main.feo_basket_types_items, main.feo_basket_items, main.feo_tovars, main.feo_magaz, main.edizm, main.feo_basket
											WHERE 
												feo_basket_items.bitem_tov_id=feo_tovars.tov_id
												AND feo_basket_types_items.item_bas_id=feo_basket.bas_type AND feo_basket_types_items.item_tov_id=feo_tovars.tov_id
												AND feo_basket_items.bitem_mag_id=feo_magaz.mag_id
												AND feo_basket_items.bitem_izm=edizm.id
												AND feo_basket_items.bitem_bas_id=feo_basket.bas_id
												AND feo_basket_items.bitem_bas_id=?i ORDER BY tov_id", $zamer_id);
						
		$magazs = $GLOBALS['DB']['80.93.183.242']->GetAll("SELECT * FROM
									main.feo_basket_types_magazs, main.feo_magaz, main.feo_basket
								WHERE 
									feo_basket_types_magazs.mag_id=feo_magaz.mag_id
								AND feo_basket_types_magazs.type_id=feo_basket.bas_type
								AND feo_basket.bas_id=?i", $zamer_id);
		
		$header = '<thead><tr><td>Товар</td><td class="hidden-xs" style="width: 100px;">Единица измерения</td>';
		$width = 60/count($magazs);
		foreach($magazs as $i=>$magaz){	$header .= '<td style="width:'.$width.'%"><div>'.$magaz['mag_name'].'</div></td>';}
		$header .= '</tr></thead>';
		$body = '<tbody>';
		foreach($tovars as $i=>$tovar){
							if($tovars[$i-1]['tov_id']!=$tovar['tov_id']){
							$row  = '<tr>';
							$row .= '<td>'.$tovar['tov_name'].'</td>';
							$row .= '<td class="center hidden-xs">'.$tovar['name'].'</td>';
							}else{$row='';}
							$updown = '';
								if($tovar['prev_coin']!=''){
									if($tovar['bitem_coin']>$tovar['prev_coin'])
									$updown = '<span class="up" title="Цена выросла на '.(number_format($tovar['bitem_coin']-$tovar['prev_coin'], 2, ',', ' ')).' руб.">&#9650;</span>';
									elseif($tovar['bitem_coin']<$tovar['prev_coin'])
									$updown = '<span class="down" title="Цена упала на '.(number_format($tovar['prev_coin']-$tovar['bitem_coin'], 2, ',', ' ')).' руб.">&#9660;</span>';
									elseif($tovar['bitem_coin']==$tovar['prev_coin'])
									$updown = '<span class="self"></span>';
								}
								$number = number_format($tovar['bitem_coin'], 2, ',', ' ');
								$row .= '<td class="center">'.$number.' руб.'.$updown.'</td>';
							
							if($tovars[$i+1]['tov_id']!=$tovar['tov_id'])$row .= '</tr>';
							$body .=$row;
		}
		$body .= '</tbody>';
	
		$result = "<style>
.form-control {
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.428571429;
    color: #555;
    vertical-align: middle;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
    -webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
.form-control {
    border: none;
    border-radius: 4px;
    margin-bottom: 4px;
	margin-right: 4px;
    box-shadow: 0px 0px 5px 1px #428bca;
}
.slider .btn-default {
    background: #fff;
}
.btn-default:hover {
    border: solid 3px #027BEF;
    color: #027BEF;
}
.btn:hover, .btn:focus {
    color: #333;
    text-decoration: none;
}
.btn-default {
    background: #fff;
    border: solid 3px #dedede;
    color: #8495A5;
    font-size: 18px;
    font-weight: bold;
    padding: 10px 25px;
    -webkit-transition: all 0.3s;
    -moz-transition: all 0.3s;
    transition: all 0.3s;
}
.tovars thead td div {
	overflow:hidden;
}
.news_zamer header{
	display: block;
	padding: 0;
	margin: 0;
	font-family: SegoeUIRegular;
	font-size: 14pt;
	font-weight: bold;
	color: #0066ff;
	vertical-align: middle;
	line-height: 30px;
}
.news_zamer .tovars{
	width:100%;
}
.news_zamer .tovars thead td {
	border:1px solid #ececec;
	text-align:center;
	font-weight: bolder;
	padding: 5px;
	margin: 1px;
}
.news_zamer .tovars tbody td {
	border-bottom:1px dotted #ececec;
	padding: 5px 1px;
	margin: 1px;
	font-size: 13px;
}
.news_zamer .tovars tbody tr:hover td{
color:#eca12b;
}
.news_zamer .tovars tbody tr td.center{
text-align:center;
}
.news_zamer span.up, .news_zamer span.down{
	display:inline-block;
	width:10px;
	height:10px;
	margin-left:5px;
	cursor:pointer;
}
.news_zamer span.up{
	color:red;
}
.news_zamer span.down{
	color:green;
}
.news_zamer .comment{
	margin-top:10px;
}
</style>
<script>
$(function(){
	var $zamer = $('.zamer-item');
	var windowWidth = $(window).width();
	if($zamer && windowWidth<768){
		var cols = $('.tovars thead td').length;
		var width = parseInt(100 / cols);
		var width = (windowWidth/100) * width;
		$('.tovars thead td div').width(width); 
	}
})
</script>
<div class=\"news_zamer\">
	<div class=\"zamer-item vh_part\">
	<header class=\"header-title\">{$zamer['bas_name']}</header>
	<table class=\"tovars\">
	{$header}
	{$body}
	</table>
	<p class=\"comment\">{$zamer['bas_comment']}</p>
	</div>
</div>
";
	}
	return $result;
	}
}
?>