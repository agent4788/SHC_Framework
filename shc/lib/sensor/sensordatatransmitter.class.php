<?php

namespace SHC\Sensor;

//Imports
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\IO\UDPSocket;
use RWF\Util\CliUtil;

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
     * verbindet sich mit dem Sensor Reciver
     * 
     * @return \RWF\IO\Socket
     */
    protected function connect() {

        //Vebindung zum RPi Reader Server aufbauen
        $sensorReciver = new UDPSocket(RWF::getSetting('shc.sensorTransmitter.ip'), RWF::getSetting('shc.sensorTransmitter.port'), 2);
        try {

            $sensorReciver->open();
        } catch (\Exception $e) {

            $cli = new CliUtil();
            $cli->writeLineColored('Die Verbindung zum Server (' . RWF::getSetting('shc.sensorTransmitter.ip') . ':' . RWF::getSetting('shc.sensorTransmitter.port') . ') konnte nicht hergestellt werden', 'red');

            //30 Sekunden warten dann wieder versuchen
            sleep(30);
        }

        return $sensorReciver;
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
                            'succsess' => true,
                            'sensorPointId' => RWF::getSetting('shc.sensorTransmitter.pointId'),
                            'sensorTypeId' => 1,
                            'sensorId' => $file,
                            'sensorValues' => array(
                                'temp' => $temp
                            )
                        );

                        //Debug Ausgabe
                        if ($debug) {

                            var_dump($data);
                        }

                        //Daten an den Sensor Reciver senden
                        $sensorReciver = $this->connect();
                        $sensorReciver->write(base64_encode(json_encode($data)));
                        $sensorReciver->close();
                        $sensorReciver = null;
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
                        'succsess' => true,
                        'sensorPointId' => RWF::getSetting('shc.sensorTransmitter.pointId'),
                        'sensorTypeId' => 2,
                        'sensorId' => $dht['id'],
                        'sensorValues' => array(
                            'temp' => $values['temp'],
                            'hum' => $values['hum']
                        )
                    );

                    //Debug Ausgabe
                    if ($debug) {

                        var_dump($data);
                    }

                    //Daten an den Sensor Reciver senden
                    $sensorReciver = $this->connect();
                    $sensorReciver->write(base64_encode(json_encode($data)));
                    $sensorReciver->close();
                    $sensorReciver = null;
                }
            }

            //BMP
            if(SensorEditor::getInstance()->isBMPenabled()) {

                $values = SensorEditor::getInstance()->readBMP();

                //Datenpaket vorbereiten
                $data = array(
                    'succsess' => true,
                    'sensorPointId' => RWF::getSetting('shc.sensorTransmitter.pointId'),
                    'sensorTypeId' => 3,
                    'sensorId' => SensorEditor::getInstance()->getBMPsensorId(),
                    'sensorValues' => array(
                        'temp' => $values['temp'],
                        'press' => $values['press'],
                        'alti' => $values['alti']
                    )
                );

                //Debug Ausgabe
                if ($debug) {

                    var_dump($data);
                }

                //Daten an den Sensor Reciver senden
                $sensorReciver = $this->connect();
                $sensorReciver->write(base64_encode(json_encode($data)));
                $sensorReciver->close();
                $sensorReciver = null;
            }

            //MCP3008 oder MCP3208 fuer die Analogsensoren

            //Run Flag alle 60 Sekunden setzen
            if(!isset($time)) {

                $time = DateTime::now();
            }
            if($time <= DateTime::now()) {

                file_put_contents(PATH_RWF_CACHE . 'sensorDataTransmitter.flag', DateTime::now()->getDatabaseDateTime());
                $time->add(new \DateInterval('PT1M'));
            }

            //wartezeit bis zum neachsten Sendevorgang
            sleep(10);
        }
    }

}
