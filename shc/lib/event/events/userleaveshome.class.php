<?php

namespace SHC\Event\Events;

//Imports
use SHC\Event\AbstractEvent;

/**
 * Ereignis Benutzer geht von zu hause
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserLeavesHome extends AbstractEvent {
    
    /**
     * gibt an ob das Ereigniss erfuellt ist
     * 
     * @return Boolean
     */
    public function isSatisfies() {
        
    }

    /**
     * fuehr die Aktionen aus
     */
    public function execute() {
        
    }
}
