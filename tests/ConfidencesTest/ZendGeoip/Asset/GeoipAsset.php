<?php

namespace ConfidencesTest\ZendGeoip\Asset;

use Confidences\ZendGeoip\Service\Geoip;
use Confidences\ZendGeoip\Entity\Record;

/**
 * GeoipAsset
 *
 *
 */
class GeoipAsset extends Geoip
{
    public function getGeoipRecord($ipAddress)
    {
        return new Record();
    }
}
