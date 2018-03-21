<?php
spl_autoload_register(function ($class) {
	if(substr($class,0,10)=='controller'){
	if(file_exists(APPDIR .'/application/controllers/' . $class . '.php')){
		include  APPDIR .'/application/controllers/' . $class . '.php';
	}
	else {
		Route::ErrorPage404(); 
		exit;
	}
	}
});

class Controller {
	
	public $view = null;
	public $cache = false;
	public $cache_dir;
	public $cache_expire;
	public $cache_file_name;
	
	function __construct()	{
		$this->POST = self::varChek($_POST);
		$this->GET = self::varChek($_GET);
	}
	
	function __destruct(){
		if(is_object($this->view) and $this->view->isRender())
			$this->view->render();
		unset($this->view);
	}
	
	public function get_cache(){
		$this->cache_file_name =  $this->cache_dir . $this->called_class . '_'.md5($_SERVER['REQUEST_URI']).'.txt';
		if($this->cache and $this->POST == array() and is_readable($this->cache_file_name) and ((time() - $this->cache_expire) < filemtime($this->cache_file_name))){
			$this->cache = false;
			echo file_get_contents($this->cache_file_name); exit;
		}
	}
	
	public function set_cache($content){
		$this->cache_file_name =  $this->cache_dir . $this->called_class . '_'.md5($_SERVER['REQUEST_URI']).'.txt';
		if($this->cache and $this->POST == array() and ((time() - $this->cache_expire) > filemtime($this->cache_file_name))){
			file_put_contents($this->cache_file_name, $content);
		}
	}
	
	public function get_chunk_cache($file){
		$this->cache_file_name =  $this->cache_dir . $this->called_class . '_'.$file.'_'.md5($_SERVER['REQUEST_URI']).'.txt';
		if($this->cache and $this->POST == array() and is_readable($this->cache_file_name) and ((time() - $this->cache_expire) < filemtime($this->cache_file_name))){
			return file_get_contents($this->cache_file_name);
		} else return false;
	}
	
	public function set_chunk_cache($file ,$content){
		$this->cache_file_name =  $this->cache_dir . $this->called_class . '_'.$file.'_'.md5($_SERVER['REQUEST_URI']).'.txt';
		if($this->cache and $this->POST == array()){
			file_put_contents($this->cache_file_name, $content);
		}
		return $content;
	}
		
	public static function varChek($variable, $adminmode = false){
		if(is_array($variable)){$result = array(); foreach ($variable as $key => $value){
			$result[$key] = self::varChek($value);
		}}
		else{
			$result = ($adminmode==false) ? trim(addslashes(htmlspecialchars(strip_tags(urldecode($variable))))) : trim(addslashes(urldecode($variable)));
		}
		return $result;
	}

}
