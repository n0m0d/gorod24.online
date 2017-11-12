<?php
class AdminList {
	private $_model, $_model_where, $_columns, $_attrs=array(), $_items=null, $_limit = 30, $_start = 0, $_page = 1, $_tablename, $_primary, $_modelcolumns, $_count;
	
	function __construct($options = array()){
		if(!empty($options)){
			$this->setOptions($options);
		}
	}
	
	public function setOptions($options = array()){
		if(!empty($options)){
			if(isset($options['model'])){
				$this->_model = $options['model'];
				$this->_tablename = $this->_model->gettablename();
				$this->_primary = $this->_model->getprimarykey();
				$this->_modelcolumns = $this->_model->getcolumns();
			}
			if(isset($options['items'])){
				$this->_items = $options['items'];
			}
			if(isset($options['attrs'])){
				$this->_attrs = $options['attrs'];
			}
			if(isset($options['where'])){
				$this->_model_where = $options['where'];
			} else $this->_model_where = "1";
			if(isset($options['columns'])){
				$this->_columns = $options['columns'];
			}
			else {
				if($this->_model){
					$this->_columns = [];
					$this->_columns[] = [ "title"=>$this->_primary, "name"=>$this->_primary, "content" => create_function('$cel,$row','echo $cel;') ];
					foreach($this->_modelcolumns as $name => $column){
						$this->_columns[] = [ "title"=>$name, "name"=>$name, "content" => create_function('$cel,$row','echo $cel;') ];
					}
				}
			}
			if(isset($options['limit'])){
				$this->_limit = $options['limit'];
			}
			if(isset($options['page'])){
				$this->_page = $options['page'];
			} 
			elseif($_GET['page']) { $this->_page = $_GET['page']; }
			$this->_start = ($this->_limit * $this->_page) - $this->_limit;
		}
	}
	
	public function setItems($items){
		if(is_array($items)){
			$this->_items = $items;
		}
	}
	
	public function get(){
		$result = '';
		$thead = ''; $tbody = '';
		foreach($this->_columns as $col){
			$attrs = '';
			if(is_array($col['attrs']))foreach($col['attrs'] as $key=>$val){
				$attrs .= $key.'="'.addslashes($val).'"';
			}
			$thead .= '<th '.$attrs.'>'.$col['title'].'</th>'; 
		} 
		$thead = '<thead><tr>'.$thead.'</tr></thead>';
		if(is_null($this->_items)){
			$this->_count = $this->_model->getCountWhere($this->_model_where);
			$rows = $this->_model->getItemsWhere($this->_model_where, null, $this->_start, $this->_limit);
		} else $rows = $this->_items;
		foreach($rows as $row){
			$tr = '<tr id="'.$this->_tablename.'-row-'.$row[$this->_primary].'">';
			foreach($this->_columns as $col){ 
				if(is_callable($col["content"])){
					ob_start();
						call_user_func($col['content'],$row[$col['name']], $row);
					$cel = ob_get_clean();
				}
				else { $cel = $col["content"]; }
				$tr .= '<td>'.$cel.'</td>'; 
			}
			$tr .= '</tr>';
			$tbody .= $tr;
		}
		$tbody = '<tbody>'.$tbody.'</tbody>';
		$attrs = '';
		foreach($this->_attrs as $key=>$value){
			$attrs.=$key.'="'.addslashes($value).'" ';
		}
		$result = '<table '.$attrs.'>'.$thead.$tbody.'</table>';
		
		$urlPattern = Registry::get('REQUEST_URI').'?page=(:num)';
		$paginator = new Paginator($this->_count, $this->_limit, $this->_page, $urlPattern);
		$paginator = $paginator->setLinkAttrs(['class'=>'ajax-load', 'data-center'=>'false']);
		return $result.$paginator;
	}
	
	public function render(){
		echo $this->get();
	}
	
	public function __toString(){
		return $this->get();
	}
	
}

class AdminPage {
	private $_model=null, $_model_where=null, $_fields=null, $_item=null, $_tablename, $_primary, $_modelcolumns, $_action;
	
	function __construct($options = array()){
		if(!empty($options)){
			$this->setOptions($options);
		}
	}
	
	public function setOptions($options = array()){
		if(!empty($options)){
			if(isset($options['model'])){
				$this->_model = $options['model'];
				$this->_tablename = $this->_model->gettablename();
				$this->_primary = $this->_model->getprimarykey();
				$this->_modelcolumns = $this->_model->getcolumns();
			}
			if(isset($options['item'])){
				$this->_item = $options['item'];
			} 
			if(isset($options['where'])){
				$this->_model_where = $options['where'];
			} else $this->_model_where = "1";
			if(isset($options['fields'])){
				$this->_fields = $options['fields'];
			}
			else {
				if($this->_model){
					$this->_fields = [];
					$this->_fields[] = [ "title"=>$this->_primary, "name"=>$this->_primary, "type"=>"text" ];
					foreach($this->_modelcolumns as $name => $column){
						$this->_fields[] = [ "title"=>$name, "name"=>$name, "type"=>"text" ];
					}
				}
			}
			if(isset($options['action'])){
				$this->_action = $options['action'];
			} else $this->_action = Registry::get('REQUEST_URI');
		}
	}
	
	public function setItem($item){
		if(is_array($item)){
			$this->_item = $item;
		}
	}
	
	public function get(){
		$result = '<form class="sectright-filters-form" action="'.$this->_action.'">';
		if(is_array($this->_fields))foreach($this->_fields as $i => $field){
			$result .= AdminPage::getFormObject($field, $this->_item);
		}	
		$result .= '</form>';
		return $result;
	
	}
	
	public static function getFormObject($object, $item){
		$content = '';
		if(is_array($object)){
			if(isAssoc($object)){
				if(!empty($object['type']))
				switch($object['type']){
					case "hidden": { $content = AdminPage::hiddenField($object, $item[$object['name']]); break;}
					case "text": { $content = AdminPage::textField($object, $item[$object['name']]); break;}
					case "mediumText": { $content = AdminPage::mediumTextField($object, $item[$object['name']]); break;}
					case "editor": { $content = AdminPage::tinyEditor($object, $item[$object['name']]); break;}
					case "number": { $content = AdminPage::numberField($object, $item[$object['name']]); break;}
					case "switch": { $content = AdminPage::switchField($object, $item[$object['name']]); break;}
					case "check": { $content = AdminPage::checkField($object, $item[$object['name']]); break;}
					case "date": { $content = AdminPage::dateField($object, $item[$object['name']]); break;}
					case "time": { $content = AdminPage::timeField($object, $item[$object['name']]); break;}
					case "datetime": { $content = AdminPage::datetimeField($object, $item[$object['name']]); break;}
					
					default:{ $content = AdminPage::textField($object, $item[$object['name']]); break;}
				}
			}
			else{
				foreach($object as $item){
					$content.= AdminPage::getFormObject($item);
				}
			}
			
		} else $content .= $object;
		return $content;
	}
	
	public static function prepareJs($js){
		return "\n<script type=\"text/javascript\">\ntry{\n$(function() {\n".$js."\n});\n}catch(error){\nconsole.error(error);\n}\n</script>";
	}
	
	public static function prepareCss($css){
		return "\n<style>\n".$css."\n</style>";
	}
	
	public static function hiddenField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['name'])) $object['name'] = 'text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		
		return '
		<input id="'.$object['id'].'" class="'.$object['class'].'" name="'.$object['name'].'" type="hidden" value="'.$object['value'].'" '.$attrs.'>
		';
	}
	
	public static function textField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
			<div class="sectright-filters-form-label">
				<label><span class="title">'.$object['title'].':</span>
					<input id="'.$object['id'].'" class="filter-input '.$object['class'].'" name="'.$object['name'].'" type="text" value="'.$object['value'].'" placeholder="'.$object['title'].'" '.$attrs.'>
				</label>
			</div>
		';
	}
	
	public static function mediumTextField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
			<div class="sectright-filters-form-label">
				<label><span class="title">'.$object['title'].':</span>
					<textarea id="'.$object['id'].'" class="filter-input '.$object['class'].'" name="'.$object['name'].'" placeholder="'.$object['title'].'" '.$attrs.'>'.addslashes($object['value']).'</textarea>
				</label>
			</div>
		';
	}
	
	public static function tinyEditor($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['height'])) $object['height'] = '300';
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		
		$result.= AdminPage::prepareJs('tinymce.init({
		selector: "#'.$object['id'].'",
		height: "'.$object['height'].'",
		language_url : "/admin/application/views/js/tinyMce_ru.js",
		forced_root_block : false,
		force_p_newlines : false,
		force_br_newlines : true,
		browser_spellcheck : true,
		contextmenu: false,

		cleanup_on_startup: false,
		trim_span_elements: false,
		verify_html: false,
		cleanup: false,
		convert_urls: false,

		//autosave_ask_before_unload: false,
		plugins: [
			"advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker save",
			"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			"table contextmenu directionality emoticons template textcolor paste  textcolor colorpicker textpattern codesample" //fullpage
		],
		toolbar1: "newdocument | save | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect", //newdocument fullpage
		toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
		toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft | codesample | photo",
		
		save_oncancelcallback: function () { console.log("Save canceled"); },
		save_onsavecallback: function () { tinymce.triggerSave(); $("button[name=submitbtn]").click();console.log("Saved"); },
		
		convert_urls: false,
		menubar: true,
		toolbar_items_size: "small",

		style_formats: [{title: "Bold text",inline: "b"}, {title: "Red text",inline: "span",styles: {color: "#ff0000"}}, {title: "Red header",block: "h1",styles: {color: "#ff0000"}}, {title: "Example 1",inline: "span",classes: "example1"}, {title: "Example 2",inline: "span",classes: "example2"}, {title: "Table styles"}, {title: "Table row 1",selector: "tr",classes: "tablerow1"}],
		templates: [{title: "Test template 1",content: "Test 1"}, {title: "Test template 2",content: "Test 2"}],
		content_css: ["//www.tinymce.com/css/codepen.min.css"],
		/* setup: function (editor) { editor.on("change", function () { tinymce.triggerSave();}); } */
	  });');
		
		return $result.'
		<div class="sectright-filters-form-label">
			<p class="title">'.$object['title'].':</p>
			<div id="'.$object['id'].'-block"><textarea name="'.$object['name'].'" id="'.$object['id'].'" class="'.$object['class'].'">'.$object['value'].'</textarea></div>
		</div>
		';
	}
	
	public static function numberField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
			<div class="sectright-filters-form-label">
				<label><span class="title">'.$object['title'].':</span>
					<input id="'.$object['id'].'" class="filter-input '.$object['class'].'" name="'.$object['name'].'" type="number" value="'.$object['value'].'" placeholder="'.$object['title'].'" '.$attrs.'>
				</label>
			</div>
		';
	}
	
	public static function switchField($object, $value=0){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
		<div class="sectright-filters-form-label">
			<input id="'.$object['id'].'" class="switch '.$object['class'].'" '.($object['value']==1?'checked="checked"':'').' name="'.$object['name'].'" type="checkbox" '.$attrs.'/>
			<label for="'.$object['id'].'">'.$object['title'].'</label>
		</div>
		';
	}
	
	public static function checkField($object, $value=0){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		
		return '
		<div class="sectright-filters-form-label">
			<label><span class="title">'.$object['title'].':</span>
				<input id="'.$object['id'].'" name="'.$object['name'].'" class="ch '.$object['class'].'" value="'.$object['value'].'" type="checkbox" '.(($object['value'] == 1)? "checked='checked'" : "").' title="'.$object['title'].': '.$object['value'].'" '.$attrs.'>
			</label>
		</div>
		';
	}
	
	public static function dateField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'date-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'date-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		$result.= AdminPage::prepareJs('$("#'.$object['id'].'").datepicker({dateFormat: "yy-mm-dd"});');
		return $result.AdminPage::textField($object, $value);
	}

	public static function timeField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'time-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'time-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['format'])) $object['format'] = 'hh:mm:ss';
		if(!isset($object['showMinute'])) $object['showMinute'] = true;
		if(!isset($object['showSecond'])) $object['showSecond'] = true;
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		$result.= AdminPage::prepareJs('$("#'.$object['id'].'").timepicker({ 
			timeFormat: "'.$object['format'].'",
			showMinute: '.($object['showMinute']?'true':'false').',
			showSecond: '.($object['showSecond']?'true':'false').',
			showMillisec: false,
			showMicrosec: false,
			showTimezone: false,
			oneLine: true,
			});');
		return $result.AdminPage::textField($object, $value);
	}

	public static function datetimeField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'time-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'time-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['format'])) $object['format'] = 'hh:mm:ss';
		if(!isset($object['showMinute'])) $object['showMinute'] = true;
		if(!isset($object['showSecond'])) $object['showSecond'] = true;
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		$result.= AdminPage::prepareJs('$("#'.$object['id'].'").datetimepicker({ 
			timeFormat: "'.$object['format'].'",
			showMinute: '.($object['showMinute']?'true':'false').',
			showSecond: '.($object['showSecond']?'true':'false').',
			showMillisec: false,
			showMicrosec: false,
			showTimezone: false,
			oneLine: true,
			});');
		return $result.AdminPage::textField($object, $value);
	}
	
	public function __toString(){
		return $this->get();
	}
}

class AdminPage2 {
	
	private $model, $_attrs, $_header, $_table, $_where, $_action, $_menu, $_id, $_fields, $_column_prefix, $_column_sufix, $_structure, $_item, $_items, $_primary_key, $_primary_key_value, $_add_date, $_update_date, $_js, $_css;
	private $_buttons, $_html_table_header, $_html_table_body, $_html_table_foorer, $_html_table_columns; 
	private $_do_action;
	
	function __construct($options = array()){
		if(!empty($options)){
			$this->_html_table_columns=array();
			$this->setOptions($options);
		}
	}
	
	public function setOptions($options = array()){
		if(!empty($options)){
			$uniq=uniqid();
			$this->model = $options['model'];
			$this->_attrs = $options['attrs'];
			$this->_header = $options['header'];
			$this->_footer = $options['footer'];
			$this->_table = $this->model->gettablename();
			$this->_primary_key = $this->model->getprimarykey();
			$this->_structure = $this->model->getcolumns();
			$this->_where = $options['row'];
			$this->_buttons = isset($options['buttons'])?$options['buttons']:array();
			$this->_action = (!empty($options['action'])?$options['action']:$_SERVER['REQUEST_URI']);
			$this->_menu = (!empty($options['menu'])?$options['menu']:'menu-'.$uniq);
			$this->_id = (!empty($options['id'])?$options['id']:'form-'.$uniq);
			$this->_fields = $options['fields'];
			$this->_do_action = $options['do_action'];
			$this->_add_date = $options['add-date'];
			$this->_update_date = $options['update-date'];
			$this->_column_prefix = $options['column_prefix'];
			$this->_column_sufix = $options['column_sufix'];
		}
		return $this;
	}
	
	public function setTable($var){
		$this->_table = $var;
		return $this;
	}
	
	public function setColumn_prefix($var){
		$this->_column_prefix = $var;
		return $this;
	}
	
	public function setColumn_sufix($var){
		$this->_column_sufix = $var;
		return $this;
	}
	
	public function setStructure($structure=array()){
		if(empty($structure)){
			if(!empty($this->_table)){
			$this->_structure = $this->model->db()->GetAll("SHOW FULL FIELDS FROM ".$this->_table);
			foreach($this->_structure as $i=>$row){ if($row['Key']=='PRI'){ $this->_primary_key=$row['Field']; break; }}
			}
		}
		else{
			$this->_structure=$structure;
		}
		return $this;
	}
	
	public function setItem($item=false){
		if(!is_array($item)){
			if(!empty($this->_table)){
				if($this->_where!='new'){
					$query=''; foreach($this->_where as $col=>$val){ $query.=" AND `{$col}`='{$val}'"; }
					$this->_item = $this->model->db()->GetRow("SELECT * FROM ".$this->_table." WHERE 1".$query);
					foreach($this->_item as $column=>$value){ if($column==$this->_primary_key){ $this->_primary_key_value=$value; break; }}
				}
				else{
					$this->_item=array();
				}
			}
		}
		else{
			
			$this->_item=$item;
		}
		return $this;
	}
	
	public function setItems($items=array()){
		if(empty($items)){
			if(!empty($this->_table)){
				if($this->_where!='new' and is_array($this->_where)){
					$query=''; foreach($this->_where as $col=>$val){ $query.=" AND `{$col}`='{$val}'"; }
					$this->_items = $this->model->db()->GetAll("SELECT * FROM ".$this->_table." WHERE 1".$query);
					$this->_primary_key_value=array();
					foreach($this->_items as $i=>$row){
						foreach($row as $column=>$value){ if($column==$this->_primary_key){ $this->_primary_key_value[$i]=$value; break; }}
					}
				}
				else{
					$this->_items=array();
				}
			}
		}
		else{
			$this->_items=$items;
		}
		return $this;
	}
	
	public function postSended($array){
		if(!empty($this->_do_action)){
			do_action($this->_do_action, $this);
		}
	}
	
	public function renderAdminPage(){
		echo $this->getAdminPage();
	}
	
	public function getAdminPage(){
		if(empty($this->_structure)){ $this->setStructure();}
		if(empty($this->_item)){ $this->setItem();}
		
		if(!empty($_POST)){ $this->postSended($_POST); }
		
		$result ='<form id="'.$this->_id.'" action="'.$this->_action.'" method="post">';
		$result.='<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>'.((!is_array($this->_where) and $this->_where=='new')?'Создать '.$this->_header:'Редактировать '.$this->_header ).'</h2>';
		$result.='<div id="wrap-main">';
		$result.='<table class="page-table" width="100%"><tr><td class="page-view" style="width:auto; padding-right:20px;">';
		$center_position='';$right_position='';
		foreach($this->_fields as $name=>$options){
			$row_item=$this->getFormObject($options);
			if($options['position'] == 'center'){ $center_position.=$row_item;}
			else if($options['position'] == 'right'){ $right_position.=$row_item;}
		}
		
		$result.=$center_position;
		$result.='</td><td style="width:260px;">';
		$result.=$right_position;
		$result.='</td></tr></table></div></form>';
		$result.=$this->prepareJs($this->_js);
		$result.=$this->prepareCss($this->_css);
		return $result;
	}
	
	public function renderAdminList(){
		echo $this->getAdminList();
	}
	
	public function getAdminList(){
		if(empty($this->_structure)){ $this->setStructure();}
		if(empty($this->_items)){ $this->setItems();}
		if(!empty($_POST)){ $this->postSended($_POST); }
		
		$result ='<form id="'.$this->_id.'" action="'.$this->_action.'" method="post">';
		$result.='<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>'.$this->_header.':</h2>';
		$result.='<div id="">';
		
		if(!empty($this->_buttons)){
			$result.='<div>'.$this->getFormObject($this->_buttons).'</div>';
		}

		foreach($this->_structure as $i=>$row){
			foreach($this->_fields as $j=>$options){
				$this->_html_table_columns[$j] = $options;
			}
		}
		
		$result.=$this->getTable();
		
		$result.='</div>';
		$result.='</form>';
		$result.=$this->prepareJs($this->_js);
		$result.=$this->prepareCss($this->_css);
		return $result;
	}
	
	public function renderTable(){
		echo $this->getTable();
	}
	
	public function getTable(){
		foreach($this->_attrs as $key=>$item){$attrs.=" {$key}=\"{$item}\"";}
		$out="<table{$attrs}>";
		
		$out.='<thead><tr>';
		foreach($this->_html_table_columns as $column=>$item){
			if($item['type']=="chechbox"){
				$out.='<th class="esc-column"><input type="checkbox" class="all-check" /></th>';
				$this->_js.="	
				$('.all-check').change(function(){
						var prop = $(this).prop('checked');
						if(prop) {
							$('.ch').prop('checked', true);
						} else { 
							$('.ch').prop('checked', false); 
						}
				});
				$('.main-table input[type=\"checkbox\"]').prop('checked', false);
				";
			}else{
				$width=(isset($item['width']))?$item['width']:'auto';
				$out.='<th class="esc-column" style="width:'.$width.'">'.$item['title'].'</th>';
			}
		}
		$out.='</tr></thead>';
		$out.='<tbody>';
		foreach($this->_items as $i=>$row){
			$this->_item=$row;
			$out.='<tr>';
			foreach($this->_html_table_columns as $column=>$item){
				if(is_null($item['value'])){
					$name = substr($item['name'],-2)=='[]'?substr($item['name'],0,-2):$item['name'];
					$item['value']=$row[$name];
				}
				$out.='<td class="esc-column">'.$this->getFormObject( $item ).'</td>';
			}
			$out.='</tr>';
		}
		$out.='</tbody>';
		
		if(is_array($this->_footer)){
			$out.='<tfoot><tr>';
			unset($this->_item);
			foreach($this->_footer as $column=>$item){
				$out.='<td class="esc-column">'.$this->getFormObject( $item ).'</td>';
			}
			$out.='</tr></tfoot>';
		}
		$out.='</table>';
		return $out;
	}
	
	public static function getCustomTable($options){
		$result = '';
		$header = $options['header'];
		$body = $options['body'];
		$footer = $options['footer'];
		$_attrs = $options['attrs'];
		foreach($_attrs as $key=>$item){$attrs.=" {$key}=\"{$item}\"";}
		$result .= "<table{$attrs}>";
		
		if(!empty($header)){
			$result .= '<thead>';
			if(is_array($header)){
					$result .= '<tr>';
					if(is_array($row)){
						foreach($row as $i=>$item){ $result .= '<td>'.$item.'</td>'; }
					} else { $result .= $row; }
					$result .= '</tr>';
			}
			else{ $result .= $header; }
			$result .= '</thead>';
		}
		
		if(!empty($body)){
			$result .= '<tbody>';
			if(is_array($body)){
				foreach($body as $i=>$row){
					$result .= '<tr>';
					if(is_array($row)){
						foreach($row as $i=>$item){ $result .= '<td>'.$item.'</td>'; }
					} else { $result .= $row; }
					$result .= '</tr>';
				}
				
			}
			else{ $result .= $body; }
			$result .= '</tbody>';
		}
		
		if(!empty($footer)){
			$result .= '<tfoot>';
			if(is_array($footer)){
				$result .= '<tr>';
				foreach($footer as $i=>$row){
					$result .= '<td>'.$row.'</td>';
				}
				$result .= '</tr>';
			}
			else{ $result .= $footer; }
			$result .= '</tfoot>';
		}
		
		
		$result .= '</table>';
		return $result;
	}
	
	public function textMatch($text, $row){
		if(is_string($text)){
			preg_match_all('/@\[([^\]]+)\]/',$text,$matches);
			for($i=0;$i<count($matches[0]);$i++){
				if(isset($row[$matches[1][$i]])){
					$text=str_replace($matches[0][$i], $row[$matches[1][$i]], $text);
				}
				else{
					$text=str_replace($matches[0][$i], '', $text);
				}
			}
		}	
		return $text;
	}
	
	public function prepareJs($js){
		return "\n<script type=\"text/javascript\">\ntry{\n$(function() {\n".$js."\n});\n}catch(error){\nconsole.error(error);\n}\n</script>";
	}
	
	public function prepareCss($css){
		return "\n<style>\n".$css."\n</style>";
	}
	
	public function getFormObject($options){
		$content='';
		if(is_array($options)){
		if(isAssoc($options)){
			if(!empty($options['type'])){
				switch($options['type']){
					case "line": { $content = $this->getLine($options); break;}
					case "hidden": { $content = $this->getHiddenField($options); break;}
					case "simply text": { $content = $this->getSimplyTextElement($options); break;}
					case "pre text": { $content = $this->getPreTextElement($options); break;}
					case "link": { $content = $this->getLinkElement($options); break;}
					case "text": { $content = $this->getTextField($options); break;}
					case "number": { $content = $this->getNumberField($options); break;}
					case "medium text": { $content = $this->getMediumTextField($options); break;}
					case "long text": { $content = $this->getTinyEditor($options); break;}
					case "json text": { $content = $this->getJsonText($options); break;}
					case "block": { $content = $this->getBlock($options); break;}
					case "block row": { $content = $this->getBlockRow($options); break;}
					case "stong label": { $content = $this->getStrongLabel($options); break;}
					case "check": { $content = $this->getCheckSwitcher($options); break;}
					case "chechbox": { $content = $this->getChechbox($options); break;}
					case "date": { $content = $this->getDatetField($options); break;}
					case "time": { $content = $this->getTimeField($options); break;}
					case "image selector": { $content = $this->getImageSelector($options); break;}
					case "select": { $content = $this->getSelectField($options); break;}
					case "button": { $content = $this->getButton($options); break;}
					case "save": { $content = $this->getSaveButton($options); break;}
					case "delete": { $content = $this->getDeleteButton($options); break;}
					
					default:{ $content = $this->getTextField($options); break;}
				}
			}
		}
		else{
			foreach($options as $item){
				$content.= $this->getFormObject($item);
			}
		}
		}
		else $content .= $options;
		return $content;
	}
	
	public function getLine($options){
		$options=shortcode_atts( array(
				"title" => "Header",
				"class" => "",
				"content" => "",
		), $options );

		if(is_array($options['content'])){
			$options['content'] = $this->getFormObject($options['content']);
		}
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return '<div class="margintop30 '.$options['class'].'"><label><h3>'.$options['title'].':</h3></label><div class="border">'.$options['content'].'</div></div>';
	}
	
	public function getSimplyTextElement($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "text-".$uniq,
				"name" => "text-".$uniq,
				"class" => "",
				"position" => "",
				"value" => "",
				"format" => "",
				"content" => "",
		), $options );
		if(is_array($options['content'])){ $options['content'] = $this->getFormObject($options['content']); } else { $options['content'] = $this->textMatch($options['content'], $this->_item);}
		
		if(empty($options['value']) and $options['value']!="0") $options['value'] = $this->_item[$options['name']];
		if(!empty($options['position'])) $style = ' style="text-align:'.$options['position'].';"';
		$format=$options['format'];
		if(is_array($format)){
			$options['value'] = isset($format[$options['value']])?$format[$options['value']]:$options['value'];
		}
		else if($format!=""){
			$options['value'] = $this->textMatch($options['value'], $this->_item);
		}
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return '<div id="'.$options['id'].'" class="'.$options['class'].'" '.$style.' name="'.$options['name'].'">'.((!empty($options['content']))?$options['content']:$options['value']).'</div>';
	}
	
	public function getPreTextElement($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "text-".$uniq,
				"name" => "text-".$uniq,
				"class" => "",
				"position" => "",
				"value" => "",
				"format" => "",
		), $options );
		if(empty($options['value'])) $options['value'] = $this->_item[$options['name']];
		if(!empty($options['position'])) $style = ' style="text-align:'.$options['position'].';"';
		$format=$options['format'];
		if(is_array($format)){
			$options['value'] = isset($format[$options['value']])?$format[$options['value']]:$options['value'];
		}
		else if($format!=""){
			$options['value'] = $this->textMatch($options['value'], $this->_item);
		}
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return '<pre id="'.$options['id'].'" class="'.$options['class'].'" '.$style.' name="'.$options['name'].'">'.$options['value'].'</pre>';
	}
	
	public function getLinkElement($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "text-".$uniq,
				"name" => "text-".$uniq,
				"class" => "",
				"position" => "",
				"content" => "",
				"href" => "",
		), $options );
		
		if(empty($options['value'])) $options['value'] = $this->_item[$options['name']];
		if(!empty($options['position'])) $style = ' style="text-align:'.$options['position'].';"';
		if(is_array($options['content'])){ $options['content'] = $this->getFormObject($options['content']); }
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		$options['href'] = $this->textMatch($options['href'], $this->_item);
		return '<a id="'.$options['id'].'" class="'.$options['class'].'" '.$style.' name="'.$options['name'].'" href="'.$options['href'].'">'.$options['content'].'</a>';
	}
	
	public function getTextField($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "text-".$uniq,
				"name" => "text-".$uniq,
				"class" => "",
				"value" => "",
		), $options );
		if(empty($options['value'])) $options['value'] = $this->_item[$options['name']];
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return '<input id="'.$options['id'].'" class="'.$options['class'].'" name="'.$options['name'].'" type="text" value="'.$options['value'].'">';
	}
	
	public function getNumberField($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "number-".$uniq,
				"name" => "number-".$uniq,
				"min" => "",
				"max" => "",
				"class" => "",
				"value" => "",
		), $options );
		if(empty($options['value']) and $options['value']!="0") $options['value'] = $this->_item[$options['name']];
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		
		if($options['min']!='' or $options['min']==0){ $min = ' min="'.$options['min'].'" ';}
		if($options['max']!='' or $options['min']==0){ $max = ' max="'.$options['max'].'" ';}
		
		return '<input id="'.$options['id'].'" class="'.$options['class'].'" name="'.$options['name'].'" type="number" '.$min.$max.' value="'.$options['value'].'">';
	}
	
	public function getHiddenField($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "hidden-".$uniq,
				"name" => "hidden-".$uniq,
				"value" => "",
		), $options );
		if(empty($options['value'])) $options['value'] = $this->_item[$options['name']];
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return '<input id="'.$options['id'].'" name="'.$options['name'].'" type="hidden" value="'.$options['value'].'">';
	}
	
	public function getMediumTextField($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "textarea-".$uniq,
				"name" => "textarea-".$uniq,
				"value" => "",
		), $options );
		if(empty($options['value'])) $options['value'] = $this->_item[$options['name']];
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return '<textarea id="'.$options['id'].'" name="'.$options['name'].'" type="hidden">'.$options['value'].'</textarea>';
	}
	
	public function getBlock($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "page-controls-".$uniq,
				"name" => "text-".$uniq,
				"title" => "",
				"content" => "",
		), $options );
		
		
		if(is_array($options['content'])){$options['content'] = $this->getFormObject($options['content']);}
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return '<div class="right-block" id="'.$options['id'].'">
		<div class="controls-header">'.$options['title'].'</div>
		<div class="controls-body">
		'.$options['content'].'
		</div>
		</div>
		';
	}
	
	public function getBlockRow($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "body-row-".$uniq,
				"content" => "",
		), $options );
		
		if(is_array($options['content'])){
			$options['content'] = $this->getFormObject($options['content']);
		}
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return '<div class="controls-body-row" id="'.$options['id'].'">'.$options['content'].'</div>';
	}
	
	public function getStrongLabel($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "strong-".$uniq,
				"title" => "",
				"name" => "",
		), $options );
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return $options['title'].': <strong id="'.$options['id'].'">'.$this->_item[$options['name']].'</strong>';
	}
	
	public function getCheckSwitcher($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "check-".$uniq,
				"title" => "",
				"name" => "",
				"value" => "",
		), $options );
		if(empty($options['value'])) $options['value'] = $this->_item[$options['name']];
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return $options['title'].': <strong id="'.$options['id'].'-text">'.(($options['value'] == 1)? "Включено " : "Выключено").'</strong><div class="switch demo3"><input id="'.$options['id'].'-hidden" type="hidden" value="0" name="'.$options['name'].'"><input id="'.$options['id'].'" name="'.$options['name'].'" value="1" type="checkbox" '.(($options['value'] == 1)? "checked='checked'" : "").'><label><i></i></label></div>';
	}
	
	public function getChechbox($options, $on=0){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "check-".$uniq,
				"title" => "",
				"name" => "",
				"position" => "",
				"class" => "",
				"value" => "",
		), $options );
		if(!empty($options['position'])) $style = ' style="text-align:'.$options['position'].';"';
		if(empty($options['value'])) $options['value'] = $this->_item[$options['name']];
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return '<input id="'.$options['id'].'" name="'.$options['name'].'[]" class="ch '.$options['class'].'" value="'.$options['value'].'" type="checkbox" '.(($on == 1)? "checked='checked'" : "").' title="'.$options['title'].': '.$options['value'].'">';
	}
	
	public function getDatetField($options){
		$options['class'].= ' datepicker ';
		$this->_js.="\n$('.datepicker').datepicker({dateFormat: 'yy-mm-dd',regional: 'ru',});\n";
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return $this->getTextField($options);
	}

	public function getTimeField($options){
		$options['class'].= ' timepicker ';
		$this->_js.="\n$('.timepicker').timepicker({'timeFormat': 'H:i:s', 'step': 15 });\n";
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		return $this->getTextField($options);
	}

	public function getImageSelector($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"title" => "Header",
				"id" => "page-controls-".$uniq,
				"name" => "img",
				"url" => "",
		), $options );
		
		if(empty($options['url'])) $options['url'] = $this->_item[$options['name']];
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		$this->_js.="
			$( document ).on('click', '#".$options['id']."-unphoto', function(e){
					$('#".$options['id']."-block .img-container img').attr('src', '/img/no-photo.png');
					$('#".$options['id']."').val('');
			});
			
			$( document ).on('click', '#".$options['id']."-file-popup', function(e){
				e.preventDefault();
				openFilesPopup({
					onSelect : function(result){
						$('#".$options['id']."-block .img-container img').attr('src', result.src);
						$('#".$options['id']."').val(result.src);
					}
				});
			});";
		
		return '			<div class="right-block" id="'.$options['id'].'-block">
				<div class="controls-header">'.$options['title'].':</div>
				<div class="controls-body">
					<div id="'.$options['id'].'-file-popup" class="file-popup controls-body-row">
						<input type="hidden" name="'.$options['name'].'" id="'.$options['id'].'" value="'.$options['url'].'">
						<div class="img-container">
							<img src="'.(empty($options['url'])?'/img/no-photo.png':$options['url']).'" style="width:100%;height:auto;"/>
						</div>
					</div>
					<div class="controls-body-row"><button type="button" class="button" id="'.$options['id'].'-unphoto" >Отвязать фото</button></div>
				</div>
			</div>';
		
		
		
	}
	
	public function getTinyEditor($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "tinymce-".$uniq,
				"name" => "tinymce-".$uniq,
				"class" => "",
				"value" => "",
		), $options );
		if(empty($options['value'])) $options['value'] = $this->_item[$options['name']];
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		$this->_js.='	tinymce.init({
		selector: "#'.$options['id'].'",
		height: 300,
		language_url : "/js/tinyMce_ru.js",
		forced_root_block : false,
		force_p_newlines : false,
		force_br_newlines : true,
		browser_spellcheck : true,
		contextmenu: false,

		cleanup_on_startup: false,
		trim_span_elements: false,
		verify_html: false,
		cleanup: false,
		convert_urls: false,

		//autosave_ask_before_unload: false,
		plugins: [
			"advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker save",
			"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			"table contextmenu directionality emoticons template textcolor paste  textcolor colorpicker textpattern codesample" //fullpage
		],
		toolbar1: "newdocument | save | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect", //newdocument fullpage
		toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
		toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft | codesample | photo",
		
		save_oncancelcallback: function () { console.log("Save canceled"); },
		save_onsavecallback: function () { tinymce.triggerSave(); $("button[name=submitbtn]").click();console.log("Saved"); },
		
		convert_urls: false,
		menubar: true,
		toolbar_items_size: "small",

		style_formats: [{title: "Bold text",inline: "b"}, {title: "Red text",inline: "span",styles: {color: "#ff0000"}}, {title: "Red header",block: "h1",styles: {color: "#ff0000"}}, {title: "Example 1",inline: "span",classes: "example1"}, {title: "Example 2",inline: "span",classes: "example2"}, {title: "Table styles"}, {title: "Table row 1",selector: "tr",classes: "tablerow1"}],
		templates: [{title: "Test template 1",content: "Test 1"}, {title: "Test template 2",content: "Test 2"}],
		content_css: ["//www.tinymce.com/css/codepen.min.css"],
		/* setup: function (editor) { editor.on("change", function () { tinymce.triggerSave();}); } */
	  });';
		
		return '<div id="'.$options['id'].'-block"><div class="tabs">'.apply_filters('the_editor_tabs', '').'</div><div class="tabs-body"><textarea name="'.$options['name'].'" id="'.$options['id'].'" class="">' . $options['value'] . '</textarea></div></div>';
	}
	
	public function getJsonText($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "json-".$uniq,
				"name" => "json-".$uniq,
				"class" => "",
				"value" => "",
		), $options );
		if(empty($options['value'])) $options['value'] = $this->_item[$options['name']];
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		$value = json_decode($options['value'], true);
		$result=$this->arrayToDivBlocks($value);
		return $result;
	}
	
	private function arrayToDivBlocks($arr){
		$result="";
		if(is_array($arr)){
		foreach($arr as $key=>$value){
			if(is_array($value)){
				$result.="<div style='padding-left:0px;'><strong style='margin-left:0px;'>{$key}: </strong>".$this->arrayToDivBlocks($value)."</div>";
			}
			else{
				$result.="<div style='padding-left:5px;'>{$key}: <strong style='margin-left:0px;'>{$value}</strong></div>";
			}
		}
		}
		else{
			$result.=$arr;
		}
		return $result;
	}
	
	public function getSelectOptions($options, $selected=0){
		$row='<option value=""></option>';
		if(is_array($options)){
			foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item);  }
			foreach($options as $option){
				$data = '';
				foreach($option as $key=>$val){
					if($key!='value' and $key!='name'){$data.=" data-$key=\"$val\""; }
				}
				$row.= '<option value="'.$option['value'].'" '.($option['value']==$selected?'selected':'').' '.$data.'>'.$option['name'].'</option>';
			}
		}
		return $row;
	}
	
	public function getSelectField($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"id" => "select-".$uniq,
				"name" => "select-".$uniq,
				"class" => "",
				"values" => array(),
				"js" => "",
		), $options );
		if(!empty($options['js'])){$js=$this->prepareJs( $options['js'] );}
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		unset($name);
		$name = substr($options['name'],-2)=='[]'?substr($options['name'],0,-2):$options['name'];
		return $js.'<select id="'.$options['id'].'" class="block-select '.$options['class'].'" name="'.$options['name'].'">'.$this->getSelectOptions($options['values'], $this->_item[$name]).'</select>';
	}
	
	public function getButton($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"title" => "Save",
				"id" => "button-".$uniq,
				"name" => "button-".$uniq,
				"class" => "",
				"action" => "submit",
				"js" => "",
		), $options );
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		if(!empty($options['js'])){$js=$this->prepareJs( '$("#'.$options['id'].'").click(function(event){ if(tinymce!==undefined){tinymce.triggerSave();} '. $options['js'].'});' );}
		return $js.'<button id="'.$options['id'].'" name="'.$options['name'].'" type="'.$options['action'].'" class="button-controls '.$options['class'].'">'.$options['title'].'</button>';
	}
	
	public function getSaveButton($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"title" => "Save",
				"id" => "button-".$uniq,
				"name" => "button-".$uniq,
				"class" => "",
				"action" => "submit",
				"js" => "",
		), $options );
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		$js=$this->prepareJs( '
		$("#'.$options['id'].'").click(function(event){
			tinymce.triggerSave(); 
			var send = $("#'.$this->_id.'").serialize();
			
			send="ajax_action=update_admin_page&table='.$this->_table.'&primary_key='.$this->_primary_key.'&primary_key_value='.$this->_primary_key_value.'&add_date='.$this->_add_date.'&update_date='.$this->_update_date.'&"+send;
			
			console.log(send);
			xhr = $.ajax({
				url : "/admin/ajax/",
				type : "post",
				data : send,
				beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log("ajax is aborting");	} },
				complete: function(){ xhr = null; },
				success: function(data){
					console.log(data);
					if (IsJsonString(data)) {
						data = JSON.parse(data);
						window.location.href = "'.$this->_menu.'&id="+data["'.$this->_primary_key.'"];
					}
				},
				error: function(jqXHR, textStatus, errorThrown){ console.error(jqXHR); }
			});
		});'
		);
		
		return $js.$this->getButton($options);
	}
	
	public function getDeleteButton($options){
		$uniq=uniqid();
		$options=shortcode_atts( array(
				"title" => "Delete",
				"id" => "button-".$uniq,
				"name" => "button-".$uniq,
				"class" => "",
				"action" => "submit",
				"js" => "",
		), $options );
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		$js=$this->prepareJs( '
		$("#'.$options['id'].'").click(function(event){
			var items = [];
			$(".ch").each(function(index){if($(this).prop("checked")) items.push($(this).val());});
			send="ajax_action=delete_admin_page&table='.$this->_table.'&primary_key='.$this->_primary_key.'&items="+JSON.stringify(items);
			if(items.length > 0){
			if(confirm("Вы действительно хотите удалить "+items.length+" элементов?")){	
				xhr = $.ajax({
					url : "/admin/ajax/",
					type : "post",
					data : send,
					beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log("ajax is aborting");	} },
					complete: function(){ xhr = null; },
					success: function(data){
						console.log(data);
						window.location.reload();
					},
					error: function(jqXHR, textStatus, errorThrown){ console.error(jqXHR); }
				});
			}
			}else{
				alert("Вы ничего не выбрали!");
			}
		});'
		);
		
		return $js.$this->getButton($options);
	}
	
	
	
	
}
?>