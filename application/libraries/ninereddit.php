<?php
/**
 * Created by Jari @ 17-1-13 17:19
 * JARIZ.PRO
 */
class ninereddit
{
    public function filter($linkarray)
    {
        $removed = 0;
        $items = 0;
        foreach ($linkarray as $item) {
            $items++;
            $url = $item->getUrl();
            $ok = true;
            $alnumcheck = false;
            if (substr($item->getThingId(), 0, 3) == "t3_") {
                if (substr($url, 0, 7) == "http://") {
                    $ex = explode("/", substr($url, 7));
                    switch ($ex[0]) {
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

            if ($alnumcheck && $ok) $ok = ctype_alnum($ex[1]);

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
