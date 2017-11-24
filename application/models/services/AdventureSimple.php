<?php
/*
 * Сделать объявление випом
 */
class Payment_Services_AdventureSimple extends Payment_Service {
    public function processPay($user_id,$service_id,$item_id,$package_id,$amount, $no_border=null) {
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
            $adv_upd = array(
				'id'  => $adv['id'],
                'simple_plus' => '1',
				'up_time' => time(),
				'up_time_send' => time(),
				'on_off' => ($adv['on_off']=='4'?'2':$adv['on_off']),
            );
            $adv_model->Update($adv_upd,$adv['id']);
            //$adv_model->_table_adv_off->Update($adv_upd,$adv['id']);
			
			$model_payment = new model_payment();
			$package = $model_payment->packages()->getItemWhere("`id`=$package_id");
			
			$auto_up = $adv_model->autoup()->getItemWhere("`adv_id`='{$adv['id']}'");
				$c = 0;
				switch($amount){
					case 1: $c = 1; break;
					case 2: $c = 2; break;
					case 3: $c = 3; break;
					case 4: $c = 4; break;
					default: $c = 0; break;
				}
			if(empty($auto_up)){			
				$adv_model->setRules($adv['id'] /* Id объявления */, date('Y-m-d') /* Дата старта поднятия */, (int)$package['period_int'] /* Количество дней */, $c /* Автоматических поднятия на срок */, 1 /* Включено/выключено */);
			}
			else {
				$adv_model->setRules($adv['id'] /* Id объявления */, date('Y-m-d', $auto_up['start_time']) /* Дата старта поднятия */, (int)($auto_up['days_count'] + $package['period_int']) /* Количество дней */, ($auto_up['need_count']+$c) /* Автоматических поднятия на срок */, 1 /* Включено/выключено */);
			}
			$adv_model->AdvToUp($adv['id'], 0, true);
			
            return "Простое + -статус для объявления №{$adv['id']}";
        } else {
            return false;
        }
    }
}