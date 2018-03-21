<?php
/* Automatic model generated
 * ver 0.1
 * model for site: gorod24.online
 * date create: 2018-02-16 09:51:40
*/
class model_push extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "push",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2018-02-16 09:44:31",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'provider' => "varchar(50) NOT NULL",				'push_id' => "int(11) NOT NULL",				'access_token' => "varchar(50) NOT NULL",				'website_id' => "int(11) NOT NULL",				'title' => "varchar(250) NOT NULL",				'body' => "text NOT NULL",				'ttl' => "int(11) NOT NULL",				'link' => "varchar(250) NOT NULL",				'filter_lang' => "varchar(50) NOT NULL",				'filter_browser' => "varchar(50) NOT NULL",				'sended' => "int(11) NOT NULL",				'delivered' => "int(11) NOT NULL",				'redirect' => "int(11) NOT NULL",				'adddate' => "datetime NOT NULL",				'senddate' => "datetime NOT NULL",				'is_sended' => "int(11) NOT NULL",				'error_code' => "int(11) NOT NULL",				'error_message' => "varchar(250) NOT NULL",				'new_id' => "int(11) NOT NULL DEFAULT '0'",				'from_new' => "int(11) NOT NULL",				'our' => "int(11) NOT NULL DEFAULT '1'",				
				),
			"index" => array(
				'from_new' => array( 'from_new' ),				'new_id' => array( 'new_id' ),				
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