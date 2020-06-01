<?php
/**
 * Router
 * 
 * Route-handler for phpht
 * 
 * @package   phpht
 * @author    Trav Colbert <trav.colbert@gmail.com>
 */
Class Router {
  private $my_name = "Router";
  private $urlBase;
  private $routes;
  protected $phpht;

  function __construct($urlBase=null,$phpht) {
    syslog(LOG_INFO,$this->my_name.": Starting new PHPHT Router at URL base: '{$urlBase}'");
    $this->phpht = $phpht;
    if($urlBase===null){
      $basedir = dirname($_SERVER['SCRIPT_NAME']);
      $this->urlBase = ($basedir==='/') ? '' : $basedir;
    } else {
      $this->urlBase = $urlBase;
    }
    syslog(LOG_INFO,$this->my_name.": Final PHPHT Router URL base: '{$this->urlBase}'");
    $this->routes = array();
  }

  public function showRoutes() {
    foreach($this->routes["GET"] as $route) {
      echo $route[0]."<br>\n";
    }
  }

  public function getUrlBase() {
    return $this->urlBase;
  }

  public function getRoutes() {
    return $this->routes;
  }

  public function route() {
    $verb = $_SERVER['REQUEST_METHOD'];
    $path = $_SERVER['REQUEST_URI'];
    syslog(LOG_INFO,$this->my_name.": REQUEST_METHOD: ".$verb);
    syslog(LOG_INFO,$this->my_name.": REQUEST_URI: ".$path);
    syslog(LOG_INFO,$this->my_name.": QUERY_STRING: " . $_SERVER['QUERY_STRING']);
    if(strlen($this->urlBase)>0) {
      if(strpos($path,$this->urlBase)==0) $path = substr($path,strlen($this->urlBase));
    }
    syslog(LOG_INFO,"Resulting path: $path");
    $verb = strtoupper($verb);
    if(array_key_exists($verb,$this->routes)) {
      foreach($this->routes[$verb] as $route) {
        $isMatched = preg_match($route[0],$path,$matches);
        if($isMatched) {
          syslog(LOG_INFO,"Found a match: ".$matches[0]." (".$route[0].")");
          return $route[1]($matches);
        }
      }
    }
    syslog(LOG_INFO,$this->my_name.": Could not find route for {$verb} {$path}");
    return $this->phpht->view404($path);
  }

  public function registerRoute($verb,$pattern,$callback) {
    $verb = strtoupper($verb);
    if(!array_key_exists($verb,$this->routes)) {
      $this->routes[$verb] = array();
    }
    array_push($this->routes[$verb],array($pattern,$callback));
  }

  public function get($pattern,$callback) {
    $this->registerRoute("GET",$pattern,$callback);
  }

  public function post($pattern,$callback) {
    $this->registerRoute("POST",$pattern,$callback);
  }

  public function delete($pattern,$callback) {
    $this->registerRoute("DELETE",$pattern,$callback);
  }

  public function patch($pattern,$callback) {
    $this->registerRoute("PATCH",$pattern,$callback);
  }

  public function put($pattern,$callback) {
    $this->registerRoute("PUT",$pattern,$callback);
  }

  /**
   * sets the asset folder for static content.
   * Also, sets the Content-Type header for the resulting content 
   * 
   * @param   string  folder off of the root that holds static content
   * @access  public
   */
  public function assets($path="public") {
    syslog(LOG_INFO,$this->my_name.": Setting assets path to: {$path}");
    $regex = '/^\\/('.$path.')\\/(.*)/';
    $this->registerRoute("GET",$regex,function($matches) {
      $this->phpht->setContentType($matches);
      readFile($matches[1].'/'.$matches[2]);
    });
  }
}
