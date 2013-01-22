<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = 'page';
$route['404_override'] = '';

$route["r/(:any)"] = "page/subreddit/$1";
$route["u/(:any)"] = "page/user/$1";
$route["new"] = "page/newposts";
$route["auth"] = "page/auth";
$route["logout"] = "page/logout";

$route["vote/(:any)/(:any)"] = "page/vote";

/* End of file routes.php */
/* Location: ./application/config/routes.php */