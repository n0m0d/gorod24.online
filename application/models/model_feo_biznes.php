<?php
class model_feo_biznes extends Model
{
	protected $otr;
	public function otr() { return $this->otr; }
	
	protected $spravka;
	public function spravka() { return $this->spravka; }
	
	protected $coords;
	public function coords() { return $this->coords; }
	
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
		
		
    }
	
	public function getBiznesRubrics(){
		$result = $this->otr->getItemsWhere("sub_otr like 'main'", "name", null, null, "id, name, icon");
		foreach($result as $i=> $item){
			$items = $this->otr->getItemsWhere("sub_otr like '%;{$item['id']};%'", "name", null, null, "id, name");
			if(!empty($items)){	$result[$i]['items'] = $items; } else unset($result[$i]);
		}
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
	
	public function getRazdel($id, $latitude=null, $longitude=null){
		$result1 = $this->getItemsWhere("otr like '%;{$id};%' and `on_off`!='0' and `show_in_app`=1", "mesto, oplata desc, name asc", null, null, "id, 'biznes' as `type`, name_kat as name,
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
	
	public function getPodrobno($type='biznes',$id, $latitude=null, $longitude=null){
		if(empty($id)) return ["error"=>1, "message"=>"The request failed!"];
		$result = [];
		switch($type){
			case "biznes": $result=$this->getItem($id, "id, name_kat as name,
					adr_f, adr_y, phones, menag, activ as description, rating as rating_inet, 
					rating_tel, rating_ob, email, web, oplata, work, lunch, satarday as saturday, sunday as weekend, priem,
					(SELECT file FROM main.pred_photo WHERE pred_photo.pid=pred.id AND type=0 ORDER BY pred_photo.id DESC LIMIT 1) as `logo`"); 
					$coords = $this->coords->getItemsWhere("`pid`={$id}", null, null, null, "x as longitude, y as latitude, img, adr");
					if(!is_null($latitude) and !is_null($longitude)){
					foreach($coords as $c=>$coord){
						$coords[$c]['distance'] = number_format(calculateTheDistance($coord['latitude'], $coord['longitude'], $latitude, $longitude), 2, ',','');
					}}
				break;
			case "social": $result=$this->spravka->getItem($id, "id, name, adr_f, adr_y, phones, 
					menag, activ as description, rating as rating_inet, reting_tel as rating_tel, rating_ob, 
					email, web, oplata, work, lunch, satarday as saturday, sunday as weekend, priem");
					$coords = [];
				break;
		}
		$result['coords']=$coords;
		return $result;
	}
}
?>