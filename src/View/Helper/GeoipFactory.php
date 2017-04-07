<?php

namespace Confidences\ZendGeoip\View\Helper;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Confidences\ZendGeoip\Service\Geoip as GeoipService;

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
        $geoipService = $container->get(GeoipService::class);
        return new $requestedName($geoipService);
    }
}
