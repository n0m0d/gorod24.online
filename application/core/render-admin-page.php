<?php
class AdminList {
	private $_model, $_multiple, $_action, $_form_start=false, $_model_where, $_model_order, $_columns, $_attrs=array(), $_controls=array(), $_items=null, $_limit = 30, $_start = 0, $_page = 1, $_tablename, $_primary, $_modelcolumns, $_count, $_form_method;

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
				$this->_model_cols = '*';
			}
			if(isset($options['items'])){
				$this->_items = $options['items'];
			}
			if(isset($options['multiple'])){
				$this->_multiple = $options['multiple'];
			}
			if(isset($options['controls'])){
				$this->_controls = $options['controls'];
			}
			if(isset($options['attrs'])){
				$this->_attrs = $options['attrs'];
			}
			if(isset($options['where'])){
				$this->_model_where = $options['where'];
			} else $this->_model_where = "1";
			if(isset($options['model_cols'])){
				$this->_model_cols = $options['model_cols'];
			} else $this->_model_cols = "*";
			if(isset($options['order'])){
				$this->_model_order = $options['order'];
			} else $this->_model_order = null;
			if(isset($options['action'])){
				$this->_action = $options['action'];
				$this->_form_start = true;
			} //else $this->_action = Registry::get('REQUEST_URI');
			if(isset($options['form_method'])){
				$this->_form_method = $options['form_method'];
			} else { $this->_form_method = 'get';}
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
		if ($this->_multiple == "true") {
			$thead .= '<th data-sortable="false" style="padding: 5px 5px;" class="maincheck-wrap"><input type="checkbox" id="maincheck"></th>';
		}
		foreach($this->_columns as $i=>$col){
			$attrs = '';
			if(is_array($col['attrs']))foreach($col['attrs'] as $key=>$val){
				$attrs .= $key.'="'.addslashes($val).'"';
			}
			$thead .= '<th '.$attrs.'>'.$col['title'].'</th>';
		}
		$thead = '<thead><tr>'.$thead.'</tr></thead>';
		if(is_null($this->_items)){
			$this->_count = $this->_model->getCountWhere($this->_model_where);
			$rows = $this->_model->getItemsWhere($this->_model_where, $this->_model_order, $this->_start, $this->_limit, $this->_model_cols);
		} else $rows = $this->_items;
		foreach($rows as $row){
			$tr = '<tr id="'.$this->_tablename.'-row-'.$row[$this->_primary].'">';
			if ($this->_multiple == "true") {
				$tr .= '<td style="padding: 5px 5px;"><input type="checkbox" name="options[]" class="check-in" value="'.$row[$this->_primary].'"></td>';
			}
			foreach($this->_columns as $col){
				if(is_callable($col["content"])){
					ob_start();
						call_user_func($col['content'],$row[$col['name']], $row);
					$cel = ob_get_clean();
				}
				else { $cel = $col["content"]; }
				$align = ($col['align']?$col['align']:'center');
				$tr .= '<td style="text-align:'.$align.';">'.$cel.'</td>';
			}
			$tr .= '</tr>';
			$tbody .= $tr;
		}
		$tbody = '<tbody>'.$tbody.'</tbody>';
		$attrs = '';
		foreach($this->_attrs as $key=>$value){
			$attrs.=$key.'="'.addslashes($value).'" ';
		}
		if(count($this->_controls)>0){
			$contols = '<div class="controls-groups">'.$this->getControl($this->_controls).'</div>';
		}
		
		if($this->_form_start) $result = AdminPage::formOpen([ "action"=>$this->_action, 'method'=>$this->_form_method ]);
		$result .= $contols.'<div class="admin-list-div-table doubleScroll" ><table '.$attrs.'>'.$thead.$tbody.'</table></div>';
		if($this->_form_start)$result .= AdminPage::formClose();

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
	
	function getControl($object){
		$content = '';
		if(is_array($object)){
			if(isAssoc($object)){
				$content.= AdminPage::getFormObject($object);
			}
			else{
				foreach($object as $item){
					$content.= AdminPage::getFormObject($item);
				}
			}
		}
		return $content;
	}
}

class AdminPage {
	private $_model=null, $_model_where=null, $_fields=null, $_item=null, $_tablename, $_primary, $_modelcolumns, $_action, $_form_start=false;

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
				$this->_form_start = true;
			} //else $this->_action = Registry::get('REQUEST_URI');
		}
	}

	public function setItem($item){
		if(is_array($item)){
			$this->_item = $item;
		}
	}

	public function get(){
		if($this->_form_start)$result = AdminPage::formOpen([ "action"=>$this->_action ]);
		if(is_array($this->_fields))foreach($this->_fields as $i => $field){
			$result .= AdminPage::getFormObject($field, $this->_item);
		}
		if($this->_form_start)$result .= AdminPage::formClose();
		return $result;

	}

	public static function getFormObject($object, $item = null){
		$content = '';
		if(is_array($object)){
			if(isAssoc($object)){
				if(!empty($object['type']))
				switch($object['type']){
					case "formOpen": { $content = AdminPage::formOpen($object); break;}
					case "formClose": { $content = AdminPage::formClose(); break;}
					case "line": { $content = AdminPage::lineField($object, $item[$object['name']]); break;}
					case "hidden": { $content = AdminPage::hiddenField($object, $item[$object['name']]); break;}
					case "text": { $content = AdminPage::textField($object, $item[$object['name']]); break;}
					case "search": { $content = AdminPage::searchTextField($object, $item[$object['name']]); break;}
					case "autocomplete": { $content = AdminPage::autocompleteField($object, $item[$object['name']]); break;}
					case "image": { $content = AdminPage::imageField($object, $item[$object['name']]); break;}
					case "audio": { $content = AdminPage::audioField($object, $item[$object['name']]); break;}
					case "file": { $content = AdminPage::fileField($object, $item[$object['name']]); break;}
					case "mediumText": { $content = AdminPage::mediumTextField($object, $item[$object['name']]); break;}
					case "editor": { $content = AdminPage::tinyEditor($object, $item[$object['name']]); break;}
					case "number": { $content = AdminPage::numberField($object, $item[$object['name']]); break;}
					case "password": { $content = AdminPage::passwordField($object, $item[$object['name']]); break;}
					case "switch": { $content = AdminPage::switchField($object, $item[$object['name']]); break;}
					case "check": { $content = AdminPage::checkField($object, $item[$object['name']]); break;}
					case "date": { $content = AdminPage::dateField($object, $item[$object['name']]); break;}
					case "time": { $content = AdminPage::timeField($object, $item[$object['name']]); break;}
					case "datetime": { $content = AdminPage::datetimeField($object, $item[$object['name']]); break;}
					case "link": { $content = AdminPage::linkField($object, $item[$object['name']]); break;}
					case "select": { $content = AdminPage::selectField($object, $item[$object['name']]); break;}
					case "button": { $content = AdminPage::buttonField($object, $item[$object['name']]); break;}
					case "submit": { $content = AdminPage::submitField($object, $item[$object['name']]); break;}
					case "filesUploader": { $content = AdminPage::filesUploaderField($object, $item[$object['name']]); break;}
					case "fileExplorer": { $content = AdminPage::getFilesSelector($object, $item[$object['name']]); break;}
					case "map": { $content = AdminPage::getMapSelector($object, $item[$object['name']]); break;}

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

	public static function formOpen($object){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'form-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['action'])) $object['action'] = Registry::get('REQUEST_URI');
		if(!isset($object['enctype'])) $object['enctype'] = 'multipart/form-data';
		if(!isset($object['method'])) $object['method'] = 'post';
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }

		return '
		<form class="sectright-filters-form '.$object['class'].'" action="'.$object['action'].'" enctype="'.$object['enctype'].'" method="'.$object['method'].'" id="'.$object['id'].'" '.$attrs.'>
		';
	}

	public static function formClose(){
		return '</form>';
	}

	public static function hiddenField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'hidden-'.$uniq;
		if(!isset($object['name'])) $object['name'] = 'hidden-'.$uniq;
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
		if(!isset($object['readonly'])) $object['readonly'] = false;
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
			<div class="sectright-filters-form-label">
				<label><span class="title">'.$object['title'].':</span>
					<input id="'.$object['id'].'" class="filter-input '.$object['class'].'" name="'.$object['name'].'" type="text" value="'.$object['value'].'" '.($object['readonly']?'readonly':'').' placeholder="'.$object['title'].'" '.$attrs.'>
				</label>
			</div>
		';
	}

	public static function searchTextField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['width'])) $object['width'] = "100%";
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
			<div class="sectright-filters-form-label" style="position:relative;width:'.$object['width'].';" >
				<label>
					<input style="width:100%;" id="'.$object['id'].'" class="filter-input '.$object['class'].'" name="'.$object['name'].'" type="text" value="'.$object['value'].'" placeholder="'.$object['title'].'" '.$attrs.'>
				</label>
				<button type="submit" class="btn btn-link" style="position:absolute; right:0px; top:0px;"><i class="fa fa-2 fa-search" aria-hidden="true"></i></button>
			</div>
		';
	}

	public static function autocompleteField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['values'])) $object['values'] = [];
		if(!isset($object['source'])) $object['source'] = '';
		if(!isset($object['label'])) $object['label'] = '';
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['multiple'])) $object['multiple'] = false;
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		if($object['multiple']){
		$result.= AdminPage::prepareJs('
		$( "#'.$object['id'].'" ).autocomplete({
			source: function( request, response ) { $.getJSON( "'.$object['source'].'", { term: extractLast( request.term ) }, response ); },
			minLength: 2,
			search: function() {
				// custom minLength
				var term = extractLast( this.value );
				if ( term.length < 2 ) {
					return false;
				}
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				console.log( "Selected: " + ui.item.id + " label: " + ui.item.value );
				
				var terms = split( this.value );
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				terms.push( "" );
				this.value = terms.join( ", " );
				return false;
				
				$( "#'.$object['id'].'-hidden" ).val(ui.item.id);
				$( "#'.$object['id'].'" ).val(ui.item.value);
				return;
			}
		});
		');
			$name = $object['name'].'[]';
			$hiddens = '';
			foreach($object['values'] as $i=>$item){
				$hiddens .= '<input id="'.$object['id'].'-'.$i.'-hidden" name="'.$name.'" type="hidden" value="'.$item.'" '.$attrs.'>';
			}
		}
		else {
		$result.= AdminPage::prepareJs('
		$( "#'.$object['id'].'" ).autocomplete({
			source: "'.$object['source'].'",
			minLength: 2,
			select: function( event, ui ) {
				console.log( "Selected: " + ui.item.id + " label: " + ui.item.value );
				$( "#'.$object['id'].'-hidden" ).val(ui.item.id);
				$( "#'.$object['id'].'" ).val(ui.item.value);
				return;
			}
		});
		');
			
			$name = $object['name'];
			$hiddens = '<input id="'.$object['id'].'-hidden" name="'.$name.'" type="hidden" value="'.$object['value'].'" '.$attrs.'>';
			
		}
		
		$result.= '
			<div class="sectright-filters-form-label">
				<label><span class="title">'.$object['title'].':</span>
					<div class="hidden '.$object['id'].'-hiddens">
						'.$hiddens.'
					</div>
					<input id="'.$object['id'].'" class="filter-input '.$object['class'].'" name="'.$object['name'].'-input"  type="text" value="'.$object['label'].'" placeholder="'.$object['title'].'" '.$attrs.'>
				</label>
			</div>
		';
		return $result;
	}

	public static function fileField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['accept'])) $object['accept'] = '';
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
			<div class="sectright-filters-form-label">
				<label><span class="title">'.$object['title'].':</span>
					<input id="'.$object['id'].'" class="filter-input '.$object['class'].'" name="'.$object['name'].'" type="file" accept="'.$object['accept'].'" '.$attrs.'>
				</label>
			</div>
		';
	}

	public static function imageField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = '';
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['width'])) $object['width'] = "auto";
		if(!isset($object['height'])) $object['height'] = "auto";
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
			<div class="sectright-filters-form-label">
				<label><span class="title">'.$object['title'].':</span>
					<input id="'.$object['id'].'-hidden" type="hidden" value="'.$object['value'].'" name="'.$object['name'].'" />
					<img id="'.$object['id'].'" class="filter-input '.$object['class'].'" width="'.$object['width'].'" height="'.$object['height'].'" src="'.$object['value'].'" '.$attrs.' />
				</label>
			</div>
		';
	}

	public static function audioField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
			<div class="sectright-filters-form-label">
				<label><span class="title">'.$object['title'].':</span>
					<audio id="'.$object['id'].'" class="filter-input '.$object['class'].'" src="'.$object['value'].'" controls '.$attrs.'>
				</label>
			</div>
		';
	}

	public static function lineField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'text-'.$uniq;
		if(!isset($object['content'])) $object['content'] = '';
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
			<div class="sectright-filters-form-label">
				<p id="'.$object['id'].'" class="filter-input '.$object['class'].'" '.$attrs.'>
					'.$object['content'].'
				</p>
			</div>
		';
	}

	public static function mediumTextField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'medium-text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'medium-text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
			<div class="sectright-filters-form-label">
				<label><span class="title">'.$object['title'].':</span>
					<textarea id="'.$object['id'].'" class="filter-input '.$object['class'].'" name="'.$object['name'].'" placeholder="'.$object['title'].'" '.$attrs.'>'.$object['value'].'</textarea>
				</label>
			</div>
		';
	}

	public static function tinyEditor($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'editor-text-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'editor-text-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['height'])) $object['height'] = '300';
		if(!isset($object['css'])) $object['css'] = [ "//www.tinymce.com/css/codepen.min.css" ];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }

		$result.= AdminPage::prepareJs('
		tinymce.execCommand("mceRemoveControl", true, "#'.$object['id'].'");
		tinymce.init({
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

		autosave_ask_before_unload: false,
		plugins: [
			"advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker save",
			"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			"table contextmenu directionality emoticons template textcolor paste  textcolor colorpicker textpattern codesample photo" //fullpage
		],
		toolbar1: "newdocument | save | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect", //newdocument fullpage
		toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
		toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft | codesample | photo gallery",

		save_oncancelcallback: function () { console.log("Save canceled"); },
		save_onsavecallback: function () { tinymce.triggerSave(); $("button[name=submitbtn]").click();console.log("Saved"); },

		convert_urls: false,
		menubar: true,
		toolbar_items_size: "small",

		style_formats: [{title: "Bold text",inline: "b"}, {title: "Red text",inline: "span",styles: {color: "#ff0000"}}, {title: "Red header",block: "h1",styles: {color: "#ff0000"}}, {title: "Example 1",inline: "span",classes: "example1"}, {title: "Example 2",inline: "span",classes: "example2"}, {title: "Table styles"}, {title: "Table row 1",selector: "tr",classes: "tablerow1"}],
		templates: [{title: "Test template 1",content: "Test 1"}, {title: "Test template 2",content: "Test 2"}],
		content_css: '.json_encode($object['css']).',
		/* setup: function (editor) { editor.on("change", function () { tinymce.triggerSave();}); } */
	  });');

		return $result.'
		<div class="sectright-filters-form-label">
			<span class="title">'.$object['title'].':</span>
			<div class="tiny-editor" id="'.$object['id'].'-block"><textarea name="'.$object['name'].'" id="'.$object['id'].'" class="'.$object['class'].'">'.$object['value'].'</textarea></div>
		</div>
		';
	}

	public static function numberField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'number-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'number-'.$uniq;
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

	public static function passwordField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'password-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'password-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		return '
			<div class="sectright-filters-form-label">
				<label><span class="title">'.$object['title'].':</span>
					<input id="'.$object['id'].'" class="filter-input '.$object['class'].'" name="'.$object['name'].'" type="password" value="'.$object['value'].'" placeholder="'.$object['title'].'" '.$attrs.'>
				</label>
			</div>
		';
	}

	public static function switchField($object, $value=0){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'switch-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'switch-'.$uniq;
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
		if(!isset($object['id'])) $object['id'] = 'check-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'check-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['checked'])) $object['checked'] = false;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }

		return '
		<div class="sectright-filters-form-label">
			<label><span class="title">'.$object['title'].':</span>
				<input id="'.$object['id'].'" name="'.$object['name'].'" class="ch '.$object['class'].'" value="'.$object['value'].'" type="checkbox" '.(($object['value'] == 1 OR $object['checked'])? "checked='checked'" : "").' title="'.$object['title'].': '.$object['value'].'" '.$attrs.'>
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
		if(!isset($object['minuteGrid'])) $object['minuteGrid'] = 0;
		if(!isset($object['stepMinute'])) $object['stepMinute'] = 1;
		if(!isset($object['showMinute'])) $object['showMinute'] = true;
		if(!isset($object['showSecond'])) $object['showSecond'] = true;
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		$result.= AdminPage::prepareJs('$("#'.$object['id'].'").timepicker({
			timeFormat: "'.$object['format'].'",
			'.($object['minuteGrid']?"minuteGrid: {$object['minuteGrid']},":'').'
			'.($object['stepMinute']?"stepMinute: {$object['stepMinute']},":'').'
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
		if(!isset($object['id'])) $object['id'] = 'datetime-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'datetime-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['format'])) $object['format'] = 'hh:mm:ss';
		if(!isset($object['minuteGrid'])) $object['minuteGrid'] = 0;
		if(!isset($object['stepMinute'])) $object['stepMinute'] = 1;
		if(!isset($object['showMinute'])) $object['showMinute'] = true;
		if(!isset($object['showSecond'])) $object['showSecond'] = true;
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		$result.= AdminPage::prepareJs('$("#'.$object['id'].'").datetimepicker({
			timeFormat: "'.$object['format'].'",
			'.($object['minuteGrid']?"minuteGrid: {$object['minuteGrid']},":'').'
			'.($object['stepMinute']?"stepMinute: {$object['stepMinute']},":'').'
			showMinute: '.($object['showMinute']?'true':'false').',
			showSecond: '.($object['showSecond']?'true':'false').',
			showMillisec: false,
			showMicrosec: false,
			showTimezone: false,
			oneLine: true,
			});');
		return $result.AdminPage::textField($object, $value);
	}

	public static function selectField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'select-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'select-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['items'])) $object['items'] = [];
		if(!isset($object['null'])) $object['null'] = false;
		if(!isset($object['width'])) $object['width'] = "100%";
		if(!isset($object['multiple'])) $object['multiple'] = false;
		if(!isset($object['compact'])) $object['compact'] = false;
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		$result.= AdminPage::prepareJs('InitChosen($("#'.$object['id'].'"));');
		$options = '';
		if($object['null']) { $options .= '<option value="">Не выбрано</option>'; }
		
		foreach($object['items'] as $item){
			if($item['items']){
				$options .= '<optgroup label="'.$item['label'].'">';
				foreach($item['items'] as $sub_item){
					
					
					$value = (isset($sub_item['value'])?$sub_item['value']:null);
					$label = (isset($sub_item['label'])?$sub_item['label']:null);
					if(!$object['multiple']){
						$selected = ($value == $object['value']) ? "selected='selected'" : "";
					}
					else {
						if(is_array($object['value'])){
							$selected = (in_array($value, $object['value'])) ? "selected='selected'" : "";
						}
					}
					$i=0; $opt_attrs = '';
					foreach($sub_item as $key=>$val){
						if(is_null($value) and $i==0){ $value=$val;}
						if(is_null($label) and $i==1){ $label=$val;}
						if($i>1){$opt_attrs.=' data-'.$key.'="'.addslashes($val).'"';}
						$i++;
					}
					$options .= '<option value="'.$value.'" '.$opt_attrs.' '.$selected.'>'.$label.'</option>';
					
					
				}
				$options .= '</optgroup>';
			}
			else {
				$value = (isset($item['value'])?$item['value']:null);
				$label = (isset($item['label'])?$item['label']:null);
				if(!$object['multiple']){
					$selected = ($value == $object['value']) ? "selected='selected'" : "";
				}
				else {
					if(is_array($object['value'])){
						$selected = (in_array($value, $object['value'])) ? "selected='selected'" : "";
					}
				}
				$i=0; $opt_attrs = '';
				foreach($item as $key=>$val){
					if(is_null($value) and $i==0){ $value=$val;}
					if(is_null($label) and $i==1){ $label=$val;}
					if($i>1){$opt_attrs.=' data-'.$key.'="'.addslashes($val).'"';}
					$i++;
				}
				$options .= '<option value="'.$value.'" '.$opt_attrs.' '.$selected.'>'.$label.'</option>';
			}
		}
		if(!$object['compact']){
		return $result.'
			<div class="sectright-filters-form-label" style="display:inline-block;margin:10px 0px;width:'.$object['width'] .';">
				<label><span class="title">'.$object['title'].':</span>
					<select id="'.$object['id'].'" class="filter-input '.$object['class'].'" name="'.$object['name'].($object['multiple']?'[]':'').'" value="'.$object['value'].'" '.($object['multiple']?'multiple':'').' placeholder="'.$object['title'].'" '.$attrs.'>
						'.$options.'
					</select>
				</label>
			</div>
		';
		}
		else {
		return $result.'
			<div style="display:inline-block;margin:10px 0px;width:'.$object['width'] .';">
				<select id="'.$object['id'].'" class="filter-input '.$object['class'].'" name="'.$object['name'].($object['multiple']?'[]':'').'" value="'.$object['value'].'" '.($object['multiple']?'multiple':'').' placeholder="'.$object['title'].'" '.$attrs.'>
						'.$options.'
				</select>
			</div>
		';
		}
	}

	public static function linkField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'button-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['href'])) $object['href'] = '#';
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['button-type'])) $object['button-type'] = 'default'; // default, primary, secondary, success, danger, warning, info, light, dark, link || outline-*
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['onClick'])) $object['onClick'] = false;
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		if($object['onClick']){
			$result.= AdminPage::prepareJs('$("#'.$object['id'].'").click(function(event){
				'.$object['onClick'].'
			});');
		}
		return $result.'
			<a id="'.$object['id'].'" class="btn btn-'.$object['button-type'].' '.$object['class'].'" href="'.$object['href'].'" '.$attrs.'>'.$object['title'].'</a>
		';
	}

	public static function buttonField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'button-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'button-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['button-type'])) $object['button-type'] = 'default'; // default, primary, secondary, success, danger, warning, info, light, dark, link || outline-*
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['onClick'])) $object['onClick'] = false;
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		if($object['onClick']){
			$result.= AdminPage::prepareJs('$("#'.$object['id'].'").click(function(event){
				'.$object['onClick'].'
			});');
		}
		return $result.'
			<button id="'.$object['id'].'" class="btn btn-'.$object['button-type'].' '.$object['class'].'" name="'.$object['name'].'" type="button" value="'.$object['value'].'" '.$attrs.'>'.$object['title'].'</button>
		';
	}

	public static function submitField($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'button-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		//if(!isset($object['name'])) $object['name'] = 'button-'.$uniq;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['button-type'])) $object['button-type'] = 'default'; // default, primary, secondary, success, danger, warning, info, light, dark, link || outline-*
		if(!isset($object['value'])) $object['value'] = $value;
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';

		return $result.'
			<button id="'.$object['id'].'" class="btn btn-'.$object['button-type'].' '.$object['class'].'" '.((isset($object['name']))?'name="'.$object['name'].'"':'').' type="submit" value="'.$object['value'].'" '.$attrs.'>'.$object['title'].'</button>
		';
	}

	public static function filesUploaderField($object){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'button-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['url'])) $object['url'] = '';
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['filters'])) $object['filters'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		$result.= AdminPage::prepareJs('
    $("#'.$object['id'].'").plupload({
        // General settings
        runtimes : "html5,flash,silverlight,html4",
        url : "'.$object['url'].'",
		multipart_params: {
			"ajax_action" : "action_upload"
		},
        // Maximum file size
        max_file_size : "100mb",
        chunk_size: "1mb",
        // Specify what files to browse for
        filters : '.json_encode($object['filters']).',
        // Rename files by clicking on their titles
        rename: true,
		//unique_names: true,
        sortable: true,  // Sort files
        dragdrop: true, // Enable ability to drag\'n\'drop files onto the widget (currently only HTML5 supports that)
        // Views to activate
        views: {list: true,thumbs: true,active: "thumbs"},
        flash_swf_url : "/admin/application/views/js/plupload/Moxie.swf", // Flash settings
        silverlight_xap_url : "/admin/application/views/js/plupload/Moxie.xap", // Silverlight settings
		uploaded: function(event, up){
			console.log(up.file);
			var file = up.file;
			console.log(up.result.response);
			var response = JSON.parse(up.result.response);
			console.log(response);
			if(response.OK == 1){
				$("#'.$object['id'].'-uploaded-files table tbody").append("<tr><td><b>" + response.name + "</b><div>Полный путь: <a href=\"" + response.url + "\" target=\"_blank\">" + response.url + "</a></div></td><td><b>" + file.size + "</b></td><td><b>" + file.type + "</b></td></tr>");
			}
			else{
				$("#'.$object['id'].'-uploaded-files table tbody").append("<tr><td><b>" + response.name + "</b><div>ОШИБКА: <span>" + response.info + "</span></div></td><td><b>NaN</b></td><td><b>NaN</b></td></tr>");
			}
		},

		complete: function(uploader, files){
			//console.log(arguments);
		}
    });

		');
		return $result.'
		<div class="">
			<span class="title">'.$object['title'].':</span>
				<div id="'.$object['id'].'" class="'.$object['class'].'" '.$attrs.'>
					<p>Your browser doesn\'t have Flash, Silverlight or HTML5 support.</p>
				</div>

				<div id="'.$object['id'].'-uploaded-files">
					<table class="main-table">
					<thead>
						<tr>
							<td>Название файла</td>
							<td style="width:200px;">Размер</td>
							<td style="width:200px;">Тип</td>
						</tr>
					</thead>
					<tbody></tbody>
					</table>
				</div>

		</div>
		';
	}

	public static function getFilesSelector($object, $value=null){
		$uniq=uniqid();
		//print_r($object);
		if(!isset($object['id'])) $object['id'] = 'explorer_'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = 'img';
		if(!isset($object['url'])) $object['url'] = '';
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['attrs'])) $object['attrs'] = [];
		if(!isset($object['accept'])) $object['accept'] = '*';
		if(!isset($object['multi'])) $object['multi'] = false;
		if(!isset($object['src'])) $object['src'] = false;
		if(!isset($object['crop'])) $object['crop'] = false;
		if(!isset($object['ratio'])) $object['ratio'] = false;
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		$type = 'image';
		if($value and !$object['src']){
			$uploads = new model_uploads();
			$upload = $uploads->getItem($value);
			$object['url'] = $upload['destination'].$upload['name'];
			$type = $upload['type'];
		}
		if($object['src']){
			$object['url'] = $value;
		}
		/*
		if(empty($options['url'])) $options['url'] = $this->_item[$options['name']];
		foreach($options as $key=>$val){ $options[$key] = $this->textMatch($val, $this->_item); }
		*/
		$result.= AdminPage::prepareJs("
			$( document ).on('click', '#".$object['id']."-unphoto', function(e){
				$('#".$object['id']."-block .img-container').empty();
				$('#".$object['id']."-block .img-container').html('<img class=\"first\" src=\"/admin/application/views/img/search.png\"  /><div class=\"title\"></div>');
				$('#".$object['id']."').val(''); $('#".$object['id']."-src').val('');
			});
			
			".($object['crop']?"
			var _parent = $('#".$object['id']."-block .img-container');
			
			var crop".$object['id']." = $('#".$object['id']."-block .img-container img.crop').imgAreaSelect({
				".($object['ratio']?"aspectRatio : '{$object['ratio']}', ":"")."
				instance: true,
				parent: _parent,
				handles: true,
				setOptions:true,
				onSelectEnd: function(img, selection){ },
			});
			
			$( document ).on('click', '#".$object['id']."-crop', function(e){
				var i = $('#".$object['id']."-block .img-container img.crop').get(0);
				var selection = crop".$object['id'].".getSelection(true);
				
				var porcX = (i.naturalWidth / i.width);
				var porcY = (i.naturalHeight / i.height);
				
				var image = {
					'src' : $(i).attr('src'),
					'x1' : Math.round(selection.x1 * porcX),
					'y1' : Math.round(selection.y1 * porcX),
					'x2' : Math.round(selection.x2 * porcX),
					'y2' : Math.round(selection.y2 * porcX),
					'w' : Math.round(selection.width * porcX),
					'h' : Math.round(selection.height * porcY),
				}
				
				xhr = $.ajax({
					url : '/admin/ajax/cropImageSrc',
					type : 'post',
					dataType: 'json',
					data : { 'image':image },
					beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
					complete: function(){ xhr = null; },
					success: function(data){
							$('#".$object['id']."-block .img-container').empty();
							$('#".$object['id']."-block .img-container').html('<img class=\"".($object['crop']?"crop":"")."\" src=\"'+data.url+'\" /><div class=\"title\"></div>');
							$('#".$object['id']."').val(data.id); $('#".$object['id']."-src').val(data.url);
			
							".($object['crop']?"
							var _parent = $('#".$object['id']."-block .img-container');
							
							crop".$object['id']." = $('#".$object['id']."-block .img-container img.crop').imgAreaSelect({
								".($object['ratio']?"aspectRatio : '{$object['ratio']}', ":"")."
								instance: true,
								parent: _parent,
								handles: true,
								setOptions:true,
								onSelectEnd: function(img, selection){ },
							});
							":"")."
					}
				});
				
			});
			
			":"")."
			
			$( document ).on('click', '#".$object['id']."-select', function(e){
				e.preventDefault();
				openFilesPopup({
					multi : '{$object['multi']}',
					accept : '{$object['accept']}',
					onSelect : function(result){
						".($object['multi']?"
						
						result.forEach(function(item, i, result) {
							var cols = 3;
							if (result.length > 8) {
								cols = 2;
							} else if (result.length > 16) {
								cols = 1;
							}
							switch(item.type){
								case 'image':
									$('#".$object['id']."-block .img-container .first').remove();
									$('#".$object['id']."-block .img-container').append('<div class=\"col-md-'+cols+'\"><input type=\"hidden\" name=\"file_id[]\" id=\"'+item.name+'\" value=\"'+item.id+'\"><input type=\"hidden\" name=\"src-file_id[]\" id=\"'+item.name+'-src\" value=\"'+item.src+'\"><input type=\"hidden\" name=\"assoc-file_id['+item.id+']\" id=\"'+item.name+'-assoc\" value=\"'+item.src+'\"><img class=\"".($object['crop']?"crop":"")."\" src=\"'+item.src+'\" /><div class=\"title\">'+item.name+'</div></div>');
									$('#".$object['id']."').val(item.id); $('#".$object['id']."-src').val(item.src);
					
									".($object['crop']?"
									var _parent = $('#".$object['id']."-block .img-container');
									
									crop".$object['id']." = $('#".$object['id']."-block .img-container img.crop').imgAreaSelect({
										".($object['ratio']?"aspectRatio : '{$object['ratio']}', ":"")."
										instance: true,
										parent: _parent,
										handles: true,
										setOptions:true,
										onSelectEnd: function(img, selection){ },
									});
									":"")."
								break;
								/*case 'audio':
									$('#".$object['id']."-block .img-container img').hide().remove();
									$('#".$object['id']."-block .img-container').html('<div class=\"audio-icon-selector\"><div><i class=\"fa fa-file-audio-o fa-5\" aria-hidden=\"true\"></i></div><audio style=\"width:150px;\" controls src=\"'+item.src+'\"></audio><div class=\"title\">'+item.name+'</div></div>');
									$('#".$object['id']."').val(item.id); $('#".$object['id']."-src').val(item.src);
								break;*/
							}
						});
						
						":"
						
						switch(result.type){
							case 'image':
							$('#".$object['id']."-block .img-container').empty();
							$('#".$object['id']."-block .img-container').html('<img class=\"".($object['crop']?"crop":"")."\" src=\"'+result.src+'\" /><div class=\"title\">'+result.name+'</div>');
							$('#".$object['id']."').val(result.id); $('#".$object['id']."-src').val(result.src);
			
							".($object['crop']?"
							var _parent = $('#".$object['id']."-block .img-container');
							
							crop".$object['id']." = $('#".$object['id']."-block .img-container img.crop').imgAreaSelect({
								".($object['ratio']?"aspectRatio : '{$object['ratio']}', ":"")."
								instance: true,
								parent: _parent,
								handles: true,
								setOptions:true,
								onSelectEnd: function(img, selection){ },
							});
							":"")."
							
								break;
							case 'audio':
							$('#".$object['id']."-block .img-container img').hide().remove();
							$('#".$object['id']."-block .img-container').html('<div class=\"audio-icon-selector\"><div><i class=\"fa fa-file-audio-o fa-5\" aria-hidden=\"true\"></i></div><audio style=\"width:150px;\" controls src=\"'+result.src+'\"></audio><div class=\"title\">'+result.name+'</div></div>');
							$('#".$object['id']."').val(result.id); $('#".$object['id']."-src').val(result.src);
							break;
						}
						
						")."
					}
				});
			});");
		switch($type){
			case 'image' : $content = '<img class="first'.($object['crop']?"crop":"").'" src="'.(empty($object['url'])?'/admin/application/views/img/search.png':$object['url']).'" />'; break;
			case 'audio' : $content = '<div class="audio-icon-selector"><div><i class="fa fa-file-audio-o fa-5" aria-hidden="true"></i></div><audio style="width:150px;" controls src="'.$object['url'].'"></audio><div class="title">'.$upload['original_name'].'</div></div>'; break;
		}

		return $result.'
		<div class="sectright-filters-form-label">
		
			<span class="title">'.$object['title'].':</span>
			<div class="explorer-block" id="'.$object['id'].'-block">
				<div class="controls-header">'.$object['title'].':</div>
				<div class="controls-body">
					<div id="'.$object['id'].'-file-popup" class="file-popup controls-body-row">
						'.($object['multi']?'
							<div class="img-container">
								'.$content.'
							</div>
						':'
							<input type="hidden" name="'.$object['name'].'" id="'.$object['id'].'" value="'.$value.'">
							<input type="hidden" name="src-'.$object['name'].'" id="'.$object['id'].'-src" value="'.$object['url'].'">
							<div class="img-container">
								'.$content.'
							</div>
						').'
					</div>
					<div class="controls-body-row">
						<button type="button" class="btn btn-success" id="'.$object['id'].'-select" >Выбрать</button>
						<button type="button" class="btn btn-info" id="'.$object['id'].'-unphoto" >Отвязать</button>
						'.($object['crop']?'<button type="button" class="btn btn-warning" id="'.$object['id'].'-crop" >Вырезать</button>':'').'
					</div>
				</div>
			</div>
		
		</div>
			
			';



	}

	public static function getMapSelector($object, $value=null){
		$uniq=uniqid();
		if(!isset($object['id'])) $object['id'] = 'button-'.$uniq;
		if(!isset($object['title'])) $object['title'] = '';
		if(!isset($object['name'])) $object['name'] = '';
		if(!isset($object['longitude'])) $object['longitude'] = null;
		if(!isset($object['latitude'])) $object['latitude'] = null;
		if(!isset($object['address'])) $object['address'] = null;
		if(!isset($object['class'])) $object['class'] = '';
		if(!isset($object['attrs'])) $object['attrs'] = [];
		$attrs = ''; foreach($object['attrs'] as $key=>$val){ $attrs .= $key.'="'.addslashes($val).'" '; }
		$result = '';
		$result.= AdminPage::prepareCss("#{$object['id']}{ width:100%;	height:400px; margin:20px 0px; }");
		$result.= AdminPage::prepareJs('
var $longitude = '.(!empty($object['longitude'])?$object['longitude']:'null').';
var $latitude = '.(!empty($object['latitude'])?$object['latitude']:'null').';
var $addres = "'.$object['address'].'";
var myMap, myPlacemark=null, hidden, visible;
	
ymaps.ready(init);
	function init() {
		// Создание экземпляра карты.
		myMap = new ymaps.Map("'.$object['id'].'", {
				center: [45.031929, 35.382429],
				zoom: 13
			});
		myMap.controls.remove("searchControl");
		myMap.controls.remove("geolocationControl");
		myMap.controls.remove("trafficControl");
		myMap.controls.remove("typeSelector");
		
		hidden = new ymaps.GeoObjectCollection();
		visible = new ymaps.GeoObjectCollection();
		
		if($longitude==null || $latitude==null){
			//console.log($addres);
			if($addres!=""){
			var myGeocoder = ymaps.geocode($addres, {"results":1});
			myGeocoder.then(
				function (res) {
					//myMap.geoObjects.add(res.geoObjects);
					var $new_longitude = res.geoObjects.get(0).geometry.getCoordinates()[0]; $("#'.$object['id'].'-longitude").val($new_longitude);
					var $new_latitude = res.geoObjects.get(0).geometry.getCoordinates()[1];  $("#'.$object['id'].'-latitude").val($new_latitude);
					// Выведем в консоль данные, полученные в результате геокодирования объекта.
					//console.log("Координаты объекта :" + res.geoObjects.get(0).geometry.getCoordinates());
					myMap.setCenter([$new_longitude, $new_latitude], 18, { checkZoomRange: true});
					// Задает опции метки и отображает метку на карте.
					myPlacemark = new ymaps.Placemark([$new_longitude, $new_latitude],{}, {preset: "twirl#redIcon", draggable: true});
					//Отслеживаем событие перемещения метки
					myPlacemark.events.add("dragend", function (e) {			
						var coords = this.geometry.getCoordinates();
						var new_coords = [coords[0].toFixed(6), coords[1].toFixed(6)];
						$("#'.$object['id'].'-longitude").val(new_coords[0]);
						$("#'.$object['id'].'-latitude").val(new_coords[1]);
						//console.log(new_coords);
						changeAdress([new_coords[0],new_coords[1]]);
					}, myPlacemark);
					// Добавление метки на карту
					visible.add(myPlacemark);
				},
				function (err) {
					// обработка ошибки
				}
			);
			}
		}
		else{
			myMap.setCenter([$longitude, $latitude], 18, { checkZoomRange: true});
					// Задает опции метки и отображает метку на карте.
					myPlacemark = new ymaps.Placemark([$longitude, $latitude],{}, {preset: "twirl#redIcon", draggable: true});
					//Отслеживаем событие перемещения метки
					myPlacemark.events.add("dragend", function (e) {			
						var coords = this.geometry.getCoordinates();
						var new_coords = [coords[0].toFixed(6), coords[1].toFixed(6)];
						$("#'.$object['id'].'-longitude").val(new_coords[0]);
						$("#'.$object['id'].'-latitude").val(new_coords[1]);
						//console.log(new_coords);
						changeAdress([new_coords[0],new_coords[1]]);
					}, myPlacemark);
					// Добавление метки на карту
					visible.add(myPlacemark);
		}
		myMap.geoObjects.add(visible);
	}
	
	
	function changeAdress(myCoords){
		return true;
		var myGeocoder = ymaps.geocode(myCoords, {kind: "house"});
		myGeocoder.then(
			function (res) {
				var nearest = res.geoObjects.get(0);
				var name = nearest.properties.get("name");
				$("#adr").val(name);
				//console.log(name);
			},
			function (err) {
				alert("Ошибка");
			}
		);
	}
	
	$("#'.$object['id'].'-clearMap").click(function(){
		visible.removeAll();
	});
	
	$("#'.$object['id'].'-setPointXY").click(function(){
		visible.removeAll();
		var _longitude = $("#'.$object['id'].'-longitude").val();
		var _latitude = $("#'.$object['id'].'-latitude").val();
		$("#latitude").val();
		myMap.setCenter([_longitude, _latitude], 18, { checkZoomRange: true});
		myPlacemark = new ymaps.Placemark([_longitude, _latitude],{}, {preset: "twirl#redIcon", draggable: true});
		// Добавление метки на карту
		myPlacemark.events.add("dragend", function (e) {			
						var coords = this.geometry.getCoordinates();
						var new_coords = [coords[0].toFixed(6), coords[1].toFixed(6)];
						$("#'.$object['id'].'-longitude").val(new_coords[0]);
						$("#'.$object['id'].'-latitude").val(new_coords[1]);
						//console.log(new_coords);
						changeAdress([new_coords[0],new_coords[1]]);
					}, myPlacemark);
		visible.add(myPlacemark);			
	});
	
	$("#'.$object['id'].'-setPointadres").click(function(){
		visible.removeAll();
		$address=$("#'.$object['id'].'-address").val();
		if($address!=""){
			var myGeocoder = ymaps.geocode($address, {"results":1});
			myGeocoder.then(
				function (res) {
					//myMap.geoObjects.add(res.geoObjects);
					var $new_longitude = res.geoObjects.get(0).geometry.getCoordinates()[0]; $("#'.$object['id'].'-longitude").val($new_longitude);
					var $new_latitude = res.geoObjects.get(0).geometry.getCoordinates()[1];  $("#'.$object['id'].'-latitude").val($new_latitude);
					// Выведем в консоль данные, полученные в результате геокодирования объекта.
					//console.log("Координаты объекта :" + res.geoObjects.get(0).geometry.getCoordinates());
					myMap.setCenter([$new_longitude, $new_latitude], 18, { checkZoomRange: true});
					// Задает опции метки и отображает метку на карте.
					myPlacemark = new ymaps.Placemark([$new_longitude, $new_latitude],{}, {preset: "twirl#redIcon", draggable: true});
					//Отслеживаем событие перемещения метки
					myPlacemark.events.add("dragend", function (e) {			
						var coords = this.geometry.getCoordinates();
						var new_coords = [coords[0].toFixed(6), coords[1].toFixed(6)];
						$("#'.$object['id'].'-longitude").val(new_coords[0]);
						$("#'.$object['id'].'-latitude").val(new_coords[1]);
						//console.log(new_coords);
						changeAdress([new_coords[0],new_coords[1]]);
					}, myPlacemark);
					// Добавление метки на карту
					visible.add(myPlacemark);
				},
				function (err) {
					// обработка ошибки
				}
			);		
		}
		else{
			alert("Поле адреса пустое!");
		}
	});

		');
		return $result.'
		<div class="">
			<span class="title">'.$object['title'].':</span>
			
			<div class="sectright-filters-form-label">
				<label><span class="title">Долгота:</span>
					<input id="'.$object['id'].'-longitude" class="filter-input" name="'.$object['name'].'-longitude" type="text" value="'.$object['longitude'].'" '.($object['readonly']?'readonly':'').' placeholder="Долгота">
				</label>
			</div>
			<div class="sectright-filters-form-label">
				<label><span class="title">Широта:</span>
					<input id="'.$object['id'].'-latitude" class="filter-input" name="'.$object['name'].'-latitude" type="text" value="'.$object['latitude'].'" '.($object['readonly']?'readonly':'').' placeholder="Широта">
				</label>
			</div>
			<div class="sectright-filters-form-label">
				<label><span class="title">Адрес:</span>
					<input id="'.$object['id'].'-address" class="filter-input" name="'.$object['name'].'-address" type="text" value="'.$object['address'].'" '.($object['readonly']?'readonly':'').' placeholder="Адрес">
				</label>
			</div>
			
			<div style="margin:20px 0px;">
				<button id="'.$object['id'].'-clearMap" class="btn btn-danger" type="button">Очистить карту</button>
				<button id="'.$object['id'].'-setPointXY" class="btn btn-primary" type="button">Поставить точку по координатам</button>
				<button id="'.$object['id'].'-setPointadres" class="btn btn-warning" type="button">Поставить точку по адресу</button>
			</div>
			
			<div id="'.$object['id'].'" class="'.$object['class'].'" '.$attrs.'>
				
			</div>
		</div>
		';
	}
	
	public function __toString(){
		return $this->get();
	}
}

?>