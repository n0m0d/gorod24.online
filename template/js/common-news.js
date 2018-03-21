$(function() {

	$(".footer-click-button").on("click", function() {
		$(".main-wrap").toggleClass("off");
		$(".main-wrap-in").slideToggle();
		var $target = $('html,body'); 
		$target.animate({scrollTop: $target.height()}, 1000);
		return false;
	});

	$(".fotorama__stage__shaft").on("click", function() {
		var fotorama = $('.fotorama').fotorama({allowfullscreen: true}).data('fotorama');
		fotorama.requestFullScreen();
	});

	$("#city-filter").chosen({
		disable_search: false
	});

	$(".toggle-menu").on("click", function() {
		$(this).toggleClass("on");
		$(".mobile-block-menu").slideToggle();
		return false;
	});

	$(".has-drop-mobile").on("click", function() {
		$(this).find('ul').slideToggle();
		$(this).toggleClass("open");
		return false;
	});

	$(".fa-search").on("click", function() {
		$(".search-input").slideToggle();
		return false;
	});

	$(".main-slider").slick({
		dots: false,
		arrows: false,
		centerMode: false,
		infinite: true,
		speed: 500,
		fade: true,
		cssEase: 'linear',
		autoplay: true,
		autoplaySpeed: 2000,
	});

	function sliderHeight() {
		var heightNewsBlock = $("#news-slider-block").height();
		var heightItemsBlock = $("#news-items-block").height();
		var heightItemsBlock1 = (heightItemsBlock / 2) - 10;

		if($(window).width() > 767) {
			$(".main-slider .slick-slide").css({"height":heightNewsBlock});
		} else {
			$(".main-slider .slick-slide").css({"height":"auto"});
		}

		if($(window).width() > 991) {
			$(".main-news .info-block-verticale .info-item").css({"height":heightItemsBlock1});
			$(".main-news .section-1 .left-slider-col .main-slider .slick-slide img").css({"height":heightItemsBlock1});
		}
	}sliderHeight();

	window.onresize = function() {
		sliderHeight();
	}

	$(window).on("scroll", function() {
		if ($(window).scrollTop() > 100) {
			$(".main-menu").addClass("header-scroll");
		} else {
			$(".main-menu").removeClass("header-scroll");
		}
	});
});
