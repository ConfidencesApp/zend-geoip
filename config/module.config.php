<?php
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Hydrator\ClassMethods;
use Zend\Http\Client\Adapter\Curl;
use Confidences\ZendGeoip\Controller;
use Confidences\ZendGeoip\DatabaseConfig;
use Confidences\ZendGeoip\DatabaseConfigFactory;
use Confidences\ZendGeoip\Entity;
use Confidences\ZendGeoip\HttpClientFactory;
use Confidences\ZendGeoip\Service;
use Confidences\ZendGeoip\View\Helper;

return [
    'maxmind' => [
        'database' => [
            'source' => 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz',
            'destination' => __DIR__ . '/../data/',
            'filename' => 'GeoLiteCity.dat',
            'flag' => GEOIP_STANDARD,
            'regionvars' => __DIR__ . '/../../../geoip/geoip/src/geoipregionvars.php',
        ],
        'http_client' => [
            'options' => [
                'timeout' => 300,
            ],
        ],
        'timezone_function_path' => __DIR__ . '/../../../geoip/geoip/src/timezone.php',
    ],
    'service_manager' => [
        'invokables' => [
            'Confidences\ZendGeoip\HttpClient\Adapter' => Curl::class,
        ],
        'factories' => [
            ClassMethods::class => InvokableFactory::class,
            Entity\Record::class => InvokableFactory::class,
            Service\Geoip::class => Service\GeoipFactory::class,
            DatabaseConfig::class => DatabaseConfigFactory::class,
            HttpClientFactory::class => HttpClientFactory::class
        ],
        'shared' => [
            Entity\Record::class => false
        ],
    ],
    'view_helpers' => [
        'factories' => [
            Helper\Geoip::class => Helper\GeoipFactory::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\ConsoleController::class => Controller\ConsoleControllerFactory::class,
        ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'geoip-download' => [
                    'options' => [
                        'route' => Confidences\ZendGeoip\Module::CONSOLE_GEOIP_DOWNLOAD,
                        'defaults' => [
                            'controller' => Controller\ConsoleController::class,
                            'action' => 'download',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
