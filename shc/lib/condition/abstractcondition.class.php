<?php

namespace SHC\Condition;

//Imports


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
     * @param type $id
     * @param type $name
     * @param array $data
     * @param type $enabled
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
    
}
