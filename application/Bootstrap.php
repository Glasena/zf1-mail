<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDispatcher(): void
    {
        $this->bootstrap('frontController');
        $fc = $this->getResource('frontController');
        $dispatcher = new \Application\Controller\Dispatcher();
        $dispatcher->setControllerDirectory($fc->getDispatcher()->getControllerDirectory());
        $fc->setDispatcher($dispatcher);
    }

    protected function _initRoutes(): void
    {
        $this->bootstrap('frontController');

        $routes = require APPLICATION_PATH . '/configs/routes.php';

        Zend_Registry::set('routes', $routes);

        $router = $this->getResource('frontController')->getRouter();

        foreach ($routes as $name => $config) {
            $router->addRoute($name, new Zend_Controller_Router_Route(
                $config['route'],
                ['module' => $config['module'], 'controller' => $config['controller'], 'action' => $config['action']]
            ));
        }
    }

    protected function _initAclPlugin(): void
    {
        $this->bootstrap('frontController');
        $this->getResource('frontController')->registerPlugin(new Application_Plugin_Acl());
    }

    protected function _initViewSetup(): Zend_View
    {
        $this->bootstrap('view');
        /** @var Zend_View $view */
        $view = $this->getResource('view');
        $view->setEncoding('UTF-8');
        $view->doctype('HTML5');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8', []);

        return $view;
    }

    protected function _initDoctrine(): EntityManager
    {
        $options = $this->getOption('doctrine');
        $isDev = APPLICATION_ENV === 'development';

        $metadataCache = $isDev
            ? new ArrayAdapter()
            : new FilesystemAdapter('doctrine_meta', 0, APPLICATION_PATH . '/../data/doctrine/cache');

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: glob(APPLICATION_PATH . '/Modules/*/Entities', GLOB_ONLYDIR) ?: [],
            isDevMode: $isDev,
            proxyDir: $options['proxies_dir'],
            cache: $metadataCache,
        );

        $config->setProxyNamespace($options['proxies_ns']);
        $config->setAutoGenerateProxyClasses((bool) $options['auto_generate_proxies']);

        $connection = DriverManager::getConnection([
            'driver' => $options['conn']['driver'],
            'host' => getenv('DB_HOST'),
            'port' => (int) getenv('DB_PORT'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'dbname' => getenv('DB_NAME'),
            'charset' => getenv('DB_CHARSET'),
        ], $config);

        $em = new EntityManager($connection, $config);

        Zend_Registry::set('doctrine.em', $em);

        return $em;
    }

    protected function _initMail()
    {
        $mailConfig = [
            'host' => getenv('MAIL_HOST'),
            'port' => (int) getenv('MAIL_PORT'),
            'user' => getenv('MAIL_USER'),
            'pass' => getenv('MAIL_PASS'),
        ];

        $zendMailTransport = new Zend_Mail_Transport_Smtp($mailConfig['host'], [
            'port' => $mailConfig['port'],
            'auth' => 'login',
            'username' => $mailConfig['user'],
            'password' => $mailConfig['pass']
        ]);

        Zend_Mail::setDefaultTransport($zendMailTransport);
    }
}
