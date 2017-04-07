<?php

namespace Confidences\ZendGeoip;

/**
 * IPable iterface
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
interface IpAwareInterface
{

    /**
     * @return string IP address
     */
    public function getIpAddress();
}
