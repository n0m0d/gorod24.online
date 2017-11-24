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
			$this->sum = 300;
			add_action( 'on_register', array($this, 'on_register'), 10, 2);
		}
		
		function on_register( $user, $invite_code ){
			$_accounts = new model_feo_accounts();
			$inviter = $_accounts->getItemWhere("`invite_code` = '{$invite_code}'");
			
			$payment = new model_payment();
			$invoice = $payment->registerInvoice($inviter['id'], "Пополнение счета", $this->sum, 0, 0, 0, "Пополнение счета (Активирован код приглашения #{$inviter['id']})");
			$transaction = $payment->transaction_begin($inviter['id'],$invoice['id'], $invoice['price'], $invoice['descr'], $exdata, 'Payment_Render_Test', 'https://xn--e1asq.xn--p1ai/myroot/', 'https://xn--e1asq.xn--p1ai/myroot/');
			$payment->process_transaction($transaction['id']); 
		}
		

		
	}
	global $register;
	$register = new register();
	
}
?>