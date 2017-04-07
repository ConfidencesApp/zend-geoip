<?php

namespace ConfidencesTest\ZendGeoip\Controller;

use Confidences\ZendGeoip\Controller\ConsoleControllerFactory;
use Interop\Container\ContainerInterface;

/**
 * ConsoleControllerFactoryTest
 *
 *
 */

class ConsoleControllerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactoryCreatesService()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $consoleControllerFactory = new ConsoleControllerFactory();
        $consoleController = $consoleControllerFactory
            ->__invoke($container, ConsoleControllerFactory::class);
        $this->assertInstanceOf(ConsoleControllerFactory::class, $consoleController);
    }
}
