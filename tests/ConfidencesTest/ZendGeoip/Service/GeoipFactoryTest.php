<?php

namespace ConfidencesTest\ZendGeoip\Service;

use Confidences\ZendGeoip\Service\Geoip;
use ConfidencesTest\ZendGeoip\Util\ServiceManagerFactory;

/**
 * GeoipFactoryTest
 *
 *
 */

class GeoipFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactoryCreatesService()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $geoip = $serviceManager->get(Geoip::class);
        $this->assertInstanceOf(Geoip::class, $geoip);
    }
}
