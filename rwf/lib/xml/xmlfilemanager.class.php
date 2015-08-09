<?php

namespace RWF\XML;

//Imports
use RWF\Util\FileUtil;

/**
 * XML Dateiverwaltung
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class XmlFileManager {

    /**
     * Versionen XML Datei
     * 
     * @var String
     */
    const XML_Version = 'version';

    /**
     * Benutzer XML Datei
     * 
     * @var String
     */
    const XML_USERS = 'users';
    
    /**
     * Einstellungen XML Datei
     * 
     * @var String
     */
    const XML_SETTINGS = 'settings';

    /**
     * Liste mit den bekannten XML Dateien
     * 
     * @var Array
     */
    protected $xmlFileList = array();

    /**
     * Liste mit den bereits geladenen XML Objekten
     * 
     * @var mixed
     */
    protected $xmlObjects = array();

    /**
     * Singleton Instanz
     * 
     * @var \RWF\XML\XmlFileManager
     */
    protected static $instance = null;

    /**
     * meldet eine XML Datei am Manager an
     * 
     * @param  String $name Name unter dem die XML Datei behandelt wird
     * @param  String $file Dateiname
     * @return Boolean
     */
    public function registerXmlFile($name, $file, $defaultFile = '') {

        if (!isset($this->xmlFileList[$name])) {

            $this->xmlFileList[$name] = array('file' => $file, 'default' => $defaultFile);
            return true;
        }
        return false;
    }

    /**
     * gibt eine Liste mit allen registrierten XML Dateien zurueck
     * 
     * @return Array
     */
    public function listKnownXmlFiles() {

        return $this->xmlFileList;
    }

    /**
     * gibt das XML Editor Objekt der jeweiligen Datei zurueck
     * 
     * @param  String  $name           Name unter dem die XML Datei behandelt wird
     * @param  Boolean $alwysNewObject immer neues Objekt erzeugen
     * @return \RWF\XML\XmlEditor
     */
    public function getXmlObject($name, $alwysNewObject = false) {

        //immer neues Objekt erzeugen
        if ($alwysNewObject == true) {

            //pruefen ob XML Datei bekannt
            if (isset($this->xmlFileList[$name])) {

                //pruefen ob die XML Datei existiert und falls nicht versuchen die default Datei zu kopieren und laden
                if (!is_file($this->xmlFileList[$name]['file']) && $this->xmlFileList[$name]['default'] != '') {

                    FileUtil::copyFile($this->xmlFileList[$name]['default'], $this->xmlFileList[$name]['file']);
                }
                return XmlEditor::createFromFile($this->xmlFileList[$name]['file']);
            }
            return null;
        }

        //Puefen ob Objekt schon erstellt
        if (!isset($this->xmlObjects[$name])) {

            //pruefen ob XML Datei bekannt
            if (isset($this->xmlFileList[$name])) {

                //pruefen ob die XML Datei existiert und falls nicht versuchen die default Datei zu kopieren und laden
                if (!is_file($this->xmlFileList[$name]['file']) && $this->xmlFileList[$name]['default'] != '') {

                    FileUtil::copyFile($this->xmlFileList[$name]['default'], $this->xmlFileList[$name]['file']);
                }
                //XML Objekt erzeugen
                return $this->xmlObjects[$name] = XmlEditor::createFromFile($this->xmlFileList[$name]['file']);
            } else {

                //XML Datei nicht registriert
                return null;
            }
        }
        return $this->xmlObjects[$name];
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {
        
    }

    /**
     * gibt die Instanz des XmlFileManagers zurueck
     * 
     * @return \RWF\XML\XmlFileManager
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new XmlFileManager();
        }
        return self::$instance;
    }

}
