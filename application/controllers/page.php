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
    }

    public function auth() {
        if(($a = $this->reddit_oauth->Auth()) != false) {
            $this->session->set_userdata("auth", $a);
            redirect(base_url());
        }
    }

    public function index()
    {
        if($this->session->userdata("auth") == false)
            $this->renderQuery($this->db->query("SELECT * FROM old_posts"));
        else {
            $this->reddit_oauth->setAccessToken($this->session->userdata("auth"));
            if(($links = $this->reddit_oauth->getFrontPage()) != false) {
                $this->authenticated = true;
                $this->renderDynamic($this->ninereddit->filter($this->reddit_oauth->getFrontPage($links)));
            } else {
                $this->renderQuery($this->db->query("SELECT * FROM old_posts"));
            }
        }
    }
    private $authenticated = false;

    public function newposts()
    {
        $this->renderQuery($this->db->query("SELECT * FROM posts"));
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    public function subreddit($subreddit) {
        try {
            $this->renderDynamic($this->ninereddit->filter($this->reddit->getLinksBySubreddit($subreddit)));
        } catch(\RedditApiClient\RedditException $ex) {
            if(\RedditApiClient\RedditException::NO_SUCH_SUBREDDIT) {
                show_404();
            } else {
                show_error("We were unable to contact reddit, try again later", 500, $ex->getMessage());
            }
        }
    }

    public function user($user) {
        try {
            $this->renderDynamic($this->ninereddit->filter($this->reddit->getLinksByUsername($user)));
        } catch(Exception $ex) {
            show_error("We were unable to contact reddit, try again later", 500, $ex->getMessage());
        }
    }

    function renderDynamic($posts) {
        $this->render($posts);
    }

    function renderQuery($query) {
        $this->render($query->result_array());
    }

    function render($posts) {
        if(count($posts) == 0) $noposts = "<h3>No posts found. Challenge accepted XD</h3>";
        else $noposts = "";

        if($this->authenticated) $me = $this->reddit_oauth->getMe();

        $this->parser->parse("template", array("post" => $posts, "noposts" => $noposts, "typeitem" => $this->buildTypes(), "authorized" => $this->authenticated ? $me : array(), "notauthorized" => !$this->authenticated ? array(array()) : array()));
    }

    function buildTypes() {
        $types = array();

        if(!$this->authenticated)
            foreach($this->config->item("types") as $type) {
                if(uri_string() != $type["url"]) {
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
        $this->output->enable_profiler(TRUE);

        if(!in_array(@$_SERVER["REMOTE_ADDR"], $this->config->item("cron_allowed_ips"))) {
            show_error("You're not supposed to be here little boy");
        }

        if(date("G:s") == "0:00") {
            //it's midnight, move all current posts to yesterday
            $this->db->query("DELETE FROM old_posts");
            foreach($this->db->query("SELECT * FROM posts")->result_array() as $item) {
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

        foreach($this->ninereddit->filter($this->reddit->getLinksBySubreddit($subreddits)) as $post) {
            $this->db->query($this->db->insert_string("posts", $post));
        }
    }
}