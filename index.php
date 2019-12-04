<?php
$config = parse_ini_file("./config/config.ini");
openlog((isset($config["appname"])) ? $config["appname"] : "phpht", LOG_PID, LOG_SYSLOG);

require __DIR__ . '/vendor/autoload.php';

require_once("lib/phpht.php");
$phpht = new Phpht($config);

// Setup what folder your static assets are pulled from (e.g. 'public')
$phpht->router->assets();

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
 * Routes are defined with the $phpht->router object.
 * 
 * The format of a route definition is:
 * 
 * $phpht->router->verb(path_regex, function);
 * 
 * like:
 * 
 * e.g.: $phpht->router->get("/^\\/_info\\/?/","showServerInfo");
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
 * $phpht->router->get("/^\\/_info\\/?/","showServerInfo");
 * 
 * The regex just catches: '/_info/'.
 * 
 * There is another form to the 'function' parameter where an array is
 * given like this:
 * 
 * $phpht->router->get("/^\\/_info\\/?/",array("phpht","showServerInfo"))
 * 
 * In this case, the first element of the array is an object and the 
 * second element is a method in the object. We often use this format 
 * when using PHPHT's internal methods.
 */
$phpht->router->get("/^\\/favicon\.ico/",array($phpht,"getFavicon"));
$phpht->router->get("/^\\/_info\\/?/",array($phpht,"viewInfo"));
$phpht->router->get("/^\\/(_diag)(\\/.+)*\\//",array($phpht,"viewDiag"));
$phpht->router->get("/^\\/404\\/?/",array($phpht,"view404"));
$phpht->router->get("/^\\/login\\/?/",array($phpht,"goLogin"));
$phpht->router->get("/^\\/logout\\/?/",array($phpht,"goLogout"));
$phpht->router->get("/^\\/register\\/?/",array($phpht,"viewRegister"));
$phpht->router->get("/^\\/verify\\/?/",array($phpht,"goVerify"));
$phpht->router->get("/^\\/settings\\/?/",array($phpht,"goSettings"));

/**
 * These are generic routes that work with basic, non-compound objects
 * As soon as you define them and create the model, they should Just Work
 */
$phpht->router->get("/^\\/([^\\/]+)\\/new\\/?/","setupNewObject");
$phpht->router->get("/^\\/([^\\/]+)\\/([0-9]+)\\/edit\\/?/","toForm");
$phpht->router->get("/^\\/([^\\/]+)\\/([0-9]+)\\/?/",array($phpht,"getItem"));
$phpht->router->get("/^\\/([^\\/]+)\\/(json)\\/?/",array($phpht,"getItemsJson"));
$phpht->router->get("/^\\/([^\\/]+)\\/?/",array($phpht,"getModelHome"));

$phpht->router->post("/^\\/register\\/?/",array($phpht,"postRegister"));
$phpht->router->post("/^\\/login\\/?/",array($phpht,"postLogin"));
$phpht->router->post("/^\\/([^\\/]+)\\/([0-9]+)\\/?/","edit");
$phpht->router->post("/^\\/([^\\/]+)\\/?$/","add");

$phpht->router->delete("/^\\/([^\\/]+)\\/([0-9]+)\\/?/","delete");

/**
 * THE ROOT ROUTE
 * 
 * This defines the root route: the front door to your app.
 */
$phpht->router->get("/\\/?/",function($matches) use ($phpht) {
  $phpht->view($phpht->getVal("home"),$matches);
});

/**
 * Find and run the route!
 */
$phpht->router->route();
