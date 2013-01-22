<?php

namespace RedditApiClient;

/**
 * Subreddit
 *
 * "data" : {
 *     "display_name" : "Ubuntu",
 *     "name"         : "t5_2qh62",
 *     "title"        : "Ubuntu: Linux for Human Beings",
 *     "url"          : "/r/Ubuntu/",
 *     "created"      : 1201269320.0,
 *     "created_utc"  : 1201269320.0,
 *     "over18"       : false,
 *     "subscribers"  : 16259,
 *     "id"           : "2qh62",
 *     "description"  : "Example description"
 * }
 *
 * @author    Henry Smith <henry@henrysmith.org>
 * @copyright 2011 Henry Smith
 * @license   GPLv2.0
 * @package   Reddit API Client
 * @version   0.5.2
 */
class Subreddit extends Entity
{

    /**
     * Returns the subreddit's unique ID
     *
     * @access public
     * @return string
     */
    public function getId()
    {
        return $this['id'];
    }

    public function get($what) {
        return $this[$what];
    }

    /**
     * Returns the subreddit's display name
     *
     * @access public
     * @return string
     */
    public function getDisplayName()
    {
        return $this['display_name'];
    }

    /**
     * Returns the subreddit's title
     *
     * @access public
     * @return string
     */
    public function getTitle()
    {
        return $this['title'];
    }

    /**
     * Returns the subreddit's description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return $this['description'];
    }

    /**
     * Returns the number of users subscribed to the subreddit
     *
     * @access public
     * @return integer
     */
    public function getSubscribers()
    {
        return $this['subscribers'];
    }

    /**
     * Indicates whether the subreddit is restricted to those over 18 years of age
     *
     * @access public
     * @return boolean
     */
    public function isAgeRestricted()
    {
        return isset($this['over18']) ? $this['over18'] : false;
    }

    /**
     * Fetches and returns an array of the top links in the subreddit
     *
     * @access public
     * @return array
     */
    public function getLinks()
    {
        $displayName = $this->getDisplayName();

        $links = $this->reddit->getLinksBySubreddit($displayName);

        return $links;
    }
}
