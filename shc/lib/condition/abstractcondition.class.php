<?php

namespace SHC\Condition;

//Imports
use SHC\Condition\Conditions\HumidityGreaterThanCondition;
use SHC\Condition\Conditions\HumidityLowerThanCondition;
use SHC\Condition\Conditions\LightIntensityGreaterThanCondition;
use SHC\Condition\Conditions\LightIntensityLowerThanCondition;
use SHC\Condition\Conditions\MoistureGreaterThanCondition;
use SHC\Condition\Conditions\MoistureLowerThanCondition;
use SHC\Condition\Conditions\TemperatureGreaterThanCondition;
use SHC\Condition\Conditions\TemperatureLowerThanCondition;
use SHC\Condition\Conditions\NobodyAtHomeCondition;
use SHC\Condition\Conditions\UserAtHomeCondition;
use SHC\Condition\Conditions\UserNotAtHomeCondition;
use SHC\Condition\Conditions\DateCondition;
use SHC\Condition\Conditions\DayOfWeekCondition;
use SHC\Condition\Conditions\TimeOfDayCondition;
use SHC\Condition\Conditions\SunriseSunsetCondition;
use SHC\Condition\Conditions\SunsetSunriseCondition;
use SHC\Condition\Conditions\FileExistsCondition;
use SHC\Condition\Conditions\HolidaysCondition;
use SHC\Condition\Conditions\InputHighCondition;
use SHC\Condition\Conditions\InputLowCondition;
use SHC\Condition\Conditions\FirstLoopCondition;
use SHC\Core\SHC;


/**
 * Standard Bedingung
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class AbstractCondition implements Condition {
    
    /**
     * ID
     * 
     * @var Integer 
     */
    protected $id = 0;
    
    /**
     * Name
     * 
     * @var String 
     */
    protected $name = '';
    
    /**
     * Daten
     * 
     * @var Array 
     */
    protected $data = array();
    
    /**
     * aktiviert/deaktiviert
     * 
     * @var Boolean 
     */
    protected $enabled = true;
    
    /**
     * @param Integer $id      ID
     * @param String  $name    Name
     * @param Array   $data    Daten
     * @param Boolean $enabled Aktiv
     */
    public function __construct($id, $name, array $data = array(), $enabled = true) {
        
        $this->id = $id;
        $this->name = $name;
        $this->data = $data;
        $this->enable($enabled);
    }
    
    /**
     * setzt die ID
     * 
     * @param   Integer $id ID
     * @return \SHC\Condition\Condition
     */
    public function setId($id) {
        
        $this->id = $id;
        return $this;
    }
    
    /**
     * gibt die ID zurueck
     * 
     * @return Integer
     */
    public function getId() {
        
        return $this->id;
    }
    
    /**
     * setzt den Namen
     * 
     * @param  String $name Name
     * @return \SHC\Condition\Condition
     */
    public function setName($name) {
        
        $this->name = $name;
        return $this;
    }
    
    /**
     * gibt den Namen zurueck
     * 
     * @return String
     */
    public function getName() {
        
        return $this->name;
    }
    
    /**
     * setzt die Daten fuer die Bedingung
     * 
     * @param  Array $data Daten
     * @return \SHC\Condition\Condition
     */
    public function setData(array $data) {
        
        $this->data = $data;
        return $this;
    }
    
    /**
     * gibt die Daten der Bedingung zurueck
     * 
     * @return Array 
     */
    public function getData() {
        
        return $this->data;
    }
    
    /**
     * Aktiviert/Deaktiviert die Bedingung
     * 
     * @param  Boolean $enabled Aktiviert
     * @return \SHC\Condition\Condition
     */
    public function enable($enabled) {

        if ($enabled == true) {

            $this->enabled = true;
        } else {

            $this->enabled = false;
        }
        return $this;
    }

    /**
     * gibt an ob die Bedingung Aktiviert ist
     * 
     * @return Boolean 
     */
    public function isEnabled() {

        return $this->enabled;
    }

    /**
     * gibt den Typnamen zurueck
     *
     * @return string
     */
    public function getTypeName() {

        if($this instanceof HumidityGreaterThanCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.HumidityGreaterThanCondition');
        } elseif($this instanceof HumidityLowerThanCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.HumidityLowerThanCondition');
        } elseif($this instanceof LightIntensityGreaterThanCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.LightIntensityGreaterThanCondition');
        } elseif($this instanceof LightIntensityLowerThanCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.LightIntensityLowerThanCondition');
        } elseif($this instanceof MoistureGreaterThanCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.MoistureGreaterThanCondition');
        } elseif($this instanceof MoistureLowerThanCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.MoistureLowerThanCondition');
        } elseif($this instanceof TemperatureGreaterThanCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.TemperatureGreaterThanCondition');
        } elseif($this instanceof TemperatureLowerThanCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.TemperatureLowerThanCondition');
        } elseif($this instanceof NobodyAtHomeCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.NobodyAtHomeCondition');
        } elseif($this instanceof UserAtHomeCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.UserAtHomeCondition');
        } elseif($this instanceof UserNotAtHomeCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.UserNotAtHomeCondition');
        } elseif($this instanceof DateCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.DateCondition');
        } elseif($this instanceof DayOfWeekCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.DayOfWeekCondition');
        } elseif($this instanceof TimeOfDayCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.TimeOfDayCondition');
        } elseif($this instanceof SunriseSunsetCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.SunriseSunsetCondition');
        } elseif($this instanceof SunsetSunriseCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.SunsetSunriseCondition');
        } elseif($this instanceof FileExistsCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.FileExistsCondition');
        } elseif($this instanceof HolidaysCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.HolidaysCondition');
        } elseif($this instanceof InputHighCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.InputHighCondition');
        } elseif($this instanceof InputLowCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.InputLowCondition');
        } elseif($this instanceof FirstLoopCondition) {

            $type = SHC::getLanguage()->get('acp.conditionManagement.condition.FirstLoopCondition');
        } else {

            $type = 'unknown';
        }
        return $type;
    }
}
