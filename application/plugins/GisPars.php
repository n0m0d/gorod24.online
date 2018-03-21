<?php
/*

Plugin Name: GisPars
Plugin URI: GisPars
Description: Плагин GisPars
Version: 1.0
Author: Заднепряный Андрей
Author URI: 

*/
//https://www.meteoservice.ru/content/export.html
//http://crimea-map.msk.ru/
if(!class_exists('GisPars', false)){
class GisPars {
    private $town_index    =  33976;
    private $xml_url       =  'https://xml.meteoservice.ru/export/gismeteo/point/xxxxx.xml', $xml_dir;
    private $times         =  array('2.40', '8.40', '14.40', '20.40');
    private $debug         =  false;
	private $data = null;
    
    private $i         =  0;
    private $res       =  array();

    function __construct($town_index)
    {
		$this->town_index = $town_index;
        $this->xml_dir = APPDIR.'/';
        $this->CheckXML();
    }
    
    function CheckXml()
    {
        $filename_l = $this->xml_dir.$this->town_index.'_1.xml';

        $this->GetXmlFile();
    }
    
    function debug($text='')
    {
        if($this->debug)
        {
            print 'GisPars::Debug "'.$text.'"<br />';
        }
    }
    
    function GetXmlFile()
    {
        $this->debug('Uploading file..');
        $filename_r = str_replace('xxxxx', $this->town_index, $this->xml_url);
        $filename_l = $this->xml_dir.$this->town_index.'_1.xml';
        $f_content = file_get_contents($filename_r, 'r');
        $file = fopen($filename_l, 'a');
        ftruncate($file, 0);
        fclose($file);
		$this->data = $f_content;
        //file_put_contents($filename_l, $f_content);
    }
    
    function SetResValue($key, $value)
    {
        $this->res[$key] = $value;
    }
    
    function startXMLElement($parser, $name, $attrs) 
    {
        switch ($name) 
        {
            case 'TOWN':
                $this->SetResValue('town', urldecode($attrs['SNAME']));
                $this->SetResValue('latitude', $attrs['LATITUDE']);
                $this->SetResValue('longtitude', $attrs['LONGITUDE']);
                break;
            case 'FORECAST':
                $this->SetResValue('d'.$this->i, array('day'=>$attrs['DAY'], 'month'=> $attrs['MONTH'], 'year'=>$attrs['YEAR'], 'hour'=>$attrs['HOUR']));
                break;
            case 'PHENOMENA':
                $this->SetResValue('p'.$this->i, array('cloudiness'=>$attrs['CLOUDINESS'], 'precipitation'=>$attrs['PRECIPITATION']));
                break;
            case 'TEMPERATURE':
                $this->SetResValue('t'.$this->i, array('min'=>$attrs['MIN'], 'max'=>$attrs['MAX']));
                
                break;
            case 'WIND':
                $this->SetResValue('w'.$this->i, array('min'=>$attrs['MIN'], 'max'=>$attrs['MAX'],'dir'=>$attrs['DIRECTION']));
                $this->i++;
                break;     
        }
    }
    
    function endXMLElement($parser, $name)
    {
        
    }
    
    function GetParse()
    {
        $filename_l = $this->xml_dir.$this->town_index.'_1.xml';

        //$data = file_get_contents($filename_l);
        $data = $this->data;
 
        $XMLparser = xml_parser_create();
        xml_set_object($XMLparser, $this);
        xml_set_element_handler($XMLparser, 'startXMLElement', 'endXMLElement');
        if(!xml_parse($XMLparser, $data)) 
        {
            $this->debug('Error of parsing..');
        }
        xml_parser_free($XMLparser);
        return $this->res;
    }
}

}