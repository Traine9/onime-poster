<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

(new RSSParser(getConnection(), getTelegramBot()))->processRSS();
