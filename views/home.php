<?php
if(!$auth->isLoggedIn()) {
?>
<article id="mainwelcome" class="fullpage">
  <div class="container">
    <div class="mainblurb">
      <div>Expense-Tracking</div>
      <div>Made Easy!</div>
      <hr/>
    </div>
    <div class="subblurb">Manage your expenses like a boss with froogle.io</div>
    <div id="learnmore">
      <a href="<?php echo $this->baseurl; ?>blog/" class="buttonstyle compact">learn more</a>
    </div>
  </div>
  <div class="container wimpy">
    <div class="logobox">
      <img id="coinslogo" src="/froogle/public/img/coins.png"/>
    </div>
  </div>
</article>
<?php
} else {
?>
<article id="mainpage" class="fullpage">
  <div class="container">
    <div id="chart-selector"></div>
    <div id="chart-card" class="flip-card">
      <div class="flip-card-inner">
        <div class="flip-card-front">
          <div id="chart-month-expenses" class="piechart_3d chart"></div>
        </div>
        <div id="flip-card-back-table" class="flip-card-back">
          <table id="chart-table"></table>
        </div>
      </div>
    </div>
    <div id="flip-icon" class="icon" onClick="flipCard()"><i class="fas fa-table"></i><i class="fas fa-chart-pie"></i></i></div>
  </div>
  <div class="container">
    <div><a href="<?php echo $this->baseurl ?>expenses/" class="buttonstyle primary">My Transactions</a></div>
  </div>
</article>
<?php
}
setMessages("errors",$data);
setMessages("messages",$data);
if($auth->isLoggedIn()) {
?>
  <div id="bottompopup">
    <div id="expenseentry"></div>
  </div>
  <div id="logoutbutton">
    <div id="reportbody" data-report="<?php echo (isset($data['reportName'])) ? $data['reportName'] : '' ?>"></div>
  </div>
<?php
}
