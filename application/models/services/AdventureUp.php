<?php
/*
 * Сделать объявление випом
 */
class Payment_Services_AdventureUp extends Payment_Service {
    /**
     * 
     * @param type $item_id
     */
    public function processPay($user_id, $service_id, $item_id, $package_id, $amount, $no_border=null) {
		$args = func_get_args();
        $adv_model = new model_adventures();
        $adv = $adv_model->getItemWhere("`id`='{$item_id}'");
		/**/
		if (empty($adv)) {
			$adv_model_off = $adv_model->adv_off();
			$adv_off = $adv_model_off->getItemWhere("`id`='{$item_id}'");
			$adv_model->AdvToOn($adv_off['id']);
			$adv = $adv_model->getItemWhere("`id`='{$item_id}'");
		}
		
        if (is_array($adv) and !empty($adv)) {
			
			$model_payment = new model_payment();
			$Payment_ServicesPackagesModel = $model_payment->packages();
			$package = $Payment_ServicesPackagesModel->getItemWhere("`id`=$package_id");

			$auto_up = $adv_model->autoup()->getItemWhere("`adv_id`='{$adv['id']}'");
			if(empty($auto_up)){			
				$adv_model->setRules($adv['id'] /* Id объявления */, date('Y-m-d') /* Дата старта поднятия */, (int)$package['period_int'] /* Количество дней */, $amount /* Автоматических поднятия на срок */, 1 /* Включено/выключено */);
			}
			else {
				$adv_model->setRules($adv['id'] /* Id объявления */, date('Y-m-d', $auto_up['start_time']) /* Дата старта поднятия */, (int)($auto_up['days_count']) /* Количество дней */, ($auto_up['need_count']+$amount) /* Автоматических поднятия на срок */, 1 /* Включено/выключено */);
			}
			
            $adv_upd = array(
                'id'      => $adv['id'],
				'up_time' => time(),
				'up_time_send' => time(),
            );
            $adv_model->Update($adv_upd,$adv['id']);
			
            return "UP для объявления №{$adv['id']}";
        } else {
            return false;
        }
    }
}