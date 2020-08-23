<?php

use Longman\TelegramBot\Telegram;

class RSSParser
{
    const CHAT_ID = '-1001419152082';

    private $connection;

    /**
     * @var Telegram
     */
    private $telegramBot;

    /**
     * RSSParser constructor.
     * @param PDO $connection
     * @param Telegram $telegramBot
     */
    public function __construct(PDO $connection, Telegram $telegramBot)
    {
        $this->connection = $connection;
        $this->telegramBot = $telegramBot;
    }

    public function getLastDate(): DateTime
    {
        $lastDate = $this->connection->query('SELECT value FROM value WHERE id = 1', PDO::FETCH_ASSOC);
        foreach ($lastDate as $l) {
            $lastDate = $l['value'];
        }

        return new DateTime($lastDate);
    }

    public function updateLastDate(DateTime $lastDate)
    {
        $statement = $this->connection->prepare('UPDATE value SET value=:value WHERE id = 1');
        $statement->execute([$lastDate->format('Y-m-d H:i:s')]);
    }

    public function getRSSArray()
    {
        $url = 'https://animevost.org/rss.xml';
        $xml = file_get_contents($url);
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        return json_decode($json, TRUE);
    }

    public function processRSS()
    {
        $data = $this->getRSSArray();
        $lastDateOld = $this->getLastDate();
        $lastDate = false;
        foreach ($data['channel']['item'] as $item) {
            if (!$lastDate) {
                $lastDate = new DateTime($item['pubDate']);
            }
            if ($lastDate <= $lastDateOld) {
                continue;
            }
            $title = $item['title'];
            $url = $item['link'];


            $html = file_get_contents($url);
            preg_match('/<img class="imgRadius" src="([^"]+)"/', $html, $r);
            $imageUrl = $r[1];
            $parsedUrl = parse_url($url);
            $imageUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $imageUrl;
            \Longman\TelegramBot\Request::sendPhoto([
                'chat_id' => self::CHAT_ID,
                'photo'   => $imageUrl,
                'caption' => $title . "\n" . $url
            ]);


        }
        $this->updateLastDate($lastDate);
    }


}