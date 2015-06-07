<?php

namespace SHC\Sensor\Sensors;

//Imports
use SHC\Sensor\AbstractSensor;
use RWF\Date\DateTime;

/**
 * BMP085/180 Sensor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AvmMeasuringSocket extends AbstractSensor {

    /**
     * Temperatur
     *
     * @var Float
     */
    protected $temperature = 0.0;

    /**
     * aktuell entnommene Leistung in mW
     *
     * @var int
     */
    protected $power = 0;

    /**
     * entnomme Leistung in Wh
     *
     * @var int
     */
    protected $energy = 0;

    /**
     * Temperatur Anzeigen
     *
     * @var Integer
     */
    protected $temperatureVisibility = 1;

    /**
     * entnommene Leustung Anzeigen
     *
     * @var Integer
     */
    protected $powerVisibility = 1;

    /**
     * Leistung Anzeigen
     *
     * @var Integer
     */
    protected $energyVisibility = 1;

    /**
     * Temperatur Offset
     *
     * @var Float
     */
    protected $temperatureOffset = 0.0;

    /**
     * @param Array   $values   Sensorwerte
     */
    public function __construct(array $values = array()) {

        if(count($values) <= 25) {

            $this->oldValues = $values;
            $this->temperature = $values[0]['temp'];
            $this->power = $values[0]['power'];
            $this->energy = $values[0]['energy'];
            $this->time = $values[0]['time'];
        }
    }

    /**
     * gibt den Aktuellen Temperaturwert zurueck
     *
     * @return Float
     */
    public function getTemperature() {

        return $this->temperature + $this->temperatureOffset;
    }

    /**
     * gibt ie aktuell entnommene Leistung zurueck
     *
     * @return int
     */
    public function getPower() {

        return $this->power;
    }

    /**
     * gibt die entnommene Leistung zurueck
     *
     * @return Float
     */
    public function getEnergy() {

        return $this->energy;
    }

    /**
     * setzt das Temperatur Offset
     *
     * @param  Float $temperatureOffset
     * @return \SHC\Sensor\Sensors\AvmMeasuringSocket
     */
    public function setTemperatureOffset($temperatureOffset) {

        $this->temperatureOffset = $temperatureOffset;
        return $this;
    }

    /**
     * gbit das Temperatur Offset zurueck
     *
     * @return Float
     */
    public function getTemperatureOffset() {

        return $this->temperatureOffset;
    }

    /**
     * setzt die Sichtbarkeit der Temperatur
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Sensors\AvmMeasuringSocket
     */
    public function temperatureVisibility($visibility) {

        $this->temperatureVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit der Temperatur an
     *
     * @return Boolean
     */
    public function isTemperatureVisible() {

        return ($this->temperatureVisibility == 1 ? true : false);
    }

    /**
     * setzt die Sichtbarkeit der aktuell entnommenen Leistung
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Sensors\AvmMeasuringSocket
     */
    public function powerVisibility($visibility) {

        $this->powerVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit  der aktuell entnommenen Leistung an
     *
     * @return Boolean
     */
    public function isPowerVisible() {

        return ($this->powerVisibility == 1 ? true : false);
    }

    /**
     * setzt die Sichtbarkeit  der entnommenen Leistung
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Sensors\AvmMeasuringSocket
     */
    public function energyVisibility($visibility) {

        $this->energyVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit  der entnommenen Leistung an
     *
     * @return Boolean
     */
    public function isEnergyVisible() {

        return ($this->energyVisibility == 1 ? true : false);
    }

    /**
     * setzt den aktuellen Sensorwert und schiebt ih in das Werte Array
     *
     * @param Float $temperature Temperatur
     * @param int   $power       aktuell entnommene Leistung
     * @param int   $energy      entnommene Leistung
     */
    public function pushValues($temperature, $power, $energy) {

        $date = DateTime::now();

        //alte Werte Schieben
        array_unshift($this->oldValues, array('temp' => $temperature, 'power' => $power, 'energy' => $energy, 'time' => $date));
        //mehr als 5 Werte im Array?
        if(isset($this->oldValues[25])) {

            //aeltesten Wert loeschen
            unset($this->oldValues[25]);
        }

        //Werte setzten
        $this->temperature = $temperature;
        $this->power = $power;
        $this->energy = $energy;
        $this->time = $date;
        $this->isModified = true;
    }
}