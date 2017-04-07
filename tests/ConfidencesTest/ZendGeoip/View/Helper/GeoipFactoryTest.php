<?php

namespace ConfidencesTest\ZendGeoip\View\Helper;

use Confidences\ZendGeoip\Service\Geoip;
use ConfidencesTest\ZendGeoip\Util\ServiceManagerFactory;

class GeoipFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactoryCreatesService()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $geoip = $serviceManager->get(Geoip::class);
        $this->assertInstanceOf(Geoip::class, $geoip);
    }
}
