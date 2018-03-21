<?php
class controller_menu extends Controller
{
	function __construct(){
		$this->model_menu = new model_menu();
		$this->model_menu_items = $this->model_menu->model_menu_items();
		//$this->view->data['foot_menu'] = $this->model_menu_items->getItemsWhere("menu_id = 2", "position");
	}

	public function action_index(){

	}

	public function header_menu_rend($attr){
		$header_menu = $this->model_menu_items->getItemsWhere("menu_id = 1 AND parent_id = 0", "position");
		$menu_html = '';
		$mob_menu_html = '';
		foreach ($header_menu AS $menu)
		{
			$sub_menu = $this->model_menu_items->getItemsWhere("menu_id = 1 AND parent_id = ".$menu['id'], "position");
			$sub_html = '';
			$mob_sub_html = '';

			foreach ($sub_menu AS $s_menu)
			{
				$sub_html .= '
					<li class="ajax-anchor-in '.$s_menu['class'].'"><a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/'.$s_menu['link'].'">'.$s_menu['name'].'</a></li>
				';

				$mob_sub_html .= '
					<li><a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/'.$s_menu['link'].'">'.$s_menu['name'].'</a><em><i></i></em></li>
				';
			}

			$menu_html .= '
				<li class="ajax-anchor '.$menu['class'].' '.(strpos(Registry::get('REQUEST_URI'), $menu['link'])?"active":"").'">
                	<a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/'.$menu['link'].'">'.$menu['name'].'</a>
                	<ul>
                		'.$sub_html.'
					</ul>
                </li>
			';

			if ($mob_sub_html)
			{
				$mob_menu_html .= '
					<li class="has-drop-mobile">
	                    <a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/'.$menu['link'].'">'.$menu['name'].'</a>
	                    <ul>
	                        '.$mob_sub_html.'
	                    </ul>
	                </li>
				';
			}
			else
			{
				$mob_menu_html .= '
					<li>
	                    <a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/'.$menu['link'].'">'.$menu['name'].'</a>
	                </li>
				';
			}
		}

		if ($attr == 'mobile')
		{
			return $mob_menu_html;
		}
		else
		{
			return $menu_html;
		}
	}
}