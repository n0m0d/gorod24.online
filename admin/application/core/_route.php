<?php
/*
Класс-маршрутизатор для определения запрашиваемой страницы.
> цепляет классы контроллеров и моделей;
> создает экземпляры контролеров страниц и вызывает действия этих контроллеров.
*/
class Route
{
	
	function __construct(){
		$this->session_run();
		
		if(isset($_POST['login']) and isset($_POST['password'])){
			$model_users = new model_users();
			$user = $model_users->login($_POST['login'], $_POST['password']);
			if($user){
				header('Location: '.$_SERVER['REQUEST_URI']);
			}
		}
		$model_access = new model_access();
		$adminAccess = $model_access->getPermission('admin');
		if(empty($_SESSION['user_id']) or !$adminAccess){
			$view = new View('login.tpl');
			$view->setTemplatesFolder(ADMINDIR.'/application/views/');
			$view->headers['title'] = 'Авторизация | Администрирование город 24';
			$view->renderBody();
			exit;
		}
		
		
	}

	
	public function start(){
		// Контроллер и действие по умолчанию
		$controller = 'index';
		$action = 'index';
		
		$REQUEST_URI = $_SERVER['REQUEST_URI'];
		$REQUEST_URI = (mb_substr_count( $_SERVER['REQUEST_URI'], '?') > 0) ? substr($REQUEST_URI, 0, strpos($REQUEST_URI,'?')) : $REQUEST_URI;
		$REQUEST_URI = apply_filters('the_uri',$REQUEST_URI);
		if( mb_substr_count( $_SERVER['REQUEST_URI'], '?') == 1 )
		{
			$get = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'],'?')+1); 
			$ampCount = mb_substr_count( $get, '&');
			if($ampCount>0) { $get = explode('&', $get); } else { $get = array($get);}
			
			foreach($get as $str) {
				  list($key, $value) = explode('=', $str);
				  $key=htmlspecialchars(strip_tags($key));
				  $value=htmlspecialchars(strip_tags($value));
				  $_GET[$key] = urldecode($value);
			   }
		}
		
		$host = apply_filters('the_host', 'http://'.$_SERVER["HTTP_HOST"]);
		Registry::set('HTTP_HOST', $host);
		Registry::set('REQUEST_URI', $REQUEST_URI);
		
		$_routes = explode('/', $REQUEST_URI);
		$routes = [];
		foreach($_routes as $route){
			if($route != 'admin' and $route!='')$routes[] = $route;
		}
		
		// Получаем имя контроллера
		if ( !empty($routes[0]) ){	
			$controller = $routes[0];
		}
		$actions=[];
		foreach ($routes as $i => $route){
			if($i == 0) { $controller = $route; 	Registry::set('controller', $controller);}
			else { $actions[]=$route;}
		}
		// Добавляем префиксы
		$controller_name = 'controller_'.str_replace(array('-', '.'), '_',$controller);
		// Подцепляем файл с классом контроллера
		$controller_path = ADMINDIR."/application/controllers/".strtolower($controller_name).'.php'; 
		// Если файл контроллера существует, значит его подключаем и определяем. что страница является статической
		if(file_exists($controller_path)){
			require_once $controller_path;
			if (class_exists($controller_name)){
				$controller_obj = new $controller_name();
				$this->runAction($controller_obj, $actions);
			}	else Route::ErrorPage404(); 
		}
		else{
			Route::ErrorPage404();
		}
	}
	
	private function runAction($controller, $actions, $params=array()){
		if(!empty($actions)){
			$action_name = 'action_'.str_replace(array('-', '.'), '_',implode('_', $actions));
			if(method_exists($controller, $action_name)){
				call_user_func_array(array($controller, $action_name), array($params));
			} else {
				$param = array_pop($actions);
				array_unshift($params, $param);
				$this->runAction($controller, $actions, $params);
			}
		}
		elseif(method_exists($controller, 'action_index')) {
			call_user_func_array(array($controller, 'action_index'), array($params));
		}
		else Route::ErrorPage404();
	}
	
	public static function ErrorPage404()
	{
		header('HTTP/1.1 404 Not Found');
		header('Status: 404 Not Found');
		include APPDIR.'/application/controllers/controller_404.php';
		$controller = new controller_404();
		$controller->action_index();
    }
	
	public function session_run(){
		if ($_COOKIE['session_id']) session_id($_COOKIE['session_id']);
		session_start();
		setcookie('session_id', session_id(), 0, '/');
			$session_time_left = $_SESSION['session_time'] - $_SESSION['session_start_time'];
		if($_SESSION['session_time'] == time()) {
			$_SESSION['session_conn_count'] = $_SESSION['session_conn_count'] + 1; 
		} else { $_SESSION['session_conn_count'] = 0; }
		if(!$_SESSION['session_start_time']) 	$_SESSION['session_start_time'] = time(); 	//Время начала сессии
												$_SESSION['session_time'] = time();			//Текуущее время сессии
	}
	
	
}
