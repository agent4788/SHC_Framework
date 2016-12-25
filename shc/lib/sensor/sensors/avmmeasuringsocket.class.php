<?php

namespace SHC\Sensor\Sensors;

//Imports
use SHC\Sensor\AbstractSensor;
use RWF\Date\DateTime;
use SHC\Sensor\Model\AbstractEnergy;
use SHC\Sensor\Model\AbstractPower;
use SHC\Sensor\Model\AbstractTemperature;
use SHC\Sensor\Model\Energy;
use SHC\Sensor\Model\Power;
use SHC\Sensor\Model\Temperature;

/**
 * BMP085/180 Sensor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AvmMeasuringSocket extends AbstractSensor implements Temperature, Power, Energy {

    use AbstractTemperature, AbstractPower, AbstractEnergy;

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