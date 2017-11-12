<?php
include_once APPDIR ."/application/includes.php"; // файлы ядра
include_once ADMINDIR .'/application/core/route.php'; // маршрутизатор
include_once ADMINDIR .'/application/load.php'; // маршрутизатор

getPlugins(); // подключаем плагины

$route = new Route(); // создаем маршрутизатор
//$route->session_run(); // запуск сессии пользователя
$route->start(); // запускаем маршрутизатор
