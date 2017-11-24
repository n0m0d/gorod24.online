<?php
/*
Класс-маршрутизатор для определения запрашиваемой страницы.
> цепляет классы контроллеров и моделей;
> создает экземпляры контролеров страниц и вызывает действия этих контроллеров.
*/
class Route
{
	function __construct()	{
		do_action('route_construct');
	}
	
	function __destruct(){

	}
	
	public function start()	{
		do_action('before_route');
		// Контроллер и действие по умолчанию
		$controller = 'index'; 		Registry::set('controller', $controller);
		$action = 'index'; 			Registry::set('action', $action);
		$REQUEST_URI = $_SERVER['REQUEST_URI'];
		$REQUEST_URI = (mb_substr_count( $_SERVER['REQUEST_URI'], '?') > 0) ? substr($REQUEST_URI, 0, strpos($REQUEST_URI,'?')) : $REQUEST_URI;
		if(substr($REQUEST_URI,-1)=='/')$REQUEST_URI=substr($REQUEST_URI, 0,-1);
		$REQUEST_URI = apply_filters('the_uri',$REQUEST_URI);
		if( mb_substr_count( $_SERVER['REQUEST_URI'], '?') == 1 ){
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
		
		$routes = explode('/', urldecode($REQUEST_URI));
		$routes = array_filter($routes,function($el){ return !empty($el);});
		$newroutes=array();
		foreach ($routes as $i => $route){$newroutes[]=$route;}
		$routes = $newroutes; $actions=[];
		foreach ($routes as $i => $route){
			if($i == 0) { $controller = $route; 	Registry::set('controller', $controller);}
			else { $actions[]=$route;}
		}
		$controller_name = 'controller_'.str_replace(array('-', '.'), '_',$controller);
				
		// Подцепляем файл с классом контроллера
		$controller_path = APPDIR."/application/controllers/".strtolower($controller_name).'.php'; 
		// Если файл контроллера существует, значит его подключаем и определяем. что страница является статической
		if(file_exists($controller_path)){
			require_once $controller_path;
			if (class_exists($controller_name)){
				$controller_obj = new $controller_name();
				$this->runAction($controller_obj, $actions);
			}	else Route::ErrorPage404(); 
		}
		elseif(is_registered_controller($controller)) {
			render_controller($controller, $routes); 
		}		
		elseif(is_registered_page(urldecode($REQUEST_URI))) {
			render_page(urldecode($REQUEST_URI)); 
		}		
		// Если файл контроллера отсутствует подключаем файл контроллера динамических страниц, который отвечает за поиск диначеской страници в БД
		else{
			if(!empty($REQUEST_URI)){
			$model_posts = new model_posts();
			$result = $model_posts->getItemWhere("`post_url`='{$REQUEST_URI}'");
			if(!empty($result)){
				$controller_index = new controller_index();
				$controller_index->renderPage($result);
			}
			else Route::ErrorPage404(); 
			} else Route::ErrorPage404(); 
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
	
	public static function ErrorPage404()	{
		header('HTTP/1.1 404 Not Found');
		header('Status: 404 Not Found');
		include 'application/controllers/controller_404.php';
		$controller_obj = new controller_404();
		$controller_obj->action_index();
    }
	
	public function IPGeo(){
		$ip = getIp(); // определение IP-адресса клиента
		// Подключение класса для определения города по IP
		$gb = new IPGeoBase();
		$IPGeo = $gb->getRecord($ip);
		
		// Записываем результат в глобальный реестр приложения
		Registry::set("IPGeo", $IPGeo);
	}
	
	public function session_run(){
		//session_save_path(__DIR__ . '/cache/sessions');		
		if ($_COOKIE['session_id']) session_id($_COOKIE['session_id']);
		session_start();
		$id_session = session_id();
		setcookie('session_id', $id_session, 0, '/');
		
			$session_time_left = $_SESSION['session_time'] - $_SESSION['session_start_time'];
			Registry::set("session_time_left", $session_time_left);
		if($_SESSION['session_time'] == time()) {
			$_SESSION['session_conn_count'] = $_SESSION['session_conn_count'] + 1; 
			if ($_SESSION['session_conn_count'] > 10){
				echo  "Колличество запросов в секунду с вашего компьютера превысило максимум. 
				Просьба не превышать колличество обновлений страници в секунду. 
				С вашего компьютера было произведено ".$_SESSION['session_conn_count']." запросов.";
				exit;
			}
		} else { $_SESSION['session_conn_count'] = 0; }
		if(!$_SESSION['session_start_time']) 	$_SESSION['session_start_time'] = time(); 	//Время начала сессии
												$_SESSION['session_time'] = time();			//Текуущее время сессии
	}
	
}
