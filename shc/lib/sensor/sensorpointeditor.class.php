<?php

namespace SHC\Sensor;

//Imports
use RWF\Util\String;
use RWF\Date\DateTime;
use SHC\Core\SHC;
use SHC\Sensor\Sensors\AvmMeasuringSocket;
use SHC\Sensor\Sensors\CometDectRadiatorThermostat;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\EdimaxMeasuringSocket;
use SHC\Sensor\Sensors\GasMeter;
use SHC\Sensor\Sensors\HcSr04;
use SHC\Sensor\Sensors\RainSensor;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;
use SHC\Sensor\Sensors\SCT013;
use SHC\Sensor\Sensors\WaterMeter;
use SHC\Sensor\vSensors\Energy;
use SHC\Sensor\vSensors\FluidAmount;
use SHC\Sensor\vSensors\Humidity;
use SHC\Sensor\vSensors\LightIntensity;
use SHC\Sensor\vSensors\Moisture;
use SHC\Sensor\vSensors\Power;
use SHC\Sensor\vSensors\Temperature;
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
     * AVM Steckdose
     *
     * @var Integer
     */
    const SENSOR_AVM_MEASURING_SOCKET = 7;

    /**
     * EDIMAX Steckdose
     *
     * @var Integer
     */
    const SENSOR_EDIMAX_MEASURING_SOCKET = 8;

    /**
     * Gaszaehler
     *
     * @var Integer
     */
    const SENSOR_GASMETER = 9;

    /**
     * Wasserzaehler
     *
     * @var Integer
     */
    const SENSOR_WATERMETER = 10;

    /**
     * COMET DECT Heitkoerperthermostat
     *
     * @var Integer
     */
    const SENSOR_COMET_DECT_RADIATOR_THERMOSTAT = 11;

    /**
     * Energiemesser
     *
     * @var Integer
     */
    const SENSOR_SCT_013 = 12;

    /**
     * Entfernungsmesser
     *
     * @var Integer
     */
    const SENSOR_HC_SR04 = 13;

    /**
     * Virtueller Sensor Energie
     *
     * @var Integer
     */
    const VSENSOR_ENERGY = 500;

    /**
     * Virtueller Sensor Menge
     *
     * @var Integer
     */
    const VSENSOR_FLUID_AMOUNT = 501;

    /**
     * Virtueller Sensor Luftfeuchte
     *
     * @var Integer
     */
    const VSENSOR_HUMIDITY = 502;

    /**
     * Virtueller Sensor Lichtstärke
     *
     * @var Integer
     */
    const VSENSOR_LIGHT_INTENSITY = 503;

    /**
     * Virtueller Sensor Feuchtigkeit
     *
     * @var Integer
     */
    const VSENSOR_MOISTURE = 504;

    /**
     * Virtueller Sensor Stromverbrauch
     *
     * @var Integer
     */
    const VSENSOR_POWER = 505;

    /**
     * Virtueller Sensor Temperatur
     *
     * @var Integer
     */
    const VSENSOR_TEMPERATURE = 506;

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

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'shc:sensors';

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

        //Sensorpunkte Lesen
        $db = SHC::getDatabase();
        $sensorPoints = $db->hGetAllArray(self::$tableName . ':sensorPoints');
        foreach($sensorPoints as $sensorPointData) {

            $sensorPoint = new SensorPoint();
            $sensorPoint->setId((int) $sensorPointData['id']);
            $sensorPoint->setName((string) $sensorPointData['name']);
            $sensorPoint->setOrderId((int) $sensorPointData['orderId']);
            $sensorPoint->visibility(((int) $sensorPointData['visibility'] == true ? true : false));
            $sensorPoint->setVoltage((float) $sensorPointData['voltage'], false);
            $sensorPoint->setWarnLevel((float) $sensorPointData['warningLevel']);
            $sensorPoint->setTime(DateTime::createFromDatabaseDateTime((string) $sensorPointData['lastConnection']));
            $this->addSensorPoint($sensorPoint);
        }

        //Sensoren lesen
        $sensors = $db->hGetAllArray(self::$tableName .':sensors');
        $vSensors = array();
        foreach($sensors as $sensorData) {

            switch ((int) $sensorData['type']) {

                case self::SENSOR_DS18X20:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'temp' => (float) $dataSet['temp'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new DS18x20($values);
                    $sensor->temperatureVisibility(((int) $sensorData['temperatureVisibility'] == true ? 1 : 0));
                    $sensor->setTemperatureOffset((float) $sensorData['temperatureOffset']);
                    break;
                case self::SENSOR_DHT:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'temp' => (float) $dataSet['temp'],
                            'hum' => (float) $dataSet['hum'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new DHT($values);
                    $sensor->temperatureVisibility(((int) $sensorData['temperatureVisibility'] == true ? 1 : 0));
                    $sensor->humidityVisibility(((int) $sensorData['humidityVisibility'] == true ? 1 : 0));
                    $sensor->setTemperatureOffset((float) $sensorData['temperatureOffset']);
                    $sensor->setHumidityOffset((float) $sensorData['humidityOffset']);
                    break;
                case self::SENSOR_BMP:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'temp' => (float) $dataSet['temp'],
                            'press' => (float) $dataSet['press'],
                            'alti' => (float) $dataSet['alti'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new BMP($values);
                    $sensor->temperatureVisibility(((int) $sensorData['temperatureVisibility'] == true ? 1 : 0));
                    $sensor->airPressureVisibility(((int) $sensorData['pressureVisibility'] == true ? 1 : 0));
                    $sensor->altitudeVisibility(((int) $sensorData['altitudeVisibility'] == true ? 1 : 0));
                    $sensor->setTemperatureOffset((float) $sensorData['temperatureOffset']);
                    $sensor->setAirPressureOffset((float) $sensorData['pressureOffset']);
                    $sensor->setAltitudeOffset((float) $sensorData['altitudeOffset']);
                    break;
                case self::SENSOR_RAIN:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'value' => (float) $dataSet['value'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new RainSensor($values);
                    $sensor->moistureVisibility(((int) $sensorData['valueVisibility'] == true ? 1 : 0));
                    $sensor->setMoistureOffset((int) $sensorData['valueOffset']);
                    break;
                case self::SENSOR_HYGROMETER:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'value' => (float) $dataSet['value'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new Hygrometer($values);
                    $sensor->moistureVisibility(((int) $sensorData['valueVisibility'] == true ? 1 : 0));
                    $sensor->setMoistureOffset((int) $sensorData['valueOffset']);
                    break;
                case self::SENSOR_LDR:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'value' => (float) $dataSet['value'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new LDR($values);
                    $sensor->lightIntensityVisibility(((int) $sensorData['valueVisibility'] == true ? 1 : 0));
                    $sensor->setLightIntensityOffset((int) $sensorData['valueOffset']);
                    break;
                case self::SENSOR_AVM_MEASURING_SOCKET:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'temp' => (float) $dataSet['temp'],
                            'power' => (float) $dataSet['power'],
                            'energy' => (float) $dataSet['energy'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new AvmMeasuringSocket($values);
                    $sensor->temperatureVisibility(((int) $sensorData['temperatureVisibility'] == true ? 1 : 0));
                    $sensor->powerVisibility(((int) $sensorData['powerVisibility'] == true ? 1 : 0));
                    $sensor->energyVisibility(((int) $sensorData['energyVisibility'] == true ? 1 : 0));
                    $sensor->setTemperatureOffset((float) $sensorData['temperatureOffset']);
                    break;
                case self::SENSOR_EDIMAX_MEASURING_SOCKET:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'power' => (float) $dataSet['power'],
                            'energy' => (float) $dataSet['energy'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new EdimaxMeasuringSocket($values);
                    $sensor->powerVisibility(((int) $sensorData['powerVisibility'] == true ? 1 : 0));
                    $sensor->energyVisibility(((int) $sensorData['energyVisibility'] == true ? 1 : 0));
                    break;
                case self::SENSOR_GASMETER:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'amount' => (float) $dataSet['amount'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new GasMeter($values);
                    $sensor->fluidAmountVisibility(((int) $sensorData['fluidAmountVisibility'] == true ? 1 : 0));
                    break;
                case self::SENSOR_WATERMETER:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'amount' => (float) $dataSet['amount'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new WaterMeter($values);
                    $sensor->fluidAmountVisibility(((int) $sensorData['fluidAmountVisibility'] == true ? 1 : 0));
                    break;
                case self::SENSOR_COMET_DECT_RADIATOR_THERMOSTAT:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'temp' => (float) $dataSet['temp'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new CometDectRadiatorThermostat($values);
                    $sensor->temperatureVisibility(((int) $sensorData['temperatureVisibility'] == true ? 1 : 0));
                    $sensor->setTemperatureOffset((float) $sensorData['temperatureOffset']);
                    break;
                case self::SENSOR_SCT_013:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'power' => (float) $dataSet['power'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new SCT013($values);
                    $sensor->powerVisibility(((int) $sensorData['powerVisibility'] == true ? 1 : 0));
                    break;
                case self::SENSOR_HC_SR04:

                    //Sensorwerte lesen
                    $values = array();
                    $list = $db->lRangeArray(self::$tableName .':sensorData:'. $sensorData['id'], 0, -1);
                    foreach($list as $dataSet) {

                        $values[] = array(
                            'dist' => (float) $dataSet['dist'],
                            'time' => DateTime::createFromDatabaseDateTime((string) $dataSet['time'])
                        );
                    }

                    $sensor = new HcSr04($values);
                    $sensor->distanceVisibility(((int) $sensorData['distanceVisibility'] == true ? 1 : 0));
                    $sensor->setDistanceOffset((float) $sensorData['distanceOffset']);
                    break;
                case self::VSENSOR_ENERGY:

                    $sensor = new Energy();
                    $vSensors[(int) $sensorData['id']] = $sensorData['sensors'];
                    break;
                case self::VSENSOR_TEMPERATURE:

                    $sensor = new Temperature();
                    $vSensors[(int) $sensorData['id']] = $sensorData['sensors'];
                    break;
                case self::VSENSOR_POWER:

                    $sensor = new Power();
                    $vSensors[(int) $sensorData['id']] = $sensorData['sensors'];
                    break;
                case self::VSENSOR_MOISTURE:

                    $sensor = new Moisture();
                    $vSensors[(int) $sensorData['id']] = $sensorData['sensors'];
                    break;
                case self::VSENSOR_LIGHT_INTENSITY:

                    $sensor = new LightIntensity();
                    $vSensors[(int) $sensorData['id']] = $sensorData['sensors'];
                    break;
                case self::VSENSOR_FLUID_AMOUNT:

                    $sensor = new FluidAmount();
                    $vSensors[(int) $sensorData['id']] = $sensorData['sensors'];
                    break;
                case self::VSENSOR_HUMIDITY:

                    $sensor = new Humidity();
                    $vSensors[(int) $sensorData['id']] = $sensorData['sensors'];
                    break;
            }

            //Allgemeine Daten setzen
            $sensor->setId((string) $sensorData['id']);
            $sensor->setIcon((isset($sensorData['icon']) ? (string) $sensorData['icon'] : ''));
            $sensor->setSensorPointId((int) $sensorData['sensorPointId']);
            $sensor->setName((string) $sensorData['name']);
            $sensor->setRooms($sensorData['rooms']);
            $sensor->setOrder( $sensorData['order']);
            $sensor->visibility(((int) $sensorData['visibility'] == true ? true : false));
            $sensor->enableDataRecording(((int) $sensorData['dataRecording'] == 1 ? true : false));

            //Sensor zum Sensorpunkt hinzufuegen
            if(isset($this->sensorPoints[$sensor->getSensorPointId()])) {

                /* @var $sensorPoint \SHC\Sensor\SensorPoint */
                $sensorPoint = $this->sensorPoints[$sensor->getSensorPointId()];
                $sensorPoint->addSensor($sensor);
            }
        }

        //Sensoren den Virtuellen Sensoren hinzufügen
        foreach($vSensors as $id => $sensors) {

            $sensor = $this->getSensorById($id);
            /* @var $sensor \SHC\Sensor\vSensor */
            foreach($sensors as $usedSensorId) {

                $sensor->addSensor($this->getSensorById($usedSensorId));
            }
            $sensor->processData();
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
     * erstellt einen neuen Sensorpunkt
     *
     * @param  Integer $spId Sensor Punkt ID
     * @return Boolean
     */
    public Function createSensorPoint($spId) {

        $newSensorPoint = array(
            'id' => $spId,
            'name' => '',
            'orderId' => '',
            'visibility' => '',
            'voltage' => '',
            'warningLevel' => '',
            'lastConnection' => DateTime::now()->getDatabaseDateTime()
        );

        if(SHC::getDatabase()->hSetNxArray(self::$tableName . ':sensorPoints', $spId, $newSensorPoint) == 0) {

            return false;
        }
        return true;
    }

    /**
     * setzt die Spannung eunes Sensorpunktes
     *
     * @param  Integer $spId    Sensor Punkt ID
     * @param  Float   $voltage Spannung
     * @return Boolean
     */
    public function setSensorPointVoltage($spId, $voltage) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName . ':sensorPoints', $spId)) {

            $sensorPoint = $db->hGetArray(self::$tableName . ':sensorPoints', $spId);
            if(isset($sensorPoint['id']) && (int) $sensorPoint['id'] == $spId) {

                $sensorPoint['voltage'] = $voltage;
                $sensorPoint['lastConnection'] = DateTime::now()->getDatabaseDateTime();

                if($db->hSetArray(self::$tableName . ':sensorPoints', $spId, $sensorPoint) == 0) {

                    return true;
                }
            }
        }
        return false;
    }

    /**
     * setzt die Spannung eunes Sensorpunktes
     *
     * @param  Integer            $spId        Sensor Punkt ID
     * @param  \RWF\Date\DateTime $lastConnect Zeit Objekt
     * @return Boolean
     */
    public function setSensorPointLastConnect($spId, DateTime $lastConnect) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName . ':sensorPoints', $spId)) {

            $sensorPoint = $db->hGetArray(self::$tableName . ':sensorPoints', $spId);
            if(isset($sensorPoint['id']) && (int) $sensorPoint['id'] == $spId) {

                $sensorPoint['lastConnection'] = $lastConnect->getDatabaseDateTime();

                if($db->hSetArray(self::$tableName . ':sensorPoints', $spId, $sensorPoint) == 0) {

                    return true;
                }
            }
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
                } else {

                    $sensors[] = $sensor;
                }
            }
        }

        if ($orderBy == 'id') {

            //nach ID sortieren
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
                if($sensor->isInRoom($roomId)) {

                    if ($orderBy == 'id') {

                        $sensors[$sensor->getId()] = $sensor;
                    } elseif ($orderBy == 'orderId') {

                        $sensors[$sensor->getOrderId($roomId)] = $sensor;
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

            $rooms = $sensor->getRooms();
            if(count($rooms) == 0 || (array_key_exists(0, $rooms) && $rooms[0] === null)) {

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
     * erstellt einen neuen Sensor
     *
     * @param  Integer $spId Sensor Punkt ID
     * @param  String  $sId  Sensor ID
     * @param  Integer $type Typ ID
     * @return Boolean
     */
    public function createSensor($spId, $sId, $type) {

        $newSensor = array(
            'id' => $sId,
            'icon' => '',
            'sensorPointId' => $spId,
            'type' => $type,
            'name' => '',
            'rooms' => array(),
            'order' => array(),
            'visibility' => true,
            'dataRecording' => true
        );

        switch($type) {

            case self::SENSOR_DS18X20:

                $newSensor['temperatureVisibility'] = true;
                $newSensor['temperatureOffset'] = 0.0;
                break;
            case self::SENSOR_DHT:

                $newSensor['temperatureVisibility'] = true;
                $newSensor['humidityVisibility'] = true;
                $newSensor['temperatureOffset'] = 0.0;
                $newSensor['humidityOffset'] = 0.0;
                break;
            case self::SENSOR_BMP:

                $newSensor['temperatureVisibility'] = true;
                $newSensor['pressureVisibility'] = true;
                $newSensor['altitudeVisibility'] = true;
                $newSensor['temperatureOffset'] = 0.0;
                $newSensor['pressureOffset'] = 0.0;
                $newSensor['altitudeOffset'] = 0.0;
                break;
            case self::SENSOR_RAIN:
            case self::SENSOR_HYGROMETER:
            case self::SENSOR_LDR:

                $newSensor['valueVisibility'] = true;
                $newSensor['valueOffset'] = 0;
                break;
            case self::SENSOR_AVM_MEASURING_SOCKET:

                $newSensor['temperatureVisibility'] = true;
                $newSensor['powerVisibility'] = true;
                $newSensor['energyVisibility'] = true;
                $newSensor['temperatureOffset'] = 0.0;
                break;
            case self::SENSOR_EDIMAX_MEASURING_SOCKET:

                $newSensor['powerVisibility'] = true;
                $newSensor['energyVisibility'] = true;
                break;
            case self::SENSOR_GASMETER:
            case self::SENSOR_WATERMETER:

                $newSensor['fluidAmountVisibility'] = true;
                break;
            case self::SENSOR_COMET_DECT_RADIATOR_THERMOSTAT:

                $newSensor['temperatureVisibility'] = true;
                $newSensor['temperatureOffset'] = 0.0;
                break;
            case self::SENSOR_SCT_013:

                $newSensor['powerVisibility'] = true;
                break;
            case self::SENSOR_HC_SR04:

                $newSensor['distanceVisibility'] = true;
                $newSensor['distanceOffset'] = 0.0;
                break;
            case self::VSENSOR_ENERGY:
            case self::VSENSOR_FLUID_AMOUNT:
            case self::VSENSOR_HUMIDITY:
            case self::VSENSOR_LIGHT_INTENSITY:
            case self::VSENSOR_MOISTURE:
            case self::VSENSOR_POWER:
            case self::VSENSOR_TEMPERATURE:

                $newSensor['sensors'] = array();
                break;
        }

        if(SHC::getDatabase()->hSetNxArray(self::$tableName . ':sensors', $sId, $newSensor) == 0) {

            return false;
        }
        return true;
    }

    /**
     * erstellt einen neuen Virtuellen Sensor
     *
     * @param  Integer $type Typ ID
     * @return Integer neue Sensor ID
     */
    public function createVirtualSensor($type) {

        $db = SHC::getDatabase();

        //pruefen ob Sensorpunkt existiert (wenn nicht erstellen)
        if($this->getSensorById(1000) === null) {

            $this->createSensorPoint(1000);
        }

        //Sensor ID
        $newId = 1000;
        if(!$db->exists('autoIncrement:shc:vSensors')) {

            $db->incrBy('autoIncrement:shc:vSensors', $newId);
        } else {

            $newId = $db->autoIncrement('shc:vSensors');
        }

        $this->createSensor(1000, $newId, $type);
        return $newId;
    }

    /**
     * erstellt einen neuen Datensatz mit Sensorwerten
     *
     * @param  Integer $spId   Sensor Punkt ID
     * @param  String  $sId    Sensor ID
     * @param  Integer $type   Typ ID
     * @param  Mixed   $value1 Wert 1
     * @param  Mixed   $value2 Wert 2
     * @param  Mixed   $value3 Wert 3
     * @return Boolean
     */
    public function pushSensorValues($spId, $sId, $type, $value1, $value2 = null, $value3 = null) {

        //Sensorpunkt erstellen falls nicht vorhanden
        if($this->getSensorPointById($spId) === null) {

            if($this->createSensorPoint($spId) === false) {

                //Sensorpunkt konnte nicht erstellt werden
                return false;
            }
        }

        //Sensor erstellen falls nicht vorhanden
        if($this->getSensorById($sId) === null) {

            if($this->createSensor($spId, $sId, $type) === false) {

                //Sensor konnte nicht erstellt werden
                return false;
            }
        }

        //Daten vorbereiten
        $data = array();
        switch($type) {

            case self::SENSOR_DS18X20:

                $data = array(
                    'temp' => (float) $value1,
                    'time' => DateTime::now()->getDatabaseDateTime()
                );
                break;
            case self::SENSOR_DHT:

                $data = array(
                    'temp' => (float) $value1,
                    'hum' => (float) $value2,
                    'time' => DateTime::now()->getDatabaseDateTime()
                );
                break;
            case self::SENSOR_BMP:

                $data = array(
                    'temp' => (float) $value1,
                    'press' => (float) $value2,
                    'alti' => (float) $value3,
                    'time' => DateTime::now()->getDatabaseDateTime()
                );
                break;
            case self::SENSOR_RAIN:
            case self::SENSOR_HYGROMETER:
            case self::SENSOR_LDR:

                $data = array(
                    'value' => (int) $value1,
                    'time' => DateTime::now()->getDatabaseDateTime()
                );
                break;
            case self::SENSOR_AVM_MEASURING_SOCKET:

                $data = array(
                    'temp' => (float) $value1,
                    'power' => (float) $value2,
                    'energy' => (float) $value3,
                    'time' => DateTime::now()->getDatabaseDateTime()
                );
                break;
            case self::SENSOR_EDIMAX_MEASURING_SOCKET:

                $data = array(
                    'power' => (float) $value1,
                    'energy' => (float) $value2,
                    'time' => DateTime::now()->getDatabaseDateTime()
                );
                break;
            case self::SENSOR_GASMETER:
            case self::SENSOR_WATERMETER:

                $knownAmount = 0;
                if(SHC::getDatabase()->exists(self::$tableName .':sensorData:'. $sId)) {

                    $sensorData = SHC::getDatabase()->lRangeArray(self::$tableName .':sensorData:'. $sId, 0, 0);
                    if(isset($sensorData[0])) {

                        $knownAmount = (float) $sensorData[0]['amount'];
                    }
                }
                $data = array(
                    'amount' => (float) $knownAmount + $value1,
                    'time' => DateTime::now()->getDatabaseDateTime()
                );
                break;
            case self::SENSOR_COMET_DECT_RADIATOR_THERMOSTAT:

                $data = array(
                    'temp' => (float) $value1,
                    'time' => DateTime::now()->getDatabaseDateTime()
                );
                break;
            case self::SENSOR_SCT_013:

                $data = array(
                    'power' => (float) $value1,
                    'time' => DateTime::now()->getDatabaseDateTime()
                );
                break;
            case self::SENSOR_HC_SR04:

                $data = array(
                    'dist' => (float) $value1,
                    'time' => DateTime::now()->getDatabaseDateTime()
                );
                break;

        }

        if(SHC::getDatabase()->lPushArray(self::$tableName .':sensorData:'. $sId, $data) !== false) {

            SHC::getDatabase()->lTrim(self::$tableName .':sensorData:'. $sId, 0, 24);
            $this->setSensorPointLastConnect($spId, DateTime::now());
            return true;
        }
        return false;
    }

    /**
     * prueft ob der Name des Sensorpunktes schon vergeben ist
     *
     * @param  String  $name
     * @return Boolean
     */
    public function isSensorPointNameAvailable($name) {

        foreach($this->sensorPoints as $sensorPoint) {

            /* @var $sensorPoint \SHC\Sensor\Sensorpoint */
            if (String::toLower($sensorPoint->getName()) == String::toLower($name)) {

                return false;
            }
        }
        return true;
    }

    /**
     * bearbeitet die Sortierung der Sensorpunkte
     * 
     * @param  Array   $order Array mit Element ID als Index und Sortierungs ID als Wert
     * @return Boolean
     */
    public function editSensorPointOrder(array $order) {

        $db = SHC::getDatabase();
        foreach($order as $spId => $orderId) {

            if(isset($this->sensorPoints[$spId])) {

                $sensorPoint = $db->hGetArray(self::$tableName . ':sensorPoints', $spId);
                if(isset($sensorPoint['id']) && (int) $sensorPoint['id'] == $spId) {

                    $sensorPoint['orderId'] = $orderId;

                    if($db->hSetArray(self::$tableName . ':sensorPoints', $spId, $sensorPoint) != 0) {

                        return false;
                    }
                } else {

                    return false;
                }
            }
        }
        return true;
    }

    /**
     * bearbeitet einen Sensorpunkt
     * 
     * @param  Integer $spId         ID
     * @param  String  $name         Name
     * @param  Boolean $visibility   Sichtbarkeit
     * @param  Float   $warningLevel Warnungs Level
     */
    public function editSensorPoint($spId, $name = null, $visibility = null, $warningLevel = null) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName . ':sensorPoints', $spId)) {

            $sensorPoint = $db->hGetArray(self::$tableName . ':sensorPoints', $spId);

            //Name
            if ($name !== null) {

                //Ausnahme wenn Elementname schon belegt
                if ((string) $sensorPoint['name'] != $name && !$this->isSensorPointNameAvailable($name)) {

                    throw new \Exception('Der Name ist schon vergeben', 1507);
                }
                $sensorPoint['name'] = $name;

                //Sichtbarkeit
                if ($visibility !== null) {

                    $sensorPoint['visibility'] = ($visibility == true ? true : false);
                }

                //Warnungslevel
                if ($warningLevel !== null) {

                    $sensorPoint['warningLevel'] = $warningLevel;
                }

                if($db->hSetArray(self::$tableName . ':sensorPoints', $spId, $sensorPoint) == 0) {

                    return true;
                }
            }
        }
        return false;
    }

    /**
     * loescht einen Sensorpunkt inkl. aller zugehoerigen Sensoren
     * 
     * @param  Integer $spId ID
     * @return Boolean
     */
    public function removeSensorPoint($spId) {

        //Sensoren loeschen
        $sensors = $this->getSensorPointById($spId)->listSensors();
        foreach($sensors as $sensor) {

            /* @var $sensor \SHC\Sensor\Sensor */
            $this->removeSensor($sensor->getId());
        }

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName . ':sensorPoints', $spId)) {

            if($db->hDel(self::$tableName . ':sensorPoints', $spId)) {

                return true;
            }
        }
        return false;
    }

    /**
     * bearbeitet die Sortierung der Sensoren
     * 
     * @param  Array   $order Array mit Element ID als Index und Sortierungs ID als Wert
     * @return Boolean
     */
    public function editSensorOrder(array $order) {

        $db = SHC::getDatabase();
        foreach($order as $sId => $sOrder) {

            if($this->getSensorById($sId) !== null) {

                $sensorData = $db->hGetArray(self::$tableName . ':sensors', $sId);
                if(isset($sensorData['id']) && (int) $sensorData['id'] == $sId) {

                    foreach($sOrder as $roomId => $roomOrder) {

                        if($sensorData['order'][$roomId]) {

                            $sensorData['order'][$roomId] = $roomOrder;
                        }
                    }

                    if($db->hSetArray(self::$tableName . ':sensors', $sId, $sensorData) != 0) {

                        return false;
                    }
                } else {

                    return false;
                }
            }
        }
        return true;
    }

    /**
     * bearbeitet einen Sensor
     * 
     * @param  String  $sId           ID
     * @param  String  $name          Name
     * @param  String  $icon          Icon
     * @param  Array   $rooms         Raeume
     * @param  Array   $order         Sortierung
     * @param  Boolean $visibility    Sichtbarkeit
     * @param  Boolean $dataRecording Datenaufzeichnung aktiv
     * @param  Array   $data          Zusatzdaten
     * @return Boolean
     */
    protected function editSensor($sId, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $dataRecording = null, array $data = array()) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName . ':sensors', $sId)) {

            $sensor = $db->hGetArray(self::$tableName . ':sensors', $sId);

            //Name
            if ($name !== null) {

                $sensor['name'] = $name;
            }

            //Icon
            if ($icon !== null) {

                $sensor['icon'] = $icon;
            }

            //Sichtbarkeit
            if ($visibility !== null) {

                $sensor['visibility'] = ($visibility == true ? true : false);
            }

            //Raum
            if ($rooms !== null) {

                //Sortierung der Raeume behabdeln
                //Vergleichen
                $oldRooms = $sensor['rooms'];
                $removedRooms = array_diff($oldRooms, $rooms);
                $addedRooms = array_diff($rooms, $oldRooms);

                //sortierung vorbereiten
                if($order === null) {

                    $order = $sensor['order'];
                }

                //entfernte Raeume
                foreach($removedRooms as $roomId) {

                    if(isset($order[$roomId])) {

                        unset($order[$roomId]);
                    }
                }

                //hinzugefuegte Raeume
                foreach($addedRooms as $roomId) {

                    $order[$roomId] = ViewHelperEditor::getInstance()->getNextOrderId();
                }

                $sensor['rooms'] = $rooms;
            }

            //Sortierungs ID
            if ($order !== null) {

                $sensor['order'] = $order;
            }

            //$atenaufzeichnung
            if ($dataRecording !== null) {

                $sensor['dataRecording'] = ($dataRecording == true ? true : false);
            }

            //Zusatzdaten
            foreach ($data as $index => $value) {

                if (!in_array($index, array('id', 'name', 'visibility', 'roomId', 'orderId', 'dataRecording')) && $value !== null) {

                    $sensor[$index] = $value;
                }
            }

            if($db->hSetArray(self::$tableName . ':sensors', $sId, $sensor) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * bearbeitet einen DS18x20 Sensor
     * 
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  String  $icon                     Icon
     * @param  Array   $rooms                    Raeume
     * @param  Array   $order                    Sortierung
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $temperatureVisibility    Sichtbarkeit Temperatur
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @param  Float   $temperatureOffset        Offset
     * @return Boolean
     */
    public function editDS18x20($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $temperatureVisibility = null, $dataRecording = null, $temperatureOffset = null) {
        
        //Zusatzdaten
        $data = array(
            'temperatureVisibility' => $temperatureVisibility,
            'temperatureOffset' => $temperatureOffset
        );
        
        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen DHT Sensor
     * 
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  String  $icon                     Icon
     * @param  Array   $rooms                    Raeume
     * @param  Array   $order                    Sortierung
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $temperatureVisibility    Sichtbarkeit Temperatur
     * @param  Boolean $humidityVisibility       Sichtbarkeit Luftfeuchte
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @param  Float   $temperatureOffset        Offset
     * @param  Float   $humidityOffset           Offset
     * @return Boolean
     */
    public function editDHT($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $temperatureVisibility = null, $humidityVisibility = null, $dataRecording = null, $temperatureOffset = null, $humidityOffset = null) {
        
        //Zusatzdaten
        $data = array(
            'temperatureVisibility' => $temperatureVisibility,
            'humidityVisibility' => $humidityVisibility,
            'temperatureOffset' => $temperatureOffset,
            'humidityOffset' => $humidityOffset
        );
        
        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen BMP Sensor
     * 
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  String  $icon                     Icon
     * @param  Array   $rooms                    Raeume
     * @param  Array   $order                    Sortierung
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $temperatureVisibility    Sichtbarkeit Temperatur
     * @param  Boolean $pressureVisibility       Sichtbarkeit Luftdruck
     * @param  Boolean $altitudeVisibility       Sichtbarkeit Hoehe
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @param  Float   $temperatureOffset        Offset
     * @param  Float   $pressureOffset           Offset
     * @param  Float   $altitudeOffset           Offset
     * @return Boolean
     */
    public function editBMP($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $temperatureVisibility = null, $pressureVisibility = null, $altitudeVisibility = null, $dataRecording = null, $temperatureOffset = null, $pressureOffset = null, $altitudeOffset = null) {
        
        //Zusatzdaten
        $data = array(
            'temperatureVisibility' => $temperatureVisibility,
            'pressureVisibility' => $pressureVisibility,
            'altitudeVisibility' => $altitudeVisibility,
            'temperatureOffset' => $temperatureOffset,
            'pressureOffset' => $pressureOffset,
            'altitudeOffset' => $altitudeOffset
        );
        
        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Regen Sensor
     * 
     * @param  String  $id                 ID
     * @param  String  $name               Name
     * @param  String  $icon               Icon
     * @param  Array   $rooms              Raeume
     * @param  Array   $order              Sortierung
     * @param  Boolean $visibility         Sichtbarkeit
     * @param  Boolean $valueVisibility    Sichtbarkeit Wert
     * @param  Boolean $dataRecording      Datenaufzeichnung aktiv
     * @param  Integer $valueOffset        Offset
     * @return Boolean
     */
    public function editRainSensor($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $valueVisibility = null, $dataRecording = null, $valueOffset = null) {
        
        //Zusatzdaten
        $data = array(
            'valueVisibility' => $valueVisibility,
            'valueOffset' => $valueOffset
        );
        
        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Feuchtigkeits Sensor
     * 
     * @param  String  $id                 ID
     * @param  String  $name               Name
     * @param  String  $icon               Icon
     * @param  Array   $rooms              Raeume
     * @param  Array   $order              Sortierung
     * @param  Boolean $visibility         Sichtbarkeit
     * @param  Boolean $valueVisibility    Sichtbarkeit Wert
     * @param  Boolean $dataRecording      Datenaufzeichnung aktiv
     * @param  Integer $valueOffset        Offset
     * @return Boolean
     */
    public function editHygrometer($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $valueVisibility = null, $dataRecording = null, $valueOffset = null) {
        
        //Zusatzdaten
        $data = array(
            'valueVisibility' => $valueVisibility,
            'valueOffset' => $valueOffset
        );
        
        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Licht Sensor
     * 
     * @param  String  $id                 ID
     * @param  String  $name               Name
     * @param  String  $icon               Icon
     * @param  Array   $rooms              Raeume
     * @param  Array   $order              Sortierung
     * @param  Boolean $visibility         Sichtbarkeit
     * @param  Boolean $valueVisibility    Sichtbarkeit Wert
     * @param  Boolean $dataRecording      Datenaufzeichnung aktiv
     * @param  Integer $valueOffset        Offset
     * @return Boolean
     */
    public function editLDR($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $valueVisibility = null, $dataRecording = null, $valueOffset = null) {
        
        //Zusatzdaten
        $data = array(
            'valueVisibility' => $valueVisibility,
            'valueOffset' => $valueOffset
        );

        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Avm Power Sensor
     *
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  String  $icon                     Icon
     * @param  Array   $rooms                    Raeume
     * @param  Array   $order                    Sortierung
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $temperatureVisibility    Sichtbarkeit Temperatur
     * @param  Boolean $powerVisibility          Sichtbarkeit Luftdruck
     * @param  Boolean $energyVisibility         Sichtbarkeit Hoehe
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @param  Float   $temperatureOffset        Offset
     * @return Boolean
     */
    public function editAvmMeasuringSensor($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $temperatureVisibility = null, $powerVisibility = null, $energyVisibility = null, $dataRecording = null, $temperatureOffset = null) {

        //Zusatzdaten
        $data = array(
            'temperatureVisibility' => $temperatureVisibility,
            'powerVisibility' => $powerVisibility,
            'energyVisibility' => $energyVisibility,
            'temperatureOffset' => $temperatureOffset
        );

        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Edimax Power Sensor
     *
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  String  $icon                     Icon
     * @param  Array   $rooms                    Raeume
     * @param  Array   $order                    Sortierung
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $powerVisibility          Sichtbarkeit Luftdruck
     * @param  Boolean $energyVisibility         Sichtbarkeit Hoehe
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @return Boolean
     */
    public function editEdimaxMeasuringSensor($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $powerVisibility = null, $energyVisibility = null, $dataRecording = null) {

        //Zusatzdaten
        $data = array(
            'powerVisibility' => $powerVisibility,
            'energyVisibility' => $energyVisibility,
        );

        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Wasserzaehler Sensor
     *
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  String  $icon                     Icon
     * @param  Array   $rooms                    Raeume
     * @param  Array   $order                    Sortierung
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $fluidAmountVisibility    Sichtbarkeit Wassermenge
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @return Boolean
     */
    public function editWatermeter($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $fluidAmountVisibility = null, $dataRecording = null) {

        //Zusatzdaten
        $data = array(
            'fluidAmountVisibility' => $fluidAmountVisibility
        );

        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Gaszaehler Sensor
     *
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  String  $icon                     Icon
     * @param  Array   $rooms                    Raeume
     * @param  Array   $order                    Sortierung
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $fluidAmountVisibility    Sichtbarkeit Gasmenge
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @return Boolean
     */
    public function editGasmeter($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $fluidAmountVisibility = null, $dataRecording = null) {

        //Zusatzdaten
        $data = array(
            'fluidAmountVisibility' => $fluidAmountVisibility
        );

        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Comet DECT Thermostat Sensor
     *
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  String  $icon                     Icon
     * @param  Array   $rooms                    Raeume
     * @param  Array   $order                    Sortierung
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $temperatureVisibility    Sichtbarkeit Temperatur
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @param  Float   $temperatureOffset        Offset
     * @return Boolean
     */
    public function editCometDectRadiatoThermostatSensor($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $temperatureVisibility = null, $dataRecording = null, $temperatureOffset = null) {

        //Zusatzdaten
        $data = array(
            'temperatureVisibility' => $temperatureVisibility,
            'temperatureOffset' => $temperatureOffset
        );

        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Comet DECT Thermostat Sensor
     *
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  String  $icon                     Icon
     * @param  Array   $rooms                    Raeume
     * @param  Array   $order                    Sortierung
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $powerVisibility          Sichtbarkeit Momentanverbrauch
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @return Boolean
     */
    public function editSct013($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $powerVisibility = null, $dataRecording = null) {

        //Zusatzdaten
        $data = array(
            'powerVisibility' => $powerVisibility
        );

        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Comet DECT Thermostat Sensor
     *
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  String  $icon                     Icon
     * @param  Array   $rooms                    Raeume
     * @param  Array   $order                    Sortierung
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  Boolean $distanceVisibility       Sichtbarkeit der Entfernung
     * @param  Boolean $dataRecording            Datenaufzeichnung aktiv
     * @param  Float   $distanceOffset           Offset
     * @return Boolean
     */
    public function editHcSr04($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, $distanceVisibility = null, $dataRecording = null, $distanceOffset = null) {

        //Zusatzdaten
        $data = array(
            'distanceVisibility' => $distanceVisibility,
            'distanceOffset' => $distanceOffset
        );

        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, $dataRecording, $data);
    }

    /**
     * bearbeitet einen Comet DECT Thermostat Sensor
     *
     * @param  String  $id                       ID
     * @param  String  $name                     Name
     * @param  String  $icon                     Icon
     * @param  array   $rooms                    Raeume
     * @param  array   $order                    Sortierung
     * @param  Boolean $visibility               Sichtbarkeit
     * @param  array   $sensors                  Liste mit den Sensoren welche der Virtuelle Sensor auswerten soll (ID's)
     * @return Boolean
     */
    public function editVirtualSensor($id, $name = null, $icon = null, $rooms = null, $order = null, $visibility = null, array $sensors) {

        //Zusatzdaten
        $data = array(
            'sensors' => $sensors
        );

        //Sensor bearbeiten
        return $this->editSensor($id, $name, $icon, $rooms, $order, $visibility, false, $data);
    }

    /**
     * entfernt einen Sensor
     * 
     * @param  String  $sId ID
     * @return Boolean
     */
    public function removeSensor($sId) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName . ':sensors', $sId)) {

            if($db->hDel(self::$tableName . ':sensors', $sId)) {

                $db->del(self::$tableName .':sensorData:'. $sId);
                return true;
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
