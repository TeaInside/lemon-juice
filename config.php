<?php

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Lemon
 * @since 0.0.1
 */

if (!defined("data")) {
	define("data", __DIR__."/data");
	define("storage", __DIR__."/storage");
	define("logs", __DIR__."/logs");
	define("PHPVIRTUAL_URL", "https://webhooks.redangel.ga/virtual/php");
	define("PHPVIRTUAL_DIR", "/home/ammar/web/webhooks/public/virtual/php");
	define("JAVAVIRTUAL_DIR", "/home/ammar/web/webhooks/public/virtual/java");
	define("RUBYVIRTUAL_DIR", "/home/ammar/web/webhooks/public/virtual/ruby");
	define("CVIRTUAL_DIR", "/home/ammar/web/webhooks/public/virtual/c");

	is_dir(storage) or mkdir(storage);
	is_dir(data) or mkdir(data);
	is_dir(logs) or mkdir(logs);

	/**
	 * Pengaturan stack.
	 */
	define("TELEGRAM_TOKEN", "448907482:AAGAaT7iP-CUC7xoBeXSyC-mrovzwmYka4w");
	define("LINE_CHANNEL_TOKEN", "j0BTVSMgvXCFSGvzSQgU19V5G/WHOujP7100ZLUKbiePp9CehOfJEH4YMP/NHKKd5bjJhhTRxBURzPw3Xi939aTamjmDWQJtH81IoHAgFN7xZ6hpDqS8jEVOrL1cSR2HQ9lnAg4zxTWzfEUTex/sXAdB04t89/1O/w1cDnyilFU=");
	define("LINE_CHANNEL_SECRET", "a710fa6d726c9ca6773a7632d740a0d4");


	/**
	 * MyAnimeList auth.
	 */
	define("MAL_USER", "ammarfaizi2");
	define("MAL_PASS", "triosemut123");

	/**
	 * PDO
	 */
	define("PDO_CONNECT", "mysql:host=localhost;dbname=lemon_juice;port=3306");
	define("PDO_USER", "debian-sys-maint");
	define("PDO_PASS", "");
}