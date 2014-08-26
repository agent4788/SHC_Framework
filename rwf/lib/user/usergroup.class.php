<?php

namespace RWF\User;

/**
 * Benutzergruppe
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserGroup {

    /**
     * Gruppen ID
     * 
     * @var Integer
     */
    protected $id = 0;

    /**
     * Gruppen Name
     * 
     * @var String 
     */
    protected $name = '';

    /**
     * Gruppenbeschreibung
     * 
     * @var String
     */
    protected $description = '';

    /**
     * Systemgruppe?
     * 
     * @var Boolean
     */
    protected $isSystemGroup = '';

    /**
     * Gruppenrechte
     * 
     * @var Array 
     */
    protected $premissions = array();

    /**
     * @param Integer $id            Gruppen ID
     * @param String  $name          Gruppen Name
     * @param String  $description   Beschreibung
     * @param Array   $premissions   Berechtigungen
     * @param Boolean $isSystemGroup ist Systemgruppe?
     */
    public function __construct($id, $name, $description, array $premissions, $isSystemGroup = false) {

        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->isSystemGroup = $isSystemGroup;
        $this->premissions = $premissions;
    }

    /**
     * gibt die Gruppen ID zurueck
     * 
     * @return Integer
     */
    public function getId() {

        return $this->id;
    }

    /**
     * gibt den Gruppen Namen zurueck
     * 
     * @return String
     */
    public function getName() {

        return $this->name;
    }

    /**
     * gibt die Gruppen Beschreibung zurueck
     * 
     * @return String
     */
    public function getDescription() {

        return $this->description;
    }

    /**
     * gibt an ob ide Gruppe eine Systemgruppe ist (kann nicht geloescht werden)
     * 
     * @return Boolean
     */
    public function isSystemGroup() {

        return $this->isSystemGroup;
    }

    /**
     * prueft die Berechtigung der Gruppe
     * 
     * @param  String  $premission Recht
     * @return Boolean
     */
    public function checkPremission($premission) {

        if (isset($this->premissions[$premission]) && $this->premissions[$premission] == true) {

            return true;
        }
        return false;
    }
    
    /**
     * wandelt das Objekt in einen String um
     */
    public function __toString() {
        
        return $this->getName();
    }

}
