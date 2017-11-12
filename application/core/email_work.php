<?php
function schedule_email_worker($hook, $title='title', $args = array()){
	global $model, $_email_workers_events;
	if(!has_action($hook))
		return false;
	$_email_workers_events[]=array("value"=>$hook, "name"=>$title);
	return true;
}

function get_email_workers(){
	global $_email_workers_events;
	return $_email_workers_events;
}

function add_worker($options){
	global $model, $_email_workers_events;
	$data=array(
		"work_date"=>(!empty($options['date'])?$options['date']:date('Y-m-d')),
		"work_time"=>(!empty($options['time'])?$options['time']:date('H:i:s')),
		"work_max"=>0,
		"work_sended"=>0,
		"work_open"=>0,
		"work_readed"=>0,
		"work_adddate"=>date('Y-m-d H:i:s'),
		"work_updatedate"=>date('Y-m-d H:i:s'),
		"work_event"=>$options['event'],
		"work_name"=>$options['name'],
		"work_theme"=>$options['theme'],
		"work_text"=>$options['text'],
		"work_email_to"=>$options['email_to'],
		"work_email_from"=>(!empty($options['email_from'])?$options['email_from']:'no-reply@feo.ua'),
		"work_status"=>(int)(is_null($options['status'])?1:$options['status']),
		"work_completed"=>0,
		"work_post_id"=>$options['post_id'],
	);
	$model->db->query("INSERT INTO mvc_email_workers SET ?u", $data);
	return $model->db->insertId();
}

function run_worker($worker, $start){
	global $model, $_email_workers_events;
	if(empty($worker['work_email_to'])){
		$table = 'mvc_email_workers_contacts';
		$emails = apply_filters('email_list_for_worker', $model->db->GetAll("SELECT * FROM mvc_email_workers_contacts WHERE 1 AND wc_worker_id=?i ORDER BY wc_id LIMIT 30 OFFSET ?i", $worker['work_id'], $worker['work_sended']));
		if(empty($emails)){
			$table = 'mvc_users_contacts';
			$emails = apply_filters('email_list_for_worker', $model->db->GetAll("SELECT * FROM mvc_users_contacts WHERE 1 AND contact_type='email' AND contact_deliv=1 AND contact_deliv_now=1 ORDER BY contact_user_id LIMIT 30 OFFSET ?i", $worker['work_sended']));
		}
	}
	else{
		$emails = apply_filters('email_one_for_worker', $model->db->GetAll("SELECT * FROM mvc_users_contacts WHERE 1 AND contact_type='email' AND contact_val=?s", $worker['work_email_to']));
		if(empty($emails)){
			$emails=array("contact_id"=>0, "contact_user_id"=>0, "contact_type"=>"email", "contact_val"=>$worker['work_email_to'], "contact_deliv"=>1, "contact_deliv_now"=>1);
		}
	}
	$update = array();
	if($worker['work_max']==0){
		if(empty($worker['work_email_to'])){
			if($table=='mvc_email_workers_contacts'){
				$max = apply_filters('email_list_for_worker_all', $model->db->GetOne("SELECT COUNT(*) FROM mvc_email_workers_contacts WHERE 1 AND wc_worker_id=?i ORDER BY wc_id", $worker['work_id']));
			}
			else{
				$max = apply_filters('email_list_for_worker_all', $model->db->GetOne("SELECT COUNT(*) FROM mvc_users_contacts WHERE 1 AND contact_type='email' AND contact_deliv=1 AND contact_deliv_now=1 ORDER BY contact_user_id"));
			}
		}
		else {
			$max = 1;
		}
		$update['work_max']=$max;
		$worker['work_max']=$max;
		$update['work_startdate']=date('Y-m-d H:i:s');
		$worker['work_startdate']=$update['work_startdate'];
	}
	$update['work_sended']=$worker['work_sended']+count($emails);
	$worker['work_sended']=$update['work_sended'];
	if($worker['work_sended']>=$worker['work_max']){
		$update['work_completed']=1; $worker['work_completed']=1;
		$update['work_enddate']=date('Y-m-d H:i:s');
		$worker['work_enddate']=$update['work_enddate'];
	}
	$model->db->query("UPDATE mvc_email_workers SET ?u WHERE work_id=?i", $update, $worker['work_id']);
	
	if($worker['work_post_id']!=''){
		$post = $model->db->GetRow("SELECT * FROM `mvc_posts` WHERE `post_id`=?i LIMIT 1", $worker['work_post_id']);
	}
	
	foreach($emails as $i=>$email){
		if(!empty($worker['work_event']) and has_action($worker['work_event'])){
			$text = apply_filters($worker['work_event'], $worker['work_text'], $email, $post);
		}
		else{
			$text = $worker['work_text'];
		}
		$theme = apply_filters('email_worker_theme'.$worker['work_event'], $worker['work_theme']);
		$headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\nFrom: ".(!empty($worker['work_email_from'])?$worker['work_email_from']:'no-reply@feo.ua')."\r\n";
		$email_id = send_mail($email['contact_val'], $theme, $text, $headers, 'worker', $email['contact_user_id'], $worker['work_id'], $worker['work_post_id']);
		sleep(1);
	}
		
	if($worker['work_sended']>=$worker['work_max'] and $table=='mvc_email_workers_contacts'){
		$model->db->query("DELETE FROM mvc_email_workers_contacts WHERE wc_worker_id=?i", $worker['work_id']);
	}
	
}

function work(){
	set_time_limit(60);
	$start = time();
	global $model, $_email_workers_events;
	$worker = $model->db->GetRow("
	SELECT 
		* 
	FROM mvc_email_workers
	WHERE 1
		AND work_date<=CURDATE() 
		AND work_time<=CURTIME()
		AND work_completed=0 
		AND work_status=1 
	ORDER BY work_date, work_time
	LIMIT 1");
	
	if(!empty($worker)){
		//$model->_log("Запущена Рассылка задача: \"{$worker['work_event']}\", id: {$worker['work_id']}", "EMAIL WORKER run");
		run_worker($worker, $start);
		//$model->_log("Рассылка завершена: \"{$worker['work_event']}\", id: {$worker['work_id']}", "EMAIL WORKER end");
	}
}



?>