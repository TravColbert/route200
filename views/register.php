<article id="mainwelcome" class="fullpage">
  <div class="container">
    <div class="mainblurb">
      <div>Sign Up</div>
      <div>To start using <?php echo $this->appname ?></div>
      <hr/>
    </div>
    <div class="subblurb">Already signed-up? Dive right in!</div>
    <div id="loginbutton">
      <a href="<?php echo $this->getConfig("prefixurl") ?>/login/" class="buttonstyle compact">log in</a>
    </div>
  </div>
  <?php
  setMessages("errors",$data);
  setMessages("messages",$data);
  ?>
  <div class="container wimpy">
    <div class="registerbox formbox">
      <form id="registerform" action="<?php echo $this->getConfig("prefixurl") ?>/register/" method="POST">
        <div class="fieldset">
          <div id="ff-email" class="formfield">
            <input type="text" name="email" id="email" placeholder="email">
          </div>
          <div id="ff-pass" class="formfield">
            <input type="password" name="pass" id="pass" placeholder="pass phrase">
          </div>
          <div id="ff-submit" class="formfield">
            <input type="submit" value="register" class="primary">
          </div>
        </div>
      </form>
    </div>
  </div>
</article>
