<?php
class controller_email extends Controller
{

	function action_index($array = array())
	{
		if( $this->getPermissions('tasks') ){
			
			if(isset($this->GET['id'])) {
				$page_id = ($this->GET['id'] == 'new')? 'new' : (int)$this->GET['id'];
				if($page_id=="new" or is_numeric($page_id)){
					$this->view->template = 'admin_view.php';
					$this->view->generate(array($this, 'renderWorker'), ADMINDIR.'/application/views/'.$this->view->template);
				}
				else { Route::ErrorPage404($this->registry);}

			} 
			else if(isset($this->GET['sended']) and isset($this->GET['worker'])) {
				$this->view->template = 'admin_view.php';
				$this->view->generate(array($this, 'renderSendedList'), ADMINDIR.'/application/views/'.$this->view->template);
			}
			else if(isset($this->GET['opened']) and isset($this->GET['worker'])) {
				$this->view->template = 'admin_view.php';
				$this->view->generate(array($this, 'renderOpenedList'), ADMINDIR.'/application/views/'.$this->view->template);
			}
			else if(isset($this->GET['readed']) and isset($this->GET['worker'])) {
				$this->view->template = 'admin_view.php';
				$this->view->generate(array($this, 'renderReadedList'), ADMINDIR.'/application/views/'.$this->view->template);
			}
			else if(isset($this->GET['user'])) {
				$this->view->template = 'admin_view.php';
				$this->view->generate(array($this, 'renderUserWorkersList'), ADMINDIR.'/application/views/'.$this->view->template);
			}
			else {
				$this->view->template = 'admin_view.php';
				$this->view->generate(array($this, 'renderWorkerList'), ADMINDIR.'/application/views/'.$this->view->template);
			}
			
			
		}
		else {
				$this->generateLoginForm();
		}
	}
	
	public function renderWorkerList(){
		$page = (int)((isset($this->GET['page']))?$this->GET['page']:1);
		$limit = (int)((isset($this->GET['limit']))?$this->GET['limit']:20);
		$start = $page * $limit - $limit;
		
		if(!empty($_GET['search'])){
			$w = "AND (work_name LIKE '%{$_GET['search']}%' OR work_theme LIKE '%{$_GET['search']}%')";
		}
		
		$n = $this->model->db->GetOne("SELECT COUNT(*) FROM mvc_email_workers WHERE 1".$w); 
		$rows = $this->model->db->GetAll('SELECT *,	(SELECT COUNT( DISTINCT red_url) FROM mvc_redirects WHERE mvc_email_workers.work_id = mvc_redirects.red_work_id) as uniq_links FROM mvc_email_workers WHERE 1 '.$w.' ORDER BY work_date DESC, work_time DESC LIMIT ?i, ?i', $start, $limit);

		$admin = new AdminPage(
			array(
				"header" => "Рассылки",
				"table" => "mvc_email_workers",
				"menu" => "/admin/email/",
				"attrs" => array("class"=>"main-table"),
				"buttons"=>array(
								array("title"=>"Название", "type"=>"link", "position"=>"left", "href"=>"/admin/email/?id=new", "content"=>array("title"=>"Создать", "type"=>"button", "action"=>"button", "class"=>"add" )),
								),	
				"fields" => array(
								array("title"=>"ID", "type"=>"link", "position"=>"left", "href"=>"/admin/email/?id=@[work_id]", "content"=>array("title"=>"ID", "type"=>"simply text", "position"=>"left", "name"=>"work_id")),
								array("title"=>"Название", "width"=>"50%", "type"=>"link", "position"=>"left", "href"=>"/admin/email/?id=@[work_id]", "content"=>array("title"=>"Название", "type"=>"simply text", "position"=>"left", "name"=>"work_name")),
								
								array("title"=>"Дата начала", "type"=>"simply text", "position"=>"center", "name"=>"work_date", ),
								array("title"=>"На отправку", "type"=>"simply text", "position"=>"center", "name"=>"work_max", ),
								
								array("title"=>"Отправлено", "type"=>"simply text", "position"=>"left", "href"=>"/admin/email/?worker=@[work_id]&sended=1&work_name=@[work_name]", "content"=>array("title"=>"ID", "type"=>"simply text", "position"=>"center", "name"=>"work_sended")),
								array("title"=>"Открыто", "type"=>"link", "position"=>"left", "href"=>"/admin/email/?worker=@[work_id]&opened=1&work_name=@[work_name]", "content"=>array("title"=>"ID", "type"=>"simply text", "position"=>"center", "name"=>"work_open")),
								array("title"=>"Переходов", "type"=>"link", "position"=>"left", "href"=>"/admin/email/?worker=@[work_id]&readed=1&work_name=@[work_name]", "content"=>array("title"=>"ID", "type"=>"simply text", "position"=>"center", "name"=>"work_readed")),
								array("title"=>"Уникальных переходов", "type"=>"link", "position"=>"left", "href"=>"/admin/email/?worker=@[work_id]&readed=1&work_name=@[work_name]", "content"=>array("title"=>"ID", "type"=>"simply text", "position"=>"center", "name"=>"uniq_links")),
								
								array("title"=>"Состояние", "type"=>"simply text", "position"=>"center", "name"=>"work_status", "format"=>array(0=>"<font color='RED'>Выключена</font>", 1=>"<font color='GREEN'>Включена</font>")),
							),
			)
		);

		$admin->setItems($rows);
		$admin->renderAdminList();
		echo do_shortcode('[pagenavigation n="'.$n.'" limit="'.$limit.'" page="'.$page.'"]');
	}

	public function renderSendedList(){
		$worker = (int)((isset($this->GET['worker']))?$this->GET['worker']:0);
		$work_name = ((isset($this->GET['work_name']))?$this->GET['work_name']:'');
		
		$page = (int)((isset($this->GET['page']))?$this->GET['page']:1);
		$limit = (int)((isset($this->GET['limit']))?$this->GET['limit']:20);
		$start = $page * $limit - $limit;
		
		$n = $this->model->db->GetOne("SELECT COUNT(*) FROM mvc_mail_log WHERE m_work_id=$worker"); 
		$rows = $this->model->db->GetAll('SELECT *, 
		(SELECT sub_user_name FROM mvc_subscriptions WHERE mvc_subscriptions.sub_user_id=mvc_mail_log.m_user_id LIMIT 1) as user_name,	
		(SELECT COUNT( DISTINCT red_url) FROM mvc_redirects WHERE mvc_mail_log.m_id = mvc_redirects.red_mail_id) as uniq_links FROM mvc_mail_log WHERE m_work_id='.$worker.' ORDER BY m_date_send DESC LIMIT ?i, ?i', $start, $limit);
		$admin = new AdminPage(
			array(
				"header" => "Отправлено на рассылку №$worker \"$work_name\"",
				"table" => "mvc_email_workers",
				"menu" => "/admin/email/",
				"attrs" => array("class"=>"main-table"),
				"fields" => array(
								array("title"=>"ID", "type"=>"simply text", "position"=>"center", "name"=>"m_id", ),
								
								array("title"=>"Пользователь", "width"=>"50%", "type"=>"link", "position"=>"left", "href"=>"/admin/email/?user=@[m_user_id]&user_name=@[user_name]", "content"=>array("title"=>"Название", "type"=>"simply text", "position"=>"left", "content"=>"@[user_name] (@[m_address])")),
								
								array("title"=>"Дата отправки", "type"=>"simply text", "position"=>"center", "name"=>"m_date_send", ),
								array("title"=>"Открыто", "type"=>"simply text", "position"=>"center", "name"=>"m_readed", "format"=>array(0=>"<font color='RED'>Не открыто</font>", 1=>"<font color='GREEN'>Открыто</font>")),
								array("title"=>"Переходов", "type"=>"simply text", "position"=>"center", "name"=>"m_linked"),
								array("title"=>"Уникальных переходов", "type"=>"simply text", "position"=>"center", "name"=>"uniq_links"),
							),
			)
		);

		$admin->setItems($rows);
		$admin->renderAdminList();
		echo do_shortcode('[pagenavigation n="'.$n.'" limit="'.$limit.'" page="'.$page.'"]');
	}

	public function renderOpenedList(){
		$worker = (int)((isset($this->GET['worker']))?$this->GET['worker']:0);
		$work_name = ((isset($this->GET['work_name']))?$this->GET['work_name']:'');
		
		$page = (int)((isset($this->GET['page']))?$this->GET['page']:1);
		$limit = (int)((isset($this->GET['limit']))?$this->GET['limit']:20);
		$start = $page * $limit - $limit;
		
		$n = $this->model->db->GetOne("SELECT COUNT(*) FROM mvc_email_open WHERE work_id=$worker"); 
		$rows = $this->model->db->GetAll('SELECT *, 
		(SELECT sub_user_name FROM mvc_subscriptions WHERE mvc_subscriptions.sub_user_id=mvc_email_open.uid LIMIT 1) as user_name,	
		(SELECT contact_val FROM mvc_users_contacts WHERE contact_type=\'email\' AND contact_user_id=mvc_email_open.uid LIMIT 1) as m_address,	
		(SELECT COUNT( DISTINCT red_url) FROM mvc_redirects WHERE mvc_email_open.uid = mvc_redirects.red_uid) as uniq_links 
		FROM mvc_email_open WHERE work_id='.$worker.' ORDER BY `date` DESC LIMIT ?i, ?i', $start, $limit);

		$admin = new AdminPage(
			array(
				"header" => "Открыто писем из рассылки №$worker \"$work_name\"",
				"table" => "mvc_email_workers",
				"menu" => "/admin/email/",
				"attrs" => array("class"=>"main-table"),
				"fields" => array(
								array("title"=>"ID", "type"=>"simply text", "position"=>"center", "name"=>"m_id", ),
								array("title"=>"Пользователь", "width"=>"50%", "type"=>"link", "position"=>"left", "href"=>"/admin/email/?user=@[m_user_id]&user_name=@[user_name]", "content"=>array("title"=>"Название", "type"=>"simply text", "position"=>"left", "content"=>"@[user_name] (@[m_address])")),
								
								array("title"=>"Дата", "type"=>"simply text", "position"=>"center", "name"=>"date", ),
								array("title"=>"Открыто", "type"=>"simply text", "position"=>"center", "name"=>"m_readed", "format"=>array(0=>"<font color='RED'>Не открыто</font>", 1=>"<font color='GREEN'>Открыто</font>")),
								array("title"=>"Переходов", "type"=>"simply text", "position"=>"center", "name"=>"m_linked"),
								array("title"=>"Уникальных переходов", "type"=>"simply text", "position"=>"center", "name"=>"uniq_links"),
							),
			)
		);

		$admin->setItems($rows);
		$admin->renderAdminList();
		echo do_shortcode('[pagenavigation n="'.$n.'" limit="'.$limit.'" page="'.$page.'"]');
	}

	public function renderReadedList(){
		$worker = (int)((isset($this->GET['worker']))?$this->GET['worker']:0);
		$work_name = ((isset($this->GET['work_name']))?$this->GET['work_name']:'');
		
		$page = (int)((isset($this->GET['page']))?$this->GET['page']:1);
		$limit = (int)((isset($this->GET['limit']))?$this->GET['limit']:20);
		$start = $page * $limit - $limit;
		
		$n = $this->model->db->GetOne("SELECT COUNT(*) FROM mvc_redirects WHERE red_work_id=$worker"); 
		$rows = $this->model->db->GetAll('SELECT 
			*, 
			(SELECT sub_user_name FROM mvc_subscriptions WHERE mvc_subscriptions.sub_user_id=mvc_redirects.red_uid) as user_name,
			(SELECT contact_val FROM mvc_users_contacts WHERE contact_type=\'email\' AND contact_user_id=mvc_redirects.red_uid LIMIT 1) as m_address,
			(SELECT COUNT( DISTINCT red_url) FROM mvc_redirects as t WHERE red_work_id='.$worker.' and t.red_uid=mvc_redirects.red_uid) as uniq_links
		FROM mvc_redirects WHERE red_work_id='.$worker.' GROUP BY red_uid ORDER BY red_date DESC LIMIT ?i, ?i', $start, $limit);

		$admin = new AdminPage(
			array(
				"header" => "Открыто писем из рассылки №$worker \"$work_name\"",
				"table" => "mvc_email_workers",
				"menu" => "/admin/email/",
				"attrs" => array("class"=>"main-table"),
				"fields" => array(
								array("title"=>"ID", "type"=>"simply text", "position"=>"center", "name"=>"m_id", ),
								array("title"=>"Пользователь", "width"=>"50%", "type"=>"link", "position"=>"left", "href"=>"/admin/email/?user=@[m_user_id]&user_name=@[user_name]", "content"=>array("title"=>"Название", "type"=>"simply text", "position"=>"left", "content"=>"@[user_name] (@[m_address])")),
								
								array("title"=>"Дата", "type"=>"simply text", "position"=>"center", "name"=>"red_date", ),
								array("title"=>"Переходов всего", "type"=>"simply text", "position"=>"center", "name"=>"m_linked"),
								array("title"=>"Уникальных переходов", "type"=>"simply text", "position"=>"center", "name"=>"uniq_links"),
							),
			)
		);

		$admin->setItems($rows);
		$admin->renderAdminList();
		echo do_shortcode('[pagenavigation n="'.$n.'" limit="'.$limit.'" page="'.$page.'"]');
	}

	public function renderUserWorkersList(){
		$user = (int)((isset($this->GET['user']))?$this->GET['user']:0);
		$user_name = ((isset($this->GET['user_name']))?$this->GET['user_name']:'');
		
		$page = (int)((isset($this->GET['page']))?$this->GET['page']:1);
		$limit = (int)((isset($this->GET['limit']))?$this->GET['limit']:20);
		$start = $page * $limit - $limit;
		
		
		$n = $this->model->db->GetOne("SELECT COUNT(*) FROM mvc_mail_log WHERE m_user_id=$user"); 
		
		$rows = $this->model->db->GetAll('SELECT 
			m_id, m_user_id, m_address, m_title, m_who_send, m_date_send, m_date_reade, m_readed, m_linked, m_work_id, m_post_id, uniq_links,
			IF(work_name IS NULL, \'Одиночная отправка\', work_name ) as work_name
		FROM (SELECT *,	(SELECT work_name FROM mvc_email_workers WHERE mvc_email_workers.work_id=mvc_mail_log.m_work_id ) as work_name, (SELECT COUNT( DISTINCT red_url) FROM mvc_redirects WHERE mvc_mail_log.m_id = mvc_redirects.red_mail_id) as uniq_links
		FROM mvc_mail_log WHERE m_user_id='.$user.' ORDER BY m_date_send DESC LIMIT ?i, ?i) AS t', $start, $limit);
		/**/
		$admin = new AdminPage(
			array(
				"header" => "Статистика по пользователю \"$user_name\"",
				"table" => "mvc_email_workers",
				"menu" => "/admin/email/",
				"attrs" => array("class"=>"main-table"),
				"fields" => array(
								array("title"=>"ID", "type"=>"simply text", "position"=>"center", "name"=>"m_id", ),
								array("title"=>"Рассылка", "width"=>"50%", "type"=>"simply text", "position"=>"left", "content"=>"@[work_name] (@[m_address])"),
								
								array("title"=>"Дата", "type"=>"simply text", "position"=>"center", "name"=>"m_date_send", ),
								array("title"=>"Открыто", "type"=>"simply text", "position"=>"center", "name"=>"m_readed", "format"=>array(0=>"<font color='RED'>Не открыто</font>", 1=>"<font color='GREEN'>Открыто</font>")),
								array("title"=>"Переходов всего", "type"=>"simply text", "position"=>"center", "name"=>"m_linked"),
								array("title"=>"Уникальных переходов", "type"=>"simply text", "position"=>"center", "name"=>"uniq_links"),
							),
			)
		);

		$admin->setItems($rows);
		$admin->renderAdminList();
		echo do_shortcode('[pagenavigation n="'.$n.'" limit="'.$limit.'" page="'.$page.'"]');
	}

	public function renderWorker(){
		$id = (int)((isset($this->GET['id']))?$this->GET['id']:false);
		if($id != 'new'){$row=array("work_id"=>$id);}else {$row = "new";}
		
		add_action('saveEmail', array($this, 'saveEmail'));
		
		$admin = new AdminPage(
			array(
				"header" => "рассылку",
				"table" => "mvc_email_workers",
				"menu" => "/admin/email/",
				"id" => "all-data",
				"add-date" => "work_adddate",
				"update-date" => "work_updatedate",
				"do_action" => "saveEmail",
				"row" => $row,
				"fields" => array(
								array("title"=>"ID", "type"=>"hidden", "position"=>"center", "name"=>"work_id"),
								array("title"=>"Название", "type"=>"line", "position"=>"center", "content"=>array("id"=>"work_name", "name"=>"work_name", "type"=>"text")),
								
								array("title"=>"Дата старта", "type"=>"line", "position"=>"center", "content"=>array("id"=>"work_date", "name"=>"work_date", "type"=>"date")),
								array("title"=>"Время старта", "type"=>"line", "position"=>"center", "content"=>array("id"=>"work_time", "name"=>"work_time", "type"=>"time")),
								
								array("title"=>"Тема письма", "type"=>"line", "position"=>"center", "content"=>array("id"=>"work_theme", "name"=>"work_theme", "type"=>"text")),
								array("title"=>"Текст письма", "type"=>"line", "position"=>"center", "content"=>array("id"=>"work_text", "name"=>"work_text", "type"=>"long text")),
								
								array("title"=>"Событие обработчик", "type"=>"line", "position"=>"center", "content"=>array("id"=>"work_event", "name"=>"work_event", "type"=>"select", "values"=>get_email_workers() ) ),
								
								array("title"=>"Кому (если указано - то будет выслано на 1 email, если нет - то на все)", "type"=>"line", "position"=>"center", "content"=>array("id"=>"work_email_to", "name"=>"work_email_to", "type"=>"text")),
								array("title"=>"От кого (если не указано - то будет выставлено no-reply@feo.ua)", "type"=>"line", "position"=>"center", "content"=>array("id"=>"work_email_from", "name"=>"work_email_from", "type"=>"text")),
								
								
								array("title"=>"Основные действия", "type"=>"block", "position"=>"right", "content"=>
														array(
															array("type"=>"block row", "content"=>
																	array("name"=>"work_status", "title"=>"Статус", "type"=>"check")),
															array("type"=>"block row", "content"=>
																	array("name"=>"work_adddate", "title"=>"Дата создания", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"work_updatedate", "title"=>"Дата изменения", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"work_startdate", "title"=>"Дата запуска", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"work_enddate", "title"=>"Дата окончания", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"save", "title"=> ($row=="new"?"Создать":"Обновить"), "type"=>"button", "action"=>"submit", "class"=>"save" )),
															)	
														),
							array("title"=>"Результаты", "type"=>"block", "position"=>"right", "content"=>
														array(
															array("type"=>"block row", "content"=>
																	array("name"=>"work_max", "title"=>"На отправку", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"work_sended", "title"=>"Отправлено", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"work_open", "title"=>"Открыто", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"work_readed", "title"=>"Прочитано", "type"=>"stong label")),
														),
							),
							),
			)
		);
		$admin->renderAdminPage();
	}
	
	public function saveEmail($object){
			$data=array(
				"work_date"=>(!empty($this->POST['work_date'])?$this->POST['work_date']:date('Y-m-d')),
				"work_time"=>(!empty($this->POST['work_time'])?$this->POST['work_time']:date('H:i:s')),
				"work_event"=>$this->POST['work_event'],
				"work_name"=>$this->POST['work_name'],
				"work_theme"=>$this->POST['work_theme'],
				"work_text"=>$_POST['work_text'],
				"work_email_to"=>$this->POST['work_email_to'],
				"work_email_from"=>(!empty($_POST['work_email_from'])?$_POST['work_email_from']:'no-reply@feo.ua'),
				"work_status"=>(int)$this->POST['work_status'],
				"work_completed"=>0,
			);
		if(empty($this->POST['work_id'])){
			$data['work_max']=0;
			$data['work_sended']=0;
			$data['work_open']=0;
			$data['work_readed']=0;
			$data['work_adddate']=date('Y-m-d H:i:s');
			$data['work_updatedate']=date('Y-m-d H:i:s');
			$this->model->db->query("INSERT INTO mvc_email_workers SET ?u", $data);
			$primary_key_value=$this->model->db->insertId();
		}
		else{
			$data['work_updatedate']=date('Y-m-d H:i:s');
			$this->model->db->query("UPDATE mvc_email_workers SET ?u WHERE work_id=?i", $data, $this->POST['work_id']);
			$primary_key_value=$this->POST['work_id'];
		}
		
		echo "<script type='text/javascript'>document.location.href = '/admin/email/?id=$primary_key_value';</script>";
	}
	
}
?>