<?php
class model_weather extends Model
{
	protected $model_settings; function model_settings(){ return $this->model_settings; }
	
	function __construct($config = array()) {
		$config = [
			"server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "weather",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'city_id' => "INT(11) NOT NULL DEFAULT 1483",
				'sky' => "INT(11) NULL DEFAULT NULL",
				'temp' => "INT(11)  NULL DEFAULT NULL",
				'water' => "varchar(11)  NULL DEFAULT NULL",
				'date' => "date  NULL DEFAULT NULL",
				'time' => "date  NULL DEFAULT NULL",
				'on_off' => "ENUM('0', '1')  NULL DEFAULT '1'",
				'wind' => "INT(11)  NULL DEFAULT NULL",
				),
			"index" => array(
				'fast_last' => ['on_off', 'date', 'time'],
				'fk_weather_weather_sky1_idx' => ['sky'],
				'city_id' => ['city_id', 'on_off'],
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
		
		$settings_config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "weather_settings",
            "engine" => "InnoDB",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'city_id' => "INT(11) NOT NULL",
				'city_title' => "VARCHAR(50) NULL DEFAULT NULL",
				'gis_city_index' => "INT(11) NOT NULL",
				'water_city_index' => "INT(11) NOT NULL",
				'status' => "INT(11) NOT NULL DEFAULT 1",
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
		$this->model_settings = new Model($settings_config);
    }
}
?>