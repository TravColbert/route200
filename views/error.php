<div class="bigbanner">
  <div class="welcome">
    phpht
  </div>
</div>
  <div class="error">
    <?php
    if(isset($data["error"])) {
      foreach($data["error"] as $errorMessage)
      echo "<div class='error'>".$errorMessage."</div>";
    } else {
      echo "an error occured"; 
    } 
    ?>
  </div>
</div>