<?php

require_once __DIR__ . '/../config/load-env.php';

define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));
define('APPLICATION_ENV', getenv('APPLICATION_ENV') ?: 'production');

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()->run();
