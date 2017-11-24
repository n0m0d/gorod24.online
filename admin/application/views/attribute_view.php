<?php
	$attribute = $data['attribute'];
	$options = $data['options'];
?>
<script>
var raw = '<tr class="option new-option-raw " data-id="">\
						<td>+</td>\
						<td><input class="option-name new-option" type="text" value="" /></td>\
						<td><input class="option-key" type="text" value="" /></td>\
						<td><input type="radio" name="default" class="option-default" /></td>\
						<td><button name="del_option" type="button" class="button-controls delete del_option">Удалить</button></td>\
						</tr>';
$(function(){
	$('#save').click(function(event){
		var $save = $(this);
		<?php 
		echo apply_filters('the_save_js_the_post_name', 'var post_name = $("#post-name").val();');
		?>
		var atribute_type = $('#type-attribute-select').val();
		var at_key = $('#at_key').val();
		var at_comment = $('#at_comment').val();
		var default_val = $('#default-val').val();
		var del = [];
		var update = [];
		var add = [];
		var s = true;
		
		$('.option-raw').each(function( index ){
			var $raw = $(this);
			var id = $raw.attr('data-id');
			var val = $raw.find('.option-name').val();
			var key = $raw.find('.option-key').val();
			var def = ($raw.find('.option-default').prop("checked"))?1:0;
			
			var orig = $raw.find('.orig-name').val();
			var orig_key = $raw.find('.orig-key').val();
			var orig_def = $raw.find('.orig-def').val();

			if($raw.css('display')=='none') {
				del.push($raw.attr('data-id'));
				$raw.remove();
				return true;
			} 
			else {
				if(val != orig || key != orig_key || def != orig_def){
					if(val != '' && key != ''){
						$raw.find('.orig-name').val(val);
						$raw.find('.orig-key').val(key);
						$raw.find('.orig-def').val(def);
						update.push({ id : id, name : val, key : key, defval : def });
					} else { 
						alert("Поля опций \"Название\" и \"Ключевое слово\" обязательны для заполнения"); s = false;
						return false;
					}
				}
			}
		});
		
		$('.new-option-raw').each(function( index ){
			var i = $('.new-option-raw').length - 1;
			if( i > index){
				var $raw = $(this);
				var val = $raw.find('.option-name').val();
				var key = $raw.find('.option-key').val();
				var def = ($raw.find('.option-default').prop("checked"))?1:0;
				if(val != '' && key != ''){
					add.push({ name : val, key : key, defval : def });	
				} else { 
					alert("Поля опций \"Название\" и \"Ключевое слово\" обязательны для заполнения"); s = false;
					return false;
				}
			}
		});
		
		var send = {};
		send.at_id = $("#at_id").val();
		send.post_name = post_name;
		send.atribute_type = atribute_type;
		send.at_key = at_key;
		send.at_comment = at_comment;
		send.default_val = default_val;
		send.del = del;
		send.update = update;
		send.add = add;
		if(at_key == '') { alert("Поле \"Ключевое слово\" обязательно для заполнения"); return false;}
		
		if(s){
		xhr = $.ajax({
			url : "/admin/ajax",
			type : "post",
			dataType: 'json',
			data : { "ajax_action" : "update_attribute", "send" : send},
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				history.replaceState({foo: 'bar'}, '', '/admin/attributes/?at='+data['at_id']);
				$("#at_id").val(data['at_id']);
				var options = data['options']
				$('#options-select .new-option-raw').remove();
				for( var i in options){
					var id = options[i]['option_id'];
					var name = options[i]['name'];
					var key = options[i]['key'];
					var defval = options[i]['defval'];
					var checked = (defval ==1)?'checked="checked"':'';
					$('#options-select tbody').append('<tr id="option_'+id+'" class="option option-raw" data-id="'+id+'">\
							<td>'+id+'</td>\
							<td><input class="option-name" type="text" value="'+name+'"><input type="hidden" name="orig_name" class="orig-name" value="'+name+'"></td>\
							<td><input class="option-key" type="text" value="'+key+'"><input type="hidden" name="orig_key" class="orig-key" value="'+key+'"></td>\
							<td><input type="radio" name="default" class="option-default" '+checked+'><input type="hidden" name="orig_def" class="orig-def" value="'+defval+'"></td>\
							<td><button name="del_option" type="button" class="button-controls delete del_option">Удалить</button></td>\
						</tr>');
				}
				$('#options-select tbody').append(raw);
				console.log(data);
			}
		});
		}
	});
	
		$(document).on('click', '.del_option', function(event){
			var $button = $(this);
			var $raw = $button.parent().parent();
			var $input = $raw.find('.option-name');
			var val = $input.val();
			if(val.length > 0){ 
				$raw.hide();
				if($raw.hasClass('new-option-raw')) $raw.remove();
			}
		});
		
		$(document).on('change keyup input click', '.new-option', function(event){
			var $input = $(this);
			var $raw = $input.parent().parent();
			var val = $input.val();

			if(val.length > 0){
				if($raw.is(':last-child')){
					$('#options-select tbody').append(raw);
				}
			} else {
			}
		});
		
		$(document).on('change keyup input click', '.new-option:eq(-2)', function(event){
			var $input = $(this);
			var $raw = $input.parent().parent();
			var val = $input.val();
			if(val.length == 0)	$('.new-option-raw:last').remove();
		});
		
		$(document).on('change keyup input click', '.new-option', function(event){
			var $input = $(this);
			var $raw = $input.parent().parent();
			var val = $input.val();
			if(val.length == 0 && $raw.is(':last-child') == false && $raw.is(':first-child') == false){	
				var $next = $raw.next();
				$next.find('.new-option').focus();
				$raw.remove(); 
			}
		});
		
		$('#type-attribute-select').change(function(event){
			return;
			var val = $(this).val();
			if(val == 'text' || val == 'bigtext' || val == 'check'){
				$('#textAttributeVal').show();
				$('#optionAttributeVal').hide();
			}
			if(val == 'select' || val == 'radio'){
				$('#textAttributeVal').hide();
				$('#optionAttributeVal').show();
			}

		});
});
</script>
<script>
<?php do_action('admin_script'); ?>
</script>
<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Редактировать атрибут</h2>
<div id="wrap-main">
	<table class="page-table" width="100%">
	<tr>
		<td class="page-view" style="width:auto; padding-right:20px;">
		<input type="hidden" value="<?php echo (!is_array($attribute) and $attribute=='new')? 'new' :$attribute['at_id']; ?>" id="at_id" />
			<div class="postarea">
				<label><h3>Название</h3></label>
				<?php 
				echo apply_filters('the_admin-name-ru', ((!is_array($attribute) and $attribute=='new')? '' : $attribute['at_name']));
				?>			
			</div>
			<div id="values">
				<?php
				//$textDisplay = "display:none;";	$optionDisplay = "display:none;";
				
				if($attribute['at_type']=='text' or $attribute=='new'){
					$textDisplay = "display:block;";
				}
				elseif($attribute['at_type']=='select'){
					$optionDisplay = "display:block;";
				}
	
					echo '
					<div id="" style="margin-bottom:20px;">
					<label><h3>Ключевое слово, для использования в коде и запросах (латиницей):</h3></label>
					<div class="border"><input id="at_key" type="text" value="'. ((!is_array($attribute) and $attribute=='new')? '' :$attribute['at_key']).'"></div>
					</div>';
					
					echo '
					<div id="" style="margin-bottom:20px;">
					<label><h3>Комментарий (небольшое пояснение сути атрибута):</h3></label>
					<div class="border"><textarea id="at_comment" style="width:100%;resize:vertical;">'. ((!is_array($attribute) and $attribute=='new')? '' :$attribute['at_comment']).'</textarea></div>
					</div>';
					
					echo '
					<div id="textAttributeVal" style="'.$textDisplay.'">
					<label><h3>Значение по умолчанию:</h3></label>
					<div class="border"><input id="default-val" type="text" value="'. ((!is_array($attribute) and $attribute=='new')? '' : $attribute['at_defval']).'"></div>
					</div>';
					echo '
					<div id="optionAttributeVal" style="'.$optionDisplay.'">
					<label><h3>Список значений:</h3></label>
					<table id="options-select" class="main-table block-select all-align-center">
					<thead>
						<tr>
							<td style="width: 30px;">ID</td>
							<td>Название</td>
							<td style="width: 200px;">Ключевое слово</td>
							<td style="width: 115px;">По умолчанию</td>
							<td style="width: 100px;">Дейсвия</td>
						</tr>
					</thead>
					<tbody>
					';
					
					foreach($options as $i => $option){
						echo '<tr id="option_'.$option['option_id'].'" class="option option-raw" data-id="'.$option['option_id'].'">
							<td>'.$option['option_id'].'</td>
							<td><input class="option-name" type="text" value="'.$option['option_name'].'" /><input type="hidden" name="orig_name" class="orig-name" value="'.$option['option_name'].'" /></td>
							<td><input class="option-key" type="text" value="'.$option['option_key'].'" /><input type="hidden" name="orig_key" class="orig-key" value="'.$option['option_key'].'" /></td>
							<td><input type="radio" name="default" class="option-default" '.(($option['option_default']==1)?'checked="checked"':'').'/><input type="hidden" name="orig_def" class="orig-def" value="'.$option['option_default'].'" /></td>
							<td><button name="del_option" type="button" class="button-controls delete del_option">Удалить</button></td>
						</tr>';
					}
					echo '<tr class="option new-option-raw " data-id="">
						<td>+</td>
						<td><input class="option-name new-option" type="text" value="" /></td>
						<td><input class="option-key" type="text" value="" /></td>
						<td><input type="radio" class="option-default" /></td>
						<td><button name="del_option" type="button" class="button-controls delete del_option">Удалить</button></td>
						</tr>';
					echo '</tbody></table>
					</div>';
				?>
			</div>
			<span class="log"></span>
		</td>
		<td style="width:260px;">
			<div class="right-block" id="page-controls">
				<div class="controls-header">Основные действия:</div>
				<div class="controls-body">
					<div class="controls-body-row"><form>
						<button id="save" name = "submitbtn" type="button" class="button-controls save">Сохранить</button>
						<a href="/admin/attributes/?at=new" class="button-controls add">Добавить атрибут</a>
					</form></div>
				</div>
			</div>
			
			<div class="right-block" id="links-controls">
				<div class="controls-header">Тип атрибута:</div>
				<div class="controls-body">
					<div class="controls-body-row">
					<select id="type-attribute-select" class="block-select">
						<option value="text" <?php echo ($attribute['at_type']=='text')?'selected':'' ?>>Текстовое поле</option>
						<option value="bigtext" <?php echo ($attribute['at_type']=='bigtext')?'selected':'' ?>>Большое текстовое поле</option>
						<option value="select" <?php echo ($attribute['at_type']=='select')?'selected':'' ?>>Поле select</option>
						<option value="radio" <?php echo ($attribute['at_type']=='option')?'selected':'' ?>>Поле radio</option>
						<option value="check" <?php echo ($attribute['at_type']=='check')?'selected':'' ?>>Поле checkbox</option>
					</select>
					</div>
				</div>
			</div>
		</td>
	</tr>
	</table>

</div>
</div>
