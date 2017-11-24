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
		
		var del = [];
		var update = [];
		var add = [];
		var s = true;
		
		$('.option-raw').each(function( index ){
			var $raw = $(this);
			var id = $raw.attr('data-id');
			var key = $raw.find('.option-key').val();
			var orig_key = $raw.find('.orig-key').val();

			if($raw.css('display')=='none') {
				del.push($raw.attr('data-id'));
				$raw.remove();
				return true;
			} 
			else {
				if(key != orig_key){
					$raw.find('.orig-key').val(key);
					update.push({ id : id, option_filter : key });
				}
			}
		});

		$('.new-option-raw').each(function( index ){
			var i = $('.new-option-raw').length - 1;
			if( i > index){
				var $raw = $(this);
				var val = $raw.find('.new-at-select').val();
				var key = $raw.find('.option-key').val();
				if(val != '-1'){
					add.push({ at_id : val, option_filter : key });	
				} else { 
					alert("Поле \"Название\" обязательно для заполнения"); s = false;
					return false;
				}
			}
		});
		
		if($("#link").text() == '') {
			$("#link").text(toURL($("#post-name, #post-name-en").val()));
		}
		<?php echo apply_filters('admin-page-send-array', '
		var send = {};
		send.post_id = $("#post_id").val();
		send.post_name =  $("#link").text();
		send.post_name_ru = post_name;
		send.post_status = ($("#post_status").prop("checked"))?1:0;
		send.post_template = $("#post-template").val();
		send.post_content = content;
		send.post_parent = $("#parent-select").val();
		send.post_is_main = ($("#to_main").prop("checked")?1:0);
		
		send.post_title = $("#post-title").val();
		send.post_description = $("#post-description").val();
		send.post_keywords = $("#post-keywords").val();
		send.post_image = $("#post-image").val();
		
		send.del = del;
		send.update = update;
		send.add = add;
		', $page['post_id'], $page, $attributes, $pages); ?>
		if(send.post_name == '' || send.post_name_ru == '') { alert("Странице нужен заголовок и URI адресс"); return;}
		tinymce.triggerSave();
		var forms = $('form').serialize();
		//return;
		if(s){
		xhr = $.ajax({
			url : "/admin/ajax",
			type : "post",
			dataType: 'json',
			data : { "ajax_action" : "update_page", "send" : send},
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				console.log(data);
				
				history.replaceState({foo: 'bar'}, '', '/admin/pages/?id='+data['post_id']);
				$('#post_id').val(data['post_id']);
				$('#post_date').text(data['post']['post_date']);
				$('#last-modified').text(data['post']['post_modified']);
				$('#post_status-text').text( (data['post']['post_status'] == 1)? 'Опубликовано': 'Неактивно' );
				
				var post_parent = (data['post']['post_parent'] == null) ? '' : data['post']['post_parent'];
				var parent_name =(data['post']['post_parent'] == null)? '' : $('#parent-select option:selected').text();
				$('#parent-link a').attr('href', '/admin/pages/?id='+post_parent).text(parent_name);
				
				var addedAttributes = data['addedAttributes']
				$('#attributes-select .new-option-raw').remove();
				for( var i in addedAttributes){
					var id = addedAttributes[i]['id'];
					var post_id = addedAttributes[i]['post_id'];
					var at_id = addedAttributes[i]['at_id'];
					var option_filter = addedAttributes[i]['option_filter'];
					var locale_name = addedAttributes[i]['attr']['locale_name'];
					
					$('#attributes-select tbody').append('<tr id="option_'+id+'" class="option option-raw" data-id="'+id+'">\
							<td>'+id+'</td>\
							<td class="attribute" data-id="'+at_id+'"><span calss="attribute-name" >'+locale_name+'</span></td>\
							<td><input class="option-key" type="text" value="'+option_filter+'" /><input type="hidden" name="orig_key" class="orig-key" value="'+option_filter+'" /></td>\
							<td><button name="del_option" type="button" class="button-controls delete del_option">Удалить</button></td>\
						</tr>');
				}
				$('#attributes-select tbody').append(raw);
				
				/**/
			}
		});
		}
		
		
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
	
	$( document ).on('click', '#unphoto', function(e){
			$('#page-photo #dis_photo_id, #post-image').val('');
			$('#page-photo .img-container img').attr('src', '/img/no-photo.png');
	});
	
	$( document ).on('click', '.file-popup', function(e){
		e.preventDefault();
		openFilesPopup({
			onSelect:function(result){
				$('#page-photo #dis_photo_id').val(result.id);
				$('#page-photo .img-container img').attr('src', result.src);
				$('#post-image').val(result.src);
			}
		});
	});
	
});
</script>
<script>
<?php do_action('admin_script'); ?>
</script>
<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Редактировать страницу</h2>
<div id="wrap-main">
	<table class="page-table" width="100%">
	<tr>
		<td class="page-view" style="width:auto; padding-right:20px;">
		<form id="main-content-form">
		<input name="post_id" type="hidden" value="<?php echo (!is_array($page) and $page=='new')? 'new' :$page['post_id']; ?>" id="post_id" />
			<div class="postarea">
				<label><h3>Заголовок</h3></label>
				
				<?php 
				echo apply_filters('the_admin-name-ru', ((!is_array($page) and $page=='new')? '' : $page['post_name_ru']));
				?>			
				<div id="permalink"><strong>Постоянная ссылка:</strong> http://<?= $this->registry['domain'];?><span class="link" id="link"><?= ((!is_array($page) and $page=='new')? '' : $page['post_name']); ?></span>/<button type="button" class="button-controls" id="change-link">Изменить</button><button type="button" class="button-controls" id="save-change-link" style="display:none;">Ok</button><button type="button" class="button-controls" id="cancel-change-link" style="display:none;">Отмена</button></div>
				<script>
				$(function(){
					$('#change-link').click(function(){
						$(this).hide();
						$('#save-change-link, #cancel-change-link').show();
						$("#link").html('<input style="width:auto;" type="text" class="new" value="'+$("#link").text()+'" /><input style="width:auto;" type="hidden" class="old" value="'+$("#link").text()+'" />');
					});
					
					$('#save-change-link, #cancel-change-link').click(function(){
						$('#save-change-link, #cancel-change-link').hide();
						$('#change-link').show();
						console.log(this.id);
						if(this.id=='save-change-link'){
							$("#link").html($("#link input.new").val());
						}else{
							$("#link").html($("#link input.old").val());
						}
					});
				});
				</script>
			</div>
			<div class="margintop30" id="editor">
		</form>	
		
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
			
		<form id="main-seo-form">	
			<div class="margintop30">
				<label><h3>Мета-теги (SEO):</h3></label>
				<table class="main-table all-align-center">
					<tr><td style="width:100px;">title:</td><td><input name="post_title" id="post-title" style="height:100%; text-align:left !important;" type="text" value="<?= ((!is_array($page) and $page=='new')? '' : $page['post_title'] ); ?>" /></td></tr>
					<tr><td style="width:100px;">description:</td><td><input name="post_description" id="post-description" style="height:100%; text-align:left !important;" type="text" value="<?= ((!is_array($page) and $page=='new')? '' : $page['post_description']); ?>" /></td></tr>
					<tr><td style="width:100px;">keywords:</td><td><input name="post_keywords" id="post-keywords" style="height:100%; text-align:left !important;" type="text" value="<?= ((!is_array($page) and $page=='new')? '' : $page['post_keywords']); ?>" /></td></tr>
					<tr><td style="width:100px;">image:</td><td><input name="post_image" id="post-image" style="height:100%; text-align:left !important;" type="text" value="<?= ((!is_array($page) and $page=='new')? '' : $page['post_image']); ?>" /></td></tr>
				</table>
			</div>
		</form>
		
		<form id="main-attributes-form">
			<div class="margintop30">
				<label><h3>Атрибуты:</h3></label>
				<table id="attributes-select" class="main-table block-select all-align-center">
					<thead>
						<tr>
							<td style="width: 30px;">ID</td>
							<td>Название</td>
							<td style="width: 200px;">Фильтровать по ключевому слову</td>
							<td style="width: 100px;">Дейсвия</td>
						</tr>
					</thead>
					<tbody>
					<?php
					foreach($attributes as $i => $at){
						echo '<tr id="option_'.$at['id'].'" class="option option-raw" data-id="'.$at['id'].'">
							<td>'.$at['id'].'</td>
							<td class="attribute" data-id="'.$at['at_id'].'"><span calss="attribute-name" >'.apply_filters('name-ru',$at['at_name']).'</span></td>
							<td><input class="option-key" type="text" value="'.$at['option_filter'].'" /><input type="hidden" name="orig_key" class="orig-key" value="'.$at['option_filter'].'" /></td>
							<td><button name="del_option" type="button" class="button-controls delete del_option">Удалить</button></td>
						</tr>';
					}
					
					?>
					<tr class="option new-option-raw " data-id="">
						<td>+</td>
						<td><select class="new-at-select block-select new-at" style="height: 43px;">
						<option value="-1">Выберите атрибут</option>
						<?php
						foreach($all_attributes as $i => $at){
							echo '<option value="'.$at['at_id'].'">'.apply_filters('name-ru',$at['at_name']).'</option>';
						}
						?>
						</select></td>
						<td><input class="option-key" type="text" value="" /></td>
						<td><button name="del_option" type="button" class="button-controls delete del_option">Удалить</button></td>
					</tr>
					</tbody>
				</table>
					
			</div>
		</form>	
			<span class="log"></span>
		</td>
		<td style="width:260px;">
		
			<div class="right-block" id="page-controls">
				<div class="controls-header">Основные действия:</div>
				<div class="controls-body">
				<form id="main-status-form">
					<div class="controls-body-row">Статус: <strong id="post_status-text"><?= (($page['post_status'] == 1)? "Опубликовано " : "Неактивно"); ?></strong><div class="switch demo3"><input name="post_status" id="post_status" type="checkbox" <?= (($page['post_status'] == 1)? "checked='checked'" : ""); ?>><label><i></i></label></div> </div>
				</form>	
					<div class="controls-body-row">Дата создания: <strong id="post_date"><?= ((!is_array($page) and $page=='new')? '' :$page['post_date']); ?></strong></div>
					<div class="controls-body-row">Дата изменения: <strong id="last-modified"><?= ((!is_array($page) and $page=='new')? '' : $page['post_modified']); ?></strong></div>
					<div class="controls-body-row"><input type="checkbox" id="to_main" value="<?= $page; ?>" <?= (((!is_array($page) and $page=='new') or $page['post_is_main']!=1)? '' : 'checked="checked"'); ?> /> <label for="to_main">Сделать главной</label></div>
					<div class="controls-body-row"><form><button id="save" name = "submitbtn" type="button" class="button-controls save">Обновить</button></form></div>
				</div>
			</div>
			
			<form id="main-template-form">
			<div class="right-block" id="page-template">
				<div class="controls-header">Шаблон страницы:</div>
				<div class="controls-body">
					<div class="controls-body-row">Шаблон: <select name="post_template" id="post-template" class="block-select"><option value="">Шаблон по умолчанию</option><?= get_page_template_options($page['post_template']); ?></select></div>
				</div>
			</div>
			</form>
			
			<div class="right-block" id="links-controls">
				<div class="controls-header">Связанные страницы:</div>
				<div class="controls-body">
					<div class="controls-body-row">
					<div style='padding:10px 0px;'>Родительская страница: <strong id="parent-link"><a href="/admin/pages/?page=<?= ((!is_array($page) and $page=='new')? '' :$page['post_parent']); ?>"><?= apply_filters('name-ru', ((!is_array($page) and $page=='new')? '' :$page['parent_name_ru'])); ?></a></strong></div>
					<form id="main-parent-form">
					<select name="post_parent" id="parent-select" class="block-select">
						<option value="null">Нет родителя</option><?php
						foreach ($new as $i => $page_){
							$selected = ($page_['post_id']==$page['post_parent'])?"selected" : "";
							echo "<option $selected value='{$page_['post_id']}'>".apply_filters('name-ru', $page_['post_name_ru'])."</option>";
						}
					?></select>
					</form>
					</div>
					<div class="controls-body-row">Дочерние страницы:<?php
						foreach ($childs as $i => $child){
							echo "<strong><a href='/admin/pages/?page={$child['post_id']}'>".apply_filters('name-ru',$child['post_name_ru'])."</a></strong>";
						}
					?></div>
				</div>
			</div>
			
			<?php 
			$url = ((!is_array($page) and $page=='new')? '' : $page['post_image']);
			$url = (!empty($url))? $url : '/img/no-photo.png';
			$url_ = (!empty($url))? $url : '';
			?>

			<div class="right-block" id="page-photo">
				<div class="controls-header">Фото:</div>
				<div class="controls-body">
				<form id="main-photo-form">
					<div class="file-popup controls-body-row">
						<input type="hidden" id="dis_photo_id" value="<?=$url_?>">
						<div class="img-container">
							<img src="<?= $url ?>" style="width:100%;height:auto;"/>
						</div>
					</div>
					<div class="controls-body-row"><button type="button" class="button" id="unphoto" >Отвязать фото</button></div>
				</form>
				</div>
			</div>

			
			<?php do_action('admin-page-right-blocks', $page['post_id'], $page, $attributes, $pages ); ?>
			
		</td>
	</tr>
	</table>

</div>
</div>
