<?php
/*

Plugin Name: yashare
Plugin URI: yashare
Description: Плагин сайдбар-рекламы
Version: 1.0
Author: Шумилов Демьян
Author URI:

*/

if(!class_exists('video', false))
{
	class video
	{
		function __construct()
		{
			add_shortcode('video', array($this, 'video_init'));
		}

		public function video_init($atts, $content = null)
		{
			extract(shortcode_atts(array(
				'url' => '',
			), $atts));

			$id =  explode('/', end(explode('=', $url)))[0];
			$result = '
				<iframe width="100%" height="auto" src="https://www.youtube.com/embed/'.$id.'" frameborder="0" allow="autoplay; encrypted-media" style="min-height: 315px;" allowfullscreen></iframe>
			';

			return $result;
		}
	}

	global $video;
	$video = new video();
}