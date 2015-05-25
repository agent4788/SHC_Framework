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
class BMP extends AbstractSensor {
    
    /**
     * Temperatur
     *  
     * @var Float 
     */
    protected $temperature = 0.0;
    
    /**
     * Luftdruck
     * 
     * @var Float 
     */
    protected $pressure = 0.0;
    
    /**
     * Standorthoehe
     *  
     * @var Float 
     */
    protected $altitude = 0.0;
    
    /**
     * Temperatur Anzeigen
     * 
     * @var Integer
     */
    protected $temperatureVisibility = 1;
    
    /**
     * Luftdruck Anzeigen
     * 
     * @var Integer 
     */
    protected $pressureVisibility = 1;
    
    /**
     * Hoehe Anzeigen
     * 
     * @var Integer 
     */
    protected $altitudeVisibility = 1;

    /**
     * Temperatur Offset
     *
     * @var Float
     */
    protected $temperatureOffset = 0.0;

    /**
     * Luftdruck Offset
     *
     * @var Float
     */
    protected $pressureOffset = 0.0;

    /**
     * Hoehen Offset
     *
     * @var Float
     */
    protected $altitudeOffset = 0.0;
    
    /**
     * @param Array   $values   Sensorwerte
     */
    public function __construct(array $values = array()) {
        
        if(count($values) <= 25) {
            
            $this->oldValues = $values;
            $this->temperature = $values[0]['temp'];
            $this->pressure = $values[0]['press'];
            $this->altitude = $values[0]['alti'];
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
     * gibt den Luftdruck zurueck
     * 
     * @return Float
     */
    public function getPressure() {
        
        return $this->pressure + $this->pressureOffset;
    }

    /**
     * gibt die Standorthoehe zurueck
     *
     * @return Float
     */
    public function getAltitude() {

        return $this->altitude + $this->altitudeOffset;
    }

    /**
     * setzt das Temperatur Offset
     *
     * @param  Float $temperatureOffset
     * @return \SHC\Sensor\Sensors\BMP
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
     * setzt das Luftdruck Offset
     *
     * @param  Float $pressureOffset
     * @return \SHC\Sensor\Sensors\BMP
     */
    public function setPressureOffset($pressureOffset) {

        $this->pressureOffset = $pressureOffset;
        return $this;
    }

    /**
     * gibt das Luftdruck Offset zurueck
     *
     * @return Float
     */
    public function getPressureOffset() {

        return $this->pressureOffset;
    }

    /**
     * setzt das Hoehen Offset
     *
     * @param  Float $altitudeOffset
     * @return \SHC\Sensor\Sensors\BMP
     */
    public function setAltitudeOffset($altitudeOffset) {

        $this->altitudeOffset = $altitudeOffset;
        return $this;
    }

    /**
     * gibt das das Hoehen Offset zurueck
     *
     * @return Float
     */
    public function getAltitudeOffset() {

        return $this->altitudeOffset;
    }

    /**
     * setzt die Sichtbarkeit der Temperatur
     * 
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Sensors\BMP
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
     * setzt die Sichtbarkeit des Luftdckes
     * 
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Sensors\BMP
     */
    public function pressureVisibility($visibility) {
        
        $this->pressureVisibility = $visibility;
        return $this;
    }
    
    /**
     * gibt die Sichtbarkeit des Luftdruckes an
     * 
     * @return Boolean
     */
    public function isPressureVisible() {
        
        return ($this->pressureVisibility == 1 ? true : false);
    }
    
    /**
     * setzt die Sichtbarkeit der Hoehe
     * 
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Sensors\BMP
     */
    public function altitudeVisibility($visibility) {
        
        $this->altitudeVisibility = $visibility;
        return $this;
    }
    
    /**
     * gibt die Sichtbarkeit der Hoehe an
     * 
     * @return Boolean
     */
    public function isAltitudeVisible() {
        
        return ($this->altitudeVisibility == 1 ? true : false);
    }
    
    /**
     * setzt den aktuellen Sensorwert und schiebt ih in das Werte Array
     * 
     * @param Float $temperature Temperatur
     * @param Floet $pressure    Luftdruck
     * @param Floet $altitude    Hoehe
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
        $this->pressure = $pressure;
        $this->altitude = $altitude;
        $this->time = $date;
        $this->isModified = true;
    }
}
