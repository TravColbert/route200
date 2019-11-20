<?php
if($auth->isLoggedIn()) {
  global $router;
  phpinfo();
?>
<div class='center'>
  <hr>
  <h1>PHPHT INFO</h1>
  <table>
    <tbody>
      <tr>
        <td class='e'>URL_BASE</td><td class='v'><?php echo $router->getUrlBase() ?></td>
      <tr>
    </tbody>
  </table>
  <h2>Routes</h2>
  <table>
    <tbody>
<?php
    foreach($router->getRoutes() as $routeVerb=>$routeVerbRoutes) {
?>
      <tr>
        <td class='e'><?php echo $routeVerb ?></td>
        <td class='v'>
<?php
      foreach($routeVerbRoutes as $route) {
        echo $route[0]."<br>";
      }
?>
        </td>
      </tr>
<?php
    }
?>
    </tbody>
  </table>
</div>
<?php
} else {
  echo "Must authenticate before showing info";
}