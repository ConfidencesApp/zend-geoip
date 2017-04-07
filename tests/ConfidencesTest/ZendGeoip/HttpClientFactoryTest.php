<?php

namespace ConfidencesTest\ZendGeoip;

use ConfidencesTest\ZendGeoip\Util\ServiceManagerFactory;
use Zend\Http\Client;

/**
 * HttpClientFactoryTest
 *
 *
 */


class HttpClientFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactoryCreatesService()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $httpClient = $serviceManager->get('HttpClient');
        $this->assertInstanceOf(Client::class, $httpClient);
    }
}
