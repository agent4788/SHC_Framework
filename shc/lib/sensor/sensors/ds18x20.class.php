<?php

namespace SHC\Sensor\Sensors;

//Imports
use SHC\Sensor\AbstractSensor;
use RWF\Date\DateTime;
use SHC\Sensor\Model\AbstractTemperature;
use SHC\Sensor\Model\Temperature;

/**
 * DS18x20 Sensor
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DS18x20 extends AbstractSensor implements Temperature {

    use AbstractTemperature;

    /**
     * @param Array  $values   Sensorwerte
     */
    public function __construct(array $values = array()) {
        
        if(count($values) <= 25) {
            
            $this->oldValues = $values;
            $this->temperature = $values[0]['temp'];
            $this->time = $values[0]['time'];
        }
    }
    
    /**
     * setzt den aktuellen Sensorwert und schiebt ih in das Werte Array
     * 
     * @param Float $temperature Temperatur
     */
    public function pushValues($temperature) {
        
        $date = DateTime::now();
        
        //alte Werte Schieben
        array_unshift($this->oldValues, array('temp' => $temperature, 'time' => $date));
        //mehr als 5 Werte im Array?
        if(isset($this->oldValues[25])) {
            
            //aeltesten Wert loeschen
            unset($this->oldValues[25]);
        }
        
        //Werte setzten
        $this->temperature = $temperature;
        $this->time = $date;
        $this->isModified = true;
    }
}
