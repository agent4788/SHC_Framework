<?php

namespace SHC\SwitchServer;

//Imports
use RWF\Util\String;
use SHC\Core\SHC;


/**
 * Verwaltung der Schaltserver
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchServerEditor {
    
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
     * nicht sortieren
     * 
     * @var String
     */
    const SORT_NOTHING = 'unsorted';
    
    /**
     * liste mit allen Registrierten Schaltservern
     * 
     * @var Array
     */
    protected $switchServers = array();
    
    /**
     * Singleton Instanz
     * 
     * @var \SHC\SwitchServer\SwitchServerEditor
     */
    protected static $instance = null;

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'shc:switchServers';
    
    protected function __construct() {

        $switchServers = SHC::getDatabase()->hGetAllArray(self::$tableName);
        foreach ($switchServers as $switchserver) {

            $this->switchServers[(int) $switchserver['id']] = new SwitchServer(
                    (int) $switchserver['id'],
                    (string) $switchserver['name'],
                    (string) $switchserver['address'],
                    (int) $switchserver['port'],
                    (int) $switchserver['model'],
                    (int) $switchserver['radioSockets'],
                    (int) $switchserver['writeGpios'],
                    (int) $switchserver['readGpios'],
                    (int) $switchserver['timeout'],
                    (int) $switchserver['enabled']
            );
        }
    }
    
    /**
     * gibt den Schalserver mit der ID zurueck
     * 
     * @param  Integer $id ID
     * @return \SHC\SwitchServer\SwitchServer
     */
    public function getSwitchServerById($id) {
        
        if(isset($this->switchServers[$id])) {
            
            return $this->switchServers[$id];
        }
        return null;
    }
    
    /**
     * prueft ob der Name des Schaltservers schon verwendet wird
     * 
     * @param  String  $name Name
     * @return Boolean
     */
    public function isSwitchServerNameAvailable($name) {
        
        foreach ($this->switchServers as $switchServer) {

            /* @var $switchServer \SHC\SwitchServer\SwitchServer */
            if (String::toLower($switchServer->getName()) == String::toLower($name)) {

                return false;
            }
        }
        return true;
    }
    
    /**
     * gibt eine Liste mir allen Schaltservern zurueck
     * 
     * @param  String $orderBy Art der Sortierung (
     *      id => nach ID sorieren, 
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listSwitchServers($orderBy = 'id') {
        
        if ($orderBy == 'id') {

            //nach ID sortieren
            $switchServers = $this->switchServers;
            ksort($switchServers, SORT_NUMERIC);
            return $switchServers;
        } elseif ($orderBy == 'name') {

            //nach Namen sortieren
            $switchServers = $this->switchServers;

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
            usort($switchServers, $orderFunction);
            return $switchServers;
        }
        return $this->switchServers;
    }
    
    /**
     * erstellt einen neuen Schaltserver
     * 
     * @param  String  $name         Name
     * @param  String  $address      IP Adresse
     * @param  Integer $port         Port
     * @param  Integer $timeout      Timeout
     * @param  Integer $model        Model ID
     * @param  Boolean $radioSockets Funksteckdosen schalten
     * @param  Boolean $writeGpios   GPIOs schalten
     * @param  Boolean $readGpios    GPIOs abfragen
     * @param  Boolean $enabled      Aktviert
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addSwitchServer($name, $address, $port, $timeout, $model, $radioSockets, $writeGpios, $readGpios, $enabled) {
        
        //Ausnahme wenn Raumname schon belegt
        if (!$this->isSwitchServerNameAvailable($name)) {

            throw new \Exception('Der Servername ist schon vergeben', 1501);
        }

        $db = SHC::getDatabase();
        $index = $db->autoIncrement(self::$tableName);

        $newSwitchServer = array(
            'id' => $index,
            'name' => $name,
            'address' => $address,
            'port' => $port,
            'timeout' => $timeout,
            'model' => $model,
            'radioSockets' => ($radioSockets == true ? true : false),
            'writeGpios' => ($writeGpios == true ? true : false),
            'readGpios' => ($readGpios == true ? true : false),
            'enabled' => ($enabled == true ? true : false)
        );

        if($db->hSetNxArray(self::$tableName, $index, $newSwitchServer) == 0) {

            return false;
        }
        return true;
    }
    
    /**
     * bearbeitet einen Schaltserver
     * 
     * @param  Integer $id           ID
     * @param  String  $name         Name
     * @param  String  $address      IP Adresse
     * @param  Integer $port         Port
     * @param  Integer $timeout      Timeout
     * @param  Integer $model        Model ID
     * @param  Boolean $radioSockets Funksteckdosen schalten
     * @param  Boolean $writeGpios   GPIOs schalten
     * @param  Boolean $readGpios    GPIOs abfragen
     * @param  Boolean $enabled      Aktviert
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editSwitchServer($id, $name = null, $address = null, $port = null, $timeout = null, $model = null, $radioSockets = null, $writeGpios = null, $readGpios = null, $enabled = null) {

        $db = SHC::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $id)) {

            $switchServer = $db->hGetArray(self::$tableName, $id);

            //Name
            if ($name !== null) {

                //Ausnahme wenn Raumname schon belegt
                if ((string) $switchServer['name'] != $name && !$this->isSwitchServerNameAvailable($name)) {

                    throw new \Exception('Der Servername ist schon vergeben', 1501);
                }

                $switchServer['name'] = $name;
            }

            //IP Adresse
            if ($address !== null) {

                $switchServer['address'] = $address;
            }

            //Port
            if ($port !== null) {

                $switchServer['port'] = $port;
            }

            //Timeout
            if ($timeout !== null) {

                $switchServer['timeout'] = $timeout;
            }

            //Model
            if ($model !== null) {

                $switchServer['model'] = $model;
            }

            //Funksteckdosen schalten
            if ($radioSockets !== null) {

                $switchServer['radioSockets'] = ($radioSockets == true ? true : false);
            }

            //GPIOs schalten
            if ($writeGpios !== null) {

                $switchServer['writeGpios'] = ($writeGpios == true ? true : false);
            }

            //GPIOs abfragen
            if ($readGpios !== null) {

                $switchServer['readGpios'] = ($readGpios == true ? true : false);
            }

            //Aktiviert
            if ($enabled !== null) {

                $switchServer['enabled'] = ($enabled == true ? true : false);
            }

            if($db->hSetArray(self::$tableName, $id, $switchServer) == 0) {

                return true;
            }
        }
        return false;
    }
    
    /**
     * loascht einen Schaltserver
     * 
     * @param  Integer $id ID
     * @return Boolean
     */
    public function removeSwitchServer($id) {

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
     * @return \SHC\SwitchServer\SwitchServerEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new SwitchServerEditor();
        }
        return self::$instance;
    }
}
