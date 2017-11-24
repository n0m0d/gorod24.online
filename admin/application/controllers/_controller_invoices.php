<?php

class controller_invoices extends Controller
{

	function action_index($array = array())
	{
		if( $this->getPermissions('invoices') ){
			if(isset($this->GET['id'])) {
				$id = ($this->GET['id'] == 'new')? 'new' : (int)$this->GET['id'];
				$invoice = ($id == 'new')? 'new' : $this->model->get_invoice_by_id($id);
				
				if(is_array($invoice)){
					$users=$this->model->get_users();
					$bas_items=$this->model->get_basckets_items($invoice['bas_id']);
					$products=$this->model->db->GetAll("SELECT prod_id as value, prod_name as name, prod_cost as cost, prod_discount as discount  FROM mvc_products WHERE prod_status=1");
					$allproducts=$this->model->db->GetAll("SELECT prod_id as value, prod_name as name, prod_cost as cost, prod_discount as discount FROM mvc_products WHERE prod_status=1 or prod_id IN (SELECT item_prod_id FROM mvc_basckets_items, mvc_basckets WHERE mvc_basckets_items.item_bas_id=mvc_basckets.bas_id AND mvc_basckets.bas_inv_id=?i)", $this->GET['id']);
					
					$this->registry->set('title', 'Панель администрирования | счет #'.$invoice['inv_id'].'');
					if($this->GET['print']==1){
						$tmp = false;
						$pg = ADMINDIR.'/application/views/invoice_print.php';
					}
					else {
					$this->view->template = 'admin_view.php';
					$tmp = ADMINDIR.'/application/views/'.$this->view->template;
					$pg = ADMINDIR.'/application/views/invoice_view.php';
					}
					$this->view->generate(
											$pg, 
											$tmp, 
											array(	
													'invoice'=>$invoice, 
													'users'=>$users, 
													'bas_items'=>$bas_items, 
													'products'=>$products, 
													'allproducts'=>$allproducts, 
												)
										);
				}
				elseif($this->GET['id'] == 'new'){
					$users=$this->model->get_users();
					$products=$this->model->db->GetAll("SELECT prod_id as value, prod_name as name, prod_cost as cost, prod_discount as discount FROM mvc_products WHERE prod_status=1");
					$this->registry->set('title', 'Панель администрирования | Создать счет');
					$qroups = $this->model->get_groups();
					$this->view->template = 'admin_view.php';
					$this->view->generate(
											ADMINDIR.'/application/views/invoice_view.php', 
											ADMINDIR.'/application/views/'.$this->view->template, 
											array(	
													'invoice'=>$invoice, 
													'users'=>$users, 
													'bas_items'=>$bas_items, 
													'products'=>$products, 
													'allproducts'=>$products, 
												)
										);
				}				
				else { Route::ErrorPage404($this->registry);}

			} 
			else {
				$this->registry->set('title', 'Панель администрирования | Счета');
				$this->view->template = 'admin_view.php';
				$delete = ( $this->getPermissions('invoices-delete') )?1:0;
				$limit = (int)((isset($_GET['limit']))?$_GET['limit']:20);
				$invoices = $this->model->get_invoices(array(
														'limit' => $limit,
														'page' => $_GET['page'],
														'search' => $_GET['search'],
														'order' => 'inv_id desc',
														));
				$this->view->generate(ADMINDIR.'/application/views/invoices_view.php', ADMINDIR.'/application/views/'.$this->view->template, array('invoices'=>$invoices,'delete' => $delete));
			}
		}
		else {
				$this->generateLoginForm();
		}
	}
}
?>