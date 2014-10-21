<?php

namespace SHC\Switchable;

//Imports
use RWF\User\Visitor;
use SHC\Room\Room;
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
interface Element {
    
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
     * setzt den Status des Objekts
     * 
     * @param  Integer $state    Status
     * @param  Boolean $modified als veaendert Markieren
     * @return \SHC\Switchable\Element
     */
    public function setState($state, $modified = true);
    
    /**
     * gibt an ob der Status veraendert wurde
     * 
     * @return Boolean
     */
    public function isStateModified();
    
    /**
     * setzt die ID des Elements
     * 
     * @param  Integer $id
     * @return \SHC\Switchable\Element
     */
    public function setId($id);
    
    /**
     * gibt die ID des Elements zurueck
     * 
     * @return Integer
     */
    public function getId();
    
    /**
     * setzt das Icon welches Angezeigt werden soll
     * 
     * @param  String $path Dateiname
     * @return \SHC\Switchable\Element
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
     * @return \SHC\Switchable\Element
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
     * @return \SHC\Switchable\Element
     */
    public function setRoom(Room $room);
    
    /**
     * gibt den Raum zurueck in dem das Element zugeordnet ist
     * 
     * @return \SHC\Room\Room
     */
    public function getRoom();
    
    /**
     * setzt die Sortierungs ID
     * 
     * @param  Integer $orderId Sortierungs ID
     * @return \SHC\Switchable\Element
     */
    public function setOrderId($orderId);
    
    /**
     * gibt die Sortierungs ID zurueck
     * 
     * @return Integer
     */
    public function getOrderId();
    
    /**
     * Aktiviert/Deaktiviert das Element
     * 
     * @param  Boolean $enabled Aktiviert
     * @return \SHC\Switchable\Element
     */
    public function enable($enabled);
    
    /**
     * gibt an ob das Element Aktiviert ist
     * 
     * @return Boolean 
     */
    public function isEnabled();
    
    /**
     * setzt das Schaltelement Sichtbar/Versteckt
     * 
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Switchable\Element
     */
    public function setVisibility($visibility);
    
    /**
     * gibt die Sichtbarkeit des Schaltelementes zurueck
     * 
     * @return Integer
     */
    public function isVisible();
    
    /**
     * fuegt eine Benutzergruppen hinzu der es erlaubt ist das Element zu schalten
     * 
     * @param  \RWF\User\UserGroup $userGroup
     * @return \SHC\Switchable\Element
     */
    public function addAllowedUserGroup(UserGroup $userGroup);
    
    /**
     * entfernt eine Benutzergruppen der es erlaubt ist das Element zu schalten
     * 
     * @param  \RWF\User\UserGroup $userGroup
     * @return \SHC\Switchable\Element
     */
    public function removeAllowedUserGroup(UserGroup $userGroup);
    
    /**
     * entfernt alle Benutzergruppen
     * 
     * @return \SHC\Switchable\Element
     */
    public function removeAllAllowedUserGroups();
    
    /**
     * prueft ob ein Benutzer berechtigt ist das Element zu schalten
     * 
     * @param \RWF\User\User $user
     * @return Boolean
     */
    public function isUserEntitled(Visitor $user);
}
