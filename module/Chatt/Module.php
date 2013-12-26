<?php

namespace Chatt;

use Zend\ModuleManager\Feature;

/**
 * @author gRRinch <radoslaw.paluszak@student.put.poznan.pl>
 */
class Module implements Feature\AutoloaderProviderInterface, Feature\ConfigProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}