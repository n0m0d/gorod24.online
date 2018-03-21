<?php
class model_payment extends Model
{
	protected $settings; public function settings(){ return $this->settings; }
	protected $services; public function services(){ return $this->services; }
	protected $packages; public function packages(){ return $this->packages; }
	protected $packages_items; public function packages_items(){ return $this->packages_items; }
	protected $invoices; public function invoices(){ return $this->invoices; }
	protected $transactions; public function transactions(){ return $this->transactions; }
    protected $_interfaces      = array();
	
	function __construct($config = array()) {
		
		$settings_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "payment_settings",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
                'provider'          => "VARCHAR(45) NOT NULL DEFAULT ''",
                'settings'        	=> "LONGTEXT NOT NULL DEFAULT ''",
				),
			"index" => array(
                'fk_provider'        => array( 'provider' ),
			),
			"unique" => array(
                'u_provider'           => array( 'provider' ),
			),
			"fulltext" => array(
				
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->settings = new Model($settings_config);
		
		$services_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "payment_services",
            "engine" => "InnoDB",
            "version" => "1.2",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'name'      => "TEXT NOT NULL DEFAULT ''",
                'descr'     => "TEXT NOT NULL DEFAULT ''",
                'price'     => "DOUBLE NOT NULL DEFAULT '0'",           /* 1.1 */
                'spec_price'=> "DOUBLE NULL DEFAULT NULL",              /* 1.3 */
                'spec_price_start'=> "DATE NULL DEFAULT NULL",          /* 1.3 */
                'spec_price_end'=> "DATE NULL DEFAULT NULL",            /* 1.3 */
                'spec_for'	=> "TEXT NULL DEFAULT NULL",            /* 1.3 */
                'php_class' => "TEXT NOT NULL DEFAULT ''",
                'on_off'    => "ENUM('0','1') NOT NULL DEFAULT '1'",
            ),
            'index' => array(
                'onoff'     => array( 'on_off' )
			),
            'unique'    => array(
			),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->services = new Model($services_config);
		
		$packages_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "payment_services_packages",
            "engine" => "InnoDB",
            "version" => "1.1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'name'      => "VARCHAR(255) NOT NULL DEFAULT ''",
                'title'     => "VARCHAR(255) NOT NULL DEFAULT ''",
                'descr'     => "VARCHAR(255) NOT NULL DEFAULT ''",
                'period'    => "VARCHAR(255) NOT NULL DEFAULT ''",
                'period_int'=> "INT(11) NOT NULL DEFAULT '0'",
                'discount'  => "DOUBLE NOT NULL DEFAULT 0",
				'sort'      => "INT(11) NOT NULL DEFAULT '0'",
                'show_on_limits' => "ENUM('0','1') NOT NULL DEFAULT '0'",
                'package_for' => "VARCHAR(255) NOT NULL DEFAULT ''",
                'on_off'    => "ENUM('0','1') NOT NULL DEFAULT '1'",
            ),
            'index' => array(
                'onoff'     => array( 'on_off' )
			),
            'unique'    => array(
			),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->packages = new Model($packages_config);
		
		$packages_items_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "payment_services_packages_items",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'pid'     => "INT NOT NULL DEFAULT 0",
                'sid'     => "INT NOT NULL DEFAULT 0",
                'amount'     => "INT NOT NULL DEFAULT 0",
            ),
            'index' => array(
			),
            'unique'    => array(
			),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->packages_items = new Model($packages_items_config);
		
		$invoices_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "payment_invoices",
            "engine" => "InnoDB",
            "version" => "1.1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'uid'               => "INT NOT NULL DEFAULT '0'",
                'package_id'        => "INT NOT NULL DEFAULT '0'",
                'service_id'        => "INT NOT NULL DEFAULT '0'",
                'service_item_id'   => "INT NOT NULL DEFAULT '0'",
                'service_descr'     => "TEXT NOT NULL DEFAULT ''",              /* 1.1 */
                'descr'             => "TEXT NOT NULL DEFAULT ''",
                'price'             => "DOUBLE NOT NULL DEFAULT '0'",
                'hash'              => "VARCHAR(32) NOT NULL DEFAULT ''",
                'utx_add'           => "INT NOT NULL DEFAULT '0'",
                'utx_pay'           => "INT NOT NULL DEFAULT '0'",
                'ip'           		=> "VARCHAR(100) NULL DEFAULT NULL",
                'agent'           	=> "VARCHAR(255) NULL DEFAULT NULL"
            ),
            'index' => array(
                'fk_uid'            => array( 'uid' ),
                'fk_service'        => array( 'service_id' ),
                'fk_serivce_item'   => array( 'service_item_id' )
			),
            'unique'    => array(
			),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->invoices = new Model($invoices_config);
		
		$transactions_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "payment_transactions",
            "engine" => "InnoDB",
            "version" => "1.5",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'uid'               => "INT NOT NULL DEFAULT '0'",
                'invoice_id'        => "INT NOT NULL DEFAULT '0'",
                'invoice_price'     => "DOUBLE NOT NULL DEFAULT '0'",
                'invoice_desc'      => "TEXT NOT NULL DEFAULT ''",      /* 1.2 */
                'hash'              => "BINARY(16) NOT NULL DEFAULT 0x0000000000000000",    /* MD5(uid:invoice_id:invoice_price:add_time:serialize(exdata)) */
                'exdata'            => "LONGTEXT NOT NULL DEFAULT ''",
                'status'            => "ENUM('0','1','2','3','4','5') NOT NULL DEFAULT '0'",    /* mod 1.3 */
                'provider'          => "VARCHAR(45) NOT NULL DEFAULT ''",
                'IncCurrLabel'      => "VARCHAR(100) NULL DEFAULT NULL",
                'error'             => "TEXT NOT NULL DEFAULT ''",
                'render_class'      => "VARCHAR(45) NOT NULL DEFAULT ''",                       /* 1.4 */
                'redirect_success'  => "VARCHAR(250) NOT NULL DEFAULT ''",                      /* 1.5 */
                'redirect_fail'     => "VARCHAR(250) NOT NULL DEFAULT ''",                      /* 1.5 */
                'add_time'          => "INT NOT NULL DEFAULT '0'",
                'start_time'        => "INT NOT NULL DEFAULT '0'",      /* 1.1 */
                'pay_time'          => "INT NOT NULL DEFAULT '0'",
                'err_time'          => "INT NOT NULL DEFAULT '0'",
                'cancel_time'       => "INT NOT NULL DEFAULT '0'"       /* 1.1 */
            ),
            'index' => array(
                'fk_uid'        => array( 'uid' ),
                'fk_invoice'    => array( 'invoice_id' ),
                'status'        => array( 'status' )
			),
            'unique'    => array(
                'uniq'  => array( 'invoice_id' , 'invoice_price' , 'hash' , 'add_time' )
			),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->transactions = new Model($transactions_config);
		
        /*
         * Подгружаем интерфейсы оплаты
         */
		$interfaces_dir = APPDIR . "/application/models/Interfaces/";
        if (file_exists($interfaces_dir) and is_dir($interfaces_dir)) {
			$files = scandir($interfaces_dir);
            foreach ($files as $K=>$f) {
                if (is_file($interfaces_dir.$f) and (substr($f,-4)=='.php')) {
					if($f == 'Interfaces.php'){
                        require_once $interfaces_dir.$f;
					}
					else {
					$i_name = substr($f,0,-4);
                    $i_class_name = 'Payment_Interface_'.$i_name;
                    if (!class_exists($i_class_name)) {
                        require_once $interfaces_dir.$f;
                        if (class_exists($i_class_name)) {
                            $cls = new $i_class_name($this);
                            if (!$cls->is_disabled()) {
                                $this->_interfaces[$i_name] = $cls;
                            }
                        }
                    }
					}
                }
            }
		}
		
        /*
         * Подгружаем услуги
         */
		$services_dir = APPDIR . "/application/models/services/";
        if (file_exists($services_dir) and is_dir($services_dir)) {
			$files = scandir($services_dir);
            foreach ($files as $K=>$f) {
                if (is_file($services_dir.$f) and (substr($f,-4)=='.php')) {
                    require_once $services_dir.$f;
                }
            }
		}
		
	}
	
    public function getInterfaceSettings($provider) {
        return $this->getSettings($provider);
    }
	
    /**
     * 
     * @param type $provider_code
     * @return Provider_Interface
     */
    public function get_interface($provider_code) {
        foreach ($this->_interfaces as $ikey=>&$iclass) {
            if (strtolower($ikey)==strtolower($provider_code)) {
                return $iclass;
            }
        }
        return false;
    }
	
    public function getSettingsAll() {
        $rows = $this->settings->getItemsWhere('1');
        $ret = array();
        foreach ($rows as $K=>&$row) {
            $ret[$row['provider']] =unserialize($row['settings']);
        }
        return $ret;
    }
    
	public function getSettings($provider) {
        $row = $this->settings()->getItemWhere("`provider`='{$provider}'");
        if (is_array($row)) {
            return unserialize($row['settings']);
        } else return false;
    }
    
	public function setSettings($provider,$new_settings) {
        $data = array(
            'provider'  =>  $provider,
            'settings'  =>  serialize($new_settings)
        );
        $this->settings->InsertUpdate($data);
    }
	
	
    /**
     * Обработка счета после оплаты
     * @param type $invoice_id
     */
    public function getInvoice($invoice_id) {
        return $this->invoices()->getItemWhere("`id`='{$invoice_id}'");
        
    }
	
    /**
     * 
     * @param type $user_id
     * @param type $descr
     * @param type $price
     * @param type $package_id
     * @param type $service_id
     * @param type $service_item_id
     */
    
	public function registerInvoice($user_id, $descr, $price, $package_id=0, $service_id=0, $service_item_id=0, $service_descr='') {
        $invoice_hash = md5(time().$user_id.$descr.$price.$package_id.$service_id.$service_item_id.$service_descr);
        $new_invoice = array(
            'uid'               => $user_id,
            'package_id'        => $package_id,
            'service_id'        => $service_id,
            'service_item_id'   => $service_item_id,
            'service_descr'     => $service_descr,
            'descr'             => $descr,
            'price'             => $price,
            'hash'              => $invoice_hash,
            'utx_add'           => time(),
            'utx_pay'           => 0,
			'ip'				=> $_SERVER['REMOTE_ADDR'],
			'agent'				=> $_SERVER['HTTP_USER_AGENT']
        );
        $new_invoice['id'] = $this->invoices()->Insert($new_invoice);
        return $new_invoice;
    }
	
    /**
     * Создает транзакцию и возвращает ее хеш/id
     * @param type $user_id
     * @param type $invoice_id
     * @param type $invoice_price
     * @param type $exdata
     */
    public function transaction_begin($user_id,$invoice_id,$invoice_price,$invoice_desc,$exdata,$render_class='',$redirect_success='',$redirect_fail='', $IncCurrLabel='') {
        /* MD5(uid:invoice_id:invoice_price:add_time:serialize(exdata)) */
        $transaction_addtime = time();
        $transaction_hash = "0x".md5($user_id.":".$invoice_id.":".$invoice_price.":".$transaction_addtime.":".serialize($exdata));
        
        $transaction_data = array(
            'uid'               => $user_id,
            'invoice_id'        => $invoice_id,
            'invoice_price'     => $invoice_price,
            'invoice_desc'      => $invoice_desc,
            'hash'              => $transaction_hash,
            'exdata'            => serialize($exdata),
            'render_class'      => $render_class,
            'redirect_success'  => $redirect_success,
            'redirect_fail'     => $redirect_fail,
            'status'            => '0',
            'provider'          => '',
            'IncCurrLabel'      => $IncCurrLabel,
            'error'             => '',
            'add_time'          => $transaction_addtime,
            'pay_time'          => '',
            'err_time'          => '',
        );
        $transaction_id = $this->transactions()->Insert($transaction_data);
        return array(
            'id'            => $transaction_id,
            'invoice_id'    => $invoice_id,
            'invoice_price' => $invoice_price,
            'invoice_desc'  => $invoice_desc,
            'exdata'        => $exdata,
            'hash'          => $transaction_hash
        );
    }
    
	/**
     * Начало транзакции 
     *  Получает хеш транзакции и название провайдера
     *      Вызывает функцию transaction_start у провайдера, которая подсчитывает нужные параметры
     *      возвращает список полей для формы отправки и ссылку цели для формы
     * @param type $transaction_hash
     * @param type $provider
     */
    public function transaction_start($transaction_id, $provider, $request) {
        $transaction = $this->transactions()->getItemWhere("`id`={$transaction_id}");
		if (is_array($transaction)) {
            /* Транзакция еще не запущена */
            if ($transaction['status']=='0') {
                if (isset($this->_interfaces[$provider])) {
                    /* Если вообще есть такой интерфейс */
                    $transaction_data = array(
                        'id'            => $transaction['id'],
                        'status'        => '1',
                        'provider'      => $provider,
                        'start_time'    => time()
                    );
                    $this->transactions()->Update($transaction_data,$transaction_data['id']);
                    return $this->_interfaces[$provider]->transaction_start($transaction['id'],$transaction['invoice_price'],$transaction['invoice_desc'],$request);
                } else {
                    return array(
                        'error'     => 'unknown provider',
                        'error_code'=> 3
                    );
                }
            } else {
                return array(
                    'error'     => 'transaction is begined',
                    'error_code'=> 2
                    
                );
            }
        } else {
            return array(
                'error'         => 'transaction not found',
                'error_code'    => 1
            );
        }
    }
	
    /**
     * 
     * @param type $transaction_id
     */
    public function process_transaction($transaction_id, $in_data=array(), $action=false) {
        $transaction = $this->transactions()->getItemWhere("`id`='{$transaction_id}'");
        if (is_array($transaction)) {
            $transaction_data = array(
                'id'            => $transaction['id'],
                'status'        => '2',
                'pay_time'      => time()
            );
            $this->transactions()->Update($transaction_data,$transaction_data['id']);
			
			$invoice = $this->invoices()->getItemWhere("`id`='{$transaction['invoice_id']}'");
			if(!empty($invoice)){
				$model_balance = new model_balance();
				if(($invoice['package_id']!=0 OR $invoice['service_id']!=0) AND $invoice['service_item_id']!=0){
					$model_balance->registerIn($invoice['uid'],$transaction_id,$transaction['invoice_price'],$transaction['invoice_desc']);
					if($invoice['package_id']!=0){
						$pay_descr = $this->packageProcessPay($invoice['uid'],$invoice['package_id'],$invoice['service_item_id']);
					}
					else {
						$pay_descr = $this->processPay($invoice['uid'],$invoice['service_id'],$invoice['service_item_id']);
					}
					
					if ($pay_descr!==false) {
						$model_balance->registerOut($invoice['uid'],$invoice['price'],$pay_descr,$invoice['package_id'],$invoice['service_id'],$invoice['service_item_id']);
					}
					/*
					if(!$action){
						$ActionsModel = new Payment_ActionsModel();
						$ActionsModel->chechPayActons($transaction_id);
					}
					*/
				}
				else {
					$params = [$invoice['uid'], $transaction_id, $transaction['invoice_price'], $transaction['invoice_desc']];
					if(empty($in_data)){
						call_user_func_array(array($model_balance, "registerIn"), $params);
					}
					else {
						$params[] = (!empty($in_data['bonus'])?true:false);
						$params[] = (!empty($in_data['use_date'])?$in_data['use_date']:null);
						$params[] = (!empty($in_data['bounty'])?true:false);
						$params[] = (!empty($in_data['bounty21200'])?true:false);
						$params[] = (!empty($in_data['acc_id'])?$in_data['acc_id']:null);
						$params[] = (!empty($in_data['acc_discount'])?$in_data['acc_discount']:null);
						$params[] = (!empty($in_data['pred_id'])?$in_data['pred_id']:null);
						call_user_func_array(array($model_balance, "registerIn"), $params);
					}
					/*
					$money = $model_balance->getForUser($invoice['uid']);
					$AdventuresModel = new Adv_AdventuresModel();
					$phones = $AdventuresModel->getUserPhones($invoice['uid']);
					
					$sms = new Payment_SMSModel();
					if(!empty($phones)){
						foreach($phones as $i=>$phone){
							if(!$action){
								switch($in_data['type']){
									case "1":
										$sms->send($phone['phone'], "Ваш баланс пополнен на ".number_format($invoice['price'],2,',', ' ').' руб. Текущий баланс: '.number_format($money,2,',', ' ').' руб.');
										break;
									case "2":
										$sms->send($phone['phone'], "Вы получили бонус на счет в размере ".number_format($invoice['price'],2,',', ' ').' руб. Текущий баланс: '.number_format($money,2,',', ' ').' руб.');
										break;
									case "3":
										$sms->send($phone['phone'], "Ваш баланс пополнен на ".number_format($invoice['price'],2,',', ' ').' руб. Текущий баланс: '.number_format($money,2,',', ' ').' руб.');
										break;
								}
								
							}
							else {
								$sms->send($phone['phone'], "Ваш баланс пополнен на ".number_format($invoice['price'],2,',', ' ').' руб. Текущий баланс: '.number_format($money,2,',', ' ').' руб.');
							}
						}
					}
					
					if(!$action){
						$ActionsModel = new Payment_ActionsModel();
						$ActionsModel->chechBalanceActons($transaction_id);
					}
					*/
				}
				return true;
			}
			else return false;
        }
        return false;
    }
	
    public function get_transaction_by_id($transaction_id) {
        $transaction = $this->transactions()->getItemWhere("`id`='{$transaction_id}'");
        $transaction['exdata'] =unserialize($transaction['exdata']);
        return $transaction;
    }
	
    public function transaction_cancel($transaction_id) {
        $transaction = $this->get_transaction_by_id($transaction_id);
        if (is_array($transaction)
            and ($transaction['status']!='2')
            and ($transaction['status']!='3')
        ) {
            $transaction_data = array(
                'id'            => $transaction['id'],
                'status'        => '5',
                'cancel_time'   => time()
            );
            
            $this->transactions()->Update($transaction_data,$transaction_data['id']);
            return true;
        } else {
            return false;
        }
    }
    /**
     * 
     * @param type $transaction_id
     */
    public function process_fail($transaction_id) {
        $transaction = $this->transactions()->getItemWhere("`id`='{$transaction_id}'");
        if (is_array($transaction)) {
            $transaction_data = array(
                'id'            => $transaction['id'],
                'status'        => '3',
                'err_time'      => time()
            );
            $this->transactions()->Update($transaction_data,$transaction_data['id']);
            return true;
        }
        return false;
    }
	
	public function getPaymentPackages($for='adv', $sub_cat=null, $simple=false){
		$ignore = [1,2];
		if(!$simple){
			array_push($ignore, 15,16,17,18 );
		}
		$packages = $this->packages()->getItemsWhere("`on_off`='1' AND `package_for` LIKE '%;{$for};%' AND id NOT IN (".implode(',',$ignore).")", '`sort`, `id`');
		$array = [];
		foreach($packages as $i => $row){
			if(!$array[$row['name']]){ $array[$row['name']] = [ 'name'=>$row['name'], 'title'=>$row['title'], 'title'=>$row['title'], 'descr'=>$row['descr'], 'items'=>[] ]; }
			$price = $this->getPricePackage($row['id'], $sub_cat);	
			$row['price'] = $price;
			$array[$row['name']]['items'][] = $row;
		}
		$parsed = [];
		foreach($array as $item){
			$parsed[] = $item;
		}
		return $parsed;
	}
	
	public function getPaymentMethods($personalSum = null, $personalDiscount = null){
		$result = [
			[ "name"=>"PersonalAccount", "title"=>"Лицевой счет", "icon"=>"", "sum" => $personalSum, "discount" => $personalDiscount ],
			[ "name"=>"QCardR", "title"=>"Банковская карта", "icon"=>"" ],
			[ "name"=>"Qiwi", "title"=>"Qiwi кошелек", "icon"=>"" ],
			[ "name"=>"WMR30QM", "title"=>"Webmoney", "icon"=>"" ],
			[ "name"=>"MixplatMTSQiwiR", "title"=>"MTS", "icon"=>"" ],
			[ "name"=>"YandexMerchantQiwiR", "title"=>"Яндекс деньги", "icon"=>"" ],
		];
		return $result;
	}
	
	public function getPricePackage($id, $sub_cat=null){
		$items = $this->getItemsWithServices($id);	
		$price = 0;
		foreach($items as $item){
			if(is_null($sub_cat)){
				$price += ($item['amount']*$item['price']);
			}
			else{
				$for = explode(',',$item['spec_for']);
				$CURDATE = date("Y-m-d");
				if( (empty($item['spec_price_start']) AND empty($item['spec_price_end'])) OR ($item['spec_price_start']<=$CURDATE AND $CURDATE<=$item['spec_price_end']) ){
					if(in_array($sub_cat, $for)){
						$price += ($item['amount']*$item['spec_price']);
					}
					else {
						$price += ($item['amount']*$item['price']);
					}
				}
				else {
					$price += ($item['amount']*$item['price']);
				}
			}
		}
		return $price;
	}
	
	public function getItemsWithServices($id){
		$query = "SELECT * FROM `{$this->packages_items->getdatabasename()}`.`adv_payment_services_packages_items` as t1, `{$this->packages->getdatabasename()}`.`adv_payment_services` as t2 WHERE t1.sid=t2.id AND t1.pid=".$id;
		return $this->packages_items->db()->getAll($query);
	}
	
    public function processPay() {
		$args = func_get_args();
		$user_id = $args[0]; $service_id = $args[1]; $service_item_id = $args[2];
        $service = $this->services()->getItemWhere("`id`='{$service_id}' AND `on_off`='1'");
        if (is_array($service)) {
            if (class_exists($service['php_class'])) {
                $pr = new $service['php_class']();
				return call_user_func_array(array($pr, 'processPay'), $args);
            } else {
                return false;
            }
        } else {
            return false;
        }
        return false;
        return "Оплата пакета усгул #{$service_id} для элемента #{$service_item_id}";
    }
	
	public function packageProcessPay($user_id, $package_id, $service_item_id){
		$args = func_get_args();
		$items = $this->packages_items->getItemsWhere("`pid`={$package_id}");
		foreach($items as $i=>$service){
			if(in_array($package_id, [11,12,13,14]) AND $service['sid']==1){
				$this->processPay($user_id, $service['sid'], $service_item_id, $package_id, $service['amount'], true);
			}
			else {
				$this->processPay($user_id, $service['sid'], $service_item_id, $package_id, $service['amount']);
			}
			
		}
		return 'Оплата пакета';
	}
}

class Payment_Service {
    public function __construct() {
        
    }
}

?>