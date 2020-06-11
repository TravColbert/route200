<?php
Class PHPHT_Model {
  protected $phpht;
  protected $auth;
  protected $my_name = "PHPHT Model";
  protected $objectRegistry = array();
  protected $query_string;
  protected $view = "model.php";

  function __construct($phpht) {
    global $auth;
    $this->phpht = $phpht;
    $this->auth = $auth;
    $this->createBaseObjectRegistry();
  }

  protected function createBaseObjectRegistry() {
    $this->objectRegistry["domain"] = array(
      "resourceType" => "domains",
      "resourceId" => "domainId",
      "resourceNameField" => "name",
      "create" => "INSERT INTO domains VALUES (null, :name, :description, null, :currentDate, '', :userId)"
    );
    return;
  }

  public function deleteDelete($matches) {
    $userId = $this->phpht->checkAuth();
    if(!isset($userId)) return;
    $className = strtolower(get_class($this));
    $registryObject = $this->getRegistryObject($className);
    $values["userId"] = $userId;
    $values["currentDomainId"] = $this->phpht->getCurrentDomain($userId);
    $values["currentDate"] = $this->phpht->getDateTime()->format('Y-m-d H:i:s');
    $values[$registryObject["resourceId"]] = $matches[2];
    $sql = $registryObject["delete"];
    $sqlstmt = $this->phpht->dbPrepare($sql);
    if($sqlstmt) {
      syslog(LOG_INFO,"sqlstmt created");
      $data = $this->delete($sqlstmt,$values,$className);
    } else {
      syslog(LOG_INFO,"no sqlstmt found");
      $data["response_code"] = 400;
      $data["errors"][] = "failed to delete {$objectType} for \"{$values[$registryObject["resourceNameField"]]}\"";
      $data["errors"][] = "(statement error)";
    }
    return $data;
  }

  public function get($query = 'readAll') {
    $userId = $this->phpht->checkAuth();
    if(!isset($userId)) return;
    $className = strtolower(get_class($this));
    syslog(LOG_INFO,"attempting to get (read) of ".$className);
    $registryObject = $this->getRegistryObject($className);
    $queryValues = $this->getGlobalValues($userId);
    $sql = $registryObject[$query][0];
    $queryValues = $this->getQueryValues($queryValues,$registryObject["readAll"][1]);
    $sqlstmt = $this->phpht->dbPrepare($sql);
    if($sqlstmt) {
      syslog(LOG_INFO,"SQL statement created");
      $data = $this->read($sqlstmt,$queryValues,$className);
    } else {
      syslog(LOG_INFO,"no SQL statement found");
      $data["response_code"] = 400;
      $data["errors"][] = "failed to delete {$objectType} for \"{$values[$registryObject["resourceNameField"]]}\"";
      $data["errors"][] = "(statement error)";
    }
    return $data;
  }

  public function getForm($matches,$action="create",$data=[]) {
    $userId = $this->phpht->checkAuth();
    if(!isset($userId)) return;
    $action = (isset($matches[2])) ? $matches[2] : "create";
    $className = strtolower(get_class($this));
    
    if(isset($this->objectRegistry[$className]["security"][$action])) {
      syslog(LOG_INFO,"Permissions-check required for action: $action");
      if(!$this->phpht->isAuthorized($this->objectRegistry[$className]["security"][$action])) return $this->phpht->view($this->phpht->getVal("home"));
    }

    global ${$matches[1]};
    $className = strtolower(get_class($this));
    syslog(LOG_INFO,"ACTION IS: $action on $matches[1] ($className)");
    $registryObject = $this->getRegistryObject($className);
    $data["matches"] = $matches;
    return $this->phpht->view($registryObject["forms"][$action],$data);
  }

  protected function getGlobalValues($userId) {
    $globalValues = [];
    $globalValues["userId"] = $userId;
    $globalValues["currentDomainId"] = $this->phpht->getCurrentDomain($userId);
    $globalValues["currentDate"] = $this->phpht->getDateTime()->format('Y-m-d H:i:s');
    return $globalValues;
  }

  protected function canDo($className,$activity="read") {
    $securityDefinition = $this->getIfSecurityDefinitionExists($className,$activity);
    if($securityDefinition) {
      if($this->phpht->isAuthorized($securityDefinition)) {
        return $activity;
      }
    }
    return false;
  }

  protected function getIfSecurityDefinitionExists($className,$activity="read") {
    syslog(LOG_INFO,"checking security for $activity on $className");
    $registryObject = $this->getRegistryObject($className);
    if(array_key_exists("security",$registryObject)) {
      if(array_key_exists($activity,$registryObject["security"])) {
        return $registryObject["security"][$activity];
      }
    }
    return FALSE;
  }

  public function getRead($matches) {
    $userId = $this->phpht->isLoggedIn();
    $className = strtolower(get_class($this));
    syslog(LOG_INFO,"attempting to get (read) of $className");
    $action = $this->canDo($className,"readAll");
    if(!$action) $action = $this->canDo($className,"read");
    if(!$action) {
      syslog(LOG_INFO,"Unauthorized to read $className");
      $data["response_code"] = 403;
      $data["errors"][] = "forbidden to read $className";
      return $data;
    }

    syslog(LOG_INFO,"Authorized read action on $className: '$action'");

    $registryObject = $this->getRegistryObject($className);
    $queryValues = $this->getGlobalValues($userId);

    if(!isset($matches[2])) {
      $sql = $registryObject[$action][0];
      // $queryValues["domainId"] = $queryValues["currentDomainId"];
      $queryValues = $this->getQueryValues($queryValues,$registryObject[$action][1]);
    } elseif(isset($matches[2])) {
      syslog(LOG_INFO,"Looking for resource ID: $matches[2]");
      $sql = $registryObject["readItem"][0];
      $queryValues[$registryObject["resourceId"]] = $matches[2];
      $queryValues = $this->getQueryValues($queryValues,$registryObject["readItem"][1]);
    }
    
    $sqlstmt = $this->phpht->dbPrepare($sql);
    if($sqlstmt) {
      syslog(LOG_INFO,"SQL statement created");
      $data = $this->read($sqlstmt,$queryValues,$className);
    } else {
      syslog(LOG_INFO,"no SQL statement found");
      $data["response_code"] = 400;
      $data["errors"][] = "failed to delete {$objectType} for \"{$values[$registryObject["resourceNameField"]]}\"";
      $data["errors"][] = "(statement error)";
    }
    return $data;
  }

  protected function getQueryValues($globalValues,$searchValues=null) {
    syslog(LOG_INFO,"VALUES PRESENT:");
    foreach ($globalValues as $key => $value) {
      syslog(LOG_INFO,"$key => $value");
    }

    syslog(LOG_INFO,"SEARCHING FOR KEYS:");
    foreach ($searchValues as $key => $value) {
      syslog(LOG_INFO,"$key => $value");
    }
    return array_intersect_key($globalValues,array_fill_keys($searchValues,null));
  }

  protected function getRegistryObject($object) {
    syslog(LOG_INFO,"Getting registry object '{$object}'");
    return $this->objectRegistry[$object];
  }

  public function getUIElement($matches) {
    $userId = $this->phpht->isLoggedIn();
    if($userId!==FALSE) {
      $className = strtolower(get_class($this));
      $registryObject = $this->getRegistryObject($className);
      if($this->phpht->isAuthorized($this->objectRegistry[$className]["security"][$matches[2]])) {
        $uiLink = '/' . $this->objectRegistry[$className]["resourceType"] . '/' . $matches[2] . '/'; 
        return array("result" => $uiLink,"errors" => []);
      }
    }
    syslog(LOG_INFO,"access to UI element unauthorized");
    return array("result" => false,"errors" => [[$this->objectRegistry[$className]["resourceType"] . " " . $matches[2] . " unauthorized"]]);
  }

  public function getView() {
    syslog(LOG_INFO,"getView(): View is: {$this->view}");
    return $this->view;
  }

  public function postCreate($values) {
    $userId = $this->phpht->checkAuth();
    if(!isset($userId)) return;
    $className = strtolower(get_class($this));
    syslog(LOG_INFO,"attempting POST (create) of ".$className);
    $registryObject = $this->getRegistryObject($className);
    
    // loookup appropriate SQL stmt
    $sql = $registryObject["create"][0];
    $sqlstmt = $this->phpht->dbPrepare($sql);

    error_log(print_r($values,TRUE));
    
    $values = array_merge($values,$this->getGlobalValues($userId));
    if($sqlstmt) {
      syslog(LOG_INFO,"SQL statement created");
      $queryValues = [];
      if(count($registryObject["create"])>1) {
        $queryValues = $this->getQueryValues($values,$registryObject["create"][1]);
      }
      $data = $this->create($sqlstmt,$queryValues,$className);
    } else {
      syslog(LOG_INFO,"no sqlstmt found");
      $data["response_code"] = 400;
      $data["errors"][] = "failed to create {$className}";
      $data["errors"][] = "(statement error)";
    }
    return $data;
  }
  
  public function putEdit($matches,$values) {
    $userId = $this->phpht->checkAuth();
    if(!isset($userId)) return;
    $className = strtolower(get_class($this));
    syslog(LOG_INFO,"attempting PUT (edit) of ".$className);
    $registryObject = $this->getRegistryObject($className);

    $sql = $registryObject["update"][0];
    $sqlstmt = $this->phpht->dbPrepare($sql);

    if($sqlstmt) {
      syslog(LOG_INFO,"sqlstmt created");
      syslog(LOG_INFO,print_r($values,TRUE));
      $values = array_merge($values,$this->getGlobalValues($userId));
      $values[$registryObject["resourceId"]] = $matches[2];
      $queryValues = $this->getQueryValues($values,$registryObject["update"][1]);
      error_log(print_r($queryValues,TRUE));
      $data = $this->update($sqlstmt,$queryValues,$className);
    } else {
      syslog(LOG_INFO,"sqlstmt did not compile");
      $data["response_code"] = 400;
      $data["errors"][] = "failed to update {$matches[1]} for \"{$values[$registryObject["resourceNameField"]]}\"";
      $data["errors"][] = "(statement error)";
    }
    return $data;    
  }

  protected function create($sqlstmt,$values,$objectType) {
    syslog(LOG_INFO,"Creating {$objectType}");
    $registryObject = $this->getRegistryObject($objectType);
    $count = $sqlstmt->execute($values);
    if($count!==false) {
      syslog(LOG_INFO,"CREATE returned count: $count");
      $objectId = $this->phpht->dbLastInsertId();
      $data["response_code"] = 201;
      $data["messages"][] = "{$objectType} \"{$values[$registryObject["resourceNameField"]]}\" added";
      $data["objectId"] = $objectId;
      $data["objectType"] = $objectType;
      $data["resource"] = $registryObject["resourceType"]."/".$objectId;
      $data["type"] = $registryObject["resourceType"];
      $data["errors"] = [];
    } else {
      $errMsg = $sqlstmt->errorInfo();
      syslog(LOG_INFO,"INSERT fail: " . print_r($errMsg, TRUE));
      $data["response_code"] = 409;
      $data["errors"][] = "failed to create object for \"{$values[$registryObject["resourceNameField"]]}\"";
      $data["errors"][] = $errMsg;
    }
    return $data;
  }

  protected function read($sqlstmt,$values,$objectType) {
    syslog(LOG_INFO,"Reading: {$objectType}");
    $registryObject = $this->getRegistryObject($objectType);
    if($sqlstmt->execute($values)) {
      syslog(LOG_INFO,"Success executing SQL statement");
      $data["result"] = $sqlstmt->fetchAll(PDO::FETCH_ASSOC);
      $data["response_code"] = 200;
      $data["messages"][] = "{$objectType} retrieved";
      $data["type"] = $registryObject["resourceType"];
      $data["errors"] = [];
    } else {
      syslog(LOG_INFO,"Error executing SQL statement");
      $dbgMsg = $sqlstmt->debugDumpParams();
      $errMsg = $sqlstmt->errorInfo();
      $data["response_code"] = 409;
      $data["errors"][] = "failed to retrieve {$objectType}";
      $data["errors"][] = $errMsg;
      $data["errors"][] = $dbgMsg;
    }
    return $data;
  }

  /*
   * $values = array(":name" => $domainName, ":description" => $domainDescription, ":date" => $currentDateTime, "domainId" => $domainId)
   * TODO: SQL statement preparation through PDO->prepare() isn't working
   * TODO: So, I had to use less secure SQL statement. This needs to be fixed
   */
  protected function update($sqlstmt,$values,$objectType) {
    syslog(LOG_INFO,"editing {$objectType}");
    $registryObject = $this->getRegistryObject($objectType);
    foreach ($values as $key => $value) {
      syslog(LOG_INFO,"$key => $value");
    }
    $result = $sqlstmt->execute($values);
    if($result) {
      syslog(LOG_INFO,"SENDING SUCCESS CODE");
      $data["response_code"] = 200;
      $data["messages"][] = "{$objectType} modified";
      $data["objectType"] = $objectType;
      $data["type"] = $registryObject["resourceType"];
      $data["errors"] = [];
    } else {
      syslog(LOG_INFO,"sending fail code");
      $data["response_code"] = 409;
      $data["errors"][] = "failed to modify {$objectType}";
      $data["errors"][] = $this->phpht->dbGetSqlError();
      syslog(LOG_INFO,print_r($data["errors"]));
    }
    return $data;
  }

  /*
   * $values = array(":expenseId" => $expenseId)
   */
  protected function delete($sqlstmt,$values,$objectType) {
    // var_dump($values);
    $result = $sqlstmt->execute($values);
    // var_dump($result);
    if($result!==false) {
      $registryObject = $this->getRegistryObject($objectType);
      $data["response_code"] = 200;
      $data["result"][] = array("id" => $values[$registryObject["resourceId"]]);
      $data["messages"][] = "{$objectType} ".$values[$registryObject["resourceId"]]." deleted";
      $data[$registryObject["resourceId"]] = $values[$registryObject["resourceId"]];
      $data["resource"] = array($objectType => "$objectType/".$values[$registryObject["resourceId"]]);
    } else {
      $dbgMsg = $sqlstmt->debugDumpParams();
      $errMsg = $sqlstmt->errorInfo();
      $data["response_code"] = 409;
      $data["errors"][] = "failed to delete {$objectType}";
      $data["errors"][] = $errMsg;
      $data["errors"][] = $dbgMsg;
      // var_dump($data);
    }
    return $data;   
  }
}

Class User extends PHPHT_Model {
  private $column_list_public = 'users.id, users.email, users.username, users.status, users.verified, users.roles_mask, domains.id as domainId, domains.name as domainName, domains.description as domainDescription';
  function __construct($phpht) {
    parent::__construct($phpht);
    $this->objectRegistry["user"] = array(
      "resourceType" => "users",
      "resourceId" => "userId",
      "resourceNameField" => "name",
      "security" => [
        "create" => [
          \Delight\Auth\Role::COORDINATOR,
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ],
        "readAll" => [
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ],
        "read" => [
          \Delight\Auth\Role::MANAGER,
          \Delight\Auth\Role::COORDINATOR,
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ]
      ],
      "forms" => [
        "create" => "users_create.php"
      ],
      "readAll" => [
        "SELECT 
          {$this->column_list_public} 
        FROM users 
          LEFT JOIN users_domains ON users.id=users_domains.userId 
          LEFT JOIN domains ON users_domains.domainId=domains.id 
        ORDER BY users.id ASC",
        []
      ],
      "read" => [
        "SELECT 
          {$this->column_list_public} 
        FROM users 
          LEFT JOIN users_domains ON users.id=users_domains.userId 
          LEFT JOIN domains ON users_domains.domainId=domains.id 
        WHERE 
          users_domains.domainId=:currentDomainId
        ORDER BY users.id ASC",
        ["currentDomainId"]
      ],
      "readItem" => [
        "SELECT 
          {$this->column_list_public} 
        FROM users 
          LEFT JOIN users_domains ON users.id=users_domains.userId 
          LEFT JOIN domains ON users_domains.domainId=domains.id 
        WHERE users.id=:userId",
        ["userId"]
      ],
      "readSU" => [
        "SELECT id, email, username FROM users WHERE id=1 AND username='superuser'"
      ]
    );
  }
  
  protected function addUserToDomainById($newUserId,$domainId) {
    $currentUserId = $this->phpht->isLoggedIn();
    if(!$currentUserId) return false;

    $sql = "INSERT INTO users_domains VALUES (:newUserId,:domainId,:currentDate,null)";
    $sqlstmt = $this->phpht->dbPrepare($sql);
    $values = $this->getGlobalValues($currentUserId);
    $values["newUserId"] = intval($newUserId);
    $values["domainId"] = intval($domainId);
    $values = $this->getQueryValues($values,["newUserId","domainId","currentDate"]);
    if($sqlstmt) {
      $result = $sqlstmt->execute($values);
      return $result;
    }
    return $sqlstmt;
  }

  protected function createDefaultUser($email,$password,$userName='',$domainId=0,$roleId=16) {
    if(!$email) return false;
    if(!$password) return false;
    
    // syslog(LOG_INFO,"Email: $email, Password: $password, Username: $userName, Domain: $domainId");

    try {
      $data = array();
      $newUserId = $this->phpht->auth->admin()->createUser($email,$password,$userName);
      syslog(LOG_INFO,"User created with ID: $newUserId");
      if($newUserId) {
        syslog(LOG_INFO,"initial user created");
        $data["response_code"] = 201;
        $data["messages"][] = "User added";
        $data["objectId"] = $newUserId;
        $data["objectType"] = "user";
        $data["resource"] = "users/".$newUserId;
        $data["type"] = "users";
        $data["errors"] = [];
        $roleResult = $this->phpht->addRoleForUserById($newUserId,$roleId);
        $data["messages"] = array_merge($data["messages"],$roleResult["messages"]);
        $roleResult = $this->addUserToDomainById($newUserId,$domainId);
        error_log(print_r($roleResult,TRUE));
      } else {
        return false;
      }
    } catch (\Delight\Auth\InvalidEmailException $e) {
        die('Invalid email address');
    } catch (\Delight\Auth\InvalidPasswordException $e) {
        die('Invalid password');
    } catch (\Delight\Auth\UserAlreadyExistsException $e) {
        die('User already exists');
    }
    return $data;
  }

  public function getUserDomain($userId) {
    $sql = "SELECT domainId FROM users_domains WHERE userId=$userId";
    $sqlstmt = $this->phpht->dbPrepare($sql);
    if($sqlstmt->execute()) {
      $row = $sqlstmt->fetch(PDO::FETCH_ASSOC);
      return $row["domainId"];
    }
    return false;
  }

  public function userInDomainId($userId,$domainId) {
    $sql = "SELECT domains.id FROM users_domains JOIN domains ON users_domains.domainId=domains.id WHERE users_domains.userId=$userId AND users_domains.domainId=$domainId";
    $sqlstmt = $this->phpht->dbPrepare($sql);
    if($sqlstmt->execute()) {
      if(count($sqlstmt->fetchAll())>0) return $domainId;
    }
    return false;
  }

  public function postCreate($matches) {
    $userId = $this->phpht->checkAuth();
    if(!isset($userId)) return;
    $action = (isset($matches[2])) ? $matches[2] : "create";
    if(isset($this->objectRegistry["user"]["security"][$action])) {
      syslog(LOG_INFO,"Permissions-check required for action: $action");
      if(!$this->phpht->isAuthorized($this->objectRegistry["user"]["security"][$action])) {
        syslog(LOG_INFO,"Permissions-check failed");
        return $this->phpht->view($this->phpht->getVal("home"));
      }
    }
    $data = $this->createDefaultUser($_POST["email"],$_POST["password"],$_POST["username"],$_POST["domainid"],$_POST["roleid"]);
    if(!$data) {
      $data["response_code"] = 409;
      $data["errors"][] = "failed to create user";
    }
    return $data;
  }

  public function superuserExists() {
    $sql = $this->objectRegistry["user"]["readSU"][0];
    $sqlstmt = $this->phpht->dbPrepare($sql);
    if($sqlstmt->execute()) {
      $row = $sqlstmt->fetch(PDO::FETCH_ASSOC);
      return $row["id"];
    }
    return false;
  }
}

Class Domain extends PHPHT_Model {
  function __construct($phpht) {
    parent::__construct($phpht);
    $this->objectRegistry["domain"] = array(
      "resourceType" => "domains",
      "resourceId" => "domainId",
      "resourceNameField" => "name",
      "security" => [
        "create" => [
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ],
        "readAll" => [
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ],
        "read" => [
          \Delight\Auth\Role::MANAGER,
          \Delight\Auth\Role::COORDINATOR,
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ],
        "readItemFull" => [
          \Delight\Auth\Role::COORDINATOR,
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ],
        "readItem" => [
          \Delight\Auth\Role::COORDINATOR,
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ]
      ],
      "forms" => [
        "create" => "domains_create.php",
        "edit" => "domains_edit.php"
      ],
      "create" => [
        "INSERT INTO domains VALUES (
          null, 
          :name, 
          :description,
          :appid, 
          :currentDate, 
          '', 
          :userId
        )",
        ["name", "description", "appid", "currentDate", "userId"]
      ],
      "readAll" => [
        "SELECT * FROM domains ORDER BY name ASC",
        []
      ],
      "read" => [
        "SELECT * FROM domains WHERE id=:currentDomainId",
        ["currentDomainId"]
      ],
      "readItemFull" => [
        "SELECT * FROM domains WHERE id=:domainId",
        ["domainId"]
      ],
      "readItem" => [
        "SELECT * FROM domains WHERE id=:domainId",
        ["domainId"]
      ],
      "update" => [
        "UPDATE domains SET 
        name=':name', 
        description=':description', 
        updatedAt=':currentDate' 
        WHERE id=:statusId",
        ["name","description","currentDate","statusId"]
      ],
      "delete" => [
        "DELETE FROM domains WHERE id=:domainId",
        ["domainId"]
      ]
    );
  }
}

Class Role extends PHPHT_Model {
  protected $roles = [
    [
      "name" => "Public", 
      "id" => \Delight\Auth\Role::CONSUMER,
      "description" => "View territories. No access to householder information. No ability to edit. Cannot request territories."],
    [
      "name" => "Publisher", 
      "id" => \Delight\Auth\Role::PUBLISHER,
      "description" => "General access to territories. Assign personal settings. View and create householders and events. Request territories."
    ],
    [
      "name" => "Territory Editor", 
      "id" => \Delight\Auth\Role::EDITOR,
      "description" => "Create householders. Assign status. Request territories."
    ],
    [
      "name" => "Territory Creator", 
      "id" => \Delight\Auth\Role::CREATOR,
      "description" => "Create territories. Create Maps."
    ],
    [
      "name" => "Territory Assistant", 
      "id" => \Delight\Auth\Role::MANAGER,
      "description" => "View users. Approve territory requests and returns. Create statuses."
    ],
    [
      "name" => "Service Overseer", 
      "id" => \Delight\Auth\Role::COORDINATOR,
      "description" => "Create users"],
    [
      "name" => "Application Admin", 
      "id" => \Delight\Auth\Role::ADMIN,
      "description" => "Approves domains (congregations)"
    ],
    [
      "name" => "Super Admin", 
      "id" => \Delight\Auth\Role::SUPER_ADMIN,
      "description" => "Assign application settings. Create application admins"
    ]
  ];

  function __construct($phpht) {
    parent::__construct($phpht);
    $this->objectRegistry["role"] = array(
      "security" => [
        "create" => [
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ],
        "readAll" => [
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ],
        "readItemFull" => [
          \Delight\Auth\Role::COORDINATOR,
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ],
        "readItem" => [
          \Delight\Auth\Role::COORDINATOR,
          \Delight\Auth\Role::ADMIN,
          \Delight\Auth\Role::SUPER_ADMIN
        ]
      ],
    );
  }

  public function getRead($matches) {
    $userId = $this->phpht->isLoggedIn();
    if(!$userId) return false; 
    $className = strtolower(get_class($this));
    syslog(LOG_INFO,"attempting to get (read) of $className");
    if($this->phpht->hasRole(\Delight\Auth\Role::SUPER_ADMIN)) {
      $data["result"] = array_slice($this->roles,0,count($this->roles)-1);
    } elseif ($this->phpht->hasRole(\Delight\Auth\Role::ADMIN)) {
      $data["result"] = array_slice($this->roles,0,count($this->roles)-2);
    } elseif ($this->phpht->hasRole(\Delight\Auth\Role::COORDINATOR)) {
      $data["result"] = array_slice($this->roles,0,count($this->roles)-3);
    } elseif ($this->phpht->hasRole(\Delight\Auth\Role::MANAGER)) {
      $data["result"] = array_slice($this->roles,0,count($this->roles)-4);
    } else {
      $data["result"] = [];
    }
    $data["response_code"] = 200;
    $data["messages"][] = "roles retrieved";
    $data["type"] = "role";
    $data["errors"] = [];
    return $data;
  }

}

Class UserAppId extends PHPHT_Model {
  function __construct($phpht) {
    parent::__construct($phpht);
  }

  public function setUserAppId($userId) {
    if(!$userId) return false;
    syslog(LOG_INFO,"Creating appId for userId: $userId");
    $sql = "INSERT INTO users_appids VALUES (:userId,:appId,:currentDate)";
    $sqlstmt = $this->phpht->dbPrepare($sql);
    if($sqlstmt) {
      $values = [];
      $values["userId"] = $userId;
      $values["appId"] = $this->phpht->createAppId(8);
      $values["currentDate"] = $this->phpht->getDateTime()->format('Y-m-d H:i:s');
      syslog(LOG_INFO,print_r($values,TRUE));
      $result = $sqlstmt->execute($values);
      return $result;
    }
    return false;
  }
}