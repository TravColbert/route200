<style>
body {
  align-items: center;
  justify-content: center;
}

body, div {
  display: flex;
}

.bigproblem {
  align-items: center;
  flex-direction: column;
}

.error404 {
  color: silver;
  font-size: 8em;
}

.errortype {

}
</style>
<div class="bigproblem">
  <div class="error404">
  :o
  </div>
  <div class="errortype">
    <?php echo $error; ?>
  </div>
</div>