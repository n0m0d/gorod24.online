<?php
class controller_support extends Controller
{
	function __construct(){
		$this->view = new View('help.tpl');
		$this->view->setHeaderView('header-help.tpl');
		$this->view->setFooterView('footer-help.tpl');
		$this->view->headers['title'] = 'Помощь | Город 24 онлайн';
		
		$this->model_help_categories = new model_help_categories();
		$this->model_help = new model_help();
		
		
	}
	
	public function action_index($array = array()){
		$this->view->data['main']['menu'] = [];
		
		$categories = $this->model_help_categories->getItemsWhere("`status`=1 AND `site`=1");
		foreach($categories as $cat){
			$pages = $this->model_help->getItemsWhere("`status`=1 AND `cat_id`='{$cat['id']}' AND `site`=1");
			$items = [];
			foreach($pages as $p){
				$items[$p['id']] = ["title" => $p['title'], "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/support/{$cat['id']}/{$p['id']}/"];
			}
			$this->view->data['main']['menu'][$cat['id']] = [ "title" => $cat['title'], "url"=>"#", "items" => $items];
		}
		$this->view->data['header'] = "Помощь";
		$cat_id = $array[0];
		$page_id = $array[1];
		if($cat_id and $this->view->data['main']['menu'][$cat_id]){ $this->view->data['main']['menu'][$cat_id]['selected']=true; }
		if($page_id){
			$page = $this->model_help->getItem($page_id);
			
			$this->view->data['header'] = $page['title'];
			$this->view->data['content'] = $page['text'];
		}
		
	}
	
	public function action_admin($array = array()){
		$this->view->data['main']['menu'] = [];
		
		$categories = $this->model_help_categories->getItemsWhere("`status`=1 AND `admin`=1");
		foreach($categories as $cat){
			$pages = $this->model_help->getItemsWhere("`status`=1 AND `cat_id`='{$cat['id']}' AND `admin`=1");
			$items = [];
			foreach($pages as $p){
				$items[$p['id']] = ["title" => $p['title'], "url"=>$GLOBALS['CONFIG']['HTTP_HOST']."/support/admin/{$cat['id']}/{$p['id']}/"];
			}
			$this->view->data['main']['menu'][$cat['id']] = [ "title" => $cat['title'], "url"=>"#", "items" => $items];
		}
		
		$this->view->data['header'] = "Помощь";
		$cat_id = $array[0];
		$page_id = $array[1];
		if($cat_id and $this->view->data['main']['menu'][$cat_id]){ $this->view->data['main']['menu'][$cat_id]['selected']=true; }
		if($page_id){
			$page = $this->model_help->getItem($page_id);
			
			$this->view->data['header'] = $page['title'];
			$this->view->data['content'] = $page['text'];
		}
		
	}
	
	
}