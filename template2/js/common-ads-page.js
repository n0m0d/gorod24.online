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
	
	$(".ads-li").on("click", function(e) {
		e.preventDefault();
		$(this).toggleClass("active");
		var block = $(this).find(".ads-ul-block-inner").slideToggle();
		return false;
	});

	function ads_boxes_reverse() {
		if($(window).width() < 992) {
			$(".ads-content-center-box").insertAfter($(".ads-aside-right-box"));
		} else {
			$(".ads-content-center-box").insertBefore($(".ads-aside-right-box"));
		}
	}ads_boxes_reverse();

	function ads_yellow_slider() {
		if($(window).width() < 768) {
			$(".slider-style-6").slick({
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
				responsive: [
					{
						breakpoint: 768,
						settings: {
							arrows: false,
							slidesToShow: 2,
							slidesToScroll: 1,
						}
					},
					{
						breakpoint: 480,
						settings: {
							arrows: false,
							slidesToShow: 1,
							slidesToScroll: 1
						}
					}
				]
			});
		}
	}ads_yellow_slider();

	window.onresize = function() {
		ads_boxes_reverse();
		ads_yellow_slider();
	}

	$(window).on("scroll", function() {
		if ($(window).scrollTop() > 100) {
			$(".main-menu").addClass("header-scroll");
		} else {
			$(".main-menu").removeClass("header-scroll");
		}
	});

});
