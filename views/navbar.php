<header class="col-12 navbar p-fixed">
  <section class="navbar-section">
    <a href="<?php echo $this->getConfig("baseurl") ?>/" class="navbar-brand mr-2"><?php echo $data["appname"] ?></a>
  </section>
  <section class="navbar-section">
    <?php if($this->isLoggedIn()) { ?>
      <a href="<?php echo $this->getConfig("baseurl") ?>/admin/" class="btn btn-link">admin</a>
      <a href="<?php echo $this->getConfig("baseurl") ?>/logout/" class="btn btn-link">logout</a>
    <?php } ?>
    <?php if(!$this->isLoggedIn()) { ?>
      <a href="<?php echo $this->getConfig("baseurl") ?>/login/" class="btn btn-link">login</a>
      <a href="<?php echo $this->getConfig("baseurl") ?>/register/" class="btn btn-link">sign up</a>
    <?php } ?>
  </section>
</header>
