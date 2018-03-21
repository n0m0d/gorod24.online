<?php
/* Automatic model generated
 * ver 0.1
 * model for site: gorod24.online
 * date create: 2017-12-08 13:17:05
*/
class model_gorod_comments extends Model
{

	protected $model_comments_pages; 		public function model_comments_pages() 	{		return $this->model_comments_pages;	}
	protected $model_comments_likes; 		public function model_comments_likes() 	{		return $this->model_comments_likes;	}

	function __construct($config = array()) {
		$config = [
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "comments",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Dynamic",
            "create_time" => "2017-11-15 12:03:17",
            "collation" => "utf8_general_ci",
            "primary_key" => "com_id",
			"autoinit"  => false,
            "columns" => array(
				'com_text' => "text NOT NULL", // Комментарий
				'com_avtor' => "int(11) NOT NULL", // Айди автора
				'com_date' => "datetime NOT NULL", // Дата
				'com_for_table' => "varchar(50) NOT NULL", // Таблица страницы комментария
				'com_for_column' => "varchar(50) NOT NULL", // Колонка таблицы страницы комментария
				'com_main_id' => "int(11) NOT NULL",
				'com_parent_id' => "int(11) NOT NULL", // Айди страницы в таблице (на которой оставлен комментарий)
				'com_page_id' => "int(11) NOT NULL",
				'com_status' => "int(11) NOT NULL DEFAULT '2'", // Статус комментарий вкл/выкл
				'com_ip' => "varchar(50) NULL DEFAULT NULL", // IP
	            'com_agent' => "varchar(255) NOT NULL", // Инфа браузера
	            'com_old_id' => "int(11) NULL DEFAULT NULL",
	            'com_gazeta_user_id' => "int(11) NULL DEFAULT NULL",
	            'com_gazeta_sub_key' => "varchar(50) NULL DEFAULT NULL",
				'com_user_alias' => "varchar(100) NULL DEFAULT NULL",
				),
			"index" => array(
				"com_for_table_com_for_column_com_main_id" => array( 'com_for_table', 'com_for_column', 'com_main_id' ),
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

		$comments_pages_config = [
			"server" => "localhost",
			"database" => "gorod24.online",
			"prefix" => "gorod_",
			"name" => "comments_pages",
			"engine" => "InnoDB",
			"version" => "1",
			"collation" => "utf8_general_ci",
			"primary_key" => "id",
			"autoinit"  => false,
			"columns" => array(
				'com_url' => "varchar(255) NOT NULL", //URL страницы
				'com_url_md5' => "varchar(50) NOT NULL", // MD5 url страницы
				'com_controller' => "varchar(255) NOT NULL", // Контроллер на который ссылается комментарий
				'com_action' => "varchar(255) NOT NULL", // Экшен этого контроллера
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
		$this->model_comments_pages = new Model($comments_pages_config);

		$comments_likes_config = [
			"server" => "localhost",
			"database" => "gorod24.online",
			"prefix" => "gorod_",
			"name" => "comments_likes",
			"engine" => "InnoDB",
			"version" => "1",
			"collation" => "utf8_general_ci",
			"primary_key" => "id",
			"autoinit"  => false,
			"columns" => array(
				'like_id' => "int(11) NOT NULL",
				'like_uid' => "int(11) NULL",
				'like_date' => "datetime NULL",
				'like_for_table' => "varchar(50) NOT NULL",
				'like_for_column' => "varchar(50) NOT NULL",
				'like_status' => "int(11) NULL",
				'like_ip' => "varchar(50) NULL",
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
		$this->model_comments_likes = new Model($comments_likes_config);
    }
}
?>