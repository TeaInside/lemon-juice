<?php
require __DIR__."/../config.php";

spl_autoload_register("___load_class");

function ___load_class($class)
{
	require __DIR__."/".str_replace("\\", "/", $class).".php";
}