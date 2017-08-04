<?php

date_default_timezone_set("Asia/Jakarta");

$pb = "/home/web/bot/public/";
$vd = $pb."virtual/";

define("TOKEN", "448907482:AAGAaT7iP-CUC7xoBeXSyC-mrovzwmYka4w");

define("SUDOERS", "");
define("FORCE_ADMIN", "");

/**
 * Database setting
 */
define("DBHOST", "localhost");
define("DBPORT", "3306");
define("DBNAME", "lemon_juice");
define("DBUSER", "icetea");
define("DBPASS", "triosemut123");

/**
 * Virtualer
 */
define("PHP_VIRTUAL_URL", "https://webhooks.redangel.ga/virtual/php");
define("PHP_VIRTUAL_PATH", $vd."php");
define("C_VIRTUAL_PATH", $vd."c");
define("RUBY_VIRTUAL_PATH", $vd."ruby");
define("JAVA_VIRTUAL_PATH", $vd."java");
define("PYTHON_VIRTUAL_PATH", $vd."python");
define("NODE_VIRTUAL_PATH", $vd."node");

/**
 * Storage
 */
define("IMAGE_STORAGE", $pb."/assets/images/");
define("VIDEO_STORAGE", $pb."/assets/videos/");
define("FILE_STORAGE", $pb."/assets/file/");
