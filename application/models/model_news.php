<?php
/* Automatic model generated
 * ver 0.1
 * model for site: mvc.test
 * date create: 2017-10-20 22:29:21
*/
class model_news extends Model
{
	protected $kafa;
	protected $razd;
	protected $town;
	protected $cities;
	protected $photos;
	protected $like_pages;
	protected $like_rates;
	protected $userNews;
   
	public function kafa() {
		return $this->kafa;
	}
	
	public function our() {
		return $this;
	}
	
	public function razd() {
		return $this->razd;
	}
	
	public function town() {
		return $this->town;
	}
	
	public function cities() {
		return $this->cities;
	}
	
	public function photos() {
		return $this->photos;
	}
	
	public function like_pages() {
		return $this->like_pages;
	}
	
	public function like_rates() {
		return $this->like_rates;
	}
	
	public function userNews() {
		return $this->userNews;
	}
	
	function __construct($config = array()) {
		$config = [
            "server" => "80.93.183.242",
            "database" => "site_21200",
            "prefix" => "",
            "name" => "our_news",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "news_id",
			"autoinit"  => false,
            "columns" => array(
				'news_head' => "TEXT NOT NULL",				'news_lid' => "TEXT NOT NULL",
				'news_body' => "TEXT NOT NULL",
				'news_vrez' => "TEXT NOT NULL",
				'news_aut' => "TEXT NOT NULL",
				'news_aut2' => "TEXT NOT NULL",
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
				'news_kto' => "TEXT NOT NULL",
				'news_tag' => "VARCHAR(255) NOT NULL",
				'l_red' => "TEXT NOT NULL",
				'l_time' => "DATETIME NOT NULL",
				'l_main' => "TEXT NOT NULL",
				'l_comment' => "TEXT NOT NULL",
				'e_red' => "TEXT NOT NULL",
				'e_time' => "DATETIME NOT NULL",
				'e_comment' => "TEXT NOT NULL",
				'e_site' => "TEXT NOT NULL",
				'slovo' => "TEXT NOT NULL",
				'serial' => "TEXT NOT NULL",
				'fotorep' => "BIGINT(20) NOT NULL",
				'c_n' => "BIGINT(20) NOT NULL",
				'c_f' => "BIGINT(20) NOT NULL",
				'kat' => "TEXT NOT NULL",
				'ver ' => "TEXT NOT NULL",
				'town ' => "TEXT NOT NULL",
				'news_key ' => "TEXT NOT NULL",
				'news_des ' => "TEXT NOT NULL",
				'look ' => "TEXT NOT NULL",
				'news_date ' => "DATETIME NOT NULL",
				'our ' => "BIGINT(20) NOT NULL DEFAULT '1'",
				'kyrort ' => "BIGINT(20) NOT NULL",
				'lock ' => "INT(11) NOT NULL DEFAULT '0'",
				'looks ' => "INT(11) NOT NULL",
				'vk_ ' => "INT(11) NOT NULL",
				'vk_feo ' => "INT(11) NOT NULL",
				'vk_feorf ' => "INT(11) NOT NULL",
				'vk_g ' => "VARCHAR(200) NOT NULL",
				'fb ' => "VARCHAR(255) NOT NULL",
				'ot_name ' => "VARCHAR(255) NOT NULL",
				'ot_sylka ' => "VARCHAR(255) NOT NULL",
				'url ' => "TEXT NOT NULL",
				'url_ru ' => "TEXT NOT NULL",
				'kay_word ' => "TEXT NOT NULL",
				'id_pr ' => "VARCHAR(11) NOT NULL",
				'narod_id ' => "INT(11) NOT NULL",
				'akciya_id ' => "INT(11) NOT NULL",
				'on_off ' => "INT(2) NOT NULL",
				'news_lock ' => "INT(11) NOT NULL",
				'news_lock_for ' => "DATETIME NOT NULL",
				'show_comment ' => "INT(1) NOT NULL DEFAULT '1'",
				'news_inter_id ' => "INT(11) NULL DEFAULT NULL",
				'news_album_id ' => "INT(11) NULL DEFAULT NULL",
				'news_zamer_id ' => "INT(11) NULL DEFAULT NULL",
				'news_panorama ' => "INT(11) NULL DEFAULT NULL",
				'news_panorama_type ' => "INT(11) NOT NULL DEFAULT '0'",				
				),
			"index" => array(
				"kyrort" => array( 'kyrort' ),
				"lock" => array( 'lock' ),
				"narod_id" => array( 'narod_id' ),
				"date" => array( 'news_date' ),
				"news_date" => array( 'news_date', 'news_razd', 'town' ),
				"town_onoff" => array( 'town', 'news_razd', 'on_off' ),
				"news_razd" => array( 'news_razd', 'news_date', 'on_off' ),
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
		
		$kafa_config = $config;
		$kafa_config['name'] = 'kafa_news';
		
		parent::__construct($config);
		$this->kafa = new Model($kafa_config);
		
		$razd_config = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
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
		$this->razd = new Model($razd_config);
		
		$town_config = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "news_town",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name_town' => "VARCHAR(20) NOT NULL",
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
		$this->town = new Model($town_config);
		
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
		$this->photos = new Model($photos_config);
		$this->cities = new model_cities();
		
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
		
		//var_dump($kafa);
    }
	
	private function getFavorites($result, $uid=null){
		if(!empty($uid)){
		foreach($result as $i=>$new){
			$md5_url = md5( 'https://feo.ua/news/'.$new['url'] );
			$md5_url_ru = md5( 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($new['url_ru'] ) );
			$pages = $this->db()->getCol("SELECT `id` FROM `new_feo_ua`.`like_pages` WHERE (`md5_url`=?s OR `md5_url`=?s)", $md5_url_ru, $md5_url);
			$result[$i]['favorites']=$this->db()->getOne("SELECT COUNT(*) FROM `new_feo_ua`.`like_rates` WHERE `uid`=?i and `cid` IN (?a)", $uid, $pages)?1:0;
		}}
		return $result;
	}

	public function getByTown(int $city_id, $uid=null, $start=0, $limit=20){
		if(empty($city_id)) return ["error"=>1, "message"=>"City not selected"];
		$city_title = $this->cities->get('city_title')->from($this->cities)->where("city_id={$city_id}")->commit('one');
		$result = $this->get("news_id, news_head, news_lid, news_video_you, url, url_ru, (SELECT concat('https://feo.ua', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, (concat('https://feo.ua/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, (if(`our`=1,'base1','base2')) as `base`, `looks`, (SELECT COUNT(*) FROM `new_feo_ua`.`com_comments` WHERE `page_id`=(select `id` from `new_feo_ua`.`com_pages` WHERE `md5_url`=(md5(concat('https://feo.ua/news/',t.url))) )) as comments, (SELECT COUNT(id) FROM `main`.`news_photo` WHERE `n_id` = `t`.`news_id` AND `news_photo`.`our` = `t`.`our` ) AS photos, (if(news_video_you!='',1,0)) as videos")
			->union([$this->our(),$this->kafa()])->where("`town`='{$city_title}' and on_off='1' AND `show_in_app`='1' AND `news_razd`!='Астропрогноз' AND `news_date`<=NOW()")->order("news_date DESC")->offset($start)->limit($limit)->commit();
		
		$result = $this->getFavorites($result, $uid);
		return $result;
	}

	public function getByRazd(int $city_id, $razd, $uid=null, $start=0, $limit=20){
		if(empty($city_id)) return ["error"=>1, "message"=>"City not selected"];
		$city_title = $this->cities->get('city_title')->from($this->cities)->where("city_id={$city_id}")->commit('one');
		$result = $this->get("news_id, news_head, news_lid, news_video_you, url, url_ru, (SELECT concat('https://feo.ua', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, (concat('https://feo.ua/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, (if(`our`=1,'base1','base2')) as `base`, `looks`, (SELECT COUNT(*) FROM `new_feo_ua`.`com_comments` WHERE `page_id`=(select `id` from `new_feo_ua`.`com_pages` WHERE `md5_url`=(md5(concat('https://feo.ua/news/',t.url))) )) as comments, (SELECT COUNT(id) FROM `main`.`news_photo` WHERE `n_id` = `t`.`news_id` AND `news_photo`.`our` = `t`.`our` ) AS photos, (if(news_video_you!='',1,0)) as videos")
			->union([$this->our(),$this->kafa()])->where("`town`='{$city_title}' AND `news_razd`='{$razd}' and on_off='1' AND `show_in_app`='1' AND `news_date`<=NOW()")->order("news_date DESC")->offset($start)->limit($limit)->commit();
		
		$result = $this->getFavorites($result, $uid);
		return $result;
	}

	public function getByTag(int $city_id, $tag, $uid=null, $start=0, $limit=20){
		if(empty($city_id)) return ["error"=>1, "message"=>"City not selected"];
		$city_title = $this->cities->get('city_title')->from($this->cities)->where("city_id={$city_id}")->commit('one');
		$result = $this->get("news_id, news_head, news_lid, news_video_you, url, url_ru, (SELECT concat('https://feo.ua', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, (concat('https://feo.ua/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, (if(`our`=1,'base1','base2')) as `base`, `looks`, (SELECT COUNT(*) FROM `new_feo_ua`.`com_comments` WHERE `page_id`=(select `id` from `new_feo_ua`.`com_pages` WHERE `md5_url`=(md5(concat('https://feo.ua/news/',t.url))) )) as comments, (SELECT COUNT(id) FROM `main`.`news_photo` WHERE `n_id` = `t`.`news_id` AND `news_photo`.`our` = `t`.`our` ) AS photos, (if(news_video_you!='',1,0)) as videos")
			->union([$this->our(),$this->kafa()])->where("`town`='{$city_title}' AND `news_tag` LIKE '%;#{$tag};%' and on_off='1' AND `show_in_app`='1' AND `news_date`<=NOW()")->order("news_date DESC")->offset($start)->limit($limit)->commit();
		
		$result = $this->getFavorites($result, $uid);
		return $result;
	}

	public function getBySearch(int $city_id, $search_word, $uid=null, $start=0, $limit=20){
		if(empty($city_id)) return ["error"=>1, "message"=>"City not selected"];
		$city_title = $this->cities->get('city_title')->from($this->cities)->where("city_id={$city_id}")->commit('one');
		$result = $this->get("news_id, news_head, news_lid, news_video_you, url, url_ru, (SELECT concat('https://feo.ua', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, (concat('https://feo.ua/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, (if(`our`=1,'base1','base2')) as `base`, `looks`, (SELECT COUNT(*) FROM `new_feo_ua`.`com_comments` WHERE `page_id`=(select `id` from `new_feo_ua`.`com_pages` WHERE `md5_url`=(md5(concat('https://feo.ua/news/',t.url))) )) as comments, (SELECT COUNT(id) FROM `main`.`news_photo` WHERE `n_id` = `t`.`news_id` AND `news_photo`.`our` = `t`.`our` ) AS photos, (if(news_video_you!='',1,0)) as videos")
			->union([$this->our(),$this->kafa()])->where("`town`='{$city_title}' AND on_off='1' AND `show_in_app`='1' AND (MATCH(`news_head`, `news_lid`, `news_body`) AGAINST ('{$search_word}'))>0 AND `news_date`<=NOW()")->order("news_date DESC")->offset($start)->limit($limit)->commit();
		
		$result = $this->getFavorites($result, $uid);
		return $result;
	}
	
	public function getFavourite(int $city_id, $user_id=null, $start=0, $limit=20){
		$query = 'select `rates`.*, `pages`.*
		from `new_feo_ua`.`like_rates` as `rates`, `new_feo_ua`.`like_pages` as `pages`  
		where `uid` = '.$user_id.' and `rates`.`cid` = `pages`.`id`;';
		$links = $this->db()->GetAll($query);
		if(count($links)>0){
			$arr = array(
						'`site_21200`.`our_news`'=>array(), 
						'`site_21200`.`kafa_news`'=>array()
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
				$id = substr($nid,0, strlen($nid)-1);
				if(is_numeric($id)){
					$table = (substr($nid,strlen($nid)-1) == 'o' or substr($nid,strlen($nid)-1) == 'о')? '`site_21200`.`our_news`' : '`site_21200`.`kafa_news`';
					$arr[$table][] = $id;
				}				
			}

			if(count($arr['`site_21200`.`our_news`'])>0){
				$our = "select news_id, news_head, news_lid, news_video_you, url, url_ru, (SELECT concat('https://feo.ua', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, (concat('https://feo.ua/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, (if(`our`=1,'base1','base2')) as `base`, `looks`, (SELECT COUNT(*) FROM `new_feo_ua`.`com_comments` WHERE `page_id`=(select `id` from `new_feo_ua`.`com_pages` WHERE `md5_url`=(md5(concat('https://feo.ua/news/',t.url))) )) as comments, (SELECT COUNT(id) FROM `main`.`news_photo` WHERE `n_id` = `t`.`news_id` AND `news_photo`.`our` = `t`.`our` ) AS photos, (if(news_video_you!='',1,0)) as videos from `site_21200`.`our_news` as `t` where ";
				foreach ($arr['`site_21200`.`our_news`'] as $i => $id){
					$our .= "`news_id` = ".$id. " or ";
				}
				$our = substr($our, 0, -4);
			}
			
			if(count($arr['`site_21200`.`kafa_news`'])>0){
				$kafa = "select news_id, news_head, news_lid, news_video_you, url, url_ru, (SELECT concat('https://feo.ua', `file`) FROM `main`.`news_audio_streams` as `s` WHERE `s`.`news_id`=`t`.`news_id` AND `s`.`our`=`t`.`our` LIMIT 1) as audio, (concat('https://feo.ua/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, (if(`our`=1,'base1','base2')) as `base`, `looks`, (SELECT COUNT(*) FROM `new_feo_ua`.`com_comments` WHERE `page_id`=(select `id` from `new_feo_ua`.`com_pages` WHERE `md5_url`=(md5(concat('https://feo.ua/news/',t.url))) )) as comments, (SELECT COUNT(id) FROM `main`.`news_photo` WHERE `n_id` = `t`.`news_id` AND `news_photo`.`our` = `t`.`our` ) AS photos, (if(news_video_you!='',1,0)) as videos from `site_21200`.`kafa_news` as `t` where ";
				foreach ($arr['`site_21200`.`kafa_news`'] as $i => $id){
					$kafa .= "`news_id` = ".$id. " or ";
				}
				$kafa = substr($kafa, 0, -4);
			}
			
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
			$news = $this->db()->GetAll($query);
			return $news;
		}
	}
	
	public function likeNew($base, $id, $uid){
		$fields = "news_id, news_head, news_lid, news_body, news_video_you, url, url_ru, (concat('https://feo.ua/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, `looks`, (if(`our`=1,'base1','base2')) as `base`";
		switch($base){
			case 'base1': $result=$this->our()->getItem($id, $fields);break;
			case 'base2': $result=$this->kafa()->getItem($id, $fields);break;
			default: $result=$this->_model_news->our()->getItem($id, $fields);break;
		}
		$md5_url = md5( 'https://feo.ua/news/'.$result['url'] );
		$md5_url_ru = md5( 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($result['url_ru'] ) );
		$pages = $this->db()->getCol("SELECT `id` FROM `new_feo_ua`.`like_pages` WHERE (`md5_url`=?s OR `md5_url`=?s)", $md5_url_ru, $md5_url);
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
		return ["success"=>1, "message"=>"Success"];
	}
	
	public function dislikeNew($base, $id, $uid){
		$fields = "news_id, news_head, news_lid, news_body, news_video_you, url, url_ru, (concat('https://feo.ua/upload/news_fotos_thumb/',if(`our`='1','onf_','knf_'), news_id, '_210_177.jpg')) as news_photo, news_razd, news_tag, town as news_town, news_date, `looks`, (if(`our`=1,'base1','base2')) as `base`";
		switch($base){
			case 'base1': $result=$this->our()->getItem($id, $fields);break;
			case 'base2': $result=$this->kafa()->getItem($id, $fields);break;
			default: $result=$this->_model_news->our()->getItem($id, $fields);break;
		}
		$md5_url = md5( 'https://feo.ua/news/'.$result['url'] );
		$md5_url_ru = md5( 'https://xn--e1asq.xn--p1ai/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8/'.urlencode($result['url_ru'] ) );
		$pages = $this->db()->getCol("SELECT `id` FROM `new_feo_ua`.`like_pages` WHERE (`md5_url`=?s OR `md5_url`=?s)", $md5_url_ru, $md5_url);
		if(!empty($pages)){
			$this->like_rates->Delete("`cid` IN (".implode(',',$pages).") AND `uid`='{$uid}'");
		}
		return ["success"=>1, "message"=>"Success"];
	}
	
	function getPhotos($id, $base='base1'){
		$fields1 = "`id`, (concat('https://feo.ua/news/foto/',`foto`)) as `original`, (concat('https://feo.ua/upload/news_fotos_thumb/onf_', n_id, '_', id,'_361_240.jpg')) as `thrumb`, discription as description, title";
		$fields2 = "`id`, (concat('https://feo.ua/news/foto/',`foto`)) as `original`, (concat('https://feo.ua/upload/news_fotos_thumb/knf_', n_id, '_', id,'_361_240.jpg')) as `thrumb`, discription as description, title";
		switch($base){
			case 'base1': $result = $this->photos()->getItemsWhere("`n_id`='{$id}' AND `our`='1' AND `on_off`='1'", "`pos` ASC", null, null, $fields1); break;
			case 'base2': $result = $this->photos()->getItemsWhere("`n_id`='{$id}' AND `our`='0' AND `on_off`='1'", "`pos` ASC", null, null, $fields2); break;
		}
		return $result;
	}
	
	function getComments($id, $base='base1'){
		switch($base){
			case 'base1': $new = $this->our()->getItem($id); break;
			case 'base2': $new = $this->kafa()->getItem($id); break;
		}
		
		$result = $this->db()->getAll("SELECT id, pid, uid, text, date, time, ip, rate FROM `new_feo_ua`.`com_comments` WHERE `on_off`='2' AND `page_id`=(select `id` from `new_feo_ua`.`com_pages` WHERE `md5_url`=(md5(concat('https://feo.ua/news/',?s))) ) ORDER BY id DESC", $new['url']);
		
		return $result;
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
		return $result;
	}
	
	function addNew($user_id, $data){
		$tags = ';'; foreach($data['tags'] as $tag){$tags .= "#{$tag};";}
		$id = $this->userNews()->Insert([
			"user_id" => $user_id,
			"name" => $data['name'],
			"text" => $data['text'],
			"tags" => $tags,
			"photos" => json_encode($data['photos'], JSON_UNESCAPED_UNICODE),
			"date" => date("Y-m-d H:i:s"),
			"status" => 0,
			"ip" => getIp(),
			"latitude" => $data['latitude'],
			"longitude" => $data['longitude'],
		]);
		return $this->getCustomNew($id);
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
			"latitude" => $data['latitude'],
			"longitude" => $data['longitude'],
		], $new_id);
		return $this->getCustomNew($id);
	}
	
	
}
?>