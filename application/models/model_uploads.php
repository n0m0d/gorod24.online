<?php
class model_uploads extends Model
{
	function __construct($config = array()) {
		$config = [
            "server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "uploads",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "varchar(200) NOT NULL DEFAULT ''",
				'original_name' => "varchar(255) NOT NULL DEFAULT ''",
				'ext' => "varchar(10) NOT NULL DEFAULT ''",
				'type' => "varchar(10) NOT NULL DEFAULT ''",
				'size' => "int(11) NULL DEFAULT NULL",
				'destination' => "varchar(200) NOT NULL DEFAULT ''",
				'author' => "int(11) NOT NULL DEFAULT 0",
				'date' => "datetime NOT NULL",
				'modified' => "datetime NULL",
				'status' => "int(11) NOT NULL",
				'other' => "text NULL",
				),
			"index" => array(
				'name' => array( 'name' ),
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