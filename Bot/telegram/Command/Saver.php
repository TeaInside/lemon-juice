<?php

namespace Telegram\Command;

use Telegram\B;
use Foundation\DB;

class Saver
{
	public function __construct()
	{

	}

	public function image($image, $title, $caption = "")
	{
		$file_name = sha1($image).".jpg";
		DB::table("assets")->insert([
				"id" => null,
				"title" => $title,
				"caption" => $caption,
				"file_name" => $file_name,
				"type" => "image",
				"created_at" => date("Y-m-d H:i:s")
			]);
		is_dir("/home/web/bot/public/assets/images/") or shell_exec("sudo mkdir -p /home/web/bot/public/assets/images/");
		file_put_contents("/home/web/bot/public/assets/images/".$file_name, $image);
		return "https://webhooks.redangel.ga/assets/images/".$file_name;
	}
}