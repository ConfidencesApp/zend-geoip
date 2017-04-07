<?php

namespace Confidences\ZendGeoip;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Http\Client;

class HttpClientFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $adapter \Zend\Http\Client\Adapter\AdapterInterface */
        $adapter = $container->get('Confidences\ZendGeoip\HttpClient\Adapter');

        $config = $container->get('config');
        $options = $config['maxmind']['http_client']['options'];

        $client = new Client();
        $client->setAdapter($adapter);
        $client->setOptions($options);

        return $client;
    }
}
