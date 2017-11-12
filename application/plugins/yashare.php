<?php
/*

Plugin Name: yashare
Plugin URI: yashare
Description: Плагин Яндекс шаре
Version: 1.0
Author: Заднепряный Андрей
Author URI: 

*/

if(!class_exists('yashare', false)){
	class yashare{
		
		function __construct(){
			wp_register_script( 'yashare-es5-shims.min.js', '//yastatic.net/es5-shims/0.0.2/es5-shims.min.js' , '', '', true );
			wp_register_script( 'yashare-share.js', '//yastatic.net/share2/share.js' , '', '', true );
			
			wp_enqueue_script( 'yashare-es5-shims.min.js' );
			wp_enqueue_script( 'yashare-share.js' );
			
			add_shortcode( 'yashare', array($this, 'yashare_2'));
		}
		
		function yashare_2( $atts ){
			return '<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus" data-counter=""></div>';
		}

		
	}
	global $yashare;
	$yashare = new yashare();
	
}