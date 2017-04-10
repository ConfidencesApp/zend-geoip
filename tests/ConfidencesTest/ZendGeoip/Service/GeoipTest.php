<?php

namespace ConfidencesTest\ZendGeoip\Service;

use Confidences\ZendGeoip\DatabaseConfig;
use Confidences\ZendGeoip\Entity\Record;
use Zend\Http\Request;
use Zend\Hydrator\ClassMethods;
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
     * @var Request
     */
    protected $request;

    /**
     * @var DatabaseConfig
     */
    protected $config;

    /**
     * @var Record
     */
    protected $record;

    /**
     * @var ClassMethods
     */
    protected $hydrator;

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

        $this->request = new Request();
        $data = array('source' => 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz'
        ,'destination' => __DIR__ . '/../../../../data/'
        ,'filename' => 'GeoLiteCity.dat'
        ,'flag' => GEOIP_STANDARD
        ,'regionvars' => __DIR__ . '/../../../geoip/geoip/src/geoipregionvars.php'
        );
        $this->config = new DatabaseConfig($data);
        $this->record = $this->serviceManager->get(Record::class);
        $this->hydrator = new ClassMethods();

        $this->geoip = new Geoip($this->request, $this->config, $this->record, $this->hydrator);
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
        $reflection_property = $this->reflection->getProperty('record');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, null);

        $this->geoip->getRecord();
    }

    public function testGetRecordInterfaceGeoipCoreFalse()//ok
    {
        $this->assertInstanceOf(Record::class, $this->geoip->getRecord());
    }

    public function testLookup()
    {
        $this->assertInstanceOf(Record::class, $this->geoip->lookup());
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
        $reflection_property = $this->reflection->getProperty('defaultIp');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, '192.168.0.1');

        $this->assertEquals($this->record, $this->geoip->getRecord());
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
