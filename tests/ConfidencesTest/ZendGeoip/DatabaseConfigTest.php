<?php

namespace ConfidencesTest\ZendGeoip;

use Confidences\ZendGeoip\DatabaseConfig;
use ReflectionClass;

class DatabaseConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DatabaseConfig
     */
    protected $databaseConfig;

    /**
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * @var array
     */
    protected $data;

    public function setUp()
    {
        $this->data = ['source' => 'geolite'
            ,'destination' => 'destination'
            ,'filename' => 'GeoLiteCity.dat'
            ,'flag' => 0
            ,'regionvars' => 'RegionVarsPath'
        ];
        $this->databaseConfig = new DatabaseConfig($this->data);
        $this->reflection = new ReflectionClass($this->databaseConfig);
    }

    public function testGetSource()
    {
        $this->assertEquals('geolite', $this->databaseConfig->getSource());
    }

    public function testGetSourceBasename()
    {
        $this->assertEquals(basename($this->databaseConfig->getSource()), $this->databaseConfig->getSourceBasename());
    }

    public function testGetDestination()
    {
        $this->assertEquals('destination', $this->databaseConfig->getDestination());
    }

    public function testGetFilename()
    {
        $this->assertEquals('GeoLiteCity.dat', $this->databaseConfig->getFilename());
    }

    public function testGetFlag()
    {
        $this->assertEquals(0, $this->databaseConfig->getFlag());
    }

    public function testGetRegionVarsPath()
    {
        $this->assertEquals('RegionVarsPath', $this->databaseConfig->getRegionVarsPath());
    }

    public function testGetDatabasePath()
    {
        $this->assertEquals($this->databaseConfig->getDestination()
            . $this->databaseConfig->getFilename(), $this->databaseConfig->getDatabasePath());
    }
}
