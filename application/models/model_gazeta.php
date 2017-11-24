<?php
class model_gazeta extends Model
{
	protected $nums; public function nums(){	return $this->nums;	}	
	protected $advs; public function advs(){	return $this->advs;	}	
	
	function __construct($config = array()) {
		$config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "gazeta",
            "engine" => "InnoDB",
            "version" => "1.2",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
				'city_id'       => "INT NOT NULL DEFAULT '0'",
                'title'         => "TEXT NOT NULL DEFAULT ''",
                'active'        => "ENUM('0','1') NOT NULL DEFAULT '1'",
                'max_advs'      => "INT NOT NULL DEFAULT '200'",            /* 1.1 */
                'max_words'     => "INT NOT NULL DEFAULT '10'",             /* 1.1 */
                'max_chars'     => "INT NOT NULL DEFAULT '180'",            /* 1.1 */
                'info_text'     => "TEXT NOT NULL DEFAULT ''",              /* 1.2 */
                'mail_text'     => "TEXT NOT NULL DEFAULT ''",              /* 1.2 */
                'default'       => "ENUM('0','1') NOT NULL DEFAULT '0'",    /* 1.1 */
                'on_off'        => "ENUM('0','1') NOT NULL DEFAULT '1'"
				),
			"index" => array(
                'i_active'      => array( 'active' ),
                'i_onoff'       => array( 'on_off' ),
                'active_onoff'  => array( 'active', 'on_off' )
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
		
		$nums_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "gazeta_nums",
            "engine" => "InnoDB",
            "version" => "1.1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
                'pid'       => "INT NOT NULL DEFAULT '0'",
                'num'       => "INT NOT NULL DEFAULT '0'",
                'date'      => "DATE NOT NULL DEFAULT '0000-00-00'",
                'stop_date' => "DATE NOT NULL DEFAULT '0000-00-00'",
                'year'      => "INT NOT NULL DEFAULT '0'",                  /* 1.1 */
                'limit'     => "INT NOT NULL DEFAULT '0'",
                'on_off'    => "ENUM('0','1') NOT NULL DEFAULT '1'"
				),
			"index" => array(
                'fk_pid'        => array( 'pid' ),
                'fk_stopdate'   => array( 'stop_date' ),
                'onoff'         => array( 'on_off' ),
                'pid_onoff'     => array( 'pid' , 'on_off' ),
                'i_year'        => array( 'year' )                          /* 1.1 */
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
		
		$this->nums = new Model($nums_config);
		
		$advs_config = [
            "server" => "80.93.183.242",
            "database" => "new_feo_ua",
            "prefix" => "adv_",
            "name" => "gazeta_adv",
            "engine" => "InnoDB",
            "version" => "1.1",
            "row_format" => "Dynamic",
            "collation" => "utf8_general_ci",
            "primary_key" => "id",
			"autoinit"  => false,
            "columns" => array(
                'gazeta_id'     => "INT NOT NULL DEFAULT '0'",
                'num_id'        => "INT NOT NULL DEFAULT '0'",
                'adv_id'        => "INT NOT NULL DEFAULT '0'",
                'published'     => "ENUM('0','1') NOT NULL DEFAULT '0'"         /* 1.1 Если стоит '1' - номер уже вышел в печать */
				),
			"index" => array(
                'fk_gazeta'     => array( 'gazeta_id' ),
                'fk_num'        => array( 'num_id' ),
                'fk_adv'        => array( 'adv_id' ),
                'gazeta_num'    => array( 'gazeta_id' , 'num_id' ),
                'gazeta_num_adv'=> array( 'gazeta_id' , 'num_id' , 'adv_id' ),
                'i_published'   => array( 'published' )
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
		
		$this->advs = new Model($advs_config);
		
		
    }
	
    public function getNear($gazeta_id,$limit=3,$need_date=false,$nocheck=false) {
        if (!$need_date) {
            $need_date = date("Y-m-j");
        }
        $query = "
            SELECT
                `n`.`id`,
                `n`.`num`,
                `n`.`date`
            FROM
                `{$this->nums->getdatabasename()}`.`{$this->nums->gettablename()}` AS `n`
            WHERE
                `n`.`pid`='{$gazeta_id}'
                ".((!$nocheck) ? "
                    AND
                `n`.`stop_date`>'{$need_date}'
                " : "")."
                    AND
                `n`.`on_off`='1'
            ORDER BY
                `n`.`date` ASC
            LIMIT {$limit}
            ";
        return $this->db()->getAll($query);
    }
	
	public function getGazeta($city_id, $limit = 2){
		$result = $this->getItemsWhere("`city_id`={$city_id} and `active`='1' AND `on_off`='1'", '`default` DESC', null, null, "id, title as name, max_chars");
		foreach($result as $i=>$item){
			$result[$i]['items'] = $this->getNear($item['id'], $limit);
		}
		return $result;
	}
	
	public function addAdv($adv_id, $nums, $force=true){
		if($force){
			$this->advs->Delete("`adv_id`='{$adv_id}' AND `published`='0'");
		}
		if (is_array($nums) and count($nums)) {
            foreach ($nums as $k=>&$num_data) {
				if(!empty($num_data['id']) and $num_data['num_id']){
                $check = $this->advs->getItemWhere("gazeta_id='{$num_data['id']}' AND num_id='{$num_data['num_id']}' AND adv_id='{$adv_id}'");
				if(empty($check)){
				$rel_row = array(
                    'gazeta_id'     => $num_data['id'],
                    'num_id'        => $num_data['num_id'],
                    'adv_id'        => $adv_id,
                    'published'     => '0'
                );
                $this->advs->Insert($rel_row);
				}
				}
            }
		}
	}
	
    public function getAdvNums($adv_id, $published=false) {
        $ret = array();
        $query = "
            SELECT
                `n`.`pid`,
                `n`.`id`
            FROM
                `{$this->advs->getdatabasename()}`.`{$this->advs->gettablename()}` AS `rel`
            LEFT JOIN
                `{$this->getdatabasename()}`.`{$this->gettablename()}` AS `g`
                    ON
                        `g`.`id`=`rel`.`gazeta_id`
                            AND
                        `g`.`active`='1'
                            AND
                        `g`.`on_off`='1'
            LEFT JOIN
                `{$this->nums->getdatabasename()}`.`{$this->nums->gettablename()}` AS `n`
                    ON
                        `n`.`id`=`rel`.`num_id`
                            AND
                        `n`.`pid`=`g`.`id`
                            AND
                        `n`.`on_off`='1'
            WHERE
                `rel`.`adv_id`='{$adv_id}'
                    AND
                `rel`.`published`='".(($published) ? '1' : '0')."'
                    AND
                `g`.`id` IS NOT NULL
                    AND
                `n`.`id` IS NOT NULL
            GROUP BY `n`.`id`
            ";
        $rows = $this->db()->getAll($query);
        foreach ($rows as $K=>&$row) {
            $ret[] = ['id'=>$row['pid'], 'num_id'=>$row['id']];
        }
        return $ret;
    }
	
	
	
	
}
?>