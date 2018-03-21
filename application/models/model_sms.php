<?php
/* Automatic model generated
 * ver 0.1
 * model for site: gorod24.online
 * date create: 2017-12-11 15:20:16
*/
class model_sms extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "sms",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Compact",
            "create_time" => "2017-12-11 15:19:48",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'code' => "varchar(50) NULL",
				'phone' => "varchar(50) NOT NULL",				'uid' => "int(11) NULL",				'date' => "date NOT NULL",				'message' => "text NOT NULL",				
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
	
	public function sendOnceDay($code, $phone, $message){
		$ch = $this->getCountWhere("`phone`='{$phone}' and `date`=CURDATE() and `code`='{$code}'");
		if($ch==0){
			$this->Insert([
				'code' => $code,
				'phone' => $phone,
				'date' => date('Y-m-d'),
				'message' => $message,
			]);
			SMS_GW_Send($code, $phone, $message);
			return true;
		}
		else {
			return false;
		}
	}
	
}
?>