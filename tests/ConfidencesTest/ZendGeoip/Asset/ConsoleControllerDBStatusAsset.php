<?php

namespace ConfidencesTest\ZendGeoip\Asset;

use Confidences\ZendGeoip\Controller\ConsoleController;
use Zend\Http\Response;

/**
 * ConsoleControllerDBAsset
 *
 *
 */
class ConsoleControllerDBStatusAsset extends ConsoleController
{
    public function getDbResponse()
    {
        $response = new Response();
        $response->setStatusCode(201);
        return $response;
    }
}
