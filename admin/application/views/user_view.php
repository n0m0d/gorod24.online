<?php

	$user = $data['user'];
	$contacts = $data['contacts'];
	$permissions = $data['permissions'];
	
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
	
	$('#save').click(function(event){
		
		
		var send = {};
		send.user_id = $("#user-id").val();
		send.user_login = $("#user-login").val();
		send.user_name = $("#user-name").val();
		send.user_status = ($('#user-status').prop("checked"))?1:0;
		
		var p1 = $('#user-psw1').val();
		var p2 = $('#user-psw2').val();
		if(p1 != '' || p2 != ''){
			if(p1 == p2){
				send.user_password = p1;
			}else{
				alert("Пароли не совпадают!");
				return;
			}
		}
		
		
		send.user_phones = $('#user-phones').val();
		send.user_emails = $('#user-mails').val();
		send.user_addres = $('#user-addres').val();
		
		send.user_permissions = [];
		$('.permissions').each(function(){
			send.user_permissions.push({'ac_res':$(this).attr('data-res'), 'ac_val': (($(this).prop("checked"))?1:0)});
		});
		
		send.user_addres_custom = [];
		$('.user-addres-custom').each(function(){
			send.user_addres_custom.push({'contact_type':$(this).attr('id'), 'contact_val': $(this).val()});
		});
		
		
		console.log(JSON.stringify(send));
		xhr = $.ajax({
			url : "/admin/ajax",
			type : "post",
			data : { "ajax_action" : "update_user", "send" : send},
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				console.log(data);
				if (IsJsonString(data)) {
				data = JSON.parse(data);
				
				
				history.replaceState({foo: 'bar'}, '', '/admin/users/?id='+data['user_id']);
				$('#user-id').val(data['user_id']);
				$('#user-id-d').val(data['user_id']);
				$('#user-registered').text(data['user']['user_registered']);
				$('#post_status-text').text( (data['user']['user_status'] == 1)? 'Активирован': 'Неактивирован' );
				}
			},
			error : function( jqXHR, textStatus, errorThrown ){
							console.error(errorThrown);
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
	
	$( "#address_index" ).autocomplete({
		source: function( request, response ) {$.getJSON( "/admin/ajax/", { "ajax_action": "get_autocomplete", "type": "address_index", "search": $( "#address_index" ).val() }, response  ) }
    });
	
	$( "#address_country" ).autocomplete({
		source: function( request, response ) {$.getJSON( "/admin/ajax/", { "ajax_action": "get_autocomplete", "type": "address_country", "search": $( "#address_country" ).val() }, response  ) }
    });
	
	$( "#address_city" ).autocomplete({
		source: function( request, response ) {$.getJSON( "/admin/ajax/", { "ajax_action": "get_autocomplete", "type": "address_city", "search": $( "#address_city" ).val() }, response  ) }
    });
	
	$( "#address_region" ).autocomplete({
		source: function( request, response ) {$.getJSON( "/admin/ajax/", { "ajax_action": "get_autocomplete", "type": "address_region", "search": $( "#address_region" ).val() }, response  ) }
    });
	
	$( "#address_streat" ).autocomplete({
		source: function( request, response ) {$.getJSON( "/admin/ajax/", { "ajax_action": "get_autocomplete", "type": "address_streat", "search": $( "#address_streat" ).val() }, response  ) }
    });
	
});
</script>
<script>
<?php do_action('admin_script'); ?>
</script>
<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Редактировать пользователя</h2>
<div id="wrap-main">
	<table class="page-table" width="100%">
	<tr>
		<td class="page-view" style="width:auto; padding-right:20px;">
		<input type="hidden" value="<?php echo (!is_array($user) and $user=='new')? 'new' :$user['user_id']; ?>" id="user-id" />
			
			<div class="margintop30">
				<label><h3>ID</h3></label>
				<div class="border" style="text-align:center;"><input style="width:300px;text-align:center;" disabled="disabled" id="user-id-d" type="text" value="<?php echo ((!is_array($user) and $user=='new')? '' : $user['user_id']); ?>"></div>		
			</div>
			
			<div class="margintop30">
				<label><h3>Логин</h3></label>
				<div class="border"><input id="user-login" type="text" value="<?php echo ((!is_array($user) and $user=='new')? '' : $user['user_login']); ?>"></div>		
			</div>
			<div class="margintop30">
				<label><h3>ФИО</h3></label>
				<div class="border"><input id="user-name" type="text" value="<?php echo ((!is_array($user) and $user=='new')? '' : $user['user_name']); ?>"></div>
			</div>

			<div class="margintop30">
				<label><h3>Изменить пароль:</h3></label>
				<table class="main-table all-align-center">
					<tbody>
						<tr><td style="width:200px;">Пароль:</td><td style="text-align:left !important;"><input name="psw1" id="user-psw1" style="height:100%; text-align:left !important;" type="password" value="" /></td></tr>
						<tr><td style="width:200px;">еще раз:</td><td style="text-align:left !important;"><input name="psw2" id="user-psw2" style="height:100%; text-align:left !important;" type="password" value="" /></td></tr>
					</tbody>
				</table>
			</div>
			
			<div class="margintop30">
				<label><h3>Контакты:</h3></label>
				<table class="main-table all-align-center">
					<tbody>
						<?php
						foreach($contacts as $i=>$c){
							$remind_b ='';
							if		($c['contact_type'] == 'phone'){ $phone .= $c['contact_val'].', '; }
							elseif	($c['contact_type'] == 'email'){ $email .= $c['contact_val'].', '; }
							elseif	($c['contact_type'] == 'address'){ $address .= $c['contact_val'].', '; }
							elseif	($c['contact_type'] == 'address_index'){ $address_index = $c['contact_val']; }
							elseif	($c['contact_type'] == 'address_country'){ $address_country = $c['contact_val']; }
							elseif	($c['contact_type'] == 'address_city'){ $address_city = $c['contact_val']; }
							elseif	($c['contact_type'] == 'address_region'){ $address_region = $c['contact_val']; }
							elseif	($c['contact_type'] == 'address_streat'){ $address_streat = $c['contact_val']; }
							elseif	($c['contact_type'] == 'address_buiding'){ $address_buiding = $c['contact_val']; }
							elseif	($c['contact_type'] == 'address_housing'){ $address_housing = $c['contact_val']; }
							elseif	($c['contact_type'] == 'address_apartment'){ $address_apartment = $c['contact_val']; }
							elseif	($c['contact_type'] == 'address_doorcode'){ $address_doorcode = $c['contact_val']; }
						}
						$phone = substr($phone,0,-2);
						$email = substr($email,0,-2);
						$address = substr($address,0,-2);
						?>
						<tr><td style="width:200px;">Телефон:</td><td style="text-align:left !important;"><input id="user-phones" style="height:100%; text-align:left !important;" type="text" value="<?= $phone ?>" /></td></tr>
						<tr><td style="width:200px;">E-mail:</td><td style="text-align:left !important;"><input id="user-mails" style="height:100%; text-align:left !important;" type="text" value="<?= $email ?>" /></td></tr>
						<tr><td style="width:200px;">Адрес:</td><td style="text-align:left !important;"><input id="user-addres" style="height:100%; text-align:left !important;" type="text" value="<?= $address ?>" /></td></tr>
						<tr><td style="width:200px;">Индекс:</td><td style="text-align:left !important;"><input id="address_index" class="user-addres-custom" style="height:100%; text-align:left !important;" type="text" value="<?= $address_index ?>" /></td></tr>
						<tr><td style="width:200px;">Страна:</td><td style="text-align:left !important;"><input id="address_country" class="user-addres-custom" style="height:100%; text-align:left !important;" type="text" value="<?= $address_country ?>" /></td></tr>
						<tr><td style="width:200px;">Город или поселок:</td><td style="text-align:left !important;"><input id="address_city" class="user-addres-custom" style="height:100%; text-align:left !important;" type="text" value="<?= $address_city ?>" /></td></tr>
						<tr><td style="width:200px;">Район:</td><td style="text-align:left !important;"><input id="address_region" class="user-addres-custom" style="height:100%; text-align:left !important;" type="text" value="<?= $address_region ?>" /></td></tr>
						<tr><td style="width:200px;">Улица:</td><td style="text-align:left !important;"><input id="address_streat" class="user-addres-custom" style="height:100%; text-align:left !important;" type="text" value="<?= $address_streat ?>" /></td></tr>
						<tr><td style="width:200px;">Номер дома:</td><td style="text-align:left !important;"><input id="address_buiding" class="user-addres-custom" style="height:100%; text-align:left !important;" type="text" value="<?= $address_buiding ?>" /></td></tr>
						<tr><td style="width:200px;">Корпус или строение:</td><td style="text-align:left !important;"><input id="address_housing" class="user-addres-custom" style="height:100%; text-align:left !important;" type="text" value="<?= $address_housing ?>" /></td></tr>
						<tr><td style="width:200px;">Номер квартиры:</td><td style="text-align:left !important;"><input id="address_apartment" class="user-addres-custom" style="height:100%; text-align:left !important;" type="text" value="<?= $address_apartment ?>" /></td></tr>
						<tr><td style="width:200px;">Код от подъезда:</td><td style="text-align:left !important;"><input id="address_doorcode" class="user-addres-custom" style="height:100%; text-align:left !important;" type="text" value="<?= $address_doorcode ?>" /></td></tr>
						
					</tbody>
				</table>
			</div>

			<span class="log"></span>
		</td>
		<td style="width:260px;">
			<div class="right-block" id="page-controls">
				<div class="controls-header">Основные действия:</div>
				<div class="controls-body">
					<div class="controls-body-row">Статус: <strong id="post_status-text"><?= (($user['user_status'] == 1)? "Активирован " : "Неактивирован"); ?></strong><div class="switch demo3"><input id="user-status" type="checkbox" <?= (($user['user_status'] == 1)? "checked='checked'" : ""); ?>><label><i></i></label></div> </div>
					<div class="controls-body-row">Дата регистрации: <strong id="user-registered"><?= ((!is_array($user) and $user=='new')? '' :$user['user_registered']); ?></strong></div>
					<div class="controls-body-row"><form><button id="save" name = "submitbtn" type="button" class="button-controls save">Обновить</button></form></div>
				</div>
			</div>
			
			<div class="right-block" id="links-controls">
				<div class="controls-header">Права доступа:</div>
				<div class="controls-body">
					<div class="controls-body-row">
					<table>
					<?php
					foreach($permissions as $i => $p){
						echo '<tr><td style="padding:5px 0px; ">'.$p['perm_comment'].': </td><td><input class="permissions" id="perm-'.$p['perm_name'].'" data-res="'.$p['perm_id'].'" type="checkbox" '.(($p['ac_val']==1)?'checked="checked"':'').'/></td></tr>';
					}
					?>
					</div>
				</div>
			</div>
		</td>
	</tr>
	</table>

</div>
</div>
