<?php
function createModelFile($class){
	$table_name = $GLOBALS['CONFIG']['DB']['table_prefix'].substr($class,6,strlen($class));
	$table = null; $columns = null; $primary_key = null; $c = null; $i = null;$u = null;$f = null;
	$table = checkTableExist($table_name);
	if($table){
		$columns = checkTableColumns($table_name);
		$indexes = checkTableIndexes($table_name);
	
	
	foreach($columns as $column){
		if($column['Key']=='PRI'){
			$primary_key = $column['Field'];
		}
		else {
			$set = "{$column['Type']}".($column['Null']=='NO'?" NOT NULL":" NULL").(!is_null($column['Default'])?" DEFAULT '{$column['Default']}'":"");
			$c .= "'{$column['Field']}' => \"{$set}\",\r\t\t\t\t";
		}
	}
	$_indexes = []; $u_indexes = []; $f_indexes = []; 
	foreach($indexes as $index){
		if($index['Key_name']!='PRIMARY'){
		if($index['Index_type']=='BTREE'){
		if($index['Non_unique']==1){
			if(!$_indexes[$index['Key_name']]) $_indexes[$index['Key_name']] = [];
			$_indexes[$index['Key_name']][]=$index['Column_name'];
		}
		else {
			if(!$u_indexes[$index['Key_name']]) $u_indexes[$index['Key_name']] = [];
			$u_indexes[$index['Key_name']][]=$index['Column_name'];
		}
		}
		elseif($index['Index_type']=='FULLTEXT') {
			if(!$f_indexes[$index['Key_name']]) $f_indexes[$index['Key_name']] = [];
			$f_indexes[$index['Key_name']][]=$index['Column_name'];
		}
		}
	}
	foreach($_indexes as $indexName => $columns){
		if(!empty($columns)){
		$i.="'{$indexName}' => array( '".implode("', '", $columns)."' ),\r\t\t\t\t";
		}
	}
	foreach($u_indexes as $indexName => $columns){
		if(!empty($columns)){
		$u.="'{$indexName}' => array( '".implode("', '", $columns)."' ),\r\t\t\t\t";
		}
	}
	foreach($f_indexes as $indexName => $columns){
		if(!empty($columns)){
		$f.="'{$indexName}' => array( '".implode("', '", $columns)."' ),\r\t\t\t\t";
		}
	}
	$Comment = trim($table['Comment']);
	if(substr($Comment, 0, 4)=='rev{' and substr($Comment, -1)=='}'){
		$version = substr($Comment, 4, -1);
	}
	else {
		$version = 1;
	}
	$new_model_text = '<?php
/* Automatic model generated
 * ver 0.1
 * model for site: '.$GLOBALS['CONFIG']['SERVER_NAME'].'
 * date create: '.date("Y-m-d H:i:s").'
*/
class '.$class.' extends Model
{
	function __construct($config = array()) {
		$config = [
            "database" => "'.$GLOBALS['CONFIG']['DB']['db'].'",
            "prefix" => "'.$GLOBALS['CONFIG']['DB']['table_prefix'].'",
            "name" => "'.substr($table['Name'], strlen($GLOBALS['CONFIG']['DB']['table_prefix']),strlen($table['Name'])).'",
            "engine" => "'.$table['Engine'].'",
            "version" => "'.$version.'",
            "row_format" => "'.$table['Row_format'].'",
            "create_time" => "'.$table['Create_time'].'",
            "collation" => "'.$table['Collation'].'",
            "primary_key" => "'.$primary_key.'",
			"autoinit"  => false,
            "columns" => array(
				'.$c.'
				),
			"index" => array(
				'.$i.'
			),
			"unique" => array(
				'.$u.'
			),
			"fulltext" => array(
				'.$f.'
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
						"<NEW COLUMN NAME>"   => "VARCHAR(50) NOT NULL DEFAULT \'\' AFTER `<AFTER COLUMN>`"
					),
					"del_columns" => array(
						"<COLUMN NAME1>", "<COLUMN NAME2>"
					),
					"mod_columns" => array(
						"<COLUMN NAME1>" => array( "name"=>"<NEW COLUMN NAME1>", "type"=>"VARCHAR(50) NOT NULL DEFAULT \'\' AFTER `<AFTER COLUMN>`" ),
						"<COLUMN NAME2>" => array( "name"=>"<NEW COLUMN NAME2>", "type"=>"VARCHAR(50) NOT NULL DEFAULT \'\' AFTER `<AFTER COLUMN>`" ),
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
		parent::__construct($config);
    }
}
?>';
	file_put_contents(APPDIR .'/application/models/' . $class . '.php', $new_model_text);
	return true;
	}
	else {
		return false;
	}
}

// автоматическая загрузка моделей
spl_autoload_register(function ($class) {
	if(substr($class,0,5)=='model'){
	if(file_exists(APPDIR .'/application/models/' . $class . '.php')){
		include  APPDIR .'/application/models/' . $class . '.php';
	}
	else {
		if(createModelFile($class)){
			include  APPDIR .'/application/models/' . $class . '.php';
		}
		else {
			echo 'Model "'.$class.'" not found and table "'.$table_name.'" does not exist!';
		}
	}
	}
});

class Model
{
	protected $_server;
	protected $_db;
	protected $_is_debug_query = false, $_is_debug_query_once = false;
	protected $_register_query = false, $_register_query_once = false;
	protected $_database = '';
	protected $_name='';
	protected $_ct_name='';
	protected $_ct_cells = array();                 /* Столбцы таблицы и их типы данных (без id) */
	protected $_ct_primary_key  = 'id';             /* Название первичного ключа */
    protected $_ct_primary_key_type = 'INT NOT NULL AUTO_INCREMENT ';             /* Тип первичного ключа */
    protected $_ct_indexes = array();
	protected $_ct_uniques = array();
	protected $_ct_fulltext = array();
	protected $_ct_initFunc = null;
	protected $_ct_engine = 'MyISAM';
	protected $_ct_collation = 'utf8_general_ci';
	protected $_ct_exists = false;
	protected $_db_prefix = '';
    protected $_ct_revisions = array();
    protected $_ct_version = '';
    protected $_ct_version_create = '';
    protected $_ct_initdata = array();
    protected $_sql_trys = 1;
	
    protected $_auto_register = false;
    protected $_register_table = null;
    protected $_register_model = null;
    protected $_last_register_id = null;
    
	private $_sql = null;
	private $_sql_cols = null;
	private $_sql_from = null;
	private $_sql_union = null;
	private $_sql_where = null;
	private $_sql_order = null;
	private $_sql_offset = null;
	private $_sql_limit = null;
	 
	function __construct($config = null){
        if ($GLOBALS['CONFIG']['DB']['_server']) {
			if(is_object($GLOBALS['DB']['localhost'])){
				$this->_server = $GLOBALS['CONFIG']['DB']['server'];
				$this->_db = $GLOBALS['DB'][$this->_server];
			} else  die("Подключение к серверу {$this->_server} отсутствует");
        }
		else {
			if(is_object($GLOBALS['DB']['localhost'])){
			$this->_db = $GLOBALS['DB']['localhost'];
			} else  die("Подключение к серверу localhost отсутствует");
		}
        if ($GLOBALS['CONFIG']['DB']['table_prefix']) {
            $this->_db_prefix = $GLOBALS['CONFIG']['DB']['table_prefix'];
        }
        if (isset($config['prefix'])) {
            $this->_db_prefix = $config['prefix'];
        }
        if (isset($config['initfunc'])) {
            $this->_ct_initFunc = $config['initfunc'];
        }
        if (isset($config['initdata'])) {
            $this->_ct_initdata = $config['initdata'];
        }
        if (isset($config['version'])) {
            $this->_ct_version_create = $config['version'];
        }
        if (isset($config['primary_key'])) {
            $this->_ct_primary_key = $config['primary_key'];
        }
        if (isset($config['primary_key_type'])) {
            $this->_ct_primary_key_type = $config['primary_key_type'];
        }
        if (isset($config['database'])) {
            $this->_database= $config['database'];
        }
        if (isset($config['name'])) {
            $this->_name= $config['name'];
            $this->_ct_name= $this->_db_prefix.$this->_name;
        }
        if (isset($config['engine'])) {
            $this->_ct_engine = $config['engine'];
        }
        if (isset($config['collation'])) {
            $this->_ct_collation = $config['collation'];
        }
        if (isset($config['columns'])) {
            $this->_ct_cells = $config['columns'];
        }
        if (isset($config['index'])) {
            $this->_ct_indexes = $config['index'];
        }
        if (isset($config['unique'])) {
            $this->_ct_uniques = $config['unique'];
        }
        if (isset($config['fulltext'])) {
            $this->_ct_fulltext = $config['fulltext'];
        }
        $auto_init = false;
        if ($config['autoinit']) {
            $auto_init = true;
        }
        if (isset($config['revisions'])) {
            $this->_ct_revisions = $config['revisions'];
        }
        if ($config['register']) {
            $this->_register_table = $config['register'];
			if ($config['autoregister']) { $this->_auto_register = $config['autoregister']; }
			
			$register_config = [
			"database" => $this->_database,
            "prefix" => $this->_db_prefix,
            "name" => $this->_register_table,
            "engine" => "MyISAM",
            "version" => "",
            "row_format" => "Dynamic",
            "create_time" => date("Y-m-d H:i:s"),
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'old_data' => "text NOT NULL DEFAULT ''",
				'new_data' => "text NOT NULL DEFAULT ''",
				'query' => "text NOT NULL DEFAULT ''",
				'restore_query' => "text NOT NULL DEFAULT ''",
				'date' => "datetime NULL DEFAULT NULL",
				'table' => "varchar(75) NOT NULL DEFAULT ''",
				'row_id' => "int(11) NOT NULL DEFAULT 0",
				'action' => "varchar(20) NOT NULL DEFAULT ''",
				'uid' => "int(11) NOT NULL DEFAULT 0",
			),
			"index" => array(
				'table' => array( 'table', 'row_id', 'action' ),
			),
			
			];
			createModelFile('model_'.$this->_register_table);
			$this->_register_model = new Model($register_config);
			
        }
		
        if ($auto_init) {
            $this->initAll();
        }
		
		//var_dump($config); exit;
	}
	
    public function db() {
        return $this->_db;
    }
    public function getdatabasename() {
        return $this->_database;
    }
    public function gettablename() {
        return $this->_ct_name;
    }
    public function getprefix() {
        return $this->_db_prefix;
    }
	
    public function getprimarykey() {
        return $this->_ct_primary_key;
    }
	
    public function getcolumns() {
        return $this->_ct_cells;
    }
	
    public function debug_query_once($new_state=true) {
        $this->_is_debug_query_once = $new_state;
		return $this;
    }
    public function debug_query($new_state=true) {
        $this->_is_debug_query = $new_state;
		return $this;
    }
	
    public function register_query_once($new_state=true) {
        $this->_register_query_once = $new_state;
		return $this;
    }
    public function register_query($new_state=true) {
        $this->_register_query = $new_state;
		return $this;
    }
	
    public function initAll() {
        if (!$this->_ct_exists) {
            $this->initTable();
			$this->_migrateChecks();
        }
    }
	
    /**
     * Опрашивает базу и создает кеш существуюущих партиций на текущий момент выполнения скрипта
     */
    private function _cacheExistsTables() {
        
        $tables = Registry::isRegistered('mysql_db_tables') ? Registry::get('mysql_db_tables') : false;
        if (!is_array($tables)) {
            $tables = $this->_db->getAll("SHOW TABLE STATUS");
            $tables_ = array();
            $comments_ = array();
            foreach ($tables as $k=>$table) {
                $comment = $table['Comment'];
                $table =$table['Name'];
                $tables_[] = $table;
                $comments_[$table] = $comment;
            }
            $tables = $tables_;
            try {
                Registry::set('mysql_db_tables',$tables);
                Registry::set('mysql_db_tables_comments',$comments_);
            } catch (Exception $e) {}
        }
        $comments = Registry::get('mysql_db_tables_comments');

        foreach ($tables as $k=>$table) {
            if ($table==$this->_ct_name) {
                $this->_ct_exists = true;
                $this->_ct_version = '1';
                $comment = $comments[$this->_ct_name];
                $c_start = strpos($comment,'rev{');
                if ($c_start!==false) {

                    $c_end = strpos($comment,'}',$c_start);
                    if ($c_end!==false) {
                        $rev = substr($comment,$c_start+4,$c_end-$c_start-4);
                        $this->_ct_version = $rev;
                    }
                }
                break;
            }
        }
        
    }
	
	
    /**
     * Проверяет необходимость миграции
     */
    private function _migrateChecks() {
		$this->_cacheExistsTables();
        //return false;
        if (is_array($this->_ct_revisions) and count($this->_ct_revisions)) {
            $need_update = false;
            if (!$this->_ct_version) $need_update = true;
			foreach ($this->_ct_revisions as $k=>$revision) {
				if ($this->_ct_version==$revision['version']) {
                    $need_update = true;
                } else {
                    if ($need_update) {
                        if (isset($revision['before_query']) and is_array($revision['before_query'])){
                            foreach ($revision['before_query'] as $k=>$query) {
                                try {
                                    $query = str_replace('{DBPREFIX}',$this->_db_prefix,$query);
                                    $this->_db->query($query);
                                } catch (Exception $e) {
                                    die('SQL Fail Migr:'.$query);
                                }
                            }
                        }
                        if (isset($revision['before_func']) and method_exists($this,$revision['before_func'])) {
                            call_user_func(array($this, $revision['before_func']));
                        }
                        if (isset($revision['del_index']) and is_array($revision['del_index'])) {
                            foreach ($revision['del_index'] as $k=>$index) {
                                $query = "ALTER TABLE `{$this->_ct_name}` DROP INDEX {$index};";
                                try {
                                    $this->_db->query($query);
                                } catch (Exception $e) {
                                    die('SQL Fail Migr:'.$query);
                                }
                            }
                        }
                        if (isset($revision['del_uniq']) and is_array($revision['del_uniq'])) {
                            foreach ($revision['del_uniq'] as $k=>$uniq) {
                                $query = "ALTER TABLE `{$this->_ct_name}` DROP INDEX {$uniq};";
                                try {
                                    $this->_db->query($query);
                                } catch (Exception $e) {
                                    die('SQL Fail Migr:'.$query);
                                }
                            }
                        }
                        if (isset($revision['del_fulltext']) and is_array($revision['del_fulltext'])) {
                            foreach ($revision['del_fulltext'] as $k=>$fulltext) {
                                $query = "ALTER TABLE `{$this->_ct_name}` DROP INDEX {$fulltext};";
                                try {
                                    $this->_db->query($query);
                                } catch (Exception $e) {
                                    die('SQL Fail Migr:'.$query);
                                }
                            }
                        }
                        if (isset($revision['del_columns']) and is_array($revision['del_columns'])) {
                            foreach ($revision['del_columns'] as $k=>$cell) {
                                $query = "ALTER TABLE `{$this->_ct_name}` DROP `{$cell}`;";
                                try {
                                    $this->_db->query($query);
                                } catch (Exception $e) {
                                    die('SQL Fail Migr:'.$query);
                                }
                            }
                        }
                        if (isset($revision['mod_columns']) and is_array($revision['mod_columns'])) {
                            foreach($revision['mod_columns'] as $k=>$cell) {
                                $cell_name = $k;
                                $cell_type = $this->_ct_cells[$k];
                                if (isset($cell['name'])) $cell_name = $cell['name'];
                                if (isset($cell['type'])) $cell_type = $cell['type'];
                                $query = "ALTER TABLE  `{$this->_ct_name}` CHANGE  `{$k}`  `{$cell_name}` {$cell_type};";
                                try {
                                    $this->_db->query($query);
                                } catch (Exception $e) {
                                    die('SQL Fail Migr:'.$query);
                                }
                            }
                        }
                        if (isset($revision['add_columns']) and is_array($revision['add_columns'])) {
                            $prev_cell = "";
                            foreach ($revision['add_columns'] as $k=>$cell) {
                                $cell_ =str_replace('{#PREV_CELL#}',$prev_cell,$cell);
                                $query = "ALTER TABLE  `{$this->_ct_name}` ADD  `{$k}` {$cell_};";
                                if ($debug) echo $query."\n";
                                try {
                                    $this->_db->query($query);
                                } catch (Exception $e) {
                                    die('SQL Fail Migr:'.$query);
                                }
                                $prev_cell = $k;
                            }
                        }
                        if (isset($revision['add_index']) and is_array($revision['add_index'])) {
                            foreach ($revision['add_index'] as $k=>$index) {
                                $query = "ALTER TABLE `{$this->_ct_name}` ADD INDEX `{$k}` (".implode(',',$index).");";
                                if ($debug) echo $query."\n";
                                try {
                                    $this->_db->query($query);
                                } catch (Exception $e) {
                                    die('SQL Fail Migr:'.$query);
                                }
                            }
                        }
                        if (isset($revision['add_uniq']) and is_array($revision['add_uniq'])) {
                            foreach ($revision['add_uniq'] as $k=>$index) {
                                $query = "ALTER TABLE `{$this->_ct_name}` ADD UNIQUE `{$k}` (".implode(',',$index).");";
                                if ($debug) echo $query."\n";
                                try {
                                    $this->_db->query($query);
                                } catch (Exception $e) {
                                    die('SQL Fail Migr:'.$query);
                                }
                            }
                        }
                        if (isset($revision['add_fulltext']) and is_array($revision['add_fulltext'])) {
                            foreach ($revision['add_fulltext'] as $k=>$fulltext) {
                                $query = "ALTER TABLE `{$this->_ct_name}` ADD FULLTEXT `{$k}` (".implode(',',$fulltext).");";
                                if ($debug) echo $query;
                                try {
                                    $this->_db->query($query);
                                } catch (Exception $e) {
                                    die('SQL Fail Migr:'.$query);
                                }
                            }
                        }
                        if (isset($revision['engine'])) {
                            $query = "ALTER TABLE  `{$this->_ct_name}` ENGINE = {$revision['engine']};";
                            try {
                                $this->_db->query($query);
                            } catch (Exception $e) {
                                die('SQL Fail Migr:'.$query);
                            }
                        }
                        if (isset($revision['after_query']) and is_array($revision['after_query'])){
                            foreach ($revision['after_query'] as $k=>$query) {
                                try {
                                    $query = str_replace('{DBPREFIX}',$this->_db_prefix,$query);
                                    $this->_db->query($query);
                                } catch (Exception $e) {
                                    die('SQL Fail Migr:'.$query);
                                }
                            }
                        }
                        if (isset($revision['after_func']) and method_exists($this,$revision['after_func'])) {
                            call_user_func(array($this, $revision['after_func']));
                        }
                        $query = "ALTER TABLE  `{$this->_database}`.`{$this->_ct_name}` COMMENT =  'rev{{$revision['version']}}';";
						try {
                            $this->_db->query($query);
                        } catch (Exception $e) {
                            die('SQL Fail Migr:'.$query);
                        }
                        $this->_ct_version = $revision['version'];
                        $comments = Registry::get('mysql_db_tables_comments');

                        $comments[$this->_ct_name] = "rev{".$this->_ct_version."}";
                        try {
                            Registry::set('mysql_db_tables_comments',$comments);
                        } catch (Exception $e) {}
                    }
                }
            }
        }
    }
	
	public function initTable(){
        $prms = array();
        $prms[] = " `{$this->_ct_primary_key}` {$this->_ct_primary_key_type}";
        foreach ($this->_ct_cells as $cName=>$opts) {
            $prms[] = " `{$cName}` {$opts} ";
        }
        $prms[] = " PRIMARY KEY (`{$this->_ct_primary_key}`) ";
        foreach ($this->_ct_indexes as $iName=>$cells) {
            $index_cells = array();
            foreach ($cells as $k=>$cell) { $index_cells[] = "`{$cell}`"; }
            $prms[] = " INDEX `{$iName}` ( ".implode(',',$index_cells)." ) ";
        }
        foreach ($this->_ct_uniques as $iName=>$cells) {
            $unique_cells = array();
            foreach ($cells as $k=>$cell) { $unique_cells[] = "`{$cell}`"; }
            $prms[] = " UNIQUE INDEX `{$iName}` ( ".implode(",",$unique_cells)." ) ";
        }
        foreach ($this->_ct_fulltext as $iName=>$cells) {
            $fulltext_cells = array();
            foreach ($cells as $k=>$cell) { $fulltext_cells[] = "`{$cell}`"; }
            $prms[] = " FULLTEXT INDEX `{$iName}` ( ".implode(",",$fulltext_cells)." ) ";
        }
        $query = "
            CREATE TABLE IF NOT EXISTS `{$this->_database}`.`{$this->_ct_name}` (
                ".implode(',',$prms)."
            ) ENGINE={$this->_ct_engine} COLLATE='{$this->_ct_collation}' COMMENT = '{$this->_ct_version_create}';";
		$debug = $this->showDebugQuery($query);
		if($debug) return true;
		try {
            $this->_db->query($query);
        } catch (Exception $e) {
            echo "sql fail:{$query}";
            exit();
        }
        if (count($this->_ct_initdata)) {
            $query_inserts = array();
            $query_cells = array();
            foreach ($this->_ct_initdata as $K=>$data) {
				$this->_db->query("INSERT IGNORE INTO `{$this->_database}`.`{$this->_ct_name}` SET ?u",$data);
            }
            $this->_ct_initdata = array();
        }
        if (!is_null($this->_ct_initFunc) and method_exists($this,$this->_ct_initFunc)) {
            call_user_func(array($this,$this->_ct_initFunc));
        }
		$this->_ct_exists = true;
	}
	
	private function showDebugQuery($query, $isReurn = false){
        if ($this->_is_debug_query or $this->_is_debug_query_once) {
			$this->_is_debug_query_once = false;
			
			if($isReurn){
				return "Model query: [{$query}]";
			}
			else {
				echo "Model query: [{$query}]";
				return true;
			}
        }
		return false;
	}
	
	public function get($cols='*'){
		if(is_array($cols)){
			$this->_sql_cols=implode(', ',$cols);
			$this->_sql = true;
		}
		else {
			$this->_sql_cols=$cols;
			$this->_sql = true;
		}
		return $this;
	}
	
	public function from($table=null){
		if(is_null($table)){
			$this->_sql_from="`{$this->_database}`.`{$this->_ct_name}`";
			$this->_sql = true;
		}
		elseif(is_array($table)) {
			$this->_sql_from=implode(', ',$table);
			$this->_sql = true;
		}
		else {
			$this->_sql_from=$table;
			$this->_sql = true;
		}
		return $this;
	}
	
	public function union($items=null){
		if(is_array($items)) {
			$this->_sql_union = [];
			foreach($items as $item){
				if(is_object($item)){
					$this->_sql_union[] = "`{$item->getdatabasename()}`.`{$item->gettablename()}`";
					$this->_sql = true;
				}
				else {
					$this->_sql_union[] = $item;
					$this->_sql = true;
				}
			}
		}
		return $this;
	}
	
	public function where($where='1'){
		$this->_sql_where = $where;
		$this->_sql = true;
		return $this;
	}
	
	public function order($order=null){
		if($order){
			$this->_sql_order = $order;
			$this->_sql = true;
		}
		return $this;
	}
	
	public function offset($offset=null){
		if($offset){
			$this->_sql_offset = $offset;
			$this->_sql = true;
		}
		return $this;
	}
	
	public function limit($limit=null){
		if($limit){
			$this->_sql_limit = $limit;
			$this->_sql = true;
		}
		return $this;
	}
	
	public function commit($type='all'){
		if($this->_sql){
			
			$sql = "";
			$sql1 = "SELECT ";
			if(!$this->_sql_cols) die('Cols not selected'); else $sql1 .= $this->_sql_cols;
			if($this->_sql_where) { $sql2 .= " WHERE ".$this->_sql_where; }
			if(!$this->_sql_from and !$this->_sql_union) die('Table not selected'); else {
				if($this->_sql_from) {
					$sql1 .= " FROM ". $this->_sql_from;
				}
				if($this->_sql_union) {
					$sql_array = [];
					foreach($this->_sql_union as $index=>$table){
						$sql_array[]= "SELECT * FROM ".$table.$sql2;
					}
					$sql = $sql1." FROM (".implode(" UNION ", $sql_array).") as `t`";
				}
				else {
					$sql = $sql1.$sql2;
				}
			}
			
			if($this->_sql_order) { $sql .= " ORDER BY ".$this->_sql_order; }
			if($this->_sql_limit) { $sql .= " LIMIT ".$this->_sql_limit; }
			if($this->_sql_offset) { $sql .= " OFFSET ".$this->_sql_offset; }
			//echo $sql; return true;
			$debug = $this->showDebugQuery($sql);
			if($debug) return true;
			try {
				switch($type){
					case 'all' : return $this->_db->getAll($sql); break;
					case 'row' : return $this->_db->getRow($sql); break;
					case 'one' : return $this->_db->getOne($sql); break;
					case 'col' : return $this->_db->getCol($sql); break;
				}
				$this->usetSql();
			} catch (Exception $e) {
				die('query fail: '.$sql);
			}
		}
	}
	
	private function usetSql(){
		$this->_sql = null;
		$this->_sql_cols = null;
		$this->_sql_from = null;
		$this->_sql_union = null;
		$this->_sql_where = null;
		$this->_sql_order = null;
		$this->_sql_offset = null;
		$this->_sql_limit = null;
	}
	
	public function __toString(){
		if($this->_sql){
			return $this->commit();
		}
		
	}
	
	// Берет первую Mysql функцию удовлетворяющую условию
    public function getMySQLFunc($func,$on=null,$where=null,$order=null,$group=null) {
        if(is_null($on)) {
            $on = $this->_primary;
        }
        if (is_null($where)) {
            $where = " 1 ";
        }
        $query = "
            SELECT
                {$func}({$on}) AS `ret` FROM `{$this->_database}`.`{$this->_ct_name}` WHERE {$where}
                    ".((!is_null($group)) ? "
                GROUP BY
                    {$group}
                        " : "") ."
                    ".((!is_null($order)) ? "
                ORDER BY
                    {$order}
                        " : "")."
            ";
		$debug = $this->showDebugQuery($query);
		if($debug) return true;
        try {
			$this->_sql_trys = 1;
            return $this->_db->getRow($query); 
        } catch (Exception $e) {
			$this->_sql_trys++;
			if($this->_sql_trys<=2){
				$this->initTable();
				$this->getMySQLFunc($func, $on, $where, $order, $group);
			}
			else {
				die('query fail: '.$query);
			}
        }
        $ret = $this->_db->getRow($query);
        return (isset($ret['ret']) ? $ret['ret'] : null );
    }
	
	// Берет первую строку из таблици удовлетворяющую условию
    public function getItemWhere($where='1', $what='*') {
		if(is_int($where) AND isset($this->_ct_primary_key)){
			$where = "`{$this->_ct_primary_key}` = {$where}";
		}
        $query = "SELECT {$what} FROM `{$this->_database}`.`{$this->_ct_name}` WHERE {$where} LIMIT 1;";
		$debug = $this->showDebugQuery($query);
		if($debug) return true;
        try {
			$this->_sql_trys = 1;
            return $this->_db->getRow($query); 
        } catch (Exception $e) {
			$this->_sql_trys++;
			if($this->_sql_trys<=2){
				$this->initTable();
				$this->getItemWhere($where, $what);
			}
			else {
				die('query fail: '.$query);
			}
        }
    }
	
    public function getItem($id, $what='*') {
		if(is_numeric($id) AND isset($this->_ct_primary_key)){
			$id = (int)$id;
			$ret = $this->getItemWhere("`{$this->_ct_primary_key}`={$id}", $what);
			return $ret;
		}
		return false;
    }
	
	// Берет все строки из таблици удовлетворяющие условию
    public function getItemsWhere($where="1",$order=null,$offset=null,$limit=null,$what='*',$alias=null) {
		$parse_where=$where;
		if(is_array($where)){
			$parse_where=[];
			foreach($where as $key=>$val){ $parse_where[] = "`{$key}`='{$val}'";} $parse_where = implode(' AND ', $parse_where);
		}
        $query = "SELECT {$what} FROM `{$this->_database}`.`{$this->_ct_name}` ".((is_null($alias)) ? "" : " AS `{$alias}`")." WHERE {$parse_where}";
        if (!is_null($order)) $query.=" ORDER BY {$order} ";
        if (!is_null($limit) and !is_null($offset)) {
            $query.=" LIMIT {$offset},{$limit}";
        }
		$debug = $this->showDebugQuery($query);
		if($debug) return true;
		try {
			$this->_sql_trys = 1;
			return $this->_db->getAll($query);
		} catch (Exception $e) {
			if($this->_sql_trys<=2){
				$this->initTable();
				$this->getItemsWhere($parse_where, $order, $offset, $limit, $what, $alias);
			}
			else {
				die('query fail: '.$query);
			}
        }
    }
	
	// Берет все строки из таблици удовлетворяющие условию
    public function getCountWhere($where="1") {
		$parse_where=$where;
		if(is_array($where)){
			$parse_where=[];
			foreach($where as $key=>$val){ $parse_where[] = "`{$key}`='{$val}'";} $parse_where = implode(' AND ', $parse_where);
		}
		if(is_int($where)){
			$parse_where = "`{$this->_ct_primary_key}`={$where}";
		}
        $query = "SELECT COUNT(*) FROM `{$this->_database}`.`{$this->_ct_name}` WHERE {$parse_where}";

		$debug = $this->showDebugQuery($query);
		if($debug) return true;
		try {
			$this->_sql_trys = 1;
			return $this->_db->getOne($query);
		} catch (Exception $e) {
			if($this->_sql_trys<=2){
				$this->initTable();
				$this->getCountWhere($parse_where);
			}
			else {
				die('query fail: '.$query);
			}
        }
    }
	
	
	function registerQuery($action, $data, $where = null){
		$register_data = [];
		switch($action){
			case 'InsertUpdate':
					if(isset($data[$this->_ct_primary_key])){
						$curent_data = $this->getItem($data[$this->_ct_primary_key]);
						if(!empty($curent_data)){
							$this->registerUpdateQuery($data[$this->_ct_primary_key], $data, $curent_data);
						}
						else {
							$this->registerInsertQuery($data, $data[$this->_ct_primary_key]);
						}
					}
					else {
						$curent_datas = $this->getItemsWhere($data);
						if(!empty($curent_datas)){
						foreach($curent_datas as $curent_data){
							$this->registerUpdateQuery($curent_data[$this->_ct_primary_key], $data, $curent_data);
						}
						}
						else {
							$this->registerInsertQuery($data);
						}
					}
					
					
					$register_data=[
						'action' => 'InsertUpdate',
					];
				break;
			case 'Insert':
				if(isset($data[$this->_ct_primary_key])){
					$count = $this->getCountWhere((int)$data[$this->_ct_primary_key]);
					if($count==0){
						$this->registerInsertQuery($data, $data[$this->_ct_primary_key]);
					}
				}
				else {
					$this->registerInsertQuery($data);
				}
				break;
			case 'Update':	
				$curent_datas = $this->getItemsWhere($where);
				if(!empty($curent_datas)){
					foreach($curent_datas as $curent_data){
						$this->registerUpdateQuery($curent_data[$this->_ct_primary_key], $data, $curent_data);
					}
				}
				break;
			case 'Delete':
				$curent_datas = $this->getItemsWhere($where);
				if(!empty($curent_datas)){
					foreach($curent_datas as $curent_data){
						$this->registerDeleteQuery($curent_data[$this->_ct_primary_key], $curent_data);
					}
				}
				break;
				
		}
		return $this->_last_register_id;
	}
	
	function registerUpdateQuery($id, $data, $curent_data=null){
		if($this->_register_model){
		if(is_null($curent_data)){
			$curent_data = $this->getItem($id);
		}
		$register_data=[
			'old_data' => json_encode($curent_data),
			'new_data' => json_encode($data),
			'query' => $this->_db->parse("UPDATE `{$this->_database}`.`{$this->_ct_name}` SET ?u WHERE `{$this->_ct_primary_key}`=?i", $data, $id),
			'restore_query' => $this->_db->parse("UPDATE `{$this->_database}`.`{$this->_ct_name}` SET ?u WHERE `{$this->_ct_primary_key}`=?i", $curent_data, $id),
			'date' => date("Y-m-d H:i:s"),
			'table' => "`{$this->_database}`.`{$this->_ct_name}`",
			'row_id' => $id,
			'action' => 'Update',
		];
		$this->_last_register_id = $this->_register_model->Insert($register_data);
		}
	}
	
	function registerInsertQuery($data, $id=null){
		if($this->_register_model){
		$register_data=[
			'old_data' => '',
			'new_data' => json_encode($data),
			'query' => $this->_db->parse("INSERT IGNORE `{$this->_database}`.`{$this->_ct_name}` SET ?u", $data),
			'restore_query' => (!is_null($id)?$this->_db->parse("DELETE FROM `{$this->_database}`.`{$this->_ct_name}` WHERE `{$this->_ct_primary_key}`=?i", $id):''),
			'date' => date("Y-m-d H:i:s"),
			'table' => "`{$this->_database}`.`{$this->_ct_name}`",
			'row_id' => (int)$id,
			'action' => 'Insert',
		];
		$this->_last_register_id = $this->_register_model->Insert($register_data);
		}
	}
	
	function registerDeleteQuery($id=0, $curent_data){
		if($this->_register_model){
		$register_data=[
			'old_data' => json_encode($curent_data),
			'new_data' => '',
			'query' => $this->_db->parse("DELETE FROM `{$this->_database}`.`{$this->_ct_name}` WHERE `{$this->_ct_primary_key}`=?i", $id),
			'restore_query' => $this->_db->parse("INSERT IGNORE `{$this->_database}`.`{$this->_ct_name}` SET ?u", $curent_data),
			'date' => date("Y-m-d H:i:s"),
			'table' => "`{$this->_database}`.`{$this->_ct_name}`",
			'row_id' => (int)$id,
			'action' => 'Delete',
		];
		$this->_last_register_id = $this->_register_model->Insert($register_data);
		}
	}
	
    // Вставляет данные, при существавании уникального индекса и такой записи, обновляет данные
    public function InsertUpdate($data=array()) {
        /* Генерируем запрос */
        /* Ячейки у нас есть в конфиге */
        $values = array();
        foreach ($data as $kName=>$kVal) {
            if (($kName!=$this->_ct_primary_key) and (!isset($this->_ct_cells[$kName]))) {
                unset($data[$kName]);
            } else {
                $values[$kName] = $kVal;
            }
        }
		
        $query = "INSERT IGNORE `{$this->_database}`.`{$this->_ct_name}` SET ?u ON DUPLICATE KEY UPDATE ?u";
		$query = $this->_db->parse($query, $values, $values);
		$debug = $this->showDebugQuery($query);
		if($debug) return true;

		try {
			if($this->_register_table AND ($this->_register_query OR $this->_register_query_once OR $this->_auto_register)) { $register_id = $this->registerQuery('InsertUpdate', $values); $this->_register_query_once = false;}
			$this->_db->query($query); $this->_sql_trys = 1;
			if(isset($data[$this->_ct_primary_key])){
				return $data[$this->_ct_primary_key];
			}
			else {
				$insertId = $this->_db->insertId();
				if($register_id){ $this->_register_model->Update(array('restore_query'=>"DELETE FROM `{$this->_database}`.`{$this->_ct_name}` WHERE `{$this->_ct_primary_key}`={$insertId} LIMIT 1;",'row_id'=>$insertId), $register_id); }
				return $insertId;
			}
		} catch (Exception $e) {
			$this->_sql_trys++;
			if($this->_sql_trys<=2){
				$this->initTable();
				$this->InsertUpdate($data);
			}
			else {
				die('query fail: '.$query);
			}
        }
    }
	
    // Вставляет данные
    public function Insert($data=array()) {
        /* Генерируем запрос */
        /* Ячейки у нас есть в конфиге */
        $values = array();
        foreach ($data as $kName=>$kVal) {
            if (($kName!=$this->_ct_primary_key) and (!isset($this->_ct_cells[$kName]))) {
                unset($data[$kName]);
            } else {
                $values[$kName] = $kVal;
            }
        }
		
        $query = "INSERT IGNORE `{$this->_database}`.`{$this->_ct_name}` SET ?u";
		$query = $this->_db->parse($query, $values);
		$debug = $this->showDebugQuery($query);
		if($debug) return true;
		try {
			if($this->_register_table AND ($this->_register_query OR $this->_register_query_once OR $this->_auto_register)) { $register_id = $this->registerQuery('Insert', $values); $this->_register_query_once = false;}
			$this->_db->query($query); $this->_sql_trys = 1;
			if(isset($data[$this->_ct_primary_key])){
				return $data[$this->_ct_primary_key];
			}
			else {
				$insertId = $this->_db->insertId();
				if($register_id){ $this->_register_model->Update(array('restore_query'=>"DELETE FROM `{$this->_database}`.`{$this->_ct_name}` WHERE `{$this->_ct_primary_key}`={$insertId} LIMIT 1;",'row_id'=>$insertId), $register_id); }
				return $insertId;
			}
		} catch (Exception $e) {
			$this->_sql_trys++;
			if($this->_sql_trys<=2){
				$this->initTable();
				$this->InsertUpdate($data);
			}
			else {
				die('query fail: '.$query);
			}
        }
    }
	
    public function Update($data=array(), $ids) {
        $values = array();
        foreach ($data as $kName=>$kVal) {
            if (($kName!=$this->_ct_primary_key) and (!isset($this->_ct_cells[$kName]))) {
                unset($data[$kName]);
            } else {
                if ($kName!=$this->_ct_primary_key){
                    $values[$kName] = $kVal;
                }
            }
        }
       
        $where = "";
        if (is_array($ids)) {
            $where = array();
            foreach ($ids as $k=>$id) {
                if (is_numeric($id)) {
                    $where[]= "`{$this->_ct_primary_key}`='{$id}'";
                } else {
                    $where[]="{$id}";
                }
            }
            $where = implode(' OR ',$where);
        } else {
            if (is_numeric($ids)) {
                $where = "`{$this->_ct_primary_key}`='{$ids}'";
            } else {
                $where = $ids;
            }
        }
        $query = "UPDATE `{$this->_database}`.`{$this->_ct_name}` SET ?u WHERE {$where}";
        $query = $this->_db->parse($query, $values);
        
		if ($this->_is_debug_query OR $this->_is_debug_query_once) {
            echo "Model query: {$query}";
            $this->_is_debug_query_once = false;
			return "Model query: {$query}";
        }
        try {
			$this->_sql_trys = 1;
			if($this->_register_table AND ($this->_register_query OR $this->_register_query_once OR $this->_auto_register)) { $register_id = $this->registerQuery('Update', $values, $where); $this->_register_query_once = false;}
			$this->_db->query($query);
			return true;
        } catch (Exception $e) {
			if($this->_sql_trys<=2){
				$this->initTable();
				$this->Update($data, $ids);
			}
			else {
				die('query fail: '.$query);
			}
        }
    }

    public function Delete($id=0){
        if(is_numeric($id)){
            $query = "DELETE FROM `{$this->_database}`.`{$this->_ct_name}` WHERE `{$this->_ct_primary_key}`={$id} LIMIT 1;";
			$debug = $this->showDebugQuery($query);
			if($debug) return true;
			try {
				$this->_sql_trys = 1;
				if($this->_register_table AND ($this->_register_query OR $this->_register_query_once OR $this->_auto_register)) { $register_id = $this->registerQuery('Delete', null, "`{$this->_ct_primary_key}`={$id}"); $this->_register_query_once = false;}
				return $this->_db->query($query);
			} catch (Exception $e) {
				if($this->_sql_trys<=2){
					$this->initTable();
					$this->Delete($id);
				}
				else {
					die('query fail: '.$query);
				}
			}
        }
		elseif(is_array($id)) {
			foreach($id as $id_a){
				$this->Delete($id_a);
			}
        }
		else {
            $query = "DELETE FROM `{$this->_database}`.`{$this->_ct_name}` WHERE {$id}";
			$debug = $this->showDebugQuery($query);
			if($debug) return true;
			try {
				$this->_sql_trys = 1;
				if($this->_register_table AND ($this->_register_query OR $this->_register_query_once OR $this->_auto_register)) { $register_id = $this->registerQuery('Delete', null, $id); $this->_register_query_once = false;}
				return $this->_db->query($query);
			} catch (Exception $e) {
				if($this->_sql_trys<=2){
					$this->initTable();
					$this->Delete($id);
				}
				else {
					die('query fail: '.$query);
				}
			}
        }
    }
	
    public function getfreeid() {
        $query = "
            SELECT
                `t1`.`{$this->_ct_primary_key}`+1
                FROM
                    `{$this->_database}`.`{$this->_ct_name}` AS `t1`
                LEFT JOIN
                    `{$this->_database}`.`{$this->_ct_name}` AS `t2`
                        ON
                            `t1`.`{$this->_ct_primary_key}`+1=`t2`.`{$this->_ct_primary_key}`
            WHERE
                `t2`.`{$this->_ct_primary_key}` IS NULL
            ORDER BY
                `t1`.`{$this->_ct_primary_key}` ASC
            LIMIT 1;
            ";
		$debug = $this->showDebugQuery($query);
		if($debug) return true;
        return $this->_db->getOne($query);
    }
	
}
?>