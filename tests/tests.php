<?php
set_include_path(realpath(__DIR__) .'/../' . PATH_SEPARATOR . get_include_path());

require_once 'vendor/autoload.php';

require_once 'opendomainregistry/opendomainregistry.php';
require_once 'opendomainregistry/api.php';

ini_set('memory_limit', '256M');
set_time_limit(20);

ini_set('display_errors', 1);
error_reporting(E_ALL);

mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'en_US');