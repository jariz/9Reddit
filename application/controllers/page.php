<?

class page extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->database();

        $this->load->helper(array("url", "file"));
        $this->load->library(array("parser", "reddit", "ninereddit"));
        $this->load->library("reddit_oauth", array("oauth_id" => $this->config->item("oauth_id"), "oauth_secret" => $this->config->item("oauth_secret"), "redirect_url" => $this->config->item("redirect_url"), "scope" => $this->config->item("scope")));
        $this->load->driver("session");

        //$this->output->enable_profiler(true);
    }

    public function auth()
    {
        if (($a = $this->reddit_oauth->Auth()) != false) {
            $this->session->set_userdata("auth", $a);

            $subs = $this->reddit_oauth->getMySubreddits();
            $z = -1;
            $subz = "";
            foreach ($subs as $sub) {
                $z++;
                $name = (substr($sub->get('url'), 3, strlen($sub->get('url')) - 4));
                if ($z == 0) $subs = $name;
                else $subz .= "+" . $name;
            }

            $this->db->query($this->db->insert_string("subs", array("sid" => $this->session->userdata('session_id'), "subs" => $subz)));
            $this->session->set_userdata("modhash", $this->reddit_oauth->getModHash());
            redirect(base_url());
        }
    }

    function initCache()
    {
        $this->output->cache(10);
    }

    public function index()
    {
        //$this->initCache();
        if ($this->session->userdata("auth") == false)
            $this->renderQuery($this->db->query("SELECT * FROM old_posts"));
        else {
            $this->reddit_oauth->setAccessToken($this->session->userdata("auth"));
            $qq = $this->db->query("SELECT subs FROM subs WHERE sid = \"{$this->session->userdata('session_id')}\"");
            if ($qq->num_rows() > 0) {
                $subz = $qq->row()->subs;
                if (($links = $this->reddit_oauth->getSubreddit($subz)) != false) {
                    $this->authenticated = true;
                    $this->reddit_oauth->setModHash($this->session->userdata("modhash"));
                    $this->renderDynamic($this->ninereddit->filter($links));
                } else $this->renderQuery($this->db->query("SELECT * FROM old_posts"));
            } else {
                $this->renderQuery($this->db->query("SELECT * FROM old_posts"));
            }
        }
    }

    private $authenticated = false;

    public function newposts()
    {
        $this->initCache();
        $this->renderQuery($this->db->query("SELECT * FROM posts"));
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    function initAuth()
    {
        if ($this->session->userdata("auth") != false) {
            $this->reddit_oauth->setAccessToken($this->session->userdata("auth"));
            $this->reddit_oauth->setModHash($this->session->userdata("modhash"));
        }
    }

    public function vote()
    {
        $dir = $this->uri->segment(2);
        $thing = $this->uri->segment(3);
        if (is_numeric($dir) && substr($thing, 0, 3) == "t3_" && ctype_alnum(substr($thing, 3))) {
            $this->initAuth();
            $this->output->set_output(json_encode(array("result" => $this->reddit_oauth->vote($thing, $dir)))); //->set_content_type("json");
        }
    }

    public function subreddit($subreddit)
    {
        $this->initCache();
        try {
            $this->renderDynamic($this->ninereddit->filter($this->reddit->getLinksBySubreddit($subreddit)));
        } catch (Exception $ex) {
            if ($ex->getCode() == \RedditApiClient\RedditException::NO_SUCH_SUBREDDIT) {
                show_404();
            } else {
                show_error("We were unable to contact reddit, try again later", 500, $ex->getMessage());
            }
        }
    }

    public function user($user)
    {
        $this->initCache();
        try {
            $this->renderDynamic($this->ninereddit->filter($this->reddit->getLinksByUsername($user)));
        } catch (Exception $ex) {
            show_error("We were unable to contact reddit, try again later", 500, $ex->getMessage());
        }
    }

    function renderDynamic($posts)
    {
        $this->render($posts);
    }

    function renderQuery($query)
    {
        $this->render($query->result_array());
    }

    function render($posts)
    {
        if (count($posts) == 0) $noposts = "<h3>No posts found. Challenge accepted XD</h3>";
        else $noposts = "";

        if ($this->authenticated) $me = $this->reddit_oauth->getMe();
        $x = 0;
        $classes = $this->config->item("buttons");
        $count = count($classes) - 1;
        $buttons = array();
        $var = $this->config->item("recommendations");
        shuffle($var);
        foreach ($var as $button) {
            $x++;
            if ($x == 10) break;
            $buttons[] = array("name" => $button, "class" => $classes[rand(0, $count)]);
        }
        $this->parser->parse("template", array("post" => $posts, "noposts" => $noposts, "typeitem" => $this->buildTypes(), "auth" => (int)$this->authenticated, "authorized" => $this->authenticated ? $me : array(), "notauthorized" => !$this->authenticated ? array(array()) : array(), "button" => $buttons));
    }

    function buildTypes()
    {
        $types = array();

        if (!$this->authenticated)
            foreach ($this->config->item("types") as $type) {
                if (uri_string() != $type["url"]) {
                    $types[] = $type;
                } else {
                    $type["active"] = " class=\"active\"";
                    $types[] = $type;
                }
            }

        return $types;
    }

    public function cron()
    {
        if (!in_array(@$_SERVER["REMOTE_ADDR"], $this->config->item("cron_allowed_ips"))) {
            show_error("You're not supposed to be here little boy");
        }

        $this->output->enable_profiler(TRUE);

        if (date("G") == "0") {
            //it's midnight, move all current posts to yesterday
            $this->db->query("DELETE FROM old_posts");
            foreach ($this->db->query("SELECT * FROM posts")->result_array() as $item) {
                $this->db->query($this->db->insert_string("old_posts", $item));
            }
        }
        $this->db->query("DELETE FROM posts");
        $i = -1;
        $subreddits = "";
        foreach ($this->config->item("subreddits_frontpage") as $item) {
            $i++;
            if ($i == 0) $subreddits = $item;
            else $subreddits .= "+$item";
        }

        foreach ($this->ninereddit->filter($this->reddit->getLinksBySubreddit($subreddits)) as $post) {
            $this->db->query($this->db->insert_string("posts", $post));
        }
    }
}