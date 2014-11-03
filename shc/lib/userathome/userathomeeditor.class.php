<?php

namespace SHC\UserAtHome;

//Imports
use RWF\Util\String;
use SHC\Core\SHC;
use RWF\XML\XmlFileManager;

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
    
    protected function __construct() {

        $this->loadData();
    }

    /**
     * Daten laden
     */
    public function loadData() {
        
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_USERS_AT_HOME, true);
        
        //Daten einlesen
        foreach($xml->user as $usersAtHome) {
            
            $this->usersAtHome[(int) $usersAtHome->id] = new UserAtHome(
                    (int) $usersAtHome->id,
                    (string) $usersAtHome->name,
                    (string) $usersAtHome->ipAddress,
                    (int) $usersAtHome->orderId,
                    ((int) $usersAtHome->enabled == 1 ? true : false),
                    (int) $usersAtHome->visibility,
                    (int) $usersAtHome->state
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

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_USERS_AT_HOME, true);

        //Benutzer durchlaufen und deren Sortierungs ID anpassen
        foreach ($xml->user as $userAtHome) {

            if (isset($order[(int) $userAtHome->id])) {

                $userAtHome->orderId = $order[(int) $userAtHome->id];
            }
        }

        //Daten Speichern
        $xml->save();
        return true;
    }
    
    /**
     * speichert den Status aller Benutzer die veraendert wurden
     * 
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function updateState() {
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_USERS_AT_HOME, true);
        
        //Alle Elemente durchlaufen
        foreach($this->usersAtHome as $userAtHome) {
            
            //Wenn der Status veraendert wurde Speichern
            if($userAtHome->isStateModified()) {
                
                //Nach Objekt suchen
                $id = $userAtHome->getId();
                foreach($xml->user as $xmlUserAtHome) {
                    
                    if((int) $xmlUserAtHome->id == $id) {
                        
                        $xmlUserAtHome->state = $userAtHome->getState();
                    }
                }
            }
        }
        
        //Daten Speichern
        $xml->save();
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
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_USERS_AT_HOME, true);

        //Autoincrement
        $nextId = (int) $xml->nextAutoIncrementId;
        $xml->nextAutoIncrementId = $nextId + 1;
        
        //Datensatz erstellen
        /* @var $user \SimpleXmlElement */
        $user = $xml->addChild('user');
        $user->addChild('id', $nextId);
        $user->addChild('name', $name);
        $user->addChild('ipAddress', $ipAddress);
        $user->addChild('orderId', $nextId);
        $user->addChild('enabled', ($enabled == true ? 1 : 0));
        $user->addChild('visibility', ($visibility == true ? 1 : 0));
        $user->addChild('state', 0);
        
        //Daten Speichern
        $xml->save();
        return true;
    }
    
    /**
     * bearbeitet einen Benutzer
     * 
     * @param  Integer $id         Benutzer ID
     * @param  String  $name       Name
     * @param  Strung  $ipAddress  IP Adresse
     * @param  Boolean $enabled    Aktiv
     * @param  Boolean $visibility Sichtbarkeit
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editUseratHome($id, $name = null, $ipAddress = null, $enabled = null, $visibility = null) {
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_USERS_AT_HOME, true);

        //Benutzer Suchen
        foreach ($xml->user as $userAtHome) {

            /* @var $userAtHome \SimpleXmlElement */
            if ((int) $userAtHome->id == $id) {

                //Name
                if ($name !== null) {

                    //Ausnahme wenn Name des Benutzers schon belegt
                    if ((string) $userAtHome->name != $name && !$this->isUserAtHomeNameAvailable($name)) {

                        throw new \Exception('Der Name ist schon vergeben', 1507);
                    }

                    $userAtHome->name = $name;
                }

                //IP Adresse
                if ($ipAddress !== null) {

                    $userAtHome->ipAddress = $ipAddress;
                }
                
                //Aktiv
                if ($enabled !== null) {

                    $userAtHome->enabled = ($enabled == true ? 1 : 0);
                }

                //Sichtbarkeit
                if ($visibility !== null) {

                    $userAtHome->visibility = ($visibility == true ? 1 : 0);
                 }

                //Daten Speichern
                $xml->save();
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
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_USERS_AT_HOME, true);

        //Element suchen
        for ($i = 0; $i < count($xml->user); $i++) {

            if ((int) $xml->user[$i]->id == $id) {

                //Element loeschen
                unset($xml->user[$i]);

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
