<?php
class model_radio extends Model
{
	
	protected $program_category;
	public function program_category() { return $this->program_category; }
	
	protected $programs;
	public function programs() { return $this->programs; }
	
	protected $questions;
	public function questions() { return $this->questions; }
	
	function __construct($config = array()) {
		$config = [
            "server" => "radio",
            "database" => "feo.fm",
            "prefix" => "radio_",
            "name" => "projects",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'city_id' => "INT(11) NOT NULL DEFAULT '0'",
				'name' => "VARCHAR(150) NOT NULL DEFAULT ''",
				'description' => "TEXT NOT NULL DEFAULT ''",
				'audio_stream' => "VARCHAR(150) NOT NULL DEFAULT ''",
				'video_stream_url' => "VARCHAR(150) NOT NULL DEFAULT ''",
				'video_stream_html' => "TEXT NOT NULL DEFAULT ''",
				'phone' => "VARCHAR(50) NOT NULL DEFAULT '1'",
				'substrate_bg' => "VARCHAR(255) NOT NULL DEFAULT '1'",
				'top_banner' => "VARCHAR(255) NOT NULL DEFAULT '1'",
				'status' => "INT(1) NOT NULL DEFAULT '1'",
				),
			"index" => array(
			),
			"unique" => array(
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		parent::__construct($config);
		
		$program_category_config = [
            "server" => "radio",
            "database" => "feo.fm",
            "prefix" => "radio_",
            "name" => "program_category",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "VARCHAR(150) NOT NULL DEFAULT ''",
				),
			"index" => array(
			),
			"unique" => array(
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->program_category = new Model($program_category_config);
		
		$programs_config = [
            "server" => "radio",
            "database" => "feo.fm",
            "prefix" => "radio_",
            "name" => "program",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'descr' => "TEXT NOT NULL DEFAULT ''",
				'name' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'link' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'img' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'cat_text' => "VARCHAR(250) NOT NULL DEFAULT ''",
				'cat_id' => "INT(11) NOT NULL DEFAULT ''",
				'date_upload' => "DATETIME NOT NULL",
				'date_pub' => "DATETIME NOT NULL",
				'author' => "INT(11) NOT NULL",
				),
			"index" => array(
			),
			"unique" => array(
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->programs = new Model($programs_config);
		
		$questions_config = [
            "server" => "radio",
            "database" => "feo.fm",
            "prefix" => "radio_",
            "name" => "questions",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'uid' => "INT(11) NULL DEFAULT NULL",
				'name' => "VARCHAR(255) NULL DEFAULT NULL",
				'phone' => "VARCHAR(50) NULL DEFAULT NULL",
				'text' => "TEXT NOT NULL DEFAULT ''",
				'ip' => "VARCHAR(50) NOT NULL DEFAULT ''",
				'date' => "DATETIME NOT NULL",
				'time' => "INT(50) NOT NULL",
				),
			"index" => array(
			),
			"unique" => array(
			),
			"fulltext" => array(
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->questions = new Model($questions_config);
		
	}
	
	public function getAudioStream($city_id, $user_id=null, $access_token=null){
		$result = $this->get("audio_stream as stream")->from($this)->where("`city_id`={$city_id} and `status`=1")->commit('row');
		return $result;
	}
	
	public function getProgramRubrics($city_id, $user_id=null, $access_token=null){
		$result = $this->program_category->get("id, name")->from($this->program_category)->where("`status`=1 AND (SELECT COUNT(*) FROM `{$this->programs->getdatabasename()}`.`{$this->programs->gettablename()}` as `pr` WHERE `pr`.`cat_id`=`{$this->program_category->gettablename()}`.`id`)>0")->commit('all');
		return $result;
	}
	
	public function getPrograms($city_id, $filters, $start=0, $limit=20, $user_id=null, $access_token=null){
		$rubric_id = $filters['rubric'];
		$date = $filters['date'];
		$where = '`status` = 1';
		if(!empty($rubric_id)){
			$where .= " AND `cat_id`={$rubric_id}";
		}
		if(!empty($date)){
			$where .= " AND date(`date_pub`)='{$date}'";
		}
		$result = $this->programs->get("name, descr, concat('https://feo.fm/', `link`) as link, concat('https://feo.fm/', `img`) as img, date_pub as date")
		->from($this->programs)
		->where($where)
		->offset($start)
		->limit($limit)
		->order("date_pub DESC, id DESC")
		->commit('all');
		return $result;
	}
	
	public function sendQuestion($city_id, $data, $user_id=null, $access_token=null){
		$time = time() - 86400;
		$ip = getIp();
		if($user_id){
			$check = $this->questions->getCountWhere("`uid` = {$user_id} AND `time`>'{$time}'", "time");
			if($check>0){
				return ["error"=>1, "message"=>"Вы уже отправляли сообщение сегодня"];
			}
			else {
				$this->questions->Insert([
					'uid' => $user_id,
					'name' => $data['name'],
					'phone' => $data['phone'],
					'text' => $data['text'],
					'ip' => $ip,
					'date' => date("y-m-d H:i:s"),
					'time' => time(),
				]);
				return ["succes"=>1, "message"=>"Сообщение отправлено"];
			}
		}
		else {
			$check = $this->questions->getCountWhere("`ip` = '{$ip}' AND `time`>'{$time}'", "time");
			if($check>0){
				return ["error"=>1, "message"=>"Вы уже отправляли сообщение сегодня"];
			}
			else {
				$this->questions->Insert([
					'uid' => $user_id,
					'name' => $data['name'],
					'phone' => $data['phone'],
					'text' => $data['text'],
					'ip' => $ip,
					'date' => date("y-m-d H:i:s"),
					'time' => time(),
				]);
				return ["succes"=>1, "message"=>"Сообщение отправлено"];
			}
		}
	}
	
	
	
}
?>