<?php
class model_cron_tasks extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "cron_tasks",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-11-30 17:29:06",
            "collation" => "utf8_general_ci",
            "primary_key" => "task_id",
			"autoinit"  => false,
            "columns" => array(
				'task_cron_id' => "int(11) NOT NULL",
				'task_name' => "varchar(250) NOT NULL",
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