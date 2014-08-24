<?php

namespace RWF\ClassLoader\Exception;

/**
 * Ausnahme Klasse nicht gefunden
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ClassNotFoundException extends \Exception {

    /**
     * angeforderte Klasse
     * 
     * @var String
     */
    protected $class = '';

    /**
     * @param String $class   Klassenname
     * @param String $errCode Fehlercode
     * @param String $message Fehlerbeschreibung
     */
    public function __construct($class, $errCode, $message = '') {

        $this->class = $class;
        $this->code = $errCode;
        $this->message = $message;
    }

    /**
     * gibt die Klasse zurueck die nicht gefunden wurde
     * 
     * @return String Klassenname
     */
    public function getClass() {

        return $this->class;
    }

}
