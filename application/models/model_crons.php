<?php
class model_crons extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "crons",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "varchar(250) NOT NULL",				'task_id' => "varchar(250) NOT NULL",				'start_date' => "datetime NOT NULL",				'end_date' => "datetime NOT NULL",				'file' => "varchar(250) NULL",				'status' => "int(11) NOT NULL",				
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
    }
}
?>