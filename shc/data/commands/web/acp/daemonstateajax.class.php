<?php

namespace SHC\Command\Web;

//Imports
use RWF\Date\DateTime;
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Util\Message;
use SHC\SwitchServer\SwitchServerEditor;

/**
 * zeigt den Status der einzelnen DIenste an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DaemonStateAjax extends AjaxCommand {

    protected $premission = 'shc.acp.menu';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('daemonstate', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Schaltserver verbindungsversuch um lebenszeichen ab zu fragen
        $switchServers = array();
        $foundRunningServer = false;
        foreach(SwitchServerEditor::getInstance()->listSwitchServers(SwitchServerEditor::SORT_BY_NAME) as $switchServer) {

            /* @var $switchServer \SHC\SwitchServer\SwitchServer */
            $socket = $switchServer->getSocket();
            $socket->setTimeout(1);

            try {

                //Verbindungsversuch
                $socket->open();

                //erfolg
                $switchServers[] = array(
                    'object' => $switchServer,
                    'state' => 1
                );
                $foundRunningServer = true;

                $socket->close();
            } catch(\Exception $e) {

                if($e->getCode() == 1150) {

                    //Fehler
                    $switchServers[] = array(
                        'object' => $switchServer,
                        'state' => 0
                    );
                } else {

                    //Fehler erneut werfen wenn unerwarteter Fehler aufgetreten
                    throw $e;
                }
            }
        }
        //kein laufender Server gefunden
        if($foundRunningServer === false) {

            $message = new Message(Message::WARNING, RWF::getLanguage()->get('acp.daemonState.noRunningServer'));
            $tpl->assign('message', $message);
        }
        $tpl->assign('switchServers', $switchServers);

        //Dienste
        //Sheduler
        $shedulerState = 0;
        $data = trim(@file_get_contents(PATH_RWF_CACHE . 'shedulerRun.flag'));
        if($data != '') {

            $date = DateTime::createFromDatabaseDateTime($data);
            $compareDate = DateTime::now()->sub(new \DateInterval('PT3M'));
            if ($date >= $compareDate) {

                $shedulerState = 1;
            }
        }
        $tpl->assign('shedulerState', $shedulerState);

        //Arduino Sensor Reciver
        $arduinoSensorReciverState = 0;
        if(RWF::getSetting('shc.arduinoReciver.active')) {

            $data = trim(@file_get_contents(PATH_RWF_CACHE . 'arduinoSensorReciver.flag'));
            if ($data != '') {

                $date = DateTime::createFromDatabaseDateTime($data);
                $compareDate = DateTime::now()->sub(new \DateInterval('PT1H'));
                if ($date >= $compareDate) {

                    $arduinoSensorReciverState = 1;
                }
            }
        } else {

            $arduinoSensorReciverState = 2;
        }
        $tpl->assign('arduinoSensorReciverState', $arduinoSensorReciverState);

        //Sensordata Reciver
        $sensorDataReciverState = 0;
        if(RWF::getSetting('shc.sensorReciver.active')) {

            $data = trim(@file_get_contents(PATH_RWF_CACHE . 'sensorDataReciver.flag'));
            if ($data != '') {

                $date = DateTime::createFromDatabaseDateTime($data);
                $compareDate = DateTime::now()->sub(new \DateInterval('PT1H'));
                if ($date >= $compareDate) {

                    $sensorDataReciverState = 1;
                }
            }
        } else {

            $sensorDataReciverState = 2;
        }
        $tpl->assign('sensorDataReciverState', $sensorDataReciverState);

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
        $tpl->assign('sensorDataTransmitterState', $sensorDataTransmitterState);

        //Template anzeigen
        $this->data = $tpl->fetchString('daemonstate.html');
    }

}