# Confidences ZendGeoip 

[![Build Status](https://travis-ci.org/ConfidencesApp/zend-geoip.svg?branch=master)](https://travis-ci.org/ConfidencesApp/zend-geoip) 
[![Latest Stable Version](https://poser.pugx.org/ConfidencesApp/zend-geoip/v/stable)](https://packagist.org/packages/ConfidencesApp/zend-geoip)
[![Coverage Status](https://coveralls.io/repos/github/ConfidencesApp/zend-geoip/badge.svg?branch=master)](https://coveralls.io/github/ConfidencesApp/zend-geoip?branch=master)
===========

Maxmind Geoip module for Zend Framework 3

Installation
---------------
This module is available on [Github](https://github.com/ConfidencesApp/zend-geoip).
Via [composer.json](https://getcomposer.org/)
```json
{
    "require": {
        "confidencesapp/zend-geoip": "dev-master"
    }
}
```

and add `Confidences\ZendGeoip` module name to application.config.php

To download data file from http://dev.maxmind.com/geoip/legacy/geolite/ use `Zend\Console` (you can add this to crontab):
```
php index/public.php geoip download
```
Or use autoupdate database during install/update in composer (just add this lines to composer.json and run composer):
```json
{
    "scripts": {
        "post-install-cmd": [
            "Confidences\\ZendGeoip\\Composer\\ScriptHandler::downloadData"
        ],
        "post-update-cmd": [
            "Confidences\\ZendGeoip\\Composer\\ScriptHandler::downloadData"
        ]
    }
}
```
Add `Confidences\ZendGeoip` to the modules array in your `application.config.php`, preferably as the first module. 

Console usage
-------------
You can download GeoIP database from application console:
```
php public/index.php geoip download
```
There are optional parameters:
* `--no-clobber` Don't overwrite an existing db file,
* `-q` Turn off output,


Usage
-----
Default ZendGeoip returns Record object created by current user's IP address.

**In controller:**

```php
$record = $this->getServiceManager()->get(Geoip::class)->getRecord();
echo $record->getCity();
```

```php
$record = $this->getServiceManager()->get(Geoip::class)->getRecord('216.239.51.99');
echo $record->getLongitude();
echo $record->getLatitude();
```

**By view helper:**

Returns city name for current IP:
```php
<?php echo $this->geoip() ?>
```
Returns country name for given IP:
```php
<?php echo $this->geoip('184.106.35.179')->getCountryName() ?>
```

You can also implements `\ZendGeoip\IpAwareInterface` interface and then use instance in service/helper:
```php
<?php echo $this->geoip($user)->getTimezone() ?>
```

Available getter methods via `\ZendGeoip\Entity\Record`:
```
getAreaCode()
getCity()
getContinentCode()
getCountryCode()
getCountryCode3()
getCountryName()
getDmaCode()
getLatitude()
getLongitude()
getMetroCode()
getPostalCode()
getRegion()
getRegionName()
getTimezone()
```

Events
------

Module supports `\Zend\EventManager`.

Class | Event name | Description | Params
--- | --- | --- | ---
ZendGeoip\Controller\ConsoleController | downloadAction.exists | If no-clobber is enabled and file exists | path (to dat file)
ZendGeoip\Controller\ConsoleController | downloadAction.pre | Before unzip file | path (to dat file), response (gziped response object)
ZendGeoip\Controller\ConsoleController | downloadAction.post | After unzip file | path (to dat file)
ZendGeoip\Service\Geoip | getIp | After read IP | ip (ip address)
ZendGeoip\Service\Geoip | getRecord | After created record | record (instance of ZendGeoip\Entity\RecordInterface)
ZendGeoip\Service\Geoip | getRegions | After first loading regions names | regions
