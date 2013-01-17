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

    public function index() {
        $this->parser->parse("template", array());
    }
}