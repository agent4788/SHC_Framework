<?php

namespace SHC\Switchable;

//Imports
use SHC\Core\SHC;
use RWF\Date\DateTime;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\AvmSocket;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\FritzBox;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\RadioSocketDimmer;
use SHC\Switchable\Switchables\Reboot;
use SHC\Switchable\Switchables\RemoteReboot;
use SHC\Switchable\Switchables\RemoteShutdown;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\Script;
use SHC\Switchable\Switchables\Shutdown;
use SHC\Switchable\Switchables\WakeOnLan;
use SHC\Switchable\Readables\RpiGpioInput;
use SHC\Timer\SwitchPointEditor;
use SHC\Timer\SwitchPoint;
use RWF\User\UserEditor;
use RWF\User\UserGroup;
use SHC\View\Room\ViewHelperEditor;

/**
 * Verwaltung der schaltbaren elemente
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchableEditor {

    /**
     * nach ID sortieren
     * 
     * @var String
     */
    const SORT_BY_ID = 'id';

    /**
     * nach Namen sortieren
     * 
     * @var String
     */
    const SORT_BY_NAME = 'name';

    /**
     * nach Sortierungs ID sortieren
     * 
     * @var String
     */
    const SORT_BY_ORDER_ID = 'orderId';

    /**
     * nicht sortieren
     * 
     * @var String
     */
    const SORT_NOTHING = 'unsorted';

    /**
     * Aktivitaet
     * 
     * @var Integer
     */
    const TYPE_ACTIVITY = 1;

    /**
     * Countdown
     * 
     * @var Integer
     */
    const TYPE_COUNTDOWN = 4;

    /**
     * Funksteckdose
     * 
     * @var Integer
     */
    const TYPE_RADIOSOCKET = 8;

    /**
     * Raspberry Pi GPIO Ausgang
     * 
     * @var Integer
     */
    const TYPE_RPI_GPIO_OUTPUT = 16;

    /**
     * WOL
     * 
     * @var Integer
     */
    const TYPE_WAKEONLAN = 32;
    
    /**
     * Raspberry Pi GPIO Eingang
     * 
     * @var Integer
     */
    const TYPE_RPI_GPIO_INPUT = 128;

    /**
     * Funkdimmer
     *
     * @var Integer
     */
    const TYPE_RADIOSOCKET_DIMMER = 256;

    /**
     * Neustart
     *
     * @var Integer
     */
    const TYPE_REBOOT = 512;

    /**
     * Herunterfahren
     *
     * @var Integer
     */
    const TYPE_SHUTDOWN = 1024;

    /**
     * externes Geraet Neustarten
     *
     * @var Integer
     */
    const TYPE_REMOTE_REBOOT = 2048;

    /**
     * externes Geraet Herunterfahren
     *
     * @var Integer
     */
    const TYPE_REMOTE_SHUTDOWN = 4096;

    /**
     * Script
     *
     * @var Integer
     */
    const TYPE_SCRIPT = 8192;

    /**
     * Script
     *
     * @var Integer
     */
    const TYPE_AVM_SOCKET = 16384;

    /**
     * Script
     *
     * @var Integer
     */
    const TYPE_FRITZBOX = 32768;

    /**
     * Liste mit allen Schaltbaren Objekten
     * 
     * @var Array 
     */
    protected $switchables = array();

    /**
     * fur Aktivitueten und Countdowns zwischenspeicher der Schtichable IDs
     * 
     * @var Array 
     */
    protected $switchableList = array();

    /**
     * Singleton Instanz
     * 
     * @var \SHC\Switchable\SwitchableEditor
     */
    protected static $instance = null;

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'shc:switchables';

    protected function __construct() {

        $this->loadData();
    }

    /**
     * laedt die Bedingungen aus den XML Daten und erzeugt die Objekte
     */
    public function loadData() {

        //alte daten loeschen
        $this->switchables = array();

        $switchables = SHC::getDatabase()->hGetAllArray(self::$tableName);
        foreach ($switchables as $switchable) {

            //Objekte initialisiernen und Spezifische Daten setzen
            switch ((int) $switchable['type']) {

                case self::TYPE_ACTIVITY:

                    $object = new Activity();
                    $object->setButtonText((int) $switchable['buttonText']);

                    //Switchable IDs zwischenspeichern (erst nach dem Laden alle Objekte setzen)
                    $list = array();
                    foreach ($switchable['switchable'] as $activitySwitchable) {

                        $list[] = $activitySwitchable;
                    }
                    $this->switchableList[(int) $switchable['id']] = $list;
                    break;
                case self::TYPE_COUNTDOWN:

                    $object = new Countdown();
                    $object->setInterval((string) $switchable['interval']);
                    $object->setSwitchOffTime(DateTime::createFromDatabaseDateTime((string) $switchable['switchOffTime']));

                    //Switchable IDs zwischenspeichern (erst nach dem Laden alle Objekte setzen)
                    $list = array();
                    foreach ($switchable['switchable'] as $activitySwitchable) {

                        $list[] = $activitySwitchable;
                    }
                    $this->switchableList[(int) $switchable['id']] = $list;
                    break;
                case self::TYPE_RADIOSOCKET:

                    $object = new RadioSocket();
                    $object->setProtocol((string) $switchable['protocol']);
                    $object->setSystemCode((string) $switchable['systemCode']);
                    $object->setDeviceCode((string) $switchable['deviceCode']);
                    $object->setContinuous((string) $switchable['continuous']);
                    $object->setButtonText((int) $switchable['buttonText']);
                    break;
                case self::TYPE_RPI_GPIO_OUTPUT:

                    $object = new RpiGpioOutput();
                    $object->setSwitchServer((int) $switchable['switchServer']);
                    $object->setPinNumber((int) $switchable['pinNumber']);
                    $object->setButtonText((int) $switchable['buttonText']);
                    break;
                case self::TYPE_WAKEONLAN:

                    $object = new WakeOnLan();
                    $object->setMac((string) $switchable['mac']);
                    $object->setIpAddress((string) $switchable['ipAddress']);
                    break;
                case self::TYPE_RPI_GPIO_INPUT:

                    $object = new RpiGpioInput();
                    $object->setSwitchServer((int) $switchable['switchServer']);
                    $object->setPinNumber((int) $switchable['pinNumber']);
                    break;
                case self::TYPE_RADIOSOCKET_DIMMER:

                    $object = new RadioSocketDimmer();
                    $object->setProtocol((string) $switchable['protocol']);
                    $object->setSystemCode((string) $switchable['systemCode']);
                    $object->setDeviceCode((string) $switchable['deviceCode']);
                    $object->setContinuous((string) $switchable['continuous']);
                    break;
                case self::TYPE_REBOOT:

                    $object = new Reboot();
                    break;
                case self::TYPE_SHUTDOWN:

                    $object = new Shutdown();
                    break;
                case self::TYPE_REMOTE_REBOOT:

                    $object = new RemoteReboot();
                    //nicht Implementiert
                    break;
                case self::TYPE_REMOTE_SHUTDOWN:

                    $object = new RemoteShutdown();
                    //nicht Implementiert
                    break;
                case self::TYPE_SCRIPT:

                    $object = new Script();
                    $object->setOnCommand((string) $switchable['onCommand']);
                    $object->setOffCommand((string) $switchable['offCommand']);
                    $object->setButtonText((int) $switchable['buttonText']);
                    break;
                case self::TYPE_AVM_SOCKET:

                    $object = new AvmSocket();
                    $object->setAin((string) $switchable['ain']);
                    $object->setButtonText((int) $switchable['buttonText']);
                    break;
                case self::TYPE_FRITZBOX:

                    $object = new FritzBox();
                    $object->setFunction((int) $switchable['function']);
                    break;
                default:

                    throw new \Exception('Unbekannter Typ', 1506);
            }

            //Allgemeine Daten setzen
            $object->setId((int) $switchable['id']);
            $object->setName((string) $switchable['name']);
            $object->enable(((string) $switchable['enabled'] == true ? true : false));
            $object->setVisibility(((string) $switchable['visibility'] == true ? true : false));
            $object->setIcon((string) $switchable['icon']);
            $object->setRooms($switchable['rooms']);
            $object->setOrder($switchable['order']);
            $object->setState((int) $switchable['state'], false);

            //Schaltpunkte
            foreach ($switchable['switchPoints'] as $switchPointId) {

                $switchPoint = SwitchPointEditor::getInstance()->getSwitchPointById($switchPointId);
                if ($switchPoint instanceof SwitchPoint) {

                    $object->addSwitchPoint($switchPoint);
                }
            }

            //Benutzergruppen
            foreach ($switchable['allowedUserGroups'] as $userGroupId) {

                $userGroup = UserEditor::getInstance()->getUserGroupById($userGroupId);
                if ($userGroup instanceof UserGroup) {

                    $object->addAllowedUserGroup($userGroup);
                }
            }

            //Objekt Speichern
            $this->switchables[$object->getId()] = $object;
        }

        //Aktivitaeten und Countdowns Schaltbare Objekte zuweisen
        foreach ($this->switchableList as $objectId => $list) {

            $object = $this->getElementById($objectId);
            if ($object instanceof Activity || $object instanceof Countdown) {

                foreach ($list as $switchables) {

                    //Schaltbare Objekte hinzufuegen
                    $usedSwitchable = $this->getElementById($switchables['id']);
                    if ($usedSwitchable instanceof Switchable) {

                        $object->addSwitchable($usedSwitchable, $switchables['command']);
                    }
                }
            }
        }
    }

    /**
     * gibt das Schaltbares Element der ID zurueck
     * 
     * @param  Integer $id ID
     * @return \SHC\Switchable\Element
     */
    public function getElementById($id) {

        if (isset($this->switchables[$id])) {

            return $this->switchables[$id];
        }
        return null;
    }

    /**
     * gibt eine Liste mit allen Schaltbaren Elementen zurueck
     * 
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listElements($orderBy = 'name') {

        if ($orderBy == 'id') {

            //nach ID sortieren
            $switchables = $this->switchables;
            ksort($switchables, SORT_NUMERIC);
            return $switchables;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            $switchables = $this->switchables;

            //Sortierfunktion
            $orderFunction = function($a, $b) {

                if ($a->getName() == $b->getName()) {

                    return 0;
                }

                if ($a->getName() < $b->getName()) {

                    return -1;
                }
                return 1;
            };
            usort($switchables, $orderFunction);
            return $switchables;
        }
        return $this->switchables;
    }

    /**
     * gibt eine Liste mit allen Elementen aus die keinem Raum zugeordnet sind
     *
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren,
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listElementsWithoutRoom($orderBy = 'Id') {

        $elements = array();
        foreach($this->switchables as $element) {

            $rooms = $element->getRooms();
            if(count($rooms) == 0 || (array_key_exists(0, $rooms) && $rooms[0] === null)) {

                if($orderBy == 'name') {

                    $elements[] = $element;
                } else {

                    $elements[$element->getId()] = $element;
                }
            }
        }

        //Sortieren und zurueck geben
        if ($orderBy == 'id') {

            //nach ID sortieren
            ksort($elements, SORT_NUMERIC);
            return $elements;
        } elseif ($orderBy == 'name') {

            //Sortierfunktion
            $orderFunction = function($a, $b) {

                if ($a->getName() == $b->getName()) {

                    return 0;
                }

                if ($a->getName() < $b->getName()) {

                    return -1;
                }
                return 1;
            };
            usort($elements, $orderFunction);
            return $elements;
        }
        return $elements;
    }

    /**
     * gibt eine Liste mit allen Schaltbaren Elementen eines Raumes zurueck
     * 
     * @param  Integer $roomId  ID des Raumes
     * @param  String  $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren, 
     *      orderId => nach Sortierungs ID,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listElementsForRoom($roomId, $orderBy = 'orderId') {

        //Schaltbare Elemente suchen die dem Raum zugeordnet sind
        $roomSwitchables = array();
        foreach ($this->switchables as $switchable) {

            if ($switchable->isInRoom($roomId)) {

                if ($orderBy == 'id') {

                    $roomSwitchables[$switchable->getId()] = $switchable;
                } elseif ($orderBy = 'orderId') {

                    $roomSwitchables[$switchable->getOrderId($roomId)] = $switchable;
                } else {

                    $roomSwitchables[] = $switchable;
                }
            }
        }

        //Sortieren
        if ($orderBy == 'id') {

            //nach ID sortieren
            ksort($roomSwitchables, SORT_NUMERIC);
            return $roomSwitchables;
        } elseif ($orderBy == 'orderId') {

            //nach Sortierungs ID sortieren
            ksort($roomSwitchables, SORT_NUMERIC);
            return $roomSwitchables;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            //Sortierfunktion
            $orderFunction = function($a, $b) {

                if ($a->getName() == $b->getName()) {

                    return 0;
                }

                if ($a->getName() < $b->getName()) {

                    return -1;
                }
                return 1;
            };
            usort($roomSwitchables, $orderFunction);
            return $roomSwitchables;
        }
        return $roomSwitchables;
    }

    /**
     * bearbeitet die Sortierung der Schaltbaren Elemente
     * 
     * @param  Array   $order Array mit Element ID als Index und Sortierungs ID als Wert
     * @return Boolean
     */
    public function editOrder(array $order) {

        $db = SHC::getDatabase();
        foreach($order as $switchableId => $switchableOrder) {

            if(isset($this->switchables[$switchableId])) {

                $switchableData = $db->hGetArray(self::$tableName, $switchableId);
                foreach($switchableOrder as $roomId => $roomOrder) {

                    if($switchableData['order'][$roomId]) {

                        $switchableData['order'][$roomId] = $roomOrder;
                    }
                }

                if($db->hSetArray(self::$tableName, $switchableId, $switchableData) != 0) {

                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * speichert den Status alle Elemente die veraendert wurden
     * 
     * @return Boolean
     */
    public function updateState() {

        $db = SHC::getDatabase();
        foreach($this->switchables as $switchable) {
            
            //Wenn der Status veraendert wurde Speichern
            if($switchable->isStateModified()) {
                
                //Nach Objekt suchen
                $id = $switchable->getId();
                $switchableData = $db->hGetArray(self::$tableName, $id);

                if(isset($switchableData['id']) && $switchableData['id'] == $id) {

                    $switchableData['state'] = $switchable->getState();
                    if($switchable instanceof Countdown) {

                        $switchableData['switchOffTime'] = $switchable->getSwitchOffTime()->getDatabaseDateTime();
                    }

                    if($db->hSetArray(self::$tableName, $id, $switchableData) != 0) {

                        return false;
                    }
                } else {

                    //Datensatz existiert nicht mehr
                    continue;
                }
            }
        }
        return true;
    }

    /**
     * fuegt einen schaltbaren Element einen Schaltpunkt hinzu
     *
     * @param  Integer $switchableId  ID des schaltbaren Elements
     * @param  Integer $switchPointId ID des Schaltpunktes
     * @return Boolean
     * @throws \RWF\XML\Exception\XmlException
     */
    public function addSwitchPointToSwitchable($switchableId, $switchPointId) {

        $db = SHC::getDatabase();
        if($db->hExists(self::$tableName, $switchableId)) {

            $switchableData = $db->hGetArray(self::$tableName, $switchableId);
            $switchableData['switchPoints'][] = $switchPointId;

            if($db->hSetArray(self::$tableName, $switchableId, $switchableData) != 0) {

                return false;
            }
        }
        return false;
    }

    /**
     * entfernt einen Schaltpunkt von einemschaltbaren Element
     *
     * @param  Integer $switchableId  ID des schaltbaren Elements
     * @param  Integer $switchPointId ID des Schaltpunktes
     * @return Boolean
     * @throws \RWF\XML\Exception\XmlException
     */
    public function removeSwitchpointFromSwitchable($switchableId, $switchPointId) {

        $db = SHC::getDatabase();
        if($db->hExists(self::$tableName, $switchableId)) {

            $switchableData = $db->hGetArray(self::$tableName, $switchableId);
            $switchableData['switchPoints'] = array_diff($switchableData['switchPoints'], array($switchPointId));

            if($db->hSetArray(self::$tableName, $switchableId, $switchableData) != 0) {

                return false;
            }
        }
        return false;
    }

    /**
     * erstellt ein neues Schaltbares Element
     * 
     * @param  Integer $type              Typ
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Array   $data              Zusatzdaten
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    protected function addElement($type, $name, $enabled, $visibility, $icon, array $rooms, array $order, array $switchPoints = array(), array $allowedUserGroups = array(), array $data = array()) {

        //sortierung vorbereiten
        if(count($order) == 0) {

            $order = array();
            foreach($rooms as $roomId) {

                $order[$roomId] = ViewHelperEditor::getInstance()->getNextOrderId();
            }
        }

        $db = SHC::getDatabase();
        $index = $db->autoIncrement(self::$tableName);
        $newElement = array(
            'type' => $type,
            'id' => $index,
            'name' => $name,
            'order' => $order,
            'enabled' => ($enabled == true ? true : false),
            'visibility' => ($visibility == true ? true : false),
            'state' => 0,
            'icon' => $icon,
            'rooms' => $rooms,
            'switchPoints' => $switchPoints,
            'allowedUserGroups' => $allowedUserGroups
        );

        foreach ($data as $tag => $value) {

            if (!in_array($tag, array('id', 'name', 'type', 'enabled', 'visibility', 'icon', 'rooms', 'order', 'switchPoints', 'allowedUserGroups', 'state'))) {

                $newElement[$tag] = $value;
            }
        }

        if($db->hSetNxArray(self::$tableName, $index, $newElement) == 0) {

            return false;
        }
        return true;
    }

    /**
     * erstellt ein neues Schaltbares Element
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Array   $data              Zusatzdaten
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    protected function editElement($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, $switchPoints = array(), $allowedUserGroups = array(), array $data = array()) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            $switchable = $db->hGetArray(self::$tableName, $id);

            //Name
            if ($name !== null) {

                $switchable['name'] = $name;
            }

            //Aktiv
            if ($enabled !== null) {

                $switchable['enabled'] = ($enabled == true ? true : false);
            }

            //Sichtbarkeit
            if ($visibility !== null) {

                $switchable['visibility'] = ($visibility == true ? true : false);
            }

            //Icon
            if ($icon !== null) {

                $switchable['icon'] = $icon;
            }

            //Raeume
            if ($rooms !== null) {

                //Sortierung der Raeume behabdeln
                //Vergleichen
                $oldRooms = $switchable['rooms'];
                $removedRooms = array_diff($oldRooms, $rooms);
                $addedRooms = array_diff($rooms, $oldRooms);

                //sortierung vorbereiten
                if($order === null) {

                    $order = $switchable['order'];
                }

                //entfernte Raeume
                foreach($removedRooms as $roomId) {

                    if(isset($order[$roomId])) {

                        unset($order[$roomId]);
                    }
                }

                //hinzugefuegte Raeume
                foreach($addedRooms as $roomId) {

                    $order[$roomId] = ViewHelperEditor::getInstance()->getNextOrderId();
                }

                $switchable['rooms'] = $rooms;
            }

            //Sortierung
            if ($order !== null) {

                $switchable['order'] = $order;
            }

            //Schaltpunkte
            if ($switchPoints !== null) {

                $switchable['switchPoints'] = $switchPoints;
            }

            //erlaubte Benutzergruppen
            if ($allowedUserGroups !== null) {

                $switchable['allowedUserGroups'] = $allowedUserGroups;
            }

            //Zusatzdaten
            foreach ($data as $tag => $value) {

                if (!in_array($tag, array('id', 'name', 'type', 'enabled', 'visibility', 'icon', 'roomId', 'order', 'switchPoints', 'allowedUserGroups', 'state'))) {

                    $switchable[$tag] = $value;
                }
            }

            if($db->hSetArray(self::$tableName, $id, $switchable) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * erstellt ein neue Aktivitaet
     * 
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addActivity($name, $enabled, $visibility, $icon, $rooms, array $order, array $switchPoints = array(), array $allowedUserGroups = array(), $buttonText = Element::BUTTONS_ON_OFF) {

        $data = array(
            'switchable' => array(),
            'buttonText' => $buttonText
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_ACTIVITY, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * erstellt ein neues Schaltbares Element
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editActivity($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, array $switchPoints = null, array $allowedUserGroups = null, $buttonText = null) {

        //Daten
        $data = array(
                'buttonText' => $buttonText
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * fuegt einer Aktivitaet ein Schaltbares Element hinzu
     * 
     * @param  Integer $activityId   ID der Aktivitaet
     * @param  Integer $switchableId ID des Schaltbaren Elements
     * @param  Integer $command      Befehl
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function addSwitchableToActivity($activityId, $switchableId, $command) {

        $db = SHC::getDatabase();
        if($db->hExists(self::$tableName, $activityId)) {

            $switchableData = $db->hGetArray(self::$tableName, $activityId);

            if(isset($switchableData['id']) && $switchableData['id'] == $activityId) {

                $switchableData['switchable'][] = array('id' => $switchableId, 'command' => $command);

                if($db->hSetArray(self::$tableName, $activityId, $switchableData) != 0) {

                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * schaltet den Befehl eines Schaltbaren Elements in einer Aktivitaet um
     * 
     * @param  Integer $activityId   ID der Aktivitaet
     * @param  Integer $switchableId ID des Schaltbaren Elements
     * @param  Integer $command      Befehl
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function setActivitySwitchableCommand($activityId, $switchableId, $command) {

        $db = SHC::getDatabase();
        if($db->hExists(self::$tableName, $activityId)) {

            $switchableData = $db->hGetArray(self::$tableName, $activityId);

            if(isset($switchableData['id']) && $switchableData['id'] == $activityId) {

                foreach($switchableData['switchable'] as $index => $data) {

                    if($data['id'] == $switchableId) {

                        $switchableData['switchable'][$index]['command'] = $command;
                    }
                }

                if($db->hSetArray(self::$tableName, $activityId, $switchableData) != 0) {

                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * entfernt ein Schaltbares Element von einer Aktivitaet
     * 
     * @param  Integer $activityId   ID der Aktivitaet
     * @param  Integer $switchableId ID des Schaltbaren Elements
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function removeSwitchableFromActivity($activityId, $switchableId) {

        $db = SHC::getDatabase();
        if($db->hExists(self::$tableName, $activityId)) {

            $switchableData = $db->hGetArray(self::$tableName, $activityId);

            if(isset($switchableData['id']) && $switchableData['id'] == $activityId) {

                foreach($switchableData['switchable'] as $index => $data) {

                    if($data['id'] == $switchableId) {

                        unset($switchableData['switchable'][$index]);
                    }
                }

                if($db->hSetArray(self::$tableName, $activityId, $switchableData) != 0) {

                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * erstellt einen neuen Arduino Ausgang
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $interval          Zeitintervall
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addCountdown($name, $enabled, $visibility, $icon, $rooms, array $order, $interval, array $switchPoints = array(), array $allowedUserGroups = array(), $buttonText = Element::BUTTONS_ON_OFF) {

        //Daten Vorbereiten
        $data = array(
            'interval' => $interval,
            'switchOffTime' => '2000-01-01 00:00:00',
            'switchable' => array(),
            'buttonText' => $buttonText
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_COUNTDOWN, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Arduino Ausgang
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $interval          Zeitintervall
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editCountdown($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, $interval = null, array $switchPoints = null, array $allowedUserGroups = null, $buttonText = null) {

        //Daten Vorbereiten
        $data = array(
            'interval' => $interval,
            'buttonText' => $buttonText
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * 
     * @param  Integer            $countdownId ID des Countdowns
     * @param  \RWF\Date\DateTime $time        Zeitobjekt
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function editCountdownSwitchOffTime($countdownId, DateTime $time) {

        $db = SHC::getDatabase();
        if($db->hExists(self::$tableName, $countdownId)) {

            $switchableData = $db->hGetArray(self::$tableName, $countdownId);

            if(isset($switchableData['id']) && $switchableData['id'] == $countdownId) {

                $switchableData['switchOffTime'] = $time->getDatabaseDateTime();

                if($db->hSetArray(self::$tableName, $countdownId, $switchableData) != 0) {

                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * fuegt einem Countdown ein Schaltbares Element hinzu
     * 
     * @param  Integer $countdownId  ID der Aktivitaet
     * @param  Integer $switchableId ID des Schaltbaren Elements
     * @param  Integer $command      Befehl
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function addSwitchableToCountdown($countdownId, $switchableId, $command) {

        $db = SHC::getDatabase();
        if($db->hExists(self::$tableName, $countdownId)) {

            $switchableData = $db->hGetArray(self::$tableName, $countdownId);

            if(isset($switchableData['id']) && $switchableData['id'] == $countdownId) {

                $switchableData['switchable'][] = array('id' => $switchableId, 'command' => $command);

                if($db->hSetArray(self::$tableName, $countdownId, $switchableData) != 0) {

                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * schaltet den Befehl eines Schaltbaren Elements in einem Countdown um
     * 
     * @param  Integer $countdownId   ID der Aktivitaet
     * @param  Integer $switchableId ID des Schaltbaren Elements
     * @param  Integer $command      Befehl
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function setCountdownSwitchableCommand($countdownId, $switchableId, $command) {

        $db = SHC::getDatabase();
        if($db->hExists(self::$tableName, $countdownId)) {

            $switchableData = $db->hGetArray(self::$tableName, $countdownId);

            if(isset($switchableData['id']) && $switchableData['id'] == $countdownId) {

                foreach($switchableData['switchable'] as $index => $data) {

                    if($data['id'] == $switchableId) {

                        $switchableData['switchable'][$index]['command'] = $command;
                    }
                }

                if($db->hSetArray(self::$tableName, $countdownId, $switchableData) != 0) {

                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * entfernt ein Schaltbares Element von einem Countdown
     * 
     * @param  Integer $countdownId   ID der Aktivitaet
     * @param  Integer $switchableId ID des Schaltbaren Elements
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function removeSwitchableFromCountdown($countdownId, $switchableId) {

        $db = SHC::getDatabase();
        if($db->hExists(self::$tableName, $countdownId)) {

            $switchableData = $db->hGetArray(self::$tableName, $countdownId);

            if(isset($switchableData['id']) && $switchableData['id'] == $countdownId) {

                foreach($switchableData['switchable'] as $index => $data) {

                    if($data['id'] == $switchableId) {

                        unset($switchableData['switchable'][$index]);
                    }
                }

                if($db->hSetArray(self::$tableName, $countdownId, $switchableData) != 0) {

                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * erstellt eine neue Funksteckdose
     * 
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $protocol          Protokoll
     * @param  String  $systemCode        System Code
     * @param  String  $deviceCode        Geraete Code
     * @param  Integer $continuous        Anzahl der Sendevorgaenge
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRadioSocket($name, $enabled, $visibility, $icon, $rooms, array $order, $protocol, $systemCode, $deviceCode, $continuous, array $switchPoints = array(), array $allowedUserGroups = array(), $buttonText = Element::BUTTONS_ON_OFF) {
        
        //Daten Vorbereiten
        $data = array(
            'protocol' => $protocol,
            'systemCode' => $systemCode,
            'deviceCode' => $deviceCode,
            'continuous' => $continuous,
            'buttonText' => $buttonText
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_RADIOSOCKET, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet eine Funksteckdose
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $protocol          Protokoll
     * @param  String  $systemCode        System Code
     * @param  String  $deviceCode        Geraete Code
     * @param  Integer $continuous        Anzahl der Sendevorgaenge
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRadioSocket($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, $protocol = null, $systemCode = null, $deviceCode = null, $continuous = null, array $switchPoints = null, array $allowedUserGroups = null, $buttonText = null) {
        
        //Daten Vorbereiten
        $data = array(
            'protocol' => $protocol,
            'systemCode' => $systemCode,
            'deviceCode' => $deviceCode,
            'continuous' => $continuous,
            'buttonText' => $buttonText
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * erstellt einen neuen Raspberry Pi GPIO Ausgang
     * 
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRriGpioOutput($name, $enabled, $visibility, $icon, $rooms, array $order, $switchServerId, $pinNumber, array $switchPoints = array(), array $allowedUserGroups = array(), $buttonText = Element::BUTTONS_ON_OFF) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber,
            'buttonText' => $buttonText
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_RPI_GPIO_OUTPUT, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Raspberry Pi GPIO Ausgang
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRpiGpioOutput($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, $switchServerId = null, $pinNumber = null, array $switchPoints = null, array $allowedUserGroups = null, $buttonText = null) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber,
            'buttonText' => $buttonText
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * erstellt einen neuen Wake On Lan
     * 
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $mac               Schaltserver ID
     * @param  String  $ipAddress         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addWakeOnLan($name, $enabled, $visibility, $icon, $rooms, array $order, $mac, $ipAddress, array $switchPoints = array(), array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'mac' => $mac,
            'ipAddress' => $ipAddress
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_WAKEONLAN, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Wake On Lan
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $mac               Schaltserver ID
     * @param  String  $ipAddress         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editWakeOnLan($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, $mac = null, $ipAddress = null, array $switchPoints = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'mac' => $mac,
            'ipAddress' => $ipAddress
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }
    
    /**
     * erstellt einen neuen Raspberry Pi GPIO Input
     * 
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRriGpioInput($name, $enabled, $visibility, $icon, $rooms, array $order, $switchServerId, $pinNumber, array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_RPI_GPIO_INPUT, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Raspberry Pi GPIO Input
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRpiGpioInput($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, $switchServerId = null, $pinNumber = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups, $data);
    }

    /**
     * erstellt eine neuen Funkdimmer
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $protocol          Protokoll
     * @param  String  $systemCode        System Code
     * @param  String  $deviceCode        Geraete Code
     * @param  Integer $continuous        Anzahl der Sendevorgaenge
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRadioSocketDimmer($name, $enabled, $visibility, $icon, $rooms, array $order, $protocol, $systemCode, $deviceCode, $continuous, array $switchPoints = array(), array $allowedUserGroups = array()) {

        //Daten Vorbereiten
        $data = array(
            'protocol' => $protocol,
            'systemCode' => $systemCode,
            'deviceCode' => $deviceCode,
            'continuous' => $continuous
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_RADIOSOCKET_DIMMER, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Funkdimmer
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $protocol          Protokoll
     * @param  String  $systemCode        System Code
     * @param  String  $deviceCode        Geraete Code
     * @param  Integer $continuous        Anzahl der Sendevorgaenge
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRadioSocketDimmer($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, $protocol = null, $systemCode = null, $deviceCode = null, $continuous = null, array $switchPoints = null, array $allowedUserGroups = null) {

        //Daten Vorbereiten
        $data = array(
            'protocol' => $protocol,
            'systemCode' => $systemCode,
            'deviceCode' => $deviceCode,
            'continuous' => $continuous
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * erstellt ein Reboot
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addReboot($name, $enabled, $visibility, $icon, $rooms, array $order, array $allowedUserGroups = array()) {

        //Datensatz erstellen
        return $this->addElement(self::TYPE_REBOOT, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups);
    }

    /**
     * bearbeitet ein Reboot
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editReboot($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, array $allowedUserGroups = null) {

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups);
    }

    /**
     * erstellt ein Shutdown
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addShutdown($name, $enabled, $visibility, $icon, $rooms, array $order, array $allowedUserGroups = array()) {

        //Datensatz erstellen
        return $this->addElement(self::TYPE_SHUTDOWN, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups);
    }

    /**
     * bearbeitet ein Shutdown
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editShutdown($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, array $allowedUserGroups = null) {

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups);
    }

    /**
     * erstellt einen externen Reboot
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRemoteReboot($name, $enabled, $visibility, $icon, $rooms, array $order, array $allowedUserGroups = array()) {

        //Datensatz erstellen
        return $this->addElement(self::TYPE_REMOTE_REBOOT, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups);
    }

    /**
     * bearbeitet einen externen Reboot
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRemoteReboot($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, array $allowedUserGroups = null) {

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups);
    }

    /**
     * erstellt ein externes Shutdown
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRemoteShutdown($name, $enabled, $visibility, $icon, $rooms, array $order, array $allowedUserGroups = array()) {

        //Datensatz erstellen
        return $this->addElement(self::TYPE_REMOTE_SHUTDOWN, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups);
    }

    /**
     * bearbeitet ein externes Shutdown
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRemoteShutdown($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, array $allowedUserGroups = null) {

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups);
    }

    /**
     * erstellt einen neuen Raspberry Pi GPIO Input
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $onCommand         Einschaltkommando
     * @param  String  $offCommand        Ausschaltkommando
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addScript($name, $enabled, $visibility, $icon, $rooms, array $order, $onCommand, $offCommand, array $allowedUserGroups = array(), $buttonText = Element::BUTTONS_ON_OFF) {

        //Daten Vorbereiten
        $data = array(
            'onCommand' => $onCommand,
            'offCommand' => $offCommand,
            'buttonText' => $buttonText
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_SCRIPT, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Raspberry Pi GPIO Input
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $onCommand         Einschaltkommando
     * @param  String  $offCommand        Ausschaltkommando
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editScript($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, $onCommand = null, $offCommand = null, array $allowedUserGroups = null, $buttonText = null) {

        //Daten Vorbereiten
        $data = array(
            'onCommand' => $onCommand,
            'offCommand' => $offCommand,
            'buttonText' => $buttonText
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups, $data);
    }

    /**
     * erstellt ein neue AVM Steckdose
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $ain               Identifizierung
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addAvmSocket($name, $enabled, $visibility, $icon, $rooms, array $order, $ain, array $allowedUserGroups = array(), $buttonText = Element::BUTTONS_ON_OFF) {

        //Daten Vorbereiten
        $data = array(
            'ain' => $ain,
            'buttonText' => $buttonText
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_AVM_SOCKET, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups, $data);
    }

    /**
     * bearbeitet eine AVM Steckdose
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  String  $ain               Identifizierung
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editAvmSocket($id, $name = null, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, $ain = null, array $allowedUserGroups = null, $buttonText = null) {

        //Daten Vorbereiten
        $data = array(
            'ain' => $ain,
            'buttonText' => $buttonText
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups, $data);
    }

    /**
     * erstellt ein neue AVM Steckdose
     *
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  int     $function          Funktion
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addFritzBox($name, $enabled, $visibility, $icon, $rooms, array $order, $function, array $allowedUserGroups = array()) {

        //Daten Vorbereiten
        $data = array(
            'function' => $function
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_FRITZBOX, '', $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups, $data);
    }

    /**
     * bearbeitet eine AVM Steckdose
     *
     * @param  Integer $id                ID
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Array   $rooms             Raeume
     * @param  Array   $order             Sortierung
     * @param  int     $function          Funktion
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Integer $buttonText        Button Text
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editFritzBox($id, $enabled = null, $visibility = null, $icon = null, $rooms = null, $order = null, $function = null, array $allowedUserGroups = null) {

        //Daten Vorbereiten
        $data = array(
            'function' => $function
        );

        //Datensatz bearbeiten
        return $this->editElement($id, null, $enabled, $visibility, $icon, $rooms, $order, array(), $allowedUserGroups, $data);
    }

    /**
     * loascht ein Schaltbares Element
     * 
     * @param  Integer $id ID
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function removeSwitchable($id) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            if($db->hDel(self::$tableName, $id)) {

                return true;
            }
        }
        return false;
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {
        
    }

    /**
     * gibt den Editor fuer Schaltbare Elemente zurueck
     * 
     * @return \SHC\Switchable\SwitchableEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new SwitchableEditor();
        }
        return self::$instance;
    }

}
