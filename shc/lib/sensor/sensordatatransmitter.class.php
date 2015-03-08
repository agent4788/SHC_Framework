<?php

namespace SHC\Sensor;

//Imports
use RWF\Date\DateTime;
use RWF\Util\FileUtil;
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
        $result = file_get_contents('http://'. SHC::getSetting('shc.sensorTransmitter.ip') .':'. SHC::getSetting('shc.sensorTransmitter.port') .'/shc/index.php?app=shc&a&ajax=pushsensorvalues'. $get, false, $http_options);

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

        while (true) {

            //DS18x20 einlesen und an den Server senden
            if (file_exists('/sys/bus/w1/devices/')) {

                $dir = opendir('/sys/bus/w1/devices/');
                while ($file = readdir($dir)) {

                    //Sensor suchen
                    if (preg_match('#^(10)|(22)|(28)-#', $file) && is_dir('/sys/bus/w1/devices/' . $file)) {

                        //Temperatur lesen
                        $dataRaw = file_get_contents('/sys/bus/w1/devices/' . $file . '/w1_slave');
                        $match = array();
                        preg_match('#t=(\d{1,6})#', $dataRaw, $match);
                        $temp = $match[1] / 1000;

                        //Datenpaket vorbereiten
                        $data = array(
                            'spid' => RWF::getSetting('shc.sensorTransmitter.pointId'),
                            'type' => 1,
                            'sid' => $file,
                            'v1' => $temp
                        );

                        //Debug Ausgabe
                        if ($debug) {

                            var_dump($data);
                        }

                        //Daten senden
                        if(!$this->sendHttpRequest($data)) {

                            //bei Fehlschlag 30 Sekunden vor dem naechsten Versuch warten
                            sleep(30);
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
                        'spid' => RWF::getSetting('shc.sensorTransmitter.pointId'),
                        'type' => 2,
                        'sid' => $dht['id'],
                        'v1' => $values['temp'],
                        'v2' => $values['hum']
                    );

                    //Debug Ausgabe
                    if ($debug) {

                        var_dump($data);
                    }

                    //Daten senden
                    if(!$this->sendHttpRequest($data)) {

                        //bei Fehlschlag 30 Sekunden vor dem naechsten Versuch warten
                        sleep(30);
                    }
                }
            }

            //BMP
            if(SensorEditor::getInstance()->isBMPenabled()) {

                $values = SensorEditor::getInstance()->readBMP();

                //Datenpaket vorbereiten
                $data = array(
                    'spid' => RWF::getSetting('shc.sensorTransmitter.pointId'),
                    'type' => 3,
                    'sid' => SensorEditor::getInstance()->getBMPsensorId(),
                    'v1' => $values['temp'],
                    'v2' => $values['press'],
                    'v3' => $values['alti']
                );

                //Debug Ausgabe
                if ($debug) {

                    var_dump($data);
                }

                //Daten senden
                if(!$this->sendHttpRequest($data)) {

                    //bei Fehlschlag 30 Sekunden vor dem naechsten Versuch warten
                    sleep(30);
                }
            }

            //MCP3008 oder MCP3208 fuer die Analogsensoren

            //Run Flag alle 60 Sekunden setzen
            if(!isset($time)) {

                $time = DateTime::now();
            }
            if($time <= DateTime::now()) {

                if(!file_exists(PATH_RWF_CACHE . 'sensorDataTransmitter.flag')) {

                    FileUtil::createFile(PATH_RWF_CACHE . 'sensorDataTransmitter.flag', 0777, true);
                }
                file_put_contents(PATH_RWF_CACHE . 'sensorDataTransmitter.flag', DateTime::now()->getDatabaseDateTime());
                $time->add(new \DateInterval('PT1M'));
            }

            //wartezeit bis zum neachsten Sendevorgang
            sleep(30);
        }
    }

}
