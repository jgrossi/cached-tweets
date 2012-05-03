CachedTweets
=============

This simple PHP class can help you to quickly add public user's tweets (public) in your site.

Usage
-------

### PHP Code

    <?php

    require_once 'CachedTweets.php';

    $twitter = new CachedTweets('your_user_name');
    $tweets = $twitter->getLastTweets(5, true);

    foreach ($tweets as $tweet) {
        $createdAt = strtotime($tweet->created_at);
        echo $tweet->text . ' - ' . date('d/m', $createdAt);
    }

### Options

If you want URLs replaced by links (html tags), just set true (or nothing) to the second parameter:

    $tweets = $twitter->getLastTweets(5, true); // or
    $tweets = $twitter->getLastTweets(5);

Or if you want just pure text, pass `false` to the second parameter.
