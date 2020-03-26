<article id="mainwelcome" class="fullpage">
  <div class="container">
    <div class="mainblurb">
      <div>Log In</div>
      <div>To start using <?php echo $this->appname ?></div>
      <hr/>
    </div>
    <div class="subblurb">If you don't have an account go ahead and sign up</div>
    <div id="regsterbutton">
      <a href="<?php echo $this->getConfig("baseurl") ?>/register/" class="buttonstyle compact">sign up</a>
    </div>
  </div>
  <?php
  setMessages("errors",$data);
  setMessages("messages",$data);
  ?>
  <div class="container wimpy">
    <div class="loginbox formbox">
      <form id="loginform" action="<?php echo $this->getConfig("baseurl") ?>/login/" method="POST">
        <div class="fieldset">
          <div id="ff-username" class="formfield">
            <input type="text" name="username" id="username" placeholder="email" tabindex=1>
          </div>
          <div id="ff-pass" class="formfield">
            <input type="password" name="pass" id="pass" placeholder="pass phrase" tabindex=1>
          </div>
          <div id="ff-login" class="formfield">
            <input type="submit" value="login" class="primary" tabindex=2>
          </div>
        </div>
      </form>
    </div>
  </div>
</article>
