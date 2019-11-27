<article id="mainwelcome" class="fullpage">
  <div class="container">
    <div class="mainblurb">
      <div>PHPHT</div>
      <div>It's working!</div>
      <hr/>
    </div>
    <div class="subblurb">PHPHT is a fast, secure MVC framework using Delight Auth and Mailjet</div>
    <div id="learnmore">
      <a href="<?php echo $this->baseurl; ?>blog/" class="buttonstyle compact">learn more</a>
    </div>
  </div>
  <div class="container wimpy">
    <div class="logobox">
      <img id="coinslogo" src="/public/img/coins.png"/>
    </div>
  </div>
</article>
<?php
  setMessages("errors",$data);
  setMessages("messages",$data);
?>
