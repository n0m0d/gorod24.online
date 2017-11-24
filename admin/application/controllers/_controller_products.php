<?php

class controller_products extends Controller
{

	function action_index($array = array())
	{
		if( $this->getPermissions('products') ){
			if(isset($this->GET['id'])) {
				$id = ($this->GET['id'] == 'new')? 'new' : (int)$this->GET['id'];
				$product = ($id == 'new')? 'new' : $this->model->get_product_by_id($id);
				
				if(is_array($product)){
					
				$qroups = $this->model->get_groups();
					
					$this->registry->set('title', 'Панель администрирования | т/у "'.apply_filters('name-ru', $product['prod_name']).'"');
					$this->view->template = 'admin_view.php';
					$this->view->generate(
											ADMINDIR.'/application/views/product_view.php', 
											ADMINDIR.'/application/views/'.$this->view->template, 
											array(	
													'product'=>$product, 
													'qroups'=>$qroups, 
												)
										);
				}
				elseif($this->GET['id'] == 'new'){
					$this->registry->set('title', 'Панель администрирования | Создать т/у');
					$qroups = $this->model->get_groups();
					$this->view->template = 'admin_view.php';
					$this->view->generate(
											ADMINDIR.'/application/views/product_view.php', 
											ADMINDIR.'/application/views/'.$this->view->template, 
											array(	
													'product'=>$product, 
													'qroups'=>$qroups, 
												)
										);
				}				
				else { Route::ErrorPage404($this->registry);}

			} 
			else {
				$this->registry->set('title', 'Панель администрирования | Товары и услуги');
				$this->view->template = 'admin_view.php';
				$products = $this->model->get_products(array(
														'limit' => 20,
														'page' => $_GET['page'],
														'search' => $_GET['search'],
														));
				$this->view->generate(ADMINDIR.'/application/views/products_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('products'=>$products));
			}
		}
		else {
				$this->generateLoginForm();
		}
	}
}
?>