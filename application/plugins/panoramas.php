<?php
/*

Plugin Name: yashare
Plugin URI: yashare
Description: Плагин сайдбар-рекламы
Version: 1.0
Author: Шумилов Демьян
Author URI:

*/

if(!class_exists('panoramas', false))
{
	class panoramas
	{
		function __construct()
		{
			add_shortcode('panoramas', array($this, 'panoramas_init'));
		}

		public function panoramas_init($atts, $content = null)
		{
			extract(shortcode_atts(array(
				'id' => '',
			), $atts));

			$id =  explode('/', end(explode('=', $id)))[0];
			$result = '
				<iframe width="100%" height="auto" src="https://feo.ua/panoramas/p'.$id.'/g" class="panorama-frame" frameborder="0" style="min-height: 315px;" allowfullscreen></iframe>
			';

			return $result;
		}
	}

	global $panoramas;
	$panoramas = new panoramas();
}