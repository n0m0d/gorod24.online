<?php

class View
{
    protected $_view = null;
    protected $_template = null;
    protected $_template_folder = null;
    protected $_template_extension = 'html';
    protected $_file = null;
    protected $_head = '';
    protected $_footer = '';
    protected $_head_file = null;
    protected $_footer_file = null;
    protected $_auto_render = true;
    public $_controller = null;
    public $_parent_view = null;
    
    public $data = array();
    public $headers = array();

	function __construct($path=null, $template=null){
		$this->_template = (!is_null($GLOBALS['CONFIG']['TEMPLATE'])?$GLOBALS['CONFIG']['TEMPLATE']:null);
		$this->_template_extension = (!is_null($GLOBALS['CONFIG']['TEMPLATE_EXTENSION'])?$GLOBALS['CONFIG']['TEMPLATE_EXTENSION']:null);
		$this->_template = (!is_null($template)?$template:$this->_template);
		$this->_template_folder = APPDIR."/application/views/".(!empty($this->_template)?$this->_template.'/':null);
		
		$this->data = array();
		$this->headers = array();
		
		$this->setView($path);
	}
	
	function __destruct(){
		
	}
	
	public function isRender(){
		return $this->_auto_render;
	}
	
	public function notRender(){
		$this->_auto_render = false;
		return $this;
	}
	
	public function yesRender(){
		$this->_auto_render = true;
		return $this;
	}
	
	public function setView($file=''){
		$this->_view = $file;
		return $this;
	}
	
	public function setTemplatesFolder($src=''){
		if(is_dir($src)){
			$this->_template_folder = $src;
		}
		else {
			die('Templates folder not found');
		}
		return $this;
	}
	
	public function includeView($file, $fullpath=false){
		$src = ($fullpath?$file:$this->_template_folder.$file);
		if(file_exists($src)){
			include $src;
		}
		else { return false; }
	}
	
	public function setHeader($html=''){
		$this->_head = $html;
		return $this;
	}
	
	public function setFooter($html=''){
		$this->_footer = $html;
		return $this;
	}
	
	public function setHeaderView($tpl, $fullpath=false){
		$src = ($fullpath?$tpl:$this->_template_folder.$tpl);
		if(file_exists($src)){
			$this->_head_file = $src;
		}
		else { return false; }
	}
	
	public function setFooterView($tpl, $fullpath=false){
		$src = ($fullpath?$tpl:$this->_template_folder.$tpl);
		if(file_exists($src)){
			$this->_footer_file = $src;
		}
		else { return false; }
	}
	
	public function renderHeader(){
		if(file_exists($this->_template_folder.'header.'.$this->_template_extension)){
			$this->_head_file = $this->_template_folder.'header.'.$this->_template_extension;
		}
		if(!empty($this->_head)){ echo $this->_head; } else {
			if(!empty($this->_head_file)){
				include $this->_head_file;
			}
			else { return ''; }
		}
	}
	
	public function getHeader(){
        ob_start();
        include $this->renderHeader();
        return ob_get_clean();
	}
	
	public function renderFooter(){
		if(file_exists($this->_template_folder.'footer.'.$this->_template_extension)){
			$this->_footer_file = $this->_template_folder.'footer.'.$this->_template_extension;
		}
		if(!empty($this->_footer)){ echo $this->_footer; } else {
			if(!empty($this->_footer_file)){
				include $this->_footer_file;
			}
			else { return ''; }
		}
	}
	
	public function getFooter(){
        ob_start();
        include $this->renderHeader();
        return ob_get_clean();
	}
	
    public function getBody() {
        ob_start();
        include $this->renderBody();
        return ob_get_clean();
    }
	
    public function renderBody() {
		//echo $this->_template_folder.$this->_view;
		if (!file_exists($this->_template_folder.$this->_view)){
            die("View not found :{$this->_template_folder}{$this->_view}");
        }
		else {
			$this->_file = $this->_template_folder.$this->_view;
			if(is_file($this->_file)){
				include $this->_file;
			}
		}
    }
	
    public function render(){
		$this->renderHeader();
		$this->renderBody();
		$this->renderFooter();
    }
	
	public function generate($content_view, $template_view, $data = null){
		if(!$template_view){
			include $content_view;
		}
		else {
			include $template_view;
		}
	}
	
}

class Template {
    protected $_html = '';
    protected $_origHtml = '';
        
    public function __construct($tmpl_text) {
        $this->_html = $tmpl_text;
        $this->_origHtml = $tmpl_text;
    }
    
    public function reset() {
        $this->_html = $this->_origHtml;
    }
    private function _processIF($prefix,$obj) {
        $result = array();
        preg_match_all('/{@if\((.*)(==|>=|<=|!=|>|<)(.*)\)}(.*){\/if}/ims',$this->_html, $result);
        if (count($result)==3) {
            $match_count = count($result[0]);
            foreach ($result[1] as $mactch_key=>&$if_role) {
                
            }
        }
    }
    private function _setObject($prefix,$obj,&$metas) {
        foreach ($obj as $k=>&$value) {
            if (is_array($value)) {
                $this->_setObject($prefix.$k.'.',$value,$metas);
            } else {
                $metas[$prefix.$k] = $value;
                $this->setVar($prefix.$k,$value);
            }
        }
    }
    public function setObject($prefix,$obj) {
        $metas = array();
        $this->_setObject($prefix.'.',$obj,$metas);
        foreach ($metas as $k=>&$v) {
            $this->setVar($k,$v);
        }
        return $this;
    }
    public function setVar($name,$value) {
        $this->_html =str_replace('{#'.$name.'#}',$value,$this->_html);
        $this->_html =str_replace('{@'.$name.'@}',"'".$value."'",$this->_html);
        $this->_html =str_replace('{%'.$name.'%}',str_replace('"','&quot;',$value),$this->_html);
        return $this;
    }
    public function addVar($name,$value) {
        $this->_html =str_replace('{#'.$name.'#}',$value.'{#'.$name.'#}',$this->_html);
        $this->_html =str_replace('{@'.$name.'@}',"'".$value."'".'{@'.$name.'@}',$this->_html);
        $this->_html =str_replace('{%'.$name.'%}',str_replace('"','&quot;',$value).'{%'.$name.'%}',$this->_html);
        return $this;
    }
    public function afterVar($name,$value) {
        $this->_html = str_replace('{#'.$name.'#}','{#'.$name.'#}'.$value,$this->_html);
        $this->_html = str_replace('{@'.$name.'@}','{@'.$name.'@}'."'".$value."'",$this->_html);
        $this->_html = str_replace('{%'.$name.'%}','{%'.$name.'%}'.str_replace('"','&quot;',$value),$this->_html);
        return $this;
    }
    public function getSource() {
        return $this->_html;
    }
    public function getPlain() {
        $ret = preg_replace('/{#.*#}/i',"",$this->_html);
        $ret = preg_replace('/{@.*@}/i',"",$ret);
        $ret = preg_replace('/{%.*%}/i',"",$ret);
        return $ret;
    }
    public function getDom() {
        return $this->getPlain();
    }
	
	public function __toString(){
		return $this->getDom();
	}
	
}