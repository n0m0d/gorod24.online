<?php
/*

Plugin Name: yashare
Plugin URI: yashare
Description: Плагин Яндекс шары
Version: 1.0
Author: Шумилов Демьян
Author URI:

*/

if(!class_exists('yashare', false))
{
	class yashare
	{
		
		function __construct()
		{
			add_shortcode( 'yashare', array($this, 'yashare_2'));
		}
		
		public function yashare_2($atts)
		{
			return '<div class="ya-share2" style="margin-top: 20px; margin-bottom: 20px;" data-services="vkontakte,twitter,facebook,odnoklassniki,moimir,gplus" data-counter=""></div>';
		}
	}
	global $yashare;
	$yashare = new yashare();
}