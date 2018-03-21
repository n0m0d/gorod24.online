<?php
class controller_merchandise extends Controller
{
	function __construct()
	{
		$this->monthes = array(
			1 => 'Января', 2 => 'Февраля', 3 => 'Марта', 4 => 'Апреля',
			5 => 'Мая', 6 => 'Июня', 7 => 'Июля', 8 => 'Августа',
			9 => 'Сентября', 10 => 'Октября', 11 => 'Ноября', 12 => 'Декабря'
		);
	}

	function random_vidget()
	{
		$query = "(SELECT 
			feo_basket.bas_id
			FROM main.feo_basket_types, main.feo_basket 
			WHERE
			 feo_basket.bas_type=feo_basket_types.type_id 
			 AND bas_date=(SELECT max(bas_date) from main.feo_basket as t1 WHERE feo_basket.bas_type=t1.bas_type )
			 AND (SELECT COUNT(*) FROM main.feo_basket_items WHERE feo_basket_items.bitem_bas_id=feo_basket.bas_id)>0
			 AND type_id in (SELECT bas_type from main.feo_basket) order by rand() LIMIT 1)";
		return $this->mini_merchandise_vidget($GLOBALS['DB']['80.93.183.242']->GetOne($query));
	}

	public function head_rates_vidget()
	{
		$query = "(SELECT 
			`feo_basket`.`bas_id`
			FROM `main`.`feo_basket` 
			WHERE
			`bas_type` IN (5)
			AND (SELECT COUNT(*) FROM `main`.`feo_basket_items` WHERE `feo_basket_items`.`bitem_bas_id` = `feo_basket`.`bas_id`) > 0
			ORDER BY `bas_date` DESC LIMIT 1)";
		return $this->head_merchandise_vidget($GLOBALS['DB']['80.93.183.242']->GetOne($query));
	}

	public function rates_vidget()
	{
		$query = "(SELECT 
			`feo_basket`.`bas_id`
			FROM `main`.`feo_basket` 
			WHERE
			`bas_type` IN (5)
			AND (SELECT COUNT(*) FROM `main`.`feo_basket_items` WHERE `feo_basket_items`.`bitem_bas_id` = `feo_basket`.`bas_id`) > 0
			ORDER BY `bas_date` DESC LIMIT 1)";
		return $this->mini_merchandise_vidget($GLOBALS['DB']['80.93.183.242']->GetOne($query), 'Курсы валют');
	}

	public function head_merchandise_vidget($id)
	{
		if ($id)
		{
			$tovars = $GLOBALS['DB']['80.93.183.242']->GetAll("SELECT *, (SELECT bitem_coin FROM main.feo_basket_items as prev_feo_basket_items, main.feo_basket as prev_feo_basket  WHERE prev_feo_basket_items.bitem_bas_id=prev_feo_basket.bas_id AND prev_feo_basket.bas_date<feo_basket.bas_date AND prev_feo_basket_items.bitem_tov_id=feo_basket_items.bitem_tov_id AND prev_feo_basket_items.bitem_mag_id=feo_basket_items.bitem_mag_id  ORDER by prev_feo_basket.bas_date DESC LIMIT 1) as prev_coin
												FROM main.feo_basket_items, main.feo_tovars, main.feo_magaz, main.edizm, main.feo_basket
												WHERE feo_basket_items.bitem_tov_id=feo_tovars.tov_id
													AND feo_basket_items.bitem_mag_id=feo_magaz.mag_id
													AND feo_basket_items.bitem_izm=edizm.id
													AND feo_basket_items.bitem_bas_id=feo_basket.bas_id
													AND feo_basket_items.bitem_bas_id=?i 
												ORDER BY tov_id", $id);

			$rows = array();

			foreach($tovars AS $i => $tovar)
			{
				if ($tovars[$i - 1]['tov_id'] != $tovar['tov_id'])
				{
					$row_ = array('name' => $tovar['tov_name']);
				}

				if (is_array($row_['coin']))
				{
					$row_['coin'][] = $tovar['bitem_coin'];
				}
				else
				{
					$row_['coin'] = array($tovar['bitem_coin']);
				}

				if (is_array($row_['prev_coin']))
				{
					$row_['prev_coin'][] = $tovar['prev_coin'];
				}
				else
				{
					$row_['prev_coin'] = array($tovar['prev_coin']);
				}

				if ($tovars[$i + 1]['tov_id'] != $tovar['tov_id'])
				{
					$row_['coin'] = array_sum($row_['coin']) / count($row_['coin']);
					$row_['prev_coin'] = array_sum($row_['prev_coin']) / count($row_['prev_coin']);
					$rows[] = $row_;
				}
			}

			foreach($rows AS $i => $tovar)
			{
				$row[] = number_format($tovar['coin'], 0, ',', ' ');
			}

			$body = '
					<div class="item">
                        <span class="icon-wrap"><i class="fa mini fa-usd" aria-hidden="true"></i></span>
                        <div><span>'.$row[0].'</span> р.</div>
                    </div>
                    <div class="item">
                        <span class="icon-wrap"><i class="fa mini fa-eur" aria-hidden="true"></i></span>
                        <div><span>'.$row[2].'</span> р.</div>
                    </div>
			';

			return $body;
		}
	}

	public function mini_merchandise_vidget($id, $header)
	{
		if ($id)
		{
			$zamer = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT * FROM `main`.`feo_basket` WHERE `bas_id`=?i", $id);
			$date = explode('-', $zamer['bas_date']);
			$date = $date[2].' '.$this->monthes[(int)$date[1]].' '.$date[0].'г.';

			$tovars = $GLOBALS['DB']['80.93.183.242']->GetAll("SELECT *, (SELECT bitem_coin FROM main.feo_basket_items as prev_feo_basket_items, main.feo_basket as prev_feo_basket  WHERE prev_feo_basket_items.bitem_bas_id=prev_feo_basket.bas_id AND prev_feo_basket.bas_date<feo_basket.bas_date AND prev_feo_basket_items.bitem_tov_id=feo_basket_items.bitem_tov_id AND prev_feo_basket_items.bitem_mag_id=feo_basket_items.bitem_mag_id  ORDER by prev_feo_basket.bas_date DESC LIMIT 1) as prev_coin
												FROM main.feo_basket_items, main.feo_tovars, main.feo_magaz, main.edizm, main.feo_basket
												WHERE feo_basket_items.bitem_tov_id=feo_tovars.tov_id
													AND feo_basket_items.bitem_mag_id=feo_magaz.mag_id
													AND feo_basket_items.bitem_izm=edizm.id
													AND feo_basket_items.bitem_bas_id=feo_basket.bas_id
													AND feo_basket_items.bitem_bas_id=?i 
												ORDER BY tov_id", $id);

			$body = '<table>';
			$rows = array();

			foreach($tovars AS $i => $tovar)
			{
				if ($tovars[$i - 1]['tov_id'] != $tovar['tov_id'])
				{
					$row_ = array('name' => $tovar['tov_name']);
				}

				if (is_array($row_['coin']))
				{
					$row_['coin'][] = $tovar['bitem_coin'];
				}
				else
				{
					$row_['coin'] = array($tovar['bitem_coin']);
				}

				if (is_array($row_['prev_coin']))
				{
					$row_['prev_coin'][] = $tovar['prev_coin'];
				}
				else
				{
					$row_['prev_coin'] = array($tovar['prev_coin']);
				}

				if ($tovars[$i + 1]['tov_id'] != $tovar['tov_id'])
				{
					$row_['coin'] = array_sum($row_['coin']) / count($row_['coin']);
					$row_['prev_coin'] = array_sum($row_['prev_coin']) / count($row_['prev_coin']);
					$rows[] = $row_;
				}
			}

			foreach($rows AS $i => $tovar)
			{
				$row = '<tr>';
				$row .= '<td>'.$tovar['name'].'</td>';
				$updown = '';

				if ($tovar['prev_coin'] != '')
				{
					if ($tovar['coin'] > $tovar['prev_coin'])
					{
						$updown = '<span class="up" title="Цена выросла на '.(number_format($tovar['coin'] - $tovar['prev_coin'], 2, ',', ' ')).' руб.">&#9650;</span>';
					}
					elseif ($tovar['coin'] < $tovar['prev_coin'])
					{
						$updown = '<span class="down" title="Цена упала на '.(number_format($tovar['prev_coin'] - $tovar['coin'], 2, ',', ' ')).' руб.">&#9660;</span>';
					}
					elseif ($tovar['coin'] == $tovar['prev_coin'])
					{
						$updown = '<span class="self"></span>';
					}
				}

				$row .= '<td class="center">'.number_format($tovar['coin'], 2, ',', ' ').' руб.'.$updown.'</td>';
				$row .= '</tr>';
				$body .= $row;
			}

			$body .= '</table>';

			return $body;
		}
	}

	public function merchandise_vidget($id)
	{
		if ($id)
		{
			$zamer = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT * FROM `main`.`feo_basket` WHERE `bas_id`=?i", $id);

			$tovars = $GLOBALS['DB']['80.93.183.242']->GetAll("SELECT *, (SELECT bitem_coin FROM main.feo_basket_items as prev_feo_basket_items, main.feo_basket as prev_feo_basket  WHERE prev_feo_basket_items.bitem_bas_id=prev_feo_basket.bas_id AND prev_feo_basket.bas_date<feo_basket.bas_date AND prev_feo_basket_items.bitem_tov_id=feo_basket_items.bitem_tov_id AND prev_feo_basket_items.bitem_mag_id=feo_basket_items.bitem_mag_id  ORDER by prev_feo_basket.bas_date DESC LIMIT 1) as prev_coin
															   FROM main.feo_basket_types_items, main.feo_basket_items, main.feo_tovars, main.feo_magaz, main.edizm, main.feo_basket
															   WHERE  feo_basket_items.bitem_tov_id=feo_tovars.tov_id
															  		AND feo_basket_types_items.item_bas_id=feo_basket.bas_type 
																	AND feo_basket_types_items.item_tov_id=feo_tovars.tov_id
																	AND feo_basket_items.bitem_mag_id=feo_magaz.mag_id
																	AND feo_basket_items.bitem_izm=edizm.id
																	AND feo_basket_items.bitem_bas_id=feo_basket.bas_id
																	AND feo_basket_items.bitem_bas_id=?i 
															   ORDER BY tov_id", $id);

			$magazs = $GLOBALS['DB']['80.93.183.242']->GetAll("SELECT * FROM main.feo_basket_types_magazs, main.feo_magaz, main.feo_basket
															   WHERE feo_basket_types_magazs.mag_id=feo_magaz.mag_id
																	AND feo_basket_types_magazs.type_id=feo_basket.bas_type
															        AND feo_basket.bas_id=?i", $id);

			$header = '<thead><tr style="border: 1px solid #ececec; text-align: center; font-weight: bolder;"><td style="border: 1px solid #ececec; text-align: center; font-weight: bolder;">Товар</td><td class="hidden-xs" style="border: 1px solid #ececec; text-align: center; font-weight: bolder;">Единица измерения</td>';
			$width = 60 / count($magazs);

			foreach($magazs AS $i => $magaz)
			{
				$header .= '<td style="width:'.$width.'%; border: 1px solid #ececec; text-align: center; font-weight: bolder;">'.$magaz['mag_name'].'</td>';
			}

			$header .= '</tr></thead>';
			$body = '<table style="margin-top: 20px; margin-bottom: 20px;">';

			$body .= $header;

			foreach($tovars AS $i => $tovar)
			{
				if ($tovars[$i - 1]['tov_id'] != $tovar['tov_id'])
				{
					$row  = '<tr>';
					$row .= '<td style="border-bottom: 1px dotted #ececec; padding: 5px 1px; margin: 1px; font-size: 13px;">'.$tovar['tov_name'].'</td>';
					$row .= '<td class="center hidden-xs" style="border-bottom: 1px dotted #ececec; padding: 5px 1px; margin: 1px; font-size: 13px; text-align: center;">'.$tovar['name'].'</td>';
				}
				else
				{
					$row='';
				}

				$updown = '';

				if ($tovar['prev_coin'] != '')
				{
					if ($tovar['bitem_coin'] > $tovar['prev_coin'])
					{
						$updown = '<span class="up" style="color: green;" title="Цена выросла на '.(number_format($tovar['bitem_coin'] - $tovar['prev_coin'], 2, ',', ' ')).' руб.">&#9650;</span>';
					}
					elseif ($tovar['bitem_coin'] < $tovar['prev_coin'])
					{
						$updown = '<span class="down" style="color: red;" title="Цена упала на '.(number_format($tovar['prev_coin'] - $tovar['bitem_coin'], 2, ',', ' ')).' руб.">&#9660;</span>';
					}
					elseif ($tovar['bitem_coin'] == $tovar['prev_coin'])
					{
						$updown = '<span class="self"></span>';
					}
				}

				$number = number_format($tovar['bitem_coin'], 2, ',', ' ');
				$row .= '<td class="center" style="border-bottom: 1px dotted #ececec; padding: 5px 1px; margin: 1px; font-size: 13px; text-align: center;">'.$number.' руб.'.$updown.'</td>';

				if ($tovars[$i + 1]['tov_id'] != $tovar['tov_id'])
				{
					$row .= '</tr>';
				}

				$body .= $row;
			}

			$body .= '</table>';

			return $body;
		}
	}
}