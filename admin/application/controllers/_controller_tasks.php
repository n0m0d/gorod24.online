<?php
class controller_tasks extends Controller
{

	function action_index($array = array())
	{
		if( $this->getPermissions('tasks') ){
			if(isset($this->GET['id'])) {
				$page_id = ($this->GET['id'] == 'new')? 'new' : (int)$this->GET['id'];
				if($page_id=="new" or is_numeric($page_id)){
					$this->view->template = 'admin_view.php';
					$this->view->generate(array($this, 'renderTask'), ADMINDIR.'/application/views/'.$this->view->template);
				}
				else { Route::ErrorPage404($this->registry);}

			} else {
				$this->view->template = 'admin_view.php';
				$this->view->generate(array($this, 'renderTaskList'), ADMINDIR.'/application/views/'.$this->view->template);
			}
		}
		else {
				$this->generateLoginForm();
		}
	}
	
	public function renderTaskList(){
		$page = (int)((isset($this->GET['page']))?$this->GET['page']:1);
		$limit = (int)((isset($this->GET['limit']))?$this->GET['limit']:20);
		$start = $page * $limit - $limit;
		
		if(!empty($_GET['search'])){
			$w = "AND (task_name LIKE '%{$_GET['search']}%' OR task_descr LIKE '%{$_GET['search']}%')";
		}
		$query = "SELECT * FROM mvc_cron_tasks WHERE 1 ".$w;
		$n = "(SELECT COUNT(*) FROM ({$query}) AS `t`) AS `n`";
		$query = 'select * FROM (SELECT *, '.$n.', (select '.$limit.') as `maxrows`	FROM mvc_cron_tasks WHERE 1 '.$w.' ) as a
		ORDER BY task_adddate DESC 
		LIMIT ?i, ?i';
		$rows = $this->model->db->GetAll($query, $start, $limit);
		$n=$rows[0]['n'];$maxrows=$rows[0]['maxrows'];

		$admin = new AdminPage(
			array(
				"header" => "Задачи",
				"table" => "mvc_cron_tasks",
				"menu" => "/admin/tasks/",
				"attrs" => array("class"=>"main-table"),
				"buttons"=>array(
								array("title"=>"Название", "type"=>"link", "position"=>"left", "href"=>"/admin/tasks/?id=new", "content"=>array("title"=>"Создать", "type"=>"button", "action"=>"button", "class"=>"add" )),
								array("name"=>"delete-rows", "title"=>"Удалить отмеченные", "type"=>"delete", "action"=>"button", "class"=>"delete delete-rows"), 
								),	
				"fields" => array(
								array("title"=>"ID", "type"=>"chechbox", "position"=>"center", "name"=>"task_id"),
								array("title"=>"ID", "type"=>"link", "position"=>"left", "href"=>"/admin/tasks/?id=@[task_id]", "content"=>array("title"=>"ID", "type"=>"simply text", "position"=>"left", "name"=>"task_id")),
								array("title"=>"Название", "width"=>"50%", "type"=>"link", "position"=>"left", "href"=>"/admin/tasks/?id=@[task_id]", "content"=>array("title"=>"Название", "type"=>"simply text", "position"=>"left", "name"=>"task_name")),
								
								array("title"=>"Дата начала", "type"=>"simply text", "position"=>"center", "name"=>"task_start_date", ),
								array("title"=>"Дата окончания", "type"=>"simply text", "position"=>"center", "name"=>"task_end_date", ),
								array("title"=>"Состояние", "type"=>"simply text", "position"=>"center", "name"=>"task_status", "format"=>array(0=>"<font color='RED'>Выключен</font>", 1=>"<font color='GREEN'>Включен</font>")),
							),
			)
		);

		$admin->setItems($rows);
		$admin->renderAdminList();
		echo do_shortcode('[pagenavigation n="'.$n.'" limit="'.$maxrows.'" page="'.$page.'"]');
	}

	public function renderTask(){
		$id = (int)((isset($this->GET['id']))?$this->GET['id']:false);
		if($id != 'new'){$row=array("task_id"=>$id);}else {$row = "new";}
		
		add_action('saveTask', array($this, 'saveTask'));
		
		$admin = new AdminPage(
			array(
				"header" => "задачу",
				"table" => "mvc_cron_tasks",
				"menu" => "/admin/tasks/",
				"id" => "all-data",
				"add-date" => "task_adddate",
				"update-date" => "task_updatedate",
				"do_action" => "saveTask",
				"row" => $row,
				"fields" => array(
								array("title"=>"ID", "type"=>"hidden", "position"=>"center", "name"=>"task_id"),
								array("title"=>"Название", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_name", "name"=>"task_name", "type"=>"text")),
								array("title"=>"Краткое описание", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_descr", "name"=>"task_descr", "type"=>"medium text")),
								
								array("title"=>"Задача", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_job", "name"=>"task_job", "type"=>"select", "values"=>get_events() ) ),
								
								array("title"=>"Дата старта задачи", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_start_date", "name"=>"task_start_date", "type"=>"date")),
								array("title"=>"Время старта задачи", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_start_time", "name"=>"task_start_time", "type"=>"time")),
								
								//array("title"=>"Дата последнего запуска", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_last_launch", "name"=>"task_last_launch", "type"=>"simply text")),
								//array("title"=>"Дата следующего запуска", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_next_launch", "name"=>"task_next_launch", "type"=>"simply text")),
								
								array("title"=>"Колличество выполнений (0-бесконечно)", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_round", "name"=>"task_round", "type"=>"number")),
								array("title"=>"Интервал перед повторением (в секундах, например: 3600 сек = 1 час )", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_round_period", "name"=>"task_round_period", "type"=>"number")),
								
								
								array("title"=>"Дата окончания задачи", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_end_date", "name"=>"task_end_date", "type"=>"date")),
								array("title"=>"Время окончания задачи", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_end_time", "name"=>"task_end_time", "type"=>"time")),
								
								//array("title"=>"Выполнено (раз)", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_launches", "name"=>"task_launches", "type"=>"simply text")),
								//array("title"=>"Результат последнего запуска", "type"=>"line", "position"=>"center", "content"=>array("id"=>"task_last_launch_result", "name"=>"task_last_launch_result", "type"=>"simply text")),
								
								array("title"=>"Основные действия", "type"=>"block", "position"=>"right", "content"=>
														array(
															array("type"=>"block row", "content"=>
																	array("name"=>"task_status", "title"=>"Статус", "type"=>"check")),
															array("type"=>"block row", "content"=>
																	array("name"=>"task_finished", "title"=>"Выполнено", "type"=>"check")),
															array("type"=>"block row", "content"=>
																	array("name"=>"task_adddate", "title"=>"Дата создания", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"task_updatedate", "title"=>"Дата изменения", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"task_last_launch", "title"=>"Дата последнего запуска", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"task_next_launch", "title"=>"Дата следующего запуска", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"save", "title"=> ($row=="new"?"Создать":"Обновить"), "type"=>"button", "action"=>"submit", "class"=>"save" )),
															)	
														),
							array("title"=>"Результаты", "type"=>"block", "position"=>"right", "content"=>
														array(
															array("type"=>"block row", "content"=>
																	array("name"=>"task_launches", "title"=>"Выполнено (раз)", "type"=>"stong label")),
															array("type"=>"block row", "content"=>
																	array("name"=>"task_last_launch_result", "type"=>"json text")),
														),
							),
							),
			)
		);
		$admin->renderAdminPage();
	}
	
	public function saveTask($object){
			$data=array(
				"task_name"=>$this->POST['task_name'],
				"task_descr"=>$this->POST['task_descr'],
				"task_job"=>$this->POST['task_job'],
				"task_start_date"=>$this->POST['task_start_date'],
				"task_start_time"=>$this->POST['task_start_time'],
				"task_next_launch"=>$this->POST['task_start_date'].' '.$this->POST['task_start_time'],
				"task_next_launch_important"=>$this->POST['task_start_date'].' '.$this->POST['task_start_time'],
				"task_round"=>(int)$this->POST['task_round'],
				"task_round_period"=>(int)$this->POST['task_round_period'],
				"task_end_date"=>$this->POST['task_end_date'],
				"task_end_time"=>$this->POST['task_end_time'],
				"task_status"=>(int)$this->POST['task_status'],
				"task_finished"=>(int)$this->POST['task_finished'],
			);
		if(empty($this->POST['task_id'])){
			$this->model->db->query("INSERT INTO mvc_cron_tasks SET task_adddate=NOW(), task_updatedate=NOW(), ?u", $data);
			$primary_key_value=$this->model->db->insertId();
			echo "<script type='text/javascript'>document.location.href = '/admin/tasks/?id=$primary_key_value';</script>";
		}
		else{
			$this->model->db->query("UPDATE mvc_cron_tasks SET task_updatedate=NOW(), ?u WHERE task_id=?i", $data, $this->POST['task_id']);
			$primary_key_value=$this->POST['task_id'];
			echo "<script type='text/javascript'>document.location.href = '/admin/tasks/?id=$primary_key_value';</script>";
		}
	}
	
}
?>