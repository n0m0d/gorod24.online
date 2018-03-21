<?php
class model_feo_biznes extends Model
{
	protected $otr;		public function otr() { return $this->otr; }
	protected $spravka;	public function spravka() { return $this->spravka; }
	protected $photo;	public function photo() { return $this->photo; }
	protected $tov;	public function tov() { return $this->tov; }
	
	protected $coords; 	public function coords() { return $this->coords; }
	protected $cities;
	
	
	function __construct($config = array()) {
		$config = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "pred",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
			"register"  => "register",
            "columns" => array(
				'name' => "TEXT NOT NULL",
				'name_kat' => "TEXT NOT NULL",
				'adr_f' => "TEXT NOT NULL",
				'adr_y' => "TEXT NOT NULL",
				'phones' => "TEXT NOT NULL",
				'menag' => "TEXT NOT NULL",
				'status' => "INT(11) NULL",
				'activ' => "TEXT NOT NULL",
				'activ_gaz' => "VARCHAR(100) NOT NULL",
				'otr' => "TEXT NOT NULL",
				'rating' => "INT(11) NOT NULL DEFAULT 0",
				'rating_tel' => "INT(11) NOT NULL DEFAULT 0",
				'rating_ob' => "INT(11) NOT NULL DEFAULT 0",
				'email' => "TEXT NOT NULL",
				'web' => "TEXT NOT NULL",
				'oplata' => "DATE NOT NULL DEFAULT '0000-00-00'",
				'oplata_g24' => "DATE NOT NULL DEFAULT '0000-00-00'",
				'login' => "TEXT NOT NULL",
				'passw' => "TEXT NOT NULL",
				'katalog' => "TEXT NOT NULL",
				'vh' => "INT(11) NOT NULL",
				'viz' => "INT(11) NOT NULL",
				'mesto' => "VARCHAR(2) NOT NULL DEFAULT '10'",
				'id_buhg' => "INT(11) NOT NULL DEFAULT '0'",
				'on_off' => "INT(11) NOT NULL DEFAULT '1'",
				'not_in_gazeta' => "INT(11) NOT NULL DEFAULT '0'",
				'icq' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'skype' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'vkcom' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'twitter' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'fecebook' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'odnoklassniki' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'work' => "TEXT NOT NULL DEFAULT ''",
				'lunch' => "TEXT NOT NULL DEFAULT ''",
				'satarday' => "TEXT NOT NULL DEFAULT ''",
				'sunday' => "TEXT NOT NULL DEFAULT ''",
				'sunday_work' => "TEXT NOT NULL DEFAULT ''",
				'priem' => "TEXT NOT NULL DEFAULT ''",
				'name_ua' => "TEXT NOT NULL DEFAULT ''",
				'name_kat_ua' => "TEXT NOT NULL DEFAULT ''",
				'adr_f_ua' => "TEXT NOT NULL DEFAULT ''",
				'adr_y_ua' => "TEXT NOT NULL DEFAULT ''",
				'name_en' => "TEXT NOT NULL DEFAULT ''",
				'name_kat_en' => "TEXT NOT NULL DEFAULT ''",
				'adr_f_en' => "TEXT NOT NULL DEFAULT ''",
				'adr_y_en' => "TEXT NOT NULL DEFAULT ''",
				'activ_en' => "TEXT NOT NULL DEFAULT ''",
				'town' => "TEXT NOT NULL DEFAULT ''",
				'city_id' => "INT(11) NOT NULL DEFAULT '0'",
				'h_redir' => "TEXT NOT NULL DEFAULT ''",
				'jump_to_site' => "INT(11) NOT NULL DEFAULT '0'",
				'feo_domen' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'yandex' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'google' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'url' => "TEXT NOT NULL DEFAULT ''",
				'url_ru' => "TEXT NOT NULL DEFAULT ''",
				'fio_contakt' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'phones_contakt' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'mail_contakt' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'vip_code' => "VARCHAR(10) NULL DEFAULT NULL",
				'show_in_app' => "INT(11) NULL DEFAULT '1'",
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
		
		$otr_config = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "otr",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "TEXT NOT NULL DEFAULT ''",
				'rating' => "INT(11) NOT NULL DEFAULT '0'",
				'sub_otr' => "VARCHAR(200) NOT NULL DEFAULT ''",
				'sub_soc' => "TEXT NOT NULL DEFAULT ''",
				'img' => "TEXT NOT NULL DEFAULT ''",
				'name_ua' => "TEXT NOT NULL DEFAULT ''",
				'name_en' => "TEXT NOT NULL DEFAULT ''",
				'kyrort_name' => "TEXT NOT NULL DEFAULT ''",
				'seo' => "TEXT NOT NULL DEFAULT ''",
				'url' => "TEXT NOT NULL DEFAULT ''",
				'url_ru' => "TEXT NOT NULL DEFAULT ''",
				'icon' => "VARCHAR(255) NULL DEFAULT ''",
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
		$this->otr = new Model($otr_config);
		
		$spravka_config = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "oper_spravka",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
			"register"  => "register",
            "columns" => array(
				'name' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'phones' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'adr_f' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'adr_y' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'otr' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'date_ins' => "DATE NOT NULL DEFAULT ''",
				'date_upda' => "DATE NOT NULL DEFAULT ''",
				'operator' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'activ' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'rating' => "INT(11) NOT NULL DEFAULT '0'",
				'reting_tel' => "INT(11) NOT NULL DEFAULT '0'",
				'rating_ob' => "INT(11) NOT NULL DEFAULT ''",
				'menag' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'email' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'web' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'oplata' => "DATE NOT NULL DEFAULT '0000-00-00'",
				'vh' => "INT(1) NOT NULL DEFAULT '0'",
				'work' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'lunch' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'satarday' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'sunday' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'priem' => "TEXT NOT NULL DEFAULT ''",
				'on_off' => "INT(11) NOT NULL DEFAULT '1'",
				'town' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'url' => "TEXT NOT NULL DEFAULT ''",
				'url_ru' => "TEXT NOT NULL DEFAULT ''",
				'spravka' => "INT(11) NOT NULL DEFAULT '0'",
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
		$this->spravka = new Model($spravka_config);
		
		$coords_config = [
            "server" => "80.93.183.242",
            "database" => "site_21200",
            "prefix" => "",
            "name" => "pred_map",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'pid' => "INT(11) NOT NULL DEFAULT '0'",
				'x' => "DOUBLE NOT NULL DEFAULT '0'",
				'y' => "DOUBLE NOT NULL DEFAULT '0'",
				'img' => "TEXT NOT NULL DEFAULT ''",
				'adr' => "TEXT NOT NULL DEFAULT ''",
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
		$this->coords = new Model($coords_config);
		
		$photo_config = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "pred_photo",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
			"register"  => "register",
            "columns" => array(
				'name' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'type' => "INT(11) NOT NULL DEFAULT '0'",
				'pid' => "INT(11) NOT NULL DEFAULT '0'",
				'coord_id' => "INT(11) NOT NULL DEFAULT '0'",
				'date_create' => "DATETIME NOT NULL",
				'date_update' => "DATETIME NOT NULL",
				'file' => "VARCHAR(255) NOT NULL DEFAULT ''",
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
		$this->photo = new Model($photo_config);
		
		$tov_config = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "tov_usl",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
			"register"  => "register",
            "columns" => array(
				'name' => "VARCHAR(255) NOT NULL DEFAULT ''",
				'id_pr' => "INT(11) NOT NULL DEFAULT '0'",
				'id_izm' => "INT(11) NOT NULL DEFAULT '99'",
				'price' => "varchar(18) NOT NULL DEFAULT 'n/a'",
				'rating' => "INT(11) NOT NULL DEFAULT '0'",
				'rating_tel' => "INT(11) NOT NULL DEFAULT '0'",
				'datelast' => "DATE NOT NULL",
				'descr' => "INT(11) NOT NULL DEFAULT '0'",
				'photo' => "INT(11) NOT NULL DEFAULT '0'",
				'url' => "VARCHAR(255) NULL DEFAULT NULL",
				'url_ru' => "VARCHAR(255) NULL DEFAULT NULL",
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
		$this->tov = new Model($tov_config);
		
		$this->cities = new model_cities();
    }
	
	public function getBiznesRubrics($city_id){
		$city_title = $this->cities->get('city_title')->from($this->cities)->where("city_id={$city_id}")->commit('one');
		if(!empty($city_title)){
			$wq = " AND `town` = '{$city_title}'";
		} else $wq='';
		$result = $this->otr->getItemsWhere("sub_otr like 'main'", "name", null, null, "id, name, icon");
		foreach($result as $i=> $item){
			$items = $this->otr->getItemsWhere("sub_otr like '%;{$item['id']};%'", "name", null, null, "id, name");
			$items2 = [];
			foreach($items as $j=>$s){
				if($city_id==1483) { $wq .= " and (`oplata`>=curdate() or `oplata_g24`>=curdate())"; }
				$count = $this->getCountWhere("otr like '%;{$s['id']};%' and `on_off`!='0' and `show_in_app`=1  {$wq}");
				//var_dump($count);
				if($count==0) unset($items[$j]);
			}
			if(!empty($items)){	
				foreach($items as $_item){ $items2[]=$_item; }
				$result[$i]['items'] = $items2; 
			} else unset($result[$i]);
		}
		if($result) $result = array_values($result);
		
		return $result;
	}
	
	public function getSocialRubrics(){
		$result = $this->otr->getItemsWhere("sub_otr like 'main'", "name", null, null, "id, name");
		foreach($result as $i=> $item){
			$items = $this->otr->getItemsWhere("sub_otr like '%;{$item['id']};%' AND `sub_soc`='1'", "name", null, null, "id, name");
			//if(!empty($items)){	
			$result[$i]['items'] = $items; 
			//} else unset($result[$i]);
		}
		return $result;
	}
	
	public function getRazdel($city_id, $id, $latitude=null, $longitude=null){
		/*
		$city_title = $this->cities->get('city_title')->where("city_id={$city_id}")->commit('one');
		if(!empty($city_title)){
			$wq = " AND `town` = '{$city_title}'";
		} else $wq='';
		*/
		$wq=" AND `city_id`='{$city_id}'";
		if($city_id==1483) { $wq .= " and (`oplata`>=curdate() or `oplata_g24`>=curdate())"; }
		$result1 = $this->getItemsWhere("otr like '%;{$id};%' and `on_off`!='0' and `show_in_app`=1 {$wq}", "mesto, oplata desc, name asc", null, null, "id, 'biznes' as `type`, name_kat as name,
															adr_f, adr_y, phones, menag, activ as description, rating as rating_inet, 
															rating_tel, rating_ob, email, web, oplata, work, lunch, satarday as saturday, sunday as weekend, priem,
															(SELECT file FROM main.pred_photo WHERE pred_photo.pid=pred.id AND type=0 ORDER BY pred_photo.id DESC LIMIT 1) as `logo` ");
		
		$result2 = $this->spravka->getItemsWhere("otr like '%;{$id};%' and `on_off`!='0' and `show_in_app`=1", "oplata desc, name asc", null, null, "id, 'social' as `type`, name, adr_f, adr_y, phones, 
		menag, activ as description, rating as rating_inet, reting_tel as rating_tel, rating_ob, 
		email, web, oplata, work, lunch, satarday as saturday, sunday as weekend, priem");
		
		foreach($result1 as $i => $r){
			$coords = $this->coords->getItemsWhere("`pid`={$r['id']}", null, null, null, "x as longitude, y as latitude, img, adr"); 
			if(!is_null($latitude) and !is_null($longitude)){
			$nearest = null;
			foreach($coords as $c=>$coord){
				$coords[$c]['distance'] = number_format(calculateTheDistance($coord['latitude'], $coord['longitude'], $latitude, $longitude), 2, '.','');
				if(is_null($nearest) or $nearest > $coords[$c]['distance']) $nearest=$coords[$c]['distance'];
			}}
			if(!empty($result1[$i]['logo'])){
			$result1[$i]['logo']=str_replace('http://21200.ru', 'https://21200.ru', $result1[$i]['logo']);
			if(substr($result1[$i]['logo'],0,4)!='http'){
				$result1[$i]['logo'] = 'https://xn--e1asq.xn--p1ai'.$result1[$i]['logo'];
			}
			} else {
				$result1[$i]['logo'] = 'https://xn--e1asq.xn--p1ai/skin/images/catalog/no_logo.png';
			}
			$result1[$i]['coords']=$coords;
			$result1[$i]['nearest']=$nearest;
		}
		
			usort($result1, function($a, $b){
				if(is_null($a['nearest'])) return 1;
				if ($a == $b) {
					return 0;
				}
				return ($a['nearest'] < $b['nearest']) ? -1 : 1;
			});
		
		$result = array_merge($result1, $result2);
		
		return $result; 
	}
	
	public function parseTimetable($result){
		
		$result['work'] = $result['work'];
		
		return $result;
	}
	
	public function checkCityPred($pred){
		if(empty($pred['city_id']) and !empty($pred['town'])){
			$model_cities = new model_cities();
			$city = $model_cities->getItemWhere("TRIM(`city_title`)=TRIM('{$pred['town']}')");
			if($city){
				$this->Update(['city_id' => $city['city_id'] ], $pred['id']);
				$pred['city_id'] = $city['city_id'];
			}
		}
		return $pred;
	}
	
	public function checkNB($pred){
		if(!empty($pred['city_id'])){
			$model_nb = new model_nb();
			$contest = $model_nb->getCurrentContest($pred['city_id']);
			if($contest['id']>0){
				$nomination = $model_nb->getPredInContest($contest['id'], $pred['id']);
				if($nomination){
					$pred['nb_code'] = "nb/resetnomination/{$contest['id']}/{$nomination}";
				}
			}
			//nb/resetnomination/10/2254
		}
		return $pred;
	}
	
	public function getPodrobno($type='biznes',$id, $latitude=null, $longitude=null){
		if(empty($id)) return ["error"=>1, "message"=>"The request failed!"];
		$result = [];
		switch($type){
			case "biznes": $result=$this->getItem($id, "id, name_kat as name,
					adr_f, adr_y, phones, menag, activ as description, rating as rating_inet, 
					rating_tel, rating_ob, email, web, oplata, work, lunch, satarday as saturday, sunday as weekend, priem, url_ru, town, city_id,
					(SELECT file FROM main.pred_photo WHERE pred_photo.pid=pred.id AND type=0 ORDER BY pred_photo.id DESC LIMIT 1) as `logo`"); 
					$result=$this->checkCityPred($result);
					$result=$this->checkNB($result);
					$coords = $this->coords->getItemsWhere("`pid`={$id}", null, null, null, "x as longitude, y as latitude, img, adr");
					if(!is_null($latitude) and !is_null($longitude)){
					foreach($coords as $c=>$coord){
						$coords[$c]['distance'] = number_format(calculateTheDistance($coord['latitude'], $coord['longitude'], $latitude, $longitude), 2, ',','');
					}}
					$result['faces'] = $this->photo->get("file")->where("`pid`='{$id}' AND `type`=1")->commit('col');
					$result['interior'] = $this->photo->get("file")->where("`pid`='{$id}' AND `type`=2")->commit('col');
				break;
			case "social": $result=$this->spravka->getItem($id, "id, name, adr_f, adr_y, phones, 
					menag, activ as description, rating as rating_inet, reting_tel as rating_tel, rating_ob, 
					email, web, oplata, work, lunch, satarday as saturday, sunday as weekend, priem, url_ru");
					$coords = [];
					$result['faces'] = [];
					$result['interior'] = [];
				break;
		}
		$result = $this->parseTimetable($result);
		
		$images = [];
		$result['link']="http://xn--e1asq.xn--p1ai/%D0%B1%D0%B8%D0%B7%D0%BD%D0%B5%D1%81/{$result['url_ru']}";
		if(!empty($result['faces'])){
			foreach($result['faces'] as $i => $face){
				$result['faces'][$i]=str_replace('http://21200.ru', 'https://21200.ru', $face);
				if(substr($face,0,4)!='http' and !empty($face)){ $result['faces'][$i] = 'https://xn--e1asq.xn--p1ai'.$face; }
				$images[] = $result['faces'][$i];
			}
		}
		
		if(!empty($result['interior'])){
			foreach($result['interior'] as $i => $interior){
				$result['interior'][$i]=str_replace('http://21200.ru', 'https://21200.ru', $interior);
				if(substr($interior,0,4)!='http' and !empty($interior)){ $result['interior'][$i] = 'https://xn--e1asq.xn--p1ai'.$interior; }
				$images[] = $result['interior'][$i];
			}
		}
		
		if(!empty($result['logo'])){
			$result['logo']=str_replace('http://21200.ru', 'https://21200.ru', $result['logo']);
			if(substr($result['logo'],0,4)!='http'){ $result['logo'] = 'https://xn--e1asq.xn--p1ai'.$result['logo']; }
			$images[] = $result['logo'];
		} else $result['logo'] = 'https://xn--e1asq.xn--p1ai/skin/images/catalog/no_logo.png';
		
		$result['images']=$images;
		$result['coords']=$coords;
		foreach($result as $key=>$val){ if($val=='n/a') $result[$key] = '';}
			
		return $result;
	}

	public function search($user_id=null, $city_id=null, $data=array()){
		$city_title = $this->cities->get('city_title')->from($this->cities)->where("city_id={$city_id}")->commit('one');
		if(!empty($city_title)){
			$wq = " AND `town` = '{$city_title}'";
		} else $wq='';
		if($city_id==1483) { $wq .= " and (`oplata`>=curdate() or `oplata_g24`>=curdate())"; }
		
		if(!empty($data['text'])) { 
		$latitude=$data['latitude']; 
		$longitude=$data['longitude'];
		$text = trim(strip_tags(addslashes($data['text'])));
		$ws = " and (
			`name` LIKE '%{$text}%'
			OR `adr_f` LIKE '%{$text}%'
			OR `phones` LIKE '%{$text}%'
			OR `menag` LIKE '%{$text}%'
			OR `activ` LIKE '%{$text}%'
			OR `email` LIKE '%{$text}%'
			OR `web` LIKE '%{$text}%'
		)"; 
		$start = $data['start'];
		$limit = $data['limit'];
		
		$result1 = $this->getItemsWhere("`on_off`!='0' and `show_in_app`=1 {$wq} {$ws}", "mesto, oplata desc, name asc", $start, $limit, 
		"id, 'biznes' as `type`, name_kat as name,
															adr_f, adr_y, phones, menag, activ as description, rating as rating_inet, 
															rating_tel, rating_ob, email, web, oplata, work, lunch, satarday as saturday, sunday as weekend, priem,
															(SELECT file FROM main.pred_photo WHERE pred_photo.pid=pred.id AND type=0 ORDER BY pred_photo.id DESC LIMIT 1) as `logo` ");
		
		$result2 = $this->spravka->getItemsWhere("`on_off`!='0' and `show_in_app`=1 {$ws}", "oplata desc, name asc", $start, $limit, "id, 'social' as `type`, name, adr_f, adr_y, phones, 
		menag, activ as description, rating as rating_inet, reting_tel as rating_tel, rating_ob, 
		email, web, oplata, work, lunch, satarday as saturday, sunday as weekend, priem");
		
		foreach($result1 as $i => $r){
			$coords = $this->coords->getItemsWhere("`pid`={$r['id']}", null, null, null, "x as longitude, y as latitude, img, adr"); 
			if(!is_null($latitude) and !is_null($longitude)){
			$nearest = null;
			foreach($coords as $c=>$coord){
				$coords[$c]['distance'] = number_format(calculateTheDistance($coord['latitude'], $coord['longitude'], $latitude, $longitude), 2, '.','');
				if(is_null($nearest) or $nearest > $coords[$c]['distance']) $nearest=$coords[$c]['distance'];
			}}
			if(!empty($result1[$i]['logo'])){
			$result1[$i]['logo']=str_replace('http://21200.ru', 'https://21200.ru', $result1[$i]['logo']);
			if(substr($result1[$i]['logo'],0,4)!='http'){
				$result1[$i]['logo'] = 'https://xn--e1asq.xn--p1ai'.$result1[$i]['logo'];
			}
			} else {
				$result1[$i]['logo'] = 'https://xn--e1asq.xn--p1ai/skin/images/catalog/no_logo.png';
			}
			$result1[$i]['coords']=$coords;
			$result1[$i]['nearest']=$nearest;
		}
		
			usort($result1, function($a, $b){
				if(is_null($a['nearest'])) return 1;
				if ($a == $b) {
					return 0;
				}
				return ($a['nearest'] < $b['nearest']) ? -1 : 1;
			});
		
		$result = array_merge($result1, $result2);
		
		return $result; 
		
		}
		else return [];
		
	}
	
}
?>