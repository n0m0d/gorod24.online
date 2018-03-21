<?php
class model_buhg_firms extends Model
{
	function __construct($config = array()) {
		$config = [
            "server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "buhg_",
            "name" => "firma",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'raspr' => "INT(1) NOT NULL",
				'date_ins' => "DATE NOT NULL DEFAULT '0000-00-00'",
				'date_upda' => "DATE NOT NULL DEFAULT '0000-00-00'",
				'name' => "VARCHAR(100) NOT NULL",
				'city' => "VARCHAR(50) NOT NULL",
				'city_id' => "INT(11) NOT NULL",
				'date_last' => "date NOT NULL DEFAULT '0000-00-00'",
				'otr_f' => "varchar(50) NOT NULL",
				'adr_f' => "varchar(100) NOT NULL",
				'phones' => "varchar(100) NOT NULL",
				'phones_of' => "varchar(100) NOT NULL",
				'mob_s_of' => "varchar(12) NOT NULL",
				'mail' => "varchar(100) NOT NULL",
				'web' => "varchar(100) NOT NULL",
				'fio_dir' => "varchar(100) NOT NULL",
				'fio_mend' => "varchar(100) NOT NULL",
				'agent' => "varchar(100) NOT NULL",
				'operator' => "varchar(100) NOT NULL",
				'oper_of' => "varchar(100) NOT NULL",
				'ber_of' => "date NOT NULL DEFAULT '0000-00-00'",
				'platelchik' => "text NOT NULL",
				'on_off' => "int(11) NOT NULL DEFAULT 1",
				'date_last_212' => "date NOT NULL DEFAULT '0000-00-00'",
				'date_last_viz' => "date NOT NULL DEFAULT '0000-00-00'",
				'osnov' => "varchar(255) NOT NULL DEFAULT ''",
				'osnov_n' => "varchar(10) NOT NULL DEFAULT ''",
				'osnov_date' => "date NOT NULL DEFAULT '0000-00-00'",
				'adr_y' => "text NOT NULL DEFAULT ''",
				'doc_dir' => "varchar(255) NOT NULL DEFAULT ''",
				'na_lico' => "text NOT NULL DEFAULT ''",
				'index' => "varchar(5) NOT NULL DEFAULT ''",
				'inn' => "varchar(100) NOT NULL DEFAULT ''",
				'rschet' => "varchar(100) NOT NULL DEFAULT ''",
				'bik' => "varchar(100) NOT NULL DEFAULT ''",
				'kschet' => "varchar(100) NOT NULL DEFAULT ''",
				'bank' => "varchar(100) NOT NULL DEFAULT ''",
				'kpp' => "varchar(100) NOT NULL DEFAULT ''",
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