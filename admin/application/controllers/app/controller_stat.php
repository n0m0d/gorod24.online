<?php
require_once( __DIR__ ."/controller_index.php");
class controller_stat extends controller_index
{
	
	function action_index($actions=null){
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url,"Статистика"=>$this->url.'/stat/'];
		$this->view->data['header'] = "Приложение";
	}
	/********** СТАТИСТИКИ **********/
	public function action_devices($actions=null){
		$this->view->headers['title'] = 'Статистика устройств | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Статистика"=>$this->url.'/stat/', "Устройства"=>$this->url.'/stat/devices/'];
		$this->view->data['header'] = 'Устройства';
		
		$model_devicelog = new model_devicelog();
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		if($_GET['from'] and $_GET['to']){
			$from  = $_GET['from'];
			$to  = $_GET['to'];
			$devices = $model_devicelog->db()->getAll("
				SELECT 
					`date` as `d_t`, 
					(SELECT COUNT(DISTINCT `imei`) FROM `gorod_devicelog` as `all`  WHERE `date` = `d_t` AND `os` != 'iOS') as `all_Android`,
					(SELECT COUNT(DISTINCT `imei`) FROM `gorod_devicelog` as `all`  WHERE `date` = `d_t` and `date` <= '{$to}' AND `os` = 'iOS') as `all_iOS`,
					(SELECT COUNT(DISTINCT `imei`) FROM `gorod_devicelog` where `date` = `d_t` AND `os` != 'iOS' and `imei` not in (SELECT `imei` FROM `gorod_devicelog` where `date` < `d_t`)) as `new_Android`,
					(SELECT COUNT(DISTINCT `imei`) FROM `gorod_devicelog` where `date` = `d_t` AND `date` <= '{$to}' AND `os` = 'iOS' and `imei` not in (SELECT `imei` FROM `gorod_devicelog` where `date` < `d_t`)) as `new_iOS`
				FROM 
					`gorod_devicelog` 
				where 
					`date` >= '{$from}' and `date` <= '{$to}' 
				group by `date`
					");
			$chart = ''; 
			$series = [
				[ 'name' => 'Всего Количество устройст (Android)', 'data'=>[] ],
				[ 'name' => 'Всего Количество устройст (iOS)', 'data'=>[] ],
				[ 'name' => 'Новые (Android)', 'data'=>[] ],
				[ 'name' => 'Новые (iOS)', 'data'=>[] ],
			];
			$xAxis = [];
			$rows = '';
			if(!empty($devices)){
				
				foreach($devices as $i=>$row){
					$sum =  $row['all_Android'] + $row['all_iOS'];
					$rows .= "<tr><td>{$row['d_t']}</td><td>{$sum}</td><td>{$row['all_Android']}</td><td>{$row['all_iOS']}</td><td>{$row['new_Android']}</td><td>{$row['new_iOS']}</td></tr>";
					$xAxis[] = $row['d_t'];
					$series[0]['data'][] = (int)$row['all_Android'];
					$series[1]['data'][] = (int)$row['all_iOS'];
					$series[2]['data'][] = (int)$row['new_Android'];
					$series[3]['data'][] = (int)$row['new_iOS'];
				}
				$chart = AdminPage::prepareJs("
				Highcharts.chart('container', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Устройства с {$from} по {$to}'
					},
					yAxis: {
						title: {
							text: 'Количество'
						}
					},
					xAxis: {
						categories: ".json_encode($xAxis, JSON_UNESCAPED_UNICODE)."
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle'
					},

					series: ".json_encode($series, JSON_UNESCAPED_UNICODE).",

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
							chartOptions: {
								legend: {
									layout: 'horizontal',
									align: 'center',
									verticalAlign: 'bottom'
								}
							}
						}]
					}

				});
				").'
					<div id="container"></div>
				';
				$log = '
						<table class="table-adapt">
						<thead>
							<tr>
								<th class="left-head"><p>Дата</p></th>
								<th data-breakpoints="xs sm"><p>Всего Количество устройст<br>(Android + iOS)</p></th>
								<th data-breakpoints="xs sm"><p>Всего Количество устройст<br>(Android)</p></th>
								<th data-breakpoints="xs sm"><p>Всего Количество устройст<br>(iOS)</p></th>
								<th data-breakpoints="xs"><p>Новые<br>(Android)</p></th>
								<th data-breakpoints="xs"><p>Новые<br>(iOS)</p></th>
							</tr>
						</thead>
						<tbody>	
							'.$rows.'
						</tbody>
						</table>
				';
			}
		
		}
		$content = '
					<form action="'.$this->url.'/stat/devices/" method="GET" class="sectright-filters-form">
						<div class="sectright-filters-form-label">
						'.AdminPage::dateField([ "title"=>"C", "name"=>"from", "class"=>"filter-input", "value"=>$from ], null).'
						'.AdminPage::dateField([ "title"=>"По", "name"=>"to", "class"=>"filter-input", "value"=>$to ], null).'
						</div>
						'.AdminPage::submitField([ "title"=>"Найти", "button-type"=>"success" ], null).'
					</form>
		';
		
		$this->view->data['content'] = $content . $chart . $log;
	}
	
	public function action_login1000($actions=null){
		$this->view->headers['title'] = 'Статистика акционных пополнений | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Статистика"=>$this->url.'/stat/', "Пополнения за вход"=>$this->url.'/stat/login1000/'];
		$this->view->data['header'] = 'Пополнения за вход';
		
		$model_payment = new model_payment();
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		
		if($_GET['from'] and $_GET['to']){
			$from  = $_GET['from'];
			$to  = $_GET['to'];
			$devices = $GLOBALS['DB']['80.93.183.242']->getAll("
			SELECT 
				from_unixtime(`utx_add`, '%Y-%m-%d') as `date`,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE `service_descr`='Пополнение за вход в приложение' AND from_unixtime(t1.`utx_add`, '%Y-%m-%d')=from_unixtime(t2.`utx_add`, '%Y-%m-%d')  ) as c
			FROM `new_feo_ua`.`adv_payment_invoices` t1
			WHERE 
				from_unixtime(t1.`utx_add`, '%Y-%m-%d')>='{$from}' AND from_unixtime(t1.`utx_add`, '%Y-%m-%d')<='{$to}'
				AND `service_descr`='Пополнение за вход в приложение' GROUP BY `date` ORDER by `date` 
					");
			$chart = ''; 
			$series = [
				[ 'name' => 'Количество устройст', 'data'=>[] ],
			];
			$xAxis = [];
			$rows = '';
			if(!empty($devices)){
				
				foreach($devices as $i=>$row){
					$rows .= "<tr><td><a href=\"{$this->url}/stat/login1000/detail/?date={$row['date']}\">{$row['date']}</a></td><td>{$row['c']}</td></tr>";
					$xAxis[] = $row['date'];
					$series[0]['data'][] = (int)$row['c'];
				}
				$chart = AdminPage::prepareJs("
				Highcharts.chart('container', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Пополнение на 1000р. за вход с {$from} по {$to}'
					},
					yAxis: {
						title: {
							text: 'Количество'
						}
					},
					xAxis: {
						categories: ".json_encode($xAxis, JSON_UNESCAPED_UNICODE)."
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle'
					},

					series: ".json_encode($series, JSON_UNESCAPED_UNICODE).",

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
							chartOptions: {
								legend: {
									layout: 'horizontal',
									align: 'center',
									verticalAlign: 'bottom'
								}
							}
						}]
					}

				});
				").'
					<div id="container"></div>
				';
				$log = '
						<table class="table-adapt">
						<thead>
							<tr>
								<th class="left-head"><p>Дата</p></th>
								<th data-breakpoints="xs sm"><p>Количество устройст (1000р.)</p></th>
							</tr>
						</thead>
						<tbody>	
							'.$rows.'
						</tbody>
						</table>
				';
			}
		
		}
		$content = '
					<form action="'.$this->url.'/stat/login1000/" method="GET" class="sectright-filters-form">
						<div class="sectright-filters-form-label">
						'.AdminPage::dateField([ "title"=>"C", "name"=>"from", "class"=>"filter-input", "value"=>$from ], null).'
						'.AdminPage::dateField([ "title"=>"По", "name"=>"to", "class"=>"filter-input", "value"=>$to ], null).'
						</div>
						'.AdminPage::submitField([ "title"=>"Найти", "button-type"=>"success" ], null).'
					</form>
		';
		
		$this->view->data['content'] = $content . $chart . $log;
	}
	
	public function action_login1000_detail($actions=null){
		$this->view->headers['title'] = 'Статистика акционных пополнений | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Статистика"=>$this->url.'/stat/', "Пополнения за вход"=>$this->url.'/stat/login1000/'];
		$this->view->data['header'] = 'Пополнения за вход';
		
		$model_payment = new model_payment();
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		
		if($_GET['date']){
			$from  = $_GET['date'];
			$to  = $_GET['date'];
			$devices = $GLOBALS['DB']['80.93.183.242']->getAll("
			SELECT 
				*,
				(SELECT name FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `user_name`,
				(SELECT i_fam FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `i_fam`,
				(SELECT i_name FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `i_name`,
				(SELECT email FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `email`,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE from_unixtime(t2.`utx_add`, '%Y-%m-%d')='{$from}'AND (`service_descr` LIKE '%Пополнение за вход в приложение%') AND t2.uid=t1.uid) as c_oper,
				(SELECT SUM(price) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE from_unixtime(t2.`utx_add`, '%Y-%m-%d')='{$from}'AND (`service_descr` LIKE '%Пополнение за вход в приложение%') AND t2.uid=t1.uid) as c_summ
			FROM `new_feo_ua`.`adv_payment_invoices` t1
			WHERE 
				from_unixtime(t1.`utx_add`, '%Y-%m-%d')='{$from}'
				AND `service_descr`='Пополнение за вход в приложение' ORDER by `utx_add` 
					");
			$chart = ''; 
			$series = [
				[ 'name' => 'Количество устройст', 'data'=>[] ],
			];
			$xAxis = [];
			$rows = '';
			if(!empty($devices)){
				
				foreach($devices as $i=>$row){
					$n = $i + 1 ;
					$rows .= "<tr><td>{$n}</td><td>{$row['id']}</td><td>{$row['service_descr']}</td><td>{$row['user_name']} ({$row['i_fam']} {$row['i_name']} {$row['email']})</td><td>{$row['c_oper']}</td><td>{$row['c_summ']}</td></tr>";
				}
				$log = '
						<table class="table-adapt">
						<thead>
							<tr>
								<th><p>№</p></th>
								<th><p>id</p></th>
								<th><p>Описание</p></th>
								<th><p>Пользователь</p></th>
								<th><p>Операций</p></th>
								<th data-breakpoints="xs sm"><p>Сумма</p></th>
							</tr>
						</thead>
						<tbody>	
							'.$rows.'
						</tbody>
						</table>
				';
			}
		
		}
		$content = '
					<form action="'.$this->url.'/stat/login1000/" method="GET" class="sectright-filters-form">
						<div class="sectright-filters-form-label">
						'.AdminPage::dateField([ "title"=>"C", "name"=>"from", "class"=>"filter-input", "value"=>$from ], null).'
						'.AdminPage::dateField([ "title"=>"По", "name"=>"to", "class"=>"filter-input", "value"=>$to ], null).'
						</div>
						'.AdminPage::submitField([ "title"=>"Найти", "button-type"=>"success" ], null).'
					</form>
		';
		
		$this->view->data['content'] = $content . $chart . $log;
	}
	
	public function action_invite($actions=null){
		$this->view->headers['title'] = 'Статистика пополнений по приглашениям | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Статистика"=>$this->url.'/stat/', "Пополнения за вход"=>$this->url.'/stat/invite/'];
		$this->view->data['header'] = 'Пополнений по приглашениям';
		
		$model_payment = new model_payment();
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		
		if($_GET['from'] and $_GET['to']){
			$from  = $_GET['from'];
			$to  = $_GET['to'];
			$devices = $GLOBALS['DB']['80.93.183.242']->getAll("
			SELECT 
				from_unixtime(`utx_add`, '%Y-%m-%d') as `date`,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE `service_descr` LIKE '%Скачивание по приглашению%' AND from_unixtime(t1.`utx_add`, '%Y-%m-%d')=from_unixtime(t2.`utx_add`, '%Y-%m-%d')  ) as c1,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE `service_descr` LIKE '%Активирован код приглашения%' AND from_unixtime(t1.`utx_add`, '%Y-%m-%d')=from_unixtime(t2.`utx_add`, '%Y-%m-%d')  ) as c2
			FROM `new_feo_ua`.`adv_payment_invoices` t1
			WHERE 
				from_unixtime(t1.`utx_add`, '%Y-%m-%d')>='{$from}' AND from_unixtime(t1.`utx_add`, '%Y-%m-%d')<='{$to}'
				AND (`service_descr` LIKE '%Скачивание по приглашению%' OR `service_descr` LIKE '%Активирован код приглашения%') GROUP BY `date` ORDER by `date` 
					");
			$chart = ''; 
			$series = [
				[ 'name' => 'Скачивание по приглашению', 'data'=>[] ],
				[ 'name' => 'Активирован код приглашения', 'data'=>[] ],
			];
			$xAxis = [];
			$rows = '';
			if(!empty($devices)){
				
				foreach($devices as $i=>$row){
					$rows .= "<tr><td><a href=\"{$this->url}/stat/invite/detail/?date={$row['date']}\">{$row['date']}</a></td><td>{$row['c1']}</td><td>{$row['c2']}</td></tr>";
					$xAxis[] = $row['date'];
					$series[0]['data'][] = (int)$row['c1'];
					$series[1]['data'][] = (int)$row['c2'];
				}
				$chart = AdminPage::prepareJs("
				Highcharts.chart('container', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Пополнение на 1000р. за вход с {$from} по {$to}'
					},
					yAxis: {
						title: {
							text: 'Количество'
						}
					},
					xAxis: {
						categories: ".json_encode($xAxis, JSON_UNESCAPED_UNICODE)."
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle'
					},

					series: ".json_encode($series, JSON_UNESCAPED_UNICODE).",

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
							chartOptions: {
								legend: {
									layout: 'horizontal',
									align: 'center',
									verticalAlign: 'bottom'
								}
							}
						}]
					}

				});
				").'
					<div id="container"></div>
				';
				$log = '
						<table class="table-adapt">
						<thead>
							<tr>
								<th class="left-head"><p>Дата</p></th>
								<th data-breakpoints="xs sm"><p>Скачивание по приглашению (10р.)</p></th>
								<th data-breakpoints="xs sm"><p>Активирован код приглашения (500р.)</p></th>
							</tr>
						</thead>
						<tbody>	
							'.$rows.'
						</tbody>
						</table>
				';
			}
		
		}
		$content = '
					<form action="'.$this->url.'/stat/invite/" method="GET" class="sectright-filters-form">
						<div class="sectright-filters-form-label">
						'.AdminPage::dateField([ "title"=>"C", "name"=>"from", "class"=>"filter-input", "value"=>$from ], null).'
						'.AdminPage::dateField([ "title"=>"По", "name"=>"to", "class"=>"filter-input", "value"=>$to ], null).'
						</div>
						'.AdminPage::submitField([ "title"=>"Найти", "button-type"=>"success" ], null).'
					</form>
		';
		
		$this->view->data['content'] = $content . $chart . $log;
	}
	
	public function action_invite_detail($actions=null){
		$this->view->headers['title'] = 'Статистика пополнений по приглашениям | Администрирование Город 24';
		$this->view->data['breadcrumbs'] = [ "Приложение"=>$this->url, "Статистика"=>$this->url.'/stat/', "Пополнения за вход"=>$this->url.'/stat/invite/'];
		$this->view->data['header'] = 'Пополнений по приглашениям';
		
		$model_payment = new model_payment();
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		
		if($_GET['date']){
			$from  = $_GET['date'];
			$to  = $_GET['date'];
			$devices = $GLOBALS['DB']['80.93.183.242']->getAll("
			SELECT 
				*,
				(SELECT name FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `user_name`,
				(SELECT i_fam FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `i_fam`,
				(SELECT i_name FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `i_name`,
				(SELECT email FROM `new_feo_ua`.`accounts` WHERE `accounts`.`id` = `t1`.`uid`) as `email`,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE from_unixtime(t2.`utx_add`, '%Y-%m-%d')='{$from}'AND (`service_descr` LIKE '%Скачивание по приглашению%') AND t2.uid=t1.uid) as c_oper_10,
				(SELECT COUNT(*) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE from_unixtime(t2.`utx_add`, '%Y-%m-%d')='{$from}'AND (`service_descr` LIKE '%Активирован код приглашения%') AND t2.uid=t1.uid) as c_oper_500,
				(SELECT SUM(price) FROM `new_feo_ua`.`adv_payment_invoices` t2 WHERE from_unixtime(t2.`utx_add`, '%Y-%m-%d')='{$from}'AND (`service_descr` LIKE '%Скачивание по приглашению%' OR `service_descr` LIKE '%Активирован код приглашения%') AND t2.uid=t1.uid) as c_summ
			FROM `new_feo_ua`.`adv_payment_invoices` t1
			WHERE 
				from_unixtime(t1.`utx_add`, '%Y-%m-%d')='{$from}'
				AND (`service_descr` LIKE '%Скачивание по приглашению%' OR `service_descr` LIKE '%Активирован код приглашения%') 
			GROUP BY `uid`
			ORDER by `utx_add`
			");

			$rows = '';
			if(!empty($devices)){
				
				foreach($devices as $i=>$row){
					$rows .= "<tr><td>{$row['id']}</td><td>{$row['user_name']} ({$row['i_fam']} {$row['i_name']} {$row['email']})</td><td>{$row['c_oper_10']}</td><td>{$row['c_oper_500']}</td><td>{$row['c_summ']}</td></tr>";
				}
				$log = '
						<table class="table-adapt">
						<thead>
							<tr>
								<th><p>id</p></th>
								<th><p>Пользователь</p></th>
								<th data-breakpoints="xs sm"><p>Пополнений (Скачивание по приглашению 10р.)</p></th>
								<th data-breakpoints="xs sm"><p>Пополнений (Активирован код приглашения 500р.)</p></th>
								<th data-breakpoints="xs sm"><p>Сумма</p></th>
							</tr>
						</thead>
						<tbody>	
							'.$rows.'
						</tbody>
						</table>
				';
			}
		
		}
		$content = '
					<form action="'.$this->url.'/stat/invite/" method="GET" class="sectright-filters-form">
						<div class="sectright-filters-form-label">
						'.AdminPage::dateField([ "title"=>"C", "name"=>"from", "class"=>"filter-input", "value"=>$from ], null).'
						'.AdminPage::dateField([ "title"=>"По", "name"=>"to", "class"=>"filter-input", "value"=>$to ], null).'
						</div>
						'.AdminPage::submitField([ "title"=>"Найти", "button-type"=>"success" ], null).'
					</form>
		';
		
		$this->view->data['content'] = $content . $chart . $log;
	}
	
}