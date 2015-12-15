<?php

namespace SHC\Sensor\Sensors;

//Imports
use SHC\Sensor\AbstractSensor;
use RWF\Date\DateTime;
use SHC\Sensor\Model\AbstractDistance;
use SHC\Sensor\Model\Distance;

/**
 * HC-SR04 Sensor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class HcSr04 extends AbstractSensor implements Distance {

    use AbstractDistance;

    /**
     * @param Array  $values   Sensorwerte
     */
    public function __construct(array $values = array()) {

        if(count($values) <= 25) {

            $this->oldValues = $values;
            $this->distance = $values[0]['dist'];
            $this->time = $values[0]['time'];
        }
    }

    /**
     * setzt den aktuellen Sensorwert und schiebt ih in das Werte Array
     *
     * @param Float $distance Entfernung
     */
    public function pushValues($distance) {

        $date = DateTime::now();

        //alte Werte Schieben
        array_unshift($this->oldValues, array('dist' => $distance, 'time' => $date));
        //mehr als 5 Werte im Array?
        if(isset($this->oldValues[25])) {

            //aeltesten Wert loeschen
            unset($this->oldValues[25]);
        }

        //Werte setzten
        $this->distance = $distance;
        $this->time = $date;
        $this->isModified = true;
    }
}