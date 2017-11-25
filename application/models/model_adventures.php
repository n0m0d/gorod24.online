<?php
class model_adventures extends Model
{
	protected $adv_off; public function adv_off(){ return $this->adv_off; }
	protected $photos; public function photos(){ return $this->photos; }
	protected $mainC; public function mainC(){ return $this->mainC; }
	protected $subC; public function subC(){ return $this->subC; }
	protected $options; public function options(){ return $this->options; }
	protected $values; public function values(){ return $this->values; }
	protected $adv_values; public function adv_values(){ return $this->adv_values; }
	protected $autoup; public function autoup(){ return $this->autoup; }
	protected $autoup_log; public function autoup_log(){ return $this->autoup_log; }
	protected $up; public function up(){ return $this->up; }
	protected $claim; public function claim(){ return $this->claim; }
	
	function __construct($config = array()) {
		$config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "adventures",
            "engine" => "InnoDB",
            "version" => "2.9",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
                'user_id'           => "INT NOT NULL DEFAULT '0'",
                'user_name'         => "VARCHAR(250) NOT NULL DEFAULT ''",
                'user_email'        => "VARCHAR(250) NOT NULL DEFAULT ''",
                'user_email_show'   => "ENUM('0','1') NOT NULL DEFAULT '0'",            
                'user_phone'        => "VARCHAR(250) NOT NULL DEFAULT ''",
                'main_catid'        => "INT NOT NULL DEFAULT '0'",
                'sub_catid'         => "INT NOT NULL DEFAULT '0'",
                'caption'           => "VARCHAR(250) NOT NULL DEFAULT ''",
                'adv_text'          => "TEXT NOT NULL DEFAULT ''",                      
                'only_gaz'          => "ENUM('0','1') NOT NULL DEFAULT '0'",           
                'descr'             => "TEXT NOT NULL DEFAULT ''",                      
                'why_bad'           => "TEXT NOT NULL DEFAULT ''",                      
                'price'             => "DOUBLE NOT NULL DEFAULT '0'",
				'price_from_to'     => "ENUM('0','1','2') NOT NULL DEFAULT '0'",        
				'price_ed_izm'      => "INT NOT NULL DEFAULT '0'",                      
                'price_valut'       => "VARCHAR(10) NOT NULL DEFAULT ''",
                'price_discuse'     => "ENUM('0','1') NOT NULL DEFAULT '0'",            
                'price_free'        => "ENUM('0','1') NOT NULL DEFAULT '0'",            
                'add_time'          => "INT NOT NULL DEFAULT '0'",
                'up_time'           => "INT NOT NULL DEFAULT '0'",
                'edit_time'         => "INT NOT NULL DEFAULT '0'",                      
                'up_time_send'      => "INT NOT NULL DEFAULT '0'",                      
                'json_data'         => "TEXT NOT NULL DEFAULT ''",
                'json_photos'       => "TEXT NOT NULL DEFAULT ''",
                'json_options'      => "TEXT NOT NULL DEFAULT ''",                      
                'url'               => "VARCHAR(512) NOT NULL DEFAULT ''",             
                'user_ip'           => "VARCHAR(31) NOT NULL DEFAULT ''",               
                'vip'               => "ENUM('0','1') NOT NULL DEFAULT '0'",            
                'vip_id'            => "INT NOT NULL DEFAULT '0'",                      
                'city'              => "VARCHAR(120) NOT NULL DEFAULT ''",              
                'region'            => "VARCHAR(120) NOT NULL DEFAULT ''",              
                'city_id'			=> "INT NOT NULL DEFAULT '0'",
                'region_id'			=> "INT NOT NULL DEFAULT '0'",
			    'video'             => "TEXT NOT NULL DEFAULT ''",                     
                'look_phone'        => "INT NOT NULL DEFAULT '0'",                     
                'look_email'        => "INT NOT NULL DEFAULT '0'",                      
                'look_url'          => "INT NOT NULL DEFAULT '0'",                      
                'view_count'        => "INT NOT NULL DEFAULT '0'",                      
                'open_count'        => "INT NOT NULL DEFAULT '0'",                      
                'gaz_frame'         => "ENUM('0','1') NOT NULL DEFAULT '0'",
                'simple_plus'       => "ENUM('0','1') NOT NULL DEFAULT '0'",
                'on_off'            => "ENUM('0','1','2','3','4') NOT NULL DEFAULT '2'",
                'latitude'          => "FLOAT NULL DEFAULT NULL",
                'longitude'         => "FLOAT NULL DEFAULT NULL",
				),
			"index" => array(
                'fk_user'           => array( 'user_id' ),
                'fk_maincat'        => array( 'main_catid' ),
                'fk_subcat'         => array( 'sub_catid' ),
                'onoff'             => array( 'on_off' ),
                'i_onlygaz'         => array( 'only_gaz' )                             
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
        $config['name'] = 'adventures_off';
        $this->adv_off = new Model($config);
       
	   $photos_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "adventures_photos",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'adv_id'        => "INT NOT NULL DEFAULT '0'",
                'name'          => "VARCHAR(250) NOT NULL DEFAULT ''",
                'ext'           => "VARCHAR(10) NOT NULL DEFAULT ''",
                'ismain'        => "ENUM('0','1') NOT NULL DEFAULT '1'"
            ),
            'index' => array(
                'fk_adv'        => array( 'adv_id' ),
                'main'          => array( 'ismain' )
            ),
            'unique'    => array(),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
        $this->photos = new Model($photos_config);
		
	    $main_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "main_category",
            "engine" => "InnoDB",
            "version" => "1.9",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'name'              => "VARCHAR(250) NOT NULL DEFAULT ''",
                'in_menu'           => "ENUM('0','1') NOT NULL DEFAULT '1'",                
                'cpu'               => "VARCHAR(250) NOT NULL DEFAULT ''",                  
                'cpu_hash'          => "BINARY(16) NOT NULL DEFAULT 0x0000000000000000",    
				'cpu_ru'            => "VARCHAR(250) NOT NULL DEFAULT ''",                  
                'cpu_hash_ru'       => "BINARY(30) NOT NULL DEFAULT 0x0000000000000000",    
                'menu_icon'         => "VARCHAR(120) NOT NULL DEFAULT ''",                 
                'center_title'      => "VARCHAR(250) NOT NULL DEFAULT ''",                  
                'in_center_menu'    => "ENUM('0','1') NOT NULL DEFAULT '1'",               
                'can_select'        => "ENUM('0','1') NOT NULL DEFAULT '1'",               
                'price_label'       => "VARCHAR(120) NOT NULL DEFAULT ''",                 
				'max_free'          => "INT NOT NULL DEFAULT '0'",                          
				'max_free_g'        => "INT NOT NULL DEFAULT '0'",                         
                'pos'               => "INT NOT NULL DEFAULT '0'",                          
                'seo_title'         => "TEXT NOT NULL DEFAULT ''",                          
                'seo_descr'         => "TEXT NOT NULL DEFAULT ''",                          
                'seo_keys'          => "TEXT NOT NULL DEFAULT ''",                          
				'seo_title_ru'      => "TEXT NOT NULL DEFAULT ''",                         
                'seo_descr_ru'      => "TEXT NOT NULL DEFAULT ''",                          
                'seo_keys_ru'       => "TEXT NOT NULL DEFAULT ''",                         
                'on_off'            => "ENUM('0','1','2') NOT NULL DEFAULT '1'"            
            ),
            'index' => array(),
            'unique'    => array(),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->mainC = new Model($main_config);
		
	    $sub_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "sub_category",
            "engine" => "InnoDB",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'pid'           => "INT NOT NULL DEFAULT '0'",
                'name'          => "VARCHAR(250) NOT NULL DEFAULT ''",
				'namer'          => "VARCHAR(250) NOT NULL DEFAULT ''",
                'in_menu'       => "ENUM('0','1') NOT NULL DEFAULT '1'",                    
                'in_submenu'    => "ENUM('0','1') NOT NULL DEFAULT '1'",                    
                'cpu'           => "VARCHAR(250) NOT NULL DEFAULT ''",                      
                'cpu_hash'      => "BINARY(16) NOT NULL DEFAULT 0x0000000000000000",        
				'cpu_ru'           => "VARCHAR(250) NOT NULL DEFAULT ''",                   
                'cpu_hash_ru'      => "BINARY(30) NOT NULL DEFAULT 0x0000000000000000",     
                'can_select'    => "ENUM('0','1') NOT NULL DEFAULT '1'",                    
                'need_price'    => "ENUM('0','1') NOT NULL DEFAULT '1'",                    
                'price_label'   => "VARCHAR(120) NOT NULL DEFAULT ''",                     
				'max_free'          => "INT NOT NULL DEFAULT '0'",                          
				'max_free_g'        => "INT NOT NULL DEFAULT '0'",                          
                'pos'           => "INT NOT NULL DEFAULT '0'",                              
                'seo_title'     => "TEXT NOT NULL DEFAULT ''",                             
                'seo_descr'     => "TEXT NOT NULL DEFAULT ''",                             
                'seo_keys'      => "TEXT NOT NULL DEFAULT ''",                              
				'seo_title_ru'     => "TEXT NOT NULL DEFAULT ''",                           
                'seo_descr_ru'     => "TEXT NOT NULL DEFAULT ''",                           
                'seo_keys_ru'      => "TEXT NOT NULL DEFAULT ''",                          
                'on_off'        => "ENUM('0','1','2') NOT NULL DEFAULT '1'",               
                'is_auto'       => "ENUM('0','1') NOT NULL DEFAULT '0'",                   
				'important_price'=>"ENUM('0','1') NOT NULL DEFAULT '0'", 					
				'important_price_g'=>"ENUM('0','1') NOT NULL DEFAULT '0'", 					
				'no_price'       =>"ENUM('0','1') NOT NULL DEFAULT '0'", 					
            ),
            'index' => array(
                'fk_pid'        => array( 'pid' ),
                'h_cpuhash'     => array( 'cpu_hash' ),                                    
                'onoff'         => array( 'on_off' )
			),
            'unique'    => array(),
            'fulltext'  => array(),
            'version'   => '1.11',
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->subC = new Model($sub_config);
		
	    $options_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "options",
            "engine" => "InnoDB",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'name'          => "VARCHAR(250) NOT NULL DEFAULT ''",
				'data_type'     => "INT NOT NULL DEFAULT '0'",             /* 
				 * 0 - Выпадалка Текст
				 * 1 - Выпадалка Число
				 * 2 - Текстовое поле
				 * 3 - Числовое поле
				 * 4 - Множественный выбор
				 * 5 - Карта
				 * 6 - Выбор даты
				 * 7 - Выбор диапазона дат
				 * 8 - Выбор нескольких дат
				 */
                'is_req'        => "ENUM('0','1') NOT NULL DEFAULT '0'",
                'on_off'        => "ENUM('0','1','2') NOT NULL DEFAULT '1'",        
                'is_gaz'        => "ENUM('0','1') NOT NULL DEFAULT '0'",
                'is_auto'       => "ENUM('0','1') NOT NULL DEFAULT '0'",            
                'in_filter'     => "ENUM('0','1') NOT NULL DEFAULT '0'",            
                'filter_type'   => "ENUM('0','1','2','3') NOT NULL DEFAULT '0'",    /* 
                 * 0 - Выпадающий список
                 * 1 - Несколько значений (Галочка)
                 * 2 - Диапазон значений
                 * 3 - 
                 *                       */
				'filter_options'=> "ENUM('0','1') NOT NULL DEFAULT '0'",             /* 
				 * 0 - из утвержденных (предустановленных)
				 * 1 - из объявлений
				 */
				'width'				=> "VARCHAR(10) NOT NULL DEFAULT '300px'",              
				'prefix'        	=> "VARCHAR(128) NOT NULL DEFAULT ''",              
                'unit'          	=> "VARCHAR(128) NOT NULL DEFAULT ''",              
                'space_prefix'		=> "ENUM('0','1') NOT NULL DEFAULT '1'",             
                'own_value'     	=> "ENUM('0','1') NOT NULL DEFAULT '1'",            
                'name_field'		=> "ENUM('0','1') NOT NULL DEFAULT '1'",             
                'descr_field'		=> "ENUM('0','1') NOT NULL DEFAULT '1'",             
                'gaz_field'			=> "ENUM('0','1') NOT NULL DEFAULT '1'",            
				'show_optid'    	=> "INT NULL DEFAULT NULL",
				'show_optval'   	=> "INT NULL DEFAULT NULL", 
            ),
            'index' => array(
                'onoff'         => array( 'on_off' )
			),
            'unique'    => array(),
            'fulltext'  => array(),
            'version'   => '1.1',
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->options = new Model($options_config);
		
	    $options_values_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "values",
            "engine" => "InnoDB",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'vid'           => "INT NOT NULL DEFAULT '0'",
                'pid'           => "INT NOT NULL DEFAULT '0'",
                'uid'           => "INT NULL DEFAULT NULL",
                'value'         => "VARCHAR(250) NOT NULL DEFAULT ''",
                'numeric_value' => "DOUBLE NULL DEFAULT NULL",
                'add_value'     => "VARCHAR(250) NOT NULL DEFAULT ''",      		/* 1.1 */
                'descr_value'   => "VARCHAR(250) NOT NULL DEFAULT ''",      		/* 1.10 */
                'gaz_value'     => "VARCHAR(250) NOT NULL DEFAULT ''",      		/* 1.5 */
                'has_cpu'       => "ENUM('0','1') NOT NULL DEFAULT '0'",    		/* 1.4 */
                'cpu'           => "VARCHAR(250) NOT NULL DEFAULT ''",      		/* 1.4 */
                'cpu_ru'        => "VARCHAR(250) NOT NULL DEFAULT ''",     			/* 1.4 */
                'seo_title'     => "TEXT NOT NULL DEFAULT ''",
                'seo_descr'     => "TEXT NOT NULL DEFAULT ''",
                'seo_keys'      => "TEXT NOT NULL DEFAULT ''",
                'seo_title_ru'  => "TEXT NOT NULL DEFAULT ''",
                'seo_descr_ru'  => "TEXT NOT NULL DEFAULT ''",
                'seo_keys_ru'   => "TEXT NOT NULL DEFAULT ''",
                'on_off'        => "ENUM('0','1','2') NOT NULL DEFAULT '1'",		/* 1.2 change */
                'is_auto'       => "ENUM('0','1') NOT NULL DEFAULT '0'",     		/* 1.3 */
                'is_gaz'        => "ENUM('0','1') NOT NULL DEFAULT '0'",
                'pos'           => "INT NOT NULL DEFAULT '0'",
				'no_important_price'=>"ENUM('0','1') NOT NULL DEFAULT '0'", 	 	/* 1.9 */
				'ip'			=> "VARCHAR(50) NULL DEFAULT NULL",    				/* 1.10 */
				'agent'			=> "VARCHAR(250) NULL DEFAULT NULL",    			/* 1.10 */
				'show_optid'	=> "INT NULL DEFAULT NULL",    						/* 1.10 */
				'show_optval'	=> "INT NULL DEFAULT NULL",    						/* 1.10 */
            ),
            'index' => array(
                'fk_pid'        => array( 'pid' ),
                'onoff'         => array( 'on_off' ),
                'i_cpu'         => array( 'has_cpu' , 'cpu' )               		/* 1.4 */
			),
            'unique'    => array(
                'vid_pid'       => array( 'vid' , 'pid' )
			),
            'fulltext'  => array(),
            'version'   => '1.5',
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->values = new Model($options_values_config);
	    
		$adv_values_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "adv_options_values",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'adv_id'		=> "INT NOT NULL DEFAULT '0'",
                'opt_id'		=> "INT NOT NULL DEFAULT '0'",
                'val_id'		=> "INT NULL DEFAULT NULL",
                'text_val'		=> "VARCHAR(255) NULL DEFAULT NULL",
                'num_val'		=> "DOUBLE NULL DEFAULT NULL",
            ),
            'index' => array(
			),
            'unique'    => array(
			),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->adv_values = new Model($adv_values_config);
	    
		$autoup_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "auto_up",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'adv_id'        => "INT NOT NULL DEFAULT '0'",
                'days_count'    => "INT NOT NULL DEFAULT '0'",
                'need_count'    => "INT NOT NULL DEFAULT '0'",
                'upok_count'    => "INT NOT NULL DEFAULT '0'",
                'start_time'    => "INT NOT NULL DEFAULT '0'",
                'last_up'       => "INT NOT NULL DEFAULT '0'",
                'enabled'       => "ENUM('0','1') NOT NULL DEFAULT '0'"
            ),
            'index' => array(
                'fk_adv'        => array( 'adv_id' ),
                'i_enabled'     => array( 'enabled' ),
                'adv_need_ok'   => array( 'adv_id' , 'need_count', 'upok_count'  )
			),
            'unique'    => array(
			),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->autoup = new Model($autoup_config);
		
		$autoup_log_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "auto_up_log",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'adv_id'        => "INT NOT NULL DEFAULT '0'",
                'utx'           => "INT NOT NULL DEFAULT '0'",
                'this_count'    => "INT NOT NULL DEFAULT '0'",
                'need_count'    => "INT NOT NULL DEFAULT '0'"
            ),
            'index' => array(
			),
            'unique'    => array(
			),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->autoup_log = new Model($autoup_log_config);
	    
		$up_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "up_register",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'adv_id'    => "INT NOT NULL DEFAULT '0'",
                'utx'       => "INT NOT NULL DEFAULT '0'"
            ),
            'index' => array(
			),
            'unique'    => array(
                'fk_adv'    => array( 'adv_id' )
			),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->up = new Model($up_config);
	    
		$claim_config = array(
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "claim",
            "engine" => "InnoDB",
            "version" => "1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            'columns' => array(
                'uid'       => "INT NOT NULL DEFAULT '0'",
                'adv_id'    => "INT NOT NULL DEFAULT '0'",
                'message'   => "TEXT NOT NULL DEFAULT ''",
                'utx'       => "INT NOT NULL DEFAULT '0'"
            ),
            'index' => array(
			),
            'unique'    => array(
			),
            'fulltext'  => array(),
            'engine'    => 'InnoDB',
            'revisions' => array(
                array(
                    'version'       => '1'
                )
            )
        );
		$this->claim = new Model($claim_config);
		
	}
	
    /**
     * Включает объявление
     * @param type $adv_id
     */
    public function AdvToOn($adv_id) {
        if (is_numeric($adv_id)) {
            $query = "DELETE FROM `{$this->getdatabasename()}`.`{$this->gettablename()}` WHERE `id`='{$adv_id}' LIMIT 1;";
            $this->db()->query($query);
            $query = "
                INSERT INTO
                    `{$this->getdatabasename()}`.`{$this->gettablename()}`
                        SELECT * FROM `{$this->adv_off->getdatabasename()}`.`{$this->adv_off->gettablename()}` WHERE `id`='{$adv_id}' LIMIT 1;
                ";
            $this->db()->query($query);
            $query = "
                DELETE FROM
                    `{$this->adv_off->getdatabasename()}`.`{$this->adv_off->gettablename()}` WHERE `id`='{$adv_id}' LIMIT 1;
                ";
            $this->db()->query($query);
			$this->ParseNewOption(null, $adv_id);
			return true;
        }
		else return false;
    }
	
    /**
     * Выключает объявление
     * @param type $adv_id
     */
    public function AdvToOff($adv_id) {
        if (is_numeric($adv_id)) {
			$this->adv_values->Delete("`adv_id` = {$adv_id}");
            $query = "
                DELETE FROM `{$this->adv_off->getdatabasename()}`.`{$this->adv_off->gettablename()}`
                    WHERE
                        `id`='{$adv_id}' LIMIT 1;
                ";
            $this->db()->query($query);
            $query = "
                INSERT INTO
                    `{$this->adv_off->getdatabasename()}`.`{$this->adv_off->gettablename()}`
                        SELECT * FROM `{$this->getdatabasename()}`.`{$this->gettablename()}` WHERE `id`='{$adv_id}' LIMIT 1;
                ";
            $this->db()->query($query);
            $query = "
                DELETE FROM
                    `{$this->getdatabasename()}`.`{$this->gettablename()}` WHERE `id`='{$adv_id}' LIMIT 1;
                ";
            $this->db()->query($query);
			return true;
        }
		else return false;
    }
	
    public function AdvToUp($adv_id, $up_max_time=0, $force=false) {
        $can_up = true;
        $last_up = $this->up()->getItemWhere("`adv_id`='{$adv_id}'");
        if (is_array($last_up)) {
            if (($last_up['utx']+$up_max_time)>time()) {
                $can_up = false;
            }
        }
        if ($can_up or $force) {
            $adv_up = array(
                'id'            => $adv_id,
                'up_time'       => time(),
                'up_time_send'  => time()
            );
            $this->Update($adv_up,$adv_up['id']);
            $up_data = array(
                'adv_id'    => $adv_id,
                'utx'       => time()
            );
            $this->up()->InsertUpdate($up_data);
			/*
            // GAZETA 
            $has_text = ($last_up['adv_text']) ? true : false;
            $adv_nums = $this->_gazeta_model->getAdvNums($adv_id);
            $adv_nums_publ = $this->_gazeta_model->getAdvNums($adv_id,true);
            if (count($adv_nums)+count($adv_nums_publ)) {
                // Есть новера - берем ближайшие 
                $active_gazetas = $this->_gazeta_model->getGazetas(GAZETA_USER_MAX_NUMBERS);
                if (count($active_gazetas)) {
                    $add_nums = array();
                    foreach ($active_gazetas as $k=>&$gaz_data) {
                        if (count($gaz_data['nums'])) {
                            foreach ($gaz_data['nums'] as $kk=>&$num_data) {
                                $add_nums[] = array(
                                    'id'    => $num_data['id'],
                                    'pid'   => $num_data['pid']
                                );
                            }
                        }
                    }
                    if (count($add_nums)) {
                        $this->_gazeta_model->addAdv($adv_id,$add_nums, false);
                    }
                }
            }
			*/
            return true;
        } else {
            return false;
        }
    }
	
    /**
     * Помечает объявление как удаленное
     * @param type $adv_id
     * @param type $where
     */
    public function AdvDelete($adv_id,$where) {
		$this->adv_values->Delete("`adv_id` = {$adv_id}");
        $query = "UPDATE `{$this->getdatabasename()}`.`{$this->gettablename()}` SET `on_off`='0' WHERE `id`='{$adv_id}' LIMIT 1;";
        $this->db()->query($query);
        $query = "UPDATE `{$this->adv_off->getdatabasename()}`.`{$this->adv_off->gettablename()}` SET `on_off`='0' WHERE `id`='{$adv_id}' LIMIT 1;";
        $this->db()->query($query);
        return true;
        switch ($where) {
            case 'on':
                $query = "UPDATE `{$this->getdatabasename()}`.`{$this->gettablename()}` SET `on_off`='0' WHERE `id`='{$adv_id}' LIMIT 1;";
                $this->db()->query($query);
                return true;
                break;
            case 'off':
                $query = "UPDATE `{$this->adv_off->getdatabasename()}`.`{$this->adv_off->gettablename()}` SET `on_off`='0' WHERE `id`='{$adv_id}' LIMIT 1;";
                $this->db()->query($query);
                return true;
                break;
        }
    }
   
	public function AdvToPay($adv_id){
        $this->Update(['id'=>$adv_id, 'on_off'=>'4'], $adv_id);
	}
	
	public function getRubrics($city_id=null){
		$result = $this->mainC->getItemsWhere("`on_off`='1'", 'pos', null, null, 'id, name');
		foreach($result as $i => $item){
			$result[$i]['items'] = $this->subC->getItemsWhere("`on_off`='1' AND `pid`='{$item['id']}'", 'pos', null, null, 'id, name');
		}
		return $result;
	}
	
	public function getFilters($city_id, $main_catid = null, $sub_catid= null){
		$filters = [];
		if(!empty($main_catid) or !empty($sub_catid)){
			if(!empty($sub_catid)){
				$results = $this->db()->getAll("SELECT adv_options.* FROM `new_feo_ua`.`adv_option_rels`, `new_feo_ua`.`adv_options` WHERE adv_options.id=adv_option_rels.opt_id AND `cat_id` = {$sub_catid} AND `in_filter` = '1' GROUP BY adv_options.id ORDER BY `pos`");
			}
			else {
				$results = $this->db()->getAll("SELECT adv_options.* FROM `new_feo_ua`.`adv_option_rels`, `new_feo_ua`.`adv_options` WHERE adv_options.id=adv_option_rels.opt_id AND `cat_id` IN (SELECT `id` FROM `new_feo_ua`.`adv_sub_category` WHERE pid={$main_catid}) AND `in_filter` = '1' GROUP BY adv_options.id ORDER BY `pos`");
			}
		}
		$filters[] = [
			'id' =>  0,
			'name' =>  'order',
			'title' => 'Сортировка',
			'type' =>  'list',
			'type_descr' =>  'Выпадающий список',
			'items' =>  [["id"=>"1","name"=>"По дате ▼"], [ "id"=>"2", "name"=>"По дате ▲"], [ "id"=>"3", "name"=>"По цене ▼"], [ "id"=>"4", "name"=>"По цене ▲"] ],
		];
		
		foreach($results as $i=>$item){
			switch($item['filter_type']){
				case 0 : 
					$type="list"; $descr="Выпадающий список"; 
					$field = 'items';
					$items = $this->values->getItemsWhere("`on_off`='1' AND `pid`={$item['id']}", "id", null, null, "id, value as name");
					break;
				case 1 : 
					$type="check"; $descr="Несколько значений (Галочка)"; 
					$field = 'items';
					$items = $this->values->getItemsWhere("`on_off`='1' AND `pid`={$item['id']}", "id", null, null, "id, value as name");
					break;
				case 2 : 
					$type="range"; $descr="Диапазон значений";
					$min = $this->adv_values->db()->getOne("SELECT MIN(num_val) FROM `new_feo_ua`.`adv_adv_options_values` WHERE `opt_id`={$item['id']}");
					$max = $this->adv_values->db()->getOne("SELECT MAX(num_val) FROM `new_feo_ua`.`adv_adv_options_values` WHERE `opt_id`={$item['id']}");
					$field = 'range';
					$items = [
						'min' => $min,
						'max' => $max,
					];
					break;
			}
			$filters[] = [
				'id' =>  $item['id'],
				'name' =>  $type.$item['id'],
				'title' =>  $item['name'],
				'type' =>  $type,
				'type_descr' =>  $descr,
				$field =>  $items,
			];
		}
		
		$filters[] = [
			'id' =>  0,
			'name' =>  'price',
			'title' => 'Цена',
			'type' =>  'range',
			'type_descr' =>  'Диапазон значений',
			'items' =>  [],
		];
		
		
		$filters[] = [
			'id' =>  0,
			'name' =>  'photo',
			'title' => 'С фото',
			'type' =>  'switch',
			'type_descr' =>  'Выключатель',
		];
		
		$filters[] = [
			'id' =>  0,
			'name' =>  'caption',
			'title' => 'Только в названиях',
			'type' =>  'switch',
			'type_descr' =>  'Выключатель',
		];
		
		return $filters;
	}
	
	public function getList($city_id=null, $user_id=null, $main_catid=null, $sub_catid=null, $start=0, $limit=20, $filters = null){
		$wq = '';
		if (!empty($main_catid)) { $wq .= " AND `main_catid` = '{$main_catid}'";}
		if (!empty($sub_catid)) { $wq .= " AND `sub_catid` = '{$sub_catid}'";}
		
		$order='up_time desc';
		if(!empty($filters['filters'])){
			foreach($filters['filters'] as $filter){
				if($filter['name']=='photo'){
					if($filter['value']=='1'){
						$wq.= " AND (`json_photos`!='{\"main\":false,\"all\":[]}')";
					}
				}
				elseif($filter['name']=='caption'){
					if(!empty($filters['search'])){
						$search = trim(addslashes($filters['search']));
						if($filter['value']=='1'){
							$wq.= " AND ( `caption` LIKE '%{$search}%' )";
						}
						else {
							$wq.= " AND (`caption` LIKE '%{$search}%' OR `adv_text` LIKE '%{$search}%' OR `descr` LIKE '%{$search}%')";
						}
					}
				}
				elseif($filter['name']=='order'){
					$type = $filter['items'][0];
					switch($type){
						case 1: $order='up_time desc'; break;
						case 2: $order='up_time'; break;
						case 3: $order='price desc'; break;
						case 4: $order='price'; break;
					}
				}				
				elseif($filter['name']=='price'){
					if(isset($filter['from'])){
						$wq .= " AND `adv_adventures`.`price`>={$filter['from']}";
					}
					if(isset($filter['to'])){
						$wq .= " AND `adv_adventures`.`price`<={$filter['to']}";
					}
				}
				else {
				switch($filter['type']){
					case 'list' :
					case 'check' : 
						$wq.=" AND (SELECT COUNT(*) FROM `new_feo_ua`.`adv_adv_options_values` as `vals` WHERE `vals`.`adv_id`=`adv_adventures`.`id` AND `vals`.`opt_id`={$filter['id']} AND `vals`.`val_id` IN (".implode(', ', $filter['items'])."))>0";
						break;
					case 'range' : 
						if(isset($filter['from']) or isset($filter['to'])){
							$from_to = '';
							if(isset($filter['from'])){
								$from_to .= " AND `vals`.`num_val`>={$filter['from']}";
							}
							if(isset($filter['to'])){
								$from_to .= " AND `vals`.`num_val`<={$filter['to']}";
							}
							$wq.=" AND (SELECT COUNT(*) FROM `new_feo_ua`.`adv_adv_options_values` as `vals` WHERE `vals`.`adv_id`=`adv_adventures`.`id` AND `vals`.`opt_id`={$filter['id']} {$from_to})>0";
						}
						break;
				}
				}
			}
		}
		
		$result = $this->getItemsWhere("`on_off`='1'".$wq, $order, $start, $limit, 'id, caption as name, price, city, json_photos, add_time as date, vip');
		
		foreach($result as $i => $item){
			$result[$i]['date'] = date("Y-m-d H:i:s", $result[$i]['date']);
			$photos = json_decode($item['json_photos'], true);
			if(!empty($photos['main'])){
				$result[$i]['photo'] = "http://xn--e1asq.xn--p1ai/app_adv/engine/upload/photos/{$photos['main']['id']}_500_310.{$photos['main']['ext']}";
			}
			else $result[$i]['photo'] = "http://xn--e1asq.xn--p1ai/app_adv/engine/views/feo_obyavleniya_no_foto.png";
			unset($result[$i]['json_photos']);
		}
		return $result;
	}
	
	public function getFor(int $user_id, $start=0, $limit=20){
		$result = [];
		$result = $this->get('id, caption, price, city, json_photos, add_time as date, vip')->union([$this,$this->adv_off()])->where("`on_off`!='0' AND `user_id`='{$user_id}'")->offset($start)->limit($limit)->order('up_time desc')->commit();;
		foreach($result as $i => $item){
			$result[$i]['date'] = date("Y-m-d H:i:s", $result[$i]['date']);
			$photos = json_decode($item['json_photos'], true);
			if(!empty($photos['main'])){
				$result[$i]['photo'] = "http://xn--e1asq.xn--p1ai/app_adv/engine/upload/photos/{$photos['main']['id']}_500_310.{$photos['main']['ext']}";
			}
			else $result[$i]['photo'] = "http://xn--e1asq.xn--p1ai/app_adv/engine/views/feo_obyavleniya_no_foto.png";
			unset($result[$i]['json_photos']);
		}
		return $result;
	}
	
    public function findItem($adv_id) {
		$cols = "id, user_id, user_name, user_email, user_phone, main_catid, sub_catid, caption, descr, price, add_time as date, on_off as status, json_photos, json_options, vip, city, latitude, longitude";
        $adv_on = $this->getItemWhere("`id`='{$adv_id}'", $cols);
        if (is_array($adv_on)) {
            return $adv_on;
            
        } else {
            $adv_off = $this->adv_off->getItemWhere("`id`='{$adv_id}'", $cols);
            if (is_array($adv_off)) {
                return $adv_off;
            }
        }
    }
	
	public function getAdv($city_id, $adv_id){
		$adv = $this->findItem($adv_id);
		if(!empty($adv)){
			$adv['date'] = date("Y-m-d H:i:s", $adv['date']);
			$photos = json_decode($adv['json_photos'], true); unset($adv['json_photos']);
			$adv['photos'] = [];
			foreach($photos['all'] as $photo){
				$adv['photos'][] = [
					'id' => $photo['id'],
					'name' => $photo['name'],
					'thrumb' => "http://xn--e1asq.xn--p1ai/app_adv/engine/upload/photos/{$photo['id']}_500_310.{$photo['ext']}"
				];
			}
			
			$options = json_decode($adv['json_options'], true); unset($adv['json_options']);
			$adv['options'] = [];
			foreach($options as $option){
				$adv['options'][] = $option;
			}
			$model_gazeta = new model_gazeta();
			$adv['nums'] = $model_gazeta->advs()->getItemsWhere("`adv_id`={$adv_id}", 'num_id', null, null, "gazeta_id as id, num_id, 
			(SELECT `num` FROM `{$model_gazeta->nums()->getdatabasename()}`.`{$model_gazeta->nums()->gettablename()}` as `nums` WHERE `nums`.`pid`=`gazeta_id` AND `nums`.`id`=num_id) as `num`, 
			(SELECT `date` FROM `{$model_gazeta->nums()->getdatabasename()}`.`{$model_gazeta->nums()->gettablename()}` as `nums` WHERE `nums`.`pid`=`gazeta_id` AND `nums`.`id`=num_id) as `date`, 
			published");
			
		}
		return $adv;
	}
	
	public function insertImage($name, $adv_id){
		if(!empty($name)){
			$file_name = explode(".",$name);
			$file_ext = strtolower($file_name[count($file_name)-1]);
			$data = [
				'adv_id' => ($adv_id?$adv_id:0),
				'name' => $name,
				'ext' => $file_ext,
				'ismain' => 0,
			];
			$id = $this->photos->Insert($data);
			return [
				'id' => $id,
				'name' => $name,
				'thrumb' => "http://xn--e1asq.xn--p1ai/app_adv/engine/upload/photos/{$id}_500_310.jpeg"
			];
		}
	}
	
	public function getOptions($main_id, $sub_id){
		$result = $this->db()->getAll("
                    SELECT
                        `opt`.id,
                        `opt`.name,
                        `opt`.prefix,
                        `opt`.unit,
                        `opt`.is_req,
                        `opt`.data_type as `type`,
                        `opt`.own_value,
                        `opt`.show_optid,
                        `opt`.show_optval
                        FROM
                            `new_feo_ua`.`adv_options` AS `opt`
                        LEFT JOIN `new_feo_ua`.`adv_option_rels` AS `opt_rel` ON `opt_rel`.`opt_id`=`opt`.`id` AND `opt_rel`.`cat_id`='{$sub_id}'
                    WHERE
                        `opt_rel`.`id` IS NOT NULL
                            AND
                        `opt`.`on_off`='1'
                    ORDER BY
                        `opt_rel`.`pos` ASC
                    ");
		foreach($result as $i=>$item){
			$result[$i]['items'] = $this->values->getItemsWhere("`pid`='{$item['id']}' AND `on_off`='1'", 'pos', null, null, "id, value as name, show_optid, show_optval");
		}
		
		return $result;
	}
	
	public function getUnits($id=null){
		if(is_null($id)){
			$query = "SELECT * FROM main.edizm WHERE 1 AND `in_adv`=1";
			return $this->db()->getAll($query);
		}
		else {
			$query = "SELECT `name` FROM main.edizm WHERE 1 AND `id`='{$id}' AND `in_adv`=1";
			return $this->db()->getOne($query);
		}
	}
	
	public function addAdv($user_id, $data, $id = null){
		$result = [];
		$regions = [ 
			"261"=>"Крым" 
		];
		$city = [
			"1483"=>"Феодосия" ,
			"1500537"=>"Береговое",
			"1500545"=>"Приморский",
			"1500539"=>"Коктебель",
		];
		
		 $adv_data = array(
            'id'           		=> $id,
            'user_id'           => $user_id,
            'user_name'         => $data['name'],
            'user_email'        => $data['email'],
            'user_email_show'   => 1,
            'user_phone'        => $data['phone'],
            'user_ip'           => getIp(),
            'main_catid'        => $data['main_cat'],
            'sub_catid'         => $data['sub_cat'],
            'caption'           => $data['caption'],
            'adv_text'          => (!empty($data['gazeta-text'])?$data['gazeta-text']:''),
            'descr'             => $data['text'],
            'price'             => $data['price'],
            'price_from_to'     => (!empty($data['price-from-to'])?$data['price-from-to']:0),
            'price_ed_izm'      => (!empty($data['price-izm'])?$data['price-izm']:0),
            'price_valut'       => 'руб.',
            'price_discuse'     => (!empty($data['price-discuse'])?$data['price-discuse']:0),
            'price_free'        => (!empty($data['price-free'])?$data['price-free']:0),
            'url'               => $data['url'],
            'city'              => $city[$data['city']],
            'region'            => $regions[$data['region']],
            'city_id'           => $data['city'],
            'region_id'         => $data['region'],
            'video'             => $data['video'],
            'add_time'          => time(),
            'up_time'           => time(),
            'edit_time'         => time(),
            'up_time_send'      => time(),
            'json_options'      => json_encode($data['options'], JSON_UNESCAPED_UNICODE)/*,JSON_CONST)*/,
			'json_data'         => json_encode($data, JSON_UNESCAPED_UNICODE)/*,JSON_CONST)*/,
            'on_off'            => ($data['on_off']?$data['on_off']:'2'),
			'latitude'			=> (!empty($data['latitude'])?$data['latitude']:'NULL'),
			'longitude'			=> (!empty($data['longitude'])?$data['longitude']:'NULL'),
        );
		
		$adv_data['id'] = $this->InsertUpdate($adv_data);
		$up_data = array(
			'adv_id'    => $adv_data['id'],
			'utx'       => time()
		);
		$this->up()->InsertUpdate($up_data);
		
		$json_photos = ['main'  => false,'all'   => array()];
		if (count($data['photos'])) {
			foreach ($data['photos'] as $i=>$photo) {
				$photo_name = explode('.',$photo_data['name']);
				$photo_ext =strtolower($photo_name[count($photo_name)-1]);
				$ismain = ($i==0)?1:0;
				$new_photo = [ "ismain" => $ismain, "adv_id" => $adv_data['id'] ];
				$this->photos->Update($new_photo, $photo['id']);
				$new_photo['name'] = $photo['name'];
				$new_photo['ext'] = $photo_ext;
				
				$json_photos['all'][] = $new_photo;
				if ($ismain) { $json_photos['main'] = $new_photo; }
			}
		}
		else {
			$this->photos->Delete("`adv_id`='{$adv_data['id']}'");
		}
		$upd_photos = ['id' => $adv_data['id'], 'json_photos' => json_encode($json_photos, JSON_UNESCAPED_UNICODE)];
		$this->Update($upd_photos, $adv_data['id']);
		
		if($data['gazeta-nums']){
			$model_gazeta = new model_gazeta();
			$model_gazeta->addAdv($adv_data['id'], $data['gazeta-nums']);
		}
		
		$limits = $this->getLimits($adv_data['id'], $user_id);
		if($limits['portal']['answer']===false OR $limits['gazeta']['answer']===false){
			$this->Update(["on_off"=>'4'], $adv_data['id']);
			$result['message'] = 'Превышен лимит публикации бесплатных объявлений. Ваше объявление будет доступно после оплаты.';
		}
		else {
			$result['message'] = 'Успешно добавлено.';
		}
		
		$result['adv'] = $this->getAdv(null, $adv_data['id']);
		
		return $result;
	}
	
	public function updateAdv($user_id, $data, $id){
		$result = [];
		$regions = [ 
			"261"=>"Крым" 
		];
		$city = [
			"1483"=>"Феодосия" ,
			"1500537"=>"Береговое",
			"1500545"=>"Приморский",
			"1500539"=>"Коктебель",
		];
		
		 $adv_data = array(
            'user_id'           => $user_id,
            'user_name'         => $data['name'],
            'user_email'        => $data['email'],
            'user_email_show'   => 1,
            'user_phone'        => $data['phone'],
            'user_ip'           => getIp(),
            'main_catid'        => $data['main_cat'],
            'sub_catid'         => $data['sub_cat'],
            'caption'           => $data['caption'],
            'adv_text'          => (!empty($data['gazeta-text'])?$data['gazeta-text']:''),
            'descr'             => $data['text'],
            'price'             => $data['price'],
            'price_from_to'     => (!empty($data['price-from-to'])?$data['price-from-to']:0),
            'price_ed_izm'      => (!empty($data['price-izm'])?$data['price-izm']:0),
            'price_valut'       => 'руб.',
            'price_discuse'     => (!empty($data['price-discuse'])?$data['price-discuse']:0),
            'price_free'        => (!empty($data['price-free'])?$data['price-free']:0),
            'url'               => $data['url'],
            'city'              => $city[$data['city']],
            'region'            => $regions[$data['region']],
            'city_id'           => $data['city'],
            'region_id'         => $data['region'],
            'video'             => $data['video'],
            'edit_time'         => time(),
            'json_options'      => json_encode($data['options'], JSON_UNESCAPED_UNICODE)/*,JSON_CONST)*/,
			'json_data'         => json_encode($data, JSON_UNESCAPED_UNICODE)/*,JSON_CONST)*/,
            'on_off'            => ($data['on_off']?$data['on_off']:'2'),
			'latitude'			=> (!empty($data['latitude'])?$data['latitude']:'NULL'),
			'longitude'			=> (!empty($data['longitude'])?$data['longitude']:'NULL'),
        );
		$adv_data['id'] = $id;
		$this->Update($adv_data, $id);
		$this->ParseNewOption($data['options'], $id, $user_id);
		$json_photos = ['main'  => false,'all'   => array()];
		if (count($data['photos'])) {
			foreach ($data['photos'] as $i=>$photo) {
				$photo_name = explode('.',$photo['name']);
				$photo_ext =strtolower($photo_name[count($photo_name)-1]);
				$ismain = ($i==0)?1:0;
				$new_photo = [ "id" => $photo['id'], "ismain" => $ismain, "adv_id" => $id ];
				$this->photos->Update($new_photo, $photo['id']);
				$new_photo['name'] = $photo['name'];
				$new_photo['ext'] = $photo_ext;
				
				$json_photos['all'][] = $new_photo;
				if ($ismain) { $json_photos['main'] = $new_photo; }
			}
		}
		else {
			$this->photos->Delete("`adv_id`='{$adv_data['id']}'");
		}		
		
		$upd_photos = ['id' => $id, 'json_photos' => json_encode($json_photos, JSON_UNESCAPED_UNICODE)];
		$this->Update($upd_photos, $id);
		
		if($data['gazeta-nums']){
			$model_gazeta = new model_gazeta();
			$model_gazeta->addAdv($id, $data['gazeta-nums']);
		}
		
		$limits = $this->getLimits($adv_data['id'], $user_id);
		if($limits['portal']['answer']===false OR $limits['gazeta']['answer']===false){
			$this->Update(["on_off"=>'4'], $adv_data['id']);
			$result['message'] = 'Превышен лимит публикации бесплатных объявлений. Ваше объявление будет доступно после оплаты.';
		}
		else {
			$result['message'] = 'Успешно обновлено.';
		}
		
		$result['adv'] = $this->getAdv(null, $id);
		
		return $result;
	}
	
	public function ParseNewOption($options = null, $adv_id, $user_id=null){
		set_time_limit(5);
		$adv_item = $this->getItemWhere("`id`='{$adv_id}'");
		if(is_null($user_id)) $user_id = $adv_item['user_id'];
		if(empty($options)){
			$options = [];
			$options = json_decode($adv_item['json_options'], true);
		}
		$this->adv_values->Delete("`adv_id` = {$adv_id}");
		foreach($options as $i=>$option){
			$text_val = trim($option['value']);
			$num_val = (is_numeric($text_val)?$text_val:NULL);
			
			switch($option['type']){
				/*
				 * 0 - Выпадалка с Текстовым содержанием
				 * 1 - Выпадалка с Числовым содержанием
				 */
				case 0:
				case 1:{
					if(empty($option['value_id']) or !is_numeric($option['value_id'])){
						$val = $this->values->getItemWhere("LOWER(`value`)=LOWER('{$text_val}') AND `pid`='{$option['id']}'");
						if(!empty($val)){ $option['value_id']=$val['id']; } else { unset($option['value_id']); }
					}
					if(empty($option['value_id'])){
						$query_vid = "SELECT MAX(`vid`) AS `vid` FROM `{$this->values->getdatabasename()}`.`{$this->values->gettablename()}` WHERE `pid`='{$option['id']}'";
						$max_vid = $this->values->db()->getRow($query_vid);
						$value=[
								'vid'=>($max_vid['vid'] + 1),
								'pid'=>$option['id'],
								'uid'=>$user_id,
								'value'=>$text_val,
								'numeric_value'=>$num_val,
								'on_off'=>'2',
								'ip'=>$_SERVER['REMOTE_ADDR'],
								'agent'=>$_SERVER['HTTP_USER_AGENT'],
							];
						$option['value_id'] = $this->values->Insert($value);
					}
					
					if(!empty($option['id'])){
						$option['value_id'] = $value_id;
						if(!empty($text_val)){
						$data=[ 'adv_id'=>$adv_id, 'opt_id'=>$option['id'], 'val_id'=>$option['value_id'], 'text_val'=>$text_val, 'num_val'=>$num_val ];
						$this->adv_values->Insert($data);
						}
					}
					break;
				}
				/*
				 * 2 - Текстовое поле
				 * 3 - Числовое поле
				 */
				case 2:
				case 3:{
					if(!empty($option['id'])){
						$option['value_id'] = $value_id;
						if(!empty($text_val)){
						$data=[ 'adv_id'=>$adv_id, 'opt_id'=>$option['id'], 'val_id'=>null, 'text_val'=>$text_val, 'num_val'=>$num_val ];
						$this->adv_values->Insert($data);
						}
					}
					break;
				}
				/*
				 * 4 - Множественный выбор (checkbox галочки)
				 */
				case 4: {
					foreach($option['value'] as $key=>$val){
						$id = $val['id']; 
						$text_val = trim($val['label']); 
						$num_val = (is_numeric($text_val)?$text_val:NULL);
						if(!empty($option['id']) AND !empty($id)){
							$data=[ 'adv_id'=>$adv_id, 'opt_id'=>$option['id'], 'val_id'=>$id, 'text_val'=>$text_val, 'num_val'=>$num_val ];
							$this->adv_values->Insert($data);
						}
					}
				}
				/*
				 * 5 - Карта
				 */
				case 5: { 
					$address = $option['value']['address']; 
					$longitude = number_format($option['value']['longitude'], 4, '.', ''); 
					$latitude = number_format($option['value']['latitude'], 4, '.', ''); 
					$val = $this->values->getItemWhere("LOWER(`value`)=LOWER('address') AND `pid`='{$option['id']}'");
					if(!empty($val)){ $val_id=$val['id']; } else{ unset($val_id); }
					if(empty($val_id)){
						$query_vid = "SELECT MAX(`vid`) AS `vid` FROM `{$this->values->getdatabasename()}`.`{$this->values->gettablename()}` WHERE `pid`='{$option['id']}'";
						$max_vid = $this->values->db()->getRow($query_vid);
						$value=[
								'vid'=>($max_vid['vid'] + 1),
								'pid'=>$option['id'],
								'uid'=>$user_id,
								'value'=>'address',
								'on_off'=>'2',
								'ip'=>$_SERVER['REMOTE_ADDR'],
								'agent'=>$_SERVER['HTTP_USER_AGENT'],
							];
						$val_id = $this->values->Insert($value);
					}
					if(!empty($option['id'])){
						$data = [ 'adv_id'=>$adv_id, 'opt_id'=>$option['id'], 'val_id'=>$val_id, 'text_val'=>$address ];
						$this->adv_values->Insert($data);
					}
					
					
					$val = $this->values->getItemWhere("LOWER(`value`)=LOWER('longitude') AND `pid`='{$option['id']}'");
					if(!empty($val)){ $val_id=$val['id']; } else{ unset($val_id); }
					if(empty($val_id)){
						$query_vid = "SELECT MAX(`vid`) AS `vid` FROM `{$this->values->getdatabasename()}`.`{$this->values->gettablename()}` WHERE `pid`='{$option['id']}'";
						$max_vid = $this->values->db()->getRow($query_vid);
						$value=[
								'vid'=>($max_vid['vid'] + 1),
								'pid'=>$option['id'],
								'uid'=>$user_id,
								'value'=>'longitude',
								'on_off'=>'2',
								'ip'=>$_SERVER['REMOTE_ADDR'],
								'agent'=>$_SERVER['HTTP_USER_AGENT'],
							];
						$val_id = $this->values->Insert($value);
					}
					if(!empty($option['id'])){
						$data = [ 'adv_id'=>$adv_id, 'opt_id'=>$option['id'], 'val_id'=>$val_id, 'text_val'=>$longitude, 'num_val'=>$longitude ];
						$this->adv_values->Insert($data);
					}
					
					$val = $this->values->getItemWhere("LOWER(`value`)=LOWER('latitude') AND `pid`='{$option['id']}'");
					if(!empty($val)){ $val_id=$val['id']; } else{ unset($val_id); }
					if(empty($val_id)){
						$query_vid = "SELECT MAX(`vid`) AS `vid` FROM `{$this->values->getdatabasename()}`.`{$this->values->gettablename()}` WHERE `pid`='{$option['id']}'";
						$max_vid = $this->values->db()->getRow($query_vid);
						$value=[
								'vid'=>($max_vid['vid'] + 1),
								'pid'=>$option['id'],
								'uid'=>$user_id,
								'value'=>'latitude',
								'on_off'=>'2',
								'ip'=>$_SERVER['REMOTE_ADDR'],
								'agent'=>$_SERVER['HTTP_USER_AGENT'],
							];
						$val_id = $this->values->Insert($value);
					}
					if(!empty($option['id'])){
						$data = [ 'adv_id'=>$adv_id, 'opt_id'=>$option['id'], 'val_id'=>$val_id, 'text_val'=>$latitude, 'num_val'=>$latitude ];
						$this->adv_values->Insert($data);
					}
				}
				/*
				 * 6 - Выбор даты
				 * 7 - Выбор диапазона дат
				 * 8 - Выбор нескольких дат
				 */
				case 6: {
						if(!empty($option['id'])){
							$option['value_id'] = $value_id;
							$data=[ 'adv_id'=>$adv_id, 'opt_id'=>$option['id'], 'val_id'=>null, 'text_val'=>$text_val, 'num_val'=>null ];
							$this->adv_values->Insert($data);
						}
						break;
					}
				case 7: {
					$range = explode('.',$text_val);
					if(!empty($option['id'])){
						$option['value_id'] = $value_id;
						$data=[ 'adv_id'=>$adv_id, 'opt_id'=>$option['id'], 'val_id'=>null, 'text_val'=>$range[0], 'num_val'=>null ];
						$this->adv_values->Insert($data);
						$data=[ 'adv_id'=>$adv_id, 'opt_id'=>$option['id'], 'val_id'=>null, 'text_val'=>$range[1], 'num_val'=>null ];
						$this->adv_values->Insert($data);
					}
				}
				case 8: {
					$range = explode(',',$text_val);
					foreach($range as $date){
						$option['value_id'] = $value_id;
						$data=[ 'adv_id'=>$adv_id, 'opt_id'=>$option['id'], 'val_id'=>null, 'text_val'=>$date, 'num_val'=>null ];
						$this->adv_values->Insert($data);
					}
					break;
				}
			}
			$options[$i] = $option;
		}
		$new_options = [];
		foreach($options as $option){
			$new_options[$option['id']] = $option;
		}
		
		$data = array( "json_options"=> json_encode($new_options, JSON_UNESCAPED_UNICODE) );
		$adv_item = $this->Update( $data, $adv_id );
	}
	
	public function deleteRule($adv_id){
		if(!empty($adv_id) and is_numeric($adv_id)){
			$this->autoup()->Delete("`adv_id`='{$adv_id}'");
		}
	}
	
    public function setRules($adv_id,$start_date,$days_count,$need_count,$is_enabled) {
        $this->deleteRule($adv_id);
		$new_row = array(
            'adv_id'    => $adv_id,
            'days_count'    => $days_count,
            'need_count'    => $need_count,
            'upok_count'    => '0',
            'start_time'    => strtotime($start_date." ".date("H:i:s")),
            'last_up'       => '0',
            'enabled'       => $is_enabled
        );
        $this->autoup()->Insert($new_row);
    }
	
	public function getActiveForPhone($advid=null, $uid=null, $phone=null, $main_catid=null, $sub_catid=null, $main_limit=0, $sub_limit=0, $main_limit_g=0, $sub_limit_g=0, $nums){
		if(!empty($advid)){
			$adv = $this->findItem($advid, $uid);
			if($adv['item']['vip']=='1' or $adv['item']['simple_plus']=='1' or $adv['item']['gaz_frame']=='1'){
				$result = ['portal'=>0, 'gazeta'=>0];
				return $result;
			}
		}
		if(!empty($phone)){
			$result = [];
			$w=((!empty($uid)))?"`adv`.`user_id`='{$uid}'":"`adv`.`user_phone` LIKE '%{$phone}%'";
			$query="SELECT COUNT(*) as `N` FROM `{$this->getdatabasename()}`.`{$this->gettablename()}` AS `adv` WHERE {$w} AND `adv`.`on_off` IN('1', '2') AND (`vip`='0' AND `simple_plus`='0')
						AND `adv`.`main_catid`='{$main_catid}'";
					
			if($sub_limit>0){
				$query.=" AND `adv`.`sub_catid`='{$sub_catid}'";
			}
			$result['query'] = $query;
			$result['portal'] = $this->db()->getOne($query);
			
			if(!empty($nums)){
				$result['gazeta'] = [];
				foreach($nums as $num){
				$query="SELECT COUNT(*) as `N` FROM `{$this->getdatabasename()}`.`adv_gazeta_adv` as `gaz`, `{$this->getdatabasename()}`.`{$this->gettablename()}` AS `adv` 
				WHERE `gaz`.`adv_id`=`adv`.`id` AND `gaz`.`num_id` = '{$num['num_id']}' AND `gaz`.`gazeta_id` = '{$num['id']}' AND {$w} AND `adv`.`on_off` IN('1', '2') AND (`gaz_frame`='0' AND `simple_plus`='0')
							AND `adv`.`main_catid`='{$main_catid}'";
				if($sub_limit_g>0){
					$query.=" AND `adv`.`sub_catid`='{$sub_catid}'";
				}
				$result['gazeta'][$num['num_id']] = $this->db()->getOne($query);
				}
			}
			return $result;
		}
	}
	
	public function getLimits($advid=null, $uid=null){
		$adv = $this->findItem($advid);
		if(!empty($adv)){
		if(is_null($uid) and !empty($adv)) { $uid = $adv['user_id']; }
		$model_gazeta = new model_gazeta();
		$nums = $model_gazeta->getAdvNums($advid);
		
		$phone = $adv['user_phone'];
		$main_catid = $adv['main_catid'];
		$sub_catid = $adv['sub_catid'];
		$main_cat = $this->mainC()->getItemWhere("`id`='{$main_catid}' AND `on_off`='1'");
		if (is_array($main_cat)) {
			$sub_cat = $this->subC()->getItemWhere("`id`='{$sub_catid}' AND `pid`='{$main_catid}' AND `can_select`='1' AND `on_off`='1'");
			$main_limit 	= $main_cat['max_free'];
			$main_limit_g 	= $main_cat['max_free_g'];
			$sub_limit 		= $sub_cat['max_free'];
			$sub_limit_g 	= $sub_cat['max_free_g'];
			
			if($main_limit==0 and $sub_limit==0 and $main_limit_g==0 and $sub_limit_g==0){return true;}
			else {
				$advs = $this->getActiveForPhone($advid, $uid, $phone, $main_catid, $sub_catid, $main_limit, $sub_limit, $main_limit_g, $sub_limit_g, $nums);
				$result = [ "main_limit" => $main_limit, "sub_limit" => $sub_limit, "main_limit_g" => $main_limit_g, "sub_limit_g" => $sub_limit_g, "nums"=> $nums];
				$result['portal'] = [];
				$result['portal']['answer'] = true;
				$result['portal']['advs'] = $advs['portal'];
				
				if($main_limit!=0){
					if(!$advid and $advs['portal']>=$main_limit){
						$result['portal']['answer'] = false;
					}
					elseif($advid and $advs['portal']>$main_limit){
						$result['portal']['answer'] = false;
					}
				}
				
				if($sub_limit!=0){
					if(!$advid and $advs['portal']>=$sub_limit){
						$result['portal']['answer'] = false;
					}
					elseif($advid and $advs['portal']>$sub_limit){
						$result['portal']['answer'] = false;
					}
				}
				
				if(!empty($nums)){
					$result['gazeta']=[];
					$result['gazeta']['answer']=true;
					$result['gazeta']['advs']=0;
					$result['gazeta']['nums']=[];
					
					foreach($nums as $item){
						$gazeta = $item['id'];
						$num = $item['num_id'];
						$result['gazeta']['nums'][$num] = [];
						$result['gazeta']['nums'][$num]['answer'] = true;
						$result['gazeta']['nums'][$num]['advs'] = $advs['gazeta'][$num];
						$result['gazeta']['advs'] = $result['gazeta']['advs'] + $advs['gazeta'][$num];
						
						if($main_limit_g!=0){
							if(!$advid and $advs['gazeta'][$num]>=$main_limit_g){
								$result['gazeta']['nums'][$num]['answer'] = false;
								$result['gazeta']['answer'] = false;
							}
							elseif($advid and $advs['gazeta'][$num]>$main_limit_g){
								$result['gazeta']['nums'][$num]['answer'] = false;
								$result['gazeta']['answer'] = false;
							}
						}
						
						if($sub_limit_g!=0){
							if(!$advid and $advs['gazeta'][$num]>=$sub_limit_g){
								$result['gazeta']['nums'][$num]['answer'] = false;
								$result['gazeta']['answer'] = false;
							}
							elseif($advid and $advs['gazeta'][$num]>$sub_limit_g){
								$result['gazeta']['nums'][$num]['answer'] = false;
								$result['gazeta']['answer'] = false;
							}
						}
					}					
				}
				return $result;
			}
		}
		else{
			return false;
		}
		} else return false;
	}
	
	public function claimRules() {
		$result = [
			["id"=>1, "name"=>"Недостоверная информация"],
			["id"=>2, "name"=>"Мошенничество"],
		];
		return $result;
	}
	
    public function claimExist($uid, $adv_id) {
		$count = $this->claim()->getItemsWhere("`uid`='{$uid}' AND `adv_id`='{$adv_id}'");
        return (count($count)>0) ? true : false;
    }
	
    public function claimAdv($uid, $adv_id, $claim_id) {
		$claims = $this->claimRules();
		foreach($claims as $c){
			if($c['id']==$claim_id){ $text = $c['name']; }
		}
        if (!$this->claimExist($uid, $adv_id)) {
            $new_claim = array(
                'uid'       => $uid,
                'adv_id'    => $adv_id,
                'message'   => $text,
                'utx'       => time()
            );
            $this->claim()->Insert($new_claim);
            return array(
                'success'   => 'ok'
            );
        } else {
            return ["error"=>1, "message"=>"Already claimed!"];
		}
    }
	
	
	
}
?>