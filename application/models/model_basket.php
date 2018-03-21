<?php
class model_basket extends Model
{
	protected $_basket_items; public function model_basket_items() { return $this->_basket_items;	}
	protected $_units; public function model_units() { return $this->_units;	}
	protected $_tovars; public function model_tovars() { return $this->_tovars;	}
	protected $_magazs; public function model_magazs() { return $this->_magazs;	}
	protected $_basket_types; public function model_basket_types() { return $this->_basket_types;	}
	protected $_basket_types_items; public function model_basket_types_items() { return $this->_basket_types_items;	}
	protected $_basket_types_magazs; public function model_basket_types_magazs() { return $this->_basket_types_magazs;	}
	
	function __construct($config = array()) {
		$config = [
			"server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "feo_",
            "name" => "basket",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "bas_id",
			"autoinit"  => false,
            "columns" => array(
				'bas_name' => "varchar(50) NULL DEFAULT NULL",
				'bas_comment' => "text NULL DEFAULT NULL",
				'bas_type' => "int(11) NOT NULL DEFAULT 0",
				'bas_date' => "date NOT NULL",
				'bas_adddate' => "datetime NOT NULL",
				'bas_updatedate' => "datetime NOT NULL",
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
		
		$config_basket_items = [
			"server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "feo_",
            "name" => "basket_items",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "bitem_id",
			"autoinit"  => false,
            "columns" => array(
				'bitem_bas_id' => "int(11) NOT NULL DEFAULT 0",
				'bitem_tov_id' => "int(11) NOT NULL DEFAULT 0",
				'bitem_amount' => "double NOT NULL DEFAULT 0",
				'bitem_coin' => "double NOT NULL DEFAULT 0",
				'bitem_izm' => "int(11) NOT NULL DEFAULT 0",
				'bitem_mag_id' => "int(11) NOT NULL DEFAULT 0",
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
		$this->_basket_items = new Model($config_basket_items);
		
		$config_units = [
			"server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "",
            "name" => "edizm",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "varchar(50) NULL DEFAULT NULL",
				'in_adv' => "int(11) NOT NULL DEFAULT 0",
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
		$this->_units = new Model($config_units);
		
		$config_tovars = [
			"server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "feo_",
            "name" => "tovars",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "tov_id",
			"autoinit"  => false,
            "columns" => array(
				'tov_name' => "varchar(50) NULL DEFAULT NULL",
				'tov_descr' => "text NULL DEFAULT NULL",
				'tov_def_amount' => "double NULL DEFAULT NULL",
				'tov_def_coin' => "double NULL DEFAULT NULL",
				'tov_def_izm' => "int(11) NULL DEFAULT NULL",
				'tov_adddate' => "datetime NOT NULL",
				'tov_updatedate' => "datetime NOT NULL",
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
		$this->_tovars = new Model($config_tovars);
		
		$config_magazs = [
			"server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "feo_",
            "name" => "magaz",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "mag_id",
			"autoinit"  => false,
            "columns" => array(
				'mag_name' => "varchar(50) NOT NULL",
				'mag_descr' => "text NOT NULL",
				'mag_addres' => "text NOT NULL",
				'mag_pid' => "int(11) NULL DEFAULT NULL",
				'mag_fid' => "int(11) NULL DEFAULT NULL",
				'mag_adddate' => "datetime NOT NULL",
				'mag_updatedate' => "datetime NOT NULL",
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
		$this->_magazs = new Model($config_magazs);
		
		$config_basket_types = [
			"server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "feo_",
            "name" => "basket_types",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "type_id",
			"autoinit"  => false,
            "columns" => array(
				'type_name' => "varchar(50) NOT NULL",
				'type_descr' => "text NOT NULL",
				'type_adddate' => "datetime NOT NULL",
				'type_updatedate' => "datetime NOT NULL",
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
		$this->_basket_types = new Model($config_basket_types);
		
		$config_basket_types_items = [
			"server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "feo_",
            "name" => "basket_types_items",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "item_id",
			"autoinit"  => false,
            "columns" => array(
				'item_bas_id' => "int(1) NOT NULL",
				'item_tov_id' => "int(1) NOT NULL",
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
		$this->_basket_types_items = new Model($config_basket_types_items);
		
		$config_basket_types_magazs = [
			"server" => "80.93.183.242",
            "database" => "main",
            "prefix" => "feo_",
            "name" => "basket_types_magazs",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'type_id' => "int(1) NOT NULL",
				'mag_id' => "int(1) NOT NULL",
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
		$this->_basket_types_magazs = new Model($config_basket_types_magazs);
		
    }
}
?>