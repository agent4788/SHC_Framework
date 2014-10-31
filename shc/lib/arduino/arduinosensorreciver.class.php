<?php

namespace SHC\Arduino;

//Imports
use SHC\Sensor\SensorPointEditor;
use SHC\Sensor\SensorPoint;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\RainSensor;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;
use SHC\Core\SHC;
use RWF\Date\DateTime;

/**
 * Liest Sensordaten vom Arduino aus
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ArduinoSensorReciver {

    /**
     * Objekt zum lesen der Seriellen Schnittstelle
     * 
     * @var \PhpSerial 
     */
    protected $serial = null;

    /**
     * Lesevorgang intialisieren
     * 
     * @param String  $device   Schnittstelle
     * @param Integer $baudRate Geschwindigkeit
     */
    public function __construct($device, $baudRate = 9600) {

        //Serial Klasse einbinden
        require_once(PATH_SHC_CLASSES . 'external/phpSerial/phpSerial.php');

        $this->serial = new \phpSerial\phpSerial();
        $this->serial->deviceSet($device);
        $this->serial->confBaudRate($baudRate);
        $this->serial->confParity("none");
        $this->serial->confCharacterLength(8);
        $this->serial->confStopBits(1);
        $this->serial->confFlowControl("none");
        $this->serial->deviceOpen();

        sleep(3);
    }

    /**
     * list in einer Endlosschleife die Sensordaten und schreib diese in die XML Dateien
     * 
     * @param Boolean $debug Eingehende Daten anzeigen
     */
    public function readDataEndless($debug = false) {

        //Zeit des naechsten Speicherns
        $time = new DateTime();
        $interval = new \DateInterval('PT30S');
        $time->add($interval);

        //Run Flag alle 60 Sekunden setzen
        if(!isset($runTime)) {

            $runTime = DateTime::now();
        }
        if($runTime <= DateTime::now()) {

            file_put_contents(PATH_RWF_CACHE . 'arduinoSensorReciver.flag', DateTime::now()->getDatabaseDateTime());
            $runTime->add(new \DateInterval('PT1M'));
        }

        //Sensorpunkte aus XML Lesen
        SensorPointEditor::getInstance();

        while (true) {

            //Zeile einlesen
            $rawData = $this->serial->readLine();
            $rawDate = trim($rawData);

            //Eingehende Daten anzeigen
            if ($debug) {

                SHC::getResponse()->writeLn($rawDate);
            }

            //Daten verarbeiten
            if (preg_match('!#(\d{1,3});(\d{1,10};)!i', $rawDate, $match)) {

                //Sensorpunkt Spannung
                $sensorPointId = intval($match[1]);
                $voltage = intval($match[2]) / 100;

                //Sensorpunkt Spannung speichern
                $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);
                if ($sensorPoint instanceof SensorPoint) {

                    //Spannung speichern
                    $sensorPoint->setVoltage($voltage);
                    $sensorPoint->setTime(DateTime::now());
                } else {

                    //Neuen Sensorpunkt erstellen und registrieren
                    $sensorPoint = new SensorPoint();
                    $sensorPoint->setId($sensorPointId);
                    $sensorPoint->setVoltage($voltage);
                    $sensorPoint->setTime(DateTime::now());

                    //Sensorpunkt registrieren
                    SensorPointEditor::getInstance()->addSensorPoint($sensorPoint);
                }
            } elseif (preg_match('!#(\d{1,3})\+((10)|(22)|(28));([0-9a-f]{14});(\d{1,4})!i', $rawDate, $match)) {

                //DS18x20 Sensor
                $sensorPointId = intval($match[1]);
                $sensorId = $match[5] . '-' . $match[6];

                //Daten Speichern
                $this->saveDS18x20($sensorPointId, $sensorId, intval($match[7]));
            } elseif (preg_match('!#(\d{1,3})\+(\d{1,2})\+(\d{1,3});((\d{1,10};)+)!i', $rawDate, $match)) {

                //alle anderen Sensoren
                $sensorPointId = intval($match[1]);
                $typeId = intval($match[2]);
                $sensorId = intval($match[3]);
                $values = preg_split('#;#', $match[4]);

                switch ($typeId) {

                    case SensorPointEditor::SENSOR_DHT:

                        //DHT11/22
                        $this->saveDHT($sensorPointId, $sensorId, $values[0], $values[1]);
                        break;
                    case SensorPointEditor::SENSOR_BMP:

                        //BMP085/180
                        $this->saveBMP($sensorPointId, $sensorId, $values[0], $values[1], $values[2]);
                        break;
                    case SensorPointEditor::SENSOR_RAIN:

                        //Regensenor (Analogwert 0 - 1023)
                        $this->saveRainSensor($sensorPointId, $sensorId, $values[0]);
                        break;
                    case SensorPointEditor::SENSOR_HYGROMETER:

                        //Hygrometer (Analogwert 0 - 1023)
                        $this->saveHygrometer($sensorPointId, $sensorId, $values[0]);
                        break;
                    case SensorPointEditor::SENSOR_LDR:

                        //LDR (Analogwert 0 - 1023)
                        $this->saveLightSensor($sensorPointId, $sensorId, $values[0]);
                        break;
                }
            }

            //XML Daten speichern
            if ($time < DateTime::now()) {

                SensorPointEditor::getInstance()->writeData();
                $time->add($interval);
                
                //Daten gespeichert
                if ($debug) {

                    SHC::getResponse()->writeLn('Sensordaten wurden gespeichert');
                }
            }
        }
    }

    /**
     * speichert den Aktuellen Sensorwert in der Objektstruktur
     * 
     * @param Integer $sensorPointId Sensor Punkt ID
     * @param Integer $sensorId      Sensor ID
     * @param Integer $temparature   Temperatur
     */
    protected function saveDS18x20($sensorPointId, $sensorId, $temparature) {

        //Sensorwerte zurueckrechnen
        $temparature /= 100;

        //Sensorpunkt suchen
        $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);
        if ($sensorPoint instanceof SensorPoint) {

            //Sensor Point ist schon bekannt
            $sensorPoint->setTime(DateTime::now());
        } else {

            //neuen Sensorpunkt erzeigen
            $sensorPoint = new SensorPoint();
            $sensorPoint->setId($sensorPointId);
            $sensorPoint->setTime(DateTime::now());

            //Sensor Registrieren
            SensorPointEditor::getInstance()->addSensorPoint($sensorPoint);
        }

        //Sensor suchen
        $sensor = $sensorPoint->getSensorById($sensorId);
        if ($sensor instanceof DS18x20) {

            //Sensor ist schon bekannt
            $sensor->pushValues($temparature);
        } else {

            //neue Sensor
            $sensor = new DS18x20();
            $sensor->setId($sensorId);
            $sensor->pushValues($temparature);

            //Sensor am Sensorpunkt registrien
            $sensorPoint->addSensor($sensor);
        }
    }

    /**
     * speichert den Aktuellen Sensorwert in der Objektstruktur
     * 
     * @param Integer $sensorPointId Sensor Punkt ID
     * @param Integer $sensorId      Sensor ID
     * @param Integer $temparature   Temperatur
     * @param Integer $humidity      Luftfeuchtigkeit
     */
    protected function saveDHT($sensorPointId, $sensorId, $temparature, $humidity) {

        //Sensorwerte zurueckrechnen
        $temparature /= 100;
        $humidity /= 100;

        //Sensorpunkt suchen
        $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);
        if ($sensorPoint instanceof SensorPoint) {

            //Sensor Point ist schon bekannt
            $sensorPoint->setTime(DateTime::now());
        } else {

            //neuen Sensorpunkt erzeigen
            $sensorPoint = new SensorPoint();
            $sensorPoint->setId($sensorPointId);
            $sensorPoint->setTime(DateTime::now());

            //Sensor Registrieren
            SensorPointEditor::getInstance()->addSensorPoint($sensorPoint);
        }

        //Sensor suchen
        $sensor = $sensorPoint->getSensorById($sensorId);
        if ($sensor instanceof DHT) {

            //Sensor ist schon bekannt
            $sensor->pushValues($temparature, $humidity);
        } else {

            //neue Sensor
            $sensor = new DHT();
            $sensor->setId($sensorId);
            $sensor->pushValues($temparature, $humidity);

            //sensor am Sensorpunkt registrien
            $sensorPoint->addSensor($sensor);
        }
    }

    /**
     * speichert den Aktuellen Sensorwert in der Objektstruktur
     * 
     * @param Integer $sensorPointId Sensor Punkt ID
     * @param Integer $sensorId      Sensor ID
     * @param Integer $temparature   Temperatur
     * @param Integer $pressure      Luftdruck
     * @param Integer $altitude      Standorthoehe
     */
    protected function saveBMP($sensorPointId, $sensorId, $temparature, $pressure, $altitude) {

        $temparature /= 100;
        $pressure /= 100;
        $altitude /= 100;

        //Sensorpunkt suchen
        $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);
        if ($sensorPoint instanceof SensorPoint) {

            //Sensor Point ist schon bekannt
            $sensorPoint->setTime(DateTime::now());
        } else {

            //neuen Sensorpunkt erzeigen
            $sensorPoint = new SensorPoint();
            $sensorPoint->setId($sensorPointId);
            $sensorPoint->setTime(DateTime::now());

            //Sensor Registrieren
            SensorPointEditor::getInstance()->addSensorPoint($sensorPoint);
        }

        //Sensor suchen
        $sensor = $sensorPoint->getSensorById($sensorId);
        if ($sensor instanceof BMP) {

            //Sensor ist schon bekannt
            $sensor->pushValues($temparature, $pressure, $altitude);
        } else {

            //neue Sensor
            $sensor = new BMP();
            $sensor->setId($sensorId);
            $sensor->pushValues($temparature, $pressure, $altitude);

            //sensor am Sensorpunkt registrien
            $sensorPoint->addSensor($sensor);
        }
    }

    /**
     * speichert den Aktuellen Sensorwert in der Objektstruktur
     * 
     * @param Integer $sensorPointId Sensor Punkt ID
     * @param Integer $sensorId      Sensor ID
     * @param Integer $analogValue   Analogwert
     */
    protected function saveRainSensor($sensorPointId, $sensorId, $analogValue) {

        //Sensorwerte zurueckrechnen
        $analogValue = intval($analogValue);

        //Sensorpunkt suchen
        $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);
        if ($sensorPoint instanceof SensorPoint) {

            //Sensor Point ist schon bekannt
            $sensorPoint->setTime(DateTime::now());
        } else {

            //neuen Sensorpunkt erzeigen
            $sensorPoint = new SensorPoint();
            $sensorPoint->setId($sensorPointId);
            $sensorPoint->setTime(DateTime::now());

            //Sensor Registrieren
            SensorPointEditor::getInstance()->addSensorPoint($sensorPoint);
        }

        //Sensor suchen
        $sensor = $sensorPoint->getSensorById($sensorId);
        if ($sensor instanceof RainSensor) {

            //Sensor ist schon bekannt
            $sensor->pushValues($analogValue);
        } else {

            //neue Sensor
            $sensor = new RainSensor();
            $sensor->setId($sensorId);
            $sensor->pushValues($analogValue);

            //sensor am Sensorpunkt registrien
            $sensorPoint->addSensor($sensor);
        }
    }

    /**
     * speichert den Aktuellen Sensorwert in der Objektstruktur
     * 
     * @param Integer $sensorPointId Sensor Punkt ID
     * @param Integer $sensorId      Sensor ID
     * @param Integer $analogValue   Analogwert
     */
    protected function saveHygrometer($sensorPointId, $sensorId, $analogValue) {

        //Sensorwerte zurueckrechnen
        $analogValue = intval($analogValue);

        //Sensorpunkt suchen
        $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);
        if ($sensorPoint instanceof SensorPoint) {

            //Sensor Point ist schon bekannt
            $sensorPoint->setTime(DateTime::now());
        } else {

            //neuen Sensorpunkt erzeigen
            $sensorPoint = new SensorPoint();
            $sensorPoint->setId($sensorPointId);
            $sensorPoint->setTime(DateTime::now());

            //Sensor Registrieren
            SensorPointEditor::getInstance()->addSensorPoint($sensorPoint);
        }

        //Sensor suchen
        $sensor = $sensorPoint->getSensorById($sensorId);
        if ($sensor instanceof Hygrometer) {

            //Sensor ist schon bekannt
            $sensor->pushValues($analogValue);
        } else {

            //neue Sensor
            $sensor = new Hygrometer();
            $sensor->setId($sensorId);
            $sensor->pushValues($analogValue);

            //sensor am Sensorpunkt registrien
            $sensorPoint->addSensor($sensor);
        }
    }

    /**
     * speichert den Aktuellen Sensorwert in der Objektstruktur
     * 
     * @param Integer $sensorPointId Sensor Punkt ID
     * @param Integer $sensorId      Sensor ID
     * @param Integer $analogValue   Analogwert
     */
    protected function saveLightSensor($sensorPointId, $sensorId, $analogValue) {

        //Sensorwerte zurueckrechnen
        $analogValue = intval($analogValue);

        //Sensorpunkt suchen
        $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);
        if ($sensorPoint instanceof SensorPoint) {

            //Sensor Point ist schon bekannt
            $sensorPoint->setTime(DateTime::now());
        } else {

            //neuen Sensorpunkt erzeigen
            $sensorPoint = new SensorPoint();
            $sensorPoint->setId($sensorPointId);
            $sensorPoint->setTime(DateTime::now());

            //Sensor Registrieren
            SensorPointEditor::getInstance()->addSensorPoint($sensorPoint);
        }

        //Sensor suchen
        $sensor = $sensorPoint->getSensorById($sensorId);
        if ($sensor instanceof LDR) {

            //Sensor ist schon bekannt
            $sensor->pushValues($analogValue);
        } else {

            //neue Sensor
            $sensor = new LDR();
            $sensor->setId($sensorId);
            $sensor->pushValues($analogValue);

            //sensor am Sensorpunkt registrien
            $sensorPoint->addSensor($sensor);
        }
    }

    /**
     * Schliest die Serielle Schnittstelle
     */
    public function close() {

        $this->serial->deviceClose();
    }

    /**
     * Schliest die Serielle Schnittstelle
     */
    public function __destruct() {

        $this->close();
    }

}
