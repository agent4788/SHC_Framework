<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\Request\Commands\CliCommand;

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
        if(RWF::getSetting('shc.switchServer.active')) {
            $data = trim(@file_get_contents(PATH_RWF_CACHE . 'switchServer.flag'));
            if ($data != '') {

                $date = DateTime::createFromDatabaseDateTime($data);
                $compareDate = DateTime::now()->sub(new \DateInterval('PT1H'));
                if ($date >= $compareDate) {

                    $switchServerState = 1;
                }
            }
        } else {

            $switchServerState = 2;
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

            $shedulerState = 2;
        }

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
        //Sheduler
        $r->write('Scheduler: ');
        if($shedulerState === 0) {

            $r->writeLnColored('läuft nicht', 'red');
        } elseif($shedulerState === 1) {

            $r->writeLnColored('läuft', 'green');
        } elseif($shedulerState === 2) {

            $r->writeLnColored('deaktiviert', 'yellow');
        }
        //Arduino Sensor Reciver
        $r->write('Arduino Sensor Empfänger: ');
        if($arduinoSensorReciverState === 0) {

            $r->writeLnColored('läuft nicht', 'red');
        } elseif($arduinoSensorReciverState === 1) {

            $r->writeLnColored('läuft', 'green');
        } elseif($arduinoSensorReciverState === 2) {

            $r->writeLnColored('deaktiviert', 'yellow');
        }
        //Sensor Reciver
        $r->write('Sensor Empfänger: ');
        if($sensorDataReciverState === 0) {

            $r->writeLnColored('läuft nicht', 'red');
        } elseif($sensorDataReciverState === 1) {

            $r->writeLnColored('läuft', 'green');
        } elseif($sensorDataReciverState === 2) {

            $r->writeLnColored('deaktiviert', 'yellow');
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