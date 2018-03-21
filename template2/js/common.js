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

	function slidersInit() {

		function slider_1_rows() {
			if($(window).width() > 992) {
				var rows = 2;
			} else if($(window).width() < 480) {
				var rows = 2;
			} else {
				var rows = 1;
			}

			return rows;
		}

		function slider_3_rows() {
			if($(window).width() < 480) {
				var rows = 2;
			} else {
				var rows = 1;
			}

			return rows;
		}

		function slider_4_rows() {
			if($(window).width() > 992) {
				var rows = 2;
			} else if($(window).width() < 480) {
				var rows = 2;
			} else {
				var rows = 1;
			}

			return rows;
		}

		$(".slider-style-1").slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			dots: false,
			centerMode: false,
			rows: slider_1_rows(),
			arrows: false,
			responsive: [
				{
					breakpoint: 992,
					settings: {
						arrows: false,
						slidesToShow: 2,
						slidesToScroll: 1
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

		$(".slider-style-2").slick({
			slidesToShow: 4,
			slidesToScroll: 2,
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
					breakpoint: 992,
					settings: {
						arrows: false,
						slidesToShow: 5,
						slidesToScroll: 2,
					}
				},
				{
					breakpoint: 480,
					settings: {
						arrows: false,
						slidesToShow: 2,
						slidesToScroll: 1
					}
				}
			]
		});

		$(".slider-style-3").slick({
			slidesToShow: 3,
			slidesToScroll: 1,
			rows: slider_3_rows(),
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
					breakpoint: 992,
					settings: {
						arrows: false,
						slidesToShow: 3,
						slidesToScroll: 1,
					}
				},
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

		$(".slider-style-4").slick({
			slidesToShow: 2,
			slidesToScroll: 1,
			rows: slider_4_rows(),
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
					breakpoint: 992,
					settings: {
						arrows: false,
						slidesToShow: 3,
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
	}slidersInit();

	$('.leftArrow').on('click', function() {
		var sliderId = $(this).data("sliderId");
		$('#' + sliderId).slick("slickPrev");
	});

	$('.rightArrow').on('click', function() {
		var sliderId = $(this).data("sliderId");
		$('#' + sliderId).slick("slickNext");
	});

	function sliderHeight() {
		var heightNewsBlock = $("#news-slider-block").height();
		var heightItemsBlock = $("#news-items-block").height();
		var heightItemsBlock1 = (heightItemsBlock / 2) - 10;

		if(document.documentElement.clientWidth > 767) {
			$(".main-slider .slick-slide").css({"height":heightNewsBlock});
		}
		if(document.documentElement.clientWidth > 991) {
			$(".main-index .info-block-verticale .info-item").css({"height":heightItemsBlock});
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
