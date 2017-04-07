<?php

namespace ConfidencesTest\ZendGeoip\Asset;

use Confidences\ZendGeoip\Controller\ConsoleController;
use Zend\Http\Client\Exception\RuntimeException;

/**
 * ConsoleControllerDBAsset
 *
 *
 */
class ConsoleControllerDBAsset extends ConsoleController
{
    public function getDbResponse()
    {
        throw new RuntimeException("RuntimeException test");
    }
}
