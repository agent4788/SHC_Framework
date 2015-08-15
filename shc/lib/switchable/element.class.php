<?php

namespace SHC\Switchable;

//Imports
use RWF\User\Visitor;
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
     * Button Text An/Aus
     *
     * @var Integer
     */
    const BUTTONS_ON_OFF = 1;

    /**
     * Button Text Auf/Ab
     *
     * @var Integer
     */
    const BUTTONS_UP_DOWN = 2;

    /**
     * Button Text Auf/Zu
     *
     * @var Integer
     */
    const BUTTONS_OPEN_CLOSED = 4;

    /**
     * Button Text Start/Stop
     *
     * @var Integer
     */
    const BUTTONS_START_STOP = 5;
    
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
     * fuegt einen Raum hinzu
     *
     * @param  Integer $roomId Raum ID
     * @return \SHC\Switchable\Element
     */
    public function addRoom($roomId);

    /**
     * setzt eine Liste mit Raeumen
     *
     * @param  Array $roomId Raum IDs
     * @return \SHC\Switchable\Element
     */
    public function setRooms(array $rooms);

    /**
     * entfernt einen Raum
     *
     * @param  Integer $roomId Raum ID
     * @return \SHC\Switchable\Element
     */
    public function removeRoom($roomId);

    /**
     * prueft on das Element dem Raum mit der uebergebenen ID zugeordnet ist
     *
     * @param  Integer $roomId Raum ID
     * @return Boolean
     */
    public function isInRoom($roomId);

    /**
     * gibt eine Liste mit allen Raeumen zurueck
     *
     * @return Array
     */
    public function getRooms();

    /**
     * gibt eine Liste mit den Raumnamen zurueck
     *
     * @return Array
     */
    public function getNamedRoomList($commaSepareted = false);

    /**
     * setzt die Sortierung
     *
     * @param  Array $order Sortierung
     * @return \SHC\Switchable\Element
     */
    public function setOrder(array $order);

    /**
     * setzt die Sortierungs ID
     *
     * @param  Integer $roomId  Raum ID
     * @param  Integer $orderId Sortierungs ID
     * @return \SHC\Switchable\Element
     */
    public function setOrderId($roomId, $orderId);

    /**
     * gibt die Sortierungs ID zurueck
     *
     * @param  Integer $roomId  Raum ID
     * @return Integer
     */
    public function getOrderId($roomId);
    
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

    /**
     * gibt den Typnamen zurueck
     *
     * @return string
     */
    public function getTypeName();
}
