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
        $statement->execute(['value' => $lastDate->format('c')]);
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
        $items = array_reverse($data['channel']['item']);
        if (isDebug()) {
            print "START CYCLE\n";
        }
        foreach ($items as $item) {
            $pubDate = new DateTime($item['pubDate']);
            if (!$lastDate || $pubDate > $lastDate) {
                $lastDate = $pubDate;
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
            $urlOriginal = $parsedUrl['scheme'] . '://' . 'animevost.org' . $parsedUrl['path'];
            $urlText = '<a href="' . $urlOriginal . '">AnimeVost</a>';
            $urlText .= ' | <a href="' . $url . '">Mirror</a>';
            $urlText .= ' ' . $pubDate->format('d-m');
            $caption =  htmlspecialchars($title)
                . "\n" . $urlText;
            $result = \Longman\TelegramBot\Request::sendPhoto([
                'chat_id' => self::CHAT_ID,
                'photo'   => $imageUrl,
                'caption' => $caption,
                'parse_mode' => 'HTML'
            ]);
            if (isDebug()) {
                print_r([
                    'chat_id' => self::CHAT_ID,
                    'photo'   => $imageUrl,
                    'caption' => $caption
                ]);
                print_r($result);
                exit();
            }


        }
        if ($lastDate) {
            $this->updateLastDate($lastDate);
        }
    }
}