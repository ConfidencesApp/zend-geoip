<?php

namespace Confidences\ZendGeoip\Controller;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Confidences\ZendGeoip\DatabaseConfig;
use Confidences\ZendGeoip\HttpClientFactory;

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
        $httpClient = $container->get(HttpClientFactory::class);

        return new $requestedName($console, $config, $httpClient);
    }
}
