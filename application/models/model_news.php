<?php
/* Automatic model generated
 * ver 0.1
 * model for site: mvc.test
 * date create: 2017-10-20 22:29:21
*/
class model_news extends Model
{
	protected $kafa;
   
	public function kafa() {
		return $this->kafa;
	}
	
	public function our() {
		return $this;
	}
	
	function __construct($config = array()) {
		$config = [
            "server" => "localhost",
            "database" => "site_21200",
            "prefix" => "",
            "name" => "our_news",
            "engine" => "MyISAM",
            "version" => "1",
            "collation" => "utf8_general_ci",
            "primary_key" => "news_id",
			"autoinit"  => false,
            "columns" => array(
				'news_head' => "TEXT NOT NULL",				'news_lid' => "TEXT NOT NULL",
				'news_body' => "TEXT NOT NULL",
				'news_vrez' => "TEXT NOT NULL",
				'news_aut' => "TEXT NOT NULL",
				'news_aut2' => "TEXT NOT NULL",
				'news_author' => "INT(11) NULL DEFAULT NULL",
				'news_video' => "TEXT NOT NULL",
				'news_video_you' => "VARCHAR(255) NOT NULL",
				'news_foto' => "TEXT NOT NULL",
				'news_foto_sm' => "TEXT NOT NULL",
				'big_open_foto' => "INT(1) NOT NULL",
				'news_foto_reportag' => "INT(1) NOT NULL DEFAULT '0'",
				'foto_all' => "INT(1) NOT NULL DEFAULT '1'",
				'news_podp' => "TEXT NOT NULL",
				'news_num' => "TEXT NOT NULL",
				'news_razd' => "TEXT NOT NULL",
				'news_kto' => "TEXT NOT NULL",
				'news_tag' => "VARCHAR(255) NOT NULL",
				'l_red' => "TEXT NOT NULL",
				'l_time' => "DATETIME NOT NULL",
				'l_main' => "TEXT NOT NULL",
				'l_comment' => "TEXT NOT NULL",
				'e_red' => "TEXT NOT NULL",
				'e_time' => "DATETIME NOT NULL",
				'e_comment' => "TEXT NOT NULL",
				'e_site' => "TEXT NOT NULL",
				'slovo' => "TEXT NOT NULL",
				'serial' => "TEXT NOT NULL",
				'fotorep' => "BIGINT(20) NOT NULL",
				'c_n' => "BIGINT(20) NOT NULL",
				'c_f' => "BIGINT(20) NOT NULL",
				'kat' => "TEXT NOT NULL",
				'ver ' => "TEXT NOT NULL",
				'town ' => "TEXT NOT NULL",
				'news_key ' => "TEXT NOT NULL",
				'news_des ' => "TEXT NOT NULL",
				'look ' => "TEXT NOT NULL",
				'news_date ' => "DATETIME NOT NULL",
				'our ' => "BIGINT(20) NOT NULL DEFAULT '1'",
				'kyrort ' => "BIGINT(20) NOT NULL",
				'lock ' => "INT(11) NOT NULL DEFAULT '0'",
				'looks ' => "INT(11) NOT NULL",
				'vk_ ' => "INT(11) NOT NULL",
				'vk_feo ' => "INT(11) NOT NULL",
				'vk_feorf ' => "INT(11) NOT NULL",
				'vk_g ' => "VARCHAR(200) NOT NULL",
				'fb ' => "VARCHAR(255) NOT NULL",
				'ot_name ' => "VARCHAR(255) NOT NULL",
				'ot_sylka ' => "VARCHAR(255) NOT NULL",
				'url ' => "TEXT NOT NULL",
				'url_ru ' => "TEXT NOT NULL",
				'kay_word ' => "TEXT NOT NULL",
				'id_pr ' => "VARCHAR(11) NOT NULL",
				'narod_id ' => "INT(11) NOT NULL",
				'akciya_id ' => "INT(11) NOT NULL",
				'on_off ' => "INT(2) NOT NULL",
				'news_lock ' => "INT(11) NOT NULL",
				'news_lock_for ' => "DATETIME NOT NULL",
				'show_comment ' => "INT(1) NOT NULL DEFAULT '1'",
				'news_inter_id ' => "INT(11) NULL DEFAULT NULL",
				'news_album_id ' => "INT(11) NULL DEFAULT NULL",
				'news_zamer_id ' => "INT(11) NULL DEFAULT NULL",
				'news_panorama ' => "INT(11) NULL DEFAULT NULL",
				'news_panorama_type ' => "INT(11) NOT NULL DEFAULT '0'",				
				),
			"index" => array(
				"kyrort" => array( 'kyrort' ),
				"lock" => array( 'lock' ),
				"narod_id" => array( 'narod_id' ),
				"date" => array( 'news_date' ),
				"news_date" => array( 'news_date', 'news_razd', 'town' ),
				"town_onoff" => array( 'town', 'news_razd', 'on_off' ),
				"news_razd" => array( 'news_razd', 'news_date', 'on_off' ),
			),
			"unique" => array(
				
			),
			"fulltext" => array(
				'news_head' => array( 'news_head', 'news_lid', 'news_body' ),
				'url_ru' => array( 'url_ru' ),
			),
			"revisions" => array(
				array(
					"version"       => "1",
					/*
					// Examples
					"before_query" => array(
						"SELECT NOW() FROM dual", "SELECT CURDATE() FROM dual"
					),
					"before_func" => "<MODEL FUNCTION NAME>",
					"del_index" => array(
						"<INDEX NAME1>", "<INDEX NAME2>"
					),
					"del_uniq" => array(
						"<UNIQ INDEX NAME1>", "<UNIQ INDEX NAME2>"
					),
					"del_fulltext" => array(
						"<FULLTEXT INDEX NAME1>", "<FULLTEXT INDEX NAME2>"
					),
					"add_columns" => array(
						"<NEW COLUMN NAME>"   => "VARCHAR(50) NOT NULL DEFAULT '' AFTER `<AFTER COLUMN>`"
					),
					"del_columns" => array(
						"<COLUMN NAME1>", "<COLUMN NAME2>"
					),
					"mod_columns" => array(
						"<COLUMN NAME1>" => array( "name"=>"<NEW COLUMN NAME1>", "type"=>"VARCHAR(50) NOT NULL DEFAULT '' AFTER `<AFTER COLUMN>`" ),
						"<COLUMN NAME2>" => array( "name"=>"<NEW COLUMN NAME2>", "type"=>"VARCHAR(50) NOT NULL DEFAULT '' AFTER `<AFTER COLUMN>`" ),
					),
					"add_index" => array(
						"<NEW INDEX NAME>"   => array( "<COLUMN NAME>" ),
					),
					"add_uniq" => array(
						"<NEW UNIQ INDEX NAME>"   => array( "<COLUMN NAME>" ),
					),
					"add_fulltext" => array(
						"<NEW FULLTEXT INDEX NAME>"   => array( "<COLUMN NAME>" ),
					),
					"engine" => "<NEW ENGINE |InnoDB|MyISAM|other>",
					"after_query" => array(
						"SELECT NOW() FROM dual", "SELECT CURDATE() FROM dual"
					),
					"after_func" => "<MODEL FUNCTION NAME>",
					*/
				),
			)
		];
		
		$kafa_config = $config;
		$kafa_config['name'] = 'kafa_news';
		
		parent::__construct($config);
		$this->kafa = new Model($kafa_config);
		
		//var_dump($kafa);
    }

	public function getTown($town, $limit=20){
		return $this->get()->union([$this->our(),$this->kafa()])->where("`town`='{$town}'")->order("news_date DESC")->limit($limit)->commit();
	}
	
	
}
?>