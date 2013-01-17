<?

class page extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->database();

        $this->load->helper(array("url", "file"));
        $this->load->library(array("parser", "reddit"));
        $this->load->driver("session");
    }

    public function index()
    {
        $this->parser->parse("template", array("post" => $this->db->query("SELECT * FROM posts")->result_array()));
    }

    public function cron()
    {
        $this->db->query("DELETE FROM posts");
        $removed = 0;
        $items = 0;
        $i = -1;
        $subreddits = "";
        foreach ($this->config->item("subreddits_frontpage") as $item) {
            $i++;
            if ($i == 0) $subreddits = $item;
            else $subreddits .= "+$item";
        }
        foreach ($this->reddit->getLinksBySubreddit($subreddits) as $item) {
            $items++;
            $url = $item->getUrl();
            $ok = true;
            $alnumcheck = false;
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
                        var_dump(substr($url, -3));
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
            }

            if($alnumcheck && $ok) $ok = ctype_alnum($ex[1]);

            if ($ok) {
                var_dump($url);
                $post = array(
                    "title" => $item->getTitle(),
                    "upvotes" => $item->getUpvotes(),
                    "comments" => $item->countComments(),
                    "author" => $item->getAuthorName(),
                    "url" => $url
                );

                $this->db->query($this->db->insert_string("posts", $post));
            } else $removed++;
        }

        echo("<pre>Done. Removed $removed/$items</pre>");
    }
}