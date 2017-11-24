<?php
	$attributes = $data['attributes'];
?>
<script>
var xhr = null;
$(function(){
	$('#all-check').change(function(){
		var prop = $(this).prop("checked");
		if(prop) {
			$('.ch').prop("checked", true);
		} else { 
			$('.ch').prop("checked", false); 
		}
	});
	
	$('#delete-attributes').click(function(){
		var at = [];
		$('.ch').each(function(index){
			if($(this).prop("checked")) at.push($(this).val());	
		});
		
		if(at.length > 0){
		xhr = $.ajax({
			url : "/admin/ajax",
			type : "post",
			data : { "ajax_action" : "delete_attribute", "attributes" : at},
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				for( var i in at){
					$('#raw_'+at[i]).remove();
				}
				console.log(data);
			}
		});
		}
		
	});
});
</script>
	
<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Атрибуты</h2>
<div id="wrap-main">
	<div>
		<h3>Основные действия над атрибутами</h3>
		<a href="/admin/attributes/?at=new" class="button-controls add">Добавить атрибут</a>
		<button id="delete-attributes" class="button-controls delete">Удалить отмеченные атрибуты</button>
	</div>
	<table class="main-table" >
		<thead>
			<tr>
				<td class='all-align-center'><input type="checkbox" id="all-check" /></td>
				<td>ID</td>
				<td>Название</td>
				<td>Тип</td>
			</tr>
		</thead>
		<tbody>
			
			<?php 
				foreach($attributes as $i => $attribute)
				{
					$alt = ($i%2 ==0)? "alt":"";
		echo "
			<tr id='raw_{$attribute['at_id']}'>
				<td class=\"{$alt} all-align-center\"><input class=\"ch\" type=\"checkbox\" value=\"{$attribute['at_id']}\" /></td>
				<td class=\"{$alt}\">{$attribute['at_id']}</td>
				<td class=\"{$alt}\"><a href=\"/admin/attributes/?at={$attribute['at_id']}\">"._(apply_filters('name-ru', $attribute['at_name']))."</a></td>
				<td class=\"{$alt}\">{$attribute['at_type']}</td>
			</tr>";
					
				}
			?>
			
		</tbody>
	</table>
</div>
