<?php

namespace SHC\UserAtHome;

//Imports


/**
 * Benutzer zu Hause
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserAtHome {
    
    /**
     * Benutzer ist zu Hause
     * 
     * @var Integer
     */
    const STATE_ONLINE = 1;
    
    /**
     * Benutzer ist nicht zu Hause
     * 
     * @var Integer
     */
    const STATE_OFFLINE = 0;
    
    /**
     * Das Schaltelement soll Angezeigt werden
     * 
     * @var Integer
     */
    const SHOW = 1;
    
    /**
     * Das Schaltelement sol Versteckt werden
     * 
     * @var Integer
     */
    const HIDE = 0;
    
    /**
     * ID des Geraetes
     * 
     * @var Integer 
     */
    protected $id = 0;

    /**
     * Name/Bezeichnung des GPIO
     * 
     * @var String
     */
    protected $name = '';
    
    /**
     * IP Adresse des PCs
     * 
     * @var String 
     */
    protected $ipAddress = 0;
    
    /**
     * Sortierungs ID
     * 
     * @var Integer 
     */
    protected $orderId = 0;
    
    /**
     * aktiviert/deaktiviert
     * 
     * @var Boolean 
     */
    protected $enabled = true;
    
    /**
     * sichtbarkeit des Benutzers
     * 
     * @var Integer 
     */
    protected $visibility = 1;

    /**
     * Status
     * 
     * @var Integer
     */
    protected $state = 0;
    
    /**
     * gibt an ob sich der Status verandert hat
     * 
     * @var Boolean 
     */
    protected $stateModified = false;
    
    /**
     * @param Integer $id             ID
     * @param String  $name           Name
     * @param Integer $ipAddress      IP Adresse des Geraetes
     * @param Integer $orderId        Sortierungs ID
     * @param Boolean $enabled        Aktiv
     * @param Integer $visibility     Sichtbarkeit
     * @param Integer $state          Status
     */
    public function __construct($id, $name, $ipAddress, $orderId, $enabled, $visibility, $state) {

        $this->id = $id;
        $this->name = $name;
        $this->ipAddress = $ipAddress;
        $this->orderId = $orderId;
        $this->enable($enabled);
        $this->visibility = $visibility;
        $this->state = $state;
    }

    /**
     * setzt die ID 
     * 
     * @param  Integer $id ID
     * @return \SHC\UserAtHome\UserAtHome
     */
    public function setId($id) {
        
        $this->id = $id;
        return $this;
    }
    
    /**
     * gibt die ID des Benutzers zurueck
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
     * @return \SHC\UserAtHome\UserAtHome
     */
    public function setName($name) {
        
        $this->name = $name;
        return $this;
    }
    
    /**
     * gibt den Namen des Benutzers zurueck
     * 
     * @return String
     */
    public function getName() {

        return $this->name;
    }
    
    /**
     * setzt die IP Adresse
     * 
     * @param  String $ipAddress IP Adresse
     * @return \SHC\UserAtHome\UserAtHome
     */
    public function setIpAddress($ipAddress) {
        
        $this->ipAddress = $ipAddress;
        return $this;
    }
    
    /**
     * gibt die IP Adresse des Benutzers zurueck
     * 
     * @return String
     */
    public function getIpAddress() {

        return $this->ipAddress;
    }
    
    /**
     * setzt die Sortierungs ID
     * 
     * @param  Integer $id Sortierungs ID
     * @return \SHC\UserAtHome\UserAtHome
     */
    public function setOrderId($id) {
        
        $this->orderId = $id;
        return $this;
    }
    
    /**
     * gibt die Sortierungs ID zurueck
     * 
     * @return Integer
     */
    public function getOrderId() {
        
        return $this->orderId;
    }

    /**
     * Aktiviert/Deaktiviert den Benutzer
     * 
     * @param  Boolean $enabled Aktiviert
     * @return \SHC\UserAtHome\UserAtHome
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
     * gibt an ob der Benutzer Aktiviert ist
     * 
     * @return Boolean 
     */
    public function isEnabled() {

        return $this->enabled;
    }
    
    /**
     * setzt die Sichtbarkeit des benutzers
     * 
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\UserAtHome\UserAtHome
     */
    public function setVisibility($visibility) {
        
        $this->visibility = $visibility;
        return $this;
    }
    
    /**
     * gibt die Sichtbarkeit des Benutzers zurueck
     * 
     * @return Integer
     */
    public function isVisible() {
        
        return $this->visibility;
    }
    
    /**
     * setzt den Status
     * 
     * @param  Integer $state    Status
     * @param  Boolean $modified als geaendert Markieren
     * @return \SHC\UserAtHome\UserAtHome
     */
    public function setState($state, $modified = true) {
        
        $this->state = $state;
        if($modified == true) {
            
            $this->stateModified = true;
        }
        return $this;
    }
    
    /**
     * gibt den Status des Benutzers zurueck
     * 
     * @return Integer
     */
    public function getState() {

        return $this->state;
    }
    
    /**
     * gibt an ob der Status veraendert wurde
     * 
     * @return Boolean
     */
    public function isStateModified() {
        
        return $this->stateModified;
    }
}
