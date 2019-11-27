<?php
phpinfo();
?>
<div class='center'>
  <hr>
  <h1>PHPHT INFO</h1>
  <table>
    <tbody>
      <tr>
        <td class='e'>URL_BASE</td><td class='v'><?php echo $this->router->getUrlBase() ?></td>
      <tr>
    </tbody>
  </table>
  <h2>Routes</h2>
  <table>
    <tbody>
<?php
    foreach($this->router->getRoutes() as $routeVerb=>$routeVerbRoutes) {
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
  <table>
    <tbody>
      <tr>
        <td class='e'>Assets</td>
        <td class='v'>
<?php
      echo $this->assets."<br>";
?>
      </tr>
    </tbody>
  </table>
</div>
