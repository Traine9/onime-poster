<?php
require __DIR__ .'/include.php';
(new RSSParser(getConnection(), getTelegramBot()))->processRSS();
