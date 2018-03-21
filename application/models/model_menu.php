<?php
/* Automatic model generated
 * ver 0.1
 * model for site: mvc.test
 * date create: 2017-10-31 17:14:11
*/
class model_menu extends Model
{

	protected $model_menu_items; 		public function model_menu_items() 	{		return $this->model_menu_items;	}
	protected $model_menu_projects; 		public function model_menu_projects() 	{		return $this->model_menu_projects;	}

	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "menu",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-10-31 16:12:18",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
	            'project_id' => "int(11) NOT NULL",
	            'name' => "varchar(150) NOT NULL",
	            'class' => "varchar(150) NOT NULL",
	            'location' => "int(11) NOT NULL",
	            //'user_status' => "int(11) NOT NULL",
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
					"version"       => "1"
				),
			)
		];
		parent::__construct($config);

		$menu_items = [
			"server" => "localhost",
			"database" => "gorod24.online",
			"prefix" => "gorod_",
			"name" => "menu_items",
			"engine" => "InnoDB",
			"version" => "1",
			"collation" => "utf8_general_ci",
			"primary_key" => "id",
			"autoinit"  => false,
			"columns" => array(
				//'project_id' => "int(11) NOT NULL",
				'menu_id' => "int(11) NOT NULL",
				'name' => "varchar(150) NOT NULL",
				'link' => "varchar(255) NOT NULL",
				'class' => "varchar(150) NOT NULL",
				'parent_id' => "int(11) NOT NULL",
				'position' => "int(11) NOT NULL",
				'user_status' => "int(11) NOT NULL",
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
		$this->model_menu_items = new Model($menu_items);

		$menu_projects = [
			"server" => "localhost",
			"database" => "gorod24.online",
			"prefix" => "gorod_",
			"name" => "menu_projects",
			"engine" => "InnoDB",
			"version" => "1",
			"collation" => "utf8_general_ci",
			"primary_key" => "id",
			"autoinit"  => false,
			"columns" => array(
				'project_id' => "int(11) NOT NULL",
				'menu_id' => "int(11) NOT NULL",
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
		$this->model_menu_projects = new Model($menu_projects);
    }
}
?>