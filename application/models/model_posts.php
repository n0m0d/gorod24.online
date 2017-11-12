<?php
/* Automatic model generated
 * ver 0.1
 * model for site: mvc.test
 * date create: 2017-10-25 16:53:24
*/
class model_posts extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "posts",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "create_time" => "2017-10-25 15:48:28",
            "collation" => "utf8_general_ci",
            "primary_key" => "post_id",
			"autoinit"  => false,
            "columns" => array(
				'post_url' => "varchar(200) NULL DEFAULT ''",				'post_name' => "varchar(200) NOT NULL",				'post_description' => "text NULL",				'post_content' => "text NULL",				'post_meta' => "text NULL",				'post_author' => "int(11) NOT NULL",				'post_date' => "datetime NOT NULL",				'post_modified' => "datetime NULL",				'post_status' => "varchar(20) NOT NULL",				'post_type' => "varchar(20) NOT NULL",				'post_template' => "varchar(50) NULL",				
				),
			"index" => array(
				'post_url_post_type' => array( 'post_url', 'post_type', 'post_status' ),				
			),
			"unique" => array(
				
			),
			"fulltext" => array(
				'post_content' => array( 'post_content', 'post_description', 'post_name' ),				
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