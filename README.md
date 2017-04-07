ZendGeoip [![Build Status](https://travis-ci.org/snapshotpl/ZendGeoip.png?branch=master)](https://travis-ci.org/snapshotpl/ZendGeoip) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/snapshotpl/ZendGeoip/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/snapshotpl/ZendGeoip/?branch=master)
===========

Maxmind Geoip module for Zend Framework 2

Created by Witold Wasiczko

Usage
-----
Default ZendGeoip returns Record object created by current user's IP address.

**In controller:**

```php
$record = $this->getServiceLocator()->get('geoip')->getRecord();
echo $record->getCity();
```

```php
$record = $this->getServiceLocator()->get('geoip')->getRecord('216.239.51.99');
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

Avaliable getter methods via `\ZendGeoip\Entity\Record`:
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


How to install?
---------------
Via [composer.json](https://getcomposer.org/)
```json
{
    "require": {
        "snapshotpl/zf-snap-geoip": "2.*"
    }
}
```

and add `ZendGeoip` module name to application.config.php

To download data file from http://dev.maxmind.com/geoip/legacy/geolite/ use `Zend\Console` (you can add this to crontab):
```
php index/public.php geoip download
```
Or use autoupdate database during install/update in composer (just add this lines to composer.json and run composer):
```json
{
    "scripts": {
        "post-install-cmd": [
            "ZendGeoip\\Composer\\ScriptHandler::downloadData"
        ],
        "post-update-cmd": [
            "ZendGeoip\\Composer\\ScriptHandler::downloadData"
        ]
    }
}
```

Console usage
-------------
You can download GeoIP database from application console:
```
php public/index.php geoip download
```
There are optional parameters:
* `--no-clobber` Don't overwrite an existing db file,
* `-q` Turn off output,
