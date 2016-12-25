<?php

namespace SHC\Sensor\Sensors;

//Imports
use SHC\Sensor\AbstractSensor;
use RWF\Date\DateTime;
use SHC\Sensor\Model\AbstractPower;
use SHC\Sensor\Model\Power;

/**
 * SCT-013 Sensor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SCT013 extends AbstractSensor implements Power {

    use AbstractPower;

    /**
     * @param Array  $values   Sensorwerte
     */
    public function __construct(array $values = array()) {

        if(count($values) <= 25) {

            $this->oldValues = $values;
            $this->power = $values[0]['power'];
            $this->time = $values[0]['time'];
        }
    }

    /**
     * setzt den aktuellen Sensorwert und schiebt ih in das Werte Array
     *
     * @param Float $power aktuell entnommene Leistung
     */
    public function pushValues($power) {

        $date = DateTime::now();

        //alte Werte Schieben
        array_unshift($this->oldValues, array('power' => $power, 'time' => $date));
        //mehr als 5 Werte im Array?
        if(isset($this->oldValues[25])) {

            //aeltesten Wert loeschen
            unset($this->oldValues[25]);
        }

        //Werte setzten
        $this->power = $power;
        $this->time = $date;
        $this->isModified = true;
    }
}