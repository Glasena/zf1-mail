<?php

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;

require_once __DIR__ . '/../load-env.php';

define('APPLICATION_PATH', __DIR__ . '/../../application');
define('APPLICATION_ENV', getenv('APPLICATION_ENV') ?: 'development');

require_once __DIR__ . '/../../vendor/autoload.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap('doctrine');

/** @var \Doctrine\ORM\EntityManager $em */
$em = Zend_Registry::get('doctrine.em');

// Retorna DependencyFactory para uso pelo vendor/bin/doctrine-migrations
return DependencyFactory::fromEntityManager(
    new PhpFile(__DIR__ . '/migrations.php'),
    new ExistingEntityManager($em)
);
