<?php

	$page = $data['page'];
	$all_attributes = $data['all_attributes'];
	$pages = $data['pages'];
	if ($page != 'new'){
	$attributes = $data['attributes'];
	$childs = $data['childs'];
	$new = array();
	foreach($pages as $i=>$p){
		$mark=true;
		foreach($childs as $j=>$c){
			if($p['post_id'] == $c['post_id'] or $p['post_id']==$page['post_id']) $mark=false;
		}
		if($mark)
			$new[]=$p;
	}
	} else {
		$attributes = array();
		$childs = array();
		$new = $pages;
	}
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

$(function(){
	
	$("#post-name, #post-name-en").on('change keyup input click',function(){
		
	});
	
	$('#save').click(function(event){
		<?php 
		echo apply_filters('the_save_js_the_post_name', 'var post_name = $("#post-name").val();');
		?>
		
		<?php 
		echo apply_filters('the_save_js_the_content', 'var content = tinyMCE.get("editor-text").getContent();');
		?>
		
		var send = {};
		send.post_id = $("#post_id").val();
		send.post_name_ru = post_name;
		send.post_content = content;
		
		xhr = $.ajax({
			url : "/admin/ajax",
			type : "post",
			dataType: 'json',
			data : { "ajax_action" : "update_mail_template", "send" : send},
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				console.log(data);
				
				history.replaceState({foo: 'bar'}, '', '/admin/mails/?id='+data['post_id']);
				$('#post_id').val(data['post_id']);
				$('#post_date').text(data['post']['post_date']);
				$('#last-modified').text(data['post']['post_modified']);
				$('#post_status-text').text( (data['post']['post_status'] == 1)? 'Опубликовано': 'Неактивно' );
				
				/**/
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.error(jqXHR);
			}
		});
		
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
	
});
</script>
<script>
<?php do_action('admin_script'); ?>
</script>
<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2><?php echo (!is_array($page) and $page=='new')? 'Создать шаблон письма' :'Редактировать шаблон письма'; ?></h2>
<div id="wrap-main">
	<table class="page-table" width="100%">
	<tr>
		<td class="page-view" style="width:auto; padding-right:20px;">
		<input type="hidden" value="<?php echo (!is_array($page) and $page=='new')? 'new' :$page['post_id']; ?>" id="post_id" />
			<div class="postarea">
				<label><h3>Название шаборна</h3></label>
				
				<?php 
				echo apply_filters('the_admin-name-ru', ((!is_array($page) and $page=='new')? '' : $page['post_name']));
				?>			
			</div>
			<div class="margintop30" id="editor">
			
			<div class="tabs">
				<?php 
				echo apply_filters('the_editor_tabs', '');
				?>
			</div>
			
			<div class="tabs-body">
				<?php 
				echo apply_filters('the_editor_content', ((!is_array($page) and $page=='new')? '' : $page['post_content']));
				?>
			</div>
			</div>
			
			<span class="log"></span>
		</td>
		<td style="width:260px;">
			<div class="right-block" id="page-controls">
				<div class="controls-header">Основные действия:</div>
				<div class="controls-body">
					<div class="controls-body-row">Дата создания: <strong id="post_date"><?= ((!is_array($page) and $page=='new')? '' :$page['post_date']); ?></strong></div>
					<div class="controls-body-row">Дата изменения: <strong id="last-modified"><?= ((!is_array($page) and $page=='new')? '' : $page['post_modified']); ?></strong></div>
					<div class="controls-body-row"><form><button id="save" name = "submitbtn" type="button" class="button-controls save"><?php echo (!is_array($page) and $page=='new')? 'Создать' :'Обновить'; ?></button></form></div>
				</div>
			</div>
			
		</td>
	</tr>
	</table>

</div>
</div>
