<?php

namespace SHC\Condition;

//Imports

/**
 * Schnitstelle einer Schaltbedingung
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Condition {

    /**
     * setzt die ID
     * 
     * @param   Integer $id ID
     * @return \SHC\Condition\Condition
     */
    public function setId($id);
    
    /**
     * gibt die ID zurueck
     * 
     * @return Integer
     */
    public function getId();
    
    /**
     * setzt den Namen
     * 
     * @param  String $name Name
     * @return \SHC\Condition\Condition
     */
    public function setName($name);
    
    /**
     * gibt den Namen zurueck
     * 
     * @return String
     */
    public function getName();
    
    /**
     * setzt die Daten fuer die Bedingung
     * 
     * @param  Array $data Daten
     * @return \SHC\Condition\Condition
     */
    public function setData(array $data);
    
    /**
     * gibt die Daten der Bedingung zurueck
     * 
     * @return Array 
     */
    public function getData();
    
    /**
     * Aktiviert/Deaktiviert ddie Bedingung
     * 
     * @param  Boolean $enabled Aktiviert
     * @return \SHC\Condition\Condition
     */
    public function enable($enabled);

    /**
     * gibt an ob die Bedingung Aktiviert ist
     * 
     * @return Boolean 
     */
    public function isEnabled();

    /**
     * gibt an ob die Bedingung erfuellt ist
     * 
     * @return Boolean
     */
    public function isSatisfies();

    /**
     * gibt den Typnamen zurueck
     *
     * @return string
     */
    public function getTypeName();
}
