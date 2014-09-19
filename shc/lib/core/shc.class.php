<?php

namespace SHC\Core;

//Imports
use RWF\Core\RWF;
use RWF\XML\XmlFileManager;

/**
 * Kernklasse (initialisiert das SHC)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SHC extends RWF {
    
    /**
     * Raeume XML Datei
     * 
     * @var String
     */
    const XML_ROOM = 'rooms';
    
    /**
     * Schaltserver XML Datei
     * 
     * @var String
     */
    const XML_SWITCHSERVER = 'switchserver';
    
    /**
     * Bedingungen XML
     * 
     * @var String
     */
    const XML_CONDITIONS = 'conditions';
    
    public function __construct() {
        
        //XML Initialisieren
        $this->initXml();
        
        //Basisklasse initalisieren
        parent::__construct();
        
        //Template Ordner anmelden
        if (ACCESS_METHOD_HTTP) {
            
            self::$template->addTemplateDir(PATH_SHC .'data/templates');
        }
    }
    
    protected function initXml() {
        
        $fileManager = XmlFileManager::getInstance();
        $fileManager->registerXmlFile(self::XML_ROOM, PATH_SHC_STORAGE . 'rooms.xml', PATH_SHC_STORAGE . 'default/defaultRooms.xml');
        $fileManager->registerXmlFile(self::XML_SWITCHSERVER, PATH_SHC_STORAGE . 'switchserver.xml', PATH_SHC_STORAGE . 'default/defaultSwitchserver.xml');
        $fileManager->registerXmlFile(self::XML_CONDITIONS, PATH_SHC_STORAGE . 'conditions.xml', PATH_SHC_STORAGE . 'default/defaultConditions.xml');
    }
}
