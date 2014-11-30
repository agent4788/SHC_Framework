<?php

namespace RWF\Settings;

//Imports
use RWF\XML\XmlFileManager;

/**
 * Einstellungen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Settings {
    
    /**
     * Einstellungen
     * 
     * @var Array 
     */
    protected $settings = array();

    /**
     * XML Objekt
     * 
     * @var \RWF\Xml\XmlEditor 
     */
    protected $xml = null;
    
    /**
     * gibt an ob die XML Datei verändert wurde
     * 
     * @var Boolean
     */
    protected $chanched = false;

    /**
     * gibt an ob die XML Datei schon gespeichert wurde
     * 
     * @var Boolean
     */
    protected $saved = false;
    
    /**
     * intialisiert die Einstellungen
     */
    public function __construct() {
        
        $this->xml = XmlFileManager::getInstance()->getXmlObject(XmlFileManager::XML_SETTINGS);
        $this->readXMLData();
    }

    /**
     * liest die Daten aus der XML Datei
     */
    protected function readXMLData() {

        foreach ($this->xml->setting as $setting) {

            $attributes = $setting->attributes();
            switch ($attributes->type) {

                case 'string':

                    $this->settings[(string) $attributes->name] = rawurldecode((string) $attributes->value);

                    break;
                case 'bool':

                    $this->settings[(string) $attributes->name] = ((string) $attributes->value === 'true' ? true : false);

                    break;
                case 'int':

                    $this->settings[(string) $attributes->name] = (int) $attributes->value;

                    break;
                default:

                    $this->settings[(string) $attributes->name] = (string) $attributes->value;
            }
        }
    }

    /**
     * gibt den Wert einer Einstellung zurueck
     * 
     * @param  String $name Name der Einstellung
     * @return Mixed
     */
    public function getValue($name) {

        if (isset($this->settings[$name])) {

            return $this->settings[$name];
        }

        return null;
    }
    
    /**
     * verweis auf die getValue Methode
     * 
     * @param  String $name Name der Einstellung
     * @return Mixed
     */
    public function __get($name) {
        
        return $this->getValue($name);
    }

    /**
     * laedt die Einstellungen neu
     */
    public function reloadSettings() {

        $this->readXMLData();
    }

    /**
     * Speichert einen neuen Wert fuer die Einstellung (es kann nur der Wert geaendert werden)
     * 
     * @param  String  $setting Einstellung
     * @param  Mixed   $value   Wert
     * @return Boolean
     * @throws Exception
     */
    public function editSetting($settingName, $value) {

        foreach ($this->xml->setting as $setting) {

            $attributes = $setting->attributes();

            if ($attributes->name == $settingName) {

                switch ($attributes->type) {

                    case 'string':

                        $attributes->value = rawurlencode($value);

                        $this->chanched = true;
                        $this->saved = false;
                        return true;
                    case 'bool':

                        if ($value === true || $value === false || $value === 1 || $value === 0 || $value === '1' || $value === '0') {

                            $attributes->value = (($value === true || $value === 1 || $value === '1') ? 'true' : 'false');
                        } else {

                            throw new \Exception('Ungültiger Wert', 1120);
                        }

                        $this->chanched = true;
                        $this->saved = false;
                        return true;
                    case 'int':

                        if ((int) $value == $value) {

                            $attributes->value = (int) $value;
                        } else {

                            throw new \Exception('Ungültiger Wert', 1120);
                        }

                        $this->chanched = true;
                        $this->saved = false;
                        return true;
                    case 'float':

                        if ((float) $value == $value) {

                            $attributes->value = (float) $value;
                        } else {

                            throw new \Exception('Ungültiger Wert', 1120);
                        }

                        $this->chanched = true;
                        $this->saved = false;
                        return true;
                }
            }
        }

        return false;
    }

    /**
     * speichert alle Einstellungen und laedt sie Neu
     * 
     * @return Boolean
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function saveAndReload() {

        if ($this->xml->save(true)) {

            $this->readXMLData();
            $this->chanched = false;
            $this->saved = true;
            return true;
        }
        return false;
    }

    /**
     * speichert die XML Datei wenn Einstellungen verändert wurden
     * 
     * @throws \RWF\Xml\Exception\XmlException
     */
    public function finalize() {

        if ($this->chanched === true && $this->saved === false) {

            if ($this->xml->save(true)) {

                $this->saved = true;
            }
        }
    }
}
