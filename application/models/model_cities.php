<?php
class model_cities extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "cities",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "city_id",
			"autoinit"  => false,
            "columns" => array(
				'country' => "INT(11) NOT NULL",
				'region' => "INT(11) NOT NULL",				'city_title' => "VARCHAR(200) NOT NULL",
				'city_area' => "VARCHAR(200) NULL DEFAULT NULL",
				'city_region' => "VARCHAR(200) NULL DEFAULT NULL",
				'url' => "VARCHAR(200) NULL DEFAULT NULL",
				'status' => "INT(11) NOT NULL DEFAULT 0",
				'in_news' => "INT(11) NULL DEFAULT NULL",				),
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
    }
	
	
}
?>