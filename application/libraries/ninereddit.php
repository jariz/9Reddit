<?php
/**
 * Created by Jari @ 17-1-13 17:19
 * JARIZ.PRO
 */
class ninereddit
{
    public function convertUrlQuery($query) {
        $queryParts = explode('&', $query);

        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }

        return $params;
    }

    public function filter($linkarray)
    {
        $removed = 0;
        $items = 0;
        foreach ($linkarray as $item) {
            $items++;
            $url = $item->getUrl();
            $ok = true;
            $video = false;
            $alnumcheck = false;
            if (substr($item->getThingId(), 0, 3) == "t3_") {
                if (substr($url, 0, 7) == "http://") {
                    $ex = explode("/", substr($url, 7));

                    switch ($ex[0]) {
                        case "youtube.com":
                        case "www.youtube.com":
                            $purl = parse_url($url);
                            $purl["query"] = str_replace("&amp;", "&", $purl["query"]);
                            if(isset($purl["query"])) {
                                $q = $this->convertUrlQuery($purl["query"]);
                                if(isset($q["v"]))
                                    if(ctype_alnum($q["v"])) {
                                        $video = true;
                                        $url = "http://www.youtube.com/embed/{$q["v"]}?origin=".base_url();
                                    } else $ok = false;
                            } else $ok = false;
                        break;
                        case "vimeo.com":
                        case "www.vimeo.com":
                            if(isset($ex[1]))
                                if(ctype_digit($ex[1])) {
                                    $url = "http://player.vimeo.com/video/{$ex[1]}";
                                    $video = true;
                            } else $ok = false;
                        else $ok = false;
                        break;
                        case "youtu.be":
                            if(ctype_alnum($ex[1])) {
                                $video = true;
                                $url = "http://www.youtube.com/embed/{$ex[1]}?origin=".base_url();
                            } else $ok = false;
                            break;
                        case "www.imgur.com":
                        case "imgur.com":
                            if ($ex[1] != "a")
                                $url = "http://i.imgur.com/{$ex[1]}.png";
                            else $ok = false;
                            $alnumcheck = true;
                            break;

                        case "www.quickmeme.com":
                        case "quickmeme.com":
                            $url = "http://i.qkme.me/{$ex[2]}.jpg";
                            $alnumcheck = true;
                            break;
                        case "qkme.me":
                        case "www.qkme.me":
                            $url = "http://i.qkme.me/{$ex[1]}.jpg";
                            $alnumcheck = true;
                            break;
                        default:
                            switch (substr($url, -3)) {
                                case "png":
                                case "jpg":
                                case "gif":
                                    break;
                                default:
                                    $ok = false;
                                    break;
                            }
                            break;
                    }
                } else $ok = false;
            } else $ok = false;

            if ($alnumcheck && $ok && !$video) $ok = ctype_alnum($ex[1]);
            if($video && $ok) $url = "<iframe type=\"text/html\" src=\"".$url."\" frameborder=\"0\"></iframe>";
            else if($ok && !$video) $url = "<img src=\"$url\">";

            if ($ok) {
                $post = array(
                    "title" => $item->getTitle(),
                    "upvotes" => $item->getUpvotes(),
                    "comments" => $item->countComments(),
                    "author" => $item->getAuthorName(),
                    "url" => $url,
                    "thing" => $item->getThingId()
                );

                $posts[] = $post;
            } else $removed++;
        }

        if(isset($posts))
            return $posts;
        else return array();
    }
}
