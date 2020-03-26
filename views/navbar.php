<header>
  <nav>
    <div class="navcontent">
      <div id="menu-title">
        <div id="app-title">
          <a href="<?php echo $this->getConfig("baseurl") ?>/"><?php echo $data["appname"] ?></a>
        </div>
      </div>
      <div class="navlinks">
        <ul>
          <input class="hidden" id="show-menu" type="checkbox" role="button">
          <li class="menu-link">
            <a href="<?php echo $this->getConfig("baseurl") ?>/blog/"><div class="nobreak">blog</div></a>
          </li>
          <?php if($this->isLoggedIn()) { ?>
            <li class="menu-link">
              <a href="<?php echo $this->getConfig("baseurl") ?>/settings/"><div class="nobreak">settings</div></a>
            </li>
            <li class="menu-link">
              <a href="<?php echo $this->getConfig("baseurl") ?>/logout/"><div class="nobreak">logout</div></a>
            </li>
          <?php } ?>
          <?php if(!$this->isLoggedIn()) { ?>
            <li class="menu-link">
              <a href="<?php echo $this->getConfig("baseurl") ?>/login/"><div class="nobreak">login</div></a>
            </li>
            <li class="menu-link">
              <a href="<?php echo $this->getConfig("baseurl") ?>/register/"><div class="nobreak">sign up</div></a>
            </li>
          <?php } ?>
          <li><label id="menu-hamburger" for="show-menu">☰</label></li>
        </ul>
      </div>
    </div>
  </nav>
</header>