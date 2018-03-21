<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$this->headers['title']?></title>
	<meta name="description" content="<?=$this->headers['description']?>">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="/application/views/gorod24/libs/font-awesome/css/font-awesome.min.css">
	<meta property="og:image" content="/application/views/gorod24/path/to/image.jpg">
	<link rel="shortcut icon" href="/application/views/gorod24/img/favicon/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" href="/application/views/gorod24/img/favicon/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/application/views/gorod24/img/favicon/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/application/views/gorod24/img/favicon/apple-touch-icon-114x114.png">
	
	<meta property="og:title" content="<?=$this->headers['title']?>" />

	<link rel="stylesheet" href="/application/views/gorod24/css/main.css?ver=1.3">

	<!-- Chrome, Firefox OS and Opera -->
	<meta name="theme-color" content="#000">
	<!-- Windows Phone -->
	<meta name="msapplication-navbutton-color" content="#000">
	<!-- iOS Safari -->
	<meta name="apple-mobile-web-app-status-bar-style" content="#000">
	
	<script src="/application/views/gorod24/js/scripts.min.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script src="/application/views/gorod24/js/jquery.mask.js"></script>
	<script src="/application/views/gorod24/js/common.js?ver=1.2"></script>
	
	<link rel="image_src" href="https://gorod24.online/application/views/gorod24/img/soc.jpg">
	<meta property="og:image" content="https://gorod24.online/application/views/gorod24/img/soc.jpg">
	<?php wp_print_styles();?>
	<?php wp_print_scripts();?>
</head>
<body>
	<header class="main-header">
		<div class="container-wrap">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="logo-wrap">
							<a href="/">
								<img src="/application/views/gorod24/img/logo.png" alt="logo">
							</a>
						</div>
						<div style="text-align:center;display: inline-block; margin:0 20px;"><?= do_shortcode("[yashare]");?></div>

						<div class="menu-wrap hidden-xs hidden-sm">
							<nav class="main-menu">
								<ul class="nav">
									<li><a href="#section-1">Получить ссылку</a></li>
									<li><a href="#section-2">Новости</a></li>
									<li><a href="#section-3">Объявления</a></li>
									<li><a href="#section-4">Предприятия</a></li>
									<li><a href="#section-6">Народный бренд</a></li>
								</ul>
							</nav>
						</div>

						<a href="#" class="toggle-menu hidden-lg hidden-md"><span></span></a>

						<div class="main-mobile-menu hidden-lg hidden-md">
							<ul class="toggle-mobile-menu nav">
								<li><a href="#section-1">Получить ссылку</a></li>
								<li><a href="#section-2">Новости</a></li>
								<li><a href="#section-3">Объявления</a></li>
								<li><a href="#section-4">Предприятия</a></li>
								<li><a href="#section-6">Народный бренд</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<main class="main-body">