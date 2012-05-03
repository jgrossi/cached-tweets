<?php

/**
 * This class provides a simple way to get the last tweets from a Twitter user
 * @author Junior GROSSI
 * @link http://juniorgrossi.com
 * @example index.php
 * @version 0.5
 */
class CachedTweets
{
    /**
     * The Twitter account's display name
     * @var string 
     */
    protected $_username = null;
    
    /**
     * The time for caching, in seconds
     * @var integer 
     */
    protected $_cacheTime = 1800;
    
    /**
     * The cache file. Where the json will be cached
     * @var string 
     */
    protected $_cacheFile = null;
    
    /**
     * The constructor for the class, using <username> as parameter
     * @param string $username The Twitter's display name
     */
    public function __construct($username)
    {
        $this->_username = $username;
        $this->_cacheFile = dirname(__FILE__) . "/tweets-{$username}.json";
    }
    
    /**
     * Set the username (display name)
     * @param string $username Twitter display name
     */
    public function setUsername($username)
    {
        $this->_username = $username;
    }
    
    /**
     * Get the username
     * @return string Twitter display name 
     */
    public function getUsername()
    {
        return $this->_username;
    }
    
    /**
     * Get the time for caching
     * @return integer The time in seconds
     */
    public function getCacheTime()
    {
        return $this->_cacheTime;
    }
    
    /**
     * Set the time for caching
     * @param int $cacheTime Time to cach the tweets in seconds
     */
    public function setCacheTime($cacheTime)
    {
        $this->_cacheTime = (int) $cacheTime;
    }
    
    /**
     * Set the file to cache the JSON. The file could not exists.
     * @param string $pathToJsonFile Path to the JSON file
     */
    public function setCacheFile($pathToJsonFile)
    {
        $this->_cacheFile = $pathToJsonFile;
    }
    
    /**
     * Get the path to the cached file
     * @return string Path 
     */
    public function getCacheFile()
    {
        return $this->_cacheFile;
    }
    
    /**
     * Get the last tweets for the user
     * @param int $count The tweets limit
     * @param boolean $replaceLinks Do want to replace links with real link <a> tags?
     * @return array Array with stdObjects with the tweets
     */
    public function getLastTweets($count = 10, $replaceLinks = true)
    {
        $tweets = $this->_verifyAndReturnTweets((int)$count);
        
        if ($replaceLinks) {
            foreach ($tweets as &$object) {
                $object->text = $this->replaceLinkTags($object->text);
            }
        }
        
        return $tweets;
    }
    
    /**
     * Verifies the cache and return the array
     * @param int $count The tweets number limit
     * @return array Array with tweets 
     */
    protected function _verifyAndReturnTweets($count)
    {
        if ($cached = $this->_isInCache()) {
            return json_decode($cached);
        }
        
        $params = "screen_name={$this->_username}&count=$count&include_rts=1";
        $url = "http://api.twitter.com/1/statuses/user_timeline.json?$params";
        $json = file_get_contents($url);
        file_put_contents($this->_cacheFile, $json);
        
        return @json_decode($json);
    }
    
    /**
     * Verifies if the cached file content is in cache time or not
     * @return boolean Return True for valid cached file or False to not valid
     */
    protected function _isInCache()
    {
        $cacheFile = $this->_cacheFile;
        
        if (!file_exists($cacheFile)) {
            return false;
        }
        
        $lastUpdatedTime = filemtime($cacheFile);

        if (($lastUpdatedTime + $this->_cacheTime) < time()) { // must get new
            return false;
        } else {
            $json = file_get_contents($cacheFile);
            return $json;
        }
    }
    
    /**
     * Replace URLs with <a> tags in text
     * @param string $text The text to be checked
     * @return string The new text with <a> tags if present 
     */
    public static function replaceLinkTags($text)
    {
        $pattern = "/http[s]?\:\/\/[a-zA-Z0-9\.\_\-\/]{5,}/";
        preg_match($pattern, $text, $matches);
        
        foreach ($matches as $key => $match) {
            $text = str_replace($match, sprintf('<a target="_blank" href="%s">%s</a>', $match, $match), $text);
        }
        
        return $text;
    }
    
}