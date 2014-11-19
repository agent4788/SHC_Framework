<?php

namespace RWF\XML\Exception;

/**
 * XML Ausnahme
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class XmlException extends \Exception {
    
    /**
     * XML Fehler
     * 
     * @var Array 
     */
    protected $xmlErrors = array();
    
    /**
     * 
     * @param String  $message   Fehlermeldung
     * @param Integer $errorCode Fehlercode
     * @param Array   $xml       Errors XML Fehler
     */
    public function __construct($message, $errorCode, array $xmlErrors = array()) {

        $this->xmlErrors = $xmlErrors;
        $this->message = $message;
        $this->code = $errorCode;
    }
    
    /**
     * gibt eine Liste mit den XML Fehlermeldungen zurueck
     * 
     * @return Array
     */
    public function getXmlErrors() {
        
        return $this->xmlErrors;
    }
}
