<?php

namespace RWF\Request;

//Imports
use \RWF\Util\DataTypeUtil;

/**
 * CLI Anfrage
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class CliRequest implements Request {

    /**
     * prueft ob ein Parameter vorhanden ist
     * 
     * @param  String  $name   Name des Parameters
     * @param  String  $method Datenquelle
     * @return Boolean
     */
    public function issetParam($name, $method = self::GET) {
        
        if(isset($argv[$name])) {
            
            return true;
        }
        return false;
    }

    /**
     * gibt den Wert eines Parameters zurueck
     * 
     * @param  String  $name     Name des Parameters
     * @param  String  $method   Datenquelle
     * @param  Integer $dataType Erwarteter Datentyp
     * @return Mixed
     */
    public function getParam($name, $method = self::GET, $dataType = DataTypeUtil::PLAIN) {
        
        if(isset($argv[$name])) {
            
            return DataTypeUtil::checkAndConvert($argv[$name], $dataType);
        }
        return null;
    }

    /**
     * gibt eine Liste mit allen Parametern zurueck
     * 
     * @param  String $method Datenquelle
     * @return Array
     */
    public function listParamNames($method = 'all') {
        
        if(isset($argv)) {
            
            return array_keys($argv);
        }
        return array();
    }
}
