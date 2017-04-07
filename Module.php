<?php
namespace Confidences\ZendGeoip;

use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\EventInterface;
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\StandardAutoloader;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface as Autoloader;
use Zend\ModuleManager\Feature\BootstrapListenerInterface as BootstrapListener;
use Zend\ModuleManager\Feature\ConfigProviderInterface as Config;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface as ConsoleUsage;

/**
 * Geoip module
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class Module implements ConsoleUsage, Config, Autoloader, BootstrapListener
{
    const CONSOLE_GEOIP_DOWNLOAD = 'geoip download [--no-clobber] [-q]';

    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return [
            AutoloaderFactory::STANDARD_AUTOLOADER => [
                StandardAutoloader::LOAD_NS => [
                    __NAMESPACE__ => __DIR__ . '/src/',
                ]
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @param AdapterInterface $console
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return array(
            'Manage GeoIP database',
            self::CONSOLE_GEOIP_DOWNLOAD => 'Downloads the newest GeoIP db',
            array('--no-clobber', 'Don\'t overwrite an existing db file'),
            array('-q', 'Turn off output'),
        );
    }

    /**
     * @param \Zend\Mvc\MvcEvent $event
     * @codeCoverageIgnore
     */
    public function onBootstrap(EventInterface $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();
        $config = $serviceManager->get('config');

        require_once $config['maxmind']['timezone_function_path'];
    }
}