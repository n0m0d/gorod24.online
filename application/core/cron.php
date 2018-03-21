<?php
function schedule_event($hook, $title='title', $args = array()){
	global $model, $_cron_events;
	if(!has_action($hook))
		return false;
	$_cron_events[]=array("value"=>$hook, "name"=>$title);
	return true;
}

function get_events(){
	global $_cron_events;
	return $_cron_events;
}

function update_event($event, $result, $stream=1){
	$model_cron_tasks = new model_cron_tasks();
	$model_crons = new model_crons();
	
	if(!has_action($event['task_job'])) return false;
	
	$data = array(
				"task_last_launch"=>date('Y-m-d H:i:s'),
				"task_launches"=>($event['task_launches']+1),
				"task_last_launch_result"=>$result,
				"task_execution_log"=>'',
			);
	if($event['task_round']==1){
		$data['task_finished']=1;
		$data['task_end_date']= date('Y-m-d');
		$data['task_end_time']= date('H:i:s');
	}
	else{
		$data['task_next_launch']=date('Y-m-d H:i:s',time()+$event['task_round_period']);
		$data['task_next_launch_important']=$data['task_next_launch'];
	}
	
	if($event['task_round']==$data['task_launches']){
		$data['task_next_launch']='0000-00-00 00:00:00';
		$data['task_next_launch_important']='0000-00-00 00:00:00';
		$data['task_finished']=1;
		$data['task_end_date']= date('Y-m-d');
		$data['task_end_time']= date('H:i:s');
	}
	
	$update_event = $model_cron_tasks->Update($data, $event['task_id']);
	$model_crons->Update([
		'task_id' => $event['task_id'],
		'start_date' => date('Y-m-d H:i:s'),
		'end_date' => '0000-00-00 00:00:00'
	], $stream);
}

function cron($stream=1){
	set_time_limit(120);
	global $_cron_events;
	$model_cron_tasks = new model_cron_tasks();
	$model_crons = new model_crons();
	$events = $model_cron_tasks->getItemsWhere("
		task_next_launch != '0000-00-00 00:00:00' 
		AND task_next_launch <= NOW() 
		AND ( (task_end_date>=CURDATE() AND task_end_time>=CURTIME()) OR (task_end_date<='0000-00-00' AND task_end_time<='00:00:00') ) 
		AND task_finished=0 
		AND task_status=1
		AND task_cron_id='{$stream}'
	", "`task_id`");
	if(!empty($events)){
	foreach($events as $i => $event){
		
		if(!empty($event['task_job'])){
			if(!has_action($event['task_job'])) break;
				ob_start();
				update_event($event, '', $stream);
				do_action($event['task_job'], $event);
				$result=ob_get_clean();
				$model_crons->Update([
					'task_id' => 0,
					'end_date' => date('Y-m-d H:i:s'),
				], $stream);
				echo $result;
				
		}
		else{
			echo "Ошибка CRON задача: \"{$event['task_job']}\", id: {$event['task_id']} не найдена";
			//$model->_log("Ошибка CRON задача: \"{$event['task_job']}\", id: {$event['task_id']} не найдена", "CRON error");
			return false;
		}
	}
	}
}



?>