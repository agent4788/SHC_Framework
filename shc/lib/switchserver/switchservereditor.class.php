<?php

namespace SHC\SwitchServer;

//Imports
use SHC\Core\SHC;
use RWF\XML\XmlFileManager;

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
    
    protected function __construct() {
        
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHSERVER);

        //Daten einlesen
        foreach ($xml->switchserver as $switchserver) {

            $this->switchServers[(int) $switchserver->id] = new SwitchServer(
                    (int) $switchserver->id, 
                    (string) $switchserver->name, 
                    (string) $switchserver->address, 
                    (int) $switchserver->port, 
                    (int) $switchserver->model,
                    (int) $switchserver->radioSockets, 
                    (int) $switchserver->writeGpios, 
                    (int) $switchserver->readGpios, 
                    (int) $switchserver->timeout
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
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function addSwitchServer($name, $address, $port, $timeout, $model, $radioSockets, $writeGpios, $readGpios) {
        
        //Ausnahme wenn Raumname schon belegt
        if (!$this->isSwitchServerNameAvailable($name)) {

            throw new \Exception('Der Servername ist schon vergeben', 1501);
        }

        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHSERVER, true);

        //Autoincrement
        $nextId = (int) $xml->nextAutoIncrementId;
        $xml->nextAutoIncrementId = $nextId + 1;

        //Datensatz erstellen
        $switchServer = $xml->addChild('switchserver');
        $switchServer->addChild('id', $nextId);
        $switchServer->addChild('name', $name);
        $switchServer->addChild('address', $address);
        $switchServer->addChild('port', $port);
        $switchServer->addChild('timeout', $timeout);
        $switchServer->addChild('radioSockets', ($radioSockets == true ? 1 : 0));
        $switchServer->addChild('writeGpios', ($writeGpios == true ? 1 : 0));
        $switchServer->addChild('readGpios', ($readGpios == true ? 1 : 0));

        //Daten Speichern
        $xml->save();
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
     * @return Boolean
     * @throws \Exception, \RWF\Xml\Exception\XmlException
     */
    public function editSwitchServer($id, $name = null, $address = null, $port = null, $timeout = null, $model = null, $radioSockets = null, $writeGpios = null, $readGpios = null) {
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHSERVER, true);

        //Server Suchen
        foreach ($xml->switchserver as $switchServer) {

            if ((int) $switchServer->id == $id) {

                //Name
                if ($name !== null) {

                    //Ausnahme wenn Raumname schon belegt
                    if (!$this->isSwitchServerNameAvailable($name)) {

                        throw new \Exception('Der Servername ist schon vergeben', 1501);
                    }

                    $switchServer->name = $name;
                }

                //IP Adresse
                if ($address !== null) {

                    $switchServer->address = $address;
                }
                
                //Port
                if ($port !== null) {

                    $switchServer->port = $port;
                }

                //Timeout
                if ($timeout !== null) {

                    $switchServer->timeout = $timeout;
                }
                
                //Funksteckdosen schalten
                if ($radioSockets !== null) {

                    $switchServer->radioSockets = ($radioSockets == true ? 1 : 0);
                }
                
                //GPIOs schalten
                if ($writeGpios !== null) {

                    $switchServer->writeGpios = ($writeGpios == true ? 1 : 0);
                }
                
                //GPIOs abfragen
                if ($readGpios !== null) {

                    $switchServer->readGpios = ($readGpios == true ? 1 : 0);
                }

                //Daten Speichern
                $xml->save();
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
        
        //XML Daten Laden
        $xml = XmlFileManager::getInstance()->getXmlObject(SHC::XML_SWITCHSERVER, true);
        
        //Server suchen
        for($i = 0; $i < count($xml->switchserver); $i++) {
            
            if((int) $xml->switchserver[$i]->id == $id) {
                
                //Raum loeschen
                unset($xml->switchserver[$i]);

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
     * @return \SHC\SwitchServer\SwitchServerEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new SwitchServerEditor();
        }
        return self::$instance;
    }
}
