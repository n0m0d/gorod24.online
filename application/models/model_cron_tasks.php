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
				'task_name' => "varchar(250) NOT NULL",				'task_descr' => "text NOT NULL",				'task_job' => "varchar(250) NOT NULL",				'task_start_date' => "date NOT NULL",				'task_start_time' => "time NOT NULL",				'task_last_launch' => "datetime NOT NULL",				'task_next_launch' => "datetime NOT NULL",				'task_next_launch_important' => "datetime NOT NULL",				'task_round' => "int(11) NOT NULL",				'task_round_period' => "int(11) NOT NULL",				'task_end_date' => "date NOT NULL",				'task_end_time' => "time NOT NULL",				'task_finished' => "int(11) NOT NULL DEFAULT '0'",				'task_launches' => "int(11) NOT NULL DEFAULT '0'",				'task_last_launch_result' => "text NULL",				'task_execution_log' => "text NULL",				'task_adddate' => "datetime NOT NULL",				'task_updatedate' => "datetime NOT NULL",				'task_status' => "int(11) NOT NULL",				
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