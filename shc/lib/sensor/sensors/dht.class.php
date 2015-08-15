<?php

namespace SHC\Sensor\Sensors;

//Imports
use SHC\Sensor\AbstractSensor;
use RWF\Date\DateTime;
use SHC\Sensor\Model\AbstractHumidity;
use SHC\Sensor\Model\AbstractTemperature;
use SHC\Sensor\Model\Humidity;
use SHC\Sensor\Model\Temperature;

/**
 * DHT11/22 Sensor
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DHT extends AbstractSensor implements Temperature, Humidity {

    use AbstractTemperature, AbstractHumidity;
    
    /**
     * @param Array   $values   Sensorwerte
     */
    public function __construct(array $values = array()) {
        
        if(count($values) <= 25) {
            
            $this->oldValues = $values;
            $this->temperature = $values[0]['temp'];
            $this->humidity = $values[0]['hum'];
            $this->time = $values[0]['time'];
        }
    }
    
    /**
     * setzt den aktuellen Sensorwert und schiebt ih in das Werte Array
     * 
     * @param Float   $temperature Temperatur
     * @param Integer $humidity    Luftfeuchte
     */
    public function pushValues($temperature, $humidity) {
        
        $date = DateTime::now();
        
        //alte Werte Schieben
        array_unshift($this->oldValues, array('temp' => $temperature, 'hum' => $humidity, 'time' => $date));
        //mehr als 5 Werte im Array?
        if(isset($this->oldValues[25])) {
            
            //aeltesten Wert loeschen
            unset($this->oldValues[25]);
        }
        
        //Werte setzten
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->time = $date;
        $this->isModified = true;
    }
}
