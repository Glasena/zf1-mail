<?php

define('APPLICATION_PATH', __DIR__ . '/../application');
define('APPLICATION_ENV', getenv('APPLICATION_ENV') ?: 'development');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/load-env.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap('doctrine');

/** @var \Doctrine\ORM\EntityManager $em */
$em = Zend_Registry::get('doctrine.em');

$transport = new Zend_Mail_Transport_Smtp(getenv('MAIL_HOST'), [
    'port' => (int) getenv('MAIL_PORT'),
    'auth' => 'login',
    'username' => getenv('MAIL_USER'),
    'password' => getenv('MAIL_PASS'),
]);

Zend_Mail::setDefaultTransport($transport);
