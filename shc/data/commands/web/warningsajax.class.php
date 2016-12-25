<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Request\Commands\AjaxCommand;
use RWF\Util\Message;
use SHC\Sensor\SensorPointEditor;
use SHC\SwitchServer\SwitchServerEditor;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class WarningsAjax extends AjaxCommand {

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $message = new Message(Message::WARNING, RWF::getLanguage()->get('index.warnings'));

        //Dienste

        //Schaltserver verbindungsversuch um lebenszeichen ab zu fragen
        $foundRunningServer = false;
        foreach(SwitchServerEditor::getInstance()->listSwitchServers(SwitchServerEditor::SORT_BY_NAME) as $switchServer) {

            /* @var $switchServer \SHC\SwitchServer\SwitchServer */
            if($switchServer->isEnabled()) {

                $socket = $switchServer->getSocket();
                $socket->setTimeout(1);

                try {

                    //Verbindungsversuch
                    $socket->open();
                    $socket->close();
                    $foundRunningServer = true;
                } catch(\Exception $e) {

                    if($e->getCode() == 1150) {

                        $message->addSubMessage(RWF::getLanguage()->get('index.warnings.switchserver.stop', $switchServer->getName()));
                    } else {

                        //Fehler erneut werfen wenn unerwarteter Fehler aufgetreten
                        throw $e;
                    }
                }
            }
        }
        //kein laufender Server gefunden
        if($foundRunningServer === false) {

            $message->removeSubMessages();
            $message->addSubMessage(RWF::getLanguage()->get('index.warnings.noRunningServer'));
        }

        //Sheduler
        $shedulerState = 0;
        if(RWF::getSetting('shc.shedulerDaemon.active')) {
            $data = trim(@file_get_contents(PATH_RWF_CACHE . 'shedulerRun.flag'));
            if ($data != '') {

                $date = DateTime::createFromDatabaseDateTime($data);
                $compareDate = DateTime::now()->sub(new \DateInterval('PT3M'));
                if ($date >= $compareDate) {

                    $shedulerState = 1;
                }
            }
        } else {

            $shedulerState = 3;
        }
        if($shedulerState === 0) {

            $message->addSubMessage(RWF::getLanguage()->get('index.warnings.sheduler.stop'));
        }

        //Sensordatat Transmitter
        $sensorDataTransmitterState = 0;
        if(RWF::getSetting('shc.sensorTransmitter.active')) {

            $data = trim(@file_get_contents(PATH_RWF_CACHE . 'sensorDataTransmitter.flag'));
            if ($data != '') {

                $date = DateTime::createFromDatabaseDateTime($data);
                $compareDate = DateTime::now()->sub(new \DateInterval('PT3M'));
                if ($date >= $compareDate) {

                    $sensorDataTransmitterState = 1;
                }
            }
        } else {

            $sensorDataTransmitterState = 2;
        }
        if($sensorDataTransmitterState === 0) {

            $message->addSubMessage(RWF::getLanguage()->get('index.warnings.sensorDataTransmitter.stop'));
        }

        //Sensorpunkte
        $inPast = DateTime::now()->sub(new \DateInterval('PT2H'));
        $sensorPoints = SensorPointEditor::getInstance()->listSensorPoints(SensorPointEditor::SORT_BY_NAME);

        //zu lange nicht gemeldet
        foreach($sensorPoints as $sensorPoint) {

            /* @var $sensorPoint \SHC\Sensor\SensorPoint */
            $lastConnect = $sensorPoint->getTime();
            if($lastConnect < $inPast && $sensorPoint->getId() !== 1000) {

                $message->addSubMessage(RWF::getLanguage()->get('index.warnings.sensorPoint.stop', $sensorPoint->getName()));
            }
        }

        //Unterspannung
        foreach($sensorPoints as $sensorPoint) {

            /* @var $sensorPoint \SHC\Sensor\SensorPoint */
            $voltage = $sensorPoint->getVoltage();
            $warningLevel = $sensorPoint->getWarnLevel();
            if($warningLevel > 0.0 && $voltage < $warningLevel) {

                $message->addSubMessage(RWF::getLanguage()->get('index.warnings.sensorPoint.underVoltage', $sensorPoint->getName(), $voltage));
            }
        }

        if(count($message->getSubMessages()) > 0) {

            $this->data = $message->fetchHtml();
        }
    }

}