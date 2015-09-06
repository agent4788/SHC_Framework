<?php

namespace SHC\UserAtHome;

//Imports
use RWF\Util\String;
use SHC\Core\SHC;

/**
 * Benutzer zu Hause Editor
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserAtHomeEditor {
    
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
     * Liste mit allen Benutzern
     * 
     * @var Array 
     */
    protected $usersAtHome = array();
    
    /**
     * Singleton Instanz
     * 
     * @var \SHC\UserAtHome\UserAtHomeEditor
     */
    protected static $instance = null;

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'shc:usersrathome';
    
    protected function __construct() {

        $this->loadData();
    }

    /**
     * Daten laden
     */
    public function loadData() {

        //alte daten loeschen
        $this->usersAtHome = array();

        $usersAtHome = SHC::getDatabase()->hGetAllArray(self::$tableName);
        foreach($usersAtHome as $userAtHome) {
            
            $this->usersAtHome[(int) $userAtHome['id']] = new UserAtHome(
                    (int) $userAtHome['id'],
                    (string) $userAtHome['name'],
                    (string) $userAtHome['ipAddress'],
                    (int) $userAtHome['orderId'],
                    ((int) $userAtHome['enabled'] == true ? true : false),
                    (int) $userAtHome['visibility'],
                    (int) $userAtHome['state']
            );
        }
        
    }
    
    /**
     * gibt den Benutzer mit der ID zurueck
     * 
     * @param  Integer $id ID
     * @return \SHC\UserAtHome\UserAtHome
     */
    public function getUserTaHomeById($id) {
        
        if(isset($this->usersAtHome[$id])) {
            
            return $this->usersAtHome[$id];
        }
        return null;
    }
    
    /**
     * prueft ob der Name des Benutzers schon verwendet wird
     * 
     * @param  String  $name Name
     * @return Boolean
     */
    public function isUserAtHomeNameAvailable($name) {
        
        foreach ($this->usersAtHome as $usersAtHome) {

            /* @var $switchServer \SHC\UserAtHome\UserAtHome */
            if (String::toLower($usersAtHome->getName()) == String::toLower($name)) {

                return false;
            }
        }
        return true;
    }
    
    /**
     * gibt eine Liste mit allen Benutzern zurueck
     * 
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren, 
     *      orderId => nach Sortierungs ID,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listUsersAtHome($orderBy = 'orderId') {

        if ($orderBy == 'id') {

            //nach ID sortieren
            $usersAtHome = $this->usersAtHome;
            ksort($usersAtHome, SORT_NUMERIC);
            return $usersAtHome;
        } elseif ($orderBy == 'orderId') {

            //nach Sortierungs ID sortieren
            $usersAtHome = array();
            foreach ($this->usersAtHome as $userAtHome) {

                /* @var $userAtHome \SHC\UserAtHome\UserAtHome */
                $usersAtHome[$userAtHome->getOrderId()] = $userAtHome;
            }

            ksort($usersAtHome, SORT_NUMERIC);
            return $usersAtHome;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            $usersAtHome = $this->usersAtHome;

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
            usort($usersAtHome, $orderFunction);
            return $usersAtHome;
        }
        return $this->usersAtHome;
    }
    
    /**
     * bearbeitet die Sortierung der Benutzer Elemente
     * 
     * @param  Array   $order Array mit Element ID als Index und Sortierungs ID als Wert
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function editOrder(array $order) {

        $db = SHC::getDatabase();
        foreach($order as $userAtHomeId => $orderId) {

            if(isset($this->usersAtHome[$userAtHomeId])) {

                $userAtHomeData = $db->hGetArray(self::$tableName, $userAtHomeId);

                if(isset($userAtHomeData['id']) && $userAtHomeData['id'] == $userAtHomeId) {

                    $userAtHomeData['orderId'] = $orderId;

                    if($db->hSetArray(self::$tableName, $userAtHomeId, $userAtHomeData) != 0) {

                        return false;
                    }
                } else {

                    //Datensatz nicht mehr vorhanden
                    continue;
                }
            }
        }
        return true;
    }
    
    /**
     * speichert den Status aller Benutzer die veraendert wurden
     * 
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function updateState() {

        $db = SHC::getDatabase();
        foreach($this->usersAtHome as $userAtHome) {

            //Wenn der Status veraendert wurde Speichern
            if($userAtHome->isStateModified()) {

                //Nach Objekt suchen
                $id = $userAtHome->getId();
                $userAtHomeData = $db->hGetArray(self::$tableName, $id);

                if(isset($userAtHomeData['id']) && $userAtHomeData['id'] == $id) {

                    $userAtHomeData['state'] = $userAtHome->getState();

                    if($db->hSetArray(self::$tableName, $id, $userAtHomeData) != 0) {

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
     * erstellt einen neuen Benutzer
     * 
     * @param  String  $name       Name
     * @param  String  $ipAddress  IP Adresse
     * @param  Boolean $enabled    Aktiv
     * @param  Boolean $visibility Sichtbarkeit
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addUserAtHome($name, $ipAddress, $enabled, $visibility) {
        
        //Ausnahme wenn Benutzername schon belegt
        if (!$this->isUserAtHomeNameAvailable($name)) {

            throw new \Exception('Der Name ist schon vergeben', 1507);
        }

        $db = SHC::getDatabase();
        $index = $db->autoIncrement(self::$tableName);

        $newUserAtHome = array(
            'id' => $index,
            'name' => $name,
            'orderId' => $index,
            'enabled' => ($enabled == true ? true : false),
            'ipAddress' => $ipAddress,
            'visibility' => ($visibility == true ? true : false),
            'state' => 0
        );

        if($db->hSetNxArray(self::$tableName, $index, $newUserAtHome) == 0) {

            return false;
        }
        return true;
    }
    
    /**
     * bearbeitet einen Benutzer
     * 
     * @param  Integer $id         Benutzer ID
     * @param  String  $name       Name
     * @param  String  $ipAddress  IP Adresse
     * @param  Boolean $enabled    Aktiv
     * @param  Boolean $visibility Sichtbarkeit
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editUserAtHome($id, $name = null, $ipAddress = null, $enabled = null, $visibility = null) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            $userAtHome = $db->hGetArray(self::$tableName, $id);

            //Name
            if ($name !== null) {

                //Ausnahme wenn Name des Benutzers schon belegt
                if ((string) $userAtHome['name'] != $name && !$this->isUserAtHomeNameAvailable($name)) {

                    throw new \Exception('Der Name ist schon vergeben', 1507);
                }

                $userAtHome['name'] = $name;
            }

            //IP Adresse
            if ($ipAddress !== null) {

                $userAtHome['ipAddress'] = $ipAddress;
            }

            //Aktiv
            if ($enabled !== null) {

                $userAtHome['enabled'] = ($enabled == true ? true : false);
            }

            //Sichtbarkeit
            if ($visibility !== null) {

                $userAtHome['visibility'] = ($visibility == true ? true : false);
            }

            if($db->hSetArray(self::$tableName, $id, $userAtHome) == 0) {

                return true;
            }

        }
        return false;
    }
    
    /**
     * loascht einen Benutzer
     * 
     * @param  Integer $id ID
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function removeUserAtHome($id) {

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
     * gibt den Schaltpunkt Editor zurueck
     * 
     * @return \SHC\UserAtHome\UserAtHomeEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new UserAtHomeEditor();
        }
        return self::$instance;
    }
}
