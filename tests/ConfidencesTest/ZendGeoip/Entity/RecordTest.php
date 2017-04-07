<?php

namespace ConfidencesTest\ZendGeoip\Entity;

use Confidences\ZendGeoip\Entity\Record;
use ConfidencesTest\ZendGeoip\Util\ServiceManagerFactory;

/**
 * RecordTest
 *
 *
 */


class RecordTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Record
     */
    private $record;

    public function setUp()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $this->record = $serviceManager->get(Record::class);
    }

    public function testSetGetAreaCode()
    {
        $this->record->setAreaCode(33);
        $this->assertEquals(33, $this->record->getAreaCode());
    }

    public function testSetGetCity()
    {
        $this->record->setCity('Brest');
        $this->assertEquals('Brest', $this->record->getCity());
    }

    public function testSetGetContinentCode()
    {
        $this->record->setContinentCode('EU');
        $this->assertEquals('EU', $this->record->getContinentCode());
    }

    public function testSetGetCountryCode()
    {
        $this->record->setCountryCode('FR');
        $this->assertEquals('FR', $this->record->getCountryCode());
    }

    public function testSetGetCountryCode3()
    {
        $this->record->setCountryCode3('FRA');
        $this->assertEquals('FRA', $this->record->getCountryCode3());
    }

    public function testSetGetCountryName()
    {
        $this->record->setCountryName('France');
        $this->assertEquals('France', $this->record->getCountryName());
    }

    public function testSetGetDmaCode()
    {
        $this->record->setDmaCode(123);
        $this->assertEquals(123, $this->record->getDmaCode());
    }

    public function testSetGetLatitude()
    {
        $this->record->setLatitude(48);
        $this->assertEquals(48, $this->record->getLatitude());
    }

    public function testSetGetLongitude()
    {
        $this->record->setLongitude(48);
        $this->assertEquals(48, $this->record->getLongitude());
    }

    public function testSetGetMetroCode()
    {
        $this->record->setMetroCode(10);
        $this->assertEquals(10, $this->record->getMetroCode());
    }

    public function testSetGetPostalCode()
    {
        $this->record->setPostalCode(29200);
        $this->assertEquals(29200, $this->record->getPostalCode());
    }

    public function testSetGetRegion()
    {
        $this->record->setRegion('FR-E');
        $this->assertEquals('FR-E', $this->record->getRegion());
    }

    public function testSetGetRegionName()
    {
        $this->record->setRegionName('Bretagne');
        $this->assertEquals('Bretagne', $this->record->getRegionName());
    }

    public function testGetTimezone()
    {
        $this->assertEquals(\get_time_zone('France', 'Bretagne'), $this->record->getTimeZone());
    }

    public function testToStringEmpty()
    {
        $this->assertEquals('', $this->record->__toString());
    }

    public function testToString()
    {
        $this->record->setCity('Brest');
        $this->assertEquals($this->record->getCity(), $this->record->__toString());
    }
}
