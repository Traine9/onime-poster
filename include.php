<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ .'/RSSParser.php';
function getConnection() : ?PDO
{
    static $conn = null;
    if (!$conn) {
        $servername = "f80b6byii2vwv8cx.chr7pe7iynqr.eu-west-1.rds.amazonaws.com";
        $username = "hku897wey7x2hefz";
        $password = "wrdo12m7ecr8u77p";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=fdg8hfjnr3bnca2t", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connected successfully";
        } catch(PDOException $e) {
            $conn = null;
            //echo "Connection failed: " . $e->getMessage();
        }
    }
    return $conn;
}

function getTelegramBot() : \Longman\TelegramBot\Telegram
{
    $bot_api_key  = '1286970428:AAE2bqouLajOSa7wbUtGyxU0ymXljQKQQUs';
    $bot_username = 'AnimePosterBot';

    try {
        // Create Telegram API object
        $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    } catch (Longman\TelegramBot\Exception\TelegramException $e) {
        // log telegram errors
        // echo $e->getMessage();
    }
    return $telegram;
}