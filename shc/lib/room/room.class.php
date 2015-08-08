<?php

namespace SHC\Room;

//Imports
use RWF\User\User;
use RWF\User\UserGroup;
use RWF\User\Visitor;

/**
 * Raum
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Room {
    
    /**
     * ID des Raumes
     * 
     * @var Integer 
     */
    protected $id = 0;
    
    /**
     * Name des Raumes
     * 
     * @var String 
     */
    protected $name = '';
    
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
     * Berechtigte Benutzergruppen
     * 
     * @var Array 
     */
    protected $allowedUserGroups = array();
    
    /**
     * @param Integer $id                ID
     * @param String  $name              Name des Raums
     * @param Integer $orderId           Sortierungs ID
     * @param Boolean $enabled           Aktiviert
     */
    public function __construct($id, $name, $orderId = 0, $enabled = true) {
        
        $this->id = $id;
        $this->name = $name;
        $this->orderId = $orderId;
        $this->enable($enabled);
    }
    
    /**
     * setzt die ID des Raumes
     * 
     * @param  Integer $id
     * @return \SHC\Room\Room
     */
    public function setId($id) {
        
        $this->id = $id;
        return $this;
    }
    
    /**
     * gibt die ID des Raumes zurueck
     * 
     * @return Integer
     */
    public function getId() {
        
        return $this->id;
    }
    
    /**
     * setzt den Namen des Raumes
     * 
     * @param  String $name Name
     * @return \SHC\Room\Room
     */
    public function setName($name) {

        $this->name = $name;
        return $this;
    }

    /**
     * gibt den Namen des Raumes zurueck
     * 
     * @return String
     */
    public function getName() {

        return $this->name;
    }

    /**
     * setzt die SOrtierungs ID des Raumes
     *
     * @param $orderId
     * @return Room
     * @internal param int $id
     */
    public function setOrderId($orderId) {
        
        $this->orderId = $orderId;
        return $this;
    }
    
    /**
     * gibt die Sortierungs ID des Raumes zurueck
     * 
     * @return Integer
     */
    public function getOrderId() {
        
        return $this->orderId;
    }
    
    /**
     * Aktiviert/Deaktiviert den Raum
     * 
     * @param  Boolean $enabled Aktiviert
     * @return \SHC\Room\Room
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
     * gibt an ob der Raum Aktiviert ist
     * 
     * @return Boolean 
     */
    public function isEnabled() {

        return $this->enabled;
    }

    /**
     * fuegt eine Benutzergruppen hinzu der es erlaubt ist den Raum und desse Elemente zu nutzen
     * 
     * @param  \RWF\User\UserGroup $userGroup
     * @return \SHC\Room\Room
     */
    public function addAllowedUserGroup(UserGroup $userGroup) {

        $this->allowedUserGroups[] = $userGroup;
    }

    /**
     * entfernt eine Benutzergruppen der es erlaubt ist den Raum und desse Elemente zu nutzen
     * 
     * @param  \RWF\User\UserGroup $userGroup
     * @return \SHC\Room\Room
     */
    public function removeAllowedUserGroup(UserGroup $userGroup) {

        $this->allowedUserGroups = array_diff($this->allowedUserGroups, array($userGroup));
        return $this;
    }

    /**
     * entfernt alle Benutzergruppen
     * 
     * @return \SHC\Room\Room
     */
    public function removeAllAllowedUserGroups() {

        $this->allowedUserGroups = array();
        return $this;
    }

    /**
     * gibt eine Liste mit allen erlaubten Benutzergruppen zurueck
     *
     * @return Array
     */
    public function listAllowedUserGroups() {

        return $this->allowedUserGroups;
    }

    /**
     * prueft ob ein Benutzer berechtigt ist das Element zu schalten
     * 
     * @param \RWF\User\Visitor $user
     * @return Boolean
     */
    public function isUserEntitled(Visitor $user) {

        if (isset($this->allowedUserGroups[0]) && $this->allowedUserGroups[0] != '') {

            //Hauptgruppe pruefen
            if (in_array($user->getMainGroup(), $this->allowedUserGroups) || ($user instanceof User && $user->isOriginator())) {

                return true;
            }

            //Alle Benutzergruppen pruefen
            foreach ($user->listGroups() as $userGroup) {

                if ($userGroup instanceof UserGroup && in_array($userGroup, $this->allowedUserGroups)) {

                    return true;
                }
            }
            
            //keine berechtigte Gruppe gefunden
            return false;
        }
        return true;
    }

    /**
     * exportiert das Objekt als Array
     *
     * @return Array
     */
    public function toArray() {

        $allowedGroups = array();
        if($this->allowedUserGroups !== null) {

            foreach($this->allowedUserGroups as $group) {

                /* @var $group \RWF\User\UserGroup */
                $allowedGroups[] = $group->getId();
            }
        } else {

            $allowedGroups = null;
        }

        return array(
            'id' => $this->id,
            'name' => $this->name,
            'orderId' => $this->orderId,
            'enabled' => $this->enabled,
            'allowedUserGroups' => $allowedGroups
        );
    }
}
