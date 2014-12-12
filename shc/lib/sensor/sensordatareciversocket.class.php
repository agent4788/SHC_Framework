<?php

namespace SHC\Sensor;

//Imports
use RWF\IO\SocketServer;
use RWF\Core\RWF;
use RWF\Date\DateTime;
use RWF\IO\UDPSocketServer;
use RWF\Request\CliResponse;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;
use SHC\Sensor\Sensors\RainSensor;

/**
 * Empfaent Sensordaten aus dem Netzwerk und speichert diese ab
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SensorDataReciverSocket {

    /**
     * Antwortobjekt
     * 
     * @var \RWF\Request\CliResponse
     */
    protected $response = null;

    /**
     * Debug Ausgabe
     * 
     * @var Boolean
     */
    protected $debug = false;

    /**
     * Server Objekt
     * 
     * @var \RWF\IO\SocketServer
     */
    protected $server = null;

    /**
     * startet den Server und wartet auf Anfragen
     * 
     * @param \RWF\Request\CliResponse $response Antwortobjekt
     * @param Boolean                  $debug    Debug Meldungen ausgeben
     */
    public function run(CliResponse $response, $debug = false) {

        $this->response = $response;
        $this->debug = $debug;
        RWF::getLanguage()->loadModul('SensorReciver');

        //Server Starten
        $this->server = new UDPSocketServer(RWF::getSetting('shc.sensorReciver.ip'), RWF::getSetting('shc.sensorReciver.port'));
        $this->server->startServer();

        //Startmeldung
        $this->response->writeLnColored(RWF::getLanguage()->get('sensorReciver.start', $this->server->getAddress(), $this->server->getPort()), 'green');

        //Zeit des naechsten Speicherns
        $time = new DateTime();
        $interval = new \DateInterval('PT30S');
        $time->add($interval);

        //Sensorpunkte aus XML Lesen
        SensorPointEditor::getInstance();

        while (true) {

            //Run Flag alle 60 Sekunden setzen
            if(!isset($runTime)) {

                $runTime = DateTime::now();
            }
            if($runTime <= DateTime::now()) {

                if(!file_exists(PATH_RWF_CACHE . 'sensorDataReciver.flag')) {

                    FileUtil::createFile(PATH_RWF_CACHE . 'sensorDataReciver.flag', 0777, true);
                }
                file_put_contents(PATH_RWF_CACHE . 'sensorDataReciver.flag', DateTime::now()->getDatabaseDateTime());
                $runTime->add(new \DateInterval('PT1M'));
            }

            //Daten epfangen
            $data = base64_decode($this->server->read(4096));
            $request = json_decode($data, true);

            //Debug Ausgabe
            if ($this->debug) {

                var_dump($request);
            }
            
            //Serverbeenden
            if(isset($request['stop']) && $request['stop'] == 1) {
                
                $this->stop();
            }

            //Sensordaten zum speichern vorbereiten
            $sensorPointId = intval($request['sensorPointId']);
            $typeId = intval($request['sensorTypeId']);
            $sensorId = $request['sensorId'];


            switch ($typeId) {

                case SensorPointEditor::SENSOR_DS18X20:

                    if (isset($request['sensorValues']['temp'])) {

                        //Sensorwerte
                        $temparature = $request['sensorValues']['temp'];

                        //Speichern
                        $this->saveDS18x20($sensorPointId, $sensorId, $temparature);
                    }
                    break;
                case SensorPointEditor::SENSOR_DHT:

                    if (isset($request['sensorValues']['temp']) && isset($request['sensorValues']['hum'])) {

                        //Sensorwete
                        $temparature = $request['sensorValues']['temp'];
                        $humidity = $request['sensorValues']['hum'];

                        //Speichern
                        $this->saveDHT($sensorPointId, $sensorId, $temparature, $humidity);
                    }
                    break;
                case SensorPointEditor::SENSOR_BMP:

                    if (isset($request['sensorValues']['temp']) && isset($request['sensorValues']['press']) && isset($request['sensorValues']['alti'])) {

                        //Sensorwete
                        $temparature = $request['sensorValues']['temp'];
                        $pressure = $request['sensorValues']['press'];
                        $altitude = $request['sensorValues']['alti'];

                        //Speichern
                        $this->saveBMP($sensorPointId, $sensorId, $temparature, $pressure, $altitude);
                    }
                    break;
                case SensorPointEditor::SENSOR_RAIN:

                    if (isset($request['sensorValues']['value'])) {

                        //Sensorwerte
                        $value = $request['sensorValues']['value'];

                        //Speichern
                        $this->saveRainSensor($sensorPointId, $sensorId, $value);
                    }
                    break;
                case SensorPointEditor::SENSOR_HYGROMETER:

                    if (isset($request['sensorValues']['value'])) {

                        //Sensorwerte
                        $value = $request['sensorValues']['value'];

                        //Speichern
                        $this->saveHygrometer($sensorPointId, $sensorId, $value);
                    }
                    break;
                case SensorPointEditor::SENSOR_LDR:

                    if (isset($request['sensorValues']['value'])) {

                        //Sensorwerte
                        $value = $request['sensorValues']['value'];

                        //Speichern
                        $this->saveLightSensor($sensorPointId, $sensorId, $value);
                    }
                    break;
                case 999:

                    //Sensorpunkt Spannung
                    if (isset($request['voltage'])) {

                        //Spannung
                        $voltgae = $request['voltage'];

                        //Speichern
                        $this->saveSensorPointVoltage($sensorPointId, $voltgae);
                    }
                    break;
                default :

                    throw new Exception('Unbekannter Sensortyp', 1509);
            }

            //XML Daten speichern
            if ($time < DateTime::now()) {

                SensorPointEditor::getInstance()->writeData();
                $time->add($interval);

                if($this->debug) {

                    $response->writeLnColored('Sensordaten gespeichert', 'green');
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
     * speichert die Spannung des Sensorpunktes in der Objektstruktur
     *
     * @param Integer $sensorPointId Sensor Punkt ID
     * @param Integer $voltage       Spannung
     */
    protected function saveSensorPointVoltage($sensorPointId, $voltage) {

        //Sensorpunkt suchen
        $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);
        if ($sensorPoint instanceof SensorPoint) {

            //Sensor Point ist schon bekannt
            $sensorPoint->setTime(DateTime::now());
            $sensorPoint->setVoltage($voltage);
        } else {

            //neuen Sensorpunkt erzeigen
            $sensorPoint = new SensorPoint();
            $sensorPoint->setId($sensorPointId);
            $sensorPoint->setTime(DateTime::now());
            $sensorPoint->setVoltage($voltage);

            //Sensor Registrieren
            SensorPointEditor::getInstance()->addSensorPoint($sensorPoint);
        }
    }

    /**
     * beendet den Server
     */
    public function stop() {
        
        $this->server->stopServer();
        $this->response->writeLnColored(RWF::getLanguage()->get('sensorReciver.stoppedSuccessfully'), 'green');
    }

}
