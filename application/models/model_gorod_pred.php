<?php
/* Automatic model generated
 * ver 0.1
 * model for site: gorod24.online
 * date create: 2017-12-08 13:17:05
*/
class model_gorod_pred extends Model
{
	protected $model_pred_photos; 		public function model_pred_photos() 	{		return $this->model_pred_photos;	}
	protected $model_pred_otr; 		public function model_pred_otr() 	{		return $this->model_pred_otr;	}

	function __construct($config = array()) {
		$config = [
			"server" => "localhost",
            "database" => "gorod24.online",
            "prefix" => "gorod_",
            "name" => "pred",
            "engine" => "MyISAM",
            "version" => "1",
            "row_format" => "Dynamic",
            "create_time" => "2017-11-15 12:03:17",
            "collation" => "cp1251_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'name' => "text NOT NULL",
				'name_kat' => "text NOT NULL",
				'adr_f' => "text NOT NULL",
				'adr_y' => "text NOT NULL",
				'phones' => "text NOT NULL",
				'menag' => "text NOT NULL",
				'status' => "smallint(6) NULL DEFAULT NULL",
				'show_in_app' => "int(11) NOT NULL DEFAULT '1'",
				'activ' => "text NOT NULL",
				'activ_gaz' => "varchar(100) NOT NULL",
				'otr' => "text NOT NULL",
				'rating' => "int(12) NOT NULL DEFAULT '0'",
				'rating_tel' => "int(12) NOT NULL DEFAULT '0'",
				'rating_ob' => "int(12) NOT NULL DEFAULT '0'",
				'email' => "text NOT NULL",
				'web' => "text NOT NULL",
				'oplata' => "date NOT NULL DEFAULT '0000-00-00'",
				'oplata_g24' => "date NOT NULL",
				'login' => "text NOT NULL",
				'passw' => "text NOT NULL",
				'katalog' => "text NOT NULL",
				'vh' => "int(1) NOT NULL DEFAULT '0'",
				'viz' => "int(11) NOT NULL DEFAULT '0'",
				'mesto' => "char(2) NOT NULL DEFAULT '10'",
				'id_buhg' => "int(11) NOT NULL DEFAULT '0'",
				'on_off' => "int(1) NOT NULL DEFAULT '1'",
				'not_in_gazeta' => "int(11) NOT NULL DEFAULT '0'",
				'icq' => "text NOT NULL",
				'skype' => "varchar(200) NOT NULL",
				'vkcom' => "varchar(200) NOT NULL",
				'twitter' => "varchar(200) NOT NULL",
				'facebook' => "varchar(200) NOT NULL",
				'odnoklassniki' => "varchar(200) NOT NULL",
				'work' => "text NOT NULL",
				'lunch' => "text NOT NULL",
				'satarday' => "text NOT NULL",
				'sunday' => "text NOT NULL",
				'sunday_work' => "text NOT NULL",
				'priem' => "text NOT NULL",
				'name_ua' => "text NOT NULL",
				'name_kat_ua' => "text NOT NULL",
				'adr_f_ua' => "text NOT NULL",
				'adr_y_ua' => "text NOT NULL",
				'activ_ua' => "text NOT NULL",
				'name_en' => "text NOT NULL",
				'name_kat_en' => "text NOT NULL",
				'adr_f_en' => "text NOT NULL",
				'adr_y_en' => "text NOT NULL",
				'activ_en' => "text NOT NULL",
				'town' => "text NOT NULL",
				'h_redir' => "text NOT NULL",
				'jump_to_site' => "bigint(20) NOT NULL DEFAULT '0'",
				'feo_domen' => "varchar(200) NOT NULL",
				'yandex' => "varchar(200) NOT NULL",
				'google' => "varchar(200) NOT NULL",
				'url' => "text NOT NULL",
				'url_ru' => "varchar(255) NULL DEFAULT NULL",
				'fio_contakt' => "varchar(200) NOT NULL",
				'phones_contakt' => "varchar(200) NOT NULL",
				'mail_contakt' => "varchar(200) NOT NULL",
				'vip_code' => "varchar(10) NULL DEFAULT NULL",
				
				),
			"index" => array(
				"oplata" => array( 'oplata', 'status' ),
				"oplata_2" => array( 'oplata' ),
				"tel_m" => array( 'phones_contakt', 'mail_contakt' ),
				"p_k" => array( 'phones_contakt' ),
			),
			"unique" => array(
				
			),
			"fulltext" => array(
				'phones' => array( 'phones', 'phones_contakt' ),
				'url_ru' => array( 'url_ru' ),
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		parent::__construct($config);

		$pred_photos_config = [
			"server" => "localhost",
			"database" => "gorod24.online",
			"prefix" => "gorod_",
			"name" => "pred_photos",
			"engine" => "InnoDB",
			"version" => "1",
			"collation" => "cp1251_general_ci",
			"primary_key" => "id",
			"autoinit"  => false,
			"columns" => array(
				'name' => "varchar(250) NOT NULL",
				'type' => "int(11) NOT NULL",
				'pid' => "int(11) NOT NULL",
				'coord_id' => "int(11) NULL DEFAULT NULL",
				'date_create' => "datetime NOT NULL",
				'date_update' => "datetime NOT NULL",
				'file' => "varchar(250) NOT NULL",
			),
			"index" => array(
				//"oplata" => array( 'coord_id', 'coord_id' ),
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
		$this->model_pred_photos = new Model($pred_photos_config);

		$pred_otr_config = [
			"server" => "localhost",
			"database" => "gorod24.online",
			"prefix" => "gorod_",
			"name" => "pred_otr",
			"engine" => "MyISAM",
			"version" => "1",
			"collation" => "cp1251_general_ci",
			"primary_key" => "id",
			"autoinit"  => false,
			"columns" => array(
				'name' => "varchar(128) NOT NULL",
				'rating' => "int(11) NOT NULL DEFAULT '1'",
				'sub_otr' => "varchar(200) NOT NULL",
				'sub_soc' => "text NOT NULL",
				'img' => "text NOT NULL",
				'name_ua' => "text NOT NULL",
				'name_en' => "text NOT NULL",
				'kyrort_name' => "text NOT NULL",
				'seo' => "text NOT NULL",
				'url' => "text NOT NULL",
				'url_ru' => "varchar(250) NULL",
				'icon' => "varchar(255) NOT NULL",
			),
			"index" => array(
				//"oplata" => array( 'coord_id', 'coord_id' ),
			),
			"unique" => array(

			),
			"fulltext" => array(
				//"url_ru" => array( 'url_ru' ),
			),
			"revisions" => array(
				array(
					"version"       => "1",
				),
			)
		];
		$this->model_pred_otr = new Model($pred_otr_config);
    }
}
?>