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
  private $urlBase;
  private $routes = [];

  function __construct($urlBase=null) {
    $this->urlBase = $urlBase ?? $this->getBaseDir();
    syslog(LOG_INFO,"Router URL base: '{$this->urlBase}'");
  }

  private function getBaseDir() {
    return (dirname($_SERVER['SCRIPT_NAME'])!=='/') ? dirname($_SERVER['SCRIPT_NAME']) : '';
  }

  private function executeRoutes($routes,$path) {
    foreach($routes as $r) {
      preg_match($r[0],$path,$matches);
      for($count=1;$count<count($r[1]);$count++) {
        syslog(LOG_INFO,"Invoking middleware: " . $r[1][$count] . " with " . count($matches) . " matches");
        $result = array($r[1][0],$r[1][$count])($matches);
        if(!$result) {
          syslog(LOG_INFO,"Middleware " . $r[1][$count] . " failed. Breaking chain");
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

  private function getQueryString() {
    $queryString = $_SERVER['QUERY_STRING'];
    syslog(LOG_INFO,"QUERY_STRING: " . $queryString);
    return $queryString;
  }

  private function getRequestUri() {
    $path = $_SERVER['REQUEST_URI'];
    syslog(LOG_ERR,"this->urlBase: '{$this->urlBase}'");
    if($this->urlBase && strpos($path,$this->urlBase)===0) $path = substr($path,strlen($this->urlBase));
    syslog(LOG_INFO,"REQUEST_URI: " . $path);
    return $path;
  }

  private function getVerb() {
    $verb = strtoupper($_SERVER['REQUEST_METHOD']);
    syslog(LOG_INFO,"REQUEST_METHOD: " . $verb);
    return $verb;
  }

  public function route() {
    $verb = $this->getVerb();
    $path = $this->getRequestUri();
    $queryString = $this->getQueryString();
    $routeList = $this->matchRoutes($verb,$path);
    if(!count($routeList)) $routeList = $this->matchRoutes("404",$path);
    $this->executeRoutes($routeList,$path);
  }

  public function registerRoute($verb,$pattern,$callback) {
    $verb = strtoupper($verb);
    if(!array_key_exists($verb,$this->routes)) $this->routes[$verb] = array();
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

  public function not_found($callback) {
    $this->registerRoute("404","/.*/",$callback);
  }

  public function err($callback) {
    $this->registerRoute("ERR","/.*/",$callback);
  }
}
