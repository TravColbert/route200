<?php
Class Phpht {
  private $auth;

  function __construct($config) {
    $this->appname = (isset($config["appname"])) ? $config["appname"] : "PHPHT - Unconfigured";
    $this->views = (isset($config["views"])) ? $config["views"] : "views";
    $this->assets = (isset($config["assets"])) ? $config["assets"] : "assets";
    $this->home = (isset($config["home"])) ? $config["home"] : "home.php";
    $this->baseurl = (isset($config["baseurl"])) ? $config["baseurl"] : "";
    $this->db = $this->buildDB($config);
    if($this->db) $this->auth = new \Delight\Auth\Auth($db);
  }

  private function buildDB($config) {
    $dbtype = (isset($config["dbtype"])) ? $config["dbtype"] : "sqlite";
    $dblocation = (isset($config["dblocation"])) ? $config["dblocation"] : "db/phpht.db";
    if(!file_exists($dblocation)) return null;
    return new \PDO("${dbtype}:${dblocation}");
  }

  public function importModule($moduleClass,$moduleName) {
    $this->modules[$moduleName] = $moduleClass;
  }

  public function asJSON($data) {
    header('Content-Type: application/json;charset=utf-8');
    if(isset($data["response_code"]) && $data["response_code"]) {
      http_response_code($data["response_code"]);
    }
    $jsonString = json_encode($data);
    // syslog(LOG_INFO,"JSON STRING: ".$jsonString);
    echo $jsonString;
  }

  public function view($template=null,$data=[]) {
    global $auth;
    $template = (isset($template)) ? $template : $this->home;
    if(!file_exists($this->views."/".$template)) {
      return $this->view404($this->views."/".$template);
    }
    $pageTitle = $this->appname;
    if($data) {
      if(array_key_exists("pageTitle",$data)) $pageTitle = $data['pageTitle'];
    }
    $data["appname"] = $this->appname;
    include($this->views."/head.php");
    if(file_exists($this->views."/navbar.php")) include($this->views."/navbar.php");
    include($this->views."/".$template);
    include($this->views."/foot.php");
  }

  public function view404($page) {
    syslog(LOG_INFO, "page: ${page} not found");
    $data = array(
      'pageTitle' => "4 oh 4",
      'errors' => array("404 - page not found")
    );
    http_response_code(404);
    return $this->view("404.php",$data);
    exit;
  }

  public function viewDiag($matches) {
    global $db;
    echo "<pre>";
    if($db) {
      echo "DB connection client version: ".$db->getAttribute(PDO::ATTR_CLIENT_VERSION)."\n";
      // $connectionStatus = ($db->getAttribute(PDO::ATTR_CONNECTION_STATUS)) ? "true" : "false";
      // echo "DB connection status: {$connectionStatus}\n";
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

  public function viewHelp() {
    return $this->view("help.php");
  }

  public function viewInfo() {
    global $router;
    syslog(LOG_INFO,"Showing server info phpinfo()");
    return $this->view("info.php");
  }

  public function redirectTo($url) {
    syslog(LOG_INFO,"Redirecting to: $url");
    header("Location: $url");
  }
  
  public function getFavicon($matches) {
    syslog(LOG_INFO,"Skipping favicon");
    return true;
  }

  public function getModelHome($matches) {
    global ${$matches[1]};
    if(!is_a(${$matches[1]},"PHPHTModel")) return $this->view404();
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

function setMessages($type,$data) {
  echo "<div id=\"${type}box\" class='messagebox $type vertical'>";
  if(isset($data[$type])) {
    foreach($data[$type] as $msg) {
      echo "<div class='msg'>".$msg."</div>";
    }
  }  
  echo "</div>";
}

function toJSON($matches) {
  global ${$matches[1]};
  syslog(LOG_DEBUG,"Converting ".$matches[1]." to JSON");
  $id = (count($matches)>=3) ? $matches[2] : null;
  echo ${$matches[1]}->toJSON($id);
  return;
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
