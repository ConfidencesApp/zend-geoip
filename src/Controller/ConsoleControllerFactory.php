<?php

namespace Confidences\ZendGeoip\Controller;

use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Confidences\ZendGeoip\DatabaseConfig;

/**
 * Factory of ConsoleController
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class ConsoleControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $console = $container->get('Console');
        $config = $container->get(DatabaseConfig::class);
        $httpClient = $container->get('ZendGeoip\HttpClient');

        return new $requestedName($console, $config, $httpClient);
    }
}
