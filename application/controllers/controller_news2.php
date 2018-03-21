<?php
class controller_news2 extends Controller
{
	function __construct()
	{
		//Модели
		$this->model_news = new model_gorod_news();
	}
	
	public function action_index($array = array())
	{
			$start = microtime();
			$getNews = $this->model_news->getItem(63087);
			$end = microtime();
			var_dump($end - $start);
	}

}