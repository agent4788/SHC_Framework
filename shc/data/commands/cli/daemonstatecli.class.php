<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\CliCommand;
use RWF\XML\XmlEditor;

/**
 * Dienste Status
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DaemonStateCli extends CliCommand {

    /**
     * kurzer Kommandozeilen Parameter
     *
     * @var String
     */
    protected $shortParam = '-ds';

    /**
     * voller Kommandozeilen Parameter
     *
     * @var String
     */
    protected $fullParam = '--daemonstate';

    /**
     * Debug Modus aktiv
     *
     * @var Boolean
     */
    protected $debug = false;

    /**
     * gibt die Hilfe zu der Kommandozeilen Funktion auf die Kommandozeile aus
     */
    public function writeHelp() {

        //Sprache einbinden
        //RWF::getLanguage()->loadModul('shedulerdaemon');

        $r = RWF::getResponse();
        $r->writeLnColored('-ds oder --daemonstate gibt eine Liste mit den Diensten und ihrem Status aus', 'green_u');
        $r->writeLn('');
        $r->writeLn('Der Daemon Status gibt eine Liste mit allen Diensten und ihrem Status aus');
        $r->writeLn('');
    }

    /**
     * konfiguriert das CLI Kommando
     */
    protected function config() {}

    /**
     * fuehrt das CLI Kommando aus
     */
    protected function executeCliCommand() {

        //lokaler Schaltserver
        $switchServerState = 0;
        if(file_exists(PATH_RWF . 'db.config.php') && RWF::getSetting('shc.switchServer.active')) {
            $data = trim(@file_get_contents(PATH_RWF_CACHE . 'switchServer.flag'));
            if ($data != '') {

                $date = \DateTime::createFromFormat('Y-m-d H:i:s', $data);
                $compareDate = (new \DateTime('now'))->sub(new \DateInterval('PT1H'));
                if ($date >= $compareDate) {

                    $switchServerState = 1;
                }
            }
        } else {

            $switchServerState = 2;
        }

        //Sheduler
        $shedulerState = 0;
        if((file_exists(PATH_RWF . 'db.config.php') && RWF::getSetting('shc.shedulerDaemon.active')) || !file_exists(PATH_RWF . 'db.config.php')) {
            $data = trim(@file_get_contents(PATH_RWF_CACHE . 'shedulerRun.flag'));
            if ($data != '') {

                $date = \DateTime::createFromFormat('Y-m-d H:i:s', $data);
                $compareDate = (new \DateTime('now'))->sub(new \DateInterval('PT3M'));
                if ($date >= $compareDate) {

                    $shedulerState = 1;
                }
            }
        } else {

            $shedulerState = 2;
        }

        //Sensordata Transmitter
        $sensorDataTransmitterState = 0;
        if(file_exists(PATH_SHC_STORAGE .'sensortransmitter.xml')) {

            $xml = XmlEditor::createFromFile(PATH_SHC_STORAGE .'sensortransmitter.xml');
            if($xml != null && isset($xml->settings->setting)) {

                foreach($xml->settings->setting as $setting) {

                    $attr = $setting->attributes();
                    if((string) $attr->name == 'shc.sensorTransmitter.active' && $attr->value == 'true') {

                        $data = trim(@file_get_contents(PATH_RWF_CACHE . 'sensorDataTransmitter.flag'));
                        if ($data != '') {

                            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $data);
                            $compareDate = (new \DateTime('now'))->sub(new \DateInterval('PT3M'));
                            if ($date >= $compareDate) {

                                $sensorDataTransmitterState = 1;
                            }
                        }
                        break;
                    } elseif((string) $attr->name == 'shc.sensorTransmitter.active') {

                        $sensorDataTransmitterState = 2;
                        break;
                    }
                }
            }
        }

        //Ausgabe
        $r = $this->response;
        $r->writeLnColored('Status der Dienste:', 'green_u');
        //Schaltserver
        $r->write('Schaltserver: ');
        if($switchServerState === 0) {

            $r->writeLnColored('läuft nicht', 'red');
        } elseif($switchServerState === 1) {

            $r->writeLnColored('läuft', 'green');
        } elseif($switchServerState === 2) {

            $r->writeLnColored('deaktiviert', 'yellow');
        }

        //Sheduler (nur bei Hauptinstallation anzeigen)
        if(file_exists(PATH_RWF . 'db.config.php')) {

            $r->write('Scheduler: ');
            if($shedulerState === 0) {

                $r->writeLnColored('läuft nicht', 'red');
            } elseif($shedulerState === 1) {

                $r->writeLnColored('läuft', 'green');
            } elseif($shedulerState === 2) {

                $r->writeLnColored('deaktiviert', 'yellow');
            }
        }

        //Sensor Transmitter
        $r->write('Sensor Sender: ');
        if($sensorDataTransmitterState === 0) {

            $r->writeLnColored('läuft nicht', 'red');
        } elseif($sensorDataTransmitterState === 1) {

            $r->writeLnColored('läuft', 'green');
        } elseif($sensorDataTransmitterState === 2) {

            $r->writeLnColored('deaktiviert', 'yellow');
        }
    }
}