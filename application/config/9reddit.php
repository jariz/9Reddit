<?php
/**
 * Created by Jari @ 17-1-13 16:13
 * JARIZ.PRO
 */

$config["subreddits_frontpage"] = array(
    "pics", "funny", "AdviceAnimals", "fffffffuuuuuuuuuuuu", "im14andthisisfunny", "facepalm", "videos", "vimeo"
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

$config["cron_allowed_ips"] = array("xxxx", null, "xxxx");

$config["slogans"] = array(
    "The site that you love to hate",
    "The frontpage of the internet yesterday",
    "You like it and hate it at the same time"
);

//oauth data
$config["oauth_id"] = "xxxx";
$config["oauth_secret"] = "xxxx";
$config["redirect_url"] = "http://9reddit.com/auth";
$config["scope"] = array(
    "identity",
    "mysubreddits",
    "vote",
    "read"
);

$config["recommendations"] = array(
    "funny",
    "pics",
    "gaming",
    "WTF",
    "videos",
    "Music",
    "movies",
    "AdviceAnimals",
    "aww",
    "gifs",
    "fffffffuuuuuuuuuuuu",
    "gonewild",
    "nsfw",
    "4chan",
    "comics",
    "ImGoingToHellForThis",
    "reactiongifs",
    "pokemon",
    "skyrim",
    "wallpapers",
    "facepalm",
    "RealGirls",
    "all"
);

$config["buttons"] = array(
    "btn-info", "btn-primary", "btn-danger", "btn-warning", "", "btn-success", "btn-inverse"
);