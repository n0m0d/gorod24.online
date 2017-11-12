<?php
class controller_index extends Controller
{
	function __construct(){
		$this->view = new View('index.tpl');
		$this->view->headers['title'] = 'Главная | Полезное радио';
		$this->view->data['menu']['index'] = 'active';
	}
	
	public function action_index($array = array()){
		$this->view->data['file'] = __FILE__;
	}
	
	public function renderPage($post){
		$this->view = new View('page.tpl');
		$meta = json_decode($post['post_meta'], true);
		$this->view->headers['title'] = $meta['title'];
		$this->view->headers['description'] = $meta['description'];
		$this->view->headers['keywords'] = $meta['keywords'];
		$this->view->headers['image'] = $meta['image'];
		$this->view->data['content'] = do_shortcode(apply_filters('the_content',$post['post_content']));

	}
	
}