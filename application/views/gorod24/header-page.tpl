<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$this->headers['title']?></title>
	<meta name="description" content="">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<meta property="og:image" content="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/path/to/image.jpg">
	<link rel="shortcut icon" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/img/favicon/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/img/favicon/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/img/favicon/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/img/favicon/apple-touch-icon-114x114.png">

	<link rel="stylesheet" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/css/main.css">

	<!-- Chrome, Firefox OS and Opera -->
	<meta name="theme-color" content="#000">
	<!-- Windows Phone -->
	<meta name="msapplication-navbutton-color" content="#000">
	<!-- iOS Safari -->
	<meta name="apple-mobile-web-app-status-bar-style" content="#000">
	<link rel="image_src" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/img/soc.jpg">
	<meta property="og:image" content="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/img/soc.jpg">
</head>
<body>
	<header class="main-header">
		<div class="container-wrap">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="logo-wrap">
							<a href="/">
								<img src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/img/logo.png" alt="logo">
							</a>
						</div>
						
						<div style="text-align:center;display: inline-block; margin:0 20px;"><?= do_shortcode("[yashare]");?></div>
						
						<div class="menu-wrap hidden-xs hidden-sm">
						</div>

						<a href="#" class="toggle-menu hidden-lg hidden-md"><span></span></a>

						<div class="main-mobile-menu hidden-lg hidden-md">
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<main class="main-body">