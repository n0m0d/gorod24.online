<?php
/*
Plugin Name: register
Plugin URI: register
Description: Плагин Регистрация на портале фео.рф
Version: 1.0
Author: Заднепряный Андрей
Author URI: 

*/

if(!class_exists('register', false)){
	class register{
		protected $sum;
		
		function __construct(){
			add_action( 'on_register', array($this, 'on_register'), 10, 2);
			add_action( 'on_login', array($this, 'on_login'), 10, 1);
		}
		
		function on_register( $user, $invite_code ){
			$_accounts = new model_feo_accounts();
			$inviter = $_accounts->getItemWhere("`invite_code` = '{$invite_code}'");
			$sum = 500;
			$payment = new model_payment();
			$invoice = $payment->registerInvoice($inviter['id'], "Пополнение счета", $sum, 0, 0, 0, "Пополнение счета (Активирован код приглашения #{$inviter['id']})");
			$transaction = $payment->transaction_begin($inviter['id'],$invoice['id'], $invoice['price'], $invoice['descr'], $exdata, 'Payment_Render_Test', 'https://xn--e1asq.xn--p1ai/myroot/', 'https://xn--e1asq.xn--p1ai/myroot/');
			$payment->process_transaction($transaction['id']); 
			
					$phones = $model_feo_accounts->get_phones_all($inviter['id']);
					foreach($phones as $phone){
						if($phone['country']=='+7' and $phone['on_off']=='1' AND $phone['checked']=='1'){
							SMS_GW_Send('feomedia app',$phone['phone'],'Вам пополнен баланс на '.number_format($sum,2,',','').'р. за приглашение друга');
						}
					}
		}
		
		function on_login( $user ){
			$dates = [
				'2017-12-29', '2017-12-30', '2017-12-31', 
				'2018-01-01', '2018-01-02', '2018-01-03', '2018-01-04', '2018-01-05', '2018-01-06', '2018-01-07', 
				'2018-01-08', '2018-01-09', '2018-01-10', '2018-01-11', '2018-01-12', '2018-01-13',
			];
			$curdate = date('Y-m-d');
			if( in_array($curdate, $dates) ){
				$sum = 1000;
				$model_payment = new model_payment();
				$model_feo_accounts = new model_feo_accounts();
				$ch = $model_payment->invoices()->getCountWhere("`uid`='{$user['id']}' AND descr='Пополнение за вход в приложение'");
				if($ch==0){
					
					$invoice = $model_payment->registerInvoice($user['id'], "Пополнение за вход в приложение", $sum, 0, 0, 0, "Пополнение за вход в приложение");
					$transaction = $model_payment->transaction_begin($user['id'],$invoice['id'], $invoice['price'], $invoice['descr'], $exdata, 'Payment_Render_Test', 'https://xn--e1asq.xn--p1ai/myroot/', 'https://xn--e1asq.xn--p1ai/myroot/');
					$model_payment->process_transaction($transaction['id']); 
					
					$phones = $model_feo_accounts->get_phones_all($user['id']);
					foreach($phones as $phone){
						if($phone['country']=='+7' and $phone['on_off']=='1' AND $phone['checked']=='1'){
							SMS_GW_Send('feomedia app',$phone['phone'],'Пополнение за вход в приложение на '.number_format($sum,2,',','').'р.');
						}
					}
					
					
				}
				//var_dump($ch);
			}
			
			/*
			$_accounts = new model_feo_accounts();
			$inviter = $_accounts->getItemWhere("`invite_code` = '{$invite_code}'");
			
			$payment = new model_payment();
			$invoice = $payment->registerInvoice($inviter['id'], "Пополнение счета", $this->sum, 0, 0, 0, "Пополнение счета (Активирован код приглашения #{$inviter['id']})");
			$transaction = $payment->transaction_begin($inviter['id'],$invoice['id'], $invoice['price'], $invoice['descr'], $exdata, 'Payment_Render_Test', 'https://xn--e1asq.xn--p1ai/myroot/', 'https://xn--e1asq.xn--p1ai/myroot/');
			$payment->process_transaction($transaction['id']); 
			*/
		}
		

		
	}
	global $register;
	$register = new register();
	
}
?>