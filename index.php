<?php

require_once 'CachedTweets.php';

$twitter = new CachedTweets('junior_grossi');
$tweets = $twitter->getLastTweets(5, true); // 5 tweets with links replacement

foreach ($tweets as $tweet) {
    $createdAt = strtotime($tweet->created_at);
    echo $tweet->text . ' - ' . date('d/m', $createdAt);
}