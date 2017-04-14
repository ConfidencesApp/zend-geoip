<?php

namespace ConfidencesTest\ZendGeoip\Controller;

use Confidences\ZendGeoip\Controller\ConsoleController;
use ConfidencesTest\ZendGeoip\Asset\ConsoleControllerAsset;
use Confidences\ZendGeoip\DatabaseConfig;
use ConfidencesTest\ZendGeoip\Asset\ConsoleControllerDBAsset;
use ConfidencesTest\ZendGeoip\Asset\ConsoleControllerDBResponseAsset;
use ConfidencesTest\ZendGeoip\Asset\ConsoleControllerDBStatusAsset;
use ConfidencesTest\ZendGeoip\Util\ServiceManagerFactory;
use Confidences\ZendGeoip\HttpClientFactory;
use Zend\Console\ColorInterface as Color;
use Zend\Console;
use Zend\Http;
use Zend\Log\Exception\RuntimeException;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Router\RouteMatch;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

/**
 * ConsoleControllerTest
 *
 *
 */
class ConsoleControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConsoleController
     */
    protected $consoleController;

    /**
     * @var ConsoleControllerAsset
     */
    protected $fakeconsoleController;

    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * @var AdapterInterface
     */
    protected $console;

    /**
     * @var DatabaseConfig
     */
    protected $config;

    /**
     * @var Http\Client
     */
    protected $client;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    protected function setUp()
    {
        $this->console = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = array('source' => 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz'
        ,'destination' => __DIR__ . '/../../../../data/'
        ,'filename' => 'GeoLiteCity.dat'
        ,'flag' => GEOIP_STANDARD
        ,'regionvars' => __DIR__ . '/../../../geoip/geoip/src/geoipregionvars.php'
        );
        $this->config = new DatabaseConfig($data);

        $serviceManager = ServiceManagerFactory::getServiceManager();
        $this->client = $serviceManager->get(HttpClientFactory::class);

        $this->fakeconsoleController = new ConsoleControllerAsset($this->console, $this->config, $this->client);
        $this->consoleController = new ConsoleController($this->console, $this->config, $this->client);
        $this->reflection = new \ReflectionClass($this->consoleController);
    }

    public function testGetConsole()
    {
        $this->assertEquals($this->console, $this->consoleController->getConsole());
    }

    public function testDispatchTrue()
    {
        $this->request = $this->getMockBuilder(Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routeMatch = $this->getMockBuilder(RouteMatch::class)
            ->disableOriginalConstructor()
            ->getMock();

        $e = $this->consoleController->getEvent();
        $e->setName(MvcEvent::EVENT_DISPATCH);
        $e->setRequest($this->request);
        $e->setResponse($this->response);
        $e->setRouteMatch($routeMatch);
        $e->setTarget($this);

        $this->assertInstanceOf(ViewModel::class, $this->consoleController
            ->dispatch($this->request));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testDispatchFalse()
    {
        $this->request = $this->getMockBuilder(Http\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->consoleController->dispatch($this->request);
    }

    public function testDatabaseAlreadyExist()
    {
        $this->request = $this->getMockBuilder(Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue('no-clobber'));

        $reflection_property = $this->reflection->getProperty('request');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->request);

        $this->config = $this->getMockBuilder(DatabaseConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config->expects($this->any())
            ->method('getDatabasePath')
            ->will($this->returnValue(__DIR__ . '/../../../../data/GeoLiteCity.dat'));

        $reflection_property = $this->reflection->getProperty('config');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->config);

        $this->assertNull($this->consoleController->downloadAction());
    }

    public function testDatabaseRuntime()
    {
        $fakeConsoleController = new ConsoleControllerDBAsset($this->console, $this->config, $this->client);

        $reflection_property = $this->reflection->getProperty('httpClient');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($fakeConsoleController, $this->client);

        $this->request = $this->getMockBuilder(Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection_property = $this->reflection->getProperty('request');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($fakeConsoleController, $this->request);

        $this->assertNull($fakeConsoleController->downloadAction());
    }

    public function testDatabaseDownloadFalse()
    {
        $this->config = $this->getMockBuilder(DatabaseConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config->expects($this->once())
            ->method('getSource')
            ->will($this->returnValue('test.txt.gz'));

        $this->console->expects($this->any())
            ->method('writeLine')
            ->will($this->returnValue('oui'));

        $reflection_property = $this->reflection->getProperty('console');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->console);

        $reflection_property = $this->reflection->getProperty('config');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->config);

        $this->request = $this->getMockBuilder(Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection_property = $this->reflection->getProperty('request');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->request);

        $this->client = $this->getMockBuilder(\Zend\Http\Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->client->expects($this->once())
            ->method('send')
            ->will($this->returnValue(null));

        $reflection_property = $this->reflection->getProperty('httpClient');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->client);

        $this->assertNull($this->consoleController->downloadAction());
    }

    public function testDatabaseDownloadTrue()
    {
        $this->config = $this->getMockBuilder(DatabaseConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config->expects($this->once())
            ->method('getSource')
            ->will($this->returnValue('https://wiki.mozilla.org/images/f/ff/Example.json.gz'));

        $this->config->expects($this->once())
            ->method('getDatabasePath')
            ->will($this->returnValue(__DIR__ . '/../Asset/example.json'));

        $reflection_property = $this->reflection->getProperty('config');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->config);

        $this->console->expects($this->any())
            ->method('writeLine')
            ->will($this->returnValue('oui'));

        $reflection_property = $this->reflection->getProperty('console');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->console);

        $this->request = $this->getMockBuilder(Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection_property = $this->reflection->getProperty('request');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->request);

        $this->assertNull($this->consoleController->downloadAction());
    }

    public function testGetDbResponse()
    {
        $this->config = $this->getMockBuilder(DatabaseConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config->expects($this->once())
            ->method('getSource')
            ->will($this->returnValue('http://speedtest.ftp.otenet.gr/files/test100k.db'));

        $reflection_property = $this->reflection->getProperty('config');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->config);

        $this->request = $this->getMockBuilder(Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection_property = $this->reflection->getProperty('request');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->request);

        $this->assertNotNull($this->consoleController->getDbResponse());
    }

    public function testWriteLine()
    {
        $this->request = $this->getMockBuilder(Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection_property = $this->reflection->getProperty('request');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->fakeconsoleController, $this->request);

        $param = array('text' => 'WriteLine', 'color' => null, 'bgColor' => null);
        $this->assertEquals($param, $this->fakeconsoleController->writeLine('WriteLine'));
    }

    public function testWriteLineFalse()
    {
        $this->request = $this->getMockBuilder(Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection_property = $this->reflection->getProperty('isQuiet');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->fakeconsoleController, true);

        $reflection_property = $this->reflection->getProperty('request');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->fakeconsoleController, $this->request);

        $this->assertNull($this->fakeconsoleController->writeLine('WriteLine'));
    }

    public function testWriteLineError()
    {
        $this->request = $this->getMockBuilder(Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection_property = $this->reflection->getProperty('request');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->fakeconsoleController, $this->request);

        $param = array('text' => 'WriteLineError', 'color' => Color::WHITE, 'bgColor' => Color::RED);
        $this->assertEquals($param, $this->fakeconsoleController->writeLineError('WriteLineError'));
    }

    public function testWriteLineSuccess()
    {
        $this->request = $this->getMockBuilder(Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection_property = $this->reflection->getProperty('request');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->fakeconsoleController, $this->request);

        $param = array('text' => 'WriteLineSuccess', 'color' => Color::LIGHT_GREEN, 'bgColor' => null);
        $this->assertEquals($param, $this->fakeconsoleController->writeLineSuccess('WriteLineSuccess'));
    }

    public function testIsQuietModeNull()
    {
        $this->request = $this->getMockBuilder(Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection_property = $this->reflection->getProperty('request');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, $this->request);

        $reflection_property = $this->reflection->getProperty('isQuiet');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, null);

        $this->assertFalse($this->consoleController->isQuietMode());
    }

    public function testIsQuietMode()
    {
        $reflection_property = $this->reflection->getProperty('isQuiet');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->consoleController, false);

        $this->assertFalse($this->consoleController->isQuietMode());
    }
}
