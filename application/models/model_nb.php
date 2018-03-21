<?php
class model_nb extends Model
{
	protected $monthes;
	protected $results; public function results(){ return $this->results; }
	protected $cities; public function cities(){ return $this->cities; }
	
	function __construct($config = array()) {
		$config = [
            "server" => "80.93.183.242",
            "database" => "thebest",
            "prefix" => "",
            "name" => "contests",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "con_id",
			"autoinit"  => false,
            "columns" => array(
				'con_name' => "VARCHAR(150) NOT NULL DEFAULT ''",
				'con_descr' => "TEXT NOT NULL DEFAULT ''",
				'con_url' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'con_start_date' => "DATE NOT NULL DEFAULT ''",
				'con_end_date' => "DATE NOT NULL DEFAULT ''",
				'con_main_img' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'con_soc_img' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'city_id' => "INT(11) NOT NULL DEFAULT '0'",
				'con_users' => "INT(11) NOT NULL DEFAULT '0'",
				'con_template' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'con_table_prefix' => "VARCHAR(50) NOT NULL DEFAULT ''",
				'con_table_sufix' => "VARCHAR(50) NOT NULL DEFAULT ''",
				'con_dop_sql' => "TEXT NULL DEFAULT NULL",
				'con_status' => "ENUM('0', '1') NOT NULL DEFAULT '1'",
				'con_app' => "INT(11) NOT NULL DEFAULT '0'",
				'con_adddate' => "DATETIME NOT NULL",
				'con_updatedate' => "DATETIME NOT NULL",
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
		parent::__construct($config);
		
		$results_config = [
            "server" => "80.93.183.242",
            "database" => "thebest",
            "prefix" => "",
            "name" => "results",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "res_id",
			"autoinit"  => false,
            "columns" => array(
				'res_contest' => "INT(11) NOT NULL DEFAULT '0'",
				'res_nom' => "INT(11) NOT NULL DEFAULT '0'",
				'res_nom_percent' => "FLOAT NULL DEFAULT NULL",
				'res_subnom' => "INT(11) NULL DEFAULT NULL",
				'res_pos' => "INT(11) NOT NULL DEFAULT '0'",
				'res_firm' => "INT(11) NOT NULL DEFAULT '0'",
				'res_votes' => "INT(11) NULL DEFAULT NULL",
				'res_percent' => "FLOAT NOT NULL DEFAULT '0'",
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
		$this->results = new Model($results_config);
		
		$cities_config = [
            "server" => "80.93.183.242",
            "database" => "nb",
            "prefix" => "",
            "name" => "mvc_cities",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "city_id",
			"autoinit"  => false,
            "columns" => array(
				'country' => "INT(11) NOT NULL DEFAULT '0'",
				'region' => "INT(11) NOT NULL DEFAULT '0'",
				'city_title' => "VARCHAR(50) NOT NULL DEFAULT ''",
				'city_area' => "VARCHAR(50) NULL DEFAULT NULL",
				'city_region' => "VARCHAR(50) NULL DEFAULT NULL",
				'url' => "VARCHAR(100) NULL DEFAULT NULL",
				'on_off' => "INT(11) NOT NULL DEFAULT 0",
				'zagl' => "VARCHAR(255) NOT NULL DEFAULT ''",
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
		$this->cities = new Model($cities_config);
		
		
		
		$this->monthes=array(
				1 => 'Января', 2 => 'Февраля', 3 => 'Марта', 4 => 'Апреля',
				5 => 'Мая', 6 => 'Июня', 7 => 'Июля', 8 => 'Августа',
				9 => 'Сентября', 10 => 'Октября', 11 => 'Ноября', 12 => 'Декабря'
			);
		
	}
	
	public function getCurrentContest($city_id, $user=null){
		$result = $this->getItemWhere("`con_status`='1' AND con_city={$city_id}  AND con_start_date<=CURDATE() AND CURDATE()<=con_end_date", "con_id as id, con_name as name, con_descr as description, con_table_prefix, con_table_sufix", "con_id");
		if(!empty($result)){
		if($user){
		$phone = $user['phones'][0]; $phone = preg_replace('~[^0-9]+~','',$phone); 
		$email = $user['emails'][0];
		$fields = "id_user as id, name, surname, second_name, sex, age, address, phone, email, valid, id_brend_random";
		if(!empty($user)) $anket = $this->db()->getRow("SELECT {$fields} FROM thebest.{$result['con_table_prefix']}user{$result['con_table_sufix']} WHERE `feo_uid`=?i", $user['id']);
		if(!empty($phone) and empty($anket)) 	$anket = $this->db()->getRow("SELECT {$fields} FROM thebest.{$result['con_table_prefix']}user{$result['con_table_sufix']} WHERE `phone`=?s", $phone);
		if(!empty($email) and empty($anket)) $anket = $this->db()->getRow("SELECT {$fields} FROM thebest.{$result['con_table_prefix']}user{$result['con_table_sufix']} WHERE `email`=?s", $email);
		$result['anketa'] = $anket;
		
		if(empty($anket)){
			$otherContests = $this->getItemsWhere("`con_status`='1' AND con_city!={$city_id}  AND con_start_date<=CURDATE() AND CURDATE()<=con_end_date", "con_id", null, null, "con_id as id, con_name as name, con_descr as description, con_table_prefix, con_table_sufix");
			
			foreach($otherContests as $contest){
				$other_anket = $this->db()->getRow("SELECT * FROM thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE `feo_uid`=?i", $user['id']);
				if(!empty($other_anket)){
					unset($other_anket['id_user']);
					$other_anket['feo_uid'] = $user['id'];
					$other_anket['ip_address'] = getIp();
					$other_anket['valid']=0;
					$other_anket['ulica']=0;
					$other_anket['id_brend_random']=0;
					$other_anket['motivation']=0;
					$other_anket['created']=date("Y-m-d H:i:s");
					$other_anket['confirmation_email']=0;
					$other_anket['email_moder']=0;
					$other_anket['priz']=0;
					$other_anket['zapol']=0;
					$other_anket['id_f_gest']=0;
					$other_anket['confirm_from_sms']=null;
					$other_anket['send_emails']=0;
					$other_anket['inviter']=null;
					$other_anket['money_add']=null;
					$this->db()->query("INSERT INTO thebest.{$result['con_table_prefix']}user{$result['con_table_sufix']} SET ?u", $other_anket);
					$anket_id = $this->db()->insertId();
					$other_anket = $this->db()->getRow("SELECT {$fields} FROM thebest.{$result['con_table_prefix']}user{$result['con_table_sufix']} WHERE `id_user`=?i", $anket_id);
					$anket = $other_anket;
					$result['anketa'] = $other_anket;
					break;
				}
			}
			
			
		}
		
		
		
		if(!empty($anket)){
			if($anket['valid']==1){
				$result['anketa']['currentstep'] = "succes";
			}
			elseif(!empty($anket['id_brend_random'])){
				$result['anketa']['currentstep'] = "step-3";
			}
			else {
				$result['anketa']['currentstep'] = "step-2"; 
			}
		}
		unset($result['con_table_prefix']);
		unset($result['con_table_sufix']);
		}
		}
		else {
			$zaglushka = $this->cities->getItem($city_id);		
			$result = ['id'=>0, "name"=>"", "description"=>"", "anketa"=>null, "image"=>$zaglushka['zagl']];
		}
		return $result;
	}
	
	public function getPredInContest($contest_id, $pred_id){
		$contest = $this->getItemWhere("`con_id`='{$contest_id}'", "con_id as id, con_name as name, con_descr as description, con_table_prefix, con_table_sufix", "con_id");
		if(!empty($contest)){
			$nom_id = $this->db()->GetOne("select id_n_brend from `thebest`.`{$contest['con_table_prefix']}brend_nomination{$contest['con_table_sufix']}` where `id_212`='{$pred_id}' ORDER BY `id_n_brend` ASC LIMIT 1");
			if($nom_id){
				return $nom_id;
			}
			else return false;
		}
		return false;
	}
	
	public function getAnketaContest($contest_id, $user){
		$result = $this->getItemWhere("`con_id`='{$contest_id}'", "con_id as id, con_name as name, con_descr as description, con_table_prefix, con_table_sufix", "con_id");
		if(!empty($result)){
		$phone = $user['phones'][0]; $phone = preg_replace('~[^0-9]+~','',$phone); 
		$email = $user['emails'][0];
		$fields = "id_user as id, name, surname, second_name, sex, age, address, phone, email, valid, id_brend_random";
		if(!empty($user)) $anket = $this->db()->getRow("SELECT {$fields} FROM thebest.{$result['con_table_prefix']}user{$result['con_table_sufix']} WHERE `feo_uid`=?s", $user['id']);
		if(!empty($phone) and empty($anket)) 	$anket = $this->db()->getRow("SELECT {$fields} FROM thebest.{$result['con_table_prefix']}user{$result['con_table_sufix']} WHERE `phone`=?s", $phone);
		if(!empty($email) and empty($anket)) $anket = $this->db()->getRow("SELECT {$fields} FROM thebest.{$result['con_table_prefix']}user{$result['con_table_sufix']} WHERE `email`=?s", $email);
		$result['anketa'] = $anket;
		if(!empty($anket)){
			if($anket['valid']==1){
				$result['anketa']['currentstep'] = "succes";
			}
			elseif(!empty($anket['id_brend_random'])){
				$result['anketa']['currentstep'] = "step-3";
			}
			else {
				$result['anketa']['currentstep'] = "step-2"; 
			}
		}
		unset($result['con_table_prefix']);
		unset($result['con_table_sufix']);
		}
		else {
			$zaglushka = $this->cities->getItem($city_id);		
			$result = ['id'=>0, "name"=>"", "description"=>"", "anketa"=>null, "image"=>$zaglushka['zagl']];
		}
		return $result;
	}
	
	public function getAnketaPercent($contest_id, $anketa_id){
		$contest = $this->getItemWhere("`con_id`='{$contest_id}'", "con_id as id, con_name as name, con_descr as description, con_table_prefix, con_table_sufix", "con_id");
		if(!empty($contest)){
			if(!empty($user)) $anket = $this->db()->getRow("SELECT {$fields} FROM thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE `feo_uid`=?s", $user['id']);
			$nominations = $this->db()->getOne("SELECT COUNT(*) FROM thebest.{$contest['con_table_prefix']}nomination{$contest['con_table_sufix']} WHERE 1");
			$votes = $this->db()->getOne("SELECT COUNT(*) FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']} WHERE `id_user`=?i", $anketa_id);
			$result = ( 100 / $nominations) * $votes;
		}
		else {
			$zaglushka = $this->cities->getItem($city_id);		
			$result = 0;
		}
		return $result;
	}
	
	public function getAnketa($contest, $id){
		$fields = "id_user as id, name, surname, second_name, sex, age, address, phone, email, valid, id_brend_random";
		$anket = $this->db()->getRow("SELECT {$fields} FROM thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE `feo_uid`=?i", $id);
		return $anket;
	}
	
	public function createAnketa($contest_id, $data, $user_id){
		$contest = $this->getItem($contest_id);
		$fields = "id_user as id, name, surname, second_name, sex, age, address, phone, email, valid, id_brend_random";
		$anket = $this->db()->getRow("SELECT {$fields} FROM thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE `phone`=?s OR `email`=?s", $data['phone'], $data['email']);
		if(empty($anket)){
			$data['phone'] = preg_replace('~[^0-9]+~','',$data['phone']); 
			$qdata = [
				'feo_uid' => $user_id,
				'ulica' => 0,
				'name' => $data['name'],
				'surname' => $data['surname'],
				'second_name' => $data['second_name'],
				'sex' => $data['sex'],
				'age' => $data['age'],
				'address' => ($data['address']?$data['address']:''),
				'phone' => $data['phone'],
				'email' => $data['email'],
				'ip_address' => getIp(),
				'valid' => 0,
				'id_brend_random' => 0,
				'motivation' => (int)$data['motivation'],
				'created' => date('Y-m-d H:i:s'),
				'confirmation_email' => 0,
				'email_moder' => 0,
				'priz' => 0,
				'zapol' => 0,
				'id_f_gest' => 0,
				'vk_id' => 0,
				'ok_id' => 0,
				'fb_id' => 0,
				'key' => substr(md5("{$data['phone']}-{$data['email']}-0-0-0"), 0, 6),
				'confirm_from_sms' => null,
				'inviter' => null,
				'money_add' => null,
			];
			$this->db()->query("INSERT INTO thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} SET ?u", $qdata);
			$anket_id = $this->db()->insertId();
			$fields = "id_user as id, name, surname, second_name, sex, age, address, phone, email, valid, id_brend_random";
			$anket = $this->db()->getRow("SELECT {$fields} FROM thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE `id_user`=?i", $anket_id);
		}
			if($anket['valid']==1){
				$anket['currentstep'] = "succes";
			}
			elseif(!empty($anket['id_brend_random'])){
				$anket['currentstep'] = "step-3";
			}
			else {
				$anket['currentstep'] = "step-2"; 
			}
		
		return $anket;
	}
	
	public function setStep2($contest_id, $anketa_id, $brend){
		$contest = $this->getItem($contest_id);
		$id_brend = $this->db()->GetOne("SELECT `id_brend` FROM `thebest`.`{$contest['con_table_prefix']}brend{$contest['con_table_sufix']}` WHERE `name` = ?s", $brend);
		if(empty($id_brend)){
			$this->db()->query("insert into `thebest`.`{$contest['con_table_prefix']}brend{$contest['con_table_sufix']}` (`name`, `date_ins`) values (?s, CURDATE())", $brend); 
			$id_brend = $this->db()->insertId();
		}
		$this->db()->query("UPDATE `thebest`.`{$contest['con_table_prefix']}user{$contest['con_table_sufix']}` SET `id_brend_random`=?i WHERE `id_user`=?i", $id_brend, $anketa_id);
		
		return ["success"=>1, "message"=>"Успешно", "brend_id"=>$id_brend];
	}
	
	public function getNominations($contest_id, $anketa_id=null){
		$contest = $this->getItem($contest_id);
		$nominations = $this->db()->GetAll("select id_nomination as id, nomination as name from `thebest`.`{$contest['con_table_prefix']}nomination{$contest['con_table_sufix']}` ORDER BY `nomination` ASC"); //выбираем все номинации
		if(!is_null($anketa_id)){
			$succes = 1;
		}
		foreach($nominations as $i=>$nomination){
			$not_use = [[ "id" => "0", "name" => "Не пользуюсь"]];
			$items = $this->db()->GetAll("select id_n_brend as id, nomination_brend as name from `thebest`.`{$contest['con_table_prefix']}brend_nomination{$contest['con_table_sufix']}` where `id_nomination`='{$nomination['id']}' ORDER BY `nomination_brend` ASC");
			$items = array_merge($not_use, $items);
			$nominations[$i]['items'] = $items;
			if(!is_null($anketa_id)){
				$selected = $this->db()->GetOne("select id_n_brend FROM `thebest`.`{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}` where `id_nomination`='{$nomination['id']}' and `id_user`=?i", $anketa_id);
				$nominations[$i]['result'] = $this->db()->GetOne("select COUNT(*) FROM `thebest`.`{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}` where `id_nomination`='{$nomination['id']}' and `id_user`=?i", $anketa_id);
				$nominations[$i]['selected'] = (int)$selected;
				if($nominations[$i]['result']==0) $succes = 0;
			}
		}
		if($succes==1){
			$this->db()->query("UPDATE `thebest`.`{$contest['con_table_prefix']}user{$contest['con_table_sufix']}` SET `valid`=1 WHERE `id_user`=?i", $anketa_id);
		}
		return $nominations;
	}
	
	public function setNomination($contest_id, $anketa_id, $nomination_id, $brend_id){
		$contest = $this->getItem($contest_id);
		$this->db()->query("delete from `thebest`.`{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}` where `id_user`=?i AND id_nomination=?i", $anketa_id, $nomination_id);
		$data = [
			'id_nomination' => $nomination_id,
			'id_n_brend' => $brend_id,
			'id_user' => $anketa_id,
			'vote_date' => date('Y-m-d H:i:s'),
			'vote_ip' => getIp(),
		];
		$this->db()->query("insert into `thebest`.`{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}` SET ?u", $data);
		$nominations = $this->db()->GetAll("select COUNT(*) FROM `thebest`.`{$contest['con_table_prefix']}nomination{$contest['con_table_sufix']}`");
		$votes = $this->db()->GetAll("select COUNT(*) FROM `thebest`.`{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}` WHERE id_user=?i", $anketa_id);
		if($nominations==$votes){
			$this->db()->query("UPDATE `thebest`.`{$contest['con_table_prefix']}user{$contest['con_table_sufix']}` SET `valid`=1 WHERE `id_user`=?i", $anketa_id);
		}
		
		return ["success"=>1, "message"=>"Успешно"];
	}
	
	public function getFirmNominations($contest_id, $brend_id, $user_id){
		$contest = $this->getItem($contest_id);
		$anketa = $this->getAnketa($contest, $user_id);
		$anketa_id = $anketa['id'];
		if(empty($anketa_id)){
			return false;
		}
		
		$nominations = $this->db()->GetAll("
			select 
				id_n_brend as id, 
				({$contest_id}) as contest_id, 
				({$anketa_id}) as anketa_id, 
				nomination_brend as name, 
				id_nomination as nomination_id, 
				(select `nomination` FROM `thebest`.`{$contest['con_table_prefix']}nomination{$contest['con_table_sufix']}` as `nomination` WHERE `nomination`.`id_nomination` = `brend_nomination`.`id_nomination`) as `nomination_name` ,
				(select `id_n_brend` FROM `thebest`.`{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}` as `votes` WHERE `votes`.`id_nomination` = `brend_nomination`.`id_nomination` and `votes`.`id_user`='{$anketa_id}') as `selected`,
				(select `nomination_brend` FROM `thebest`.`{$contest['con_table_prefix']}brend_nomination{$contest['con_table_sufix']}` as `t2` WHERE `t2`.`id_n_brend` = (select `id_n_brend` FROM `thebest`.`{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}` as `votes` WHERE `votes`.`id_nomination` = `brend_nomination`.`id_nomination` and `votes`.`id_user`='{$anketa_id}')) as `selected_name` 
			FROM 
				`thebest`.`{$contest['con_table_prefix']}brend_nomination{$contest['con_table_sufix']}` as `brend_nomination`
			WHERE 
				`id_n_brend`='{$brend_id}'");
		foreach($nominations as $i=>$nomination){
			if($nomination['selected']=='0'){
				$nominations[$i]['selected'] = null;
			}
		}
		return $nominations;
	}
	
	public function setCustomNomination($contest_id, $anketa_id, $nomination_id, $brend){
		$contest = $this->getItem($contest_id);
		$this->db()->query("delete from `thebest`.`{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}` where `id_user`=?i AND id_nomination=?i", $anketa_id, $nomination_id);
		
		$brend_id = $this->db()->GetOne("select id_n_brend from `thebest`.`{$contest['con_table_prefix']}brend_nomination{$contest['con_table_sufix']}` where `nomination_brend`=?s AND `id_nomination`=?i", $brend, $nomination_id);
		if(!$brend_id){
			$this->db()->query("insert into `thebest`.`{$contest['con_table_prefix']}brend_nomination{$contest['con_table_sufix']}` (`nomination_brend`, `id_nomination`, `date_ins`) values (?s, ?i, CURDATE())", $brend, $nomination_id);
			$brend_id = $this->db()->insertId();
		}
		
		$data = [
			'id_nomination' => $nomination_id,
			'id_n_brend' => $brend_id,
			'id_user' => $anketa_id,
			'vote_date' => date('Y-m-d H:i:s'),
			'vote_ip' => getIp(),
		];
		$this->db()->query("insert into `thebest`.`{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}` SET ?u", $data);
		$nominations = $this->db()->GetAll("select COUNT(*) FROM `thebest`.`{$contest['con_table_prefix']}nomination{$contest['con_table_sufix']}`");
		$votes = $this->db()->GetAll("select COUNT(*) FROM `thebest`.`{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}` WHERE id_user=?i", $anketa_id);
		if($nominations==$votes){
			$this->db()->query("UPDATE `thebest`.`{$contest['con_table_prefix']}user{$contest['con_table_sufix']}` SET `valid`=1 WHERE `id_user`=?i", $anketa_id);
		}
		
		return ["success"=>1, "message"=>"Successfully", "brend_id" => $brend_id];
	}
	
	public function getWeeksIntermediateResults($contest_id){
		$contest = $this->getItem($contest_id);
        $today = date('Y-m-d h:i:s');
		//$result = $this->get_week_list($today, (date("W", strtotime($contest['con_start_date']))-1));
		$result = $this->get_month_list($today, $contest['con_start_date'], $contest['con_end_date']);
		return $result; 
	}
	
	public function getIntermediateResults($contest_id, $week){
		$contest = $this->getItem($contest_id);
        $year = date("Y", strtotime($contest['con_start_date']));
		$start = (int)date("m", strtotime($contest_start));
		$end = (int)date("m", strtotime($date));
		
		$date_array = $this->get_monday($week, $year);
		for($i=$start;$i<=$end;$i++){
			if($i==$week){
			if($i == $start){ $day_ds = date("d", strtotime($contest_start)); $day_de = date("t", strtotime($contest_start));}
			elseif($i == $end) {
				$day_ds = "01";
				$day_de = date("d", time());
			}
			else {
				$day_ds = "01";
				$day_de = date("t", strtotime(date("{$year}-{$i}-01")));
			}
			
			$date_start = "{$year}-{$i}-{$day_ds}";
			$date_end = "{$year}-{$i}-{$day_de}";
			}
		}
		
		$ds = date("Y-m-d 00:00:00", strtotime($date_start));
		$de = date("Y-m-d 23:59:59", strtotime($date_end));
		$nominations = $this->db()->GetAll("SELECT id_nomination as id, nomination as name FROM `thebest`.`{$contest['con_table_prefix']}nomination{$contest['con_table_sufix']}`");
		foreach($nominations as $i=>$nomination){
			
			$nominations[$i]['items'] = $this->db()->GetAll("
				SELECT id_n_brend as id, nomination_brend as name,  
				0 as votes
				/*(SELECT COUNT(*) FROM `thebest`.`{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}` as v WHERE v.id_nomination=b.id_nomination AND v.id_n_brend=b.id_n_brend AND (v.vote_date BETWEEN STR_TO_DATE('{$ds}', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('{$de}', '%Y-%m-%d %H:%i:%s'))) as votes  */
				FROM `thebest`.`{$contest['con_table_prefix']}brend_nomination{$contest['con_table_sufix']}`as b WHERE `id_nomination`=?i ORDER BY `votes` DESC LIMIT 3
				", $nomination['id']);
				shuffle($nominations[$i]['items']);
		}
		return $nominations; 
	}
	
	function get_monday($weekNumber, $year){
		$time = strtotime($year . '-01-01 +' . ($weekNumber ). ' weeks');
		$mondayTime = strtotime('0 days', $time);
		$dayTimes = array ();
		for ($i = 0; $i < 7; ++$i) {
			$dayTimes[] = strtotime('+' . $i . ' days', $mondayTime);
		}
		return $dayTimes;
	}
	
	function get_month_list($date, $contest_start, $contest_end) {
		$year = (int)date("Y", strtotime($contest_start));
		$start = (int)date("m", strtotime($contest_start));
		$end = (int)date("m", strtotime($date));
		
		$result = [];
		
		for($i=$start;$i<=$end;$i++){
			if($i == $start){ $day_ds = date("d", strtotime($contest_start)); $day_de = date("t", strtotime($contest_start));}
			elseif($i == $end) {
				$day_ds = "01";
				$day_de = date("d", time());
			}
			else {
				$day_ds = "01";
				$day_de = date("t", strtotime(date("{$year}-{$i}-01")));
			}
			$result[] = ["week"=>$i, "title"=>$day_ds." ".$this->monthes[$i]." - ".$day_de." ".$this->monthes[$i]];
			
			//echo $this->monthes[$i];
		}
		
		
		//exit;
		return $result;
	}
	
	function get_week_list($date, $wk=31) {
		$week = date("W", strtotime($date));
		$year = date("Y", strtotime($date));
		$result = [];
		while ($wk <= $week-1) {
				$array = $this->get_monday($wk, $year);
				$month_ds = date("m", $array['0']);
				$day_ds = date("d", $array['0']);
				if (($day_ds == 26) and ($month_ds == 9)){$day_ds = 1; $month_ds = 10;}
				$month_de = date("m", $array['6']);
				$day_de = date("d", $array['6']);
				$result[] = ["week"=>$wk, "title"=>$day_ds." ".$this->monthes[(int)$month_ds]." - ".$day_de." ".$this->monthes[(int)$month_de]];
				$wk++;
		}
		return $result;
	}
	
	public function getFinishedContest($city_id){
		if(date("Y-m-d")<"2017-12-22") {
			$wq = "`con_id`!=9 AND ";
		}
		$result = $this->getItemsWhere("$wq `con_status`='1' AND `con_city`={$city_id} AND `con_end_date`<=CURDATE() AND (SELECT COUNT(*) FROM `thebest`.`results` WHERE res_contest=con_id)>0", "con_end_date DESC", null, null, "con_id as id, con_name as name, con_descr as description");
		return $result;
	}
	
	public function getResultsContest($contest_id){
		$contest = $this->getItem($contest_id);
		$query = $this->db()->parse("
		SELECT 
			*,
			(SELECT COUNT(*) FROM thebest.{$contest['con_table_prefix']}brend_nomination{$contest['con_table_sufix']} as brend_nomination WHERE brend_nomination.id_nomination=results.res_nom AND results.res_contest=?i  ) as nominants,
			(SELECT sub_name FROM thebest.subnominations WHERE subnominations.sub_contest=results.res_contest AND subnominations.sub_nom=results.res_nom AND subnominations.sub_id=results.res_subnom ) as sub_nom_name
		FROM 
			thebest.results, 
			thebest.{$contest['con_table_prefix']}nomination{$contest['con_table_sufix']},
			thebest.{$contest['con_table_prefix']}brend_nomination{$contest['con_table_sufix']}
		WHERE 1 
			AND {$contest['con_table_prefix']}nomination{$contest['con_table_sufix']}.id_nomination=results.res_nom 
			AND {$contest['con_table_prefix']}brend_nomination{$contest['con_table_sufix']}.id_n_brend=results.res_firm 
			AND res_contest=?i ORDER BY nomination, res_subnom, res_pos", $contest['con_id'], $contest['con_id']);
			$rows = $this->db()->GetAll($query);
			$list=array();
			foreach($rows as $i=>$row){
				if($row['res_subnom']!=0){
					$nid=$row['res_nom'].'_'.$row['res_subnom'];
					if(!$list[$nid]){
						$list[$nid]=array(
								//"id"=>$row['res_nom'],
								//"name"=>$row['nomination'],
								//"sub_id"=>$row['res_subnom'],
								//"sub_name"=>$row['sub_nom_name'],
								"name"=>$row['sub_nom_name'],
								"ico"=>'http://xn--90ax.xn--e1asq.xn--p1ai/images/complete_img_small.png',
								"percent"=>$row['res_nom_percent'],
								"nominants"=>$row['nominants'],
								"victories"=>array(),
						);
					}
				} 
				else{
					$nid=$row['res_nom'];
					if(!$list[$nid]){
						$list[$nid]=array(
								//"id"=>$row['res_nom'],
								"name"=>$row['nomination'],
								"ico"=>'http://xn--90ax.xn--e1asq.xn--p1ai/images/complete_img_small.png',
								"percent"=>$row['res_nom_percent'],
								"nominants"=>$row['nominants'],
								"victories"=>array(),
						);
					}
				}
				$list[$nid]["victories"][]=array(
						"pos"=>$row['res_pos'],
						"id"=>$row['res_firm'],
						"name"=>$row['nomination_brend'],
						"votes"=>$row['res_votes'],
						"percent"=>$row['res_percent'],
						"nominants"=>$row['nominants'],
					);
			}
			$list2=array();
			foreach($list as $i=>$row){
				$list2[] = $row;
			}
		if(count($list2)>0){
			return $list2;
		}
	}
	
	public function getResultsContestDetails($contest_id, $brend_id){
		$contest = $this->getItem($contest_id);
			$results = $this->db()->getRow("
			SELECT
				brend_name,
				res_percent,
				( 100 / vsego * 18_man) as percent_18_man,
				( 100 / vsego * 18_woman) as percent_18_woman,
				( 100 / vsego * 25_man) as percent_25_man,
				( 100 / vsego * 25_woman) as percent_25_woman,
				( 100 / vsego * 35_man) as percent_35_man,
				( 100 / vsego * 35_woman) as percent_35_woman,
				( 100 / vsego * 45_man) as percent_45_man,
				( 100 / vsego * 45_woman) as percent_45_woman,
				( 100 / vsego * 55_man) as percent_55_man,
				( 100 / vsego * 55_woman) as percent_55_woman,
				( 100 / vsego * 56_man) as percent_56_man,
				( 100 / vsego * 56_woman) as percent_56_woman
			FROM (
			SELECT 
				(SELECT nomination_brend FROM thebest.{$contest['con_table_prefix']}brend_nomination{$contest['con_table_sufix']} WHERE `id_n_brend`={$brend_id}) as brend_name,
				(SELECT res_percent FROM thebest.results WHERE res_contest={$contest_id} AND `res_firm`={$brend_id}) as res_percent,
				COUNT(*) as vsego,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=1 AND sex=1) as `18_man`,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=1 AND sex=2) as `18_woman`,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=2 AND sex=1) as `25_man`,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=2 AND sex=2) as `25_woman`,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=3 AND sex=1) as `35_man`,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=3 AND sex=2) as `35_woman`,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=4 AND sex=1) as `45_man`,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=4 AND sex=2) as `45_woman`,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=5 AND sex=1) as `55_man`,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=5 AND sex=2) as `55_woman`,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=6 AND sex=1) as `56_man`,
				(SELECT COUNT(*) FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes WHERE age=6 AND sex=2) as `56_woman`
			FROM (SELECT {$contest['con_table_prefix']}user{$contest['con_table_sufix']}.* FROM thebest.{$contest['con_table_prefix']}votes{$contest['con_table_sufix']}, thebest.{$contest['con_table_prefix']}user{$contest['con_table_sufix']} WHERE 1 AND {$contest['con_table_prefix']}votes{$contest['con_table_sufix']}.id_user={$contest['con_table_prefix']}user{$contest['con_table_sufix']}.id_user AND `id_n_brend`={$brend_id}) as all_votes
			) as t
			");
		$result = [];
		if(!empty($results)){
			$result = [
				'name' => $results['brend_name'],
				'percent' => $results['res_percent'],
				'items' => [
					[ 'caption' => 'до 18 лет', 'man' => $results['percent_18_man'], 'woman' => $results['percent_18_woman'] ],
					[ 'caption' => '18-25 лет', 'man' => $results['percent_25_man'], 'woman' => $results['percent_25_woman'] ],
					[ 'caption' => '26-35 лет', 'man' => $results['percent_35_man'], 'woman' => $results['percent_35_woman'] ],
					[ 'caption' => '36-45 лет', 'man' => $results['percent_45_man'], 'woman' => $results['percent_45_woman'] ],
					[ 'caption' => '46-55 лет', 'man' => $results['percent_55_man'], 'woman' => $results['percent_55_woman'] ],
					[ 'caption' => 'после 56 лет', 'man' => $results['percent_56_man'], 'woman' => $results['percent_56_woman'] ],
				]
			];
		}
		
		return $result;
	}
	
}
?>