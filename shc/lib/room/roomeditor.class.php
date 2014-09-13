<?php

namespace SHC\Room;

//Imports
use SHC\Core\SHC;
use RWF\XML\XmlFileManager;
use RWF\Util\String;

/**
 * Raumverwaltung
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RoomEditor {

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
     * Liste mit allen Raeumen
     * 
     * @var Array 
     */
    protected $rooms = array();

    /**
     * Singleton Instanz
     * 
     * @var \SHC\Room\RoomEditor
     */
    protected static $instance = null;

    public function __construct() {

        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM);

        //Daten einlesen
        foreach ($xml->room as $room) {

            $this->rooms[(int) $room->id] = new Room(
                    (int) $room->id, (string) $room->name, (int) $room->orderId, ((int) $room->enabled == 1 ? true : false), explode(',', $room->allowedUserGroups)
            );
        }
    }

    /**
     * gibt den Raum mit der ID zurueck
     * 
     * @param  Integer $id ID
     * @return \SHC\Room\Room
     */
    public function getRoomById($id) {

        if (isset($this->rooms[$id])) {

            return $this->rooms[$id];
        }
        return null;
    }

    /**
     * Prueft ob der Name des Raumes schon verwendet wird
     * 
     * @param  String  $name Name
     * @return Boolean
     */
    public function isRoomNameAvailable($name) {

        foreach ($this->rooms as $room) {

            /* @var $room \SHC\Room\Room */
            if (String::toLower($room->getName()) == String::toLower($name)) {

                return false;
            }
        }
        return true;
    }

    /**
     * gibt eine Liste mir allen Raeumen zurueck
     * 
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren, 
     *      orderId => nach Sortierungs ID,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listRooms($orderBy = 'orderId') {

        if ($orderBy == 'id') {

            //Raeume nach ID sortieren
            $rooms = $this->rooms;
            ksort($rooms, SORT_NUMERIC);
            return $rooms;
        } elseif ($orderBy == 'orderId') {

            //Raeume nach Sortierungs ID sortieren
            $rooms = array();
            foreach ($this->rooms as $room) {

                /* @var $room \SHC\Room\Room */
                $rooms[$room->getOrderId()] = $room;
            }

            ksort($rooms, SORT_NUMERIC);
            return $rooms;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            $rooms = $this->rooms;

            //Sortierfunktion
            $orderFunction = function($a, $b) {
                
                if($a->getName() == $b->getName()) {
                    
                    return 0;
                }
                
                if($a->getName() < $b->getName()) {
                    
                    return -1;
                }
                return 1;
            };
            usort($rooms, $orderFunction);
            return $rooms;
        }
        return $this->rooms;
    }

    /**
     * bearbeitet die SOrtierung der Raeume
     * 
     * @param  Array   $order Array mit Raum ID als Index und Sortierungs ID als Wert
     * @return Boolean
     */
    public function editRoomOrder(array $order) {

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM, true);

        //Raeume durchlaufen und deren Sortierungs ID anpassen
        foreach ($xml->room as $room) {

            if (isset($order[(int) $room->id])) {

                $room->orderId = $order[(int) $room->id];
            }
        }

        //Daten Speichern
        $xml->save();
        return true;
    }

    /**
     * erstellt einen neuen Raum
     * 
     * @param  String  $name              Name des Raumes
     * @param  Boolean $enabled           Aktiviert
     * @param  Array   $allowedUserGroups Erlaubte Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addRoom($name, $enabled = true, array $allowedUserGroups = array()) {

        //Ausnahme wenn Raumname schon belegt
        if (!$this->isRoomNameAvailable($name)) {

            throw new \Exception('Der Raumname ist schon vergeben', 1500);
        }

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM, true);

        //Autoincrement
        $nextId = (int) $xml->nextAutoIncrementId;
        $xml->nextAutoIncrementId = $nextId + 1;

        //Datensatz erstellen
        $room = $xml->addChild('room');
        $room->addChild('id', $nextId);
        $room->addChild('name', $name);
        $room->addChild('orderId', $nextId);
        $room->addChild('enabled', ($enabled == true ? 1 : 0));
        $room->addChild('allowedUserGroups', implode(',', $allowedUserGroups));

        //Daten Speichern
        $xml->save();
        return true;
    }

    /**
     * bearbeitet einen Raum
     * 
     * @param  Integer $id                ID
     * @param  String  $name              Name des Raumes
     * @param  Boolean $enabled           Aktiviert
     * @param  Array   $allowedUserGroups Erlaubte Benutzergruppen
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editRoom($id, $name = null, $enabled = null, array $allowedUserGroups = null) {

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM, true);

        //Raum Suchen
        foreach ($xml->room as $room) {

            if ((int) $room->id == $id) {

                //Name
                if ($name !== null) {

                    //Ausnahme wenn Raumname schon belegt
                    if (!$this->isRoomNameAvailable($name)) {

                        throw new \Exception('Der Raumname ist schon vergeben', 1500);
                    }

                    $room->name = $name;
                }

                //Aktiviert
                if ($enabled !== null) {

                    $room->enabled = ($enabled == true ? 1 : 0);
                }

                //erlaubte Benutzergruppen
                if ($allowedUserGroups !== null) {

                    $room->allowedUserGroups = implode(',', $allowedUserGroups);
                }

                //Daten Speichern
                $xml->save();
                return true;
            }
        }
        return false;
    }

    /**
     * loescht einen Raum
     * 
     * @param  Integer $id ID
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function removeRoom($id) {
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_ROOM, true);
        
        //Raum suchen
        for($i = 0; $i < count($xml->room); $i++) {
            
            if((int) $xml->room[$i]->id == $id) {
                
                //Raum loeschen
                unset($xml->room[$i]);

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
     * gibt den Raum Editor zurueck
     * 
     * @return \SHC\Room\RoomEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new RoomEditor();
        }
        return self::$instance;
    }

}
