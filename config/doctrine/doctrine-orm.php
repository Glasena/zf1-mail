<?php

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\ConsoleRunner as MigrationsConsoleRunner;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Symfony\Component\Console\Application;

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

$dependencyFactory = DependencyFactory::fromEntityManager(
    new PhpFile(__DIR__ . '/migrations.php'),
    new ExistingEntityManager($em)
);

$cli = new Application('Doctrine');
ConsoleRunner::addCommands($cli, new SingleManagerProvider($em));
MigrationsConsoleRunner::addCommands($cli, $dependencyFactory);
$cli->run();
