<?php
/*

Plugin Name: yashare
Plugin URI: yashare
Description: Плагин сайдбар-рекламы
Version: 1.0
Author: Шумилов Демьян
Author URI:

*/

if(!class_exists('sidebar_adv', false))
{
	class sidebar_adv
	{
		function __construct()
		{
			add_shortcode( 'sidebar_adv', array($this, 'sidebar_adv_init'));
		}

		public function sidebar_adv_init($atts, $content = null)
		{

			extract(shortcode_atts(array(
				'limit' => '',
			), $atts));

			$items = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT * FROM `main`.`reklama_s` WHERE `date` >= CURDATE() AND `on_off`='1' ORDER BY `mesto` ASC");

			foreach ($items as $item)
			{
				if ($item['phone'] || $item['phone_m'])
				{
					$phone = '<span class="icon-phone"></span><span class="number">'.($item['phone'] ? $item['phone'] : $item['phone_m']).'</span>';
				}
				$opis = str_replace('&lt;', '<', $item['opis']);
				$opis = str_replace('&gt;', '>', $opis);
				$result .= '
					<div class="block-sticker">
						<h3><a href="http://'.$item['sate'].'" target="_blank">'.$item['name_s'].'</a></h3>
						<p>'.str_replace(array('<p>', '</p>'), '', $opis).'</p>
						<div class="phone">
							'.$phone.'
						</div>
					</div>
				';
			}

			return $result;
		}
	}

	global $sidebar_adv;
	$sidebar_adv = new sidebar_adv();
}