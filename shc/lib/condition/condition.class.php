<?php

namespace SHC\Condition;

//Imports


/**
 * Schnitstelle einer Schaltbedingung
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Condition {
    
    /**
     * gibt an ob die Bedingung erfuellt ist
     * 
     * @return Boolean
     */
    public function isSatisfies();
}
