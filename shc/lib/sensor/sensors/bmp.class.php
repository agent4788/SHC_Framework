<?php

namespace SHC\Sensor\Sensors;

//Imports
use SHC\Sensor\AbstractSensor;
use RWF\Date\DateTime;
use SHC\Sensor\Model\AbstractAirPressure;
use SHC\Sensor\Model\AbstractAltitude;
use SHC\Sensor\Model\AbstractTemperature;
use SHC\Sensor\Model\AirPressure;
use SHC\Sensor\Model\Altitude;
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
class BMP extends AbstractSensor implements Temperature, AirPressure, Altitude {

    use AbstractTemperature, AbstractAirPressure, AbstractAltitude;
    
    /**
     * @param Array   $values   Sensorwerte
     */
    public function __construct(array $values = array()) {
        
        if(count($values) <= 25) {
            
            $this->oldValues = $values;
            $this->temperature = $values[0]['temp'];
            $this->airPressure = $values[0]['press'];
            $this->altitude = $values[0]['alti'];
            $this->time = $values[0]['time'];
        }
    }
    
    /**
     * setzt den aktuellen Sensorwert und schiebt ih in das Werte Array
     * 
     * @param float $temperature Temperatur
     * @param float $pressure    Luftdruck
     * @param float $altitude    Hoehe
     */
    public function pushValues($temperature, $pressure, $altitude) {
        
        $date = DateTime::now();
        
        //alte Werte Schieben
        array_unshift($this->oldValues, array('temp' => $temperature, 'press' => $pressure, 'alti' => $altitude, 'time' => $date));
        //mehr als 5 Werte im Array?
        if(isset($this->oldValues[25])) {
            
            //aeltesten Wert loeschen
            unset($this->oldValues[25]);
        }
        
        //Werte setzten
        $this->temperature = $temperature;
        $this->airPressure = $pressure;
        $this->altitude = $altitude;
        $this->time = $date;
        $this->isModified = true;
    }
}
