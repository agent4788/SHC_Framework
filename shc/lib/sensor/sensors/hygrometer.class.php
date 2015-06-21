<?php 

namespace SHC\Sensor\Sensors;

//Imports
use SHC\Sensor\AbstractSensor;
use RWF\Date\DateTime;
use SHC\Sensor\Model\AbstractMoisture;
use SHC\Sensor\Model\Moisture;

/**
 * Feuchtigkeits Sensor
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Hygrometer extends AbstractSensor implements Moisture {

    use AbstractMoisture;

    /**
     * @param Array  $values   Sensorwerte
     */
    public function __construct(array $values = array()) {
        
        if(count($values) <= 25) {
            
            $this->oldValues = $values;
            $this->moisture = $values[0]['value'];
            $this->time = $values[0]['time'];
        }
    }
    
    /**
     * setzt den aktuellen Sensorwert und schiebt ih in das Werte Array
     * 
     * @param Integer $value Wert
     */
    public function pushValues($value) {
        
        $date = DateTime::now();
        
        //alte Werte Schieben
        array_unshift($this->oldValues, array('value' => $value, 'time' => $date));
        //mehr als 5 Werte im Array?
        if(isset($this->oldValues[25])) {
            
            //aeltesten Wert loeschen
            unset($this->oldValues[25]);
        }
        
        //Werte setzten
        $this->moisture = $value;
        $this->time = $date;
        $this->isModified = true;
    }
}