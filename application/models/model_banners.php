<?php
/* Automatic model generated
 * ver 0.1
 * model for site: gorod24.online
 * date create: 2017-12-08 13:17:05
*/
class model_banners extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "banners",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "create_time" => "2017-11-15 12:03:17",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'cities' => "varchar(255) NOT NULL DEFAULT ''",
				'type' => "int(11) NOT NULL DEFAULT '1'",// 1-Картинка, 2-HTML
				'position' => "int(11) NOT NULL DEFAULT '1'",// 1-верхний, 2-списковый, 3-всплывающий				'name' => "varchar(255) NOT NULL DEFAULT ''",				'link' => "varchar(250) NOT NULL DEFAULT ''",				'img' => "varchar(250) NOT NULL DEFAULT ''",
				'img_id' => "int(11) NOT NULL DEFAULT ''",				'img760' => "varchar(250) NOT NULL DEFAULT ''",
				'img760_id' => "int(11) NOT NULL DEFAULT ''",				'img480' => "varchar(250) NOT NULL DEFAULT ''",
				'img480_id' => "int(11) NOT NULL DEFAULT ''",				'html' => "text NOT NULL",				'date_start' => "date NOT NULL DEFAULT '0000-00-00'",				'date_end' => "date NOT NULL DEFAULT '0000-00-00'",				'on_off' => "int(11) NOT NULL DEFAULT '1'",				'impressions' => "int(11) NOT NULL DEFAULT '0'",				'clicks' => "int(11) NOT NULL DEFAULT '0'",				'controller' => "varchar(50) NOT NULL DEFAULT ''",				
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