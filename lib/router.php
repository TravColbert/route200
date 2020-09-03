<?php
/**
 * Router101
 * 
 * A simple route-handler for PHP
 * 
 * @package   router101
 * @author    Trav Colbert <trav.colbert@gmail.com>
 */
Class Router {
  private $my_name = "Router";
  private $urlBase;
  private $routes = [];
  public $output = '';

  function __construct($urlBase=null) {
    syslog(LOG_INFO,$this->my_name.": Starting new router at URL base: '{$urlBase}'");
    $this->urlBase = $urlBase ?? $this->getBaseDir();
    syslog(LOG_INFO,$this->my_name.": Final router URL base: '{$this->urlBase}'");
  }

  public function showRoutes() {
    foreach($this->routes["GET"] as $route) {
      echo $route[0]."<br>\n";
    }
  }

  private function getBaseDir() {
    return (dirname($_SERVER['SCRIPT_NAME'])!=='/') ? dirname($_SERVER['SCRIPT_NAME']) : '';
  }

  public function getUrlBase() {
    return $this->urlBase;
  }

  private function executeRoutes($routes,$path) {
    foreach($routes as $r) {
      for($count=1;$count<count($r[1]);$count++) {
        preg_match($r[0],$path,$matches);
        $func = array($r[1][0],$r[1][$count]);
        $func($matches);
      }
    }
  }

  protected function matchRoutes($verb,$path) {
    syslog(LOG_INFO,"matching path: $path");
    if(!array_key_exists($verb,$this->routes)) return false;
    $eligibleRoutes = array_filter($this->routes[$verb],function($route) use ($path) {
      return preg_match($route[0],$path,$matches);
    });
    return $eligibleRoutes;
  }

  public function getRoutes() {
    return $this->routes;
  }

  public function route() {
    $verb = strtoupper($_SERVER['REQUEST_METHOD']);
    $path = $_SERVER['REQUEST_URI'];
    syslog(LOG_INFO,$this->my_name.": REQUEST_METHOD: ".$verb);
    syslog(LOG_INFO,$this->my_name.": REQUEST_URI: ".$path);
    syslog(LOG_INFO,$this->my_name.": QUERY_STRING: " . $_SERVER['QUERY_STRING']);
    if(strlen($this->urlBase)>0) {
      if(strpos($path,$this->urlBase)==0) $path = substr($path,strlen($this->urlBase));
    }
    syslog(LOG_INFO,"Resulting path: $path");
    $routeList = $this->matchRoutes($verb,$path);
    $this->executeRoutes($routeList,$path);
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
