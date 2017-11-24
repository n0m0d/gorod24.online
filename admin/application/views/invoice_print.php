<?php
$main = $data['invoice'];
$bas_items = $data['bas_items'];
$day = date("d", strtotime($main['inv_date']));
$month = date("n", strtotime($main['inv_date']));
$year = date("Y", strtotime($main['inv_date']));
switch($month){
	case "1": $month_str = "Января"; break;
	case "2": $month_str = "Февраля"; break;
	case "3": $month_str = "Марта"; break;
	case "4": $month_str = "Апреля"; break;
	case "5": $month_str = "Мая"; break;
	case "6": $month_str = "Июня"; break;
	case "7": $month_str = "Июля"; break;
	case "8": $month_str = "Августа"; break;
	case "9": $month_str = "Сентября"; break;
	case "10": $month_str = "Октября"; break;
	case "11": $month_str = "Ноября"; break;
	case "12": $month_str = "Декабря"; break;
}
$date = "$day $month_str $year";
?>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body><table width="700" border="0" cellspacing="1" cellpadding="0" align="center" id="menu">


    <tbody><tr><td align="center">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="menu">
        <tbody><tr>
          <td rowspan="2" width="47%">
            <div align="center"><font size="4"><b> <font face="Arial, Helvetica, sans-serif">Индивидуальный предприниматель ПУШКАРЕВ СЕРГЕЙ НИКОЛАЕВИЧ</font></b></font></div>
          </td>

        </tr>
        <tr>
          <td width="53%"><font size="2" face="Arial, Helvetica, sans-serif">р/с 40802810504100031296 БИК 046711103<br> ОАО «Севастопольский морской банк»<br>кор. счет № 30101810567110000103 ИНН 910810741417 Плательщик УСН Тел. 212-00</font></td>
        </tr>
      </tbody></table>
      <p align="center" style="text-align:center"><b>СЧЕТ №<u> </u></b><u><?= $main['inv_id']?></u><b>&nbsp;от
        </b><u><?= $date ?></u></p>
      <table border="0" cellspacing="0" cellpadding="0" width="648" id="menu">
        <tbody><tr>
          <td width="150" valign="top"> <i>Плательщик: <br><font size="3">Заказ</font>:
            <br>
            <font size="3">Ометка обоплате</font></i><font size="3">:</font></td>
          <td width="498" valign="top"> <b> &nbsp;<font face="Arial, Helvetica, sans-serif">&nbsp; <?= $main['bas_avtor_name']?></font><br>
            <font size="2">______________________________________________________________
            ______________________________________________________________</font></b></td>
        </tr>
      </tbody></table>
	  
      <table width="100%" border="1" cellspacing="0" cellpadding="0" align="center" bordercolor="#000000">
        <tbody>
		<tr>
          <td width="4%"><b><font face="Arial, Helvetica, sans-serif">N <font size="1">п.п</font></font></b></td>
          <td width="57%">
            <div align="center"><b><font face="Arial, Helvetica, sans-serif">Название</font></b></div>
          </td>
          <td width="8%">
            <div align="center"><b><font face="Arial, Helvetica, sans-serif">Ед.изм</font></b></div>
          </td>
          <td width="9%">
            <div align="center"><b><font face="Arial, Helvetica, sans-serif">Кол-во</font></b></div>
          </td>
          <td width="8%">
            <div align="center"><b><font face="Arial, Helvetica, sans-serif">Цена</font></b></div>
          </td>
          <td width="14%">
            <div align="center"><b><font face="Arial, Helvetica, sans-serif">Сумма</font></b></div>
          </td>
        </tr>
		
        <tr>
          <td width="4%"><font size="2">&nbsp;</font></td>
          <td width="57%"><font size="2" face="Arial, Helvetica, sans-serif"></font></td>
          <td width="8%">&nbsp;</td>
          <td width="9%">&nbsp;</td>
          <td width="8%">&nbsp;</td>
          <td width="14%">&nbsp;</td>
        </tr>

		<?php
		foreach($bas_items as $i=>$item){
		echo '<tr>
          <td width="4%">&nbsp;</td>
          <td width="57%"><font size="2">'.$item['prod_name'].'</font></td>
          <td width="8%">
            <div align="center"><font size="2">&nbsp;шт</font></div>
          </td>
          <td width="9%">
            <div align="center"><font size="2">&nbsp;'.($item['item_amount'] * $item['id_releases']).'</font></div>
          </td>
          <td width="8%">
            <div align="center"><font size="2">&nbsp;'.number_format($item['item_cost'], 2, ',', ' ').'</font></div>
          </td>
          <td width="14%">
            <div align="right"><font size="2">&nbsp;'.number_format(($item['item_cost'] * $item['item_amount'] * $item['id_releases']), 2, ',', ' ').'</font></div>
          </td>
        </tr>';
			
		}
		if(count($bas_items)<8){
			for ($i=0;$i<(8-count($bas_items));$i++){
			echo '	
				<tr>
				  <td width="4%">&nbsp;</td>
				  <td width="57%"><font size="2">&nbsp;          </font></td>
				  <td width="8%">
					<div align="center"><font size="2">&nbsp;</font></div>
				  </td>
				  <td width="9%">
					<div align="center"><font size="2">&nbsp;</font></div>
				  </td>
				  <td width="8%">
					<div align="center"><font size="2">&nbsp;</font></div>
				  </td>
				  <td width="14%">
					<div align="right"><font size="2">&nbsp;</font></div>
				  </td>
				</tr>
				';
			}
			
		}
		
		
		?>
		<tr>
          <td width="4%">&nbsp;</td>
          <td width="57%"><font face="Arial, Helvetica, sans-serif" size="2">&nbsp;НДС
            </font></td>
          <td width="8%">&nbsp;</td>
          <td width="9%">&nbsp;</td>
          <td width="8%">&nbsp;</td>
          <td width="14%">
            <div align="right"><font size="2" face="Arial, Helvetica, sans-serif">без НДС</font></div>
          </td>
        </tr>
        
      </tbody></table>



      <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#000000">
        <tbody>
		<tr>
          <td width="87%">
            <div align="right"><b>ВСЕГО:</b></div>
          </td>
          <td width="13%">
            <div align="right"><b><?= number_format($main['inv_summ'], 2, ',', ' ');?></b></div>
          </td>
        </tr>
		</tbody>
	  </table>
	  
      <table bordercolor="#808080" bordercolorlight="black" bordercolordark="white" cellspacing="0" cellpadding="0" border="1" id="menu">
        <tbody><tr bgcolor="#FFFFFF">
          </tr>
      </tbody>
	  </table>
      <p class="Normal"><b>К оплате:  <?= num2str($main['inv_summ'])?> </b></p>
      <table border="0" cellspacing="0" cellpadding="0" id="menu">
        <tbody><tr>
          <td width="500" valign="middle">
            <p align="center">Директор</p>
          </td>
          <td rowspan="3" valign="top">&nbsp; </td>
          <td width="500" valign="middle">
            <center>
              / Пушкарев С. Н. /
            </center>
          </td>
        </tr>

      </tbody>
	  </table>
    </td>
  </tr>
</tbody></table><br><br>
<table width="700" border="0" cellspacing="1" cellpadding="0" align="center" id="menu">


    <tbody><tr><td align="center">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" id="menu">
        <tbody><tr>
          <td rowspan="2" width="47%">
            <div align="center"><font size="4"><b> <font face="Arial, Helvetica, sans-serif">Индивидуальный предприниматель ПУШКАРЕВ СЕРГЕЙ НИКОЛАЕВИЧ</font></b></font></div>
          </td>

        </tr>
        <tr>
          <td width="53%"><font size="2" face="Arial, Helvetica, sans-serif">р/с 40802810504100031296 БИК 046711103<br> ОАО «Севастопольский морской банк»<br>кор. счет № 30101810567110000103 ИНН 910810741417 Плательщик УСН Тел. 212-00</font></td>
        </tr>
      </tbody></table>
      <p align="center" style="text-align:center"><b>СЧЕТ №<u> </u></b><u><?= $main['inv_id']?></u><b>&nbsp;от
        </b><u><?= $date ?></u></p>
      <table border="0" cellspacing="0" cellpadding="0" width="648" id="menu">
        <tbody><tr>
          <td width="150" valign="top"> <i>Плательщик: <br><font size="3">Заказ</font>:
            <br>
            <font size="3">Ометка обоплате</font></i><font size="3">:</font></td>
          <td width="498" valign="top"> <b> &nbsp;<font face="Arial, Helvetica, sans-serif">&nbsp; <?= $main['bas_avtor_name']?></font><br>
            <font size="2">______________________________________________________________
            ______________________________________________________________</font></b></td>
        </tr>




      </tbody></table>
      <table width="100%" border="1" cellspacing="0" cellpadding="0" align="center" bordercolor="#000000">
        <tbody><tr>
          <td width="4%"><b><font face="Arial, Helvetica, sans-serif">N <font size="1">п.п</font></font></b></td>
          <td width="57%">
            <div align="center"><b><font face="Arial, Helvetica, sans-serif">Название</font></b></div>
          </td>
          <td width="8%">
            <div align="center"><b><font face="Arial, Helvetica, sans-serif">Ед.изм</font></b></div>
          </td>
          <td width="9%">
            <div align="center"><b><font face="Arial, Helvetica, sans-serif">Кол-во</font></b></div>
          </td>
          <td width="8%">
            <div align="center"><b><font face="Arial, Helvetica, sans-serif">Цена</font></b></div>
          </td>
          <td width="14%">
            <div align="center"><b><font face="Arial, Helvetica, sans-serif">Сумма</font></b></div>
          </td>
        </tr>
        <tr>
          <td width="4%"><font size="2">&nbsp;</font></td>
          <td width="57%"><font size="2" face="Arial, Helvetica, sans-serif"></font></td>
          <td width="8%">&nbsp;</td>
          <td width="9%">&nbsp;</td>
          <td width="8%">&nbsp;</td>
          <td width="14%">&nbsp;</td>
        </tr>

		<?php
		foreach($bas_items as $i=>$item){
		echo '<tr>
          <td width="4%">&nbsp;</td>
          <td width="57%"><font size="2">'.$item['prod_name'].'</font></td>
          <td width="8%">
            <div align="center"><font size="2">&nbsp;шт</font></div>
          </td>
          <td width="9%">
            <div align="center"><font size="2">&nbsp;'.($item['item_amount'] * $item['id_releases']).'</font></div>
          </td>
          <td width="8%">
            <div align="center"><font size="2">&nbsp;'.number_format($item['item_cost'], 2, ',', ' ').'</font></div>
          </td>
          <td width="14%">
            <div align="right"><font size="2">&nbsp;'.number_format(($item['item_cost'] * $item['item_amount'] * $item['id_releases']), 2, ',', ' ').'</font></div>
          </td>
        </tr>';
			
		}
		if(count($bas_items)<8){
			for ($i=0;$i<(8-count($bas_items));$i++){
			echo '	
				<tr>
				  <td width="4%">&nbsp;</td>
				  <td width="57%"><font size="2">&nbsp;          </font></td>
				  <td width="8%">
					<div align="center"><font size="2">&nbsp;</font></div>
				  </td>
				  <td width="9%">
					<div align="center"><font size="2">&nbsp;</font></div>
				  </td>
				  <td width="8%">
					<div align="center"><font size="2">&nbsp;</font></div>
				  </td>
				  <td width="14%">
					<div align="right"><font size="2">&nbsp;</font></div>
				  </td>
				</tr>
				';
			}
			
		}
		
		
		?>
		
		<tr>
          <td width="4%">&nbsp;</td>
          <td width="57%"><font face="Arial, Helvetica, sans-serif" size="2">&nbsp;НДС
            </font></td>
          <td width="8%">&nbsp;</td>
          <td width="9%">&nbsp;</td>
          <td width="8%">&nbsp;</td>
          <td width="14%">
            <div align="right"><font size="2" face="Arial, Helvetica, sans-serif">без НДС</font></div>
          </td>
        </tr>
        
      </tbody></table>



      <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#000000">
        <tbody><tr>
          <td width="87%">
            <div align="right"><b>ВСЕГО:</b></div>
          </td>
          <td width="13%">
            <div align="right"><b><?= number_format($main['inv_summ'], 2, ',', ' ');?></b></div>
          </td>
        </tr>
      </tbody></table>
      <table bordercolor="#808080" bordercolorlight="black" bordercolordark="white" cellspacing="0" cellpadding="0" border="1" id="menu">
        <tbody><tr bgcolor="#FFFFFF">
          </tr>
      </tbody></table>
      <p class="Normal"><b>К оплате:  <?= num2str($main['inv_summ'])?> </b></p>
      <table border="0" cellspacing="0" cellpadding="0" id="menu">
        <tbody><tr>
          <td width="500" valign="middle">
            <p align="center">Директор</p>
          </td>
          <td rowspan="3" valign="top">&nbsp; </td>
          <td width="500" valign="middle">
            <center>
              / Пушкарев С. Н. /
            </center>
          </td>
        </tr>

      </tbody></table>
    </td>
  </tr>
</tbody></table>
</body></html>