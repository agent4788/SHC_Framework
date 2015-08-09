<?php

namespace RWF\Style;

//Imports

/**
 * Bildet einen Style fuer die Weboberflaeche ab
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Style {

    /**
     * Name des Styles
     * 
     * @var string
     */
    protected $name = '';

    /**
     * gibt an ob der Style fuer die Weboberflaeche oder Mobiloberflaeche geeignet ist
     * 
     * @var Boolean 
     */
    protected $isMobile = false;

    /**
     * Liste aller zugehoerigen JavaScript Dateien
     * 
     * @var Array 
     */
    protected $jsFiles = array();

    /**
     * Liste aller zugehoerigen CSS Dateien
     * 
     * @var Array 
     */
    protected $cssFiles = array();

    /**
     * @param String  $name     Name des Styles
     * @param Boolean $isMobile gibt an ob es ein Mobile Style ist
     * @param Array   $jsFiles  Liste der JavaScript Dateien
     * @param Array   $cssFiles Liste der CSS Dateien
     */
    public function __construct($name, $isMobile = false, array $jsFiles = array(), array $cssFiles = array()) {

        $this->name = $name;
        $this->isMobile = $isMobile;
        $this->jsFiles = $jsFiles;
        $this->cssFiles = $cssFiles;
    }

    /**
     * gibt den Namen des Styles zurueck
     * 
     * @return String
     */
    public function getName() {

        return $this->name;
    }

    public function isMobile() {

        if ($this->isMobile == true) {

            return true;
        }
        return false;
    }

    /**
     * gibt eine Liste mit allen zugehoerigen JavaScript Dateien zurueck
     * 
     * @return String
     */
    public function listJsFiles() {

        return $this->jsFiles;
    }

    /**
     * gibt eine Liste mit allen zugehoerigen CSS Dateien zurueck
     * 
     * @return String
     */
    public function listCssFiles() {

        return $this->cssFiles;
    }

    /**
     * gibt die HTML Header fuer den Stylie als HTML Fragment zurueck
     * 
     * @return String
     */
    public function fetchHtmlHeaderTags() {

        $html = '';
        sort($this->cssFiles);
        foreach ($this->cssFiles as $file) {

            $html .= '<link rel="stylesheet" type="text/css" href="'. $file .'" />' . "\n";
        }

        sort($this->jsFiles);
        foreach ($this->jsFiles as $file) {

            $html .= '<script type="text/javascript" src="'. $file .'"></script>' . "\n";
        }
        
        return $html;
    }

}
