<?php

namespace RWF\Core;

//Imports
use RWF\Request\HttpRequest;
use RWF\Request\HttpResponse;
use RWF\Request\CliRequest;
use RWF\Request\CliResponse;

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
     * Anfrageobjekt
     * 
     * @var RWF\Request\Request 
     */
    protected static $request = null;
    
    /**
     * Antwortobjekt
     * 
     * @var RWF\Request\Response 
     */
    protected static $response = null;
    
    public function __construct() {
        
        //Multibyte Engine Konfigurieren
        if (function_exists('mb_internal_encoding')) {

            mb_internal_encoding('UTF-8');
            define('MULTIBYTE_STRING', true);
        } else {

            define('MULTIBYTE_STRING', false);
        }
        
        //Anfrage/Antwort initialisieren
        $this->initRequest();
    }
    
    /**
     * initalisiert die Anfrage und Antwortobjekte
     */
    public function initRequest() {
        
        if(ACCESS_METHOD_HTTP) {
            
            self::$request = new HttpRequest();
            self::$response = new HttpResponse();
        } else {
            
            self::$request = new CliRequest();
            self::$response = new CliResponse();
        }
    }
    
    /**
     * gibt das Anfrageobjekt zurueck
     * 
     * @return RWF\Request\Request
     */
    public static function getRequest() {

        return self::$request;
    }

    /**
     * gibt das Antwortobjekt zurueck
     * 
     * @return RWF\Request\Response
     */
    public static function getResponse() {

        return self::$response;
    }
    
    /**
     * beendet die Anwendung
     */
    public function finalize() {
        
    }
}
