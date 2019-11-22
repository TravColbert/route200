<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $pageTitle; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script src="https://kit.fontawesome.com/d5c73f28c4.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" media="screen" href="/froogle/public/css/froogle.css">
</head>
<?php
if($this->auth && !$this->auth->isLoggedIn()) {
?>
<body class="backgroundsplash">
<?php
} else {
?>
<body>
<?php
}

