<?php
class model_feo_accounts extends Model
{
	protected $premium;
	protected $devices;
	protected $topics;
	protected $emails;
	protected $phones;
	protected $vk;
	protected $od;
	protected $fb;
	protected $passwords;
	
	public function premium(){
		return $this->premium;
	}
	
	public function devices(){
		return $this->devices;
	}
	
	public function topics(){
		return $this->topics;
	}
	
	public function emails(){
		return $this->emails;
	}
	
	public function phones(){
		return $this->phones;
	}
	
	public function vk(){
		return $this->vk;
	}
	
	public function od(){
		return $this->od;
	}
	
	public function fb(){
		return $this->fb;
	}
	
	public function passwords(){
		return $this->passwords;
	}
	
	function __construct($config = array()) {
		$config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "accounts",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "TEXT NULL DEFAULT NULL",
				'login' => "TEXT NULL DEFAULT NULL",
				'password' => "TEXT NULL DEFAULT NULL",
				'password_md5' => "	VARCHAR(32) NOT NULL",
				'permisions' => "INT(11) NULL DEFAULT NULL",
				'email' => "TEXT NULL DEFAULT NULL",
				'phone' => "VARCHAR(31) NULL DEFAULT NULL",
				'pol' => "INT(11) NULL DEFAULT NULL",
				'bdate' => "DATE NULL DEFAULT NULL",
				'city' => "TEXT NULL DEFAULT NULL",
				'city_id' => "INT(11) NULL DEFAULT NULL",
				'about' => "TEXT NULL DEFAULT NULL",
				'site' => "TEXT NULL DEFAULT NULL",
				'ava_file' => "TEXT NULL DEFAULT NULL",
				'photo_file' => "TEXT NULL DEFAULT NULL",
				'rating' => "DOUBLE NULL DEFAULT NULL",
				'join_date' => "DATETIME NULL DEFAULT NULL",
				'login_unix' => "INT(11) NULL DEFAULT NULL",
				'old_id' => "INT(11) NULL DEFAULT NULL",
				'i_fam' => "TEXT NULL DEFAULT NULL",
				'i_name' => "TEXT NULL DEFAULT NULL",
				'on_off' => "ENUM('0', '1') NULL DEFAULT '1'",
				'sert_level' => "ENUM('0', '1', '2', '3') NULL DEFAULT '0'",
				'sert_count' => "INT(11) NULL DEFAULT '20'",
				'sert_req' => "ENUM('0', '1') NULL DEFAULT '0'",
				'sert_req_dtu' => "INT(11) NULL DEFAULT NULL",
				'sert_level_locked' => "ENUM('-1', '0', '1', '2', '3') NULL DEFAULT '-1'",
				'sert_count_locked' => "INT(11) NULL DEFAULT '-1'",
				'sert_end_date' => "DATE NULL DEFAULT NULL",
				'sert_end_level' => "ENUM('-1', '0', '1', '2', '3') NULL DEFAULT '-1'",
				'sert_end_count' => "INT(11) NULL DEFAULT '-1'",
				'last_cookie' => "INT(11) NULL DEFAULT NULL",
				'last_ip' => "VARCHAR(32) NULL DEFAULT NULL",
				'invite_code' => "VARCHAR(10) NULL DEFAULT NULL",				),
			"index" => array(
				
			),
			"unique" => array(
				'invite_code' => array('invite_code'),
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
		
		$premium_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "accounts_premium",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'uid' => "INT(11) NOT NULL",
				'is_paid' => "INT(11) NOT NULL DEFAULT 1",
				'paid_to' => "DATE NOT NULL",
				),
			"index" => array(
				
			),
			"unique" => array(
				'uid' => array('uid'),
			),
			"fulltext" => array(
				
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		
		$this->premium = new Model($premium_config);
		
		$devices_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "accounts_devices",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'uid' => "INT(11) NOT NULL",
				'access_token' => "VARCHAR(50) NOT NULL DEFAULT ''",
				'os' => "VARCHAR(50) NOT NULL DEFAULT ''",
				'device' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'push_token' => "VARCHAR(100) NOT NULL DEFAULT ''",
				'user_agent' => "VARCHAR(250) NOT NULL DEFAULT ''",
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
		
		$this->devices = new Model($devices_config);
		
		$topics_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "accounts_topics",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'uid' => "INT(11) NOT NULL",
				'access_token' => "VARCHAR(50) NOT NULL DEFAULT ''",
				'topic' => "VARCHAR(50) NOT NULL DEFAULT ''",
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
		
		$this->topics = new Model($topics_config);
		
		$emails_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "accounts_emails",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'uid' => "INT(11) NOT NULL",
				'email' => "VARCHAR(128) NOT NULL",
				'date' => "DATE NOT NULL",
				'time' => "TIME NOT NULL",
				'code' => "VARCHAR(10) NOT NULL",
				'code_time' => "INT(11) NOT NULL",
				'checked' => "ENUM('0', '1') NOT NULL DEFAULT '0'",
				'on_off' => "ENUM('0', '1') NOT NULL DEFAULT '1'",
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
		
		$this->emails = new Model($emails_config);
		
		$phones_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "accounts_phones",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'uid' => "INT(11) NOT NULL",
				'country' => "VARCHAR(3) NOT NULL",
				'oper' => "VARCHAR(3) NOT NULL",
				'number' => "VARCHAR(7) NOT NULL",
				'phone' => "VARCHAR(13) NOT NULL",
				'code' => "VARCHAR(10) NOT NULL",
				'code_time' => "INT(11) NOT NULL",
				'checked' => "ENUM('0', '1') NOT NULL DEFAULT '0'",
				'on_off' => "ENUM('0', '1') NOT NULL DEFAULT '1'",
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
		
		$this->phones = new Model($phones_config);
		
		$oid_vk_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "oid_vk",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'aid' => "INT(11) NOT NULL",
				'soc_id' => "INT(11) NOT NULL",
				'nick' => "TEXT NULL",
				'fname' => "TEXT NULL",
				'lname' => "TEXT NULL",
				'email' => "TEXT NULL",
				'birthday' => "TEXT NULL",
				'avatar' => "TEXT NULL",
				'sex' => "TEXT NULL",
				'other' => "TEXT NULL",
				'friends' => "TEXT NULL",
				'friends_last' => "TEXT NULL",
				'city' => "INT(11) NULL",
				'country' => "INT(11) NULL",
				'avatar_50' => "TEXT NULL",
				'on_off' => "ENUM('0', '1') NOT NULL DEFAULT '1'",
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
		
		$this->vk = new Model($oid_vk_config);
		
		$oid_od_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "oid_od",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'aid' => "INT(11) NOT NULL",
				'soc_id' => "INT(11) NOT NULL",
				'birthday' => "DATE NOT NULL",
				'first_name' => "TEXT NULL",
				'last_name' => "TEXT NULL",
				'name' => "TEXT NULL",
				'gender' => "varchar(10) NULL",
				'has_email' => "enum('0', '1') NULL",
				'location_country' => "TEXT NULL",
				'location_city' => "TEXT NULL",
				'pic_1' => "TEXT NULL",
				'pic_2' => "TEXT NULL",
				'age' => "varchar(10) NULL",
				'on_off' => "ENUM('0', '1') NOT NULL DEFAULT '1'",
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
		
		$this->od = new Model($oid_od_config);
		
		$oid_fb_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "oid_fb",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'uid' => "INT(11) NOT NULL",
				'soc_id' => "INT(11) NOT NULL",
				'name' => "TEXT NULL",
				'first_name' => "TEXT NULL",
				'last_name' => "TEXT NULL",
				'link' => "TEXT NULL",
				'username' => "TEXT NULL",
				'gender' => "varchar(10) NULL",
				'timezone' => "varchar(10) NULL",
				'locale' => "varchar(10) NULL",
				'verified' => "enum('0', '1') NULL",
				'updated_time' => "int(11) NULL",
				'on_off' => "ENUM('0', '1') NOT NULL DEFAULT '1'",
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
		
		$this->fb = new Model($oid_fb_config);
		
		$passwords_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "",
            "name" => "accounts_passwords",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'uid' => "INT(11) NOT NULL",
				'password' => "varchar(10) NOT NULL",
				'date' => "DATE NOT NULL",
				'lifetime' => "INT(11) NOT NULL",
				'used' => "INT(11) NOT NULL",
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
		
		$this->passwords = new Model($passwords_config);
		
    }
	
	public function get_token(int $id, $imei){
		$access_token = md5("{$id}:{$imei}");
		$device = $this->devices->getItemWhere("`access_token`='{$access_token}'");
		if(!empty($device)){
			return $device['access_token'];
		}else {
			$Browser = new Browser();
			$os = $Browser->getPlatform();
			$user_agent = $Browser->getUserAgent();
			$data = [
				'uid' => $id,
				'access_token' => $access_token,
				'os' => $os,
				'device' => $imei,
				'user_agent' => $user_agent,
				'date' => date('Y-m-d'),
				'time' => date('H:i:s'),
			];
			$this->devices->Insert($data);
			return $access_token;
		}
	}
	
	public function checkToken($id, $access_token = null){
		$device = $this->devices->getItemWhere("`uid`={$id} AND `access_token`='{$access_token}'");
		if(!empty($device)){
			if($device['access_token']==$access_token){
				return true;
			}
			else return false;
		}
		else return false;
	}
	
	public function get_phones(int $user_id, $access_token = null){
		//if(empty($access_token)) return ["error"=>1, "message"=>"Неверный ключ доступа"];
		//if($this->checkToken($user_id, $access_token)){
		//$result = $this->phones->getItemsWhere("uid={$user_id} AND on_off='1'", 'id', null, null, "phone");
		$result = $this->get('phone')->from($this->phones)->where("uid={$user_id} AND on_off='1'")->commit('col');
		return $result;
		//} else return ["error"=>1, "message"=>"Неверный ключ доступа"];
	}
	
	public function get_phones_all(int $user_id){
		$result = $this->phones->getItemsWhere("uid={$user_id}", 'id', null, null);
		return $result;
	}

	public function add_phone($number, $user_id, $access_token = null){
		if(empty($access_token)) return ["error"=>1, "message"=>"Неверный ключ доступа"];
		if($this->checkToken($user_id, $access_token)){
		if(!empty($phone)){
			$phone = check_phone($phone);
			$result = $this->phones->getItemWhere("`country`='{$phone['country']}' AND `oper`='{$phone['oper']}' AND `number`='{$phone['number']}' AND `on_off`='1'");
			if(!empty($result)) return ["error"=>1, "message"=>"Phone is busy"];
			$result = $this->phones->getItemWhere("`country`='{$phone['country']}' AND `oper`='{$phone['oper']}' AND `number`='{$phone['number']}' AND `on_off`='0'");
			$need_new = false;
			if(!empty($result)){
				$phone_id = $result['id'];
				$code = $result['code'];
				$code_time = $result['code_time'];
				if (($code_time+60*60*24)<time()) {
					/* Прошли сутки - отправить еще раз */
					$need_new_code = true;
					$code = genCode(6,array(0,1,2,3,4,5,6,7,8,9));
					$code_time = time();
					$this->phones->Update(['on_off'=>'1', 'code'=>$code, 'code_time'=>$code_time], $phone_id);
				}
				else {$need_new = true;$need_new_code = true;}
			}
			if ($need_new){
				$code = genCode(6,array(0,1,2,3,4,5,6,7,8,9)); $code_time = time();
				$phone_id = $this->phones->Insert([
					'uid' => $user_id,
					'country' => $phone['country'],
					'oper' => $phone['oper'],
					'number' => $phone['number'],
					'phone' => $phone['phone'],
					'code' => $code,
					'code_time' => $code_time,
					'checked' => '0',
					'on_off' => '1',
				]);
			}
			if ($need_new_code) {
				SMS_GW_Send('feomedia app',$phone['phone'],'Код подтверждения номера '.$code);
			}
			if($phone_id){
			$result = $this->phones->getItem($phone_id);
			return $result;
			}
			
		}
		else return ["error"=>1, "message"=>"Phone is empty"];
		} else return ["error"=>1, "message"=>"Неверный ключ доступа"];
	}
	
	public function del_phone(int $user_id, int $phone_id, $access_token = null){
		if(empty($access_token)) return ["error"=>1, "message"=>"Неверный ключ доступа"];
		if($this->checkToken($user_id, $access_token)){
		$result = $this->phones->Update(['on_off'=>'0'], "`uid`='{$user_id}' AND `id`='{$phone_id}'");
		return $result;
		} else return ["error"=>1, "message"=>"Неверный ключ доступа"];
	}
	
	public function phone_send_code(int $user_id, int $phone_id, $access_token = null){
		if(empty($access_token)) return ["error"=>1, "message"=>"Неверный ключ доступа"];
		if($this->checkToken($user_id, $access_token)){
		$result = $this->phones->getItemWhere("`uid`='{$user_id}' AND `id`='{$phone_id}' AND `on_off`='1' AND `checked`='0'");
		if(!empty($result)){
			if (($result['code_time']+60*60*24)<time()) {
				$code = genCode(6,array(0,1,2,3,4,5,6,7,8,9));
				$time = time();
				$this->phones->Update(['code'=>$code, 'code_time'=>$time], $phone_id);
				$result['code']=$code;
				$result['code_time']=$time;
				SMS_GW_Send('feomedia app', $result['phone'], 'Код подтверждения номера '.$code);
				return ['success'=>1, 'phone'=>$result];
			} else { return ["error"=>1, "message"=>"SMS can not be sent more than once a day"]; }
		}
		else return ["error"=>1, "message"=>"Phone not found"];
		} else return ["error"=>1, "message"=>"Неверный ключ доступа"];
	}
	
	public function confirm_phone(int $user_id, int $phone_id, $code, $access_token = null){
		if(empty($access_token)) return ["error"=>1, "message"=>"Неверный ключ доступа"];
		if($this->checkToken($user_id, $access_token)){
		$result = $this->phones->getItemWhere("`uid`='{$user_id}' AND `id`='{$phone_id}' AND `on_off`='1' AND `checked`='0'");
		if(!empty($result)){
			if ($result['code']==$code) {
				$this->phones->Update(['checked'=>'1', 'code_time'=>$time], $phone_id);
				$result['checked'] = '1';
				return ['success'=>1, 'phone'=>$result];
			} else { return ["error"=>1, "message"=>"Incorect code"]; }
		}
		else return ["error"=>1, "message"=>"Phone not found"];
		} else return ["error"=>1, "message"=>"Неверный ключ доступа"];
	}
	
	public function get_emails(int $user_id, $access_token = null){
		//if(empty($access_token)) return ["error"=>1, "message"=>"Неверный ключ доступа"];
		//if($this->checkToken($user_id, $access_token)){
		//	return $this->emails->getItemsWhere("uid={$user_id} AND on_off='1'", 'id', null, null, "email");
			return $this->get('email')->from($this->emails)->where("uid={$user_id} AND on_off='1'")->commit('col');
		//} else return ["error"=>1, "message"=>"Неверный ключ доступа"];
	}
	
	public function del_email(int $user_id, int $email_id, $access_token = null){
		if(empty($access_token)) return ["error"=>1, "message"=>"Неверный ключ доступа"];
		if($this->checkToken($user_id, $access_token)){
		$result = $this->emails->Update(['on_off'=>'0'], "`uid`='{$user_id}' AND `id`='{$email_id}'");
		return $result;
		} else return ["error"=>1, "message"=>"Неверный ключ доступа"];
	}
	
	public function get_premium_info(int $id){
		$result = $this->premium->getItemWhere("uid={$id}", 'paid_to');
		if($result['paid_to']<=date('Y-m-d')) { $result=null; }
		return $result;
	}
	
	public function get_user(int $id, $access_token = null){
		if(empty($access_token)) return ["error"=>1, "message"=>"Неверный ключ доступа"];
		if($this->checkToken($id, $access_token)){
		$result = $this->getItemWhere("id={$id} and on_off='1'", "id, name, login, email, phone, bdate, city, city_id, ava_file, join_date, i_fam, i_name, invite_code");
		if($result){
			$result['phones'] = $this->get_phones($result['id'], $access_token);
			$result['emails'] = $this->get_emails($result['id'], $access_token);
			$result['premium'] = $this->get_premium_info($result['id']);
			if(empty($result['phone']) and !empty($result['phones'])){ $result['phone'] = $result['phones'][0]; }
			if(empty($result['email']) and !empty($result['emails'])){ $result['email'] = $result['emails'][0]; }
			if(empty($result['ava_file'])) $result['ava_file'] = "https://gorod24.online/application/views/gorod24/img/no-ava.png";
			return $result;
		}
		else return ["error"=>1, "message"=>"Нет такого пользователя"];
		} else return ["error"=>1, "message"=>"Неверный ключ доступа"];
	}
	
	public function get_user_public(int $id){
		$result = $this->getItemWhere("id={$id} and on_off='1'", "id, name, bdate, city, city_id, ava_file, join_date, i_fam, i_name");
		if($result){
			if(empty($result['ava_file'])) $result['ava_file'] = "https://gorod24.online/application/views/gorod24/img/no-ava.png";
			return $result;
		}
		else return ["error"=>1, "message"=>"Нет такого пользователя"];
	}
	
	function LOGIN_PassGen($login, $password){
		return sha1(strtoupper($login).':'.strtoupper($password));
	}
	
	public function send_temp_password($login){
		$login = trim($login);
		//$login = str_replace(['+'], '', $login);
		//$ch1 = substr($login,0,1);
		//if($ch1==7 or $ch1==8){ $login = '+7'.substr($login,1); }
		$user = $this->getItemWhere("(`login`='{$login}' OR `email`='{$login}') and on_off='1'", "id, name, login, password, email, phone, bdate, city, city_id, ava_file, join_date, i_fam, i_name");
		if(empty($user)){
			$user = $this->get("`a`.id, `a`.name, `a`.login, `a`.login, `a`.password, `a`.email, `a`.phone, `a`.bdate, `a`.city, `a`.city_id, `a`.ava_file, `a`.join_date, `a`.i_fam, `a`.i_name")->from(["`new_feo_ua`.`accounts` AS `a`", "`new_feo_ua`.`accounts_emails` AS `e`"])->where("`e`.`email`='{$login}' AND `e`.`on_off`='1' AND `e`.`checked`='1' AND `e`.`uid`=`a`.`id`")->limit(1)->commit('row');
		}
		if(empty($user)){
			$phone = check_phone($login);
			$user = $this->get("`a`.id, `a`.name, `a`.login, `a`.password, `a`.email, `a`.phone, `a`.bdate, `a`.city, `a`.city_id, `a`.ava_file, `a`.join_date, `a`.i_fam, `a`.i_name")->from(["`new_feo_ua`.`accounts` AS `a`", "`new_feo_ua`.`accounts_phones` AS `e`"])->where("`e`.`number`='{$phone['number']}' AND `e`.`on_off`='1' AND `e`.`uid`=`a`.`id`")->limit(1)->commit('row');
		}
		if(empty($user)) return ["success"=>0, "code"=>101, "message"=>"Вы еще не зарегистрированы на портале. {$login}"];
		
		$check = $this->passwords->getCountWhere("`uid`={$user['id']} AND `date`=CURDATE() AND `lifetime`>=".time());
		if($check>0) return ["success"=>0, "code"=>110, "message"=>"Временный пароль уже отправлен. Подождите немного..."];
		$phones = $this->get_phones_all($user['id']);
		if(!empty($phones)){
			$data = [
				'uid' => $user['id'],
				'password' => genCode(6,array(0,1,2,3,4,5,6,7,8,9)),
				'date' => date("Y-m-d"),
				'lifetime' => time() + (60 * 15),
				'used' => 0,
			];
			$this->passwords->Insert($data);
			foreach($phones as $phone){
				if($phone['country']=='+7' and $phone['on_off']=='1'){
					SMS_GW_Send('feomedia app', $phone['phone'], 'Ваш временный пароль: '.$data['password']);
				}
			}
			return ["success"=>1, "code"=>110, "message"=>"Временный пароль отправлен."];
		} 
		else return ["success"=>0, "code"=>101, "message"=>"Вы еще не зарегистрированы на портале."];
	}
	
	public function login_byTempPassword($login, $password, $imei){
		$login = trim($login);
		$login = str_replace(['+'], '', $login);
		if(!$imei) return ["error"=>1, "code"=>100, "message"=>"IMEI is empty"];
		$result = $this->getItemWhere("(`login`='{$login}' OR `email`='{$login}') and on_off='1'", "id, name, login, email, phone, bdate, city, city_id, ava_file, join_date, i_fam, i_name");
		if(empty($result)){
			$result = $this->get("`a`.id, `a`.name, `a`.login, `a`.email, `a`.phone, `a`.bdate, `a`.city, `a`.city_id, `a`.ava_file, `a`.join_date, `a`.i_fam, `a`.i_name")->from(["`new_feo_ua`.`accounts` AS `a`", "`new_feo_ua`.`accounts_emails` AS `e`"])->where("`e`.`email`='{$login}' AND `e`.`on_off`='1' AND `e`.`checked`='1' AND `e`.`uid`=`a`.`id`")->limit(1)->commit('row');
		}
		if(empty($result)){
			$check_phone = check_phone($login);
			$result = $this->get("`a`.id, `a`.name, `a`.login, `a`.email, `a`.phone, `a`.bdate, `a`.city, `a`.city_id, `a`.ava_file, `a`.join_date, `a`.i_fam, `a`.i_name")->from(["`new_feo_ua`.`accounts` AS `a`", "`new_feo_ua`.`accounts_phones` AS `e`"])->where("`e`.`number`='{$check_phone['number']}' AND `e`.`on_off`='1' AND `e`.`uid`=`a`.`id`")->limit(1)->commit('row');
		}
		if(!empty($result)){
			$time = time();
			$tempPassword = $this->passwords->getItemWhere("`uid`='{$result['id']}' AND `lifetime`>={$time}", "*", "`lifetime` DESC");
			if(!empty($tempPassword) AND $tempPassword['used']=='0'){
			if($tempPassword['password'] == $password){
				$this->passwords->Update(['used'=>'1'], $tempPassword['id']);
				$phone = $this->phones->getItemWhere("`number`='{$check_phone['number']}'");
				if($phone['checked']=='0'){ $this->phones->Update(['checked'=>"1"], $phone['id']); }
				$result['access_token'] = $this->get_token($result['id'], $imei);
				$result['phones'] = $this->get_phones($result['id'], $result['access_token']);
				$result['emails'] = $this->get_emails($result['id'], $result['access_token']);
				if(empty($result['phone']) and !empty($result['phones'])) $result['phone'] = $result['phones'][0];
				if(empty($result['email']) and !empty($result['emails'])) $result['email'] = $result['emails'][0];
				if(empty($result['ava_file'])) $result['ava_file'] = "https://gorod24.online/application/views/gorod24/img/no-ava.png";
				$result['premium'] = $this->get_premium_info($result['id']);
				return $result;
			} else return ["error"=>1, "code"=>103, "message"=>"Неверный логин или пароль"];
			} else return ["error"=>1, "code"=>102, "message"=>"Пароль уже был использован или истек срок действия пароля."];
		}
		else return ["error"=>1, "code"=>101, "message"=>"Неверный логин или пароль"];
	}
	
	public function login($login, $password, $imei){
		$login = trim($login);
		if(!$imei) return ["error"=>1, "code"=>100, "message"=>"IMEI is empty"];
		$result = $this->getItemWhere("(`login`='{$login}' OR `email`='{$login}') and on_off='1'", "id, name, login, password, password_md5, permisions, email, phone, pol, bdate, city, city_id, ava_file, photo_file, rating, join_date, i_fam, i_name, invite_code");
		if(empty($result)){
			$result = $this->get("`a`.id, `a`.name, `a`.login, `a`.login, `a`.password, `a`.password_md5, `a`.email, `a`.phone, `a`.pol, `a`.bdate, `a`.city, `a`.city_id, `a`.ava_file, `a`.photo_file, `a`.rating, `a`.join_date, `a`.i_fam, `a`.i_name, `a`.invite_code")->from(["`new_feo_ua`.`accounts` AS `a`", "`new_feo_ua`.`accounts_emails` AS `e`"])->where("`e`.`email`='{$login}' AND `e`.`on_off`='1' AND `e`.`checked`='1' AND `e`.`uid`=`a`.`id`")->limit(1)->commit('row');
		}
		if(empty($result)){
			$phone = check_phone($login);
			$result = $this->get("`a`.id, `a`.name, `a`.login, `a`.password, `a`.password_md5, `a`.permisions, `a`.email, `a`.phone, `a`.pol, `a`.bdate, `a`.city, `a`.city_id, `a`.ava_file, `a`.photo_file, `a`.rating, `a`.join_date, `a`.i_fam, `a`.i_name, `a`.invite_code")->from(["`new_feo_ua`.`accounts` AS `a`", "`new_feo_ua`.`accounts_phones` AS `e`"])->where("`e`.`number`='{$phone['number']}' AND `e`.`on_off`='1' AND `e`.`checked`='1' AND `e`.`uid`=`a`.`id`")->limit(1)->commit('row');
		}
		if(!empty($result)){
			$pass_gen = $this->LOGIN_PassGen($result['login'], $password);
			$pass_old = md5($password);
			if($result['password'] == $pass_gen OR $result['password_md5'] == $pass_old){
				$result['access_token'] = $this->get_token($result['id'], $imei);
				$result['phones'] = $this->get_phones($result['id'], $result['access_token']);
				$result['emails'] = $this->get_emails($result['id'], $result['access_token']);
				if(empty($result['phone']) and !empty($result['phones'])) $result['phone'] = $result['phones'][0];
				if(empty($result['email']) and !empty($result['emails'])) $result['email'] = $result['emails'][0];
				if(empty($result['ava_file'])) $result['ava_file'] = "https://gorod24.online/application/views/gorod24/img/no-ava.png";
				$result['premium'] = $this->get_premium_info($result['id']);
				unset($result['password']);
				unset($result['password_md5']);
				return $result;
			}
			else return ["error"=>1, "message"=>"Неверный логин или пароль"];
		}
		else return ["error"=>1, "message"=>"Неверный логин или пароль"];
	}
	
	public function getUniqCode(){
		$code = genCode(6,array(0,1,2,3,4,5,6,7,8,9, 'a','b','c','d','e','f','g','k','l','m','n','o','p','r'));
		$ch = $this->getCountWhere("`invite_code`='{$code}'");
		
		if($ch==0) 
			return $code;
		else 
			return self::getUniqCode();
	}
	
	public function register_user( array $data, $imei){
		if(!$imei) return ["error"=>1, "message"=>"IMEI is empty"];
		if(
			empty($data['login'])
			or empty($data['password'])
			or empty($data['email'])
			or empty($data['phone'])
			or empty($data['name'])
			or empty($data['surname'])
		){
			return ["error"=>1, "message"=>"Не достаточно данных"];
		}
		if (strlen($data['password'])<6) {
			return ["error"=>1, "message"=>"Пароль должен быть не менее 6-ти символов"];
		}
		$data['phone'] = str_replace(['+'], '', $data['phone']); $data['phone'] = '+'.$data['phone'];
		foreach($data as $key=>$val){ $data[$key] = trim($val);}
		$checkLogin = $this->getCountWhere("`login`='{$data['login']}'"); if($checkLogin>0) return ["error"=>1, "message"=>"Login is busy"];
		$checkEmail = $this->getCountWhere("`email`='{$data['email']}'"); if($checkEmail>0) return ["error"=>1, "message"=>"На указанный Вами email уже существует аккаунт. Вам на него отправлена ссылка для восстановления пароля, если Вы помните пароль от портала Фео.РФ войдите по нему"];
		$checkEmail2 = $this->emails->getCountWhere("`email`='{$data['email']}' AND `on_off`!='0'"); if($checkEmail2>0) return ["error"=>1, "message"=>"На указанный Вами email уже существует аккаунт. Вам на него отправлена ссылка для восстановления пароля, если Вы помните пароль от портала Фео.РФ войдите по нему"];
		$checkPhone = $this->getCountWhere("`phone`='{$data['phone']}'"); if($checkPhone>0) return ["error"=>1, "message"=>"На указанный Вами телефон уже существует аккаунт."];
		$checkPhone2 = $this->phones->getCountWhere("`phone`='{$data['phone']}' AND `on_off`!='0'"); if($checkPhone2>0) return ["error"=>1, "message"=>"На указанный Вами телефон уже существует аккаунт."];
		
		
		$data_in = [
			'name' => (!empty($data['nik'])?$data['nik']:$data['login']),
			'login' => $data['login'],
			'password' => $this->LOGIN_PassGen($data['login'], $data['password']),
			'permisions' => 2,
			'email' => $data['email'],
			'phone' => $data['phone'],
			'pol' => null,
			'bdate' => (!empty($data['bdate'])?$data['bdate']:'0000-00-00'),
			'city' => '',
			'city_id' => (!empty($data['city'])?$data['city']:1483),
			'about' => null,
			'ava_file' => (!empty($data['ava_file'])?$data['ava_file']:null),
			'join_date' => date('Y-m-d H:i:s'),
			'login_unix' => 0,
			'old_id' => 0,
			'i_name' =>$data['name'],
			'i_fam' =>$data['surname'],
			'on_off' =>'1',
			'last_ip' => getIp(),
			'invite_code' => self::getUniqCode(),
		];
		
		if($data_in['city_id']){
			$_model_cities = new model_cities();
			$city = $_model_cities->getItem($data_in['city_id']);
			$data_in['city'] = $city['city_title'];
		}
		
		$email_code = genCode(10);
		$user_id = $this->InsertUpdate($data_in);
		$this->emails->InsertUpdate([ "uid"=>$user_id, "email"=>$data['email'], "date"=>date('Y-m-d'), "time"=>date('H:i:s'), "code"=>$email_code, "checked"=>'0', "on_off"=>'1']);
		$phone = check_phone($data['phone']);
		$code = genCode(6,array(0,1,2,3,4,5,6,7,8,9));
		$this->phones->InsertUpdate([ "uid"=>$user_id, "country"=>$phone['country'], "oper"=>$phone['oper'], "number"=>$phone['number'], "phone"=>$phone['phone'], "code"=>$code, "code_time"=>time(), "checked"=>'0', "on_off"=>'1']);
		//SMS_GW_Send('feomedia app',$phone['phone'],'Код подтверждения номера '.$code);
			$result = $this->getItem($user_id, "id, name, login, permisions, email, phone, pol, bdate, city, city_id, ava_file, photo_file, rating, join_date, i_fam, i_name");
			$result['access_token'] = $this->get_token($result['id'], $imei);
			$result['phones'] = $this->get_phones($result['id'], $result['access_token']);
			$result['emails'] = $this->get_emails($result['id'], $result['access_token']);
			if(empty($result['phone']) and !empty($result['phones'])) $result['phone'] = $result['phones'][0];
			if(empty($result['email']) and !empty($result['emails'])) $result['email'] = $result['emails'][0];
			if(empty($result['ava_file'])) $result['ava_file'] = "https://gorod24.online/application/views/gorod24/img/no-ava.png";
			$result['premium'] = $this->get_premium_info($result['id']);
		return $result;
	}
	
	public function quickRegister_user( array $data, $imei){
		if(!$imei) return ["error"=>1, "message"=>"IMEI is empty"];
		if(
			empty($data['email'])
			or empty($data['phone'])
			or empty($data['name'])
		){
			return ["error"=>1, "message"=>"Не достаточно данных {$data['email']}"];
		}
		$data['password'] = genCode(6);
		$data['login'] = 'app-'.genCode(6);
		if (strlen($data['password'])<6) {
			return ["error"=>1, "message"=>"Пароль должен быть не менее 6-ти символов"];
		}
		$data['phone'] = str_replace(['+'], '', $data['phone']); $data['phone'] = '+'.$data['phone'];
	
		foreach($data as $key=>$val){ $data[$key] = trim($val);}
		$checkEmail = $this->getCountWhere("`email`='{$data['email']}'"); if($checkEmail>0) return ["error"=>1, "message"=>"На указанный Вами email уже существует аккаунт. Вам на него отправлена ссылка для восстановления пароля, если Вы помните пароль от портала Фео.РФ войдите по нему"];
		$checkEmail2 = $this->emails->getCountWhere("`email`='{$data['email']}' AND `on_off`!='0'"); if($checkEmail2>0) return ["error"=>1, "message"=>"На указанный Вами email уже существует аккаунт. Вам на него отправлена ссылка для восстановления пароля, если Вы помните пароль от портала Фео.РФ войдите по нему"];
		$checkPhone = $this->getCountWhere("`phone`='{$data['phone']}'"); if($checkPhone>0) return ["error"=>1, "message"=>"На указанный Вами телефон уже существует аккаунт."];
		$checkPhone2 = $this->phones->getCountWhere("`phone`='{$data['phone']}' AND `on_off`!='0'"); if($checkPhone2>0) return ["error"=>1, "message"=>"На указанный Вами телефон уже существует аккаунт."];
		
		$phone = check_phone($data['phone']);
		
		$data_in = [
			'name' => (!empty($data['name'])?$data['name']:$data['login']),
			'login' => $data['login'],
			'password' => $this->LOGIN_PassGen($data['login'], $data['password']),
			'permisions' => 2,
			'email' => $data['email'],
			'phone' => $phone['phone'],
			'pol' => null,
			'bdate' => (!empty($data['bdate'])?$data['bdate']:'0000-00-00'),
			'city' => '',
			'city_id' => (!empty($data['city'])?$data['city']:1483),
			'about' => null,
			'ava_file' => (!empty($data['ava_file'])?$data['ava_file']:null),
			'join_date' => date('Y-m-d H:i:s'),
			'login_unix' => 0,
			'old_id' => 0,
			'i_name' =>$data['name'],
			'i_fam' =>$data['surname'],
			'on_off' =>'1',
			'last_ip' => getIp(),
			'invite_code' => self::getUniqCode(),
		];
		
		if($data_in['city_id']){
			$_model_cities = new model_cities();
			$city = $_model_cities->getItem($data_in['city_id']);
			$data_in['city'] = $city['city_title'];
		}
		$user_id = $this->InsertUpdate($data_in);
		
		$email_code = genCode(10);
		$this->emails->InsertUpdate([ "uid"=>$user_id, "email"=>$data['email'], "date"=>date('Y-m-d'), "time"=>date('H:i:s'), "code"=>$email_code, "checked"=>'0', "on_off"=>'1']);
		
		$code = genCode(6,array(0,1,2,3,4,5,6,7,8,9));
		$this->phones->InsertUpdate([ "uid"=>$user_id, "country"=>$phone['country'], "oper"=>$phone['oper'], "number"=>$phone['number'], "phone"=>$phone['phone'], "code"=>$code, "code_time"=>time(), "checked"=>'0', "on_off"=>'1']);
		
		return $this->send_temp_password($phone['phone']);
		/*
			SMS_GW_Send('feomedia app',$phone['phone'],'Код подтверждения номера '.$code);
			$result = $this->getItem($user_id, "id, name, login, permisions, email, phone, pol, bdate, city, city_id, ava_file, photo_file, rating, join_date, i_fam, i_name");
			$result['access_token'] = $this->get_token($result['id'], $imei);
			$result['phones'] = $this->get_phones($result['id'], $result['access_token']);
			$result['emails'] = $this->get_emails($result['id'], $result['access_token']);
			$result['premium'] = $this->get_premium_info($result['id']);
		return $result;
		*/
	}
	
	public function update_user( array $data, $id, $access_token = null){
		if(empty($access_token)) return ["error"=>1, "message"=>"Неверный ключ доступа"];
		if($this->checkToken($id, $access_token)){
		$data_update = [];
		if(!empty(trim(addslashes($data['surname'])))) $data_update['i_fam'] = trim(addslashes($data['surname']));
		if(!empty(trim(addslashes($data['name'])))) $data_update['i_name'] = trim(addslashes($data['name']));
		if(!empty(trim(addslashes($data['nik'])))) $data_update['name'] = trim(addslashes($data['nik']));
		if(!empty(trim(addslashes($data['bdate'])))) $data_update['bdate'] = trim(addslashes($data['bdate']));
		if(!empty(trim(addslashes($data['city'])))) { 
													$data_update['city_id'] = (int)$data['city']; 
													$_model_cities = new model_cities();
													$city = $_model_cities->getItem($data_update['city_id']);
													$data_update['city'] = $city['city_title'];
													}
		if(!empty(trim(addslashes($data['ava_file'])))) $data_update['ava_file'] = trim(addslashes($data['ava_file']));
		if(!empty($data_update)){
			$this->Update($data_update, $id);
			$result = $this->getItem($id);
				
				$result['access_token'] = $access_token;
				$result['phones'] = $this->get_phones($result['id'], $result['access_token']);
				$result['emails'] = $this->get_emails($result['id'], $result['access_token']);
				if(empty($result['phone']) and !empty($result['phones'])) $result['phone'] = $result['phones'][0];
				if(empty($result['email']) and !empty($result['emails'])) $result['email'] = $result['emails'][0];
				if(empty($result['ava_file'])) $result['ava_file'] = "https://gorod24.online/application/views/gorod24/img/no-ava.png";
				$result['premium'] = $this->get_premium_info($result['id']);
				unset($result['password']);
				unset($result['password_md5']);
			
			return $result;
		}
		else {
			return ["error"=>1, "message"=>"Nothing to update"];
		}
		} else return ["error"=>1, "message"=>"Неверный ключ доступа"];
	}
	
	public function user_change_password($password, $id, $access_token = null){
		if(empty($access_token)) return ["error"=>1, "message"=>"Неверный ключ доступа"];
		if($this->checkToken($id, $access_token)){
		$result = $this->getItem($id);
		if(!empty($result)){
			$new_password = $this->LOGIN_PassGen($result['login'], $password);
			$this->Update(['password' =>$new_password], $id);
		}
		else return ["error"=>1, "message"=>"Нет такого пользователя"];
		} else return ["error"=>1, "message"=>"Неверный ключ доступа"];
	}
	
	public function get_user_vk(int $id, $data=null){
		$result = $this->vk->getItemWhere("soc_id={$id} and on_off='1'");
		if($result){
			$account = $this->getItem($result['aid'], "id, name, login, permisions, email, phone, pol, bdate, city, city_id, ava_file, photo_file, rating, join_date, i_fam, i_name");
			if(!empty($account)){
			$account['phones'] = $this->get_phones($account['id']);
			$account['emails'] = $this->get_emails($account['id']);
			$account['premium'] = $this->get_premium_info($account['id']);
			}
			return ["soc-data"=>$result, "account"=>$account];
		}
		elseif(!empty($data) AND !empty($data['id'])) {
			$data['login'] = 'VK-'.$data['id'].'-'.genCode(6);
			$data['password'] = genCode(6);
			$name = trim($data['f_name'].' '.$data['l_name']);
			$account_data = [
				'name' => (!empty($name)?$name:$data['login']),
				'login' => $data['login'],
				'password' => $this->LOGIN_PassGen($data['login'], $data['password']),
				'permisions' => 2,
				'email' => ($data['email']?$data['email']:''),
				'phone' => ($data['phone']?$data['phone']:''),
				'pol' => $data['sex'],
				'bdate' => (!empty($data['born_date'])?$data['born_date']:'0000-00-00'),
				'city' => (!empty($data['city_title'])?$data['city_title']:''),
				'city_id' => (!empty($data['city_id'])?$data['city_id']:0),
				'about' => null,
				'ava_file' => (!empty($data['ava'])?$data['ava']:''),
				'join_date' => date('Y-m-d H:i:s'),
				'login_unix' => 0,
				'old_id' => 0,
				'i_name' =>$data['f_name'],
				'i_fam' =>$data['l_name'],
				'on_off' =>'1',
				'last_ip' => getIp(),
				'invite_code' => self::getUniqCode(),
			];
			$uid = $this->Insert($account_data);
			$oidfb_id = $this->vk->Insert([
				'aid' => $uid,
				'soc_id' => $data['id'],
				'nick' => $name,
				'lname' => $data['l_name'],
				'fname' => $data['f_name'],
				'email' => ($data['email']?$data['email']:''),
				'birthday' => (!empty($data['born_date'])?$data['born_date']:'0000-00-00'),
				'avatar' => (!empty($data['ava'])?$data['ava']:''),
				'sex' => $data['sex'],
				'other' => '',
				'friends' => '',
				'friends_last' => '0',
				'city' => $data['city_id'],
				'country' => '',
				'avatar_50' => (!empty($data['ava'])?$data['ava']:''),
				'on_off' => 1,
			]);
			return $this->get_user_vk($data['id']);
		}
		else return ["error"=>1, "message"=>"Нет такого пользователя"];
	}
	
	public function get_user_od(int $id, $data=null){
		$result = $this->od->getItemWhere("soc_id={$id} and on_off='1'");
		if($result){
			$account = $this->getItem($result['aid'], "id, name, login, permisions, email, phone, pol, bdate, city, city_id, ava_file, photo_file, rating, join_date, i_fam, i_name");
			if(!empty($account)){
			$account['phones'] = $this->get_phones($account['id']);
			$account['emails'] = $this->get_emails($account['id']);
			$account['premium'] = $this->get_premium_info($account['id']);
			}
			return ["soc-data"=>$result, "account"=>$account];
		}
		elseif(!empty($data) AND !empty($data['id'])) {
			$data['login'] = 'OD-'.$data['id'].'-'.genCode(6);
			$data['password'] = genCode(6);
			$name = trim($data['f_name'].' '.$data['l_name']);
			$account_data = [
				'name' => (!empty($name)?$name:$data['login']),
				'login' => $data['login'],
				'password' => $this->LOGIN_PassGen($data['login'], $data['password']),
				'permisions' => 2,
				'email' => ($data['email']?$data['email']:''),
				'phone' => ($data['phone']?$data['phone']:''),
				'pol' => $data['sex'],
				'bdate' => (!empty($data['born_date'])?$data['born_date']:'0000-00-00'),
				'city' => (!empty($data['city_title'])?$data['city_title']:''),
				'city_id' => (!empty($data['city_id'])?$data['city_id']:0),
				'about' => null,
				'ava_file' => (!empty($data['ava'])?$data['ava']:''),
				'join_date' => date('Y-m-d H:i:s'),
				'login_unix' => 0,
				'old_id' => 0,
				'i_name' =>$data['f_name'],
				'i_fam' =>$data['l_name'],
				'on_off' =>'1',
				'last_ip' => getIp(),
				'invite_code' => self::getUniqCode(),
			];
			$uid = $this->Insert($account_data);
			$oidfb_id = $this->od->Insert([
				'aid' => $uid,
				'soc_id' => $data['id'],
				'birthday' => (!empty($data['born_date'])?$data['born_date']:'0000-00-00'),
				'name' => $name,
				'last_name' => $data['l_name'],
				'first_name' => $data['f_name'],
				'username' => $data['login'],
				'gender' => $data['sex'],
				'has_email' => 1,
				'location_country' => '',
				'location_city' => $data['city_title'],
				'pic_1' => (!empty($data['ava'])?$data['ava']:''),
				'pic_2' => (!empty($data['ava'])?$data['ava']:''),
				'age' => '',
				'on_off' => 1,
			]);
			return $this->get_user_od($data['id']);
		}
		else return ["error"=>1, "message"=>"Нет такого пользователя"];
	}
	
	public function get_user_fb(int $id, $data=null){
		$result = $this->fb->getItemWhere("soc_id={$id} and on_off='1'");
		if($result){
			$account = $this->getItem($result['uid'], "id, name, login, permisions, email, phone, pol, bdate, city, city_id, ava_file, photo_file, rating, join_date, i_fam, i_name");
			if(!empty($account)){
			$account['phones'] = $this->get_phones_all($account['id']);
			$account['emails'] = $this->get_emails($account['id']);
			$account['premium'] = $this->get_premium_info($account['id']);
			}
			return ["soc-data"=>$result, "account"=>$account];
		}
		elseif(!empty($data) AND !empty($data['id'])) {
			$data['login'] = 'FB-'.$data['id'].'-'.genCode(6);
			$data['password'] = genCode(6);
			$name = trim($data['f_name'].' '.$data['l_name']);
			$account_data = [
				'name' => (!empty($name)?$name:$data['login']),
				'login' => $data['login'],
				'password' => $this->LOGIN_PassGen($data['login'], $data['password']),
				'permisions' => 2,
				'email' => ($data['email']?$data['email']:''),
				'phone' => ($data['phone']?$data['phone']:''),
				'pol' => $data['sex'],
				'bdate' => (!empty($data['born_date'])?$data['born_date']:'0000-00-00'),
				'city' => (!empty($data['city_title'])?$data['city_title']:0),
				'city_id' => (!empty($data['city_id'])?$data['city_id']:0),
				'about' => null,
				'ava_file' => (!empty($data['ava'])?$data['ava']:null),
				'join_date' => date('Y-m-d H:i:s'),
				'login_unix' => 0,
				'old_id' => 0,
				'i_name' =>$data['f_name'],
				'i_fam' =>$data['l_name'],
				'on_off' =>'1',
				'last_ip' => getIp(),
				'invite_code' => self::getUniqCode(),
			];
			$uid = $this->Insert($account_data);
			$oidfb_id = $this->fb->Insert([
				'uid' => $uid,
				'soc_id' => $data['id'],
				'name' => $name,
				'first_name' => $data['f_name'],
				'last_name' => $data['l_name'],
				'link' => 'https://www.facebook.com/profile.php?id='.$data['id'],
				'username' => $data['login'],
				'gender' => $data['sex'],
				'timezone' => '',
				'locale' => '',
				'verified' => '',
				'update_time' => 0,
				'on_off' => 1,
			]);
			return $this->get_user_fb($data['id']);
		}
		else return ["error"=>1, "message"=>"Нет такого пользователя"];
	}
	
	public function device_setPushToken($id, $access_token, $data){
		$result = $this->devices->getItemWhere("`uid`={$id} and `access_token`='{$access_token}'");
		if($result){
			$this->devices->Update([ 'os'=>$data['os'], 'push_token'=>$data['token']], $result['id']);
			return ["success"=>1, "message"=>"Токен успешно сохранен."];
		}
		else return ["error"=>1, "message"=>"Device not found"];
	}
	
	function genInvites(){
		
		$accounts = $this->
			//debug_query_once()->
			getItemsWhere("`invite_code` is null", null, 0, 10000);
		foreach($accounts as $account){
			$data = ['invite_code'=>self::getUniqCode()];
			$this->Update($data, $account['id']);
		}
		/**/
		
	}
	
}
?>