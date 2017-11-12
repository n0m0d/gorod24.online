<?php
/* Automatic model generated
 * ver 0.1
 * model for site: mvc.test
 * date create: 2017-10-25 14:52:46
*/
class model_access extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "access",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Fixed",
            "create_time" => "2017-10-25 13:51:28",
            "collation" => "utf8_general_ci",
            "primary_key" => "ac_id",
			"autoinit"  => false,
            "columns" => array(
				'user_id' => "int(11) NOT NULL",				'ac_res' => "int(11) NOT NULL",				'ac_val' => "int(11) NOT NULL",				),
			"index" => array(
				
			),
			"unique" => array(
				
			),
			"fulltext" => array(
				
			),
			"initdata" => array(
				array(
					"ac_id" => 1,
					"user_id" => 1,
					"ac_res" => 1,
					"ac_val" => 1,
				),
				array(
					"ac_id" => 2,
					"user_id" => 1,
					"ac_res" => 2,
					"ac_val" => 1,
				),
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
	
	public function getPermission($res=null, $id=null){
		if(is_null($id) and !empty($_SESSION['user_id'])) $id = $_SESSION['user_id'];
		if(isset($id) and !is_null($id)){
			if($res == null)
				return false;
				//return $this->getItemsWhere("`user_id` = {$id}");
			else {
				$model_permissions = new model_permissions();
				$result = $this->db()->GetRow("select *  from `{$this->getdatabasename()}`.`{$this->gettablename()}` as `ac`, `{$model_permissions->getdatabasename()}`.`{$model_permissions->gettablename()}` as `perm`  where `perm_id` = `ac_res` and `user_id`= {$id} and `perm_name` = '{$res}'");
				if($result['ac_val']==1) return true; else return false;
			}
		}
		else return false;
	}
	
}
?>