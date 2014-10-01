<?php

namespace SHC\Sheduler;

/**
 * Schnittstelle fuer Aufgaben des Shedulers
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Task {
    
    /**
     * gibt die Prioritaet der Aufgabe zurueck
     * 
     * @return Integer
     */
    public function getPriority();
    
    /**
     * fuehrt die Aufgabe aus
     */
    public function execute();
}
