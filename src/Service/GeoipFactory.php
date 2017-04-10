<?php

namespace Confidences\ZendGeoip\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Hydrator\ClassMethods;
use Zend\Http\Request;
use Confidences\ZendGeoip\DatabaseConfig;
use Confidences\ZendGeoip\Entity\Record;
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
        $request = new Request();
        $config = $container->get(DatabaseConfig::class);
        $record = $container->get(Record::class);
        $hydrator = $container->get(ClassMethods::class);
        return new Geoip($request, $config, $record, $hydrator);
    }
}
