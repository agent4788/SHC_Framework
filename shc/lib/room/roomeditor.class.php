<?php

namespace SHC\Room;

//Imports
use RWF\User\UserEditor;
use RWF\User\UserGroup;
use SHC\Core\SHC;
use RWF\Util\String;
use SHC\Database\NoSQL\Redis;

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

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'shc:rooms';

    protected function __construct() {

        $this->loadData();
    }

    /**
     * laedt die XML Daten
     */
    public function loadData() {

        //alte Daten loeschen
        $this->rooms = array();

        $rooms = SHC::getDatabase()->hGetAllArray(self::$tableName);
        foreach($rooms as $room) {

            $id = $room['id'];
            $this->rooms[$id] = new Room(
                $id, (string) $room['name'], (int) $room['orderId'], (bool) $room['enabled']
            );

            foreach($room['allowedUserGroups'] as $allowedGroupId) {

                $group = UserEditor::getInstance()->getUserGroupById((int) $allowedGroupId);
                if($group instanceof UserGroup) {

                    $this->rooms[$id]->addAllowedUserGroup($group);
                }
            }
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
     * bearbeitet die Sortierung der Raeume
     * 
     * @param  Array   $order Array mit Raum ID als Index und Sortierungs ID als Wert
     * @return Boolean
     */
    public function editRoomOrder(array $order) {

        $db = SHC::getDatabase();
        foreach($order as $roomId => $orderId) {

            if(isset($this->rooms[$roomId])) {

                /* @var $room \SHC\Room\Room */
                $room = $this->rooms[$roomId];
                $room->setOrderId($orderId);
                $db->hSetArray(self::$tableName, $roomId, $room->toArray());
            }
        }
        $db->exec();
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

        $db = SHC::getDatabase();
        $index = $db->autoIncrement(self::$tableName);
        $newRoom = array(
            'id' => $index,
            'name' => $name,
            'orderId' => $index,
            'enabled' => ($enabled == true ? true : false),
            'allowedUserGroups' => $allowedUserGroups
        );

        if($db->hSetNxArray(self::$tableName, $index, $newRoom) == 0) {

            return false;
        }
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

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            $room = $db->hGetArray(self::$tableName, $id);

            //Name
            if ($name !== null) {

                //Ausnahme wenn Raumname schon belegt
                if ($room['name'] != $name && !$this->isRoomNameAvailable($name)) {

                    throw new \Exception('Der Raumname ist schon vergeben', 1500);
                }

                $room['name'] = $name;
            }

            //Aktiviert
            if ($enabled !== null) {

                $room['enabled'] = ($enabled == true ? true : false);
            }

            //erlaubte Benutzergruppen
            if ($allowedUserGroups !== null) {

                $room['allowedUserGroups'] = $allowedUserGroups;
            }

            if($db->hSetArray(self::$tableName, $id, $room) == 0) {

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
