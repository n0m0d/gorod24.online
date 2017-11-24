<?php

	$main = $data['group'];
	$qroups = $data['qroups'];
	
	$all_attributes=array();
?>
<script>
var raw = '<tr class="option new-option-raw " data-id="">\
						<td>+</td>\
						<td><select class="new-at-select block-select new-at" style="height: 43px;">\
						<option value="-1">Выберите атрибут</option>\
						<?php
						foreach($all_attributes as $i => $at){
							echo '<option value="'.$at['at_id'].'">'.apply_filters('name-ru',$at['at_name']).'</option>';
						}
						?>\
						</select></td>\
						<td><input class="option-key" type="text" value="" /></td>\
						<td><button name="del_option" type="button" class="button-controls delete del_option">Удалить</button></td>\
					</tr>\
';	

var previous;
function undisable_attr(id){
	var option = $(raw).find('option[value='+id+']').clone().wrap('<div/>').parent().html();
	var noption = $(option).removeAttr('disabled').clone().wrap('<div/>').parent().html();
	raw = raw.replace(option, noption);
	$('.new-at-select').find('option[value='+id+']').removeAttr('disabled');
}

function disable_attr(id){
	var option = $(raw).find('option[value='+id+']').clone().wrap('<div/>').parent().html();
	var noption = $(option).attr('disabled','disabled').clone().wrap('<div/>').parent().html();
	raw = raw.replace(option, noption);
	$('.new-at-select').find('option[value='+id+']').attr('disabled','disabled');
}

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
$(function(){
	
	$("#post-name, #post-name-en").on('change keyup input click',function(){
		
	});
	
	
	$(document).on('click', '.del_option', function(event){
		var $button = $(this);
		var $raw = $button.parent().parent();
		
		if($raw.is(':last-child') == false){
			$raw.hide();
			if($raw.hasClass('new-option-raw')){
				var $select = $raw.find('.new-at-select');
				var id = $select[0].value;
				undisable_attr(id);
				$raw.remove();
			}
			else {
				var $select = $raw.find('.attribute');
				var id = $select.attr('data-id');
				undisable_attr(id);
				$raw.hide();
			}
		}
	
	});

	$('.option-raw .attribute').each(function(i){
		var id = $(this).attr('data-id');
		disable_attr(id);
	});

	
	$(document).on('focus click', '.new-at-select', function () {
        previous = this .value;
		$(this).find('option[value='+previous+']').removeAttr('disabled');
	})
	.on('change', '.new-at-select', function(event){
		var $select = $(this);
		var $raw = $select.parent().parent();
		var val = $select.val();
		var $next = $raw.next();
		
		undisable_attr(previous);
		if(val == -1){
			$next.find('.new-at').focus();
			$raw.remove(); 
		} 
		else{
			disable_attr(val);			
			if($raw.is(':last-child') != false){
				$('#attributes-select tbody').append(raw);
			}
			
		}
		
		
	});
	
	
	$('#save').click(function(event){
		<?php 
		echo apply_filters('the_save_js_the_content', 'if (tinymce.editors.length > 0) {var content = tinyMCE.get("editor-text").getContent();}');
		?>

		<?php echo apply_filters('admin-group-send-array', '
		var send = {};
		send.main_id = $("#main-id").val();
		send.main_name = $("#main-name").val();
		send.main_status = ($("#main-status").prop("checked"))?1:0;
		send.main_photo = $("#main-photo").val();
		send.main_parent = $("#main-parent").val();
		send.main_description = content;
		'); ?>
		
		
		
		console.log(JSON.stringify(send));
		xhr = $.ajax({
			url : "/admin/ajax/",
			type : "post",
			data : { "ajax_action" : "update_productgroup", "send" : send},
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				console.log(data);
				if (IsJsonString(data)) {
				data = JSON.parse(data);
				history.replaceState({foo: 'bar'}, '', '/admin/productsgroups/?id='+data['group_id']);
				$('#main-id').val(data['group_id']);
				$('#main-id-d').val(data['group_id']);
				$('#main-createdate').text(data['group']['group_createdate']);
				$('#post_status-text').text( (data['group']['group_status'] == 1)? 'Активен': 'Неактивен' );
				}
			},
			error : function( jqXHR, textStatus, errorThrown ){
							console.error(errorThrown);
			}
		});
		
		
	});
});
</script>
<script>
<?php do_action('admin_script'); ?>
</script>
<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Редактировать т/у</h2>
<div id="wrap-main">
	<table class="page-table" width="100%">
	<tr>
		<td class="page-view" style="width:auto; padding-right:20px;">
		<input type="hidden" value="<?php echo (!is_array($main) and $main=='new')? 'new' :$main['group_id']; ?>" id="main-id" />
			
			<div class="margintop30">
				<label><h3>ID</h3></label>
				<div class="border" style="text-align:center;"><input style="width:300px;text-align:center;" disabled="disabled" id="main-id-d" type="text" value="<?php echo ((!is_array($main) and $main=='new')? '' : $main['group_id']); ?>"></div>		
			</div>
			
			<div class="margintop30">
				<label><h3>Название</h3></label>
				<div class="border"><input id="main-name" type="text" value="<?php echo ((!is_array($main) and $main=='new')? '' : $main['group_name']); ?>"></div>		
			</div>
			
			<div class="margintop30">
				<label><h3>Фото (перечислить через запятую URL картинок)</h3></label>
				<div class="border"><input id="main-photo" type="text" value="<?php echo ((!is_array($main) and $main=='new')? '' : $main['group_photo']); ?>"></div>		
			</div>
			
			<div class="margintop30">
				<label><h3>Описание:</h3></label>
				<div class="tabs-body">
					<?php 
					echo apply_filters('the_editor_content', ((!is_array($main) and $main=='new')? '' : $main['group_description']));
					?>
				</div>
			</div>
			
			<span class="log"></span>
		</td>
		<td style="width:260px;">
			<div class="right-block" id="page-controls">
				<div class="controls-header">Основные действия:</div>
				<div class="controls-body">
					<div class="controls-body-row">Статус: <strong id="post_status-text"><?= (($main['group_status'] == 1)? "Активен " : "Неактивен"); ?></strong><div class="switch demo3"><input id="main-status" type="checkbox" <?= (($main['group_status'] == 1)? "checked='checked'" : ""); ?>><label><i></i></label></div> </div>
					<div class="controls-body-row">Дата создания: <strong id="main-createdate"><?= ((!is_array($main) and $main=='new')? '' :$main['group_createdate']); ?></strong></div>
					<div class="controls-body-row"><form><button id="save" name = "submitbtn" type="button" class="button-controls save">Обновить</button></form></div>
				</div>
			</div>
			
			<div class="right-block" id="page-controls">
				<div class="controls-header">Родительская группа:</div>
				<div class="controls-body">
					<div class="controls-body-row"><select id="main-parent" class="block-select">
					<option value="0">Выберите группу</option>
					<?php
					foreach ($qroups as $i => $g){
						$s = ($g['group_id']== $main['group_parent'])?'selected="selected"':'';
						if($g['group_id'] != $main['group_id'])
						echo '<option value="'.$g['group_id'].'" '.$s.'>'.$g['group_name'].'</option>';
						
					}
					?>
					</select></div>
				</div>
			</div>
			
			<?php do_action('admin-group-right-blocks', $main['group_id'], $main, $qroups); ?>
			
		</td>
	</tr>
	</table>

</div>
</div>
