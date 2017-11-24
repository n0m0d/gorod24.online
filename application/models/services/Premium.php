<?php
/*
 * Продлить премиум аккаунт
 */
class Payment_Services_Premium extends Payment_Service {
    public function processPay($user_id, $service_id, $item_id, $package_id, $amount) {
		$args = func_get_args();
        $_model = new model_feo_accounts();
		$paid_to = $_model->get_premium_info($item_id);
		
		if(empty($paid_to)) $paid_to = time(); else {
			$paid_to = strtotime($paid_to['paid_to']);
			if($paid_to<time()) $paid_to = time();
		}
		
		$time = (3600 * 24 * 30 ) * $amount + $paid_to;
		$newdate = date('Y-m-d', $time);
		$_model->premium()->InsertUpdate([
			"uid" => $item_id,
			"is_paid" => 1,
			"paid_to" => $newdate,
		]);
        
		return "VIP-статус для объявления №{$adv['id']}";
    }
}