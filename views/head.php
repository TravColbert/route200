<!DOCTYPE html>
<html lang="<?php echo $this->setLang() ?>">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title><?php echo $pageTitle; ?></title>

  <link rel="apple-touch-icon" sizes="57x57" href="<?php echo $this->getConfig("baseurl") ?><?php echo $this->getConfig("baseurl") ?>/public/img/favicons/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/favicon-16x16.png">
  
  <!-- PHPHT CSS -->
  <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->getConfig("baseurl") ?>/public/css/phpht.css">

  <!-- Spectre CSS -->
  <link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre.min.css">
  <link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre-icons.min.css">

  <!-- <link rel="manifest" href="<?php echo $this->getConfig("baseurl") ?>/manifest.webmanifest"> -->
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="<?php echo $this->getConfig("baseurl") ?>/public/img/favicons/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://code.jquery.com/jquery-3.5.0.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js" crossorigin="anonymous"></script>

</head>
<?php
$class="";
if(!$this->isLoggedIn()) {
  $class .= "backgroundsplash";
}
?>
<body class="<?php echo $class; ?>">
