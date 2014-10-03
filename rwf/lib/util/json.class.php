<?php

namespace RWF\Util;

//Imports
use RWF\Request\HttpResponse;
use RWF\Exception\JsonException;

/**
 * Hilfsobjekt zur JSON verarbeitung
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class JSON {

    /**
     * sendet ein Array als JSON Object an den Browser
     * 
     * @param Array                     $array    Array das gesendet werden soll
     * @param \RWF\Request\HttpResponse $response Antwortobjekt
     * @param Integer                   $depth    Tiefe
     */
    public static function sendJSON(array $array, HttpResponse $response, $depth = 512) {

        //JSON Optionen
        $options = JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        
        //Mimetype und Header schicken
        //$response->setContentType('application/json');
        $response->addHeader('X-APPLICATION', 'RWF');
        
        //Daten senden
        $response->setBody(json_encode($array, $options));
    }
    
    /**
     * sendet ein Array als JSON Object an den Browser
     * 
     * @param \JsonSerializable         $object    Array das gesendet werden soll
     * @param \RWF\Request\HttpResponse $response Antwortobjekt
     * @param Integer                   $depth    Tiefe
     */
    public static function sendJSONObject(\JsonSerializable $object, HttpResponse $response, $depth = 512) {

        //JSON Optionen
        $options = JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        if(DEVELOPMENT_MODE) {
            $options |= JSON_PRETTY_PRINT;
        }
        
        //Mimetype und Header schicken
        $response->setContentType('application/json');
        $response->addHeader('X-APPLICATION', 'RWF');
        
        //Daten senden
        $response->setBody(json_encode($object, $options));
    }

    /**
     * erzeugt ein Array aus den empfangenen JSON Daten
     * 
     * @param  String $jsonString JSON Daten
     * @return Array
     * @throws \RWF\Exception\JsonException
     */
    public function reciveJSON($jsonString) {
        
        $return = json_encode($jsonString);
        if($return === false) {
            
            throw new JsonException(json_last_error_msg(), json_last_error());
        }
        return $return;
    }
    
}
