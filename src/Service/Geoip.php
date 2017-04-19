<?php

namespace Confidences\ZendGeoip\Service;

use Confidences\ZendGeoip\DatabaseConfig;
use Confidences\ZendGeoip\Entity\Record;
use Confidences\ZendGeoip\Entity\RecordInterface;
use Confidences\ZendGeoip\Exception\DomainException;
use Confidences\ZendGeoip\IpAwareInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Hydrator\ClassMethods;
use geoiprecord as GeoipCoreRecord;

/**
 * Geoip Service
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class Geoip implements EventManagerAwareInterface
{
    /**
     * @var \GeoIP
     */
    private $geoip;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * @var DatabaseConfig
     */
    private $config;

    /**
     * @var ClassMethods
     */
    private $hydrator;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var GeoipCoreRecord[]
     */
    private $records;

    /**
     * @var string|bool
     */
    private $defaultIp;
    /**
     * @var array
     */
    private $regions;

    /**
     * Geoip constructor
     * HttpRequest $request, DatabaseConfig $config, Array $records, ClassMethods $hydrator
     */
    public function __construct(HttpRequest $request, DatabaseConfig $config, array $records, ClassMethods $hydrator)
    {
        $this->request = $request;
        $this->config = $config;
        $this->records = $records;
        $this->hydrator = $hydrator;
    }

    /**
     * Destructor
     * @codeCoverageIgnore
     */
    public function __destruct()
    {
        $this->closeGeoip();
    }

    /**
     * @return \GeoIP
     */
    public function getGeoip()
    {
        if (!$this->geoip) {
            $database = $this->config->getDatabasePath();
            if (file_exists($database)) {
                $this->geoip = geoip_open($database, $this->config->getFlag());
            } else {
                throw new DomainException('You need to download Maxmind database. 
                You can use ZFTool or composer.json for that :)');
            }
        }
        return $this->geoip;
    }

    /**
     * @param string $ipAddress
     * @return GeoipCoreRecord
     */
    public function getGeoipRecord($ipAddress)
    {
        $ipAddress = $this->getIp($ipAddress);
        if (!isset($this->records[$ipAddress])) {
            $this->records[$ipAddress] = GeoIP_record_by_addr($this->getGeoip(), $ipAddress);
        }
        return $this->records[$ipAddress];
    }

    /**
     * @param string $ipAddress
     * @return string
     */
    private function getIp($ipAddress)
    {
        if ($ipAddress === null) {
            $ipAddress = $this->getDefaultIp();
        }

        if ($ipAddress instanceof IpAwareInterface) {
            $ipAddress = $ipAddress->getIpAddress();
        }
        $this->getEventManager()->trigger(__FUNCTION__, $this, [
            'ip' => $ipAddress,
        ]);

        return $ipAddress;
    }

    /**
     * @param string $ipAdress
     * @return RecordInterface
     */
    public function getRecord($ipAdress = null)
    {
        $record = new Record();
        /* @var $record RecordInterface */

        $geoipRecord = $this->getGeoipRecord($ipAdress);

        if (!$geoipRecord instanceof GeoipCoreRecord) {
            return $record;
        }

        $data = get_object_vars($geoipRecord);
        $data['region_name'] = $this->getRegionName($data);

        $hydrator = $this->hydrator;
        /* @var $hydrator \Zend\Hydrator\HydratorInterface */

        $hydrator->hydrate($data, $record);

        $this->getEventManager()->trigger(__FUNCTION__, $this, [
            'record' => $record,
        ]);

        return $record;
    }

    /**
     * @param string $ipAddress
     * @return RecordInterface
     */
    public function lookup($ipAddress = null)
    {
        return $this->getRecord($ipAddress);
    }

    /**
     * @return self
     * @codeCoverageIgnore
     */
    private function closeGeoip()
    {
        if ($this->geoip) {
            geoip_close($this->geoip);
            $this->geoip = null;
        }
        return $this;
    }

    /**
     * @return array
     */
    private function getRegions()
    {
        if ($this->regions === null) {
            $regionVarPath = $this->config->getRegionVarsPath();
            include($regionVarPath);
            if (!isset($GEOIP_REGION_NAME)) {
                throw new DomainException(sprintf('Missing region names data in path %s', $regionVarPath));
            }

            $this->regions = $GEOIP_REGION_NAME;

            $this->getEventManager()->trigger(__FUNCTION__, $this, [
                'regions' => $this->regions,
            ]);
        }
        return $this->regions;
    }

    /**
     * @return string|null
     */
    private function getDefaultIp()
    {
        if ($this->defaultIp === null) {
            $request = $this->request;

            if ($request instanceof HttpRequest) {
                $ipAddress = $request->getServer('REMOTE_ADDR', false);
                $this->defaultIp = $ipAddress;
            } else {
                $this->defaultIp = false;
                return null;
            }
        }
        return $this->defaultIp;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getRegionName(array $data = [])
    {
        $regions = $this->getRegions();
        $countryCode = isset($data['country_code']) ? $data['country_code'] : null;

        if (isset($regions[$countryCode])) {
            $regionCodes = $regions[$countryCode];
            $regionCode = isset($data['region']) ? $data['region'] : null;

            if (isset($regionCodes[$regionCode])) {
                return $regionCodes[$regionCode];
            }
        }
        return null;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if ($this->eventManager === null) {
            $this->eventManager = new EventManager();
        }
        return $this->eventManager;
    }

    /**
     * @param EventManagerInterface $eventManager
     * @return self
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers([
            __CLASS__,
            get_called_class(),
        ]);
        $this->eventManager = $eventManager;

        return $this;
    }
}
