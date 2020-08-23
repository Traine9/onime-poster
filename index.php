<?php
require __DIR__ .'/include.php';
define('DEBUG', (bool) $_GET['DEBUG']);
print "DEBUG: " . (DEBUG ? 'true' : 'false');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

(new RSSParser(getConnection(), getTelegramBot()))->processRSS();
