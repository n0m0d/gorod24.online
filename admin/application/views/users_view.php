<?php
	$users = $data['users'];
	$page = (int)((isset($_GET['page']))?$_GET['page']:1);
	$n=$users[0]['n'];
	$maxrows=$users[0]['limit'];

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
	
	$('#delete-users').click(function(){
		var users = [];
		$('.ch').each(function(index){
			if($(this).prop("checked")) users.push($(this).val());	
		});
		
		if(users.length > 0){
		xhr = $.ajax({
			url : "/admin/ajax",
			type : "post",
			data : { "ajax_action" : "delete_users", "users" : users},
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				for( var i in users){
					$('#raw_'+users[i]).remove();
				}
				console.log(data);
			}
		});
		}
		
	});
});
</script>
<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Пользователи</h2>
<div id="wrap-main">
	<div>
		<h3>Основные действия над пользователями</h3>
		<a href="/admin/users/?id=new" class="button-controls add">Добавить пользователя</a>
		<button id="delete-users" class="button-controls delete">Удалить отмеченных пользователей</button>
	</div>
	<div class="page-view">
		<form action="/admin/users/" method="get">
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
				<td>Логин</td>
				<td>ФИО</td>
				<td>Дата регистрации</td>
				<td>Статус</td>
			</tr>
		</thead>
		<tbody>
			
			<?php 
				foreach($users as $i => $item)
				{
					$alt = ($i%2 ==0)? "alt":"";
		echo "
			<tr id='raw_{$item['user_id']}'>
				<td class=\"{$alt} all-align-center\"><input class=\"ch\" type=\"checkbox\" value=\"{$item['user_id']}\" /></td>
				<td class=\"{$alt}\">{$item['user_id']}</td>
				<td class=\"{$alt}\"><a href=\"/admin/users/?id={$item['user_id']}\">"._(apply_filters('name-ru', $item['user_login']))."</a></td>
				<td class=\"{$alt}\"><a href=\"/admin/users/?id={$item['user_id']}\">"._(apply_filters('name-ru', $item['user_name']))."</a></td>
				<td class=\"{$alt}\">{$item['user_registered']}</td>
				<td class=\"{$alt}\">{$item['user_status']}</td>
			</tr>";
					
				}
			?>
			
		</tbody>
	</table>
		<?php
		echo do_shortcode('[pagenavigation n="'.$n.'" limit="'.$maxrows.'" page="'.$page.'"]');
		?>
</div>
