<?php

namespace RWF\Core;

//Imports
use RWF\Settings\Settings;
use RWF\Request\HttpRequest;
use RWF\Request\HttpResponse;
use RWF\Request\CliRequest;
use RWF\Request\CliResponse;
use RWF\Session\Session;

/**
 * Kernklasse (initialisiert das RWF)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RWF {
    
    /**
     * Einstellungen
     * 
     * @var \RWF\Settings\Settings 
     */
    protected static $settings = null;

    /**
     * Anfrageobjekt
     * 
     * @var \RWF\Request\Request 
     */
    protected static $request = null;
    
    /**
     * Antwortobjekt
     * 
     * @var \RWF\Request\Response 
     */
    protected static $response = null;
    
    /**
     * Sessionobjekt
     * 
     * @var \RWF\Session\Session 
     */
    protected static $session = null;
    
    public function __construct() {
        
        //Multibyte Engine Konfigurieren
        if (function_exists('mb_internal_encoding')) {

            mb_internal_encoding('UTF-8');
            define('MULTIBYTE_STRING', true);
        } else {

            define('MULTIBYTE_STRING', false);
        }
        
        //Anfrage/Antwort initialisieren
        $this->initSettings();
        $this->initRequest();
        $this->initSession();
    }
    
    /**
     * initialisiert die Einstellungen
     */
    public function initSettings() {
        
        self::$settings = new Settings();
    }
    
    /**
     * initalisiert die Anfrage und Antwortobjekte
     */
    protected function initRequest() {
        
        if(ACCESS_METHOD_HTTP) {
            
            self::$request = new HttpRequest();
            self::$response = new HttpResponse();
        } else {
            
            self::$request = new CliRequest();
            self::$response = new CliResponse();
        }
    }
    
    /**
     * initialisiert die Session
     */
    protected function initSession() {
        
        self::$session = new Session();
    }
    
    /**
     * gibt das Einstellungsobjekt zurueck
     * 
     * @return \RWF\Setting\Settings
     */
    public static function getSettings() {
        
        return self::$settings;
    }
    
    /**
     * gibt das Anfrageobjekt zurueck
     * 
     * @return \RWF\Request\Request
     */
    public static function getRequest() {

        return self::$request;
    }

    /**
     * gibt das Antwortobjekt zurueck
     * 
     * @return \RWF\Request\Response
     */
    public static function getResponse() {

        return self::$response;
    }
    
    /**
     * gibt das Sessionobjekt zurueck
     * 
     * @return \RWF\Session\Session
     */
    public static function getSession() {
        
        return self::$session;
    }
    
    /**
     * gibt den Wert einer Einstellung zurueck
     * 
     * @param  String $name Name der Einstellung
     * @return Mixed
     */
    public static function getSetting($name) {
        
        return self::$settings->getValue($name);
    }
    
    /**
     * beendet die Anwendung
     */
    public function finalize() {
        
        //Einstellungen Speichern
        if(self::$settings instanceof Settings) {
            
            self::$settings->finalize();
        }
        
        //Sessionobjekt abschliesen
        if(self::$session instanceof Session) {
            
            self::$session->finalize();
        }
    }
}
