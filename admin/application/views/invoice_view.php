<?php

	$main = $data['invoice'];
	$users = $data['users'];
	$bas_items = $data['bas_items'];
	$products = $data['products'];
	$allproducts = $data['allproducts'];
	
	$all_attributes=array();
?>
<script src="/js/jquery.ui.datepicker-ru.js"></script>
<script>
var previous;
var ondateSelect = function(selectedDate){
	var option = $(this).hasClass('from') ? "minDate" : "maxDate";
	var next = $(this).hasClass('from') ? ".to" : ".from";
	var $date_block = $( this ).parent().parent();
	$date_block.find(next).datepicker( "option", option, selectedDate );
	/*
	if($date_block.find('.from').val()!='' && $date_block.find('.to').val()!=''){
		var n = 0;
		var _from = new Date($date_block.find('.from').val());
		var newDate = _from;
		var _to = new Date($date_block.find('.to').val());
		var milliseconds  = Math.abs(_to - _from);
		var seconds = milliseconds / 1000;
		var minutes = seconds / 60;
		var hours = minutes / 60;
		var days = hours / 24;
		for(var i = 0;i<days; i++){
			newDate.setDate(_from.getDate() + 1);
			if(newDate.getDay()==3){ n++; }
		}
		$( this ).closest('tr').find('.releases').val(n);
	}
	*/
}
/*
var onReleasesChange = function(){
	var $row = $( this ).closest('tr');
	var n = $( this ).val();
	if($row.find('.from').val()!=''){
		var _from = new Date($row.find('.from').val());
		var newDate = _from;
		while(n >0 ){
			newDate.setDate(_from.getDate() + 1);
			if(newDate.getDay()==3){n=n-1;}
		}
		$row.find('.to').val(newDate.getFullYear()+'-'+(newDate.getMonth()+1)+'-'+newDate.getDate());
	}
}
*/
$(function(){
	$(document).on('click', '.del_option', function(event){
		var $button = $(this);
		var $raw = $button.closest('tr');
		$raw.hide().remove();
	});

	$('#save').click(function(event){
		var send = {};
		<?php echo apply_filters("admin-invoice-send-data",'send.inv_id = $("#main-inv-id").val();
		send.bas_id = $("#main-bas-id").val();
		send.bas_avtor = $("#main-bas-avtor option:selected").val();
		send.def_status = $("#main-def-status").val();
		send.inv_status = ($("#main-status").prop("checked"))?1:0;
		send.inv_summ = $("#main-summ").val();
		send.inv_desc = $("#main-inv-desc").val();
		send.bas_desc = $("#main-bas-desc").val();
		');
		?>
		send.bas_items = [];
		$('#products-list tbody tr').each(function(index){
			var $raw = $(this);
			var prod_id = $raw.find('.product-select option:selected').val();
			var amount = $raw.find('.product-amount').val();
			var cost = $raw.find('.product-cost').val();
			var discount = $raw.find('.product-discount').val();
			
			if(prod_id!="-1"){
				<?php echo apply_filters("admin-invoice-send-bas_items",'send.bas_items.push({ "prod_id" : prod_id, "amount" : amount, "cost" : cost, "discount" : discount })'); ?>
			}
		});
		
		if(send.def_status == "1"){
			alert("Данный счет уже был оплачен, изменения статуса, заказчика и товаров сохранены не будут. Будут сохранены только описание счета и описание заказа.");
		}
		if(send.bas_items.length == 0){
			alert('В корзине отсутсвуют товары, счет не может быть оплачен!');
			return false;
		}
		console.log(JSON.stringify(send));
		xhr = $.ajax({
			url : "/admin/ajax/",
			type : "post",
			data : { "ajax_action" : "update_invoice", "send" : send},
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				console.log(data);
				if (IsJsonString(data)) {
					data = JSON.parse(data);
					history.replaceState({foo: 'bar'}, '', '/admin/invoices/?id='+data['inv_id']);
					$('.inv-id').html(data['inv_id']);
					$('#main-inv-id').val(data['inv_id']);
					$('#main-bas-id').val(data['bas_id']);
					$('#main-avtor').val(data['invoice']['inv_avtor_name']);
					$('#main-createdate').text(data['invoice']['inv_date']);
					$('#main-lastupdate').text(data['invoice']['inv_lastupdate']);
					$('#main-paydate').text(data['invoice']['inv_paydate']);
					$('#post_status-text').text( (data['invoice']['inv_status'] == 1)? 'Оплачен': 'Неоплачен' );
				}
			},
			error : function( jqXHR, textStatus, errorThrown ){
							console.error(errorThrown);
			}
		});
		
		
	});
	$('#main-bas-avtor').zelect({});
	
	$( ".from, .to" ).datepicker({
		dateFormat: "yy-mm-dd",
		regional: 'ru',
		onClose: ondateSelect
	});
	
	<?php 
	if($main['inv_status'] == 1){
		echo '$( "input, select, option" ).prop( "disabled", true ); $(".zelect *").unbind()';
	} 
	?>
});
</script>
<script>
<?php do_action('admin_script'); ?>
</script>
<style>
	.from, .to {
		width:80px !important;
	}
</style>
<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div><h2>Счет № <span class="inv-id"><?php echo (!is_array($main) and $main=='new')? '_____' :$main['inv_id']; ?></span></h2>
<div id="wrap-main">
	<table class="page-table" width="100%">
	<tr>
		<td class="page-view" style="width:auto; padding-right:20px;">
		<input type="hidden" value="<?php echo (!is_array($main) and $main=='new')? 'new' :$main['inv_id']; ?>" id="main-inv-id" />
		<input type="hidden" value="<?php echo (!is_array($main) and $main=='new')? 'new' :$main['bas_id']; ?>" id="main-bas-id" />
			
			<div class="margintop30">
				<label><h3>Создатель счета</h3></label>
				<div class="border"><input id="main-avtor" disabled="disabled" type="text" value="<?php echo ((!is_array($main) and $main=='new')? '' : $main['inv_avtor_name']); ?>"></div>		
			</div>
			
			<div class="margintop30">
				<label><h3>Заказчик</h3></label>
				<div class="border">
				<select id="main-bas-avtor" style="width:100%;">
					<?php
						foreach ($users as $i => $item){
							$s = ($item['user_id']==$main['bas_uid'] or $item['user_id']==$_GET['client'])?'selected="selected"':'';
							echo '<option value="'.$item['user_id'].'" '.$s.'>'.$item['user_name'].'</option>';
						}
					?>
				</select>
				</div>
			</div>
			
			<div class="margintop30">
				<label><h3>Сумма</h3></label>
				<div class="border"><input id="main-summ" disabled="disabled" type="text" value="<?php echo ((!is_array($main) and $main=='new')? '' : $main['inv_summ']); ?>"></div>		
			</div>
			<?php
		
		$tovars = new AdminPage(
			array(
				"header" => "Корзина заказа",
				"table" => "mvc_basckets_items",
				"menu" => "/admin/tasks/",
				"attrs" => array("class"=>"main-table", "id"=>"products-list"),
				"fields" => apply_filters("admin-invoice-product-fields", array(
								array("title"=>"Название", "class"=>"product-select", "width"=>"auto", "type"=>"select", "values"=>$allproducts, 'name'=>'item_prod_id[]'),
								array("title"=>"Кол-во шт.", "class"=>"product-amount", "width"=>"50px", "type"=>"number", "name"=>"item_amount[]", ),
								array("title"=>"Ст-ть за 1 шт.", "class"=>"product-cost",  "width"=>"50px", "type"=>"number", "name"=>"item_cost[]", ),
								array("title"=>"Скидка %", "class"=>"product-discount",  "width"=>"50px", "type"=>"number", "name"=>"item_discount[]", ),
								array("title"=>"Дейсвия",  "width"=>"100px", "type"=>"simply text", "content"=>'<button name="del_option" type="button" class="button-controls delete del_option" '.(($main['inv_status'] == 1)? 'disabled="disabled"' : '').'>Удалить</button>'),
							)),
				"footer" => apply_filters("admin-invoice-product-footer", array(
								array("id"=>"product-select", "class"=>"product-select", "type"=>"select", "values"=>$products, 'name'=>'item_prod_id[]', "js"=>"
									function setCost(){
										$(this).closest('tr').find('.product-cost').val($(this).find('option:selected').data('cost'));
										$(this).closest('tr').find('.product-discount').val($(this).find('option:selected').data('discount'));
									}
									
									function changeSum(){
										var rows = $(this).closest('tbody').find('tr');
										var sum = 0
										for(var i=0; i<rows.length; i++){
											var row = $(rows[i]);
											var amount = row.find('.product-amount').val();
											var cost = row.find('.product-cost').val();
											var discount = row.find('.product-discount').val();
											var releases = row.find('.product-releases').val();
											sum += releases * amount * ( cost - ( (cost * discount)/100)  );
											$('#main-summ').val(sum);
										}
									}
									
									$('.product-select').change(setCost);
									$('tbody .product-amount, tbody .product-cost, tbody .product-discount').change(changeSum).keyup(changeSum);
									
									$('#product-select').change(function(){
										$(this).find('option:selected').attr('selected', 'selected');
										var row = $(this).closest('tr'); 
										var tbody = $(this).closest('table').find('tbody');
										var clone = row.clone();
										
										clone.find('#product-controls').append('<button name=\"del_option\" type=\"button\" class=\"button-controls delete del_option\" >Удалить</button>')
										clone.find('select, input, div').removeAttr('id');
										clone.appendTo(tbody);

										$(this).find('option:selected').removeAttr('selected').val('');
										$('#product-releases').val('0');
										$('#product-amount').val('1');
										$('#product-cost').val('0');
										$('#product-discount').val('0');
										
										clone.find('.product-select').change(setCost);
										clone.find('.product-releases, .product-amount, .product-cost, .product-discount').change(changeSum).keyup(changeSum); clone.find('.product-cost').change();
										
										$('.from, .to').removeClass('hasDatepicker').datepicker('destroy');
										$('.from, .to').datepicker({dateFormat: 'yy-mm-dd',regional: 'ru',onClose: ondateSelect});
									});
								"),
								array("id"=>"product-amount", "class"=>"product-amount", "type"=>"number", "value"=>"1", "name"=>"item_amount[]"),
								array("id"=>"product-cost", "class"=>"product-cost", "type"=>"number", "value"=>"0", "name"=>"item_cost[]", ),
								array("id"=>"product-discount", "class"=>"product-discount", "type"=>"number", "value"=>"0", "name"=>"item_discount[]", ),
								array("id"=>"product-controls", "type"=>"simply text", "content"=>''),
							)),
			)
		);

		$tovars->setItems($bas_items);
		$tovars->renderAdminList();
			
			?>
			
			<div class="margintop30">
				<table></table>
			</div>
			
			<div class="margintop30">
				<label><h3>Описание счета:</h3></label>
				<div class="border"><textarea id="main-inv-desc" style="width:100%;height:100px;"><?php echo ((!is_array($main) and $main=='new')? '' : $main['inv_desc']); ?></textarea></div>	
			</div>
			
			<div class="margintop30">
				<label><h3>Текст, который будет добавлен в письмо:</h3></label>
				<div class="border"><textarea id="main-bas-desc" style="width:100%;height:100px;"><?php echo ((!is_array($main) and $main=='new')? '' : $main['bas_desc']); ?></textarea></div>	
			</div>
			
			<span class="log"></span>
		</td>
		<td style="width:260px;">
			<div class="right-block" id="page-controls">
				<div class="controls-header">Основные действия:</div>
				<div class="controls-body">
					<div class="controls-body-row">Статус: <strong id="post_status-text"><?= (($main['inv_status'] == 1)? "Оплачен " : "Неоплачен"); ?></strong><div class="switch demo3"><input type="hidden" id="main-def-status" value="<?= (($main['inv_status'] == 1)? "1" : "0"); ?>" /><input id="main-status" type="checkbox" <?= (($main['inv_status'] == 1)? "checked='checked'" : ""); ?>><label><i></i></label></div> </div>
					<div class="controls-body-row">Дата создания: <strong id="main-createdate"><?= ((!is_array($main) and $main=='new')? '' :$main['inv_date']); ?></strong></div>
					<div class="controls-body-row">Дата изменения.: <strong id="main-lastupdate"><?= ((!is_array($main) and $main=='new')? '' :$main['inv_lastupdate']); ?></strong></div>
					<div class="controls-body-row">Дата оплаты.: <strong id="main-paydate"><?= ((!is_array($main) and $main=='new')? '' :$main['inv_paydate']); ?></strong></div>
					<div class="controls-body-row"><form><button id="save" name = "submitbtn" type="button" class="button-controls save">Обновить</button> <a href="/admin/invoices/?id=<?= $main['inv_id']?>&print=1" target="_blanck">Печать</a> </form></div>
				</div>
			</div>
			
			
		</td>
	</tr>
	</table>

</div>
</div>
