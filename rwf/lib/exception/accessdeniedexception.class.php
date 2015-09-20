<?php

namespace RWF\Exception;

/**
 * Ausnahme fuer unberechtigte zugriffe
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AccessDeniedException extends \Exception {
    
    /**
     * benoetigte Berechtigung
     * 
     * @var String 
     */
    protected  $premission = '';

    /**
     * @param String $premission benoetigte Berechtigung
     */
    public function __construct($premission) {
        
        $this->premission = $premission;
    }
    
    /**
     * gibt den Namen der Berchtigung die benoetigt werden wuerde zurueck
     * 
     * @return string
     */
    public function getPremission() {
        
        return $this->premission;
    }
}
