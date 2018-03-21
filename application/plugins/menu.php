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
				'project' => ''
			), $atts));

			$this->model_menu = new model_menu();
			$this->model_menu_items = $this->model_menu->model_menu_items();

			$header_menu = $this->model_menu_items->getItemsWhere("menu_id = 1 AND parent_id = 0 AND status = 1", "position");
			$menu_html = '';
			$mob_menu_html = '';
			foreach ($header_menu AS $menu)
			{
				//$sub_menu = $this->model_menu_items->getItemsWhere("menu_id = 1 AND parent_id = ".$menu['id'], "position");
				$link = explode('/', $url);
				$parent_id = $this->model_menu_items->getItemWhere("menu_id = 1 AND link = '".$link[1]."' AND status = 1", "id");

				if ($parent_id)
				{
					$sub_menu = $this->model_menu_items->getItemsWhere("menu_id = 1 AND parent_id = ".$parent_id['id']." AND status = 1", "position");
				}

				$sub_html = '';
				$mob_sub_html = '';
				
				foreach ($sub_menu AS $s_menu)
				{
					$link = str_replace('%project_controller%', $project, $s_menu['link']);
					$sub_html .= '
						<li class="ajax-anchor-in '.$s_menu['class'].'"><a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/'.$link.'">'.$s_menu['name'].'</a></li>
					';

					$mob_sub_html .= '
						<li><a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/'.$s_menu['link'].'">'.$s_menu['name'].'</a><em><i></i></em></li>
					';
				}
				$link = str_replace('%project_controller%', $project, $menu['link']);
				$menu_html .= '
					<li class="ajax-anchor '.$menu['class'].' '.(strpos(Registry::get('REQUEST_URI'), $link)?"active":"").'">
	                    <a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/'.$link.'">'.$menu['name'].'</a>
	                    <ul class="menu-sub-list">
	                        '.$sub_html.'
						</ul>
	                </li>
				';

				if ($mob_sub_html)
				{
					
					$mob_menu_html .= '
						<li class="has-drop-mobile">
		                    <a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/'.$link.'">'.$menu['name'].'</a>
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
		                    <a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/'.$link.'">'.$menu['name'].'</a>
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