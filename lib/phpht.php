<?php
Class Phpht {

  function __construct($config) {
    $this->appname = (isset($config["appname"])) ? $config["appname"] : "PHPHT - Unconfigured";
    $this->views = (isset($config["views"])) ? $config["views"] : "views";
    $this->assets = (isset($config["assets"])) ? $config["assets"] : "assets";
    $this->home = (isset($config["home"])) ? $config["home"] : "homepage.php";
  }

  public function importModule($moduleClass,$moduleName) {
    $this->modules[$moduleName] = $moduleClass;
  }
  
  public function showServerInfo() {
    global $router;
    syslog(LOG_INFO,"Showing server info phpinfo()");
    phpinfo();
    echo "<div class='center'>";
    echo "<hr>";
    echo "<h1>PHPHT INFO</h1>";
    echo "<table><tbody><tr>";
    echo "<td class='e'>URL_BASE</td><td class='v'>".$router->getUrlBase()."</td>";
    echo "<tr></tbody></table>";
    echo "<h2>Routes</h2>";
    echo "<table><tbody>";
    foreach($router->getRoutes() as $routeVerb=>$routeVerbRoutes) {
      echo "<tr>";
      echo "<td class='e'>".$routeVerb."</td>";
      echo "<td class='v'>";
      foreach($routeVerbRoutes as $route) {
        echo $route[0]."<br>";
      }
      echo "</td></tr>";
    }
    echo "</tbody></table>";
    echo "</div>";
  }

  public function diag($matches) {
    global $db;
    echo "<pre>";
    if($db) {
      echo "DB connection client version: ".$db->getAttribute(PDO::ATTR_CLIENT_VERSION)."\n";
      $connectionStatus = ($db->getAttribute(PDO::ATTR_CONNECTION_STATUS)) ? "true" : "false";
      echo "DB connection status: {$connectionStatus}\n";
    }
    echo "URL Handler-checker\n";
    echo "Matches:\n";
    if($matches[2]) {
      $path = explode("/",$matches[2]);
      array_shift($path);
      array_splice($matches, 2, 1, $path);
    }
    for($c=0;$c<count($matches);$c++) {
      echo "Match ".$c." => ".$matches[$c]."\n";
    }
    echo "Query String Processing:\n";
    var_dump($_GET);
    var_dump($_SERVER['QUERY_STRING']);
    echo "</pre>";
  }
  
  public function showHelp() {
    $this->view("help.php");
  }
  
  public function show404() {
    $data = array(
      'pageTitle' => "4 oh 4",
      'error' => "404 - page not found"
    );
    http_response_code(404);
    $this->view("404.php",$data);
    exit;
  }

  public function view($template=null,$data=null) {
    $template = (isset($template)) ? $template : $this->home;
    if(file_exists($this->views."/".$template)) {
      $pageTitle = $this->appname;
      if($data) {
        if(array_key_exists("pageTitle",$data)) $pageTitle = $data['pageTitle'];
        if(array_key_exists("error",$data)) $error = $data['error'];
      }
      include($this->views."/head.php");
      include($this->views."/".$template);
      include($this->views."/foot.php");
    } else {
      $this->show404();
    }
  }
  
  public function getModelHome($matches) {
    global ${$matches[1]};
    if(!is_a(${$matches[1]},"PHPHTModel")) return $this->show404();
    $reportName = ($matches[1]) ? $matches[1] : null;
    syslog(LOG_INFO,"getModelHome(): ".$_SERVER['QUERY_STRING']);
    $this->view(null,array(
      "pageTitle" => $this->appname,
      "reportName" => $reportName,
      "reportQueryString" => $_SERVER['QUERY_STRING']
    ));
  }  
}

function viewAsJSON($objType,$objs,$errors) {
  header('Content-Type: application/json');
  if($errors) {
    $returnObj = json_encode(array(
      $objType => array(),
      "errors" => $errors
    ));
  } else {
    $returnObj = json_encode(array(
      $objType => $objs->toArray(),
      "errors" => array(),
    ));
  }
  echo $returnObj;
}

function asJSON($data) {
  // var_dump($data);
  header('Content-Type: application/json');
  $jsonString = json_encode($data);
  // var_dump($jsonString);
  // syslog(LOG_DEBUG,"JSON string: ".$jsonString." length: ".strlen($jsonString));
  return $jsonString;
}

function toJSON($matches) {
  global ${$matches[1]};
  syslog(LOG_DEBUG,"Converting ".$matches[1]." to JSON");
  $id = (count($matches)>=3) ? $matches[2] : null;
  echo ${$matches[1]}->toJSON($id);
  return;
}

function logout() {
  global $phpht;
  $phpht->logout($_SERVER);
}

function setupNewObject($matches) {
  global $phpht;
  //if(!$phpht->checkAuthentication()) exit;
  $pageTitle = "New Object";
  view("new-$matches[1].php");
  return;
}

// function listItems($matches) {
//   global ${$matches[1]};
//   //echo "Listing items for: ".$matches[1]."<br>\n";
//   $id = (count($matches)>=3) ? $matches[2] : null;
//   $count = ${$matches[1]}->collect($id);
//   //echo "Collected ".$count->count()." objects<br>\n";
//   viewAsJSON($matches[1],${$matches[1]},null);
//   return;
// }

function getModel($matches) {
  global ${$matches[1]};
  $id = (count($matches)>=3) ? $matches[2] : null;
  $resultArray = ${$matches[1]}->get();
  // syslog(LOG_DEBUG,"ResultArray: ".join(',',$resultArray['result']));
  syslog(LOG_DEBUG,"Result count: ".count($resultArray['result']));
  $resultArray["errors"] = array();
  return $resultArray;
}

/**
 * EXPORT FUNCTIONS
 */
function getExporterObject($matches) {
  $resultArray = getModel($matches);
  require_once('lib/excelexport.php');
  $exporter = new ExcelExport($resultArray['result']);
  // Might be nice to put a heading row in the data...
  $exporter->insertHeaderRow();
  $exporter->toExcel();
  return $exporter;
};

function exportToSpreadsheet($matches) {
  syslog(LOG_INFO,"Exporting to spreadsheet");
  $exporter = getExporterObject($matches);
  $exporter->downloadExcel();
  return;
}

function exportToCSV($matches) {
  syslog(LOG_INFO,"Exporting to CSV");
  $exporter = getExporterObject($matches);
  $exporter->downloadCSV();
  return;
}

function exportToJSON($matches) {
  $modelData = getModel($matches);
  // var_dump($modelData);
  // return;
  $jsonResult = asJSON($modelData);
  // syslog(LOG_DEBUG,"JSON: ".$jsonResult." length: ".strlen($jsonResult));
  echo $jsonResult;
  return;
}

function toForm($matches) {
  global ${$matches[1]};
  echo ${$matches[1]}->toForm($matches[2]);
  return;
}

function edit($matches) {
  global ${$matches[1]};
  if(${$matches[1]}->edit($_POST)) toJSON($matches);
  return;
}

function add($matches) {
  global ${$matches[1]};
  if($id = ${$matches[1]}->add($_POST)) {
    syslog(LOG_DEBUG,"added record ID: ".$id);
    listItems(array(null,$matches[1],$id));
  } else {
    viewAsJSON("transactions",null,"Could not add record");
  }
  return;
}

function delete($matches) {
  global ${$matches[1]};
  if(${$matches[1]}->delete(${$matches[2]})) {
    log("record ".${$matches[2]}." deleted");
    listItems(array(null,$matches[1],$id));
  } else {
    viewAsJSON("transactions",null,"Could not delete record");
  }
  return;
}
