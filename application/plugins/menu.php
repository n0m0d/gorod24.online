<?php
/*

Plugin Name: yashare
Plugin URI: yashare
Description: Плагин сайдбар-рекламы
Version: 1.0
Author: Шумилов Демьян
Author URI:

*/

if(!class_exists('menu', false))
{
	class menu
	{
		function __construct()
		{
			add_shortcode('menu', array($this, 'menu_init'));
		}

		public function menu_init($atts, $content = null)
		{
			extract(shortcode_atts(array(
				'url' => '',
				'type' => '',
				'project' => '',
				'menu_id' => '1',
			), $atts));

			$this->model_menu = new model_menu();
			$this->model_menu_items = $this->model_menu->model_menu_items();

			$header_menu = $this->model_menu_items->getItemsWhere("menu_id = '{$menu_id}' AND parent_id = 0 AND status = 1", "position");
			$menu_html = '';
			$mob_menu_html = '';
			foreach ($header_menu AS $menu)
			{
				$menu_link = str_replace('%project_controller%', $project, $menu['link']);
				if(strpos(Registry::get('REQUEST_URI'), $menu_link)!==FALSE){
				$link = explode('/', $url);
				$parent_id = $menu['id'];
				if ($parent_id)
				{
					$sub_menu = $this->model_menu_items->getItemsWhere("menu_id = '{$menu_id}' AND parent_id = ".$parent_id." AND status = 1", "position");
				}
			
				$sub_html = '';
				$mob_sub_html = '';

				foreach ($sub_menu AS $s_menu)
				{
					$menu_link = str_replace('%project_controller%', $project, $s_menu['link']);
					$sub_html .= '
						<li class="ajax-anchor-in '.$s_menu['class'].'"><a href="'.$menu_link.'">'.$s_menu['name'].'</a></li>
					';

					$mob_sub_html .= '
						<li><a href="'.$menu_link.'">'.$s_menu['name'].'</a><em><i></i></em></li>
					';
				}
				}
				//var_dump(Registry::get('REQUEST_URI'));				var_dump($menu_link); var_dump(strpos(Registry::get('REQUEST_URI'), $menu_link));	echo "------\r\n";
				$menu_html .= '
					<li class="ajax-anchor '.$menu['class'].' '.(strpos(Registry::get('REQUEST_URI'), $menu_link)!==FALSE?"active":"").'">
	                    <a href="'.$menu_link.'">'.$menu['name'].'</a>
	                    <ul class="menu-sub-list">
	                        '.$sub_html.'
						</ul>
	                </li>
				';

				if ($mob_sub_html)
				{
					
					$mob_menu_html .= '
						<li class="has-drop-mobile">
		                    <a href="'.$menu_link.'">'.$menu['name'].'</a>
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
		                    <a href="'.$menu_link.'">'.$menu['name'].'</a>
		                </li>
					';
				}
			}

			if ($type == 'mobile')
			{
				$result = $mob_menu_html;
			}
			else
			{
				$result = $menu_html;
			}

			return $result;
		}
	}

	global $menu;
	$menu = new menu();
}