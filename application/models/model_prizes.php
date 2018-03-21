<?php
class model_prizes extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "contests_prizes",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
            	'group_id' => "int(11) NOT NULL",
            	'contest_id' => "int(11) NOT NULL",
				'title' => "text NULL DEFAULT NULL",
				'desc' => "text NULL DEFAULT NULL",
				'who' => "text NULL DEFAULT NULL",
				'who_link' => "text NULL DEFAULT NULL",
				'cost' => "text NULL DEFAULT NULL",
				'start_date' => "date NULL DEFAULT NULL",
				'start_time' => "time NULL DEFAULT NULL",
				'end_date' => "date NULL DEFAULT NULL",
				'end_time' => "time NULL DEFAULT NULL",
				'unix_start' => "int(11) NOT NULL",
				'unix_end' => "int(11) NOT NULL",
				'unix_len' => "int(11) NOT NULL",
				'code' => "text NOT NULL",
				'row' => "int(11) NOT NULL",
				'status' => "int(11) NOT NULL",
				'user_base' => "varchar(255) NOT NULL DEFAULT '`gorod24.online`.`gorod_devicelog`'",
				'owner' => "int(11) NULL DEFAULT NULL",
				'ext' => "varchar(5) NOT NULL",
				'otdali' => "int(1) NOT NULL",
				'push_sends' => "int(11) NOT NULL DEFAULT 0",
				
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
					/*
					// Examples
					"before_query" => array(
						"SELECT NOW() FROM dual", "SELECT CURDATE() FROM dual"
					),
					"before_func" => "<MODEL FUNCTION NAME>",
					"del_index" => array(
						"<INDEX NAME1>", "<INDEX NAME2>"
					),
					"del_uniq" => array(
						"<UNIQ INDEX NAME1>", "<UNIQ INDEX NAME2>"
					),
					"del_fulltext" => array(
						"<FULLTEXT INDEX NAME1>", "<FULLTEXT INDEX NAME2>"
					),
					"add_columns" => array(
						"<NEW COLUMN NAME>"   => "VARCHAR(50) NOT NULL DEFAULT '' AFTER `<AFTER COLUMN>`"
					),
					"del_columns" => array(
						"<COLUMN NAME1>", "<COLUMN NAME2>"
					),
					"mod_columns" => array(
						"<COLUMN NAME1>" => array( "name"=>"<NEW COLUMN NAME1>", "type"=>"VARCHAR(50) NOT NULL DEFAULT '' AFTER `<AFTER COLUMN>`" ),
						"<COLUMN NAME2>" => array( "name"=>"<NEW COLUMN NAME2>", "type"=>"VARCHAR(50) NOT NULL DEFAULT '' AFTER `<AFTER COLUMN>`" ),
					),
					"add_index" => array(
						"<NEW INDEX NAME>"   => array( "<COLUMN NAME>" ),
					),
					"add_uniq" => array(
						"<NEW UNIQ INDEX NAME>"   => array( "<COLUMN NAME>" ),
					),
					"add_fulltext" => array(
						"<NEW FULLTEXT INDEX NAME>"   => array( "<COLUMN NAME>" ),
					),
					"engine" => "<NEW ENGINE |InnoDB|MyISAM|other>",
					"after_query" => array(
						"SELECT NOW() FROM dual", "SELECT CURDATE() FROM dual"
					),
					"after_func" => "<MODEL FUNCTION NAME>",
					*/
				),
			)
		];
		parent::__construct($config);
    }
}
?>