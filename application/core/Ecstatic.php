<?php
class EcstaticTable{
	private $_data;
	private $_attrs;
	private $_header,$_ifempty,$_rowclass,$_columns,$_footer;

	public function __construct($data,$attrs=null){
		$this->_data=$data;
		$this->_rowclass=null;
		$this->_header=true;
		$this->_footer=false;
		$this->_ifempty='';
		$this->_columns=array();
		$this->_attrs=isset($attrs)?$attrs:array();
	}
	
	public function show($column,$options){
		$title=$options['title'];
		$format=$options['format'];
		$attrs=$options['attrs'];
		$footer=$options['footer'];
		
		$attrs=isset($attrs)?$attrs:array();
		$properties = array('title'=>isset($title)?$title:$column,'attrs'=>$attrs,'footer'=>$footer,'sum'=>0);
		$this->_columns[$column]=$properties;
		if(isset($format) && !empty($this->_data)){
			if(is_array($format)){
				foreach($this->_data as &$row){
					$row[$column]=isset($format[$row[$column]])?$format[$row[$column]]:$row[$column];
				}
			}else{
				foreach($this->_data as &$row){
					preg_match_all('/@\[([^\]]+)\]/',$format,$matches);
					$value=$format;
					for($i=0;$i<count($matches[0]);$i++){
						if(isset($row[$matches[1][$i]])){
							$value=str_replace($matches[0][$i],$row[$matches[1][$i]],$value);
						}
					}
					$row[$column]=$value;
				}
			}
		}
		return $this;
	}
	
	
	public function header($value){$this->_header=$value;return $this;}
	public function footer($value){$this->_footer=$value;return $this;}
	public function ifempty($value){$this->_ifempty=$value;return $this;}
	public function rows(){$this->_rowclass=func_get_args();return $this;}
	public function out(){echo $this->_render();return $this;}
	public function get(){return $this->_render();}
	public function getSum($column){return $this->_columns[$column]['sum']; }
	
	private function _render(){
		if(empty($this->_data)){
			return $this->_ifempty;
		}
		if(empty($this->_columns)){
			foreach(reset($this->_data) as $key=>$item){
				$this->_columns[$key]=array('title'=>$key,'attrs'=>array());
			}
		}
		$attrs='';
		foreach($this->_attrs as $key=>$item){
			$attrs.=" {$key}=\"{$item}\"";
		}
		$out="<table{$attrs}>";
		if($this->_header){
			$out.='<thead><tr>';
			foreach($this->_columns as $column=>$item){
				$out.='<th class="esc-column">'.$item['title'].'</th>';
			}
			$out.='</tr></thead>';
		}
		$out.='<tbody>';
		if(isset($this->_rowclass)){$rowclass=reset($this->_rowclass);}else{$rowclass='';}
		foreach($this->_data as $row){
			$out.='<tr'.($rowclass?" class=\"{$rowclass}\"":'').'>';
			foreach($this->_columns as $column=>$item){
				$attrs='';
				foreach($item['attrs'] as $key=>$item){
					$attrs.=" {$key}=\"{$item}\"";
				}
				$out.="<td{$attrs} class=\"esc-cell\">";
				if($item['footer']=="sum"){ $this->_columns[$column]['sum']+=$row[$column];}
				$out.= $row[$column];
				$out.='</td>';
			}
			$out.='</tr>';
			if(isset($this->_rowclass)){
				$rowclass=next($this->_rowclass);
				if($rowclass===false)$rowclass=reset($this->_rowclass);
			}
		}
		$out.='</tbody>';
		if($this->_footer){
			$out.='<tfoot><tr>';
			foreach($this->_columns as $column=>$item){
				if($item['footer']=='sum'){
					$out.='<th class="esc-footer"><div>'.$item['sum'].'</div></th>';
				}
				else{
					$out.='<th class="esc-footer"><div>'.$item['footer'].'</div></th>';
				}
			}
			$out.='</tfoot></tr>';
		}
		
		$out.='</table>';
		return $out;
	}
}

class Ecstatic{
	public static function table($data,$attrs=null){
		return new EcstaticTable($data,$attrs);
	}
}
?>