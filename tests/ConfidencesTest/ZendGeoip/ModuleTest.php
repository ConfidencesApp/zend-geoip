<?php
namespace ConfidencesTest\ZendGeoip;

use Confidences\ZendGeoip\Module;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Loader;

class ModuleTest extends \PHPUnit\Framework\TestCase
{
    const CONSOLE_GEOIP_DOWNLOAD = 'geoip download [--no-clobber] [-q]';

    public function testModuleProvidesConfig()
    {
        $module = new Module;
        $config = $module->getConfig();

        $this->assertEquals('array', gettype($config));
    }

    public function testModuleAutoloader()
    {
        $module   = new Module;
        $actual   = $module->getAutoloaderConfig();
        $expected = [
            Loader\AutoloaderFactory::STANDARD_AUTOLOADER => [
                Loader\StandardAutoloader::LOAD_NS => [
                    'Confidences\ZendGeoip' => realpath(__DIR__ . '/../../../src') . '/',
                ],
            ],
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testGetConsoleUsage()
    {
        $module = new Module;
        $console = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $usage = $module->getConsoleUsage($console);
        $consoleUsage = ['Manage GeoIP database',
            self::CONSOLE_GEOIP_DOWNLOAD => 'Downloads the newest GeoIP db',
            ['--no-clobber', 'Don\'t overwrite an existing db file'],
            ['-q', 'Turn off output'],
        ];
        $this->assertEquals($consoleUsage, $usage);
    }
}
