<?php
/**
 * Created by Jari @ 17-1-13 16:13
 * JARIZ.PRO
 */

$config["subreddits_frontpage"] = array(
    "AdviceAnimals",
    "fffffffuuuuuuuuuuuu",
    "funny",
    "pics",
    "im14andthisisfunny"
);

$config["types"] = array(
    array(
        "title" => "What's old",
        "url" => "",
        "active" => ""
    ),
    array(
        "title" => "What's new",
        "url" => "new",
        "active" => ""
    )
);

$config["cron_allowed_ips"] = array("xxxxx", null);

$config["slogans"] = array(
    "The site that you love to hate",
    "\"I'm confused and mad at the same time\"",
    "No, This is not 9gag"
);

//oauth data
$config["oauth_id"] = "xxxxx";
$config["oauth_secret"] = "xxxxx";
$config["redirect_url"] = "http://9reddit.com/auth";
$config["scope"] = array(
    "identity",
    "read",
    "mysubreddits",
    "vote"
);