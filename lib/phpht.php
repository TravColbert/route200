<?php
Class Phpht {
  private $db = false;
  private $auth = false;
  public $router;
  protected $config;

  function __construct($config) {
    $this->config = $config;
    require_once("lib/router.php");
    $this->router = new Router();
    $this->appname = (isset($config["appname"])) ? $config["appname"] : "PHPHT - Unconfigured";
    $this->views = (isset($config["views"])) ? $config["views"] : "views";
    $this->assets = (isset($config["assets"])) ? $config["assets"] : "public";
    $this->home = (isset($config["home"])) ? $config["home"] : "home.php";
    $this->appurl = (isset($config["appurl"])) ? $config["appurl"] : "localhost.localhost";
    $this->config["baseurl"] = (isset($config["baseurl"])) ? $config["baseurl"] : dirname($_SERVER['SCRIPT_NAME']);
    $this->dbtype = (isset($config["dbtype"])) ? $config["dbtype"] : "sqlite";
    $this->dblocation = (isset($config["dblocation"])) ? $config["dblocation"] : "db/phpht.db";
    $this->db = new \PDO($this->dbtype.":".$this->dblocation);
    if($this->db) {
      $this->auth = new \Delight\Auth\Auth($this->db);
    }
  }

  public function asJSON($data) {
    header('Content-Type: application/json;charset=utf-8');
    if(isset($data["response_code"]) && $data["response_code"]) {
      http_response_code($data["response_code"]);
    }
    $jsonString = json_encode($data);
    echo $jsonString;
  }

  public function getConfig($var) {
    return $this->config[$var];
  }

  public function getFavicon($matches) {
    syslog(LOG_INFO,"Skipping favicon");
    return true;
  }

  public function getItemsJson($matches) {
    return $this->asJSON($this->getItems($matches));
  }

  private function getItems($matches) {
    global ${$matches[1]};
    // $id = (count($matches)>=3) ? $matches[2] : null;
    $resultArray = ${$matches[1]}->get();
    syslog(LOG_DEBUG,"Result count: ".count($resultArray['result']));
    $resultArray["errors"] = array();
    return $resultArray;
  }
  
  public function getModelHome($matches) {
    global ${$matches[1]};
    if(!is_a(${$matches[1]},"PHPHTModel")) return $this->view404();
    $reportName = ($matches[1]) ? $matches[1] : null;
    syslog(LOG_INFO,"getModelHome(): ".$_SERVER['QUERY_STRING']);
    return $this->view(null,array(
      "pageTitle" => $this->appname,
      "reportName" => $reportName,
      "reportQueryString" => $_SERVER['QUERY_STRING']
    ));
  }

  public function getVal($var) {
    syslog(LOG_INFO,"Returning: ".$this->$var);
    return $this->$var;
  }

  public function getDiag($matches) {
    echo "<pre>";
    if($this->db) {
      echo "DB connection client version: ".$this->db->getAttribute(PDO::ATTR_CLIENT_VERSION)."\n";
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

  public function goAuthCheck($userName = 'superuser') {
    syslog(LOG_INFO,"starting auth-check");
    try {
      syslog(LOG_INFO,"initial user registered?");
      $userId = $this->auth->register("su@".$this->appurl,"test123!","superuser",null);
      syslog(LOG_INFO,"initial user created");
      return $this->view("login.php");
    } catch (\Delight\Auth\UserAlreadyExistsException $e) {
      syslog(LOG_INFO,"initial user exists");
      return $this->view("home.php");
    } catch(\Delight\Auth\DatabaseError $e) {
      syslog(LOG_INFO,$e);
      return $this->view404();
    } catch (Error $e) {
      syslog(LOG_DEBUG,$e);
      return $this->view404();
    }
  }

  public function goLogin() {
    syslog(LOG_INFO,"starting auth-check");
    if(!$this->auth->isLoggedIn()) {
      syslog(LOG_INFO,"user not logged-in");
      $_SESSION["last_uri"] = $_SERVER["REQUEST_URI"];
      return $this->view("login.php",array("messages" => array("you must log in")));
    } else {
      syslog(LOG_INFO,"checking if last URL matches login page: ".preg_match('/\\/login\\/?$/',$_SESSION["last_uri"]));
      if(isset($_SESSION["last_uri"]) && preg_match('/\\/login\\/?$/',$_SESSION["last_uri"])===0) return header('Location: '.$_SESSION["last_uri"]);
      return $this->redirectTo($this->getConfig("baseurl"));
    }
  }

  public function goLogout() {
    syslog(LOG_INFO,"attempting logout...");
    try {
      $this->auth->logOutEverywhere();
      syslog(LOG_INFO,"logout complete");
      return $this->redirectTo($this->getConfig("baseurl"));
    }
    catch (\Delight\Auth\NotLoggedInException $e) {
      syslog(LOG_INFO,"logout complete");
      return $this->redirectTo($this->getConfig("baseurl"));
    }
  }

  public function goVerify($matches) {
    syslog(LOG_INFO,"attempting to verify registration");
    try {
      $auth->confirmEmail($_GET['selector'], $_GET['token']);
      echo 'Email address has been verified';
      return $this->redirectTo($this->getConfig("baseurl"));
    }
    catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
      die('Invalid token');
    }
    catch (\Delight\Auth\TokenExpiredException $e) {
        die('Token expired');
    }
    catch (\Delight\Auth\UserAlreadyExistsException $e) {
        die('Email address already exists');
    }
    catch (\Delight\Auth\TooManyRequestsException $e) {
        die('Too many requests');
    }
  }

  public function isLoggedIn() {
    return $this->auth->isLoggedIn();
  }

  public function postLogin($matches) {
    syslog(LOG_INFO,"attempting login");
    try {
      $this->auth->login($_POST["username"],$_POST["pass"]);
      syslog(LOG_INFO,"login success");
      if(isset($_SESSION["last_uri"])) header('Location: '.$_SESSION["last_uri"]);
      return $this->view();
    }
    catch (\Delight\Auth\InvalidEmailException $e) {
      return $this->view("login.php",array('errors' => array('Wrong email address')));
    }
    catch (\Delight\Auth\InvalidPasswordException $e) {
      return $this->view("login.php",array('errors' => array('Wrong password')));
    }
    catch (\Delight\Auth\EmailNotVerifiedException $e) {
      return $this->view("login.php",array('errors' => array('Email not verified')));
    }
    catch (\Delight\Auth\TooManyRequestsException $e) {
      return $this->view("login.php",array('errors' => array('Too many requests')));
    }
  }

  public function postRegister($matches) {
    syslog(LOG_INFO, "attempting to register user");
    try {
      $userId = $this->auth->register($_POST["email"],$_POST["pass"],null, function ($selector, $token) {
        $mj = new \Mailjet\Client($this->config["mailApiKey"],$this->config["mailSecretKey"],true,['version' => 'v3.1']);
        syslog(LOG_INFO, "user registered - user must be verified");
        syslog(LOG_INFO, "sending email verification");
        $verificationURL = 'https://froogle.io/verify?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
        $body = [
          'Messages' => [
            [
              'From' => [
                'Email' => "support@froogle.io",
                'Name' => "Froogle Support"
              ],
              'To' => [
                [
                  'Email' => $_POST["email"],
                  'Name' => "Trav"
                ]
              ],
              'Subject' => "Greetings from Froogle.io.",
              'TextPart' => "Hi ".$_POST['email'].", welcome to froogle.io! Please go to ".$verificationURL." to verify your account. Thanks!",
              'HTMLPart' => "<h3>Hi ".$_POST['email'].", welcome to <a href='https://froogle.io/'>froogle.io</a>!</h3><br />Click <a href='".$verificationURL."'>this link</a> to verify your account. Start managing your money like a true boss!",
              'CustomID' => "AppGettingStartedTest"
            ]
          ]
        ];
        $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
        if($response->success()) {
          $data["response_code"] = 201;
          $data["messages"][] = "User ".$_POST['email']." registered (but not verified)";
        } else {
          $data["response_code"] = 409;
          $data["errors"][] = "failed to register user";  
        }
        $this->view("registered.php",$data);
      });
    }
    catch (\Delight\Auth\InvalidEmailException $e) {
      die('Invalid email address');
    }
    catch (\Delight\Auth\InvalidPasswordException $e) {
        die('Invalid password');
    }
    catch (\Delight\Auth\UserAlreadyExistsException $e) {
        die('User already exists');
    }
    catch (\Delight\Auth\TooManyRequestsException $e) {
        die('Too many requests');
    }
  }

  public function redirectTo($url) {
    syslog(LOG_INFO,"Redirecting to: $url");
    header("Location: $url");
  }

  public function view($template=null,$data=[]) {
    // global $auth;
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
    include($this->views."/navbar.php");
    include($this->views."/".$template);
    include($this->views."/foot.php");
  }

  public function view404($page = "") {
    syslog(LOG_INFO, "page: ${page} not found");
    $data = array(
      'pageTitle' => "4 oh 4",
      'errors' => array("404 - page not found")
    );
    http_response_code(404);
    return $this->view("404.php",$data);
    exit;
  }

  public function viewHelp() {
    return $this->view("help.php");
  }

  public function viewInfo() {
    // global $router;
    syslog(LOG_INFO,"Showing server info phpinfo()");
    return $this->view("info.php");
  }

  public function viewRegister() {
    syslog(LOG_INFO,"Showing registration (sign-up) page");
    return $this->view("register.php");
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
