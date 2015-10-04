<?php

namespace SHC\Sensor;

//Imports
use DateTime;
use RWF\Util\CliUtil;
use RWF\Util\FileUtil;
use SHC\Command\CLI\SensorDatatTransmitterCli;
use SHC\Core\SHC;

/**
 * Liest Sensordaten vom Arduino aus
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SensorDataTransmitter {

    /**
     * CLI Kommando
     *
     * @var \SHC\Command\CLI\SensorDatatTransmitterCli
     */
    protected $command = null;

    /**
     * @param SensorDatatTransmitterCli $command
     */
    public function __construct(SensorDatatTransmitterCli $command) {

        $this->command = $command;
    }

    /**
     * gibt die Sensordaten zur Fehlersuche auf die Kommandozeile aus
     *
     * @param Array $data Daten
     */
    protected function printDebugData(array $data) {

        switch($data['type']) {

            case 1:

                echo "Typ: DS18x20\n";
                echo "Sensor Punkt Id: ". $data['spid'] ."\n";
                echo "Sensor ID: ". $data['sid'] ."\n";
                echo "Temeratur: ". $data['v1'] ."°C\n";
                break;
            case 2:

                echo "Typ: DHT11 oder DHT22\n";
                echo "Sensor Punkt Id: ". $data['spid'] ."\n";
                echo "Sensor ID: ". $data['sid'] ."\n";
                echo "Temeratur: ". $data['v1'] ."°C\n";
                echo "Luftfeuchte: ". $data['v2'] ."%\n";
                break;
            case 3:

                echo "Typ: BMP085 oder BMP180\n";
                echo "Sensor Punkt Id: ". $data['spid'] ."\n";
                echo "Sensor ID: ". $data['sid'] ."\n";
                echo "Temeratur: ". $data['v1'] ."°C\n";
                echo "Luftdruck: ". $data['v2'] ."pa\n";
                echo "Standorhöhe: ". $data['v3'] ."m\n";
                break;
            default:

        }
        echo "-----------------------------------------------------------------------------\n";
    }

    /**
     * sendet eine HTTP Anfrage
     * @param  Array   $getParameter HTTP GET Parameter
     * @return Boolean
     */
    protected function sendHttpRequest(array $getParameter) {

        //Get Parameter vorbereiten
        $get = '';
        foreach($getParameter as $name => $value) {

            $get .= '&' . rawurlencode($name) .'='. rawurlencode($value);
        }

        //HTTP Anfrage
        $http_options = stream_context_create(array(
            'http' => array(
                'method'  => 'GET',
                'user_agent' => "SHC Framework Sensor Transmitter Version ". SHC::VERSION,
                'max_redirects' => 3
            )
        ));
        $result = @file_get_contents('http://'. $this->command->getSetting('shc.sensorTransmitter.ip') .':'. $this->command->getSetting('shc.sensorTransmitter.port') .'/shc/index.php?app=shc&a&ajax=pushsensorvalues'. $get, false, $http_options);

        //Verbindung Fehlgeschlagen
        if($result === false) {

            $cli = new CliUtil();
            $cli->writeLineColored('Verbindung zum Server "'. $this->command->getSetting('shc.sensorTransmitter.ip') .':'. $this->command->getSetting('shc.sensorTransmitter.port') .'" fehlgeschlagen', 'red');
            $cli->writeLineColored('erneuter Versuch in 30 Sekunden', 'yellow');

            //30 Sekunden Wartezeit
            sleep(30);
        }

        //Auswertung der Rueckantwort
        if($result == 1) {

            return true;
        }
        return false;
    }

    /**
     * list in einer Endlosschleife die Sensordaten und sendet sie an den Sensor Reciver
     * 
     * @param Boolean $debug gesendete Daten mit ausgeben
     */
    public function transmitSensorData($debug = false) {

        //Wartezeit vorbereiten
        $nextRuntime = new DateTime('now');
        $LEDnextRuntime = new DateTime('now');

        //GPIO Vorbereiten
        $pin = $this->command->getSetting('shc.sensorTransmitter.blinkPin');
        $gpioPath = $this->command->getSetting('shc.switchServer.gpioCommand');
        $state = 0;

        //Status LED initalisieren
        if($pin >= 0) {

            @shell_exec($gpioPath . ' mode ' . escapeshellarg($pin) . ' out');
        }

        while (true) {

            if($nextRuntime  <= new DateTime('now')) {

                //DS18x20 einlesen und an den Server senden
                if (file_exists('/sys/bus/w1/devices/')) {

                    $dir = opendir('/sys/bus/w1/devices/');
                    while ($file = readdir($dir)) {

                        //Sensor suchen
                        if (preg_match('#^(10)|(22)|(28)-#', $file) && is_dir('/sys/bus/w1/devices/' . $file)) {

                            //Temperatur lesen
                            $dataRaw = @file_get_contents('/sys/bus/w1/devices/' . $file . '/w1_slave');

                            //naechster Durchlauf wenn Sensor nicht gelesen werden kann
                            if($dataRaw == false) {

                                continue;
                            }

                            //Daten zum senden vorbereiten
                            $match = array();
                            preg_match('#t=(\d{1,6})#', $dataRaw, $match);

                            //pruefen ob die Daten valid sind
                            if(isset($match[1])) {

                                $temp = $match[1] / 1000;

                                //Datenpaket vorbereiten
                                $data = array(
                                    'spid' => $this->command->getSetting('shc.sensorTransmitter.pointId'),
                                    'type' => 1,
                                    'sid' => $file,
                                    'v1' => $temp
                                );

                                //Debug Ausgabe
                                if ($debug) {

                                    $this->printDebugData($data);
                                }

                                //Daten senden
                                $this->sendHttpRequest($data);
                            }
                        }
                    }
                }

                //DHT
                $dhts = SensorEditor::getInstance()->listDHT();
                foreach($dhts as $dht) {

                    $values = SensorEditor::getInstance()->readDHT($dht['id']);

                    if(!isset($values[0])) {

                        //Datenpaket vorbereiten
                        $data = array(
                            'spid' => $this->command->getSetting('shc.sensorTransmitter.pointId'),
                            'type' => 2,
                            'sid' => $dht['id'],
                            'v1' => $values['temp'],
                            'v2' => $values['hum']
                        );

                        //Debug Ausgabe
                        if ($debug) {

                            $this->printDebugData($data);
                        }

                        //Daten senden
                        $this->sendHttpRequest($data);
                    }
                }

                //BMP
                if(SensorEditor::getInstance()->isBMPenabled()) {

                    $values = SensorEditor::getInstance()->readBMP();

                    //Datenpaket vorbereiten
                    $data = array(
                        'spid' => $this->command->getSetting('shc.sensorTransmitter.pointId'),
                        'type' => 3,
                        'sid' => SensorEditor::getInstance()->getBMPsensorId(),
                        'v1' => $values['temp'],
                        'v2' => $values['press'],
                        'v3' => $values['alti']
                    );

                    //Debug Ausgabe
                    if ($debug) {

                        $this->printDebugData($data);
                    }

                    //Daten senden
                    $this->sendHttpRequest($data);
                }

                //MCP3008 oder MCP3208 fuer die Analogsensoren

                //Run Flag alle 60 Sekunden setzen
                if(!isset($time)) {

                    $time = new DateTime('now');
                }
                if($time <= new DateTime('now')) {

                    if(!file_exists(PATH_RWF_CACHE . 'sensorDataTransmitter.flag')) {

                        FileUtil::createFile(PATH_RWF_CACHE . 'sensorDataTransmitter.flag', 0777, true);
                    }
                    file_put_contents(PATH_RWF_CACHE . 'sensorDataTransmitter.flag', (new DateTime('now'))->format('Y-m-d H:i:s'));
                    $time->add(new \DateInterval('PT1M'));
                }

                //Wartezeit setzen
                $nextRuntime = (new DateTime('now'))->add(new \DateInterval('PT30S'));
            }

            //Status LED
            if($pin >= 0 && $LEDnextRuntime <= new DateTime('now')) {

                if($state === 0) {

                    @shell_exec($gpioPath . ' write ' . escapeshellarg($pin) . ' 1');
                    $state = 1;
                } else {

                    @shell_exec($gpioPath . ' write ' . escapeshellarg($pin) . ' 0');
                    $state = 0;
                }

                //Wartezeit setzen
                $LEDnextRuntime = (new DateTime('now'))->add(new \DateInterval('PT1S'));
            }
        }
    }

}
