<?php
class model_social extends Model
{
	protected $_model_accounts;		public function model_accounts() { return $this->_model_accounts; }
	protected $_model_auto_posting;		public function model_auto_posting() { return $this->_model_auto_posting; }
	
	function __construct($config = array()) {
		
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "social_publisher",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2018-03-06 11:20:35",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'message_id' => "varchar(50) NULL DEFAULT NULL",
				'account_id_from' => "int(11) NOT NULL",
				'account_id_to' => "int(11) NOT NULL",
				'social_id_to' => "int(11) NOT NULL",
				'social_id_type' => "int(11) NOT NULL", /* 0-страница, 1-группа */
				'text' => "text NOT NULL",
				'link' => "text NOT NULL",
				'post_type' => "int(11) NOT NULL", /* 0-публикация ссылки, 1-публикация картинки */
				'group_id' => "varchar(50) NOT NULL DEFAULT '0'",
				'album_id' => "varchar(50) NOT NULL DEFAULT '0'",
				'photo' => "text NOT NULL DEFAULT ''",
				'new_id' => "INT(11) NOT NULL DEFAULT 0",
				'auto_id' => "INT(11) NOT NULL DEFAULT 0",
				'upped' => "INT(11) NOT NULL DEFAULT 0",				'access_token' => "varchar(255) NOT NULL",				'send_date' => "DATETIME NOT NULL",
				'date' => "DATETIME NOT NULL",
				'is_sended' => "INT(11) NOT NULL",
				'error_code' => "INT(11) NOT NULL",
				'error_message' => "TEXT NOT NULL",
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
		
		$config_model_accounts = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "social_publisher_accounts",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2018-03-06 11:20:35",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "text NOT NULL",
				'description' => "text NOT NULL",
				'social_type' => "varchar(50) NOT NULL",
				'social_id' => "varchar(255) NOT NULL",
				'social_id_type' => "int(11) NOT NULL", /* 0-страница, 1-группа */
				'album_id' => "int(11) NOT NULL DEFAULT 0", 
				'is_main' => "int(11) NOT NULL DEFAULT 0", 
				'access_token' => "varchar(255) NOT NULL",				'app_id' => "varchar(50) NOT NULL",				'app_secret' => "text NOT NULL",				'app_public' => "text NOT NULL",				'sends' => "int(11) NOT NULL",
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
					"version"       => "1",
				),
			)
		];
		$this->_model_accounts = new Model($config_model_accounts);
		
		$config_model_auto_posting = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "social_publisher_auto_posting",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2018-03-06 11:20:35",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "text NOT NULL",
				'description' => "text NOT NULL",
				'news_cities' => "varchar(255) NOT NULL",
				'news_razds' => "varchar(255) NOT NULL",
				'news_tags' => "text NOT NULL",
				'news_interval' => "int(11) NOT NULL DEFAULT 0",
				'url' => "varchar(10) NOT NULL DEFAULT 'url'",
				'post_type' => "int(11) NOT NULL", /* 0-публикация ссылки, 1-публикация картинки */
				'acount_id' => "int(11) NOT NULL",
				'acount_id_from' => "int(11) NOT NULL",
				'group_id' => "varchar(50) NOT NULL DEFAULT '0'",
				'album_id' => "varchar(50) NOT NULL DEFAULT '0'",				'round_period' => "int(11) NOT NULL",
				'round_launches' => "int(11) NOT NULL",
				'start_date' => "DATETIME NOT NULL",
				'end_date' => "DATETIME NOT NULL",
				'last_launch' => "DATETIME NOT NULL",
				'next_launch' => "DATETIME NOT NULL",
				'sends' => "int(11) NOT NULL",
				'status' => "int(11) NOT NULL",				),
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
		$this->_model_auto_posting = new Model($config_model_auto_posting);
		
    }
}
?>