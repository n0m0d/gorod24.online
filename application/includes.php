<?php
// подключаем файлы ядра
include_once APPDIR .'/application/core/ini.php'; // редактор файла конфигураций *.ini
include_once APPDIR .'/application/core/safemysql.class.php';	// класс для работы с СУБД MySQL
///////////////////////////////////////////////////////////////////////////////////////////
// сканируем конфиг и создаем подключение к БД
$GLOBALS = [];
$config = new ini(APPDIR . '/config.ini');
$GLOBALS['CONFIG'] = [];
$GLOBALS['CONFIG']['HTTP_PROTOCOL'] = $config->read('main', 'server_protocol');
$GLOBALS['CONFIG']['HTTP_HOST'] = $config->read('main', 'server_url');
$GLOBALS['CONFIG']['SERVER_NAME'] = $config->read('main', 'server_name');
$GLOBALS['CONFIG']['CASH'] = $config->read('main', 'cahe');
$GLOBALS['CONFIG']['TEMPLATE'] = $config->read('main', 'template');
$GLOBALS['CONFIG']['TEMPLATE_EXTENSION'] = $config->read('main', 'template_extension');
$GLOBALS['CONFIG']['DB'] = [];
$GLOBALS['CONFIG']['DB']['host'] = $config->read('MYSQL', 'host');
$GLOBALS['CONFIG']['DB']['user'] = $config->read('MYSQL', 'user');
$GLOBALS['CONFIG']['DB']['password'] = $config->read('MYSQL', 'password');
$GLOBALS['CONFIG']['DB']['db'] = $config->read('MYSQL', 'db');
$GLOBALS['CONFIG']['DB']['table_prefix'] = $config->read('MYSQL', 'table_prefix');
$GLOBALS['CONFIG']['DB']['charset'] = $config->read('MYSQL', 'charset');

$GLOBALS['DB']['localhost'] = new SafeMySQL(array(	
									'host'    => $GLOBALS['CONFIG']['DB']['host'],
									'user'    => $GLOBALS['CONFIG']['DB']['user'],
									'pass'    => $GLOBALS['CONFIG']['DB']['password'],
									'db'      => $GLOBALS['CONFIG']['DB']['db'],
									'charset' => $GLOBALS['CONFIG']['DB']['charset']
								)) or die('База данных не доступна...');
								
///////////////////////////////////////////////////////////////////////////////////////////
include_once APPDIR .'/application/core/functions.php'; // основные функции приложения
include_once APPDIR ."/application/core/error.php"; // сканер ошибок
include_once APPDIR ."/application/core/Paginator.php"; // постраничная навигация
include_once APPDIR ."/application/core/hooks.php"; // крючки
include_once APPDIR ."/application/core/Ecstatic.php"; // Рендер таблиц
include_once APPDIR ."/application/core/wp-load.php"; // (WORDPRESS)
include_once APPDIR ."/application/core/wp-kses.php"; // (WORDPRESS)
include_once APPDIR ."/application/core/wp-general-template.php"; // (WORDPRESS)
include_once APPDIR ."/application/core/wp-pomo/translations.php"; // языки (WORDPRESS)
include_once APPDIR ."/application/core/wp-l10n.php"; // языки (WORDPRESS)
include_once APPDIR ."/application/core/wp-formatting.php"; // форматирование HTML кодов (WORDPRESS)
include_once APPDIR ."/application/core/wp-shortcodes.php"; // шорткоды (WORDPRESS)
include_once APPDIR .'/application/core/wp-class.wp-dependencies.php'; // (WORDPRESS)
include_once APPDIR .'/application/core/wp-class.wp-scripts.php'; 		
include_once APPDIR .'/application/core/wp-functions.wp-scripts.php'; // основные функции работы со скриптами (WORDPRESS)
include_once APPDIR .'/application/core/wp-class.wp-styles.php';		
include_once APPDIR .'/application/core/wp-functions.wp-styles.php'; // основные функции работы с стилями (WORDPRESS)
include_once APPDIR .'/application/core/wp-option.php'; // (WORDPRESS)
include_once APPDIR .'/application/core/wp-functions.php'; // основные функции приложения (WORDPRESS)
include_once APPDIR .'/application/core/simple_html_dom.php';	// класс для обработки html файлов
include_once APPDIR .'/application/core/nokogiri.php';	// класс для обработки html файлов
include_once APPDIR .'/application/core/model.php'; // основной класс модели
include_once APPDIR .'/application/core/view.php'; // основной класс представления
include_once APPDIR .'/application/core/controller.php'; // основной класс когтроллера
include_once APPDIR .'/application/core/ipgeobase.php-master/ipgeobase.php'; // геолокация
include_once APPDIR .'/application/core/regedit.php'; // реестр
include_once APPDIR .'/application/core/cookie.php'; // пиченьки
include_once APPDIR .'/application/core/browser.php'; // браузер
include_once APPDIR .'/application/core/cron.php'; // планировщик cron
include_once APPDIR .'/application/core/email_work.php'; // EMAIL рассылка
include_once APPDIR .'/application/core/render-admin-page.php'; // Создатель страницы админки
include_once APPDIR .'/application/core/rss.php'; // rss генератор
include_once APPDIR .'/application/core/idna_convert.class.php'; // rss генератор
?>