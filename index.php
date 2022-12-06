<?php
require __DIR__ .'/include.php';
if ($_GET['DEBUG'] ?? null) {
    enableDebug();
}
date_default_timezone_set('Europe/Moscow');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


(new RSSParser(getConnection(), getTelegramBot()))->processRSS();
