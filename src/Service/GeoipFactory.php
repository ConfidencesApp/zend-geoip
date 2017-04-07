<?php

namespace Confidences\ZendGeoip\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

/**
 * Factory of ZendGeoip\View\Helper\Geoip
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class GeoipFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName($container);
    }
}
