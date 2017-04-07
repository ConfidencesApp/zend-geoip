<?php

namespace ConfidencesTest\ZendGeoip\Asset;

use Confidences\ZendGeoip\Controller\ConsoleController;
use Zend\Console\ColorInterface as Color;
use Zend\Http\Response;
use Zend\Http\Client\Exception\RuntimeException;

/**
 * ConsoleControllerAsset
 *
 *
 */
class ConsoleControllerAsset extends ConsoleController
{
    public function downloadAction()
    {
        $datFilePath = $this->config->getDatabasePath();
        $events = $this->getEventManager();

        if ($this->getRequest()->getParam('no-clobber') && is_file($datFilePath)) {
            $events->trigger(__FUNCTION__ . '.exists', $this, array(
                'path' => $datFilePath,
            ));
            $this->writeLine('Database already exist. Skipping...');
            return;
        }

        try {
            $response = $this->getDbResponse();
        } catch (RuntimeException $e) {
            $this->writeLineError(sprintf('%s', $e->getMessage()));
            return;
        }

        if (!$response instanceof Response || $response->getStatusCode() !== Response::STATUS_CODE_200) {
            $this->writeLineError('Error during file download occured');
            return;
        }

        $events->trigger(__FUNCTION__ . '.pre', $this, array(
            'path' => $datFilePath,
            'response' => $response,
        ));

        $this->writeLineSuccess('Download completed');
        $this->writeLine('Unzip the downloading data...');

        $fpc = file_put_contents($datFilePath, gzdecode($response->getBody()));

        $events->trigger(__FUNCTION__ . '.post', $this, array(
            'path' => $datFilePath,
        ));
        $this->writeLineSuccess(sprintf('Unzip completed (%s)', $datFilePath));

        return $fpc;
    }

    public function writeLine($text, $color = null, $bgColor = null)
    {
        if (!$this->isQuietMode()) {
            $param = array('text' => $text, 'color' => $color, 'bgColor' => $bgColor);
            $this->getConsole()->writeLine($text, $color, $bgColor);
            return $param;
        }
        return null;
    }

    public function writeLineError($text)
    {
        return $this->writeLine($text, Color::WHITE, Color::RED);
    }

    public function writeLineSuccess($text)
    {
        return $this->writeLine($text, Color::LIGHT_GREEN);
    }
}
