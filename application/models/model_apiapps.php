<?php
class model_apiapps extends Model
{
	protected $model_feo_accounts;
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
	
	function __construct($config = array()) {
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
		
		$banner_config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "banners",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
					'type' => "INT(11) NOT NULL DEFAULT '1'", // 1-верхний, 2-списковый, 3-всплывающий
					'name' => "VARCHAR(255) NOT NULL DEFAULT ''",
					'link' => "VARCHAR(250) NOT NULL DEFAULT ''",
					'img' => "VARCHAR(250) NOT NULL DEFAULT ''",
					'html' => "TEXT NOT NULL DEFAULT ''",
					'date_start' => "DATE NOT NULL DEFAULT '0000-00-00'",
					'date_end' => "DATE NOT NULL DEFAULT '0000-00-00'",
					'on_off' => "INT(11) NOT NULL DEFAULT '1'",
					'impressions' => "INT(11) NOT NULL DEFAULT '0'",
					'clicks' => "INT(11) NOT NULL DEFAULT '0'",
					'controller' => "VARCHAR(50) NOT NULL DEFAULT ''",
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
		$this->banner = new Model($banner_config);
		
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
		
		$this->model_feo_accounts = new model_feo_accounts();
	
    }
	
	public function checkBanner($uid=null, $access_token=null){
		$top = $this->banner->getCountWhere("`type`=1")>0?1:0;
		$list = $this->banner->getCountWhere("`type`=2")>0?1:0;
		$popup = $this->banner->getCountWhere("`type`=3")>0?1:0;
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
	
	public function getTopBanner($uid=null, $access_token=null){
		if(!empty($uid) and !empty($access_token)){
			$check = $this->model_feo_accounts->checkToken($uid, $access_token);
			if($check){
				$user = $this->model_feo_accounts->get_user($uid, $access_token);
				if($user['premium']['paid_to'] and ($user['premium']['paid_to']>=date('Y-m-d'))){
					
				}
				else {
					$last = $this->banner_log->getItemWhere("`uid`={$uid} AND `type`=1", "*", "`id` DESC");
					if(!empty($last)) $w = "`type`=1 AND `id`!={$last['bid']}"; else $w = "`type`=1";
					$result = $this->banner->getItemWhere($w, "*", "RAND()");
					$this->banner_log->Insert(["bid"=>$result['id'], "type"=>$result['type'], "uid"=>$uid, "date"=>date('Y-m-d'), "show"=>date('Y-m-d H:i:s')]);
				}
			}
			else $result = $this->banner->getItemWhere("`type`=1", "*", "RAND()");
		} else $result = $this->banner->getItemWhere("`type`=1", "*", "RAND()");
		if($result) $this->banner->Update(["impressions"=>$result['impressions']+1], $result['id']);
		return $result;
	}
	
	public function getListBanner($uid=null, $access_token=null){
		if(!empty($uid) and !empty($access_token)){
			$check = $this->model_feo_accounts->checkToken($uid, $access_token);
			if($check){
				$user = $this->model_feo_accounts->get_user($uid, $access_token);
				if($user['premium']['paid_to'] and ($user['premium']['paid_to']>=date('Y-m-d'))){
					
				}
				else {
					$last = $this->banner_log->getItemWhere("`uid`={$uid} AND `type`=2", "*", "`id` DESC");
					if(!empty($last)) $w = "`type`=2 AND `id`!={$last['bid']}"; else $w = "`type`=2";
					$result = $this->banner->getItemWhere($w, "*", "RAND()");
					$this->banner_log->Insert(["bid"=>$result['id'], "type"=>$result['type'], "uid"=>$uid, "date"=>date('Y-m-d'), "show"=>date('Y-m-d H:i:s')]);
				}
			}
			else $result = $this->banner->getItemWhere("`type`=2", "*", "RAND()");
		} else $result = $this->banner->getItemWhere("`type`=2", "*", "RAND()");
		if($result) $this->banner->Update(["impressions"=>$result['impressions']+1], $result['id']);
		return $result;
	}
	
	public function getPopupBanner($uid=null, $access_token=null){
		if(!empty($uid) and !empty($access_token)){
			$check = $this->model_feo_accounts->checkToken($uid, $access_token);
			if($check){
				$user = $this->model_feo_accounts->get_user($uid, $access_token);
				if($user['premium']['paid_to'] and ($user['premium']['paid_to']>=date('Y-m-d'))){
					
				}
				else {
					$last = $this->banner_log->getItemWhere("`uid`={$uid} AND `type`=3", "*", "`id` DESC");
					if(!empty($last)) $w = "`type`=3 AND `id`!={$last['bid']}"; else $w = "`type`=3";
					$result = $this->banner->getItemWhere($w, "*", "RAND()");
					$this->banner_log->Insert(["bid"=>$result['id'], "type"=>$result['type'], "uid"=>$uid, "date"=>date('Y-m-d'), "show"=>date('Y-m-d H:i:s')]);
				}
			}
			else $result = $this->banner->getItemWhere("`type`=3", "*", "RAND()");
		} 
		else $result = $this->banner->getItemWhere("`type`=3", "*", "RAND()");
		if($result) $this->banner->Update(["impressions"=>$result['impressions']+1], $result['id']);
		return $result;
	}

	
}
?>