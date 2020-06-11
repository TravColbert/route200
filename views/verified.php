<article id="mainwelcome" class="fullpage">
  <div class="empty">
    <div class="empty-icon">
      <i class="icon icon-check icon-4x"></i>
    </div>
<?php
  if($data["response_code"] === 200) {
?>
    <p class="empty-title h1">Verified</p>
    <p class="empty-subtitle">Good work! You've verified your new account. Now, log in to begin.</p>
    <div class="empty-action">
      <a href="/login/" class="btn btn-primary">Log In!</a>
    </div>
<?php
  } else {
?>
    <p class="empty-title h3">Sorry!</p>
    <p class="empty-subtitle">We ran into a problem verifying your account.</p>
    <div class="empty-action">
      <a href="/" class="btn btn-primary">C'mon. Let's go back home.</a>
    </div>
<?php
  }
?>    
  </div>
</article>
