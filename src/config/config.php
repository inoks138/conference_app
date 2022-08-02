<?php

require '../public/env.php';

define('DB_HOST', $db_host);
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASSWORD', $db_password);

define('GOOGLE_MAPS_API_KEY', $google_maps_api_key);

define('APP_ROOT', dirname(dirname(dirname(__FILE__))));
define('URL_ROOT', 'http://' . $_SERVER['SERVER_NAME']);
define('SITE_NAME', 'Conference App');
