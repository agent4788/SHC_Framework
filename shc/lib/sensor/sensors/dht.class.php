<?php

namespace SHC\Sensor\Sensors;

//Imports
use SHC\Sensor\AbstractSensor;
use RWF\Date\DateTime;

/**
 * DHT11/22 Sensor
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DHT extends AbstractSensor{
    
    /**
     * Temperatur
     *  
     * @var Float 
     */
    protected $temperature = 0.0;
    
    /**
     * Luftfeuchtigkeit
     * 
     * @var Integer 
     */
    protected $humidity = 0;
    
    /**
     * Temperatur Anzeigen
     * 
     * @var Integer
     */
    protected $temperatureVisibility = 1;
    
    /**
     * Luftfeuchte Anzeigen
     * 
     * @var Integer
     */
    protected $humidityVisibility = 1;
    
    /**
     * @param Array   $values   Sensorwerte
     */
    public function __construct(array $values = array()) {
        
        if(count($values) == 5) {
            
            $this->oldValues = $values;
            $this->temperature = $values[0]['temp'];
            $this->humidity = $values[0]['hum'];
            $this->time = $values[0]['time'];
        }
    }
    
    /**
     * gibt den Aktuellen Temperaturwert zurueck
     * 
     * @return Float
     */
    public function getTemperature() {
        
        return $this->temperature;
    }
    
    /**
     * gibt die Luftfeuchte zurueck
     * 
     * @return Integer
     */
    public function getHumidity() {
        
        return $this->humidity;
    }
    
    /**
     * setzt die Sichtbarkeit der Temperatur
     * 
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Sensors\DHT
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
     * setzt die Sichtbarkeit der Luftfeuchte
     * 
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Sensors\DHT
     */
    public function humidityVisibility($visibility) {
        
        $this->humidityVisibility = $visibility;
        return $this;
    }
    
    /**
     * gibt die Sichtbarkeit der Luftfeuchte an
     * 
     * @return Boolean
     */
    public function isHumidityVisivle() {
        
        return ($this->humidityVisibility == 1 ? true : false);
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
        if(isset($this->oldValues[5])) {
            
            //aeltesten Wert loeschen
            unset($this->oldValues[5]);
        }
        
        //Werte setzten
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->time = $date;
        $this->isModified = true;
    }
}
