<?php

namespace ConfidencesTest\ZendGeoip\Service;

use Confidences\ZendGeoip\DatabaseConfig;
use Confidences\ZendGeoip\Entity\Record;
use Zend\ServiceManager\ServiceManager;
use ConfidencesTest\ZendGeoip\Asset\GeoipAsset;
use Confidences\ZendGeoip\Service\Geoip;
use Confidences\ZendGeoip\IpAwareInterface;
use Confidences\ZendGeoip\Exception\DomainException;
use ConfidencesTest\ZendGeoip\Util\ServiceManagerFactory;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * ServiceGeoipTest
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class GeoipTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Geoip
     */
    protected $geoip;

    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * @var \GeoIP
     */
    protected $gip;

    /**
     * @var ServiceManagerFactory
     */
    protected $serviceManager;

    /**
     * @var array
     */
    protected $ip = array(
        'local' => '192.168.0.1',
        'google' => '216.239.51.99',
    );

    protected function setUp()
    {
        $this->serviceManager = ServiceManagerFactory::getServiceManager();
        $this->geoip = $this->serviceManager->get(Geoip::class);
        $this->reflection = new \ReflectionClass($this->geoip);
    }

    public function testValidTestDependencies()
    {
        $this->assertInstanceOf(Geoip::class, $this->geoip);
    }

    public function testGetEventManagerNull()
    {
        $this->assertInstanceOf(EventManager::class, $this->geoip->getEventManager());
    }

    public function testGetSetEventManager()
    {
        $event = $this->getMockBuilder(EventManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->geoip->setEventManager($event);
        $this->assertEquals($event, $this->geoip->getEventManager());
    }

    public function testGetGeoipNull()
    {
        $config = $this->getMockBuilder(DatabaseConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config->expects($this->once())
            ->method('getDatabasePath')
            ->will($this->returnValue(__DIR__ . '/../../../../data/GeoLiteCity.dat'));

        $config->expects($this->once())
            ->method('getFlag')
            ->will($this->returnValue(0));

        $reflection_property = $this->reflection->getProperty('config');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $config);

        $gip = geoip_open(__DIR__ . '/../../../../data/GeoLiteCity.dat', 0);

        $gep = $this->geoip->getGeoip();

        $this->assertEquals($gip->databaseSegments, $gep->databaseSegments);
    }

    /**
     * @expectedException DomainException
     */
    public function testGetGeoipFalse()
    {
        $config = $this->getMockBuilder(DatabaseConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config->expects($this->once())
            ->method('getDatabasePath')
            ->will($this->returnValue(null));

        $reflection_property = $this->reflection->getProperty('config');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $config);

        $this->geoip->getGeoip();
    }

    public function testGetGeoipRecord()
    {
        $record = array('192.168.0.1' => '192.168.0.1');

        $reflection_property = $this->reflection->getProperty('records');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $record);

        $ip = '192.168.0.1';
        $this->assertEquals($ip, $this->geoip->getGeoipRecord($ip));
    }

    public function testGetIpAwareInterface()
    {
        $ip = $this->getMockBuilder(IpAwareInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ip->expects($this->once())
            ->method('getIpAddress')
            ->will($this->returnValue('192.168.0.1'));

        $this->assertInstanceOf(Record::class, $this->geoip->getRecord($ip));
    }

    /**
     * @expectedException DomainException
     */
    public function testGetRecordInterfaceFalse()
    {
        $servMan = $this->getMockBuilder(ServiceManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $servMan->expects($this->once())
            ->method('get')
            ->will($this->returnValue(null));

        $reflection_property = $this->reflection->getProperty('serviceManager');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $servMan);

        $this->geoip->getRecord();
    }

    public function testGetRecordInterfaceGeoipCoreFalse()//ok
    {
        $sMan = new ServiceManager();
        $fakegeoip = new GeoipAsset($sMan);

        $servMan = $this->getMockBuilder(ServiceManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $servMan->expects($this->once())
            ->method('get')
            ->will($this->returnValue(new Record()));

        $reflection_property = $this->reflection->getProperty('serviceManager');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($fakegeoip, $servMan);
        $this->assertInstanceOf(Record::class, $fakegeoip->getRecord());
    }

    public function testLookup()
    {
        $this->assertInstanceOf(Record::class, $this->geoip->lookup());
    }

    public function testGetConfig()
    {
        $config = $this->getMockBuilder(DatabaseConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection_property = $this->reflection->getProperty('config');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $config);

        $config->expects($this->once())
            ->method('getDatabasePath')
            ->will($this->returnValue(__DIR__ . '/../../../../data/GeoLiteCity.dat'));

        $this->assertInstanceOf(Record::class, $this->geoip->getRecord());
    }

    public function testGetConfigNull()
    {
        $reflection_property = $this->reflection->getProperty('config');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, null);

        $this->assertInstanceOf(Record::class, $this->geoip->getRecord());
    }

    public function testGetDefaultIpNotNull()
    {
        $reflection_property = $this->reflection->getProperty('defaultIp');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, '192.168.0.1');

        $this->assertNull($this->geoip->getGeoipRecord(null));
    }

    public function testGetDefaultIpNullFalse()
    {
        $servMan = $this->getMockBuilder(ServiceManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $rec = $this->serviceManager->get('geoip_record');
        $conf = $this->serviceManager->get(DatabaseConfig::class);

        $servMan->expects($this->any())
            ->method('get')
            ->will($this->returnValue($rec));

        $reflection_property = $this->reflection->getProperty('serviceManager');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $servMan);

        $reflection_property = $this->reflection->getProperty('defaultIp');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, '192.168.0.1');

        $reflection_property = $this->reflection->getProperty('config');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $conf);

        $record = $this->serviceManager->get('geoip_record');

        $this->assertEquals($record, $this->geoip->getRecord());
    }

    /**
     * @expectedException DomainException
     */
    public function testGetRegionsNull()
    {
        $config = $this->getMockBuilder(DatabaseConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config->expects($this->any())
            ->method('getRegionVarsPath')
            ->will($this->returnValue(null));

        $reflection_property = $this->reflection->getProperty('config');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $config);

        $reflection_property = $this->reflection->getProperty('regions');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, null);

        $this->geoip->getRecord();
    }

    public function testGetRegionsNotNull()
    {
        $regions = array();

        $reflection_property = $this->reflection->getProperty('regions');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $regions);

        $this->assertInstanceOf(Record::class, $this->geoip->getRecord());
    }
}
