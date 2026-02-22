<?php

class Application_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request): void
    {
        $routes = Zend_Registry::get('routes');

        $currentModule     = $request->getModuleName();
        $currentController = $request->getControllerName();
        $currentAction     = $request->getActionName();

        $routeConfig = $this->findRoute($routes, $currentModule, $currentController, $currentAction);

        if ($routeConfig === null) {
            return;
        }

        if ($routeConfig['auth'] === true) {
            // Verificar autenticação aqui quando implementar login
            // Ex: if (!Zend_Auth::getInstance()->hasIdentity()) { ... }
        }
    }

    private function findRoute(array $routes, string $module, string $controller, string $action): ?array
    {
        foreach ($routes as $config) {
            if (
                $config['module']     === $module &&
                $config['controller'] === $controller &&
                $config['action']     === $action
            ) {
                return $config;
            }
        }

        return null;
    }
}
