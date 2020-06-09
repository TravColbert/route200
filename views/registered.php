<article id="mainwelcome" class="fullpage">
  <div class="empty">
    <div class="empty-icon">
      <i class="icon icon-check icon-4x"></i>
    </div>
<?php
  if($data["response_code"] === 201) {
?>
    <p class="empty-title h1">Registered</p>
    <p class="empty-subtitle">Good job! Please check your email to complete verification.</p>
<?php
  } else {
?>
    <p class="empty-title h3">Sorry!</p>
    <p class="empty-subtitle">We ran into a problem registering your account.</p>
    <p class="empty-subtitle">It might be because the account name has already been registered.</p>
<?php
  }
?>    
    <div class="empty-action">
      <a href="/" class="btn btn-primary">C'mon. Let's go back home!</a>
    </div>
  </div>
</article>
