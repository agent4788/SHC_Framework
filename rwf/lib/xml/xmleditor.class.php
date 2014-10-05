<?php

namespace RWF\XML;

//Imports
use RWF\XML\Exception\XmlException;

/**
 * Wrapper fuer SimpleXmlElement mit erweiterten Funktionen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class XmlEditor extends \SimpleXMLElement {

    /**
     * Dateiname der XML Datei
     * 
     * @var type 
     */
    protected $fileName = '';

    /**
     * gibt an ob die XML Libary Initialisiert wurde
     * 
     * @var Boolean
     */
    protected static $init = false;

    /**
     * initalisiert die XML Libary
     */
    protected static function initLibXml() {

        if (self::$init === false) {

            \libxml_use_internal_errors(true);
        }
        self::$init = true;
    }

    /**
     * erzeugt einen XML Editor aus einer Zeichenkette
     * 
     * @param  String $xmlString Zeichenkette mit den XML Daten
     * @return \RWF\XML\XmlEditor
     */
    public static function createFromString($xmlString) {

        //initialisieren
        self::initLibXml();

        //Objekt erzeugen
        try {

            return new XmlEditor($xmlString);
        } catch (\Exception $e) {

            throw new XmlException('Die XML Datei konnte nicht geladen werden', 1100, \libxml_get_errors());
        }
    }

    /**
     * erzeugt einen XML Editor aus einer Datei
     * 
     * @param  String $file     Dateiname der XML Datei
     * @return \RWF\XML\XmlEditor
     */
    public static function createFromFile($file) {

        //initialisieren
        self::initLibXml();

        //pruefen ob die Datei existiert
        if (!is_file($file)) {

            throw new XmlException('Die XML Datei "' . $file . '" konnte nicht gefunden werden', 1101);
        }

        //Objekt erzeugen
        try {

            $xml = new XmlEditor(file_get_contents($file));
            $xml->setFileName($file);
            return $xml;
        } catch (\Exception $e) {

            throw new XmlException('Die XML Datei konnte nicht geladen werden', 1100, \libxml_get_errors());
        }
    }

    /**
     * erstellt einen XML Editor
     * 
     * @param  String $xml Dateiname oder XML Zeichenkette
     * @return \RWF\XML\XmlEditor
     */
    public static function create($xml) {

        if (is_file($xml)) {

            return self::createFromFile($xml);
        }
        return self::createFromString($xml);
    }

    /**
     * prueft ob eine XML Datei konsistent ist
     * 
     * @param  String  $file Dateiname oder XML Zeichenkette
     * @return Boolean
     */
    public static function isConsistent($file) {

        //Versuch die Datei zu laden
        try {

            self::create($file);
            return true;
        } catch (XmlException $e) {

            return false;
        }
    }

    /**
     * setzt den Dateinamen der XML Datei
     * 
     * @param  String $fileName Dateiname
     * @return \RWF\XML\XmlEditor
     */
    public function setFileName($fileName) {

        $this->fileName = $fileName;
        return $this;
    }

    /**
     * gibt den Dateinamen der XML Datei zurueck
     * 
     * @return String
     */
    public function getFileName() {

        return $this->fileName;
    }

    /**
     * speichert die XML Datei in der in FileName angegebenen Datei
     * 
     * @param  Boolean $createReloadFlag Reload Flag erzeugen
     * @throws XmlException
     */
    public function save($createReloadFlag = false) {

        //pruefen ob Dateiname nicht leer
        if ($this->fileName != '') {

            //Schreibrechte pruefen
            if (!is_writeable($this->fileName)) {

                throw new XmlException('Die XML Datei kann wegen fehlenden schreibrechten nicht gespeichert werden', 1102);
            }

            //versuchen die Datei zu speichern
            $fileName = $this->fileName;
            unset($this->fileName);
            if (!$this->asXML($fileName)) {

                throw new XmlException('Die XML Datei konntenicht gespeichert werden', 1103, \libxml_get_errors());
            }
            $this->fileName = $fileName;
        }

        //Reload Flag erzeugen um andere Prozesse ueber aenderungen der XML Daten zu informieren
        if ($createReloadFlag == true) {

            file_put_contents(PATH_RWF_CACHE . 'reload.flag', '1');
        }
    }

}
