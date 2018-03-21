<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title><?=$this->headers['title']?></title>
    <meta name="description" content="">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <?php
        if ($this->headers['p_id'])
        {
            echo '
                <meta property="og:image" content="https://gorod24.online/thrumbs/news/new_'.$this->headers['p_id'].'_640_320.jpg">
                <link rel="image_src" href="https://gorod24.online/thrumbs/news/new_'.$this->headers['p_id'].'_640_320.jpg">
            ';
        }
        else
        {
            echo '
                <meta property="og:image" content="https://feo.ua/skin/images/adv_new_soc.jpg">
                <link rel="image_src" href="https://feo.ua/skin/images/adv_new_soc.jpg">
            ';
        }
    ?>

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
    <script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/jquery-ui/js/jquery.datepicker.extension.range.min.js"></script>
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
    <script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/masonry/masonry.min.js"></script>
    <script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/validate/jquery.validate.js"></script>
    <script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/validate/localization/messages_ru.js"></script>
    <script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/mask/jquery.maskedinput.min.js"></script>
    <script src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/libs/audioplayer/audioplayer.js"></script>
    <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
    <script src="//yastatic.net/share2/share.js"></script>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
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

<header class="main-header">
    <div class="main-wrap">
        <div class="container">
            <div class="sect-1">
                <div class="logo-wrap">
                    <div class="logo">
                        <a href="/">
                            <img src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/logo-head.png" alt="ФЕО.РФ">
                        </a>
                    </div>
                </div>
            </div>
            <div class="sect-2">
                <?php
                    if ($_SESSION['user']['id'])
                    {
                        echo '
                            <div class="authorized-user">
                                <div class="user-ava"><img src="'.$this->data['user_ava'].'" alt="'.$this->data['user_name'].'"></div>
                                <div class="user-name">'.$this->data['user_name'].'</div>
                                <a href="'.$GLOBALS['CONFIG']['HTTP_HOST'].'/login/exit/" style="font-size: 12px; display: inline-block; vertical-align: middle; margin-left: 10px;">Выйти</a>
                            </div>
                        ';
                    }
                    else
                    {
                        echo '
                            <div class="auth-wrap">
                                <div class="login"><span class="icon-user"></span><a href="#login-form" class="login-form-open">Вход</a></div>
                                <div class="reg"><a href="#">Регистрация</a></div>
                            </div>
                        ';
                    }
                ?>
                <div class="location-wrap">
                    <div class="location">
                        <span class="icon-location-pin hidden-xs"></span>
                        <select name="city-filter" class="city-filter" id="city-filter">
                            <?php foreach ((array) $this->data['projects'] as $item): ?>
                            <option value="<?=$item['name']?>" data-id="<?=$item['city_id']?>" data-name="<?=$item['name']?>"><?=$item['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="usr-wrap">
                    <?php
                        if($_COOKIE['usrselected'] == "Житель")
                        {
                            echo '<div class="resident"><span class="icon-direction"></span><span>Житель</span></div>';
                        }
                        else
                        {
                            echo '<div class="visitor"><span class="icon-eye"></span><span>Гость</span></div>';
                        }
                    ?>
                </div>
            </div>
            <div class="sect-3">
                <div class="add-listing">
                    <span class="icon-plus"></span>
                    <a href="#">Добавить объявление</a>
                </div>
                <div class="items-wrap">
                    <?=$this->merchandise->head_rates_vidget();?>
                    <div class="item">
                        <i class="fa fa-thermometer-three-quarters" aria-hidden="true"></i>
                        <div><span>+7</span> <span class="temperature"> °C</span></div>
                    </div>
                    <div class="item">
                        <i class="fa fa-tint" aria-hidden="true"></i>
                        <div><span>+7</span> <span class="temperature"> °C</span></div>
                    </div>
                </div>
            </div>
            <div class="sect-4">
                <div class="banner-wrap">
                    <div class="banner">
                        <a href="#">
                            <img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-1.jpg" alt="alt">
                        </a>
                    </div>
                </div>
                <div class="items-wrap">
                    <div class="item">
                        <span class="icon-wrap"><i class="fa mini fa-usd" aria-hidden="true"></i></span>
                        <div><span>60</span> р.</div>
                    </div>
                    <div class="item">
                        <span class="icon-wrap"><i class="fa mini fa-eur" aria-hidden="true"></i></span>
                        <div><span>70</span> р.</div>
                    </div>
                    <div class="item">
                        <i class="fa fa-thermometer-three-quarters" aria-hidden="true"></i>
                        <div><span>+7</span> <span class="temperature"> °C</span></div>
                    </div>
                    <div class="item">
                        <i class="fa fa-tint" aria-hidden="true"></i>
                        <div><span>+7</span> <span class="temperature"> °C</span></div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="main-menu">
            <div class="container nps">
                <ul class="menu hidden-xs">
                    <?=do_shortcode('[menu url="'.$_SERVER['REQUEST_URI'].'" type="main"]');?>
                </ul>

                <div class="toggle-menu-wrap">
                    <a href="#" class="toggle-menu hidden-lg hidden-md hidden-sm"><span></span></a>
                </div>

                <div class="auth-wrap hidden-lg hidden-md hidden-sm">
                    <div class="login"><a href="#">Вход</a></div>
                    <div class="reg"><a href="#">Регистрация</a></div>
                </div>

                <span class="search-button">
					<i class="fa fa-search" aria-hidden="true"></i>
					<input type="text" class="search-input" placeholder="Поиск...">
				</span>

                <div class="toggle-mobile-menu hidden-lg hidden-md hidden-sm">
                    <ul class="mobile-block-menu">
                        <?=do_shortcode('[menu type="mobile"]');?>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="main-menu-line hidden-xs" style="display: none;"></div>
    </div>
</header>

<div id="login-form" class="login-form-wrap white-popup-block mfp-hide">
    <div class="top-sect">
        <span>Авторизация</span>
        <button class="mfp-close" title="Закрыть" type="button">×</button>
    </div>
    <div class="content-sect-left">
        <form action="/login/" method="POST" id="login-form-auth">
            <span class="head-span">Пользователь feo.ua</span>
            <div class="pole">
                <label for="input-login">Логин</label>
                <input type="text" id="input-login" name="login">
            </div>
            <div class="pole">
                <label for="input-pass" class="label-pass">Пароль</label>
                <input type="password" id="input-pass" name="password">
            </div>
            <button id="login-form-sub" type="submit">Войти</button>
        </form>
        <div class="item-link">
            <a href="http://xn--e1asq.xn--p1ai/myroot/restore/" target="_blank">восстановить пароль</a>
        </div>
        <div class="item-link">
            <a href="http://xn--e1asq.xn--p1ai/myroot/register/" target="_blank">регистрация</a>
        </div>
    </div>
    <div class="content-sect-right">
        <p>или если<br><span>у вас нет аккаунта</span><br>войдите через<br>социальную сеть</p>
        <div class="soc">
            <a class="vk-auth" href="https://oauth.vk.com/authorize?client_id=<?=$GLOBALS['CONFIG']['SOCIAL']['vk_appid']?>&display=page&redirect_uri=<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/login/vk/&scope=email&response_type=code&v=5.69"></a>
            <a class="fb-auth" href="https://www.facebook.com/v2.11/dialog/oauth?client_id=<?=$GLOBALS['CONFIG']['SOCIAL']['fb_appid']?>&redirect_uri=<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/login/fb/&scope=public_profile,email,user_birthday,user_location&response_type=code"></a>
            <a class="od-auth" href="https://connect.ok.ru/oauth/authorize?client_id=<?=$GLOBALS['CONFIG']['SOCIAL']['od_appid']?>&scope=VALUABLE_ACCESS;GET_EMAIL;LONG_ACCESS_TOKEN&response_type=code&redirect_uri=<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/login/od/"></a>
        </div>
    </div>
    <div class="footer-sect">
        <span>Входя на портал и регистриуясь на нем Вы принимаете: </span><div class="item-link"><a href="http://xn--e1asq.xn--p1ai/soglashenie_polzovatelya" target="_blank">соглашение</a></div>
    </div>
</div>