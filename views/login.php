<div class="container main fullheight flex-centered">
  <div class="columns">
    <article id="loginpage" class="column col-12">
      <div class="columns">
        <form id="loginform" class="loginbox formbox column col-12 panel" action="<?php echo $this->getConfig("baseurl") ?>/login/" method="POST">
          <div class="panel-header text-center">
            <div class="panel-title">
              <figure class="avatar avatar-xl p-2">
                <i class="icon icon-people icon-3x"></i>
              </figure>
              <div class="text-large text-bold">Log In</div>
            </div>
          </div>
          <div class="panel-nav">
          </div>
          <div class="panel-body">
            <div class="fieldset form-group">
              <div id="ff-username" class="formfield">
                <label class="form-label" for="username">Email</label>
                <input type="text" name="username" id="username" class="col-12 noLastPassStyle" data-lpignore="true" placeholder="email" autocomplete="username" tabindex=0>
              </div>
              <div id="ff-pass" class="formfield">
                <label class="form-label" for="pass">Passphrase</label>
                <input type="password" name="pass" id="pass" class="col-12 noLastPassStyle" data-lpignore="true" placeholder="pass phrase" autocomplete="current-password" tabindex=0>
              </div>
            </div>
          </div>
          <div class="panel-footer">
            <div class="fieldset form-group">
              <div id="ff-login" class="formfield">
                <input type="submit" name="login-button" value="Login" class="btn btn-primary col-12" tabindex=0>
              </div>
            </div>
            <div class="fieldset form-group">
              <div class="ff-cancel" class="formfield">
                <a href="<?php echo $this->getConfig("baseurl") ?>/" type="submit" name="cancel-button" value="Cancel" class="btn btn col-12" tabindex=0>Cancel</a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </article>
  </div>
</div>