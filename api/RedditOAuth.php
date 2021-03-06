<?php

require_once 'Entity.php';
require_once 'Account.php';
require_once 'Comment.php';
require_once 'HttpRequest.php';
require_once 'HttpResponse.php';
require_once 'Link.php';
require_once 'RedditException.php';
require_once 'Subreddit.php';

require_once("Client.php");
require_once("GrantType/IGrantType.php");
require_once("GrantType/AuthorizationCode.php");

/**
 * Created by JariZ.pro
 * Basic Reddit OAuth client
 * Very, very basic, but it gets the job done
 */
class RedditOAuth
{
    private $client;

    private $authorizeUrl = 'https://ssl.reddit.com/api/v1/authorize';
    private $accessTokenUrl = 'https://ssl.reddit.com/api/v1/access_token';
    private $realmUrl = "https://oauth.reddit.com/";
    private $redirectUrl;

    private $scope;
    public $modHash;

    public $authorized = false;

    public function __construct($clientId, $clientSecret, $redirectUrl, $scope=array("identity"))
    {
        $this->scope = $scope;
        $this->client = new OAuth2\Client($clientId, $clientSecret, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
        $this->redirectUrl = $redirectUrl;
    }

    public function getModHash() {
        return $this->modHash;
    }

    public function setModHash($hash) {
        $this->modHash = $hash;
    }

    public function setAccessToken($token)
    {
        $this->authorized = true;
        $this->client->setAccessToken($token);
        $this->client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_BEARER);
    }

    public function Fetch($url, $parameters = array(), $http_method = \OAuth2\Client::HTTP_METHOD_GET, array $http_headers = array(), $form_content_type = \OAuth2\Client::HTTP_FORM_CONTENT_TYPE_MULTIPART) {
        try
        {
            $f = $this->client->fetch($this->realmUrl.$url, $parameters, $http_method, $http_headers, $form_content_type);
            if(isset($f["result"]["data"]["modhash"])) $this->modHash = $f["result"]["data"]["modhash"];
            return $f;
        } catch(Exception $e) {
            show_error($e->getMessage());
        }
    }

    private function buildScope() {
        $i = -1;
        $s = "";
        foreach($this->scope as $scope) {
            $i++;
            if($i == 0) $s = $scope;
            else $s .= ",$scope";
        }
        return $s;
    }

    public function Auth()
    {
        if (!isset($_GET["code"])) {
            $authUrl = $this->client->getAuthenticationUrl($this->authorizeUrl, $this->redirectUrl, array("scope" => $this->buildScope(), "state" => str_shuffle("abcdefghijkl123456789")));
            header("Location: " . $authUrl);
            return false;
        } else {
            $params = array("code" => $_GET["code"], "redirect_uri" => $this->redirectUrl);
            $response = $this->client->getAccessToken($this->accessTokenUrl, \OAuth2\Client::GRANT_TYPE_AUTH_CODE, $params);
            $accessTokenResult = $response["result"];
            if (!isset($accessTokenResult["error"])) {
                $this->setAccessToken($accessTokenResult["access_token"]);
                return $accessTokenResult["access_token"];
            } else return false;
        }
    }

    public function vote($thingId, $direction)
    {
        $verb = 'POST';
        $url = 'api/vote';
        $data = array(
            'id' => $thingId,
            'dir' => $direction,
            'uh' => $this->modHash,
        );

        $response = $this->Fetch($url, $data, $verb);
        //var_dump($response);
        if (empty($response["result"])) {
            return true;
        } else {
            return false;
        }
    }

    public function getSubreddit($s) {
        $response = $this->Fetch("/r/$s.json", array("limit" => 100));
        $links = array();
        if(isset($response["result"]["data"]["error"])) return false;
        foreach ($response["result"]['data']['children'] as $child) {

            $link = new \RedditApiClient\Link($this);
            $link->setData($child['data']);

            $links[] = $link;
        }

        return $links;
    }

    public function getMe() {
        $response = $this->Fetch("api/v1/me.json");
        //var_dump($response);
        if(!isset($response["result"]["error"]))
            return array($response["result"]);
        else return array();
    }

    public function getMySubreddits() {
        $response = $this->Fetch('reddits/mine.json', array("limit" => 100));

        $subreddits = array();

        foreach ($response["result"]['data']['children'] as $child) {

            $subreddit = new \RedditApiClient\Subreddit($this);
            $subreddit->setData($child['data']);

            $subreddits[] = $subreddit;
        }
        return $subreddits;
    }


}