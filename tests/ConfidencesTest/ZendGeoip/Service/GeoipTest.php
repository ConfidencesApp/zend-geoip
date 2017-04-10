<?php

namespace ConfidencesTest\ZendGeoip\Service;

use Confidences\ZendGeoip\DatabaseConfig;
use Confidences\ZendGeoip\Entity\Record;
use Zend\Http\Request;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Hydrator\ClassMethods;
use Confidences\ZendGeoip\Service\Geoip;
use Confidences\ZendGeoip\IpAwareInterface;
use Confidences\ZendGeoip\Exception\DomainException;
use ConfidencesTest\ZendGeoip\Util\ServiceManagerFactory;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use geoiprecord as GeoipCoreRecord;

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
     * @var GeoipCoreRecord[]
     */
    protected $records;

    /**
     * @var ClassMethods
     */
    protected $hydrator;

    /**
     * @var \ReflectionClass
     */
    protected $reflection;

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
        ,'regionvars' => __DIR__ . '/../../../../vendor/geoip/geoip/src/geoipregionvars.php'
        );
        $this->config = new DatabaseConfig($data);
        $this->records = array(new GeoipCoreRecord());
        $this->hydrator = new ClassMethods();

        $this->geoip = new Geoip($this->request, $this->config, $this->records, $this->hydrator);
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
        $records = array('192.168.0.1' => '192.168.0.1');

        $reflection_property = $this->reflection->getProperty('records');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $records);

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

    public function testGetRecordTrue()
    {
        $this->assertNotNull($this->geoip->getRecord('216.239.51.99'));
    }

    public function testGetRecordInterfaceGeoipCoreFalse()
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
        $this->assertInstanceOf(Record::class, $this->geoip->getRecord());
    }

    public function testGetDefaultIpNullTrue()
    {
        $request = $this->getMockBuilder(HttpRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection_property = $this->reflection->getProperty('request');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $request);

        $this->assertInstanceOf(Record::class, $this->geoip->getRecord());
    }

    /**
     * @expectedException DomainException
     */
    public function testGetRegionsNullFalse()
    {
        $gip = $this->geoip->getGeoip();

        $reflection_property = $this->reflection->getProperty('geoip');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $gip);

        $config = $this->getMockBuilder(DatabaseConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config->expects($this->any())
            ->method('getRegionVarsPath')
            ->will($this->returnValue(__DIR__ . '/../../../../public/index.php'));

        $reflection_property = $this->reflection->getProperty('config');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $config);

        $reflection_property = $this->reflection->getProperty('regions');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, null);

        $this->geoip->getRecord('216.239.51.99');
    }

    public function testGetRegionsNullTrue()
    {
        $this->assertInstanceOf(Record::class, $this->geoip->getRecord('216.239.51.99'));
    }

    public function testGetRegionsNotNull()
    {
        $regions = array();

        $reflection_property = $this->reflection->getProperty('regions');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->geoip, $regions);

        $this->assertInstanceOf(Record::class, $this->geoip->getRecord('216.239.51.99'));
    }
}
