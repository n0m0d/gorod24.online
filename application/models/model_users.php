<?php
/* Automatic model generated
 * ver 0.1
 * model for site: mvc.test
 * date create: 2017-10-25 14:45:35
*/
class model_users extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "users",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "create_time" => "2017-10-25 13:43:29",
            "collation" => "utf8_general_ci",
            "primary_key" => "user_id",
			"autoinit"  => false,
            "columns" => array(
				'user_login' => "varchar(50) NOT NULL",				'user_password' => "varchar(50) NOT NULL",				'user_name' => "varchar(100) NOT NULL",				'user_avatar' => "varchar(100) NULL DEFAULT '/images/defaultAvatar.jpg'",				'user_status' => "int(10) NOT NULL",				'user_activation_key' => "varchar(50) NOT NULL",				'user_registered' => "datetime NOT NULL",				
				),
			"initdata" => array(
				array(
					"user_id" => 1,
					"user_login" => 'admin',
					"user_password" => '21232f297a57a5a743894a0e4a801fc3',
					"user_name" => 'admin',
					"user_avatar" => '',
					"user_status" => 1,
					"user_activation_key" => '',
					"user_registered" => date('Y-m-d H:i:s'),
				),
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
	
	public function login($login, $password){
		$result = $this->getItemWhere("`user_login`='{$login}' and `user_password`='".md5($password)."' and user_status=1");
		if(!empty($result)){
			$_SESSION['user_id']			=	$result['user_id'];
			$_SESSION['user_login']			=	$result['user_login'];
			$_SESSION['user_name']			=	$result['user_name'];
			$_SESSION['user_avatar']		=	$result['user_avatar'];
			$_SESSION['user_status']		=	$result['user_status'];
			$_SESSION['user_activation_key']=	$result['user_activation_key'];
			$_SESSION['user_registered']	=	$result['user_registered'];
			return $result;
		}
		else {
			return false;
		}
	}
}
?>