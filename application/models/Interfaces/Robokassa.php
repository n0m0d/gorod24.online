<?php

class Payment_Interface_Robokassa extends Payment_Interface {
    
    public function __construct(&$iterator) {
        $this->_name = 'Robokassa';
        $this->_descr= <<<HTML
<div  class="robokassa pay_description">
            <ul>
                <li>Вы можете оплатить через: WebMoney (R, Z кошелек), Яндекс.Деньги,
                    Деньги@mail.ru и другие виды электронных денег;
                </li>
                <li>Оплата зачисляется в режиме online;</li>
                <li>Комиссия отсутствует;</li>
            </ul>
</div>


HTML;
        $this->_logo_small='/App/modules/payment/interfaces/robokassa.png';
        $this->_logo_medium='/App/modules/payment/interfaces/robokassa.png';
        $this->_logo_big='/App/modules/payment/interfaces/robokassa.png';
        
        $this->_settings    = array(
            'MrchLogin' => array(
                'type'      => 'text',
                'label'     => 'Логин в системе Robokassa',
                'require'   => true
            ),
            'MrchPass1' => array(
                'type'      => 'password',
                'label'     => 'Пароль #1 в системе Robokassa',
                'require'   => true
            ),
            'MrchPass2' => array(
                'type'      => 'password',
                'label'     => 'Пароль #2 в системе Robokassa',
                'require'   => true
            ),
            'isDebug'   => array(
                'type'      => 'check',
                'label'     => 'Режим отладки',
                'default'   => '0'
            )
        );
        
        parent::__construct($iterator);
    }
	
    public function admin_get_transaction_toolbar($tr_id){
        return '';
    }
	
    public function transaction_start($transaction_id,$transaction_price,$transaction_desc,$request) {
        
        $mrh_login = $this->_configed_settings['MrchLogin'];
        $mrh_pass1 = $this->_configed_settings['MrchPass1'];
		//if(($_SESSION['LOGIN_Id']==14600 or $_SESSION['LOGIN_Id']==41)){ $mrh_pass1 = 'hrirSY26M5v7rCobj3qS'; }
	/*
	pass1 = J3Q5ypnH30zvX1taYZDa
	pass2 = IfrQ82pA18gOpvxdw1kh
	
	for Test
	pass1 = hrirSY26M5v7rCobj3qS
	pass2 = P15LNdxD1InCUrI3Nr2s
	
	*/
  
  
        /*
         * 
            $mrh_login = "Test1999";
            $mrh_pass1 = "password_1";
            $inv_id = 678678;
            $inv_desc = "Товары для животных";
            $out_summ = "100.00";
            $IsTest = 1;
            $crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1");
         */
        $inv_id = $transaction_id; 
        $inv_desc = $transaction_desc;
        $out_summ = "{$transaction_price}";
        
        $crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1");
        
        $fields = array(
            'MrchLogin'     => $mrh_login,
            'OutSum'        => $out_summ,
            'InvId'         => $inv_id,
            'Desc'          => $inv_desc,
            'SignatureValue'=> $crc
        );
        if ($this->_configed_settings['isDebug']) {
            $fields['IsTest'] = '1';
        }
        $target = "https://auth.robokassa.ru/Merchant/Index.aspx";
        /*
        if ($this->_configed_settings['isDebug']) {
            $target = "http://test.robokassa.ru/Index.aspx";
        }
         * 
         */
        return array(
            'target'    => $target,
            'fields'    => $fields
        );
    }
    public function transaction_success($request) {
        if (isset($request['OutSum'])
            and isset($request['InvId'])
            and isset($request['SignatureValue'])
        ) {
            $mrh_login = $this->_configed_settings['MrchLogin'];
            $mrh_pass1 = $this->_configed_settings['MrchPass1'];
			//if(($_SESSION['LOGIN_Id']==14600 or $_SESSION['LOGIN_Id']==41)){ $mrh_pass1 = 'hrirSY26M5v7rCobj3qS'; }
			
            $out_summ = $request['OutSum'];
            $inv_id = $request['InvId'];
            
            $sign = "$out_summ:$inv_id:$mrh_pass1";
            $crc =strtoupper(md5($sign));
            $in_crc =strtoupper($request['SignatureValue']);
            
            if ($crc==$in_crc) {
                
                $transaction = $this->_iterator->get_transaction_by_id($inv_id);
                
                if ($transaction['invoice_price']==$out_summ) {
                
                    return $transaction;
                }
            }
        }
        return false;
    }
    public function transaction_fail($request) {
        
        if (isset($request['OutSum'])
            and isset($request['InvId'])
        ) {
            
            $out_summ = $request['OutSum'];
            $inv_id = $request['InvId'];
            
            
            $transaction = $this->_iterator->get_transaction_by_id($inv_id);
            if ($transaction['invoice_price']==$out_summ) {
                $this->_iterator->process_fail($transaction['id']);
                return $transaction;
            }
        }
        return false;
    }
    public function transaction_complete($request) {
        if (isset($request['OutSum'])
            and isset($request['InvId'])
            and isset($request['SignatureValue'])
        ) {
            $mrh_login = $this->_configed_settings['MrchLogin'];
            $mrh_pass2 = $this->_configed_settings['MrchPass2'];
			//if(($_SESSION['LOGIN_Id']==14600 or $_SESSION['LOGIN_Id']==41)){ $mrh_pass2 = 'P15LNdxD1InCUrI3Nr2s'; }
            $out_summ = $request['OutSum'];
            $inv_id = $request['InvId'];
            
            $sign = "$out_summ:$inv_id:$mrh_pass2";
            $crc =strtoupper(md5($sign));
            $in_crc =strtoupper($request['SignatureValue']);
            
            if ($crc==$in_crc) {
                $transaction = $this->_iterator->get_transaction_by_id($inv_id);
                if ($transaction['invoice_price']==$out_summ) {
                    $this->_iterator->process_transaction($transaction['id']);
                    return array(
                        'answer'    => 'OK'.$transaction['id']
                    );
                } else {
                    return array(
                        'answer'    => 'summ not matched'
                    );
                }
            } else {
                return array(
                    'answer'    => 'signature incorrect'
                );
            }
        } else {
            return array(
                'answer'    => 'bad request'
            );
        }
    }
    
    public function get_fields($invoice_id,$invoice_price) {
        return array(
         
        );
        parent::get_fields($invoice_id,$invoice_price);
    }
}
?>