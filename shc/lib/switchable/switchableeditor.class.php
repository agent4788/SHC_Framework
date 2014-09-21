<?php

namespace SHC\Switchable;

//Imports
use SHC\Core\SHC;
use RWF\XML\XmlFileManager;
use RWF\Date\DateTime;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\ArduinoOutput;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\WakeOnLan;
use SHC\Switchable\Readables\ArduinoInput;
use SHC\Switchable\Readables\RpiGpioInput;
use SHC\Room\RoomEditor;
use SHC\Timer\SwitchPointEditor;
use SHC\Timer\SwitchPoint;
use RWF\User\UserEditor;
use RWF\User\UserGroup;

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

    protected function __construct() {

        $this->loadData();
    }

    /**
     * laedt die Bedingungen aus den XML Daten und erzeugt die Objekte
     */
    public function loadData() {

        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES);

        //Daten einlesen
        foreach ($xml->switchable as $switchable) {

            //Objekte initialisiernen und Spezifische Daten setzen
            switch ((int) $switchable->type) {

                case self::TYPE_ACTIVITY:

                    $object = new Activity();

                    //Switchable IDs zwischenspeichern (erst nach dem Laden alle Objekte setzen)
                    $list = array();
                    foreach ($switchable->switchable as $activitySwitchable) {

                        /* @var $activitySwitchable \SimpleXmlElement */
                        $attributes = $activitySwitchable->attributes();
                        $list[] = array('id' => (int) $attributes->id, 'command' => (int) $attributes->command);
                    }
                    $this->switchableList[(int) $switchable->id] = $list;
                    break;
                case self::TYPE_ARDUINO_OUTPUT:

                    $object = new ArduinoOutput();
                    $object->setDeviceId((string) $switchable->deviceId);
                    $object->setPinNumber((int) $switchable->pinNumber);
                    break;
                case self::TYPE_COUNTDOWN:

                    $object = new Countdown();
                    $object->setIntervall(new \DateInterval((string) $switchable->intervall));
                    $object->setSwitchOffTime(DateTime::createFromDatabaseDateTime((string) $switchable->switchOffTime));

                    //Switchable IDs zwischenspeichern (erst nach dem Laden alle Objekte setzen)
                    $list = array();
                    foreach ($switchable->switchable as $countdownSwitchable) {

                        /* @var $countdownSwitchable \SimpleXmlElement */
                        $attributes = $countdownSwitchable->attributes();
                        $list[] = array('id' => (int) $attributes->id, 'command' => (int) $attributes->command);
                    }
                    $this->switchableList[(int) $switchable->id] = $list;
                    break;
                case self::TYPE_RADIOSOCKET:

                    $object = new RadioSocket();
                    $object->setProtocol((string) $switchable->protocol);
                    $object->setSystemCode((string) $switchable->systemCode);
                    $object->setDeviceCode((string) $switchable->deviceCode);
                    break;
                case self::TYPE_RPI_GPIO_OUTPUT:

                    $object = new RpiGpioOutput();
                    $object->setSwitchServer((int) $switchable->switchServer);
                    $object->setPinNumber((int) $switchable->pinNumber);
                    break;
                case self::TYPE_WAKEONLAN:

                    $object = new WakeOnLan();
                    $object->setMac((string) $switchable->mac);
                    $object->setIpAddress((string) $switchable->ipAddress);
                    break;
                case self::TYPE_ARDUINO_INPUT:

                    $object = new ArduinoInput();
                    $object->setDeviceId((string) $switchable->deviceId);
                    $object->setPinNumber((int) $switchable->pinNumber);
                    break;
                case self::TYPE_RPI_GPIO_INPUT:

                    $object = new RpiGpioInput();
                    $object->setSwitchServer((int) $switchable->switchServer);
                    $object->setPinNumber((int) $switchable->pinNumber);
                    break;
                default:

                    throw new Exception('Unbekannter Typ', 1506);
            }

            //Allgemeine Daten setzen
            $object->setId((int) $switchable->id);
            $object->setName((string) $switchable->name);
            $object->enable(((string) $switchable->enabled == 1 ? true : false));
            $object->setVisibility(((string) $switchable->visibility == 1 ? true : false));
            $object->setIcon((string) $switchable->icon);
            $object->setRoom(RoomEditor::getInstance()->getRoomById((int) $switchable->roomId));
            $object->setOrderId((int) $switchable->orderId);
            $object->setState((int) $switchable->state);

            //Schaltpunkte
            $switchPoints = explode(',', (string) $switchable->switchPoints);
            foreach ($switchPoints as $switchPointId) {

                $switchPoint = SwitchPointEditor::getInstance()->getSwitchPointById($switchPointId);
                if ($switchPoint instanceof SwitchPoint) {

                    $object->addSwitchPoint();
                }
            }

            //Benutzergruppen
            $userGroups = explode(',', (string) $switchable->allowedUserGrups);
            foreach ($userGroups as $userGroupId) {

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
            foreach ($this->switchables as $room) {

                /* @var $room \SHC\Room\Room */
                $switchables[$room->getOrderId()] = $room;
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
                } elseif ($orderBy = 'orderID') {

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
    public function editsOrder(array $order) {

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES, true);

        //Raeume durchlaufen und deren Sortierungs ID anpassen
        foreach ($xml->switchable as $switchable) {

            if (isset($order[(int) $switchable->id])) {

                $switchable->orderId = $order[(int) $switchable->id];
            }
        }

        //Daten Speichern
        $xml->save();
        return true;
    }
    
    /**
     * speichert den Status alle Elemente die veraendert wurden
     * 
     * @return Boolean
     */
    public function updateState() {
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES, true);
        
        //Alle Elemente durchlaufen
        foreach($this->switchables as $switchable) {
            
            //Wenn der Status veraendert wurde Speichern
            if($switchable->isStateModified()) {
                
                //Nach Objekt suchen
                $id = $switchable->getId();
                foreach($xml->switchable as $xmlSwitchable) {
                    
                    if((int) $xmlSwitchable->id == $id) {
                        
                        $xmlSwitchable->state = $switchable->getState();
                    }
                }
            }
        }
        
        //Daten Speichern
        $xml->save();
        return true;
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
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Array   $data              Zusatzdaten
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    protected function addElement($type, $name, $enabled, $visibility, $icon, $room, array $switchPoints = array(), array $allowedUserGroups = array(), array $data = array()) {

        //Ausnahme wenn Elementname schon belegt
        if (!$this->isElementNameAvailable($name)) {

            throw new \Exception('Der Name ist schon vergeben', 1507);
        }

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM, true);

        //Autoincrement
        $nextId = (int) $xml->nextAutoIncrementId;
        $xml->nextAutoIncrementId = $nextId + 1;

        //Datensatz erstellen
        /* @var $switchable \SimpleXmlElement */
        $switchable = $xml->addChild('switchable');
        $switchable->addChild('type', $type);
        $switchable->addChild('id', $nextId);
        $switchable->addChild('name', $name);
        $switchable->addChild('enabled', ($enabled == true ? 1 : 0));
        $switchable->addChild('visibility', ($visibility == true ? 1 : 0));
        $switchable->addChild('state', 0);
        $switchable->addChild('icon', $icon);
        $switchable->addChild('roomId', $room);
        $switchable->addChild('orderId', $nextId);
        $switchable->addChild('switchPoints', implode(',', $switchPoints));
        $switchable->addChild('allowedUserGroups', implode(',', $allowedUserGroups));

        //Daten hinzufuegen
        foreach ($data as $tag => $value) {

            if (!in_array($tag, array('id', 'name', 'type', 'enabled', 'visibility', 'icon', 'roomId', 'orderId', 'switchPoints', 'allowedUserGroups'))) {

                if (is_array($value)) {

                    //wenn Wert ein Array ist Werte als Attibute speichern
                    $newTag = $switchable->addChild($tag);
                    foreach ($value as $attribute => $attributeValue) {

                        $newTag->addAttribute($attribute, $attributeValue);
                    }
                } else {

                    //wenn kein Array als Tag Wert
                    $switchable->addChild($tag, $value);
                }
            }
        }

        //Daten Speichern
        $xml->save();
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
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @param  Array   $data              Zusatzdaten
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    protected function editElement($id, $name, $enabled, $visibility, $icon, $room, array $switchPoints = array(), array $allowedUserGroups = array(), array $data = array()) {

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES, true);

        //Server Suchen
        foreach ($xml->switchable as $switchable) {

            /* @var $switchable \SimpleXmlElement */
            if ((int) $switchable->id == $id) {

                //Name
                if ($name !== null) {

                    //Ausnahme wenn Name der Bedingung schon belegt
                    if (!$this->isElementNameAvailable($name)) {

                        throw new \Exception('Der Name ist schon vergeben', 1507);
                    }

                    $switchable->name = $name;
                }

                //Aktiv
                if ($enabled !== null) {

                    $switchable->enabled = ($enabled == true ? 1 : 0);
                }

                //Sichtbarkeit
                if ($visibility !== null) {

                    $switchable->visibility = ($visibility == true ? 1 : 0);
                }

                //Icon
                if ($icon !== null) {

                    $switchable->icon = $icon;
                }

                //Raum
                if ($room !== null) {

                    $switchable->roomId = $room;
                }

                //Schaltpunkte
                if ($switchPoints !== null) {

                    $switchable->switchPoints = implode(',', $switchPoints);
                }

                //erlaubte Benutzergruppen
                if ($allowedUserGroups !== null) {

                    $switchable->allowedUserGroups = implode(',', $allowedUserGroups);
                }

                //Zusatzdaten
                foreach ($data as $tag => $value) {

                    if (!in_array($tag, array('id', 'name', 'type', 'enabled', 'visibility', 'icon', 'roomId', 'orderId', 'switchPoints', 'allowedUserGroups'))) {

                        if (is_array($value)) {

                            //wenn Wert ein Array ist Werte als Attibute speichern
                            $attr = $switchable->$tag->attributes();
                            foreach ($value as $attribute => $attributeValue) {

                                if (isset($attr->$attribute)) {

                                    $attr->$attribute = $attributeValue;
                                }
                            }
                        } elseif ($value !== null) {

                            //wenn kein Array als Tag Wert
                            $switchable->addChild($tag, $value);
                        }
                    }
                }

                //Daten Speichern
                $xml->save();
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
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addActivity($name, $enabled, $visibility, $icon, $room, array $switchPoints = array(), array $allowedUserGroups = array()) {

        //Datensatz erstellen
        return $this->addElement(self::TYPE_ACTIVITY, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups);
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
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editAcrivity($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, array $switchPoints = null, array $allowedUserGroups = null) {

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups);
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

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES, true);

        //Aktivitaet Suchen
        foreach ($xml->switchable as $switchable) {

            /* @var $switchable \SimpleXmlElement */
            if ((int) $switchable->id == $activityId) {

                //Neues ELement erstellen
                $tag = $switchable->addChild('switchable');
                $tag->addAttribute('id', $switchableId);
                $tag->addAttribute('command', $command);

                //Daten Speichern
                $xml->save();
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

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES, true);

        //Aktivitaet Suchen
        foreach ($xml->switchable as $switchable) {

            /* @var $switchable \SimpleXmlElement */
            if ((int) $switchable->id == $activityId) {

                //Nach Element suchen
                foreach ($switchable->switchable as $activitySwitchable) {

                    $attr = $activitySwitchable->attributes();
                    if ((int) $attr->id == $switchableId) {

                        $attr->command = $command;

                        //Daten Speichern
                        $xml->save();
                        return true;
                    }
                }
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

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES, true);

        //Aktivitaet suchen
        for ($i = 0; $i < count($xml->switchable); $i++) {

            if ((int) $xml->switchable[$i]->id == $activityId) {

                //Element suchen
                for ($j = 0; $j < count($xml->switchable[$i]->switchable); $j++) {

                    $attr = $xml->switchable[$i]->switchable[$j]->attributes();
                    if ((int) $attr->id == $switchableId) {

                        //Element loeschen
                        unset($xml->switchable[$i]->switchable[$j]);

                        //Daten Speichern
                        $xml->save();
                        return true;
                    }
                }
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

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES, true);

        //Aktivitaet Suchen
        foreach ($xml->switchable as $switchable) {

            /* @var $switchable \SimpleXmlElement */
            if ((int) $switchable->id == $countdownId) {

                $switchable->switchOffTime = $time->getDatabaseDateTime();

                //Daten Speichern
                $xml->save();
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
     * @param  Integer $room              Raum ID
     * @param  String  $intervall         Zeitintervall
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addCountdown($name, $enabled, $visibility, $icon, $room, $intervall, array $switchPoints = array(), array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'intervall' => $intervall
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_COUNTDOWN, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
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
     * @param  String  $intervall         Zeitintervall
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editCountdown($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $intervall = null, array $switchPoints = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'intervall' => $intervall
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
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
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES, true);

        //Aktivitaet Suchen
        foreach ($xml->switchable as $switchable) {

            /* @var $switchable \SimpleXmlElement */
            if ((int) $switchable->id == $countdownId) {

                //Neues ELement erstellen
                $tag = $switchable->addChild('switchable');
                $tag->addAttribute('id', $switchableId);
                $tag->addAttribute('command', $command);

                //Daten Speichern
                $xml->save();
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
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES, true);

        //Aktivitaet Suchen
        foreach ($xml->switchable as $switchable) {

            /* @var $switchable \SimpleXmlElement */
            if ((int) $switchable->id == $countdownId) {

                //Nach Element suchen
                foreach ($switchable->switchable as $countdownSwitchable) {

                    $attr = $countdownSwitchable->attributes();
                    if ((int) $attr->id == $switchableId) {

                        $attr->command = $command;

                        //Daten Speichern
                        $xml->save();
                        return true;
                    }
                }
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
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES, true);

        //Aktivitaet suchen
        for ($i = 0; $i < count($xml->switchable); $i++) {

            if ((int) $xml->switchable[$i]->id == $countdownId) {

                //Element suchen
                for ($j = 0; $j < count($xml->switchable[$i]->switchable); $j++) {

                    $attr = $xml->switchable[$i]->switchable[$j]->attributes();
                    if ((int) $attr->id == $switchableId) {

                        //Element loeschen
                        unset($xml->switchable[$i]->switchable[$j]);

                        //Daten Speichern
                        $xml->save();
                        return true;
                    }
                }
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
     * @param  String  $protocol          Protokoll
     * @param  String  $systemCode        System Code
     * @param  String  $deviceCode        Geraete Code
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRadioSocket($name, $enabled, $visibility, $icon, $room, $protocol, $systemCode, $deviceCode, array $switchPoints = array(), array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'protocol' => $protocol,
            'systemCode' => $systemCode,
            'deviceCode' => $deviceCode
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_RADIOSOCKET, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
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
     * @param  String  $protocol          Protokoll
     * @param  String  $systemCode        System Code
     * @param  String  $deviceCode        Geraete Code
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRadioSocket($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $protocol = null, $systemCode = null, $deviceCode = null, array $switchPoints = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'protocol' => $protocol,
            'systemCode' => $systemCode,
            'deviceCode' => $deviceCode
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * erstellt einen neuen Raspberry Pi GPIO Ausgang
     * 
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRriGpioOutput($name, $enabled, $visibility, $icon, $room, $switchServerId, $pinNumber, array $switchPoints = array(), array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_RPI_GPIO_OUTPUT, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
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
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRpiGpioOutput($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $switchServerId = null, $pinNumber = null, array $switchPoints = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * erstellt einen neuen Wake On Lan
     * 
     * @param  String  $name              Name
     * @param  Boolean $enabled           Aktiv
     * @param  Boolean $visibility        Sichtbarkeit
     * @param  String  $icon              Icon
     * @param  Integer $room              Raum ID
     * @param  String  $mac               Schaltserver ID
     * @param  String  $ipAddress         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addWakeOnLan($name, $enabled, $visibility, $icon, $room, $mac, $ipAddress, array $switchPoints = array(), array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'mac' => $mac,
            'ipAddress' => $ipAddress
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_WAKEONLAN, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
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
     * @param  String  $mac               Schaltserver ID
     * @param  String  $ipAddress         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editWakeOnLan($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $mac = null, $ipAddress = null, array $switchPoints = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'mac' => $mac,
            'ipAddress' => $ipAddress
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
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
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRriGpioInput($name, $enabled, $visibility, $icon, $room, $switchServerId, $pinNumber, array $switchPoints = array(), array $allowedUserGroups = array()) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber
        );

        //Datensatz erstellen
        return $this->addElement(self::TYPE_RPI_GPIO_OUTPUT, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
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
     * @param  Integer $switchServerId    Schaltserver ID
     * @param  Integer $pinNumber         Pin Nummer
     * @param  Array   $switchPoints      Liste der Schaltpunkte
     * @param  Array   $allowedUserGroups Liste erlaubter Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRpiGpioInput($id, $name = null, $enabled = null, $visibility = null, $icon = null, $room = null, $switchServerId = null, $pinNumber = null, array $switchPoints = null, array $allowedUserGroups = null) {
        
        //Daten Vorbereiten
        $data = array(
            'switchServer' => $switchServerId,
            'pinNumber' => $pinNumber
        );

        //Datensatz bearbeiten
        return $this->editElement($id, $name, $enabled, $visibility, $icon, $room, $switchPoints, $allowedUserGroups, $data);
    }

    /**
     * loascht ein Schaltbares Element
     * 
     * @param  Integer $id ID
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function removeSwitchable($id) {
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHABLES, true);

        //Element suchen
        for ($i = 0; $i < count($xml->switchable); $i++) {

            if ((int) $xml->switchable[$i]->id == $id) {

                //Element loeschen
                unset($xml->switchable[$i]);

                //Daten Speichern
                $xml->save();
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
