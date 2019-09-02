<?php
  if(isset($data['reportQueryString'])) {
    echo "<script>queryString='".$data['reportQueryString']."'</script>";
  }
?>
<div id="reportbody" data-report="<?php echo (isset($data['reportName'])) ? $data['reportName'] : '' ?>"></div>
