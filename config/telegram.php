<?php

/**
 * Timezone.
 */
date_default_timezone_set("Asia/Jakarta");

<<<<<<< HEAD
$pb = "/home/web/bot/public/";
$vd = $pb."virtual/";

define("TOKEN", "448907482:AAGAaT7iP-CUC7xoBeXSyC-mrovzwmYka4w");

define("SUDOERS", "");
define("FORCE_ADMIN", "");

/**
 * Database setting
=======
/**
 * Bot token.
 */
define("TOKEN", "448907482:AAGAaT7iP-CUC7xoBeXSyC-mrovzwmYka4w");

/**
 * Bot username.
 */
define("BOT_USERNAME", "MyIceTea_Bot");

/**
 * Admin and root.
 */
define("SUDOERS", "[]");
define("FORCE_ADMIN", "[]");

/**
 * Database (MySQL)
>>>>>>> master
 */
define("DBHOST", "localhost");
define("DBPORT", "3306");
define("DBNAME", "lemon_juice");
<<<<<<< HEAD
=======
/*define("DBUSER", "debian-sys-maint");
define("DBPASS", "");*/

>>>>>>> master
define("DBUSER", "icetea");
define("DBPASS", "triosemut123");

/**
<<<<<<< HEAD
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
=======
 * Logs and Storage.
 */
define("data", __DIR__."/../public/data");
define("logs", data."/logs");
define("storage", data."/storage");

/**
 * API MyAnimeList.
 */
define("MAL_USER", "ammarfaizi2");
define("MAL_PASS", "triosemut123");

/**
 * Virtualer
 */
$pb = "/home/web/bot/public/";
$vr = $pb."virtual/";
define("PUBLIC_DIR", $pb);
define("PHP_VIRTUAL_URL", "https://webhooks.redangel.ga/virtual");
define("PHP_VIRTUAL_PATH", $vr."php");
define("C_VIRTUAL_PATH", $vr."c");
define("JAVA_VIRTUAL_PATH", $vr."java");
define("RUBY_VIRTUAL_PATH", $vr."ruby");
define("NODE_VIRTUAL_PATH", $vr."node");
define("PYTHON_VIRTUAL_PATH", $vr."python");

define("ASSETS_R", $pb."/assets/files");
define("IMG_ASSETS", $pb."/assets/images");
define("VID_ASSETS", $pb."/assets/videos");
define("ASSETS_URL", "https://webhooks.redangel.ga/assets");

define("WHATANIME_URL", "https://webhooks.redangel.ga/whatanime");
<<<<<<< HEAD
>>>>>>> master
=======
>>>>>>> master
