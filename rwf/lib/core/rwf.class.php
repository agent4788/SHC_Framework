<?php

namespace RWF\Core;

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
    
    public function __construct() {
        
        //Multibyte Engine Konfigurieren
        if (function_exists('mb_internal_encoding')) {

            mb_internal_encoding('UTF-8');
            define('MULTIBYTE_STRING', true);
        } else {

            define('MULTIBYTE_STRING', false);
        }
        
        
    }
    
    /**
     * beendet die Anwendung
     */
    public function finalize() {
        
    }
}
