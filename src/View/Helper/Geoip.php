<?php

namespace Confidences\ZendGeoip\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Confidences\ZendGeoip\Service\Geoip as GeoipService;

/**
 * Geoip view helper
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class Geoip extends AbstractHelper
{
    /**
     * @var GeoipService
     */
    private $geoip;

    /**
     * Geoip view helper constructor.
     * @param GeoipService $geoip
     */
    public function __construct(GeoipService $geoip)
    {
        $this->geoip = $geoip;
    }
    
    /**
     * @param string $ipAddress
     * @return GeoipService
     */
    public function __invoke($ipAddress = null)
    {
        return $this->geoip->getRecord($ipAddress);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->geoip->getRecord();
    }
}
