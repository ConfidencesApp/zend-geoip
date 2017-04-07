<?php

namespace ConfidencesTest\ZendGeoip;

use Confidences\ZendGeoip\DatabaseConfig;
use ConfidencesTest\ZendGeoip\Util\ServiceManagerFactory;

/**
 * DatabaseConfigFactoryTest
 *
 *
 */

class DatabaseConfigFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactoryCreatesService()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $databaseConfig = $serviceManager->get(DatabaseConfig::class);
        $this->assertInstanceOf(DatabaseConfig::class, $databaseConfig);
    }
}
