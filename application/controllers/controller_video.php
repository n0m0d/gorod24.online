<?php
class controller_video extends Controller
{
	function __construct(){

	}

	public function action_index(){

	}

	public function video_vidget($url){
		$id =  explode('/', end(explode('=', $url)))[0];
		return '
			<iframe width="100%" height="auto" src="https://www.youtube.com/embed/'.$id.'" frameborder="0" allow="autoplay; encrypted-media" style="min-height: 315px;" allowfullscreen></iframe>
		';
	}
}