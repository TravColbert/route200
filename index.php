<?php
$config = parse_ini_file("./config/config.ini");
openlog((isset($config["appname"])) ? $config["appname"] : "phpht", LOG_PID, LOG_SYSLOG);

// Set up PHPHT first:
require_once("lib/phpht.php");
$phpht = new Phpht($config);
require_once("lib/Router.php");
$router = new Router();

// Setup what folder your static assets are pulled from (e.g. 'public')
$router->assets();

/**
 * Custom functions can go here
 * 
 */

/**
 * Require and add your models here.
 * 
 * Something like this works:
 * 
require_once("lib/My_Model.php");
 *
 * Now you can instantiate your model something like this:
 * 
$my_model = new My_Model();
 *
 * Now my my_model is available to be used in routes.
 * By default if you do this: my_app_root/my_model/
 * phpht will try to run a ->getModel() function on that
 * model if it exists.
 require_once("lib/Report.php");
 */

/**
 * == DEFINING ROUTES ==
 * Once, all your model objects are created we can start defining
 * the routes that tie URL requests to PHPHT to methods.
 * 
 * Routes are defined with the $router object created above.
 * 
 * The format of a route definition is:
 * 
 * $router->verb(path_regex, function);
 * 
 * like:
 * 
 * e.g.: $router->get("/^\\/_info\\/?/","showServerInfo");
 * 
 * where:
 * 
 * - verb : GET, POST, PUSH, DELETE
 * - path_regex : a regex that, when matched, triggers the running.
 *                of the supplied function. The path_regex might also 
 *                build a $matches array that contains all the parts
 *                of the URI. The $matches array gets passed to the 
 *                function. This allows the function to act on the 
 *                specifics of the URI call.
 *                You want to define more specific paths at the top
 *                and more general paths near the bottom.
 * - function : the function that is called when the URI matches
 *              the path_regex.
 *              The function takes a $matches array as an argument.
 * 
 * notes:
 * 
 * The path_regex does not include the root elements of the URI. So,
 * in GET https://my.server/myapp/_info/ (from above), the 
 * 'my.server/myapp' part of the URI does not need to be defined in 
 * the regex. The regex would look like this:
 * 
 * $router->get("/^\\/_info\\/?/","showServerInfo");
 * 
 * The regex just catches: '/_info/'.
 * 
 * There is another form to the 'function' parameter where an array is
 * given like this:
 * 
 * $router->get("/^\\/_info\\/?/",array("phpht","showServerInfo"))
 * 
 * In this case, the first element of the array is an object and the 
 * second element is the method in the object. We often use this format 
 * when using PHPHT's internal methods.
 */
$router->get("/^\\/favicon\.ico/",function ($matches) {
  syslog(LOG_INFO,"Skipping favicon");
});
$router->get("/^\\/_info\\/?/",array("phpht","showServerInfo"));
$router->get("/^\\/(_diag)(\\/.+)*\\//",array("phpht","diag"));

/**
 * If you have authentication and sessions set up you can use these
 * routes:
 *
$router->get("/^\\/login\\/?/","login");
$router->get("/^\\/logout\\/?/","logout");
 */

/**
 * The default 404-handler route
 */
$router->get("/^\\/404\\/?/",array("phpht","show404"));

/**
 * These are generic routes that work with basic, non-compound objects
 * As soon as you define them and create the model, they should Just Work
 */
$router->get("/^\\/([^\\/]+)\\/new\\/?/","setupNewObject");
$router->get("/^\\/([^\\/]+)\\/([0-9]+)\\/edit\\/?/","toForm");
$router->get("/^\\/([^\\/]+)\\/([0-9]+)\\/?/","listItems");
$router->get("/^\\/([^\\/]+)\\/(json)\\/?/","exportToJSON");
$router->get("/^\\/([^\\/]+)\\/?/",array("phpht","getModelHome"));

$router->post("/^\\/([^\\/]+)\\/([0-9]+)\\/?/","edit");
$router->post("/^\\/([^\\/]+)\\/?$/","add");

$router->delete("/^\\/([^\\/]+)\\/([0-9]+)\\/?/","delete");

/**
 * THE ROOT ROUTE
 * 
 * This defines the root route: the front door to your app.
 */
$router->get("/\\/?/",function($matches) {
  global $phpht;
  $phpht->view("homepage.php",$matches);
});

/**
 * Find and run the route!
 */
$router->route();
