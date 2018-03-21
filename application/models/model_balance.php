<?php
class model_balance extends Model
{
    protected $_model_in = null;		public function in(){	return $this->_model_in;	}	
    protected $_model_out = null;		public function out(){	return $this->_model_out;	}	
    protected $_model_services = null;	public function services(){	return $this->_model_services;	}	
    protected $_protection_hash = '';
	
	function __construct($config = array()) {
        
		if (!isset($_SESSION['PAYMENT_PROTECTION_HASH'])) {
            $_SESSION['PAYMENT_PROTECTION_HASH'] = md5(time().$_SESSION['LOGIN_Id'].date("Y-m-j"));
        }
        
        $this->_protection_hash = $_SESSION['PAYMENT_PROTECTION_HASH'];
		
		$config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "payment_balance",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
                'uid'               => "INT NOT NULL DEFAULT '0'",
                'money'             => "DOUBLE NOT NULL DEFAULT '0'",
                'bonus'             => "DOUBLE NOT NULL DEFAULT '0'",
                'utx'               => "INT NOT NULL DEFAULT '0'",
				),
			"index" => array(
			),
			"unique" => array(
                'u_uid'         => array( 'uid' )
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
		
		$in_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "payment_in",
            "engine" => "InnoDB",
            "version" => "1.6",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
                'uid'               => "INT NOT NULL DEFAULT '0'",
                'transaction_id'    => "INT NOT NULL DEFAULT '0'",
                'money'             => "DOUBLE NOT NULL DEFAULT '0'",
                'descr'             => "TEXT NOT NULL DEFAULT ''",
                'date'              => "DATE NOT NULL DEFAULT '0000-00-00'",
                'time'              => "TIME NOT NULL DEFAULT '00:00:00'",
                'utx'               => "INT NOT NULL DEFAULT '0'",
                'is_bonus'          => "ENUM('0', '1') NOT NULL DEFAULT '0'",
                'use_date'          => "INT NOT NULL DEFAULT '0'",
				'used_money'        => "DOUBLE NOT NULL DEFAULT '0'",
				'off_money'         => "DOUBLE NOT NULL DEFAULT '0'",
                'bounty'            => "ENUM('0', '1') NOT NULL DEFAULT '0'",
                'bounty21200'       => "ENUM('0', '1') NOT NULL DEFAULT '0'",
				'acc_id'            => "INT NOT NULL DEFAULT '0'",
				'acc_discount'      => "INT NOT NULL DEFAULT '0'",
				'pred_id'           => "INT NOT NULL DEFAULT '0'",
				),
			"index" => array(
                'fk_uid'        => array( 'uid' ),
                'fk_transaction'=> array( 'transaction_id' )
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
		$this->_model_in = new Model($in_config);
		
		$out_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "payment_out",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
                'uid'               => "INT NOT NULL DEFAULT '0'",
                'money'             => "DOUBLE NOT NULL DEFAULT '0'",
                'package_id'        => "INT NOT NULL DEFAULT '0'",
                'service_id'        => "INT NOT NULL DEFAULT '0'",
                'service_item_id'   => "INT NOT NULL DEFAULT '0'",
                'descr'             => "TEXT NOT NULL DEFAULT ''",
                'date'              => "DATE NOT NULL DEFAULT '0000-00-00'",
                'time'              => "TIME NOT NULL DEFAULT '00:00:00'",
                'utx'               => "INT NOT NULL DEFAULT '0'"
				),
			"index" => array(
                'fk_uid'        => array( 'uid' )
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
		$this->_model_out = new Model($out_config);
        //$this->_model_services = new Payment_ServicesModel();
	}
	
    public function getForUser($user_id) {
        $balance_exist = $this->getItemWhere("`uid`='{$user_id}'");
        if (is_array($balance_exist)) {
            return $balance_exist['money'];
        } else {
            return 0.0;
        }
    }
	
	
	public function getAccDiscount($user_id=null){
        if (is_null($user_id)) {

        } else {
		    $bonus_query = "SELECT `acc_discount` AS `bonus` FROM `{$this->_model_in->getdatabasename()}`.`{$this->_model_in->gettablename()}` AS `in` 
			WHERE `in`.`uid`='{$user_id}' AND (`in`.`money` - `in`.`used_money` - `in`.`off_money`)>0 AND `acc_discount`!=0 ORDER BY `acc_discount` DESC LIMIT 1";
            $bonus = $this->db()->getOne($bonus_query);
			return (int)$bonus;
		}
	}
	
    /**
     * 
     * @param type $user_id
     * @param type $money
     * @param type $descr
     */
    public function registerIn($user_id,$transaction_id,$money,$descr, $is_bonus=false, $use_date=null, $bounty=false, $bounty21200=false, $acc_id=null, $acc_discount=null, $pred_id=null) {
        if(!empty($user_id)){
		$in_data = array(
            'uid'               => $user_id,
            'transaction_id'    => $transaction_id,
            'money'             => $money,
            'descr'             => $descr,
            'date'              => date("Y-m-d"),
            'time'              => date("H:i:s"),
            'utx'               => time()
        );
		if($is_bonus){ $in_data['is_bonus'] = 1; }
		if($use_date){ $in_data['use_date'] = strtotime($use_date); }
		if($bounty){ $in_data['bounty'] = 1; }
		if($bounty21200){ $in_data['bounty21200'] = 1; }
		if($acc_id){ $in_data['acc_id'] = $acc_id; }
		if($acc_discount){ $in_data['acc_discount'] = $acc_discount; }
		if($pred_id){ $in_data['pred_id'] = $pred_id; }
		
        $this->_model_in->Insert($in_data);
        /* Пересчет баланса */
        $this->Recalc($user_id);
		
        return true;
		}
    }
	
    /**
     * 
     * @param type $user_id
     * @param type $money
     * @param type $descr
     * @param type $service_id
     * @param type $service_item_id
     */
    public function registerOut($user_id,$money,$descr='',$package_id=0,$service_id=0,$service_item_id=0) {
        if(!empty($user_id)){
		/* Списание бонусных средств у которых истек срок */
		$this->offBonus($user_id);
	    $out_data = array(
            'uid'               => $user_id,
            'money'             => $money,
            'package_id'        => $package_id,
            'service_id'        => $service_id,
            'service_item_id'   => $service_item_id,
            'descr'             => $descr,
            'date'              => date("Y-m-d"),
            'time'              => date("H:i:s"),
            'utx'               => time()
        );
        $this->_model_out->Insert($out_data);
		
		/* Пересчет использованных бонусов*/
		
		$bonuses = $this->_model_in->getItemsWhere("uid='{$user_id}' AND ((`use_date`>".time()." AND `is_bonus`='1') OR (`acc_discount`!=0)) AND (`used_money`+`off_money`)<`money`", '`use_date`');
		$ost = $money;
		foreach($bonuses as $i => $bonuse){
			$rest = $bonuse['money'] - $bonuse['used_money'];
			if($rest >=$ost){
				$data = ['id' =>$bonuse['id'], 'used_money'=>($bonuse['used_money']+$ost)];
				$this->_model_in->Update($data, $bonuse['id']);
				break;
			}
			elseif($rest == 0){ }
			else {
				$ost = $ost - $rest;
				$data = ['id' =>$bonuse['id'], 'used_money'=>($bonuse['used_money']+$rest)];
				$this->_model_in->Update($data, $bonuse['id']);
			}
		}
		
        /* Пересчет баланса */
        $this->Recalc($user_id);
        return true;
		}
    }
	
    public function registerOutBonuse($user_id,$money,$descr='',$package_id=0,$service_id=0,$service_item_id=0) {
        if(!empty($user_id)){
	    $out_data = array(
            'uid'               => $user_id,
            'money'             => $money,
            'package_id'        => $package_id,
            'service_id'        => $service_id,
            'service_item_id'   => $service_item_id,
            'descr'             => $descr,
            'date'              => date("Y-m-d"),
            'time'              => date("H:i:s"),
            'utx'               => time()
        );
        $this->_model_out->Insert($out_data);
        return true;
		}
    }
	
    /*
     * Обновление баланса
     *  Складываем приходы вычитаем расходы - обновляем
     */
    public function Recalc($user_id=null) {
        if (is_null($user_id)) {
            /* Обновление баланса для всех пользователей */
        } else {
			/* Списание бонусных средств у которых истек срок */
			$this->offBonus($user_id);
            /* Обновление баланса для одного пользователя */
            $user_id = (empty($user_id))?$_SESSION['LOGIN_Id']:$user_id;
            $in_query = "SELECT SUM(`in`.`money`) AS `money` FROM `{$this->_model_in->getdatabasename()}`.`{$this->_model_in->gettablename()}` AS `in` WHERE `in`.`uid`='{$user_id}'";
            $in_money = $this->db()->getOne($in_query);
           
		    $bonus_query = "SELECT SUM((`in`.`money` - `in`.`used_money` - `in`.`off_money`)) AS `bonus` FROM `{$this->_model_in->getdatabasename()}`.`{$this->_model_in->gettablename()}` AS `in` WHERE `is_bonus`='1' AND `in`.`uid`='{$user_id}'";
            $bonus = $this->db()->getOne($bonus_query);
			
            $out_query = "SELECT SUM(`out`.`money`) AS `out` FROM `{$this->_model_out->getdatabasename()}`.`{$this->_model_out->gettablename()}` AS `out` WHERE `out`.`uid`='{$user_id}'";
            $out_money = $this->db()->getOne($out_query);

            $balance = $in_money-$out_money;
            $user_data = array(
                'uid'   => $user_id,
                'money' => $balance,
                'bonus' => $bonus,
                'utx'   => time()
            );
            $this->InsertUpdate($user_data);
            return true;
        }
    }
	
	public function offBonus($user_id=null){
		if (is_null($user_id)) {
			
		}
		else {
			$bonuses = $this->_model_in->getItemsWhere("`use_date`<".time()." AND `is_bonus`='1' AND `use_date`!=0 AND (`used_money` + `off_money`)<`money`", '`use_date`');
			foreach($bonuses as $i => $bonuse){
				$rest = $bonuse['money'] - $bonuse['used_money'];
				if($rest>0){
					$this->registerOutBonuse($user_id, $rest, 'Списание неиспользованных бонусов по истечению времени.' );
					$data = ['id' =>$bonuse['id'], 'off_money'=>$rest];
					$this->_model_in->Update($data, $bonuse['id']);
					/**/
				}
			}
		}
	}

    /**
     * История изменения баланса пользователя
     * @param type $user_id
     * @param type $date_start
     * @param type $date_end
     */
    public function getHistory($user_id,$date_start=null,$date_end=null,$limit_start=null,$limit_count=null) {
		$query = "
            SELECT
                `union`.*
                FROM (
                    (
                        SELECT
                            `in`.`id`,
                            `in`.`transaction_id` AS `tr_id`,
                            `in`.`money`,
                            `in`.`descr`,
                            '0' AS `service_id`,
                            '0' AS `service_item_id`,
                            `in`.`date`,
                            `in`.`time`,
                            `in`.`utx`,
                            'IN' AS `type`,
                            '#1abc3a' AS `color`,
							`in`.`is_bonus`,
							`in`.`use_date`,
							`in`.`used_money`,
							`in`.`off_money`,
							`in`.`acc_discount`
                        FROM 
                            `{$this->_model_in->getdatabasename()}`.`{$this->_model_in->gettablename()}` AS `in`
                        WHERE
                            `in`.`uid`='{$user_id}'
                        ".((!is_null($date_start)) ? "
                                AND
                            `in`.`date`>='{$date_start}'
                            " : "")."
                        ".((!is_null($date_end)) ? "
                                AND
                            `in`.`date`<='{$date_end}'
                            " : "")."
                    ) UNION (
                        SELECT
                            `out`.`id`,
                            '0' AS `tr_id`,
                            `out`.`money`,
                            `out`.`descr`,
                            `out`.`service_id`,
                            `out`.`service_item_id`,
                            `out`.`date`,
                            `out`.`time`,
                            `out`.`utx`,
                            'OUT' AS `type`,
                            '#f44242' AS `color`,
							'0' as `is_bonus`,
							'0' as `use_date`,
							'0' as `used_money`,
							'0' as `off_money`,
							'0' as `acc_discount`
                        FROM
                            `{$this->_model_out->getdatabasename()}`.`{$this->_model_out->gettablename()}` AS `out`
                        WHERE
                            `out`.`uid`='{$user_id}'
                        ".((!is_null($date_start)) ? "
                                AND
                            `out`.`date`>='{$date_start}'
                            " : "")."
                        ".((!is_null($date_end)) ? "
                                AND
                            `out`.`date`<='{$date_end}'
                            " : "")."
                    )
                ) AS `union`
            WHERE
                1
            ORDER BY
                `union`.`utx` DESC
            ".((!is_null($limit_start) and !is_null($limit_count)) ? "
            LIMIT
                {$limit_start} , {$limit_count}
                " : "")."
            ";
        return $this->db()->getAll($query);
    }
	
}
?>