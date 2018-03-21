<?php
class controller_panoramas extends Controller
{
	function __construct(){

	}

	public function action_index(){

	}

	public function panoramas_vidget($id){
		return '
			<iframe width="100%" height="auto" src="https://feo.ua/panoramas/p'.$id.'/g" class="panorama-frame" frameborder="0" style="min-height: 315px;" allowfullscreen></iframe>
		';
	}
}