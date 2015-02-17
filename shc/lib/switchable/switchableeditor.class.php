<?php

namespace SHC\Switchable;

//Imports
use RWF\Util\String;
use SHC\Core\SHC;
use RWF\XML\XmlFileManager;
use RWF\Date\DateTime;
use SHC\Room\Room;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\ArduinoOutput;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\RadioSocketDimmer;
use SHC\Switchable\Switchables\Reboot;
use SHC\Switchable\Switchables\RemoteReboot;
use SHC\Switchable\Switchables\RemoteShutdown;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\Script;
use SHC\Switchable\Switchables\Shutdown;
use SHC\Switchable\Switchables\WakeOnLan;
use SHC\Switchable\Readables\ArduinoInput;
use SHC\Switchable\Readables\RpiGpioInput;
use SHC\Room\RoomEditor;
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
     * Arduino Ausgang
     * 
     * @var Integer
     */
    const TYPE_ARDUINO_OUTPUT = 2;

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
     * Arduino Eingang
     * 
     * @var Integer
     */
    const TYPE_ARDUINO_INPUT = 64;
    
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
    protected static $tableName = 'switchables';

    protected function __construct() {

        $this->loadData();
    }

    /**
     * laedt die Bedingungen aus den XML Daten und erzeugt die Objekte
     */
    public function loadData() {

        $switchables = SHC::getDatabase()->hGetAll(self::$tableName);
        foreach ($switchables as $switchable) {

            //Objekte initialisiernen und Spezifische Daten setzen
            switch ((int) $switchable['type']) {

                case self::TYPE_ACTIVITY:

                    $object = new Activity();

                    //Switchable IDs zwischenspeichern (erst nach dem Laden alle Objekte setzen)
                    $list = array();
                    foreach ($switchable['switchable'] as $activitySwitchable) {

                        $list[] = $activitySwitchable;
                    }
                    $this->switchableList[(int) $switchable['id']] = $list;
                    break;
                case self::TYPE_ARDUINO_OUTPUT:

                    $object = new ArduinoOutput();
                    $object->setDeviceId((string) $switchable['deviceId']);
                    $object->setPinNumber((int) $switchable['pinNumber']);
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
                    break;
                case self::TYPE_RPI_GPIO_OUTPUT:

                    $object = new RpiGpioOutput();
                    $object->setSwitchServer((int) $switchable['switchServer']);
                    $object->setPinNumber((int) $switchable['pinNumber']);
                    break;
                case self::TYPE_WAKEONLAN:

                    $object = new WakeOnLan();
                    $object->setMac((string) $switchable['mac']);
                    $object->setIpAddress((string) $switchable['ipAddress']);
                    break;
                case self::TYPE_ARDUINO_INPUT:

                    $object = new ArduinoInput();
                    $object->setDeviceId((string) $switchable['deviceId']);
                    $object->setPinNumber((int) $switchable['pinNumber']);
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
                    break;
                default:

                    throw new Exception('Unbekannter Typ', 1506);
            }

            //Allgemeine Daten setzen
            $object->setId((int) $switchable['id']);
            $object->setName((string) $switchable['name']);
            $object->enable(((string) $switchable['enabled'] == true ? true : false));
            $object->setVisibility(((string) $switchable['visibility'] == true ? true : false));
            $object->setIcon((string) $switchable['icon']);
            $room = RoomEditor::getInstance()->getRoomById((int) $switchable['roomId']);
            if($room instanceof Room) {
                $object->setRoom($room);
            }
            $object->setOrderId((int) $switchable['orderId']);
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
     * prueft ob der Name des Schaltbaren Elements schon verwendet wird
     * 
     * @param  String  $name Name
     * @return Boolean
     */
    public function isElementNameAvailable($name) {

        foreach ($this->switchables as $switchable) {

            /* @var $switchable \SHC\Switchable\Switchable */
            if (String::toLower($switchable->getName()) == String::toLower($name)) {

                return false;
            }
        }
        return true;
    }

    /**
     * gibt eine Liste mit allen Schaltbaren Elementen zurueck
     * 
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren, 
     *      orderId => nach Sortierungs ID,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listElements($orderBy = 'orderId') {

        if ($orderBy == 'id') {

            //nach ID sortieren
            $switchables = $this->switchables;
            ksort($switchables, SORT_NUMERIC);
            return $switchables;
        } elseif ($orderBy == 'orderId') {

            //nach Sortierungs ID sortieren
            $switchables = array();
            foreach ($this->switchables as $element) {

                /* @var $element \SHC\Switchable\Element */
                $switchables[$element->getOrderId()] = $element;
            }

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

            if(!$element->getRoom() instanceof Room) {

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

            if ($switchable->getRoom()->getId() == $roomId) {

                if ($orderBy == 'id') {

                    $roomSwitchables[$switchable->getId()] = $switchable;
                } elseif ($orderBy = 'orderId') {

                    $roomSwitchables[$switchable->getOrderId()] = $switchable;
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
        foreach($order as $switchableId => $orderId) {

            if(isset($this->switchables[$switchableId])) {

                $switchableData = $db->hGet(self::$tableName, $switchableId);
                $switchableData['orderId'] = $orderId;

                if($db->hSet(self::$tableName, $switchableId, $switchableData) != 0) {

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
                $switchableData = $db->hGet(self::$tableName, $id);
                $switchableData['state'] = $switchable->getState();
                if($switchable instanceof Countdown) {

                    $switchableData['switchOffTime'] = $switchable->getSwitchOffTime()->getDatabaseDateTime();
                }

                if($db->hSet(self::$tableName, $id, $switchableData) != 0) {

                    return false;
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

            $switchableData = $db->hGet(self::$tableName, $switchableId);
            $switchableData['switchPoints'][] = $switchPointId;

            if($db->hSet(self::$tableName, $switchableId, $switchableData) != 0) {

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

            $switchableData = $db->hGet(self::$tableName, $switchableId);
            $switchableData['switchPoints'] = array_diff($switchableData['switchPoints'], array($switchPointId));

            if($db->hSet(self::$tableName, $switchableId, $switchableData) != 0) {

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
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Array   $data              Zusatzdaten
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    protected function addElement($type, $name, $enabled, $visibility, $icon, $room, $orderId, array $switchPoints = array(), array $allowedUserGroups = array(), array $data = array()) {

        //Ausnahme wenn Elementname schon belegt
        if (!$this->isElementNameAvailable($name)) {

            throw new \Exception('Der Name ist schon vergeben', 1507);
        }

        $db = SHC::getDatabase();
        $index = $db->autoIncrement(self::$tableName);
        $newElement = array(
            'type' => $type,
            'id' => $index,
            'name' => $name,
            'orderId' => $orderId,
            'enabled' => ($enabled == true ? true : false),
            'visibility' => ($visibility == true ? true : false),
            'state' => 0,
            'icon' => $icon,
            'roomId' => $room,
            'switchPoints' => $switchPoints,
            'allowedUserGroups' => $allowedUserGroups
        );

        foreach ($data as $tag => $value) {

            if (!in_array($tag, array('id', 'name', 'type', 'enabled', 'visibility', 'icon', 'roomId', 'orderId', 'switchPoints', 'allowedUserGroups', 'state'))) {

                $newElement[$tag] = $value;
            }
        }

        if($db->hSetNx(self::$tableName, $index, $newElement) == 0) {

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
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Array   $data              Zusatzdaten
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    protected function editElement($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, $switchPoints = array(), $allowedUserGroups = array(), array $data = array()) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            $switchable = $db->hGet(self::$tableName, $id);

            //Name
            if ($name !== null) {

                //Ausnahme wenn Name der Bedingung schon belegt
                if ($name != (string) $switchable['name'] && !$this->isElementNameAvailable($name)) {

                    throw new \Exception('Der Name ist schon vergeben', 1507);
                }

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

            //Raum
            if ($room !== null) {

                //neue Sortierungs ID beim wechseln des Raumes um doppelte Sortierungs IDs zu vermeiden
                if($room != (int) $switchable['roomId'] && $orderId === null) {

                    $switchable['orderId'] = ViewHelperEditor::getInstance()->getNextOrderId();
                }
                $switchable['roomId'] = $room;
            }

            //Sortierungs ID
            if ($orderId !== null) {

                $switchable['orderId'] = $orderId;
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

                if (!in_array($tag, array('id', 'name', 'type', 'enabled', 'visibility', 'icon', 'roomId', 'orderId', 'switchPoints', 'allowedUserGroups', 'state'))) {

                    $switchable[$tag] = $value;
                }
            }

            if($db->hSet(self::$tableName, $id, $switchable) == 0) {

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
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addActivity($name, $enabled, $visibility, $icon, $room, $orderId, array $switchPoints = array(), array $allowedUserGroups = array()) {

        $data = array(
                'switchable' => array()
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_ACTIVITY, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * erstellt ein neues Schaltbares Element
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editAcrivity($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, array $switchPoints = null, array $allowedUserGroups = null) {

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups);
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

            $switchableData = $db->hGet(self::$tableName, $activityId);
            $switchableData['switchable'][] = array('id' => $switchableId, 'command' => $command);

            if($db->hSet(self::$tableName, $activityId, $switchableData) != 0) {

                return false;
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

            $switchableData = $db->hGet(self::$tableName, $activityId);
            foreach($switchableData['switchable'] as $index => $data) {

                if($data['id'] == $switchableId) {

                    $switchableData['switchable'][$index]['command'] = $command;
                }
            }

            if($db->hSet(self::$tableName, $activityId, $switchableData) != 0) {

                return false;
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

            $switchableData = $db->hGet(self::$tableName, $activityId);
            foreach($switchableData['switchable'] as $index => $data) {

                if($data['id'] == $switchableId) {

                    unset($switchableData['switchable'][$index]);
                }
            }

            if($db->hSet(self::$tableName, $activityId, $switchableData) != 0) {

                return false;
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
     * @param  Integer $room              Raum ID
     * @param  String  $deviceId          Geraete ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addArduinoOutput($name, $enabled, $visibility, $icon, $room, $deviceId, $pinNumber, array $switchPoints = array(), array $allowedUserGroups = array()) {

        //Daten Vorbereiten
        $data = array(
            'deviceId' => $deviceId,
            'pinNumber' => $pinNumber
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_ARDUINO_OUTPUT, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Arduino Ausgang
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  String  $deviceId          Geraete ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editArduinoOutput($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $deviceId = null, $pinNumber = null, array $switchPoints = null, array $allowedUserGroups = null) {

        //Daten Vorbereiten
        $data = array(
            'deviceId' => $deviceId,
            'pinNumber' => $pinNumber
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
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

            $switchableData = $db->hGet(self::$tableName, $countdownId);
            $switchableData['switchOffTime'] = $time->getDatabaseDateTime();

            if($db->hSet(self::$tableName, $countdownId, $switchableData) != 0) {

                return false;
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
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  String  $interval          Zeitintervall
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addCountdown($name, $enabled, $visibility, $icon, $room, $orderId, $interval, array $switchPoints = array(), array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'interval' => $interval,
            'switchOffTime' => '2000-01-01 00:00:00',
            'switchable' => array()
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_COUNTDOWN, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Arduino Ausgang
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  String  $interval          Zeitintervall
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editCountdown($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, $interval = null, array $switchPoints = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'interval' => $interval
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups, $data);
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

            $switchableData = $db->hGet(self::$tableName, $countdownId);
            $switchableData['switchable'][] = array('id' => $switchableId, 'command' => $command);

            if($db->hSet(self::$tableName, $countdownId, $switchableData) != 0) {

                return false;
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

            $switchableData = $db->hGet(self::$tableName, $countdownId);
            foreach($switchableData['switchable'] as $index => $data) {

                if($data['id'] == $switchableId) {

                    $switchableData['switchable'][$index]['command'] = $command;
                }
            }

            if($db->hSet(self::$tableName, $countdownId, $switchableData) != 0) {

                return false;
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

            $switchableData = $db->hGet(self::$tableName, $countdownId);
            foreach($switchableData['switchable'] as $index => $data) {

                if($data['id'] == $switchableId) {

                    unset($switchableData['switchable'][$index]);
                }
            }

            if($db->hSet(self::$tableName, $countdownId, $switchableData) != 0) {

                return false;
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
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  String  $protocol          Protokoll
     * @param  String  $systemCode        System Code
     * @param  String  $deviceCode        Geraete Code
     * @param  Integer $continuous        Anzahl der Sendevorgaenge
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRadioSocket($name, $enabled, $visibility, $icon, $room, $orderId, $protocol, $systemCode, $deviceCode, $continuous, array $switchPoints = array(), array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'protocol' => $protocol,
            'systemCode' => $systemCode,
            'deviceCode' => $deviceCode,
            'continuous' => $continuous
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_RADIOSOCKET, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet eine Funksteckdose
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  String  $protocol          Protokoll
     * @param  String  $systemCode        System Code
     * @param  String  $deviceCode        Geraete Code
     * @param  Integer $continuous        Anzahl der Sendevorgaenge
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRadioSocket($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, $protocol = null, $systemCode = null, $deviceCode = null, $continuous = null, array $switchPoints = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'protocol' => $protocol,
            'systemCode' => $systemCode,
            'deviceCode' => $deviceCode,
            'continuous' => $continuous
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * erstellt einen neuen Raspberry Pi GPIO Ausgang
     * 
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRriGpioOutput($name, $enabled, $visibility, $icon, $room, $orderId, $switchServerId, $pinNumber, array $switchPoints = array(), array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_RPI_GPIO_OUTPUT, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Raspberry Pi GPIO Ausgang
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRpiGpioOutput($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, $switchServerId = null, $pinNumber = null, array $switchPoints = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * erstellt einen neuen Wake On Lan
     * 
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  String  $mac               Schaltserver ID
     * @param  String  $ipAddress         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addWakeOnLan($name, $enabled, $visibility, $icon, $room, $orderId, $mac, $ipAddress, array $switchPoints = array(), array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'mac' => $mac,
            'ipAddress' => $ipAddress
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_WAKEONLAN, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Wake On Lan
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  String  $mac               Schaltserver ID
     * @param  String  $ipAddress         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editWakeOnLan($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, $mac = null, $ipAddress = null, array $switchPoints = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'mac' => $mac,
            'ipAddress' => $ipAddress
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups, $data);
    }
    
    /**
     * erstellt einen neuen Arduino Eingang
     * 
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  String  $deviceId          Geraete ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addArduinoInput($name, $enabled, $visibility, $icon, $room, $deviceId, $pinNumber, array $switchPoints = array(), array $allowedUserGroups = array()) {

        //Daten Vorbereiten
        $data = array(
            'deviceId' => $deviceId,
            'pinNumber' => $pinNumber
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_ARDUINO_INPUT, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Arduino Eingang
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  String  $deviceId          Geraete ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editArduinoInput($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $deviceId = null, $pinNumber = null, array $switchPoints = null, array $allowedUserGroups = null) {

        //Daten Vorbereiten
        $data = array(
            'deviceId' => $deviceId,
            'pinNumber' => $pinNumber
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
    }
    
    /**
     * erstellt einen neuen Raspberry Pi GPIO Input
     * 
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRriGpioInput($name, $enabled, $visibility, $icon, $room, $orderId, $switchServerId, $pinNumber, array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_RPI_GPIO_INPUT, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Raspberry Pi GPIO Input
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRpiGpioInput($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, $switchServerId = null, $pinNumber = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups, $data);
    }

    /**
     * erstellt eine neuen Funkdimmer
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  String  $protocol          Protokoll
     * @param  String  $systemCode        System Code
     * @param  String  $deviceCode        Geraete Code
     * @param  Integer $continuous        Anzahl der Sendevorgaenge
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRadioSocketDimmer($name, $enabled, $visibility, $icon, $room, $orderId, $protocol, $systemCode, $deviceCode, $continuous, array $switchPoints = array(), array $allowedUserGroups = array()) {

        //Daten Vorbereiten
        $data = array(
            'protocol' => $protocol,
            'systemCode' => $systemCode,
            'deviceCode' => $deviceCode,
            'continuous' => $continuous
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_RADIOSOCKET_DIMMER, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Funkdimmer
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  String  $protocol          Protokoll
     * @param  String  $systemCode        System Code
     * @param  String  $deviceCode        Geraete Code
     * @param  Integer $continuous        Anzahl der Sendevorgaenge
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRadioSocketDimmer($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, $protocol = null, $systemCode = null, $deviceCode = null, $continuous = null, array $switchPoints = null, array $allowedUserGroups = null) {

        //Daten Vorbereiten
        $data = array(
            'protocol' => $protocol,
            'systemCode' => $systemCode,
            'deviceCode' => $deviceCode,
            'continuous' => $continuous
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * erstellt ein Reboot
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addReboot($name, $enabled, $visibility, $icon, $room, $orderId, array $allowedUserGroups = array()) {

        //Datensatz erstellen
        return $this->addElement(self::TYPE_REBOOT, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups);
    }

    /**
     * bearbeitet ein Reboot
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editReboot($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, array $allowedUserGroups = null) {

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups);
    }

    /**
     * erstellt ein Shutdown
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addShutdown($name, $enabled, $visibility, $icon, $room, $orderId, array $allowedUserGroups = array()) {

        //Datensatz erstellen
        return $this->addElement(self::TYPE_SHUTDOWN, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups);
    }

    /**
     * bearbeitet ein Shutdown
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editShutdown($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, array $allowedUserGroups = null) {

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups);
    }

    /**
     * erstellt einen externen Reboot
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRemoteReboot($name, $enabled, $visibility, $icon, $room, $orderId, array $allowedUserGroups = array()) {

        //Datensatz erstellen
        return $this->addElement(self::TYPE_REMOTE_REBOOT, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups);
    }

    /**
     * bearbeitet einen externen Reboot
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRemoteReboot($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, array $allowedUserGroups = null) {

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups);
    }

    /**
     * erstellt ein externes Shutdown
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRemoteShutdown($name, $enabled, $visibility, $icon, $room, $orderId, array $allowedUserGroups = array()) {

        //Datensatz erstellen
        return $this->addElement(self::TYPE_REMOTE_SHUTDOWN, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups);
    }

    /**
     * bearbeitet ein externes Shutdown
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRemoteShutdown($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, array $allowedUserGroups = null) {

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups);
    }

    /**
     * erstellt einen neuen Raspberry Pi GPIO Input
     *
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  String  $onCommand         Einschaltkommando
     * @param  String  $offCommand        Ausschaltkommando
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addScript($name, $enabled, $visibility, $icon, $room, $orderId, $onCommand, $offCommand, array $allowedUserGroups = array()) {

        //Daten Vorbereiten
        $data = array(
            'onCommand' => $onCommand,
            'offCommand' => $offCommand
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_SCRIPT, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups, $data);
    }

    /**
     * bearbeitet einen Raspberry Pi GPIO Input
     *
     * @param  Integer $id                ID
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $orderId           Sortierungs ID
     * @param  String  $onCommand         Einschaltkommando
     * @param  String  $offCommand        Ausschaltkommando
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editScript($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $orderId = null, $onCommand = null, $offCommand = null, array $allowedUserGroups = null) {

        //Daten Vorbereiten
        $data = array(
            'onCommand' => $onCommand,
            'offCommand' => $offCommand
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $orderId, array(), $allowedUserGroups, $data);
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
