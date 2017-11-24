<?php
	$groups = $data['groups'];
	$page = (int)((isset($_GET['page']))?$_GET['page']:1);
	$n=$groups[0]['n'];
	$maxrows=$groups[0]['limit'];

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
			data : { "ajax_action" : "delete_groups", "items" : items},
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
<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Группы</h2>
<div id="wrap-main">
	<div>
		<h3>Основные действия над группами</h3>
		<a href="/admin/productsgroups/?id=new" class="button-controls add">Добавить группу</a>
		<button id="delete-items" class="button-controls delete">Удалить отмеченные группы</button>
	</div>
	<div class="page-view">
		<form action="/admin/productsgroups/" method="get">
		<table style="width:100%;"><tr>
		<td><input name="search" id="search" style="height:30px; text-align:left !important;" type="text" value="<?= $_GET['search']; ?>"></td>
		<td style="width:50px;"><button style="height:29px; text-align:left !important;">Поиск</button></td>
		</tr></table>
		</form>
	</div>

	<table class="main-table" >
		<thead>
			<tr>
				<td class='all-align-center'><input type="checkbox" id="all-check" /></td>
				<td>ID</td>
				<td>Название</td>
				<td>Дата создания</td>
				<td>Статус</td>
			</tr>
		</thead>
		<tbody>
			
			<?php 
				foreach($groups as $i => $item)
				{
					$alt = ($i%2 ==0)? "alt":"";
					switch($item['group_status']){
						case 0 : $status_t = "Неактивен"; break;
						case 1 : $status_t = "Активен"; break;
					}
					
		echo "
			<tr id='raw_{$item['group_id']}'>
				<td class=\"{$alt} all-align-center\"><input class=\"ch\" type=\"checkbox\" value=\"{$item['group_id']}\" /></td>
				<td class=\"{$alt}\">{$item['group_id']}</td>
				<td class=\"{$alt}\"><a href=\"/admin/productsgroups/?id={$item['group_id']}\">"._(apply_filters('name-ru', $item['group_name']))."</a></td>
				<td class=\"{$alt}\">{$item['group_createdate']}</td>
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
