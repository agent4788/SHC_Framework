<?php

namespace RWF\Language;

//Imports
use RWF\Util\String;
use RWF\Util\FileUtil;

/**
 * Sprachverwaltung
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Language {

    /**
     * Ordner in dem die Sprachdateien gesucht werden
     * 
     * @var String 
     */
    protected $languageDir = '';

    /**
     * Sprache
     *
     * @var String
     */
    protected $language = '';

    /**
     * geladene Module
     * 
     * @var Array
     */
    protected $modules = array();

    /**
     * Sprachvariablen
     *
     * @var Array
     */
    protected $languageItems = array();

    /**
     * aktuell verwendete Sprache
     *
     * @var Integer
     */
    protected $currentLanguage = 0;
    
    /**
     * Automatisch HTML encodieren
     * 
     * @var Boolean 
     */
    protected $autoEncodeHtml = true;

    /**
     * Parameter
     *
     * @var Array
     */
    protected $params = array();

    /**
     * Tausender Seperator
     *
     * @var String
     */
    protected $tousandsSeparator = ',';

    /**
     * Dezimal Seperator
     *
     * @var String
     */
    protected $decimalSeparator = '.';

    /**
     * unbekannte Variable
     * 
     * @var String
     */
    protected $unknown = 'unknown';

    /**
     * gibt an ob PHP im CLI Modus läuft
     * 
     * @var Boolean 
     */
    protected $cli = false;

    public function __construct($lang = 'de', $languageDir = '') {

        if (PHP_SAPI == 'cli') {

            $this->cli = true;
            $this->autoEncodeHtml = false;
        }

        //Ordner mit den Sprachdateien
        if ($languageDir != '') {

            $this->setLanguageDir($languageDir);
        } else {

            $this->setLanguageDir(PATH_BASE . APP_NAME . '/data/lang/');
        }

        $this->language = $lang;
        $this->loadModul('global');

        $this->tousandsSeparator = $this->getPlain('global.tousandsSeparator');
        $this->decimalSeparator = $this->getPlain('global.decimalSeparator');
    }

    /**
     * aktiviert das automatische HTML Encodieren
     */
    public function enableAutoHtmlEndocde() {
        
        $this->autoEncodeHtml = true;
    }
    
    /**
     * deaktiviert das automatische HTML Encodieren
     */
    public function disableAutoHtmlEndocde() {
        
        $this->autoEncodeHtml = false;
    }
    
    /**
     * gibt an ob das automatische HTML Encodieren aktiv ist
     * 
     * @return Boolean
     */
    public function isEnabledAutoHtmlEndocde() {
        
        return $this->autoEncodeHtml;
    }
    
    /**
     * setzt den Ordner in dem nach den Sprachdateien gesucht wird
     * 
     * @param  String  $dir Ordner mit Pfadangabe
     * @return Boolean
     */
    public function setLanguageDir($dir) {

        if (is_dir($dir)) {

            $this->languageDir = $dir;
            return true;
        }
        return false;
    }

    /**
     * laedt ein Sprach Modul
     * 
     * @param  String $name Name des Moduls
     * @return Boolean
     */
    public function loadModul($name) {

        if (in_array($name, $this->modules)) {

            return true;
        }

        $file = FileUtil::scannDirectory($this->languageDir . $this->language, strtolower($name) . '.lang.php');
        if ($file !== null && @require($file)) {

            $this->languageItems = array_merge($this->languageItems, $l);
            $this->modules[] = $name;
            return true;
        }
        return false;
    }

    /**
     * gibt die Sprachvariable im Klartext zurueck
     * 
     * @param  String  $var            Name
     * @param  String  $default        Standartwert
     * @param  Boolean $useTranslation Sprache oder STandartwert verwenden
     * @param  Boolean $encodeHTML     HTML encodieren
     * @return String
     */
    public function getPlain($var, $default = null, $useTranslation = true, $encodeHTML = true) {

        if ($this->cli === true && $this->autoEncodeHtml === false) {

            $encodeHTML = false;
        }

        //keine Sprachvariable verwenden
        if ($default !== null && $useTranslation !== null && $useTranslation == false) {

            return $default;
        }

        //Sprachvariable
        if (isset($this->languageItems[$var])) {

            if ($encodeHTML == true) {

                return String::encodeHTML($this->languageItems[$var]);
            }
            return $this->languageItems[$var];
        }

        if ($encodeHTML == true) {

            return String::encodeHTML($this->unknown);
        }
        return $this->unknown;
    }

    /**
     * gibt die Sprachvariable im Klartext zurueck
     * alias fuer getPlain()
     * 
     * @param  String  $var            Name
     * @param  String  $default        Standartwert
     * @param  Boolean $useTranslation Sprache oder STandartwert verwenden
     * @param  Boolean $encodeHTML     HTML encodieren
     * @return String
     * @see    Language::getPlain()
     */
    public function val($var, $default = null, $useTranslation = true, $encodeHTML = true) {

        return $this->getPlain($var, $default, $useTranslation, $encodeHTML);
    }

    /**
     * gibt die Sprachvariable mit geparsten Platzhaltern zurueck
     *
     * @param  String $var Name
     * @return String
     */
    public function get($var) {

        $this->params = array();
        $this->params = func_get_args();

        if ($this->cli === false && $this->autoEncodeHtml === true) {

            if (isset($this->languageItems[$var])) {

                return String::encodeHTML(@preg_replace('#\{(\d+):(i|f|s)(?::([^:]+))?(?::(\d+))?\}#ie', "\$this->parse('$1', '$2', '$3', '$4')\n", $this->languageItems[$var]));
            }

            return String::encodeHTML($this->unknown);
        } else {

            if (isset($this->languageItems[$var])) {

                return @preg_replace('#\{(\d+):(i|f|s)(?::([^:]+))?(?::(\d+))?\}#ie', "\$this->parse('$1', '$2', '$3', '$4')\n", $this->languageItems[$var]);
            }

            return $this->unknown;
        }
    }

    /**
     * ersetzt Platzhalter
     * 
     * @param String $id       Nummer
     * @param String $type     Datentyp
     * @param String $default  Standart Falls keine Daten uebergeben
     * @param String $decimals Kommastellen
     */
    protected function parse($id, $type, $default = '', $decimals = '') {

        $i = intval($id);
        $type = String::toLower(String::trim($type));
        switch ($type) {

            case 's':

                //String
                if (isset($this->params[$i])) {

                    $return = $this->params[$i];
                    break;
                }

                $return = $default;

                break;
            case 'i':

                //Integer
                if (isset($this->params[$i])) {

                    $return = number_format($this->params[$i], 0, $this->decimalSeparator, $this->tousandsSeparator);
                    break;
                } elseif ($default != '') {

                    $return = number_format($default, 0, $this->decimalSeparator, $this->tousandsSeparator);
                    break;
                }

                $return = 0;

                break;
            case 'f':

                //Float
                if ($decimals == '') {

                    $decimals = 2;
                }

                if (isset($this->params[$i])) {

                    $return = number_format($this->params[$i], $decimals, $this->decimalSeparator, $this->tousandsSeparator);
                    break;
                } elseif ($default != '') {

                    $return = number_format($default, $decimals, $this->decimalSeparator, $this->tousandsSeparator);
                    break;
                }

                $return = 0.0;

                break;
            default:

                //Fehler
                throw new \Exception('Ungültiger Datentyp in der Sprachvariable', 1130);
        }

        return $return;
    }

    /**
     * gibt den Tausender Seperator der aktuellen Sprache zurueck
     *
     * @return String Seperator
     */
    public function getTousandsSeparator() {

        return $this->tousandsSeparator;
    }

    /**
     * gibt den Dezimal Seperator der aktuellen Sprache zurueck
     *
     * @return String Seperator
     */
    public function getDecimalSeparator() {

        return $this->decimalSeparator;
    }

}
