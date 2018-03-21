$(function() {

	$(".footer-click-button").on("click", function() {
		$(".main-wrap").toggleClass("off");
		$(".main-wrap-in").slideToggle();
		var $target = $('html,body'); 
		$target.animate({scrollTop: $target.height()}, 1000);
		return false;
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
	
	$(".ads-li").on("click", function() {
		$(this).toggleClass("active");
		var block = $(this).find(".ads-ul-block-inner").slideToggle();
	});

	function firm_nav_slider() {
		if($(window).width() < 768) {
			$(".slider-style-5").slick({
				slidesToShow: 2,
				slidesToScroll: 1,
				rows: 1,
				dots: false,
				arrows: false,
				centerMode: false,
				infinite: true,
				speed: 500,
				fade: false,
				cssEase: 'linear',
				autoplay: false,
				autoplaySpeed: 2000,
			});
		}
	}firm_nav_slider();


	window.onresize = function() {
		firm_nav_slider();
	}

	$(window).on("scroll", function() {
		if ($(window).scrollTop() > 100) {
			$(".main-menu").addClass("header-scroll");
		} else {
			$(".main-menu").removeClass("header-scroll");
		}
	});

});
