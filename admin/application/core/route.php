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
			header('HTTP/1.0 401 Unauthorized');
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
		/*
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
		}*/
		if(self::checkControllerFolder(ADMINDIR."/application/controllers", $routes)){
			//Поиск контроллера начиная с корнеаого каталога, если контроллер ненайден, то вернет false и продолжит выполнение скрипта.
		}		else{
			Route::ErrorPage404();
		}
	}
	
	
	private function checkControllerFolder($folder, $routes=array())	{
		$new_routes = $routes;
		if(!empty($routes)){
		foreach($routes as $i=>$route){
			if(is_dir("{$folder}/{$route}")){
				array_shift($new_routes);
				return self::checkControllerFolder("{$folder}/{$route}", $new_routes);
			}
			else {
				return self::runControllerFolder("{$folder}", $new_routes);
			}
		}}
		else {
			return self::runControllerFolder("{$folder}", $new_routes);
		}
		return true;
	}
	
	private function runControllerFolder($folder, $routes=array())	{
		if($folder!=ADMINDIR."/application/controllers"){
		$actions=[];
		if(!empty($routes)){
			foreach ($routes as $i => $route){
				if($i == 0) { $controller = $route; 	Registry::set('controller', $controller);}
				else { $actions[]=$route;}
			}
		} else { $controller="index"; }
		$default_controller_name = 'controller_index';
		$controller_name = 'controller_'.str_replace(array('-', '.'), '_',$controller);
		// Подцепляем файл с классом контроллера
		$default_controller_path = "{$folder}/".strtolower($default_controller_name).'.php'; 
		$controller_path = "{$folder}/".strtolower($controller_name).'.php'; 
		if(file_exists($controller_path)){
			require_once $controller_path;
			if (class_exists($controller_name)){
				$controller_obj = new $controller_name();
				$this->runAction($controller_obj, $actions);
				return true;
			}	else { Route::ErrorPage404(); return false; }
		}
		elseif(file_exists($default_controller_path)){
			$controller_path = $default_controller_path;
			$controller_name = $default_controller_name;
			require_once $controller_path;
			if (class_exists($controller_name)){
				$controller_obj = new $controller_name();
				$this->runAction($controller_obj, $routes);
				return true;
			}	else { Route::ErrorPage404();  return false; } 
		}
		else {
			Route::ErrorPage404(); 
			return false;
		}
		}
		else {
			$REQUEST_URI = Registry::get('REQUEST_URI');
			if(empty($routes)){
				$default_controller_name = 'controller_index';
				$default_controller_path = "{$folder}/".strtolower($default_controller_name).'.php'; 
				require_once $default_controller_path;
				if (class_exists($default_controller_name)){
					$controller_obj = new $default_controller_name();
					$this->runAction($controller_obj, array());
					return true;
				}	else { Route::ErrorPage404();  return false; } 
			} 
			else { 
				$actions=[];
				if(!empty($routes)){
					foreach ($routes as $i => $route){
						if($i == 0) { $controller = $route; 	Registry::set('controller', $controller);}
						else { $actions[]=$route;}
					}
				} else { $controller="index"; }
				$controller_name = 'controller_'.str_replace(array('-', '.'), '_',$controller);
				$controller_path = "{$folder}/".strtolower($controller_name).'.php'; 
				if(file_exists($controller_path)){
					require_once $controller_path;
					if (class_exists($controller_name)){
						$controller_obj = new $controller_name();
						$this->runAction($controller_obj, $actions);
						return true;
					}	else { Route::ErrorPage404(); return false; }
				}
			}
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
