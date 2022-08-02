<?php

require_once 'Config/config.php';

spl_autoload_register(function ($className) {
  $class = str_replace('\\', DIRECTORY_SEPARATOR, $className);
  $path = __DIR__ . DIRECTORY_SEPARATOR . "$class.php";

  if (is_readable($path)) {
    require $path;
  }
});
