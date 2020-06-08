<article id="welcome" class="fullpage">
  <div class="container">
    <div class="mainblurb">
      <div>PHPHT - PWA Demo</div>
      <div>It's working!</div>
      <hr/>
    </div>
    <div class="subblurb">PHPHT is a fast, secure, Progressive Web App framework using Vue.js, Delight Auth and Mailjet</div>
    <div id="learnmore">
      <a href="<?php echo $this->getConfig("baseurl") ?>/blog/" class="buttonstyle compact">Learn more</a>
    </div>
  </div>
  <div class="container">
    <div id="animals" class="listbox"></div>
    <input type="button" value="Fetch animals" onClick='fetchThing("demo")'>
    <input type="button" value="Bad fetch" onClick='fetchThing("bad")'>
  </div>
</article>
<script src="<?php echo $this->getConfig("baseurl") ?>/public/js/pwa_demo.js"></script>

