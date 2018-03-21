<?php
class model_apiapps extends Model
{
	protected $model_feo_accounts;
	protected $app_version;
	public function model_feo_accounts(){
		return $this->model_feo_accounts;
	}
	
	protected $_log;
	public function log(){
		return $this->_log;
	}
	
	protected $banner;
	public function banner(){
		return $this->banner;
	}
	
	protected $banner_log;
	public function banner_log(){
		return $this->banner_log;
	}
	protected $_model_devicelog;
	public function _model_devicelog(){
		return $this->_model_devicelog;
	}
	
	function __construct($config = array()) {
		
		$this->app_version = "1.15";
		
		$config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "apiapps",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "VARCHAR(50) NOT NULL DEFAULT ''",				'description' => "TEXT NOT NULL DEFAULT ''",
				'publickey' => "VARCHAR(20) NOT NULL DEFAULT ''",
				'secretkey' => "VARCHAR(20) NOT NULL DEFAULT ''",
				'date_create' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
				'status' => "INT(1) NOT NULL DEFAULT '1'",
				'access_lk' => "INT(1) NOT NULL DEFAULT '1'",
				'access_news' => "INT(1) NOT NULL DEFAULT '1'",
				'access_adv' => "INT(1) NOT NULL DEFAULT '1'",
				'access_nb' => "INT(1) NOT NULL DEFAULT '1'",
				'access_bk' => "INT(1) NOT NULL DEFAULT '1'",
				'access_radio' => "INT(1) NOT NULL DEFAULT '1'",
				'access_rules' => "INT(1) NOT NULL DEFAULT '1'",
				'access_friend' => "INT(1) NOT NULL DEFAULT '1'",
				'access_banners' => "INT(1) NOT NULL DEFAULT '1'",
				),
			"index" => array(
				"kyrort" => array( 'publickey', 'status' ),
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
		
		parent::__construct($config);
		
		$logs_config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "apialogs",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'appid' => "int(11) NOT NULL DEFAULT '0'",
				'src' => "VARCHAR(50) NOT NULL DEFAULT ''",
				'method' => "VARCHAR(50) NOT NULL DEFAULT ''",
				'params' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'fullurl' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'uid' => "INT(11) NOT NULL DEFAULT '0'",
				'date' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
				),
			"index" => array(
				"method" => array( 'method', 'uid', 'date' ),
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
		
		$this->_log = new Model($logs_config);
		
		$this->banner = new model_banners();
		
		$banner_log_config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "banners_log",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
					'bid' => "INT(11) NOT NULL DEFAULT '1'", 
					'type' => "INT(11) NOT NULL DEFAULT '1'", 
					'uid' => "VARCHAR(255) NOT NULL DEFAULT ''",
					'date' => "DATE NOT NULL DEFAULT '0000-00-00'",
					'show' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
				),
			"index" => array(
				"bid_uid_date" => array('bid', 'uid', 'date'),
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
		$this->banner_log = new Model($banner_log_config);
		
		$devicelog_config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "devicelog",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
					'uid' => "INT(11) NULL DEFAULT NULL", 
					'city_id' => "INT(11) NULL DEFAULT NULL", 
					'imei' => "VARCHAR(100) NOT NULL DEFAULT ''", 
					'longitude' => "VARCHAR(50) NOT NULL DEFAULT ''",
					'latitude' => "VARCHAR(50) NOT NULL DEFAULT ''",
					'ip' => "VARCHAR(50) NOT NULL DEFAULT ''",
					'os' => "VARCHAR(50) NOT NULL DEFAULT ''",
					'date' => "DATE NOT NULL DEFAULT '0000-00-00'",
					'time' => "TIME NOT NULL DEFAULT '00:00:00'",
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
		$this->_model_devicelog = new Model($devicelog_config);
		
		$this->model_feo_accounts = new model_feo_accounts();
	
    }
	
	public function deviceLog($data){
		if($data['uid']){
			$model_feo_accounts = new model_feo_accounts();
			$user = $model_feo_accounts->getItem($data['uid']);
			$city_id = $user['city_id'];
		}
		$insertdata = [
			"uid" => ($data['uid']?$data['uid']:null),
			"city_id" => ($city_id?$city_id:null),
			"imei" => ($data['imei']?$data['imei']:''),
			"longitude" => ($data['longitude']?$data['longitude']:''),
			"latitude" => ($data['latitude']?$data['latitude']:''),
			"ip" => ($data['ip']?$data['ip']:''),
			"os" => ($data['os']?$data['os']:''),
			"date" => date('Y-m-d'),
			"time" => date('H:i:s')
		];
		$this->_model_devicelog->Insert($insertdata);
		return [ "success" => 1, "message" => $this->app_version ];
	}
	
	public function checkBanner($city_id, $uid=null, $access_token=null){
		$top = $this->banner->getCountWhere("`position`=1 AND `on_off`=1")>0?1:0;
		$list = $this->banner->getCountWhere("`position`=2 AND `on_off`=1")>0?1:0;
		$popup = $this->banner->getCountWhere("`position`=3 AND `on_off`=1")>0?1:0;
		$result = ["top"=>$top, "list"=>$list, "popup"=>["status"=>$popup, "screens"=>"5"]];
		if(!empty($uid) and !empty($access_token)){
			$check = $this->model_feo_accounts->checkToken($uid, $access_token);
			if($check){
				$user = $this->model_feo_accounts->get_user($uid, $access_token);
				
				if($user['premium']['paid_to'] and ($user['premium']['paid_to']>=date('Y-m-d'))){
					return ["top"=>0, "list"=>0, "popup"=>["status"=>0, "screens"=>"5"]];
				}
			}
		} 
		return $result;
	}
	
	public function getTopBanner($city_id, $width=null, $uid=null, $access_token=null){
		if($city_id){ $wc = " AND `cities` like '%;{$city_id};%'"; }
		if(!empty($uid) and !empty($access_token)){
			$check = $this->model_feo_accounts->checkToken($uid, $access_token);
			if($check){
				$user = $this->model_feo_accounts->get_user($uid, $access_token);
				if($user['premium'] and $user['premium']['paid_to'] and ($user['premium']['paid_to']>=date('Y-m-d'))){
					
				}
				else {
					//$last = $this->banner_log->getItemWhere("`uid`={$uid} AND `type`=1", "*", "`id` DESC");
					if(!empty($last)) $w = "`position`=1 AND `on_off`=1 AND `id`!='{$last['bid']}' {$wc}"; else $w = "`position`=1 AND `on_off`=1 AND `cities` like '%;{$city_id};%'";
					$result = $this->banner->getItemWhere($w, "*", "RAND()");
					$this->banner_log->Insert(["bid"=>$result['id'], "type"=>$result['position'], "uid"=>$uid, "date"=>date('Y-m-d'), "show"=>date('Y-m-d H:i:s')]);
				}
			}
			else $result = $this->banner->getItemWhere("`position`=1 AND `on_off`=1 {$wc}", "*", "RAND()");
		} else $result = $this->banner->getItemWhere("`position`=1 AND `on_off`=1 {$wc}", "*", "RAND()");
		
		if($result) $this->banner->Update(["impressions"=>$result['impressions']+1], $result['id']);
		
		if($width<=600 and !empty($result['img480'])){
			$result['img'] = $result['img480'];
		}
		elseif($width<=1020 and !empty($result['img760'])){
			$result['img'] = $result['img760'];
		}
		unset($result['img480']); unset($result['img480_id']);
		unset($result['img760']); unset($result['img760_id']);
		
		list($width, $height, $type) = getimagesize($result['img']);
		if($result){
		$result['width'] = $width;
		$result['height'] = $height;
		}
		return $result;
	}
	
	public function getListBanner($city_id, $uid=null, $access_token=null){
		if($city_id){ $wc = " AND `cities` like '%;{$city_id};%'"; }
		if(!empty($uid) and !empty($access_token)){
			$check = $this->model_feo_accounts->checkToken($uid, $access_token);
			if($check){
				$user = $this->model_feo_accounts->get_user($uid, $access_token);
				if($user['premium']['paid_to'] and ($user['premium']['paid_to']>=date('Y-m-d'))){
					
				}
				else {
					//$last = $this->banner_log->getItemWhere("`uid`={$uid} AND `type`=2", "*", "`id` DESC");
					if(!empty($last)) $w = "`position`=2 AND `on_off`=1 AND `id`!='{$last['bid']}' {$wc}"; else $w = "`position`=2 AND `on_off`=1 AND `cities` like '%;{$city_id};%'";
					$result = $this->banner->getItemWhere($w, "*", "RAND()");
					$this->banner_log->Insert(["bid"=>$result['id'], "type"=>$result['position'], "uid"=>$uid, "date"=>date('Y-m-d'), "show"=>date('Y-m-d H:i:s')]);
				}
			}
			else $result = $this->banner->getItemWhere("`position`=2 AND `on_off`=1 {$wc}", "*", "RAND()");
		} else $result = $this->banner->getItemWhere("`position`=2 AND `on_off`=1 {$wc}", "*", "RAND()");
		if($result) $this->banner->Update(["impressions"=>$result['impressions']+1], $result['id']);
		
		if($width<=600 and !empty($result['img480'])){
			$result['img'] = $result['img480'];
		}
		elseif($width<=1020 and !empty($result['img760'])){
			$result['img'] = $result['img760'];
		}
		unset($result['img480']); unset($result['img480_id']);
		unset($result['img760']); unset($result['img760_id']);
		
		list($width, $height, $type) = getimagesize($result['img']);
		if($result){
		$result['width'] = $width;
		$result['height'] = $height;
		}
		
		return $result;
	}
	
	public function getPopupBanner($city_id, $uid=null, $access_token=null){
		if($city_id){ $wc = " AND `cities` like '%;{$city_id};%'"; }
		if(!empty($uid) and !empty($access_token)){
			$check = $this->model_feo_accounts->checkToken($uid, $access_token);
			if($check){
				$user = $this->model_feo_accounts->get_user($uid, $access_token);
				if($user['premium']['paid_to'] and ($user['premium']['paid_to']>=date('Y-m-d'))){
					
				}
				else {
					//$last = $this->banner_log->getItemWhere("`uid`={$uid} AND `type`=3", "*", "`id` DESC");
					if(!empty($last)) $w = "`position`=3 AND `on_off`=1 AND `id`!='{$last['bid']}' {$wc}"; else $w = "`position`=3 AND `on_off`=1 AND `cities` like '%;{$city_id};%'";
					$result = $this->banner->getItemWhere($w, "*", "RAND()");
					$this->banner_log->Insert(["bid"=>$result['id'], "type"=>$result['position'], "uid"=>$uid, "date"=>date('Y-m-d'), "show"=>date('Y-m-d H:i:s')]);
				}
			}
			else $result = $this->banner->getItemWhere("`position`=3 AND `on_off`=1 {$wc}", "*", "RAND()");
		} 
		else $result = $this->banner->getItemWhere("`position`=3 AND `on_off`=1 {$wc}", "*", "RAND()");
		if($result) $this->banner->Update(["impressions"=>$result['impressions']+1], $result['id']);
		if($result){
			$result['width'] = 100;
			$result['height'] = 100;
			$result['timer'] = 5;
		if($width<=600 and !empty($result['img480'])){
			$result['img'] = $result['img480'];
		}
		elseif($width<=1020 and !empty($result['img760'])){
			$result['img'] = $result['img760'];
		}
		unset($result['img480']); unset($result['img480_id']);
		unset($result['img760']); unset($result['img760_id']);
		
		list($width, $height, $type) = getimagesize($result['img']);
		if($result){
		$result['width'] = $width;
		$result['height'] = $height;
		}
		
		}
		return $result;
	}

	
}
?>