<?php

namespace ConfidencesTest\ZendGeoip\View\Helper;

use Confidences\ZendGeoip\View\Helper\Geoip;
use Confidences\ZendGeoip\Entity\RecordInterface;
use Confidences\ZendGeoip\Service\Geoip as GeoipService;
use ConfidencesTest\ZendGeoip\Util\ServiceManagerFactory;

class GeoipTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var GeoipService
     */
    protected $geoipService;

    /**
     * @var Geoip
     */
    protected $geoip;

    protected function setUp()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $this->geoip = $serviceManager->get('ViewHelperManager')->get(Geoip::class);
        $this->geoipService = $serviceManager->get(GeoipService::class);
    }

    public function testShowRecordFromViewHelper()
    {
        $result = $this->geoip->__invoke('216.239.51.99');

        $this->assertInstanceOf(RecordInterface::class, $result);
    }

    public function testToString()
    {
        $this->assertEquals((string)$this->geoipService->getRecord(), $this->geoip->__toString());
    }
}
