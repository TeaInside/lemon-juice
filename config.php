<?php
define("data", __DIR__."/data");
define("logs", __DIR__."/logs");

is_dir(data) or mkdir(data);
is_dir(logs) or mkdir(logs);

define("TELEGRAM_TOKEN", "448907482:AAGAaT7iP-CUC7xoBeXSyC-mrovzwmYka4w");
