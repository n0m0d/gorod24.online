<?php
	$invoices = $data['invoices'];
	$delete = $data['delete'];
	$page = (int)((isset($_GET['page']))?$_GET['page']:1);
	$limit = (int)((isset($_GET['limit']))?$_GET['limit']:20);
	$n=$invoices[0]['n'];
	$maxrows=$invoices[0]['limit'];

?>
<script>
$(function(){
	$('#all-check').change(function(){
		var prop = $(this).prop("checked");
		if(prop) {
			$('.ch').prop("checked", true);
		} else { 
			$('.ch').prop("checked", false); 
		}
	});
	
	$('#delete-items').click(function(){
		var items = [];
		$('.ch').each(function(index){
			if($(this).prop("checked")) items.push($(this).val());	
		});
		
		if(items.length > 0){
		xhr = $.ajax({
			url : "/admin/ajax",
			type : "post",
			data : { "ajax_action" : "delete_invoices", "items" : items},
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				for( var i in items){
					$('#raw_'+items[i]).remove();
				}
				console.log(data);
			}
		});
		}
		
	});
});
</script>
<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Счета</h2>
<div id="wrap-main">
	<div>
		<h3>Основные действия над счетами</h3>
		<a href="/admin/invoices/?id=new" class="button-controls add">Добавить счет</a>
		<?php 
		if($delete=="1"){
			echo '<button id="delete-items" class="button-controls delete">Удалить отмеченные счета</button>';
		}
		?>
	</div>
	<div class="page-view">
		<form action="/admin/invoices/" method="get">
		<table style="width:100%;"><tr>
		<td><input name="search" id="search" style="height:30px; text-align:left !important;" type="text" value="<?= $_GET['search']; ?>"></td>
		<td style="width:165px;">
			<select name="limit" style="-webkit-appearance: auto; border-radius: 0px; font-size:14px; padding:6px;" >
				<option value="20" <?php if($limit==20) echo 'selected="selected"' ?>>20 элементов на странице</option>
				<option value="50" <?php if($limit==50) echo 'selected="selected"' ?>>50 элементов на странице</option>
				<option value="100" <?php if($limit==100) echo 'selected="selected"' ?>>100 элементов на странице</option>
			</select>
		</td>
		<td style="width:50px;"><button style="height:29px; text-align:left !important;">Поиск</button></td>
		</tr></table>
		</form>
	</div>

	<table class="main-table" >
		<thead>
			<tr>
				<td class='all-align-center'><input type="checkbox" id="all-check" /></td>
				<td>ID</td>
				<td>Создатель счета</td>
				<td>Заказчик</td>
				<td></td>
				<td>Сумма</td>
				<td>Дата создания</td>
				<td>Статус</td>
			</tr>
		</thead>
		<tbody>
			
			<?php 
				foreach($invoices as $i => $item)
				{
					$alt = ($i%2 ==0)? "alt":"";
					switch($item['inv_status']){
						case 0 : $status_t = "Неоплачен"; break;
						case 1 : $status_t = "Оплачен"; break;
					}
					
		echo "
			<tr id='raw_{$item['inv_id']}'>
				<td class=\"{$alt} all-align-center\"><input class=\"ch\" type=\"checkbox\" value=\"{$item['inv_id']}\" /></td>
				<td class=\"{$alt}\"><a href=\"/admin/invoices/?id={$item['inv_id']}\">{$item['inv_id']}</a></td>
				<td class=\"{$alt}\"><a href=\"/admin/invoices/?id={$item['inv_id']}\">"._(apply_filters('name-ru', $item['inv_avtor_name']))."</a></td>
				<td class=\"{$alt}\"><a href=\"/admin/invoices/?id={$item['inv_id']}\">"._(apply_filters('name-ru', $item['bas_avtor_name']))."</a></td>
				<td class=\"{$alt}\"><a href=\"/admin/invoices/?id={$item['inv_id']}&print=1\" target=\"_blank\">Распечатать счет</a></td>
				<td class=\"{$alt}\">{$item['inv_summ']}</td>
				<td class=\"{$alt}\">{$item['inv_date']}</td>
				<td class=\"{$alt}\">{$status_t}</td>
			</tr>";
					
				}
			?>
			
		</tbody>
	</table>
		<?php
		echo do_shortcode('[pagenavigation n="'.$n.'" limit="'.$maxrows.'" page="'.$page.'"]');
		?>
</div>
