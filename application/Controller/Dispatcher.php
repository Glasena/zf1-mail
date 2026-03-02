<?php

namespace Application\Controller;

class Dispatcher extends \Zend_Controller_Dispatcher_Standard
{
    public function loadClass($className)
    {
        $module     = $this->_curModule ?: $this->getDefaultModule();
        $controller = preg_replace('/Controller$/', '', $className);

        $psr4Class = sprintf(
            'Application\\Modules\\%s\\Controllers\\%sController',
            ucfirst($module),
            ucfirst($controller)
        );

        $filePath = sprintf(
            '%s/Modules/%s/Controllers/%sController.php',
            APPLICATION_PATH,
            ucfirst($module),
            ucfirst($controller)
        );

        if (file_exists($filePath)) {
            require_once $filePath;
            if (class_exists($psr4Class, false)) {
                $zf1Class = $this->formatClassName($module, $className);
                if (!class_exists($zf1Class, false)) {
                    class_alias($psr4Class, $zf1Class);
                }
                return $psr4Class;
            }
        }

        return parent::loadClass($className);
    }
}
