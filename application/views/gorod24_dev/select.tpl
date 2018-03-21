<head>

	<meta charset="utf-8">

	<title><?=$this->headers['title']?></title>
	<meta name="description" content="">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<meta property="og:image" content="path/to/image.jpg">
	<link rel="image_src" href="http://feo.ua/skin/images/adv_new_soc.jpg">

	<link rel="shortcut icon" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/favicon/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/favicon/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/favicon/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/favicon/apple-touch-icon-114x114.png">

	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/simple-line-icons/css/simple-line-icons.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/chosen/css/chosen.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/slick/css/slick.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/owl/css/owl.carousel.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/fotorama/css/fotorama.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/jquery-ui/css/jquery-ui.min.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/noui-slider/css/nouislider.min.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/nprogress/css/nprogress.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/bootstrap-select/css/bootstrap-select.min.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/magnific-popup/css/magnific-popup.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/feorflogin/css/feorflogin.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/swal/css/sweetalert.css">
	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/css/main.min.css">

	<!-- Chrome, Firefox OS and Opera -->
	<meta name="theme-color" content="#000">
	<!-- Windows Phone -->
	<meta name="msapplication-navbutton-color" content="#000">
	<!-- iOS Safari -->
	<meta name="apple-mobile-web-app-status-bar-style" content="#000">

	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/jquery/jquery.min.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/chosen/js/chosen.jquery.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/slick/js/slick.min.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/owl/js/owl.carousel.min.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/fotorama/js/fotorama.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/jquery-ui/js/jquery-ui.min.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/noui-slider/js/nouislider.min.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/wNumb/wNumb.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/nprogress/js/nprogress.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/cookie/jquery.cookie.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/session/jquery.session.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/bootstrap-select/js/bootstrap-select.min.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/timeago/jquery.timeago.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/timeago/jquery.timeago.ru.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/equalheights/jquery.equalheights.min.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/magnific-popup/js/jquery.magnific-popup.min.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/feorflogin/js/feorflogin.js"></script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/swal/js/sweetalert.min.js"></script>
	<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
	<script src="//yastatic.net/share2/share.js"></script>
	<script type="text/javascript">
        <?php
        if (isset($_SESSION['user']))
        {
            echo "var user_login = 'true';";
        }
        else
        {
            echo "var user_login = 'false';";
        }
            ?>
	</script>
	<script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/js/common.js"></script>

</head>

<body>

	<div class="select-city-wrap" style="background-image: url(<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/bg-select-city.jpg)">
		<form>
			<select class="selectpicker select-city">
				<option data-id="1483" data-name="Выберите ваш город" selected>Выберите ваш город</option>
				<option data-id="1483" data-name="Феодосия">Феодосия</option>
				<option data-id="478" data-name="Керчь">Керчь</option>
			</select>
			<select class="selectpicker select-usr">
				<option selected>Кто вы?</option>
				<option>Гость</option>
				<option>Житель</option>
			</select>
			<button class="select-button">Выбрать</button>
		</form>
	</div>

</body>