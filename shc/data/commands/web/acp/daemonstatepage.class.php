<?php

namespace SHC\Command\Web;

//Imports
use RWF\Date\DateTime;
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Util\Message;
use SHC\Core\SHC;
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
class DaemonStatePage extends PageCommand {

    protected $requiredPremission = 'shc.acp.menu';

    protected $template = 'daemonstate.html';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'daemonstate', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Header Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());

        //Schaltserver verbindungsversuch um lebenszeichen ab zu fragen
        $switchServers = array();
        $foundRunningServer = false;
        foreach(SwitchServerEditor::getInstance()->listSwitchServers(SwitchServerEditor::SORT_BY_NAME) as $switchServer) {

            /* @var $switchServer \SHC\SwitchServer\SwitchServer */
            if($switchServer->isEnabled()) {

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
            } else {

                //Deaktiviert
                $switchServers[] = array(
                    'object' => $switchServer,
                    'state' => 0
                );
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

            $shedulerState = 2;
        }
        $tpl->assign('shedulerState', $shedulerState);

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
    }

}