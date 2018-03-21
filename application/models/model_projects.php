<?php
/* Automatic model generated
 * ver 0.1
 * model for site: mvc.test
 * date create: 2017-10-31 17:14:11
*/
class model_projects extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "projects",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "varchar(150) NOT NULL",
				'description' => "text NOT NULL",
				'type' => "int(1) NOT NULL",
				'country_id' => "int(11) NOT NULL",
				'region_id' => "int(11) NOT NULL",
				'city_id' => "int(11) NOT NULL",
				'background' => "varchar(255) NOT NULL",
				'background_id' => "int(11) NOT NULL",
				'background_time_start' => "datetime NOT NULL",
				'background_time_end' => "datetime NOT NULL",
				'status' => "int(11) NOT NULL",
				
				),
			"index" => array(
				
			),
			"unique" => array(
				
			),
			"fulltext" => array(
				
			),
			"revisions" => array(
				array(
					"version"       => "1"
				),
			)
		];
		parent::__construct($config);
    }
}
?>