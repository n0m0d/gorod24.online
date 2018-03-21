$(function() {

	$(".toggle-menu").on("click", function() {
		$(this).toggleClass("on");
		$(".toggle-mobile-menu").slideToggle();
		return false;
	});
	
	$('.main-header ul.nav').find('a').click(function(e){
		e.preventDefault();
		var $href = $(this).attr('href');
		var $anchor = $($href).offset();
		
		$('html, body').stop().animate({scrollTop:$anchor.top-75}, 500, 'swing', function() { 
		  /* alert("Finished animating");*/
		});
		return false;
	});
	
	$('.phone').mask('+0(000) 000 00 00');
	
	$('.ajax').on('submit', function(e){
		e.preventDefault();
		var $form = $(this);
		var phone = $form.find('.phone').val();
		if(phone==''){
			swal("Ошибка!", "Вы не заполнили поле \"Ваш номер телефона\"", "error");
		}
		else {
			$.ajax({
				type : 'POST',
				data:$form.serialize(),
				url : $form.attr('action'),
				success : function (result) {
					console.log(result);
					switch(result){
						case '0': swal("Ошибка!", "Вы не заполнили поле \"Ваш номер телефона\"", "error");  break;
						case '1': swal("Отлично!", 'Мы отправили ссылку для скачивания приложения на указанный Вами номер телефона.', "success");  break;
						case '2': swal("Перебор!", 'Мы уже отправляли Вам ссылку. Подождите немного или попробуйте позже.', "warning");  break;
						case '3': swal("Ошибка!", 'Не правильный формат номера телефона', "error");  break;
					}
					
					
				}
			});
		}
	});
	
	
});
