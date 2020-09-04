<?php
/**
 * Route200
 * 
 * A simple route-handler for PHP
 * 
 * @package   route200
 * @author    Trav Colbert <trav.colbert@gmail.com>
 */
Class Router {
  private $my_name = "Route200";
  private $urlBase;
  private $routes = [];

  function __construct($urlBase=null) {
    $this->urlBase = $urlBase ?? $this->getBaseDir();
    syslog(LOG_INFO,"Final router URL base: '{$this->urlBase}'");
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
        syslog(LOG_INFO,"Invoking route handler: " . $r[1][$count]);
        $result = array($r[1][0],$r[1][$count])($matches);
        if(!$result) {
          syslog(LOG_INFO,"Middleware in chain has failed. Breaking chain");
          break;
        }
      }
    }
  }

  protected function matchRoutes($verb,$path) {
    syslog(LOG_INFO,"Compiling eligible routes for " . $verb . " " . $path);
    if(!array_key_exists($verb,$this->routes)) return false;
    $eligibleRoutes = array_filter($this->routes[$verb],function($route) use ($path) {
      return preg_match($route[0],$path,$matches);
    });
    syslog(LOG_INFO,"Found " . count($eligibleRoutes) . " eligible routes");
    return $eligibleRoutes;
  }

  public function getRoutes() {
    return $this->routes;
  }

  public function route() {
    $verb = strtoupper($_SERVER['REQUEST_METHOD']);
    $path = $_SERVER['REQUEST_URI'];
    syslog(LOG_INFO,"REQUEST_METHOD: " . $verb);
    syslog(LOG_INFO,"REQUEST_URI: " . $path);
    syslog(LOG_INFO,"QUERY_STRING: " . $_SERVER['QUERY_STRING']);
    if(strpos($path,$this->urlBase)==0) $path = substr($path,strlen($this->urlBase));
    syslog(LOG_INFO,"Resulting path: $path");
    $routeList = $this->matchRoutes($verb,$path);
    if(!count($routeList)) $routeList = $this->matchRoutes("ERR",$path);
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

  public function err($callback) {
    $this->registerRoute("ERR","/.*/",$callback);
  }

  /**
   * sets the asset folder for static content.
   * Also, sets the Content-Type header for the resulting content 
   * 
   * @param   string  folder off of the root that holds static content
   * @access  public
   */
  public function assets($path="public") {
    syslog(LOG_INFO,"Setting assets path to: {$path}");
    $regex = '/^\\/('.$path.')\\/(.*)/';
    $this->registerRoute("GET",$regex,function($matches) {
      $this->phpht->setContentType($matches);
      readFile($matches[1].'/'.$matches[2]);
    });
  }
}
