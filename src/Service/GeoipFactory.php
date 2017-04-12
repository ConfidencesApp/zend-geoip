<?php

namespace Confidences\ZendGeoip\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Hydrator\ClassMethods;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Confidences\ZendGeoip\DatabaseConfig;
use Interop\Container\ContainerInterface;
use geoiprecord as GeoipCoreRecord;

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
        $request = new HttpRequest();
        $config = $container->get(DatabaseConfig::class);
        $records = array(new GeoipCoreRecord());
        $hydrator = $container->get(ClassMethods::class);
        return new Geoip($request, $config, $records, $hydrator);
    }
}
