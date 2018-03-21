var call_func; //Коллбеки
var call_func; //Коллбеки
var city_name; //Название выбранного города
var city_id; //ID выбранного города

$(function() {

    if ($('*').is(".audio-wrap")) {
        $('.mini-radio').audioPlayer({
            classPrefix: 'mini-player',
            strPlay: 'Пуск',
            strPause: 'Пауза',
            strVolume: 'Звук',
        });
    }

    if ($('*').is(".ajax-anchor-in")) {
        $(".main-menu-line").css({"display":"block"});
    }

    //Высота блоков в новостях
    function sliderHeight() {
        var heightNewsBlock = $("#news-slider-block").height();
        var heightItemsBlock = $("#news-items-block").height();
        var heightItemsBlock1 = (heightItemsBlock / 2) - 10;
        var block = ($(".info-block-horizontale").height() * 2) + 20;
        var block1 = $(".info-block-verticale").height();

        if ($(window).width() > 991) {
            $(".main-index .info-block-verticale .info-item").css({"height": heightItemsBlock});
            $(".main-slider .slick-slide").css({"height": heightNewsBlock});
            $(".main-news .info-block-verticale .info-item, .main-news .info-block-big .info-item").css({"height": block});
            //$(".main-news .section-1 .left-slider-col .main-slider .slick-slide").css({"height": heightItemsBlock1});
        } else if ($(window).width() > 767) {
            $(".main-slider .slick-slide").css({"height": heightNewsBlock});
            //$(".main-news .section-1 .left-slider-col .main-slider .slick-slide").css({"height": heightItemsBlock});
            $(".main-news .info-block-verticale .info-item").css({"height": "auto"});
            $(".main-news .info-block-big .info-item").css({"height": block1});
        } else {
            $(".main-news .info-block-big .info-item").css({"height": block1});
            $(".main-slider .slick-slide").css({"height": "450px"});
            //$(".main-news .section-1 .left-slider-col .main-slider .slick-slide").css({"height": "auto"});
        }
    }

    $(".pay-item").equalHeights();

    sliderHeight();

    //Фикс masonry сетки
    $(window).on("load", function() {
        sliderHeight();
        if($('*').is('.grid')) {
            $(".grid").imagesLoaded(function () {
                $(".grid").masonry({
                    itemSelector: '.grid-item',
                    columnWidth: '.grid-sizer',
                    percentPosition: true
                });
            });
        }

        if ($(".toggle-bg input:checked").val() == "off")
        {
            $(".toggle-bg").css({"background-color" : "#dedede"});
        } else {
            $(".toggle-bg").css({"background-color" : "#93CF2C"});
        }
    });

    function formValidate() {
        $.validator.setDefaults({ ignore: ":hidden:not(select)" });

        if ($(".select-filter").length > 0) {
            $(".select-filter").each(function() {
                if ($(this).attr("required") !== undefined) {
                    $(this).on("change", function() {
                        $(this).valid();

                        var show_optid = $(this).attr("data-id");
                        var show_optval = $(this).find("option:selected").val();

                        $(".dop-filters-container > .input-wrap").each(function () {

                            if ($(this).data("showOptid") == show_optid && $(this).data("showOptval") == show_optval)
                            {
                                $(this).css({"display" : "block"});
                            }

                        });

                    });
                }
            });
        }

        $(".adsaddform").validate({
            errorPlacement: function (error, element) {
                if (element.is(".select-filter")) {
                    //console.log("placement for chosen");
                    element.next(".chosen-container").append(error);
                    element.next(".chosen-container .chosen-single").addClass("error");
                } else {
                    error.insertAfter(element);
                }
            }
        });
    }

    /******************************
     *** ПРОВЕРКА НА АВТОРИЗАЦИЮ ***
     ******************************/

	//Проверяем наличение логина
    function user_auth(cb) {
        if (cb) {
            if (user_login == 'false') {
                user_auth_form(cb);
            } else {
                user_auth_call();
                cb();
            }
        }
    };

	//Коллбек логина
    function user_auth_call() {
        user_login = 'true';
    };

	//Авторизация через соц. сеть, вспомогательная функция
    function soc_auth(url) {
        var popup = function(options) {
            var
                screenX = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft,
                screenY = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop,
                outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth,
                outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22),
                width = options.width,
                height = options.height,
                left = parseInt(screenX + ((outerWidth - width) / 2), 10),
                top = parseInt(screenY + ((outerHeight - height) / 2.5), 10),
                features = (
                    'width=' + width +
                    ',height=' + height +
                    ',left=' + left +
                    ',top=' + top
                );
            this.active = window.open(options.url, 'soc_openapi', features);
        };

        var p = new popup( {
            width: 620,
            height: 470,
            url: url
        });

        var popupCheck = function() {
            try {
                if (p.active.closed) {

                    //Смотрим, существует ли сессия
                    $.ajax({
                        type: "POST",
                        url: "../login/soc/",
                        success: function(data) {

                            //Вызываем коллбек логина
                            if(data == "loginTrue") {
                                user_auth_call();

                                $.magnificPopup.close({
                                    items: {
                                        src: '#login-form'
                                    }
                                });

                                call_func();

                            } else {
                                swal("Ошибка!", 'Вы не авторизованы в соц. сети!', "error");
                            }
                        },
                        error: function(xhr, str){
                            alert("Возникла ошибка: " + xhr.responseCode);
                        }
                    })

                    return true;
                }
            } catch(e) {
                return true;
            }
            setTimeout(popupCheck, 100);
        };

        setTimeout(popupCheck, 1000);
    }

    function user_auth_form(call_func) {

        $.magnificPopup.open({
            items: {
                src: '#login-form'
            },
            type: 'inline',
            preloader: false,
            modal: true,
            callbacks: {
                open: function () {
                    //Форма авторизации
                    $("#login-form-auth").on("submit", function (e) {
                        e.preventDefault();

                        var $form = $("#login-form-auth");
                        var url = $form.attr("action") + "?" + $form.serialize();

                        $.ajax({
                            type: "POST",
                            url: url,
                            data: $form.serialize(),
                            success: function(data) {

                                //Вызываем коллбек логина
                                if(data == "true") {
                                    user_auth_call();

                                    $.magnificPopup.close({
                                        items: {
                                            src: '#login-form'
                                        }
                                    });

                                    call_func();

                                } else {
                                    swal("Ошибка!", 'Неправильный логин или пароль!', "error");
                                }
                            },
                            error: function(xhr, str){
                                alert("Возникла ошибка: " + xhr.responseCode);
                            }
                        })
                    });

                    $(".vk-auth").on("click", function (e) {
                        e.preventDefault();
                        var url = $(".vk-auth").attr("href");
                        soc_auth(url);
                    });

                    $(".fb-auth").on("click", function (e) {
                        e.preventDefault();
                        var url = $(".fb-auth").attr("href");
                        soc_auth(url);
                    });

                    $(".od-auth").on("click", function (e) {
                        e.preventDefault();
                        var url = $(".od-auth").attr("href");
                        soc_auth(url);
                    });
                },
                close: function() {
                    $("#login-form-auth").off("submit");
                    $(".vk-auth").off("click");
                    $(".fb-auth").off("click");
                    $(".od-auth").off("click");
                }
            }
        });
    }

	$(window).on("scroll", function() {
		if ($(window).scrollTop() > 200) {
			$(".main-menu").addClass("header-scroll");
			$(".main-menu-line").addClass("header-scroll-line");
		} else {
			$(".main-menu").removeClass("header-scroll");
			$(".main-menu-line").removeClass("header-scroll-line");
		}
	});

	$(".footer-click-button").on("click", function() {
		$(".main-wrap").toggleClass("off");
		$(".main-wrap-in").slideToggle();
		var $target = $('html,body'); 
		$target.animate({scrollTop: $target.height()}, 1000);
		return false;
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

    $(document).on("change", "input[name=toggle]", function () {
        if ($(".toggle-bg input:checked").val() == "off")
        {
            $(".toggle-bg").css({"background-color" : "#dedede"});
        } else {
            $(".toggle-bg").css({"background-color" : "#93CF2C"});
        }
    });

    $(document).on("click", ".open-pa-button", function (e) {
        e.preventDefault();
        $(".menu-option-list").css({"display" : "block"});
    });

    $(document).on("click", ".open-pa-button", function (e) {
        e.preventDefault();
        $(".menu-option-list-mobile").slideToggle();
    });

    $(document).on("click", ".menu-option-list-mobile li .in-menu", function (e) {
        e.preventDefault();
        var menu_class = $(this).attr("href");
        var status = $("."+menu_class+"-span").data("status");

        $("."+menu_class+"").slideToggle();

        if (status == "off") {
            $("."+menu_class+"-span").data("status", "on");
            $("."+menu_class+"-span").removeClass("icon-arrow-up");
            $("."+menu_class+"-span").addClass("icon-arrow-down");
        } else {
            $("."+menu_class+"-span").data("status", "off");
            $("."+menu_class+"-span").removeClass("icon-arrow-down");
            $("."+menu_class+"-span").addClass("icon-arrow-up");
        }
    });

    /*$(document).on("click", ".date-wrap", function () {

        var sort_type = $(this).data("type");

        if (sort_type == 'price') {
            $(this).data("type", "price-off");
            $(this).find("span").removeClass("icon-arrow-down");
            $(this).find("span").addClass("icon-arrow-up");
        }
        if (sort_type == 'price-off') {
            $(this).data("type", "price");
            $(this).find("span").removeClass("icon-arrow-up");
            $(this).find("span").addClass("icon-arrow-down");
        }
        if (sort_type == 'date') {
            $(this).data("type", "date-off");
            $(this).find("span").removeClass("icon-arrow-down");
            $(this).find("span").addClass("icon-arrow-up");
        }
        if (sort_type == 'date-off') {
            $(this).data("type", "date");
            $(this).find("span").removeClass("icon-arrow-up");
            $(this).find("span").addClass("icon-arrow-down");
        }

        $.ajax({
            type: "POST",
            url: window.location.href,
            data: {
                "side" : "server",
                "sort_type" : sort_type
            },
            success: function(result) {
                NProgress.done();
                try {
                    var $main_content = $(result).find(".ads-content-center-box").html() || $(result).filter(".ads-content-center-box").html();
                    $('.ads-content-center-box').html($main_content);
                    var title = $(result).filter('title').text();
                    $(".text-clock").timeago();
                } catch(e) { console.error(e); }
                history.pushState("state", title, window.location.href);
            },
            error: function(xhr, str){
                alert("Возникла ошибка: " + xhr.responseCode);
            }
        });
    });*/

	//Кнопка показать телефон
    $(document).on("click", "#show_phone", function (e) {
        e.preventDefault();
        var ads_id = $(this).data("adsId");

        if ($(this).html() === 'Показать телефон') {

            if (parseInt($.session.get("count")) >= 5) {
                call_func =
                    function () {
                        $.ajax({
                            url: "../ajax/get.phone/",
                            dataType: "json",
                            type: "POST",
                            data: {
                                'id' : ads_id,
                            },
                            success: function(data){
                                if(data.result == "true") {
                                    $("#show_phone").html(data.html);

                                    //Записываем в сессию кол-во нажатий для не авторизованного пользователя
                                    if (!$.session.get("count"))
                                    {
                                        $.session.set("count", 1);
                                    } else {
                                        var inter_1 = parseInt($.session.get("count")) + parseInt(1);
                                        $.session.set("count", inter_1);
                                    }
                                } else {
                                    swal("Внимание!", 'Ошибочка', "warning");
                                }
                            },
                            error: function (jqXHR){
                                swal("Ошибка!", 'Возникла ошибка: ' + jqXHR, "error");
                            }
                        });
                    }

                user_auth(call_func);
            } else {
                $.ajax({
                    url: "../ajax/get.phone/",
                    dataType: "json",
                    type: "POST",
                    data: {
                        'id' : ads_id,
                    },
                    success: function(data){
                        if(data.result == "true") {
                            $("#show_phone").html(data.html);

                            //Записываем в сессию кол-во нажатий для не авторизованного пользователя
                            if (!$.session.get("count"))
                            {
                                $.session.set("count", 1);
                            } else {
                                var inter_1 = parseInt($.session.get("count")) + parseInt(1);
                                $.session.set("count", inter_1);
                            }
                        } else {
                            swal("Внимание!", 'Ошибочка', "warning");
                        }
                    },
                    error: function (jqXHR){
                        swal("Ошибка!", 'Возникла ошибка: ' + jqXHR, "error");
                    }
                });
            }
        }
    });

    //Отправка ответа ОПРОСА
    function ajax_send_inter(option_id, option_inter_id) {
        $.ajax({
            url: "../interviews/send.inter",
            dataType: "json",
            type: "POST",
            data: {
                "sending" : {
                    'option_id' : option_id,
                    'option_inter_id' : option_inter_id
                }
            },
            success: function(data){
                if(data.result == "true") {
                    $(".interviews-wrap").html(data.html);
                    swal("Отлично!", "Вы отправили ответ", "success");
                } else {
                    swal("Внимание!", 'Ошибочка', "warning");
                }
            },
            error: function (jqXHR){
                swal("Ошибка!", 'Возникла ошибка: ' + jqXHR, "error");
            }
        });
    }
    //Удаление ответа ОПРОСА
    function ajax_del_inter(option_inter_id) {
        $.ajax({
            url: "../interviews/del.inter",
            dataType: "json",
            type: "POST",
            data: {
                "sending" : {
                    'option_inter_id' : option_inter_id
                }
            },
            success: function(data){
                if(data.result == "true") {
                    $(".interviews-wrap").html(data.html);
                    swal("Отлично!", "Теперь вы можете переголосовать", "success");
                } else {
                    swal("Внимание!", 'Ошибочка', "warning");
                }
            },
            error: function (jqXHR){
                swal("Ошибка!", 'Возникла ошибка: ' + jqXHR, "error");
            }
        });
    }

    $(document).on("click", ".menu-option-list li", function (e) {
    	e.preventDefault();
    	$(".menu-option-list li").removeClass("active");
    	$(this).addClass("active");
    });

    $(document).on("click", ".show-filter-open, .show-filter-exit", function (e) {
    	e.preventDefault();
    	$(".show-filter-exit").toggleClass("active");
    	$(".filter-wrap .middle-sect").slideToggle();
    });

    $(document).on("click", ".open-cats-button", function (e) {
    	e.preventDefault();
    	$(".main-ads-page .ads-cat").slideToggle();
    });

    $(document).on("click", ".open-filters-button", function (e) {
        e.preventDefault();
        $(".main-ads-page .realty-filter").slideToggle();
    });

    var track = 0;
    var status = 0;

    //Аудио новости
    $(document).on("click", ".item-audio", function (e) {
        e.preventDefault();
        track = $(this).attr("data-track-id");
        status = $(this).attr("data-track-status");

        if (status == "off") {
            $("audio").each(function () {
               $(this)[0].pause();
               $(this).removeAttr("controls");
            });
            $(".item-audio").attr("data-track-status", "off");
            $("#track-"+track)[0].currentTime = 0
            $("#track-"+track).attr("controls", "");
            $("#track-"+track)[0].play();
            $(".item-audio").removeClass("audio-on");
            $(".item-audio").addClass("audio-off");
            $(this).removeClass("audio-off");
            $(this).addClass("audio-on");
            $(this).attr("data-track-status", "on");
        } else {
            $("#track-"+track).removeAttr("controls");
            $(this).removeClass("audio-on");
            $(this).addClass("audio-off");
            $("#track-"+track)[0].pause();
            $(this).attr("data-track-status", "off");
        }
    });

    //18+ новости
    $(document).on("click", ".info-block-horizontale .info-item .info-descr, .info-block-verticale .info-item .info-descr", function (e) {
        e.preventDefault();

        var check18 = $(this).attr("data-18plus");
        var link = $(this).attr("href");

        if (check18 == 1) {
            swal({
                title: '<u>Вам есть 18 лет?</u>',
                type: 'error',
                html: $(this).text(),
                showCloseButton: true,
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: 'Да',
                confirmButtonAriaLabel: 'Да',
                cancelButtonText: 'Нет',
                cancelButtonAriaLabel: 'Нет',
            });

            $('button.confirm').on('click', function() {
                window.location.href = link;
                console.log(link);
            });
        } else {
            window.location.href = link;
        }
    });

    //Страница оплаты выбор VIP пакетов
    /*var count = 0;
    var text = 0;
    var price = 0;
    var cost = 0;
    updateCount();

    $(document).on("change", ".gazeta-check", function () {
        updateCount(this.checked ? 1 : -1);
    });

    function updateCount(a) {
        count = a ? count + a : $(".gazeta-check:checked").length;

        if (count == 0) {
            $(".item-select-1 option[value='2'], .item-select-1 option[value='1']").removeAttr("selected");
            $(".item-select-4, .item-button-1, .item-button-4").attr("disabled", "");

            text = $(".item-select-1 option[value='1']").text();
            price = $(".item-select-1 option[value='1']").attr("data-price");
            cost = $(".item-select-1 option[value='1']").attr("data-cost");

            $("#pay-item-1").find(".pay-old-price .old-price").attr("hidden", "");
            $("#pay-item-1").find(".pay-price .price").text(price);
            $("#pay-item-1").find(".pay-head .head-stick .period").text(text);

        } else if (count == 1) {
            $(".item-select-4, .item-button-1, .item-button-4").removeAttr("disabled");
            $(".item-select-1 option[value='2']").removeAttr("selected");
            $(".item-select-1 option[value='1']").attr("selected", "");

            text = $(".item-select-1 option[value='1']").text();
            price = $(".item-select-1 option[value='1']").attr("data-price");
            cost = $(".item-select-1 option[value='1']").attr("data-cost");

            $("#pay-item-1").find(".pay-old-price .old-price").attr("hidden", "");
            $("#pay-item-1").find(".pay-price .price").text(price);
            $("#pay-item-1").find(".pay-head .head-stick .period").text(text);

        } else {
            $(".item-select-4, .item-button-1, .item-button-4").removeAttr("disabled");
            $(".item-select-1 option[value='1']").removeAttr("selected");
            $(".item-select-1 option[value='2']").attr("selected", "");

            text = $(".item-select-1 option[value='2']").text();
            price = $(".item-select-1 option[value='2']").attr("data-price");
            cost = $(".item-select-1 option[value='2']").attr("data-cost");

            $("#pay-item-1").find(".pay-old-price .old-price").removeAttr("hidden");
            $("#pay-item-1").find(".pay-price .price").text(price);
            $("#pay-item-1").find(".pay-old-price .old-price").text(cost);
            $("#pay-item-1").find(".pay-head .head-stick .period").text(text);
        }
    }*/

    $(document).on("click", ".pay-item .pay-button button", function () {
        var item_id = $(this).attr("data-id");
        $(".pay-item .pay-button button").text("Выбрать");
        $(".pay-item").removeClass("active");
        $("#"+item_id).addClass("active");
        $(this).text("Отменить");
    });

    $(document).on("click", ".active .pay-button button", function () {
        $(".pay-item").removeClass("active");
        $(".pay-item .pay-button button").text("Выбрать");
    });

    $(document).on("change", ".select-wrap select", function () {
        var id = $(this).attr("data-type");
        var text = $(this).find("option:selected").text();
        var price = $(this).find("option:selected").attr("data-price");
        var cost = $(this).find("option:selected").attr("data-cost");
        var cost_int = cost.split(',');
        var price_int = price.split(',');

        $("#pay-item-"+id).find(".pay-head .head-stick .period").text(text);

        if (cost_int[0] != price_int[0]) {
            $("#pay-item-"+id).find(".pay-old-price .old-price").removeAttr("hidden");
            $("#pay-item-"+id).find(".pay-price .price").text(price);
            $("#pay-item-"+id).find(".pay-old-price .old-price").text(cost);
        } else {
            $("#pay-item-"+id).find(".pay-old-price .old-price").attr("hidden", "");
            $("#pay-item-"+id).find(".pay-price .price").text(price);
        }
    });

    $(document).on("click", ".payment-item", function () {
        $(".payment-item").removeClass("selected");
        $(this).addClass("selected");
    });

    //Подготовка отправки checkbox опросов
    $(document).on("click", "#send_inters", function (e) {
        e.preventDefault();

        call_func =
            function () {

                var option_inter_id = $("#send_inters").data("interId");
                var max = $("#send_inters").data("maxVotes");
                var options=[];

                if ($('.inter_' + option_inter_id + ':checked').length > max){
                    swal("Внимание!", "Максимальное число вариантов ответа: "+ max + "\nПожалуйста уберите лишние варианты!", "warning");
                    return false;
                }

                if ($('.inter_' + option_inter_id + ':checked').length == 0){
                    swal("Внимание!", "Вы не выбрали ни одного варианта!", "warning");
                    return false;
                }

                $('.inter_' + option_inter_id + ':checked').each(function(){
                    options.push($(this).val());
                })

                ajax_send_inter(options, option_inter_id);
            };

        user_auth(call_func);
    });

    //Подготовка отправки radio опросов
    $(document).on("click", "#send_inter", function (e) {
        e.preventDefault();

        call_func =
            function () {
                var option_inter_id = $("#send_inter").data("interId");
                var option_id = $('input[name=inter-option]:checked').val();

                if (typeof(option_id) == 'undefined')
                {
                    swal("Внимание!", "Вы не выбрали ни одного варианта!", "warning");
                    return false;
                }

                ajax_send_inter(option_id, option_inter_id);
            };

        user_auth(call_func);
    });

    //Подготовка удаления ответа
    $(document).on("click", "#delete_inters", function (e) {
       e.preventDefault();

        call_func =
            function () {
                var option_inter_id = $("#delete_inters").data("interId");
                ajax_del_inter(option_inter_id);
            };

        user_auth(call_func);
    });

	//Показать ещё комментарии
    $(document).on("click", "#show_more", function() {
        var btn_more = $(this);
        var count_show = parseInt($(this).attr('count_show'));
        var count_add  = $(this).attr('count_add');
        var com_main_id = $(".com_main_id").val();
        var com_for_table = $(".com_for_table").val();
        var com_for_column = $(".com_for_column").val();
        var page = $(".page").val();
        btn_more.val('Подождите...');

        if (page == 'all') {
            var url = "../comments/view/more/";
        } else {
            var url = "../comments/view/more/opinions/";
        }

        $.ajax({
            url: url,
            dataType: "json",
            type: "POST",
            data: {
                "count_show": count_show,
                "count_add": count_add,
                "com_main_id": com_main_id,
                "com_for_table": com_for_table,
                "com_for_column": com_for_column,
            },
            success: function(data) {
                if(data.result == "true") {
                    $('.comments').append(data.html);
                    $(".time").timeago();
                    btn_more.val('Показать еще');
                    btn_more.attr('count_show', (count_show + 20));
                } else {
                    btn_more.val('Больше нечего показывать');
                }
            },
            error: function (xhr, str) {
                swal("Ошибка!", 'Возникла ошибка: ' + xhr.responseCode, "error");
            }
        });
    });

	//Лайки комментариев
	$(document).on("click", ".icon-like, .icon-dislike", function (e) {
        e.preventDefault();

        var com_for_table = $(this).data("table");
        var com_for_column = $(this).data("column");
        var com_main_id = $(this).data("mainId");
        var com_parent_id = $(this).data("commentId");

        if ($(this).attr('class') == 'icon-like') {
            var type = 'like';
        } else {
            var type = 'dislike';
        }

        if ($(this).data('page') == 'opinions') {
            var page = 'opinions';
        } else {
            var page = 'default';
        }

        var strGET = window.location.search.replace( '?', '');

        call_func =
            function () {

                $.ajax({
                    type: "POST",
                    url: '../comments/like/?'+strGET,
                    dataType: "json",
                    data: {
                        "com_for_table" : com_for_table,
                        "com_for_column" : com_for_column,
                        "com_main_id" : com_main_id,
                        "com_parent_id" : com_parent_id,
                        "type" : type,
                        "page" : page,
					},
                    success: function (data) {
                        if (data.result == "true") {
                            $(".comments-init").html(data.html);
                            $(".time").timeago();
                        } else {
                            swal("Внимание!", 'Вы уже поставили оценку комментарию', "warning");
                        }
                    },
                    error: function (xhr, str) {
                        swal("Ошибка!", 'Возникла ошибка: ' + xhr.responseCode, "error");
                    }
                });
            };

        user_auth(call_func);
    });

	//Отправка комментария ответа
    $(document).on("click", ".review a", function (e) {
        e.preventDefault();

        var com_for_table = $(this).data("table");
        var com_for_column = $(this).data("column");
        var com_main_id = $(this).data("mainId");
        var com_parent_id = $(this).data("commentId");

        if ($(this).data('page') == 'opinions') {
            var page = 'opinions';
        } else {
            var page = 'default';
        }

        call_func =
            function () {
                swal({
                    title: "Ваш ответ:",
                    //text: "Вы можете оставить комментарий на этой странице",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Ваш ответ...",
                    confirmButtonText: 'Отправить',
                    confirmButtonAriaLabel: 'Отправить',
                    cancelButtonText: 'Закрыть',
                    cancelButtonAriaLabel: 'Закрыть',
                }, function (inputValue) {
                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("Вы должны заполнить это поле!");
                        return false
                    }

                    $(".confirm .btn").prop("disabled", true);

                    var strGET = window.location.search.replace( '?', '');

                    $.ajax({
                        type: "POST",
                        url: '../comments/?'+strGET,
                        dataType: "json",
                        data: {
                            "com_for_table" : com_for_table,
                            "com_for_column" : com_for_column,
                            "com_main_id" : com_main_id,
                            "com_parent_id" : com_parent_id,
                            "type" : 2,
                            "page" : page,
                            "comment" : inputValue,
                        },
                        success: function (data) {
                            if (data.result == "true") {
                                $(".comments-init").html(data.html);
                                $(".time").timeago();
                                swal("Отлично!", "Вы отправили ответ: " + inputValue, "success");
                            } else {
                                swal("Внимание!", data.error, "warning");
                            }
                            $(".confirm .btn").prop("disabled", false);
                        },
                        error: function (xhr, str) {
                            swal("Ошибка!", 'Возникла ошибка: ' + xhr.responseCode, "error");
                        }
                    });
                });
            };

        user_auth(call_func);
    });

	//Отправка комментария
    $(document).on("submit", "#form-comments", function(e) {
        e.preventDefault();
        $(this).prop("disabled", true);

        call_func =
            function () {
                $.ajax({
                    type: "POST",
                    url: '../comments/',
                    dataType: "json",
                    data: $("#form-comments").serialize(),
                    success: function (data) {
                        if (data.result == "true") {
                            $(".comments-init").html(data.html);
                            $(".time").timeago();
                        } else {
                            swal("Внимание!", data.error, "warning");
                        }
                        $(this).prop("disabled", false);
                    },
                    error: function (xhr, str) {
                        swal("Ошибка!", 'Возникла ошибка: ' + xhr.responseCode, "error");
                    }
                });
            };

        user_auth(call_func);
    });

	$(document).on("submit", ".select-city-wrap form", function () {
        var usr = $(".select-city-wrap form .select-usr .btn-info").attr("title");
        city_name = $(".select-city-wrap form .select-city .btn-info").attr("title");
        city_id = $(".select-city-wrap .select-city").find("option[data-name=" + city_name + "]").data("id");
        $.cookie('city_id', city_id);
        $.cookie('city_name', city_name)
        $.cookie('usrselected', usr);
	});

	/*$(document).on("click", ".ads-li", function(e) {
	    e.preventDefault();
		$(this).toggleClass("active");
		var block = $(this).find(".ads-ul-block-inner").slideToggle();
	});*/

	/*$(document).on("click", ".fotorama__stage__shaft", function() {
		var fotorama = $('.fotorama').fotorama({allowfullscreen: true}).data('fotorama');
		fotorama.requestFullScreen();
	});*/

	$(document).on("click", ".add-comment .right-col .comment-area form textarea", function() {
		$(this).toggleClass("active");
		$(".add-comment .right-col .comment-area form button").toggleClass("button-active");
		return false;
	});

    $(document).on('click', '.popup-modal-dismiss', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });

    //Открываем окно авторизации
    $(document).on('click', '.login-form-open', function (e) {
    	e.preventDefault();

        call_func =
            function () {
                swal("Отлично!", 'Вы успешно авторизовались.', "success");
            };

        user_auth(call_func);
    });

    //При изменении выбраного города
    $(document).on("change", ".city-filter", function () {
        city_name = $(".location .chosen-container-single .chosen-single span").text();
        $.cookie('city_name', city_name);
        project_params();
    });

    $(".city-filter").find("option[data-id=" + $.cookie('city_id') + "]").attr("selected", "");

    function project_params() {

        city_id = $(".city-filter").find("option[data-name=" + city_name + "]").data("id");

        $.cookie('city_id', city_id);

        NProgress.start();
        $.ajax({
            url: window.location.href,
            type: "GET",
            data: {
                "city_id": city_id
            },
            success: function(result) {
                NProgress.done();
                var $main_content = $(result).find("main").html() || $(result).filter("main").html();
                $('main').html($main_content);
                main();
            }
        });
    }

	function main() {

		$(".text-clock, .time").timeago();

		$("#city-filter").chosen({
			disable_search: true
		});

		$(".select-filter").each(function () {

            var count_options = $(this).find("option").length;

            if (count_options > 10) {
                $(".select-filter").chosen({
                    disable_search: false
                });
            } else {
                $(".select-filter").chosen({
                    disable_search: true
                });
            }
        });

		function inputsValid () {
            if($('*').is('.adsaddform')) {
                $("#form-add-phone").mask("+7 (999) 999 - 9999");
                formValidate();
            }
        }

        inputsValid();

		//Добавление объявления функции
		$("#region-select").change(function(event) {

            if(event.target == this) {
                var id = $(this).val();

                $.ajax({
                    url: "../../../ajax/get.subcats/",
                    dataType: "json",
                    type: "POST",
                    data: {
                        'id' : id,
                    },
                    success: function(data) {
                        $("#cats-select").html(data.html);
                        $("#cats-select").trigger("chosen:updated");
                    },
                    error: function (jqXHR) {
                        swal("Ошибка!", 'Возникла ошибка: ' + jqXHR, "error");
                    }
                });
            }
        });

        $("#cats-select").change(function(event) {

            if (event.target == this) {

                var main_id = $("#region-select").val();
                var sub_id = $(this).val();

                $.ajax({
                    url: "../../../ajax/get.options/",
                    dataType: "json",
                    type: "POST",
                    data: {
                        'main_id' : main_id,
                        'sub_id' : sub_id
                    },
                    success: function(data) {
                        $(".dop-filters-container").html(data.html);
                        $(".dop-filters").chosen();
                        inputsValid();
                    },
                    error: function (jqXHR) {
                        swal("Ошибка!", 'Возникла ошибка: ' + jqXHR, "error");
                    }
                });
            }
        });

		$('.selectpicker').selectpicker({
			style: 'btn-info',
			size: 4
		});

		function fotoramaInitBanner() {
			if ($(window).width() > 991) {
				$('.fotorama').on('fotorama:load fotorama:show fotorama:showend', function (e, fotorama, extra) {
					$(".fotorama-banner").remove();
					$(".fotorama__active").append("<div class='fotorama-banner'><img src='https://gsp1.feomedia.ru/application/views/gorod24_dev/img/banners/banner-2.jpg' alt='alt' /></div>");
				});
			} else {
				$(".fotorama-banner").remove();
			}
		}

		fotoramaInitBanner();

		$(".main-slider").slick({
			dots: true,
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
				if ($(window).width() > 991) {
					var rows = 2;
				} else if ($(window).width() < 479) {
					var rows = 2;
				} else {
					var rows = 1;
				}

				return rows;
			}

			function slider_3_rows() {
				if ($(window).width() < 479) {
					var rows = 2;
				} else {
					var rows = 1;
				}

				return rows;
			}

			function slider_4_rows() {
				if ($(window).width() > 991) {
					var rows = 2;
				} else if ($(window).width() < 479) {
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
		}

		slidersInit();

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

		function ads_boxes_reverse() {
			if ($(window).width() < 991) {
				$(".ads-content-center-box").insertAfter($(".ads-aside-right-box"));
			} else {
				$(".ads-content-center-box").insertBefore($(".ads-aside-right-box"));
			}
		}

		ads_boxes_reverse();

		function news_boxes_reverse() {
			if($(window).width() < 991) {
				$("#news-items-block").insertAfter($("#news-sibebar-block"));
			} else {
				$("#news-items-block").insertBefore($("#news-sibebar-block"));
			}
		}

		news_boxes_reverse();

		function ads_yellow_slider() {
			if ($(window).width() < 767) {
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
		}

		ads_yellow_slider();

		function firm_nav_slider() {
			if ($(window).width() < 767) {
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
			} else {
				$(".slider-style-5").slick('unslick');
			}
		}

		firm_nav_slider();

		$('.leftArrow').on('click', function () {
			var sliderId = $(this).data("sliderId");
			$('#' + sliderId).slick("slickPrev");
		});

		$('.rightArrow').on('click', function () {
			var sliderId = $(this).data("sliderId");
			$('#' + sliderId).slick("slickNext");
		});

		/*function sliderHeight() {
			var heightNewsBlock = $("#news-slider-block").height();
			var heightItemsBlock = $("#news-items-block").height();
			var heightItemsBlock1 = (heightItemsBlock / 2) - 10;
			var block = ($(".info-block-horizontale").height() * 2) + 20;
			var block1 = $(".info-block-verticale").height();

			if ($(window).width() > 991) {
				$(".main-index .info-block-verticale .info-item").css({"height": heightItemsBlock});
				$(".main-slider .slick-slide").css({"height": heightNewsBlock});
				$(".main-news .info-block-verticale .info-item, .main-news .info-block-big .info-item").css({"height": block});
				//$(".main-news .section-1 .left-slider-col .main-slider .slick-slide").css({"height": heightItemsBlock1});
			} else if ($(window).width() > 767) {
				$(".main-slider .slick-slide").css({"height": heightNewsBlock});
				//$(".main-news .section-1 .left-slider-col .main-slider .slick-slide").css({"height": heightItemsBlock});
				$(".main-news .info-block-verticale .info-item").css({"height": "auto"});
				$(".main-news .info-block-big .info-item").css({"height": block1});
			} else {
				$(".main-news .info-block-big .info-item").css({"height": block1});
				$(".main-slider .slick-slide").css({"height": "auto"});
				//$(".main-news .section-1 .left-slider-col .main-slider .slick-slide").css({"height": "auto"});
			}

		}*/

		sliderHeight();

        if($('*').is('.grid')) {
            function masonryItems() {
                $('.grid').masonry({
                    itemSelector: '.grid-item',
                    columnWidth: '.grid-sizer',
                    percentPosition: true
                });
            }

            masonryItems();
        }

		/*$(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });*/

		$.datepicker.regional['ru'] = {
			closeText: 'Закрыть',
			prevText: '<Пред',
			nextText: 'След>',
			currentText: 'Сегодня',
			monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
				'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
			monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
				'Июл','Авг','Сен','Окт','Ноя','Дек'],
			dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
			dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
			dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
			weekHeader: 'Не',
			dateFormat: 'yy-mm-dd',
			firstDay: 1,
			isRTL: false,
			showMonthAfterYear: false,
			yearSuffix: ''
		};
		$.datepicker.setDefaults($.datepicker.regional['ru']);

        /*if($('*').is('#price-slider')) {

            var priceslider = document.querySelector('#price-slider');
            var priceslidermobile = document.querySelector('#mobile-price-slider');

            noUiSlider.create(priceslider, {
                start: [150, 350],
                connect: true,
                tooltips: true,
                step: 1,
                format: wNumb({
                    decimals: 0,
                    postfix: ' 000',
                }),
                range: {
                    'min': 5,
                    'max': 500
                }
            });

            noUiSlider.create(priceslidermobile, {
                start: [150, 350],
                connect: true,
                tooltips: true,
                step: 1,
                format: wNumb({
                    decimals: 0,
                    postfix: ' 000',
                }),
                range: {
                    'min': 5,
                    'max': 500
                }
            });
        }

        if($('*').is('#rooms-slider')) {

            var roomsslider = document.querySelector('#rooms-slider');

            noUiSlider.create(roomsslider, {
                start: [5, 15],
                connect: true,
                tooltips: true,
                step: 1,
                format: wNumb({
                    decimals: 0,
                    postfix: ' комнаты',
                }),
                range: {
                    'min': 1,
                    'max': 20
                }
            });
        }

        if($('*').is('#area-slider')) {

            var areaslider = document.querySelector('#area-slider');

            noUiSlider.create(areaslider, {
                start: [150, 350],
                connect: true,
                tooltips: true,
                step: 1,
                format: wNumb({
                    decimals: 0,
                    postfix: ' м2',
                }),
                range: {
                    'min': 1,
                    'max': 600
                }
            });
        }*/

		window.onresize = function() {
			sliderHeight();
			ads_boxes_reverse();
			ads_yellow_slider();
			firm_nav_slider();
			news_boxes_reverse();
			fotoramaInitBanner();
		}
	}

	main();

    /*function toggle (element) {
        if (this.checked) {
            $("#"+element).attr('disabled', false);
            $("#range_"+element+"_1").attr('disabled', false);
            $("#range_"+element+"_2").attr('disabled', false);
        } else {
            $("#"+element).attr('disabled', true);
            $("#range_"+element+"_1").attr('disabled', true);
            $("#range_"+element+"_2").attr('disabled', true);
        }
    }

    $(document).on("click", ".checkbox_range", function () {
        var slider = $(this).data("sliderId");
        toggle.call(this, slider);
    });*/

    //Аякс вложеное меню
    /*$(document).on("click", ".ajax-anchor-in", function (e) {
        e.stopPropagation();
        e.preventDefault();
        var $a = $(this);
        var href = $a.find('a').attr('href');
        NProgress.start();
        $.ajax({
            type: 'POST',
            data: {"side": "server"},
            url: href,
            success: function (result) {
                NProgress.done();
                try {
                    var $main_content = $(result).find("main").html() || $(result).filter("main").html();
                    $('main').html($main_content);
                    var title = $(result).filter('title').text();
                    var description = $('meta[name="description"]').attr("content");
                    $('head title').text(title);
                    $('head meta[name="description"]').attr("content", description);
                    main();
                } catch (e) {
                    console.error(e);
                }
                history.pushState("state", title, href);
            }
        });
    });

	//Аякс меню
	$(document).on("click", '.ajax-anchor', function (e) {
		console.log("ttt");
		e.preventDefault();
		var $a = $(this);
		var href = $a.find('a').attr('href');
		NProgress.start();
		$.ajax({
			type: 'POST',
			data: {"side": "server"},
			url: href,
			success: function (result) {
				NProgress.done();
				try {
					$a.parent().find('li.active').removeClass('active');
					$a.addClass('active');

					var $main_content = $(result).find("main").html() || $(result).filter("main").html();
					$('main').html($main_content);

					var title = $(result).filter('title').text();
					var description = $('meta[name="description"]').attr("content");
					$('head title').text(title);
					$('head meta[name="description"]').attr("content", description);
					main();
				} catch (e) {
					console.error(e);
				}
				history.pushState("state", title, href);
			}
		});
	});

	//Айкс пагинация
    $(document).on("click", '.pagination li', function(e){
        e.preventDefault();
        var $a = $(this);
        var href = $a.find('a').attr('href');
        NProgress.start();
        $.ajax({
            type : 'POST',
            data:{"side":"server"},
            url : href,
            success : function (result) {
                NProgress.done();
                try {
                    var $main_content = $(result).find("main").html() || $(result).filter("main").html();
                    $('main').html($main_content);
                    var title = $(result).filter('title').text();
                    main();
                } catch(e) { console.error(e); }
                history.pushState("state",title, href);
            }
        });
    });

    $(document).on("click", '.ajax-category', function(e){
        e.preventDefault();
        var $a = $(this);
        var href = $a.attr('href');
        NProgress.start();
        $.ajax({
            type : 'POST',
            data:{"side":"server"},
            url : href,
            success : function (result) {
                NProgress.done();
                try {
                    var $main_content = $(result).find("main").html() || $(result).filter("main").html();
                    $('main').html($main_content);
                    var title = $(result).filter('title').text();

                    $(".text-clock").timeago();

                    if($('*').is('#price-slider')) {

                        var nouislider = document.querySelector('#price-slider');
                        var nouislidermobile = document.querySelector('#mobile-price-slider');

                        noUiSlider.create(nouislider, {
                            start: [150, 350],
                            connect: true,
                            tooltips: true,
                            step: 1,
                            format: wNumb({
                                decimals: 0,
                                postfix: ' 000',
                            }),
                            range: {
                                'min': 5,
                                'max': 500
                            }
                        });

                        noUiSlider.create(nouislidermobile, {
                            start: [150, 350],
                            connect: true,
                            tooltips: true,
                            step: 1,
                            format: wNumb({
                                decimals: 0,
                                postfix: ' 000',
                            }),
                            range: {
                                'min': 5,
                                'max': 500
                            }
                        });
                    }

                    $(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });

                    $.datepicker.regional['ru'] = {
                        closeText: 'Закрыть',
                        prevText: '<Пред',
                        nextText: 'След>',
                        currentText: 'Сегодня',
                        monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
                            'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
                        monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
                            'Июл','Авг','Сен','Окт','Ноя','Дек'],
                        dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
                        dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
                        dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
                        weekHeader: 'Не',
                        dateFormat: 'yy-mm-dd',
                        firstDay: 1,
                        isRTL: false,
                        showMonthAfterYear: false,
                        yearSuffix: ''
                    };
                    $.datepicker.setDefaults($.datepicker.regional['ru']);
                } catch(e) { console.error(e); }
                history.pushState("state",title, href);
            }
        });
    });*/

    /*$(document).on('click', '.price-accept', function (e) {
        e.preventDefault();

        var value1 = $(".noUi-handle-lower .noUi-tooltip").text();
        var value2 = $(".noUi-handle-upper .noUi-tooltip").text();

        var coefLength1 = value1.length / 2;
        var coefLength2 = value2.length / 2;

        var result1 = value1.substr(coefLength1);
        var result2 = value2.substr(coefLength2);

        $.ajax({
            type: "POST",
            url: window.location.href,
            data: {
                "side":"server",
                "result1" : result1,
                "result2" : result2
            },
            success: function(result) {
                NProgress.done();
                try {
                    var $main_content = $(result).find("main").html() || $(result).filter("main").html();
                    $('main').html($main_content);
                    var title = $(result).filter('title').text();

                    $(".text-clock").timeago();

                    if($('*').is('#price-slider')) {

                        var nouislider = document.querySelector('#price-slider');
                        var nouislidermobile = document.querySelector('#mobile-price-slider');

                        noUiSlider.create(nouislider, {
                            start: [result1.slice(0, -3), result2.slice(0, -3)],
                            connect: true,
                            tooltips: true,
                            step: 1,
                            format: wNumb({
                                decimals: 0,
                                postfix: ' 000',
                            }),
                            range: {
                                'min': 5,
                                'max': 500
                            }
                        });

                        noUiSlider.create(nouislidermobile, {
                            start: [result1.slice(0, -3), result2.slice(0, -3)],
                            connect: true,
                            tooltips: true,
                            step: 1,
                            format: wNumb({
                                decimals: 0,
                                postfix: ' 000',
                            }),
                            range: {
                                'min': 5,
                                'max': 500
                            }
                        });
                    }

                    /*$(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });

                    $.datepicker.regional['ru'] = {
                        closeText: 'Закрыть',
                        prevText: '<Пред',
                        nextText: 'След>',
                        currentText: 'Сегодня',
                        monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
                            'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
                        monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
                            'Июл','Авг','Сен','Окт','Ноя','Дек'],
                        dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
                        dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
                        dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
                        weekHeader: 'Не',
                        dateFormat: 'yy-mm-dd',
                        firstDay: 1,
                        isRTL: false,
                        showMonthAfterYear: false,
                        yearSuffix: ''
                    };
                    $.datepicker.setDefaults($.datepicker.regional['ru']);*/
                /*} catch(e) { console.error(e); }
                history.pushState("state", title, window.location.href);
            },
            error: function(xhr, str){
                alert("Возникла ошибка: " + xhr.responseCode);
            }
        })
    });*/


    /*var full_url = window.location.href;
    var arr = full_url.split("//" + window.location.hostname);
    var href = arr.splice(1,1);
    var _current_url = href[0] + "/?";

    $(document).on('click', '.price-accept', function (e) {
        e.preventDefault();

        var _opts = $('.realty-filter .-filter-opt');
        var _opts_array = {};

        $.each(_opts, function (i,opt) {

            var $opt = $(opt);

            switch ($opt.data('filter-type')) {
                case 'droplist':
                    var _v = $opt.val();
                    if (_v) {
                        if(_v != 'null') {
                            _opts_array[$opt.data('filter-key')] = _v;
                        }
                    }
                    break;
                case 'check':
                    var _v = $opt.val();
                    if (_v && $opt.prop('checked')) {
                        if (_opts_array[$opt.data('filter-key')] === undefined) _opts_array[$opt.data('filter-key')] = [];
                        _opts_array[$opt.data('filter-key')].push(_v);
                    }
                    break;
                case 'int':
                    var _v = $opt.val();

                    if (_v) {
                        try {
                            var _v_int = parseInt(_v,10);
                            if (_v_int) {
                                if ($opt.data('max')) {
                                    if (_v_int>parseInt($opt.data('max'),10)) {
                                        _v_int = $opt.data('max');
                                        $opt.val(_v_int);
                                    }
                                }
                                if ($opt.data('min')) {
                                    if (_v_int<parseInt($opt.data('min'),10)) {
                                        _v_int = $opt.data('min');
                                        $opt.val(_v_int);
                                    }
                                }
                                _opts_array[$opt.data('filter-key')] = _v_int;
                            }
                        } catch (e) {}
                    }
                    break;
                case 'opt':
                    break;
            }
        });
        try {
            var f_url = '';

            for (key in _opts_array) {

                var val = _opts_array[key];

                if (typeof val === 'object') {
                    f_url += ""+key+"=["+val.join(',')+"]&";
                } else {
                    f_url += ""+key+"="+val+"&";
                }
            }

            var _new_url = _current_url + f_url;
            document.location.href = _new_url;
        } catch(e) {
            console.error(e);
        }
    });*/

    $(document).on("submit", "#add-form-1", function (e) {
        e.preventDefault();

        var email = $("input[name='email']").val();
            email_show = ($("input[name='email_show']").is(':checked')) ? 1 : 0;
            name = $("input[name='name']").val();
            phone = $("input[name='phone']").val();
            region = $("select[name='region']").val();
            city = $("select[name='city']").val();
            page = 1;

        call_func =
            function () {
                $.ajax({
                    url: "../../../ajax/adv.add/",
                    dataType: "json",
                    type: "POST",
                    data: {
                        'email' : email,
                        'email_show' : email_show,
                        'name' : name,
                        'phone' : phone,
                        'region' : region,
                        'city' : city,
                        'page' : page,
                    },
                    success: function(data){
                        if(data.result == "true") {
                            window.location.href = "../../../ads/add/2/";
                        } else {
                            swal("Внимание!", 'Ошибочка', "warning");
                        }
                    },
                    error: function (jqXHR){
                        swal("Ошибка!", 'Возникла ошибка: ' + jqXHR, "error");
                    }
                });
            }

        user_auth(call_func);
    });

    $(document).on("submit", "#add-form-2", function (e) {
        e.preventDefault();

        var cat = $("select[name='cat']").val();
            sub_cat = $("select[name='sub_cat']").val();
            title = $("input[name='title']").val();
            descr = $("textarea[name='descr']").val();
            site = $("input[name='site']").val();
            price_from_to = $("select[name='price-from-to']").val();
            price = $("input[name='price']").val();
            price_currency = $("select[name='price-currency']").val();
            price_izm = $("select[name='price-izm']").val();
            price_discuse = ($("input[name='price-discuse']").is(':checked')) ? 1 : 0;
            price_free = ($("input[name='price-free']").is(':checked')) ? 1 : 0;
            video = $("input[name='video']").val();
            map_longitude = $("input[name='map-longitude']").val();
            map_latitude = $("input[name='map-latitude']").val();
            adv_id = $("input[name='adv_id']").val();
            page = 2;

        var options = [];
        var $options = $(".dop-option");

        $.each($options, function (i,opt) {

            var options_id = $(opt).data('id');
            var type = $(opt).data('type');
            var value_id = $(opt).val();
            var label = $(opt).data('label').trim();

            switch(type){
                case 0:
                case 1:
                    var value = $(opt).find('option:selected').text().trim();
                    options[options_id] = {
                        "type" : type,
                        "id" : options_id,
                        "label" : label,
                        "value" : value,
                        "value_id" : value_id,
                    };
                    break;
                case 2:
                case 3:
                    var value = $(opt).val().trim(); value_id = null;
                    options[options_id] = {
                        "type" : type,
                        "id" : options_id,
                        "label" : label,
                        "value" : value,
                        "value_id" : value_id,
                    };
                    break;
                case 4:
                    if($(opt).prop("checked")){
                        var value = $(opt).parent().find('label').text().trim();
                        var _value = [{"id":value_id, "label":value}];
                        var _value_id=[value_id];
                        if(options[options_id]!==undefined){
                            options[options_id]['value'].push({"id":value_id, "label":value});	_value = options[options_id]['value'];
                            options[options_id]['value_id'].push(value_id);						_value_id = options[options_id]['value_id'];
                        }
                        options[options_id] = {
                            "type" : type,
                            "id" : options_id,
                            "label" : label,
                            "value" : _value,
                            "value_id" : _value_id
                        };

                    } else { return;}
                    break;
                case 5:
                    var address = $(opt).val().trim(),
                        longitude = $('.map-longitude-'+options_id).val(),
                        latitude = $('.map-latitude-'+options_id).val();
                    if(address!='' && longitude!='' && latitude !=''){
                        var value = {
                            "address" : address,
                            "longitude" : longitude,
                            "latitude" : latitude,
                        };
                        value_id = null;
                        options[options_id] = {
                            "type" : type,
                            "id" : options_id,
                            "label" : label,
                            "value" : value,
                            "value_id" : value_id,
                        };
                    }
                    break;
                case 6:
                case 7:
                case 8:
                    var value = $(opt).val().trim(); value_id = null;
                    options[options_id] = {
                        "type" : type,
                        "id" : options_id,
                        "label" : label,
                        "value" : value,
                        "value_id" : value_id,
                    };
                    break;
                default : var value = null; break;
            }
        });

        var json_options = [];
        for(id in options) { json_options.push(options[id]); }

        console.log(json_options)

        if (adv_id != 'undefined')
        {
            $.ajax({
                url: "../../../ajax/adv.add/",
                dataType: "json",
                type: "POST",
                data: {
                    'page' : page,
                    'adv_id' : adv_id,
                    'cat' : cat,
                    'sub_cat' : sub_cat,
                    'title' : title,
                    'descr' : descr,
                    'site' : site,
                    'price_from_to' : price_from_to,
                    'price' : price,
                    'price_currency' : price_currency,
                    'price_izm' : price_izm,
                    'price_discuse' : price_discuse,
                    'price_free' : price_free,
                    'video' : video,
                    'map_latitude' : map_longitude,
                    'map_longitude' : map_latitude,
                    'options' : json_options
                },
                success: function(data){
                    if(data.result == "true") {
                        window.location.href = "../../../ads/add/3/";
                        //console.log(data.gazeta);
                    } else {
                        swal("Внимание!", 'Ошибочка', "warning");
                    }
                },
                error: function (jqXHR){
                    swal("Ошибка!", 'Возникла ошибка: ' + jqXHR, "error");
                }
            });
        }
        else
        {
            window.location.href = "../../../ads/add/1/";
        }
    });

    $(document).on("submit", "#add-form-3", function (e) {
        e.preventDefault();

        var gazeta_text = $("textarea[name='gazeta_text']").val();
            adv_id = $("input[name='adv_id']").val();
            page = 3;

        if (adv_id != 'undefined') {
            $.ajax({
                url: "../../../ajax/adv.add/",
                dataType: "json",
                type: "POST",
                data: {
                    'page' : page,
                    'adv_id' : adv_id,
                    'gazeta_text' : gazeta_text,
                },
                success: function(data){
                    if(data.result == "true") {
                        window.location.href = "../../../ads/add/4/";
                    } else {
                        swal("Внимание!", 'Ошибочка', "warning");
                    }
                },
                error: function (jqXHR){
                    swal("Ошибка!", 'Возникла ошибка: ' + jqXHR, "error");
                }
            });
        }
    });
});
