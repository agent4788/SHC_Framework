<?php

namespace SHC\Switchable;

//Imports
use SHC\Condition\Condition;
use SHC\Timer\SwitchPoint;
use SHC\Room\Room;
use RWF\User\User;
use RWF\User\UserGroup;

/**
 * Schnittstelle eines Schaltbaren Elements
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Switchable {
    
    /**
     * Status eingeschalten
     * 
     * @var Integer
     */
    const STATE_OFF = 0;
    
    /**
     * Status Ausgeschalten
     * 
     * @var Integer
     */
    const STATE_ON = 1;
    
    /**
     * fuegt einen Schaltpunkt hinzu
     * 
     * @param  \SHC\Timer\SwitchPoint $switchPoint
     * @return \SHC\Switchable\Switchable
     */
    public function addSwitchPoint(SwitchPoint $switchPoint);
    
    /**
     * loescht einen Schaltpunkt
     * 
     * @param  \SHC\Timer\SwitchPoint $switchPoint
     * @return \SHC\Switchable\Switchable
     */
    public function removeSwitchPoint(SwitchPoint $switchPoint);
    
    /**
     * loescht alle Schaltpunkte
     * 
     * @return \SHC\Switchable\Switchable
     */
    public function removeAllSwitchPoints();
    
    /**
     * schaltet das Objekt ein
     * 
     * @return Boolean
     */
    public function switchOn();
    
    /**
     * schaltet das Objekt aus
     * 
     * @return Boolean
     */
    public function switchOff();
    
    /**
     * schaltet das Objekt um (in den jeweils gegenteiligen zustand)
     * 
     * @return Boolean
     */
    public function toggle();
    
    /**
     * gibt den aktuellen geschaltenen Zustand zurueck
     * 
     * @return Integer
     */
    public function getState();
    
    /**
     * fuehrt alle anstehenden Schaltbefehle aus und gibt true zurueck wenn eine Aktion ausgefuehrt wurde
     * 
     * @return Boolean
     */
    public function execute();
    
    /**
     * setzt das Icon welches Angezeigt werden soll
     * 
     * @param  String $path Dateiname
     * @return \SHC\Switchable\Switchable
     */
    public function setIcon($path);
    
    /**
     * gibt den Dateinamen des Icons zurueck
     * 
     * @return String
     */
    public function getIcon();
    
    /**
     * setzt den Namen des Elements
     * 
     * @param  String $name Name
     * @return \SHC\Switchable\Switchable
     */
    public function setName($name);
    
    /**
     * gibt den Namen des Elements zurueck
     * 
     * @return String
     */
    public function getName();
    
    /**
     * setzt den Raum dem das Element zugeordnet ist
     * 
     * @param  \SHC\Room\Room $room
     * @return \SHC\Switchable\Switchable
     */
    public function setRoom(Room $room);
    
    /**
     * gibt den Raum zurueck in dem das Element zugeordnet ist
     * 
     * @return \SHC\Room\Room
     */
    public function getRoom();
    
    /**
     * Aktiviert/Deaktiviert das Element
     * 
     * @param  Boolean $enabled Aktiviert
     * @return \SHC\Switchable\Switchable
     */
    public function enable($enabled);
    
    /**
     * gibt an ob das Element Aktiviert ist
     * 
     * @return Boolean 
     */
    public function isEnabled();
    
    /**
     * fuegt eine Benutzergruppen hinzu der es erlaubt ist das Element zu schalten
     * 
     * @param  \RWF\User\UserGroup $userGroup
     * @return \SHC\Switchable\Switchable
     */
    public function addAllowedUserGroup(UserGroup $userGroup);
    
    /**
     * entfernt eine Benutzergruppen der es erlaubt ist das Element zu schalten
     * 
     * @param  \RWF\User\UserGroup $userGroup
     * @return \SHC\Switchable\Switchable
     */
    public function removeAllowedUserGroup(UserGroup $userGroup);
    
    /**
     * entfernt alle Benutzergruppen
     * 
     * @return \SHC\Switchable\Switchable
     */
    public function removeAllAllowedUserGroups();
    
    /**
     * prueft ob ein Benutzer berechtigt ist das Element zu schalten
     * 
     * @param \RWF\User\User $user
     * @return Boolean
     */
    public function isUserEntitled(User $user);
}
