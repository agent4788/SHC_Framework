<?php

namespace RWF\Settings;

//Imports
use RWF\Core\RWF;

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
     * Typkonstanten
     *
     * @var int
     */
    const TYPE_BOOL = 1;
    const TYPE_INT = 2;
    const TYPE_FLOAT = 3;
    const TYPE_STRING = 4;

    /**
     * Einstellungen
     * 
     * @var Array 
     */
    protected $settings = array();

    /**
     * gibt an ob die EInstellungen geladen wurden
     *
     * @var bool
     */
    protected $init = false;

    /**
     * erstellt eine neue Einstellung
     *
     * @param  string $name         Name der Einstellung
     * @param  int    $type         Datentyp
     * @param  mixed  $defaultValue Default Wert
     * @return bool
     */
    public function addSetting($name, $type, $defaultValue) {

        if(!isset($this->settings[$name])) {

            $this->settings[$name] = array(
                'name' => $name,
                'type' => $type,
                'defaultValue' => $defaultValue,
                'value' => null
            );
            return true;
        }
        return false;
    }

    /**
     * liest die Daten aus der XML Datei
     */
    protected function readData() {

        $db = RWF::getDatabase();

        //RWF Eisntellungen laden
        $rwfSettings = $db->hGetAll('settings');
        foreach($rwfSettings as $name => $value) {

            if(isset($this->settings[$name])) {

                switch ($this->settings[$name]['type']) {

                    case self::TYPE_STRING:

                        $this->settings[$name]['value'] = (string) $value;
                        break;
                    case self::TYPE_BOOL:

                        $this->settings[$name]['value'] = ($value == "1" ? true : false);
                        break;
                    case self::TYPE_INT:

                        $this->settings[$name]['value'] = (int) $value;
                        break;
                    case self::TYPE_FLOAT:

                        $this->settings[$name]['value'] = (float) $value;
                        break;
                }
            }
        }

        //APP Einstellungen laden
        $appSettings = $db->hGetAll(strtolower(APP_NAME) .':settings');
        foreach($appSettings as $name => $value) {

            if(isset($this->settings[$name])) {

                switch ($this->settings[$name]['type']) {

                    case self::TYPE_STRING:

                        $this->settings[$name]['value'] = (string) $value;

                        break;
                    case self::TYPE_BOOL:

                        $this->settings[$name]['value'] = ($value == "1" ? true : false);

                        break;
                    case self::TYPE_INT:

                        $this->settings[$name]['value'] = (int) $value;

                        break;
                    case self::TYPE_FLOAT:

                        $this->settings[$name]['value'] = (float) $value;

                        break;
                }
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

        //Initialisieren
        if($this->init == false) {

            $this->readData();
            $this->init = true;
        }

        //Einstellung laden
        if (isset($this->settings[$name])) {

            if($this->settings[$name]['value'] !== null) {

                return $this->settings[$name]['value'];
            } else {

                return $this->settings[$name]['defaultValue'];
            }
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

        $this->readData();
    }

    /**
     * Speichert einen neuen Wert fuer die Einstellung (es kann nur der Wert geaendert werden)
     * 
     * @param  String  $setting Einstellung
     * @param  Mixed   $value   Wert
     * @return Boolean
     */
    public function editSetting($settingName, $value) {

        $db = RWF::getDatabase();
        if(isset($this->settings[$settingName])) {

            switch ($this->settings[$settingName]['type']) {

                case self::TYPE_STRING:

                    $value = (string) $value;
                    break;
                case self::TYPE_BOOL:

                    $value = ((bool) $value == true ? 1 : 0);
                    break;
                case self::TYPE_INT:

                    $value = (int) $value;
                    break;
                case self::TYPE_FLOAT:

                    $value = (float) $value;
                    break;
            }

            //in der Datenbank speichern
            if(preg_match('#^rwf\.#i', $settingName)) {

                //RWF Einstellung
                $db->hSet('settings', $settingName, $value);
                return true;
            } elseif(preg_match('#^'. APP_NAME .'\.#i', $settingName)) {

                //APP Eisntellung
                $db->hSet(strtolower(APP_NAME) .':settings', $settingName, $value);
                return true;
            }
        }
        return false;
    }
}
