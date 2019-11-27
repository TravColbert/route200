<article id="mainwelcome" class="fullpage">
  <div class="container">
    <div class="mainblurb">
      <div>User Registration</div>
      <div>Registration Status</div>
      <hr/>
    </div>
<?php
  if($data["response_code"] === 201) {
?>
    <div class="subblurb"><?php echo $data["messages"][0] ?> Please check your email to complete verification.</div>
<?php
  } else {
?>
    <div class="subblurb">There was an error registering the user. <a href="">Try again</a>.</div>
<?php
  }
?>
    <div id="loginbutton">
      <a href="<?php echo $this->baseurl; ?>" class="buttonstyle compact">back home</a>
    </div>
  </div>
  <?php
  setMessages("errors",$data);
  setMessages("messages",$data);
  ?>
</article>
