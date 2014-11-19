<?php

namespace SHC\Sensor;

//Imports
use RWF\Util\FileUtil;
use RWF\Util\String;
use RWF\XML\XmlFileManager;
use RWF\Date\DateTime;
use SHC\Room\Room;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\RainSensor;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;
use SHC\Room\RoomEditor;
use SHC\View\Room\ViewHelperEditor;

/**
 * Verwaltung der Sensorpunkte
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SensorPointEditor {

    /**
     * nach ID sortieren
     * 
     * @var String
     */
    const SORT_BY_ID = 'id';

    /**
     * nach Namen sortieren
     * 
     * @var String
     */
    const SORT_BY_NAME = 'name';

    /**
     * nach Sortierungs ID sortieren
     * 
     * @var String
     */
    const SORT_BY_ORDER_ID = 'orderId';

    /**
     * nicht sortieren
     * 
     * @var String
     */
    const SORT_NOTHING = 'unsorted';

    /**
     * DS18x20 Sensor
     * 
     * @var Integer
     */
    const SENSOR_DS18X20 = 1;

    /**
     * DHT Sensor
     * 
     * @var Integer
     */
    const SENSOR_DHT = 2;

    /**
     * BMP Sensor
     * 
     * @var Integer
     */
    const SENSOR_BMP = 3;

    /**
     * Regen Sensor
     * 
     * @var Integer
     */
    const SENSOR_RAIN = 4;

    /**
     * Feuchtigkeits Sensor
     * 
     * @var Integer
     */
    const SENSOR_HYGROMETER = 5;

    /**
     * Lichtsensor Sensor
     * 
     * @var Integer
     */
    const SENSOR_LDR = 6;

    /**
     * Sensorpunkte
     * 
     * @var Array 
     */
    protected $sensorPoints = array();

    /**
     * Singleton Instanz
     * 
     * @var \SHC\Sensor\SensorPointEditor
     */
    protected static $instance = null;

    protected function __construct() {

        $this->loadData();
    }

    /**
     * Daten laden
     * 
     * @throws \Exception
     */
    public function loadData() {

        //alte daten loeschen
        $this->sensorPoints = array();

        //Dateien durchlaufen
        foreach (FileUtil::listDirectoryFiles(PATH_SHC_STORAGE . 'sensorpoints/', false, false, true) as $file) {

            //Dateinamen pruefen
            if (preg_match('#sp-(\d{1,3})\.xml#', $file['name'], $match)) {

                //XML Datei am Manager anmelden und XML Objekt laden
                XmlFileManager::getInstance()->registerXmlFile($file['name'], PATH_SHC_STORAGE . 'sensorpoints/' . $file['name']);
                $xml = XmlFileManager::getInstance()->getXmlObject($file['name'], true);

                //Sensorpunkt initalisieren
                $sensorPoint = new SensorPoint();
                $sensorPoint->setId((int) $xml->id);
                $sensorPoint->setName((string) $xml->name);
                $sensorPoint->setOrderId((int) $xml->orderId);
                $sensorPoint->visibility(((int) $xml->visibility == 1 ? true : false));
                $sensorPoint->setVoltage((float) $xml->voltage, false);
                $sensorPoint->setWarnLevel((float) $xml->warningLevel);
                $sensorPoint->setTime(DateTime::createFromDatabaseDateTime((string) $xml->lastConnection));

                //Sensoren Laden
                foreach ($xml->sensors->sensor as $xmlSensor) {

                    //Objekte erstellen und spezifische Daten laden
                    switch ((int) $xmlSensor->type) {

                        case self::SENSOR_DS18X20:

                            //Werte einlesen
                            $values = array();
                            foreach ($xmlSensor->values->value as $value) {

                                $values[] = array(
                                    'temp' => (float) $value->temparature,
                                    'time' => DateTime::createFromDatabaseDateTime((string) $value->time)
                                );
                            }

                            $sensor = new DS18x20($values);
                            $sensor->temperatureVisibility(((int) $xmlSensor->temperatureVisibility == 1 ? true : false));
                            break;
                        case self::SENSOR_DHT:

                            //Werte einlesen
                            $values = array();
                            foreach ($xmlSensor->values->value as $value) {

                                $values[] = array(
                                    'temp' => (float) $value->temparature,
                                    'hum' => (float) $value->humidity,
                                    'time' => DateTime::createFromDatabaseDateTime((string) $value->time)
                                );
                            }

                            $sensor = new DHT($values);
                            $sensor->temperatureVisibility(((int) $xmlSensor->temperatureVisibility == 1 ? true : false));
                            $sensor->humidityVisibility(((int) $xmlSensor->humidityVisibility == 1 ? true : false));
                            break;
                        case self::SENSOR_BMP:

                            //Werte einlesen
                            $values = array();
                            foreach ($xmlSensor->values->value as $value) {

                                $values[] = array(
                                    'temp' => (float) $value->temparature,
                                    'press' => (float) $value->pressure,
                                    'alti' => (float) $value->altitude,
                                    'time' => DateTime::createFromDatabaseDateTime((string) $value->time)
                                );
                            }

                            $sensor = new BMP($values);
                            $sensor->temperatureVisibility(((int) $xmlSensor->temperatureVisibility == 1 ? true : false));
                            $sensor->pressureVisibility(((int) $xmlSensor->pressureVisibility == 1 ? true : false));
                            $sensor->altitudeVisibility(((int) $xmlSensor->altitudeVisibility == 1 ? true : false));
                            break;
                        case self::SENSOR_RAIN:

                            //Werte einlesen
                            $values = array();
                            foreach ($xmlSensor->values->value as $value) {

                                $values[] = array(
                                    'value' => (float) $value->sensorValue,
                                    'time' => DateTime::createFromDatabaseDateTime((string) $value->time)
                                );
                            }

                            $sensor = new RainSensor($values);
                            $sensor->valueVisibility(((int) $xmlSensor->valueVisibility == 1 ? true : false));
                            break;
                        case self::SENSOR_HYGROMETER:

                            //Werte einlesen
                            $values = array();
                            foreach ($xmlSensor->values->value as $value) {

                                $values[] = array(
                                    'value' => (float) $value->sensorValue,
                                    'time' => DateTime::createFromDatabaseDateTime((string) $value->time)
                                );
                            }

                            $sensor = new Hygrometer($values);
                            $sensor->valueVisibility(((int) $xmlSensor->valueVisibility == 1 ? true : false));
                            break;
                        case self::SENSOR_LDR:

                            //Werte einlesen
                            $values = array();
                            foreach ($xmlSensor->values->value as $value) {

                                $values[] = array(
                                    'value' => (float) $value->sensorValue,
                                    'time' => DateTime::createFromDatabaseDateTime((string) $value->time)
                                );
                            }

                            $sensor = new LDR($values);
                            $sensor->valueVisibility(((int) $xmlSensor->valueVisibility == 1 ? true : false));
                            break;
                        default:

                            throw new Exception('Unbekannter Sensortyp', 1509);
                    }

                    //Allgemeine Daten setzen
                    $sensor->setId((string) $xmlSensor->id);
                    $sensor->setName((string) $xmlSensor->name);

                    $room = RoomEditor::getInstance()->getRoomById((int) $xmlSensor->roomId);
                    if ($room !== null) {

                        $sensor->setRoom($room);
                    }
                    $sensor->setOrderId((int) $xmlSensor->orderId);
                    $sensor->visibility(((int) $xmlSensor->visibility == 1 ? true : false));
                    $sensor->enableDataRecording(((int) $xmlSensor->dataRecording == 1 ? true : false));
                    $sensor->setTime(DateTime::createFromDatabaseDateTime((string) $xmlSensor->lastConnection));

                    //Sensor am Sensorpunkt registrieren
                    $sensorPoint->addSensor($sensor, false);
                }

                //Sensorpunkt im Editor registrieren
                $this->addSensorPoint($sensorPoint);
            }
        }
    }

    /**
     * Speichert die Sensorpunkte als XML Dateien ab
     * 
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function writeData() {

        foreach ($this->sensorPoints as $sensorPoint) {

            /* @var $sensorPoint \SHC\Sensor\SensorPoint */

            //pruefen ob der Sensorpunkt verandert wurde
            if (!$sensorPoint->isModified()) {

                //ueberspringen wenn nicht veranedert
                continue;
            }

            //XML Objekt erstrellen
            XmlFileManager::getInstance()->registerXmlFile('sp-' . $sensorPoint->getId(), PATH_SHC_STORAGE . 'sensorpoints/sp-' . $sensorPoint->getId() . '.xml', PATH_SHC_STORAGE . 'default/defaultSensorpoint.xml');
            $xml = XmlFileManager::getInstance()->getXmlObject('sp-' . $sensorPoint->getId(), true);

            //ID behandeln
            if ((int) $xml->id != $sensorPoint->getId()) {

                $xml->id = $sensorPoint->getId();
                $xml->name = $sensorPoint->getName();
            }

            //Allgemeine Daten setzen
            $xml->voltage = $sensorPoint->getVoltage();
            $xml->lastConnection = $sensorPoint->getTime()->getDatabaseDateTime();

            //Sensoren speichern
            foreach ($sensorPoint->listSensors() as $sensor) {

                /* @var $sensor \SHC\Sensor\Sensor */

                //Sensor in XML Daten suchen
                $found = false;
                foreach ($xml->sensors->sensor as $xmlSensor) {

                    //ID vergleichen
                    if ((string) $xmlSensor->id == $sensor->getId()) {

                        //Sensor gefunden
                        $found = true;

                        //Sensordaten speichern
                        if ($sensor instanceof DS18x20) {

                            $sensorValues = $sensor->getOldValues();
                            for ($i = 0; $i < count($sensorValues); $i++) {

                                if (isset($sensorValues[$i]['temp'])) {

                                    $xmlSensor->values->value[$i]->temparature = $sensorValues[$i]['temp'];
                                    $xmlSensor->values->value[$i]->time = $sensorValues[$i]['time']->getDatabaseDateTime();
                                } else {

                                    $xmlSensor->values->value[$i]->temparature = 0.0;
                                    $xmlSensor->values->value[$i]->time = "2000-01-01 00:00:00";
                                }
                            }
                        } elseif ($sensor instanceof DHT) {

                            $sensorValues = $sensor->getOldValues();
                            for ($i = 0; $i < count($sensorValues); $i++) {

                                if (isset($sensorValues[$i]['temp'])) {

                                    $xmlSensor->values->value[$i]->temparature = $sensorValues[$i]['temp'];
                                    $xmlSensor->values->value[$i]->humidity = $sensorValues[$i]['hum'];
                                    $xmlSensor->values->value[$i]->time = $sensorValues[$i]['time']->getDatabaseDateTime();
                                } else {

                                    $xmlSensor->values->value[$i]->temparature = 0.0;
                                    $xmlSensor->values->value[$i]->humidity = 0;
                                    $xmlSensor->values->value[$i]->time = "2000-01-01 00:00:00";
                                }
                            }
                        } elseif ($sensor instanceof BMP) {

                            $sensorValues = $sensor->getOldValues();
                            for ($i = 0; $i < count($sensorValues); $i++) {

                                if (isset($sensorValues[$i]['temp'])) {

                                    $xmlSensor->values->value[$i]->temparature = $sensorValues[$i]['temp'];
                                    $xmlSensor->values->value[$i]->pressure = $sensorValues[$i]['press'];
                                    $xmlSensor->values->value[$i]->altitude = $sensorValues[$i]['alti'];
                                    $xmlSensor->values->value[$i]->time = $sensorValues[$i]['time']->getDatabaseDateTime();
                                } else {

                                    $xmlSensor->values->value[$i]->temparature = 0.0;
                                    $xmlSensor->values->value[$i]->pressure = 0.0;
                                    $xmlSensor->values->value[$i]->altitude = 0.0;
                                    $xmlSensor->values->value[$i]->time = "2000-01-01 00:00:00";
                                }
                            }
                        } elseif ($sensor instanceof RainSensor) {

                            $sensorValues = $sensor->getOldValues();
                            for ($i = 0; $i < count($sensorValues); $i++) {

                                if (isset($sensorValues[$i]['value'])) {

                                    $xmlSensor->values->value[$i]->sensorValue = $sensorValues[$i]['value'];
                                    $xmlSensor->values->value[$i]->time = $sensorValues[$i]['time']->getDatabaseDateTime();
                                } else {

                                    $xmlSensor->values->value[$i]->sensorValue = 0;
                                    $xmlSensor->values->value[$i]->time = "2000-01-01 00:00:00";
                                }
                            }
                        } elseif ($sensor instanceof Hygrometer) {

                            $sensorValues = $sensor->getOldValues();
                            for ($i = 0; $i < count($sensorValues); $i++) {

                                if (isset($sensorValues[$i]['value'])) {

                                    $xmlSensor->values->value[$i]->sensorValue = $sensorValues[$i]['value'];
                                    $xmlSensor->values->value[$i]->time = $sensorValues[$i]['time']->getDatabaseDateTime();
                                } else {

                                    $xmlSensor->values->value[$i]->sensorValue = 0;
                                    $xmlSensor->values->value[$i]->time = "2000-01-01 00:00:00";
                                }
                            }
                        } elseif ($sensor instanceof LDR) {

                            $sensorValues = $sensor->getOldValues();
                            for ($i = 0; $i < count($sensorValues); $i++) {

                                if (isset($sensorValues[$i]['value'])) {

                                    $xmlSensor->values->value[$i]->sensorValue = $sensorValues[$i]['value'];
                                    $xmlSensor->values->value[$i]->time = $sensorValues[$i]['time']->getDatabaseDateTime();
                                } else {

                                    $xmlSensor->values->value[$i]->sensorValue = 0;
                                    $xmlSensor->values->value[$i]->time = "2000-01-01 00:00:00";
                                }
                            }
                        }
                    }
                }

                //Sensor nicht gefunden
                if ($found === false) {

                    //neuen Sensor in XML Daten speichern
                    //Allgemeine Daten
                    $newXmlSensor = $xml->sensors->addChild('sensor');
                    $newXmlSensor->addChild('id', $sensor->getId());
                    $newXmlSensor->addChild('name', '');
                    $newXmlSensor->addChild('roomId', '');
                    $newXmlSensor->addChild('orderId', $sensor->getId());
                    $newXmlSensor->addChild('visibility', 1);
                    $newXmlSensor->addChild('dataRecording', 0);
                    $newXmlSensor->addChild('lastConnection', $sensor->getTime()->getDatabaseDateTime());

                    //Sensor Spezifische Daten
                    if ($sensor instanceof DS18x20) {

                        $newXmlSensor->addChild('type', self::SENSOR_DS18X20);
                        $newXmlSensor->addChild('temperatureVisibility', 1);

                        //Werte
                        $values = $newXmlSensor->addChild('values');
                        $sensorValues = $sensor->getOldValues();
                        for ($i = 0; $i < 5; $i++) {

                            //Werte Schreiben
                            $value = $values->addChild('value');
                            if (isset($sensorValues[$i]['temp'])) {

                                $value->addChild('temparature', $sensorValues[$i]['temp']);
                                $value->addChild('time', $sensorValues[$i]['time']->getDatabaseDateTime());
                            } else {

                                $value->addChild('temparature', 0.0);
                                $value->addChild('time', "2000-01-01 00:00:00");
                            }
                        }
                    } elseif ($sensor instanceof DHT) {

                        $newXmlSensor->addChild('type', self::SENSOR_DHT);
                        $newXmlSensor->addChild('temperatureVisibility', 1);
                        $newXmlSensor->addChild('humidityVisibility', 1);

                        //Werte
                        $values = $newXmlSensor->addChild('values');
                        $sensorValues = $sensor->getOldValues();
                        for ($i = 0; $i < 5; $i++) {

                            //Werte Schreiben
                            $value = $values->addChild('value');
                            if (isset($sensorValues[$i]['temp'])) {

                                $value->addChild('temparature', $sensorValues[$i]['temp']);
                                $value->addChild('humidity', $sensorValues[$i]['hum']);
                                $value->addChild('time', $sensorValues[$i]['time']->getDatabaseDateTime());
                            } else {

                                $value->addChild('temparature', 0.0);
                                $value->addChild('humidity', 0);
                                $value->addChild('time', "2000-01-01 00:00:00");
                            }
                        }
                    } elseif ($sensor instanceof BMP) {

                        $newXmlSensor->addChild('type', self::SENSOR_BMP);
                        $newXmlSensor->addChild('temperatureVisibility', 1);
                        $newXmlSensor->addChild('pressureVisibility', 1);
                        $newXmlSensor->addChild('altitudeVisibility', 1);

                        //Werte
                        $values = $newXmlSensor->addChild('values');
                        $sensorValues = $sensor->getOldValues();
                        for ($i = 0; $i < 5; $i++) {

                            //Werte Schreiben
                            $value = $values->addChild('value');
                            if (isset($sensorValues[$i]['temp'])) {

                                $value->addChild('temparature', $sensorValues[$i]['temp']);
                                $value->addChild('pressure', $sensorValues[$i]['press']);
                                $value->addChild('altitude', $sensorValues[$i]['alti']);
                                $value->addChild('time', $sensorValues[$i]['time']->getDatabaseDateTime());
                            } else {

                                $value->addChild('temparature', 0.0);
                                $value->addChild('pressure', 0.0);
                                $value->addChild('altitude', 0.0);
                                $value->addChild('time', "2000-01-01 00:00:00");
                            }
                        }
                    } elseif ($sensor instanceof RainSensor) {

                        $newXmlSensor->addChild('type', self::SENSOR_RAIN);
                        $newXmlSensor->addChild('valueVisibility', 1);

                        //Werte
                        $values = $newXmlSensor->addChild('values');
                        $sensorValues = $sensor->getOldValues();
                        for ($i = 0; $i < 5; $i++) {

                            //Werte Schreiben
                            $value = $values->addChild('value');
                            if (isset($sensorValues[$i]['value'])) {

                                $value->addChild('sensorValue', $sensorValues[$i]['value']);
                                $value->addChild('time', $sensorValues[$i]['time']->getDatabaseDateTime());
                            } else {

                                $value->addChild('sensorValue', 0);
                                $value->addChild('time', "2000-01-01 00:00:00");
                            }
                        }
                    } elseif ($sensor instanceof Hygrometer) {

                        $newXmlSensor->addChild('type', self::SENSOR_HYGROMETER);
                        $newXmlSensor->addChild('valueVisibility', 1);

                        //Werte
                        $values = $newXmlSensor->addChild('values');
                        $sensorValues = $sensor->getOldValues();
                        for ($i = 0; $i < 5; $i++) {

                            //Werte Schreiben
                            $value = $values->addChild('value');
                            if (isset($sensorValues[$i]['value'])) {

                                $value->addChild('sensorValue', $sensorValues[$i]['value']);
                                $value->addChild('time', $sensorValues[$i]['time']->getDatabaseDateTime());
                            } else {

                                $value->addChild('sensorValue', 0);
                                $value->addChild('time', "2000-01-01 00:00:00");
                            }
                        }
                    } elseif ($sensor instanceof LDR) {

                        $newXmlSensor->addChild('type', self::SENSOR_LDR);
                        $newXmlSensor->addChild('valueVisibility', 1);

                        //Werte
                        $values = $newXmlSensor->addChild('values');
                        $sensorValues = $sensor->getOldValues();
                        for ($i = 0; $i < 5; $i++) {

                            //Werte Schreiben
                            $value = $values->addChild('value');
                            if (isset($sensorValues[$i]['value'])) {

                                $value->addChild('sensorValue', $sensorValues[$i]['value']);
                                $value->addChild('time', $sensorValues[$i]['time']->getDatabaseDateTime());
                            } else {

                                $value->addChild('sensorValue', 0);
                                $value->addChild('time', "2000-01-01 00:00:00");
                            }
                        }
                    }
                }
            }

            //XML Daten speichern
            $xml->save();
        }
    }

    /**
     * fuegt einen neuen Sensor Punkt hinzu
     * 
     * @param  \SHC\Sensor\SensorPoint $sensorPoint Sensor Punkt Objekt
     * @param  Boolean                 $overwrite   Ueberschreiben falls vorhanden?     
     * @return Boolean   
     */
    public function addSensorPoint(SensorPoint $sensorPoint, $overwrite = false) {

        $id = $sensorPoint->getId();
        if ((isset($this->sensorPoints[$id]) && $overwrite == true) || !isset($this->sensorPoints[$id])) {

            $this->sensorPoints[$id] = $sensorPoint;
            return true;
        }
        return false;
    }

    /**
     * gibt falls vorhanden das Objekt des Sensorpunktes zutueck
     * 
     * @param  Integer $id Sensor Punkt ID
     * @return \SHC\Sensor\SensorPoint
     */
    public function getSensorPointById($id) {

        if (isset($this->sensorPoints[$id])) {

            return $this->sensorPoints[$id];
        }
        return null;
    }
    
    /**
     * gibt falls vorhanden das Objekt des Sensors zutueck
     * 
     * @param  Integer $id Sensor ID
     * @return \SHC\Sensor\Sensor
     */
    public function getSensorById($id) {
        
        //Sensor Suchen
        foreach ($this->sensorPoints as $sensorPoint) {

            /* @var $sensorPoint \SHC\Sensor\SensorPoint */
            foreach ($sensorPoint->listSensors()as $sensor) {

                /* @var $sensor \SHC\Sensor\Sensor */
                if ($sensor->getId() == $id) {

                    return $sensor;
                }
            }
        }
        return null;
    }

    /**
     * gibt eine Liste mit allen Sensorpunkten zurueck
     * 
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren, 
     *      orderId => nach Sortierungs ID,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listSensorPoints($orderBy = 'id') {

        if ($orderBy == 'id') {

            //nach ID sortieren
            $sensorPoints = $this->sensorPoints;
            ksort($sensorPoints, SORT_NUMERIC);
            return $sensorPoints;
        } elseif ($orderBy == 'orderId') {

            //nach Sortierungs ID sortieren
            $sensorPoints = array();
            foreach ($this->sensorPoints as $sensorPoint) {

                /* @var $sensorPoint \SHC\Sensor\SensorPoint */
                $sensorPoints[$sensorPoint->getOrderId()] = $sensorPoint;
            }

            ksort($sensorPoints, SORT_NUMERIC);
            return $sensorPoints;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            $sensorPoints = $this->sensorPoints;

            //Sortierfunktion
            $orderFunction = function($a, $b) {

                if ($a->getName() == $b->getName()) {

                    return 0;
                }

                if ($a->getName() < $b->getName()) {

                    return -1;
                }
                return 1;
            };
            usort($sensorPoints, $orderFunction);
            return $sensorPoints;
        }
        return $this->sensorPoints;
    }

    /**
     * gibt eine Liste mit allen Sensoren zurueck
     * 
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren, 
     *      orderId => nach Sortierungs ID,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listSensors($orderBy = 'id') {

        //Alle Sensoren durchlaufen
        $sensors = array();
        foreach ($this->sensorPoints as $sensorPoint) {

            /* @var $sensorPoint \SHC\Sensor\SensorPoint */
            foreach ($sensorPoint->listSensors() as $sensor) {

                /* @var $sensor \SHC\Sensor\Sensor */
                if ($orderBy == 'id') {

                    $sensors[$sensor->getId()] = $sensor;
                } elseif ($orderBy == 'orderId') {

                    $sensors[$sensor->getOrderId()] = $sensor;
                } else {

                    $sensors[] = $sensor;
                }
            }
        }

        if ($orderBy == 'id') {

            //nach ID sortieren
            ksort($sensors, SORT_NUMERIC);
            return $sensors;
        } elseif ($orderBy == 'orderId') {

            //nach Sortierungs ID sortieren
            ksort($sensors, SORT_NUMERIC);
            return $sensors;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            //Sortierfunktion
            $orderFunction = function($a, $b) {

                if ($a->getName() == $b->getName()) {

                    return 0;
                }

                if ($a->getName() < $b->getName()) {

                    return -1;
                }
                return 1;
            };
            usort($sensors, $orderFunction);
            return $sensors;
        }
        return $sensors;
    }

    /**
     * gibt eine Liste mit allen Sensoren zurueck
     *
     * @param  Integer $roomId  ID des Raumes
     * @param  String  $orderBy Art der Sortierung (
     *      id => nach ID sorieren,
     *      name => nach Namen sortieren,
     *      orderId => nach Sortierungs ID,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listSensorsForRoom($roomId, $orderBy = 'id') {

        //Alle Sensoren durchlaufen
        $sensors = array();
        foreach ($this->sensorPoints as $sensorPoint) {

            /* @var $sensorPoint \SHC\Sensor\SensorPoint */
            foreach ($sensorPoint->listSensors() as $sensor) {

                /* @var $sensor \SHC\Sensor\Sensor */
                $room = $sensor->getRoom();
                if($room != null && $room->getId() == $roomId) {

                    if ($orderBy == 'id') {

                        $sensors[$sensor->getId()] = $sensor;
                    } elseif ($orderBy == 'orderId') {

                        $sensors[$sensor->getOrderId()] = $sensor;
                    } else {

                        $sensors[] = $sensor;
                    }
                }
            }
        }

        if ($orderBy == 'id') {

            //nach ID sortieren
            ksort($sensors, SORT_NUMERIC);
            return $sensors;
        } elseif ($orderBy == 'orderId') {

            //nach Sortierungs ID sortieren
            ksort($sensors, SORT_NUMERIC);
            return $sensors;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            //Sortierfunktion
            $orderFunction = function($a, $b) {

                if ($a->getName() == $b->getName()) {

                    return 0;
                }

                if ($a->getName() < $b->getName()) {

                    return -1;
                }
                return 1;
            };
            usort($sensors, $orderFunction);
            return $sensors;
        }
        return $sensors;
    }

    /**
     * gibt eine Liste mit allen Elementen aus die keinem Raum zugeordnet sind
     *
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren,
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listSensorsWithoutRoom($orderBy = 'Id') {

        $sensors = array();
        foreach($this->listSensors(self::SORT_NOTHING) as $sensor) {

            if(!$sensor->getRoom() instanceof Room) {

                if($orderBy == 'name') {

                    $sensors[] = $sensor;
                } else {

                    $sensors[$sensor->getId()] = $sensor;
                }
            }
        }

        //Sortieren und zurueck geben
        if ($orderBy == 'id') {

            //nach ID sortieren
            ksort($sensors, SORT_NUMERIC);
            return $sensors;
        } elseif ($orderBy == 'name') {

            //Sortierfunktion
            $orderFunction = function($a, $b) {

                if ($a->getName() == $b->getName()) {

                    return 0;
                }

                if ($a->getName() < $b->getName()) {

                    return -1;
                }
                return 1;
            };
            usort($sensors, $orderFunction);
            return $sensors;
        }
        return $sensors;
    }

    /**
     * bearbeitet die Sortierung der Sensorpunkte
     * 
     * @param  Array   $order Array mit Element ID als Index und Sortierungs ID als Wert
     * @return Boolean
     */
    public function editSensorPointOrder(array $order) {

        foreach ($order as $spId => $orderId) {

            if (file_exists(PATH_SHC_STORAGE . 'sensorpoints/sp-' . $spId . '.xml')) {

                //XML Objekt erstellen
                XmlFileManager::getInstance()->registerXmlFile('sp-' . $spId, PATH_SHC_STORAGE . 'sensorpoints/sp-' . $spId . '.xml');
                $xml = XmlFileManager::getInstance()->getXmlObject('sp-' . $spId);

                //Sortierungs ID setzen
                $xml->orderId = $orderId;

                //XML Speichern
                $xml->save();
            }
        }
        return true;
    }

    /**
     * prueft ob der Name des Sensorpunktes schon verwendet wird
     * 
     * @param  String  $name Name
     * @return Boolean
     */
    public function isSensorPointNameAvailable($name) {

        foreach ($this->sensorPoints as $sensorPoint) {

            /* @var $sensorPoint \SHC\Sensor\SensorPoint */
            if (String::toLower($sensorPoint->getName()) == String::toLower($name)) {

                return false;
            }
        }
        return true;
    }

    /**
     * bearbeitet einen Sensorpunkt
     * 
     * @param  Integer $id           ID
     * @param  String  $name         Name
     * @param  Boolean $visibility   Sichtbarkeit
     * @param  Float   $warningLevel Warnungs Level
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editSensorPoint($id, $name = null, $visibility = null, $warningLevel = null) {

        if (file_exists(PATH_SHC_STORAGE . 'sensorpoints/sp-' . $id . '.xml')) {

            //XML Objekt erstellen
            XmlFileManager::getInstance()->registerXmlFile('sp-' . $id, PATH_SHC_STORAGE . 'sensorpoints/sp-' . $id . '.xml');
            $xml = XmlFileManager::getInstance()->getXmlObject('sp-' . $id);

            //Name
            if ($name !== null) {

                //Ausnahme wenn Elementname schon belegt
                if ((string) $xml->name != $name && !$this->isSensorPointNameAvailable($name)) {

                    throw new \Exception('Der Name ist schon vergeben', 1507);
                }
                $xml->name = $name;
            }

            //Sichtbarkeit
            if ($visibility !== null) {

                $xml->visibility = ($visibility == true ? 1 : 0);
            }

            //Warnungslevel
            if ($warningLevel !== null) {

                $xml->warningLevel = $warningLevel;
            }

            //Daten Speichern
            $xml->save();
            return true;
        }
        return false;
    }

    /**
     * loescht einen Sensorpunkt inkl. aller zugehoerigen Sensoren
     * 
     * @param  Integer $sensorPointId ID
     * @return Boolean
     */
    public function removeSensorPoint($sensorPointId) {

        if (file_exists(PATH_SHC_STORAGE . 'sensorpoints/sp-' . $sensorPointId . '.xml')) {

            //XML Objekt erstellen
            XmlFileManager::getInstance()->registerXmlFile('sp-' . $sensorPointId, PATH_SHC_STORAGE . 'sensorpoints/sp-' . $sensorPointId . '.xml');
            $xml = XmlFileManager::getInstance()->getXmlObject('sp-' . $sensorPointId);

            //Sortierungs ID setzen
            if ((string) $xml->id == $sensorPointId) {

                if (@unlink($xml->getFileName())) {

                    return true;
                }
            }
            return false;
        }
    }

    /**
     * bearbeitet die Sortierung der Sensoren
     * 
     * @param  Array   $order Array mit Element ID als Index und Sortierungs ID als Wert
     * @return Boolean
     */
    public function editSensorOrder(array $order) {

        //Dateien durchlaufen
        foreach (FileUtil::listDirectoryFiles(PATH_SHC_STORAGE . 'sensorpoints/', false, false, true) as $file) {

            //Dateinamen pruefen
            if (preg_match('#sp-(\d{1,3})\.xml#', $file['name'], $match)) {

                //XML Datei am Manager anmelden und XML Objekt laden
                XmlFileManager::getInstance()->registerXmlFile($file['name'], PATH_SHC_STORAGE . 'sensorpoints/' . $file['name']);
                $xml = XmlFileManager::getInstance()->getXmlObject($file['name']);

                foreach ($xml->sensors->sensor as $sensor) {

                    if (isset($order[(string) $sensor->id])) {

                        $sensor->orderId = $order[(string) $sensor->id];
                    }
                }

                //Daten Speichern
                $xml->save();
            }
        }
        return true;
    }

    /**
     * prueft ob der Name des Sensors schon verwendet wird
     * 
     * @param  String  $name Name
     * @return Boolean
     */
    public function isSensorNameAvailable($name) {

        foreach ($this->sensorPoints as $sensorPoint) {

            /* @var $sensorPoint \SHC\Sensor\SensorPoint */
            foreach ($sensorPoint->listSensors() as $sensor) {

                /* @var $sensor \SHC\Sensor\Sensor */
                if (String::toLower($sensor->getName()) == String::toLower($name)) {

                    return false;
                }
            }
        }
        return true;
    }

    /**
     * bearbeitet einen Sensor
     * 
     * @param  String  $id            ID
     * @param  String  $name          Name
     * @param  Integer $roomId        Raum ID
     * @param  Integer $orderId       Sortierungs ID
     * @param  Boolean $visibility    Sichtbarkeit
     * @param  Boolean $dataRecording Datenaufzeichnung aktiv
     * @param  Array   $data          Zusatzdaten
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    protected function editSensor($id, $name = null, $roomId = null, $orderId = null, $visibility = null, $dataRecording = null, array $data = array()) {

        //Sensor Suchen
        foreach ($this->sensorPoints as $sensorPoint) {

            /* @var $sensorPoint \SHC\Sensor\SensorPoint */
            foreach ($sensorPoint->listSensors()as $sensor) {

                /* @var $sensor \SHC\Sensor\Sensor */
                if ($sensor->getId() == $id) {

                    if (file_exists(PATH_SHC_STORAGE . 'sensorpoints/sp-' . $sensorPoint->getId() . '.xml')) {

                        //XML Datei am Manager anmelden und XML Objekt laden
                        XmlFileManager::getInstance()->registerXmlFile('sp-' . $sensorPoint->getId(), PATH_SHC_STORAGE . 'sensorpoints/sp-' . $sensorPoint->getId() . '.xml');
                        $xml = XmlFileManager::getInstance()->getXmlObject('sp-' . $sensorPoint->getId());

                        //Sensor in XML Datei suchen
                        foreach ($xml->sensors->sensor as $xmlSensor) {

                            /* @var $xmlSensor \SimpleXmlElement */
                            if ((string) $xmlSensor->id == $id) {

                                //Name
                                if ($name !== null) {

                                    //Ausnahme wenn Name der Bedingung schon belegt
                                    if ($name != (string) $xmlSensor->name && !$this->isSensorNameAvailable($name)) {

                                        throw new \Exception('Der Name ist schon vergeben', 1507);
                                    }

                                    $xmlSensor->name = $name;
                                }

                                if ($visibility !== null) {

                                    $xmlSensor->visibility = ($visibility == true ? 1 : 0);
                                }

                                //Raum
                                if ($roomId !== null) {

                                    if((int )$xmlSensor->roomId != $roomId && $orderId === null) {

                                        //Bei Raumwechsel neue Sortieruzngs ID setzen
                                        $xmlSensor->orderId = ViewHelperEditor::getInstance()->getNextOrderId();
                                    }
                                    $xmlSensor->roomId = $roomId;
                                }

                                //Sortierungs ID
                                if ($orderId !== null) {

                                    $xmlSensor->orderId = $orderId;
                                }

                                //$atenaufzeichnung
                                if ($dataRecording !== null) {

                                    $xmlSensor->dataRecording = ($dataRecording == true ? 1 : 0);
                                }

                                //Zusatzdaten
                                foreach ($data as $tag => $value) {

                                    if (!in_array($tag, array('id', 'name', 'visibility', 'roomId', 'orderId', 'dataRecording', 'lastConnection')) && $value !== null) {

                                        $xmlSensor->$tag = $value;
                                    }
                                }
                                
                                //Daten Speichern
                                $xml->save();
                                return true;
                            }
                        }
                    }
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * bearbeitet einen DS18x20 Sensor
     * 
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  Integer $roomId                   Raum ID
     * @param  Integer $orderId                  Sortierungs ID
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $temperatureVisibility    Sichtbarkeit Temperatur
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editDS18x20($id, $name = null, $roomId = null, $orderId = null, $visibility = null, $temperatureVisibility = null, $dataRecording = null) {
        
        //Zusatzdaten
        $data = array(
            'temperatureVisibility' => $temperatureVisibility
        );
        
        //Sensor bearbeiten
        return $this->editSensor($id, $name, $roomId, $orderId, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen DHT Sensor
     * 
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  Integer $roomId                   Raum ID
     * @param  Integer $orderId                  Sortierungs ID
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $temperatureVisibility    Sichtbarkeit Temperatur
     * @param  Boolean $humidityVisibility       Sichtbarkeit Luftfeuchte
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editDHT($id, $name = null, $roomId = null, $orderId = null, $visibility = null, $temperatureVisibility = null, $humidityVisibility = null, $dataRecording = null) {
        
        //Zusatzdaten
        $data = array(
            'temperatureVisibility' => $temperatureVisibility,
            'humidityVisibility' => $humidityVisibility    
        );
        
        //Sensor bearbeiten
        return $this->editSensor($id, $name, $roomId, $orderId, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen BMP Sensor
     * 
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  Integer $roomId                   Raum ID
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $temperatureVisibility    Sichtbarkeit Temperatur
     * @param  Boolean $pressureVisibility       Sichtbarkeit Luftdruck
     * @param  Boolean $altitudeVisibility       Sichtbarkeit Hoehe
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editBMP($id, $name = null, $roomId = null, $orderId = null, $visibility = null, $temperatureVisibility = null, $pressureVisibility = null, $altitudeVisibility = null, $dataRecording = null) {
        
        //Zusatzdaten
        $data = array(
            'temperatureVisibility' => $temperatureVisibility,
            'pressureVisibility' => $pressureVisibility,
            'altitudeVisibility' => $altitudeVisibility
        );
        
        //Sensor bearbeiten
        return $this->editSensor($id, $name, $roomId, $orderId, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Regen Sensor
     * 
     * @param  String  $id                 ID
     * @param  String  $name               Name
     * @param  Integer $roomId             Raum ID
     * @param  Integer $orderId            Sortierungs ID
     * @param  Boolean $visibility         Sichtbarkeit
     * @param  Boolean $valueVisibility    Sichtbarkeit Wert
     * @param  Boolean $dataRecording      Datenaufzeichnung aktiv
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRainSensor($id, $name = null, $roomId = null, $orderId = null, $visibility = null, $valueVisibility = null, $dataRecording = null) {
        
        //Zusatzdaten
        $data = array(
            'valueVisibility' => $valueVisibility
        );
        
        //Sensor bearbeiten
        return $this->editSensor($id, $name, $roomId, $orderId, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Feuchtigkeits Sensor
     * 
     * @param  String  $id                 ID
     * @param  String  $name               Name
     * @param  Integer $roomId             Raum ID
     * @param  Integer $orderId            Sortierungs ID
     * @param  Boolean $visibility         Sichtbarkeit
     * @param  Boolean $valueVisibility    Sichtbarkeit Wert
     * @param  Boolean $dataRecording      Datenaufzeichnung aktiv
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editHygrometer($id, $name = null, $roomId = null, $orderId = null, $visibility = null, $valueVisibility = null, $dataRecording = null) {
        
        //Zusatzdaten
        $data = array(
            'valueVisibility' => $valueVisibility
        );
        
        //Sensor bearbeiten
        return $this->editSensor($id, $name, $roomId, $orderId, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Licht Sensor
     * 
     * @param  String  $id                 ID
     * @param  String  $name               Name
     * @param  Integer $roomId             Raum ID
     * @param  Integer $orderId            Sortierungs ID
     * @param  Boolean $visibility         Sichtbarkeit
     * @param  Boolean $valueVisibility    Sichtbarkeit Wert
     * @param  Boolean $dataRecording      Datenaufzeichnung aktiv
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editLDR($id, $name = null, $roomId = null, $orderId = null, $visibility = null, $valueVisibility = null, $dataRecording = null) {
        
        //Zusatzdaten
        $data = array(
            'valueVisibility' => $valueVisibility
        );

        //Sensor bearbeiten
        return $this->editSensor($id, $name, $roomId, $orderId, $visibility, $dataRecording, $data);
    }

    /**
     * entfernt einen Sensor
     * 
     * @param  String $id ID
     * @return Boolean
     */
    public function removeSensor($id) {
        
        //Sensor Suchen
        foreach ($this->sensorPoints as $sensorPoint) {

            /* @var $sensorPoint \SHC\Sensor\SensorPoint */
            foreach ($sensorPoint->listSensors()as $sensor) {

                /* @var $sensor \SHC\Sensor\Sensor */
                if ($sensor->getId() == $id) {

                    if (file_exists(PATH_SHC_STORAGE . 'sensorpoints/sp-' . $sensorPoint->getId() . '.xml')) {

                        //XML Datei am Manager anmelden und XML Objekt laden
                        XmlFileManager::getInstance()->registerXmlFile('sp-' . $sensorPoint->getId(), PATH_SHC_STORAGE . 'sensorpoints/sp-' . $sensorPoint->getId() . '.xml');
                        $xml = XmlFileManager::getInstance()->getXmlObject('sp-' . $sensorPoint->getId());

                        //Sensor in XML Datei suchen
                        for ($i = 0; $i < count($xml->sensors->sensor); $i++) {

                            /* @var $xmlSensor \SimpleXmlElement */
                            if ((string) $xml->sensors->sensor[$i]->id == $id) {

                                //Sensor entfernen
                                unset($xml->sensors->sensor[$i]);
                                
                                //Daten Speichern
                                $xml->save();
                                return true;
                            }
                        }
                    }
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {
        
    }

    /**
     * gibt den Editor fuer Sensorpunke zurueck
     * 
     * @return \SHC\Sensor\SensorPointEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new SensorPointEditor();
        }
        return self::$instance;
    }

}
