<?php
class model_rules extends Model
{

	protected $rubric; public function rubric(){ return $this->rubric; }

	function __construct($config = array()) {
		$config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "rules",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'rubric_id' => "INT(11) NOT NULL DEFAULT '0'",
				'name' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'text' => "TEXT NOT NULL DEFAULT ''",
				'date_create' => "DATETIME NOT NULL",
				'status' => "INT(11) NOT NULL DEFAULT '1'",
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
		
		$rubrics_config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "rules_rubrics",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'description' => "TEXT NOT NULL DEFAULT ''",
				'date_create' => "DATETIME NOT NULL",
				'status' => "INT(11) NOT NULL DEFAULT '1'",
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
		$this->rubric = new Model($rubrics_config);
		
	}
	
	public function getRubrics($city_id=0){
		$result = $this->rubric->getItemsWhere("`status`='1' AND (`city_id`='0' OR `city_id`='{$city_id}')", 'id', null, null, "id, name, description");
		foreach($result as $i=>$r){
			$result[$i]['items'] = $this->getItemsWhere("`status`='1' AND (`city_id`='0' OR `city_id`='{$city_id}') AND `rubric_id`='{$r['id']}'", 'id', null, null, "id, name");
		}
		return $result;
	}
	
	public function getRule($city_id=0, $rule_id){
		$result = $this->getItemsWhere("`status`='1' AND (`city_id`='0' OR `city_id`='{$city_id}') AND `id`={$rule_id}", 'id', null, null, "id, name, text");
		return $result;
	}
	
}
?>