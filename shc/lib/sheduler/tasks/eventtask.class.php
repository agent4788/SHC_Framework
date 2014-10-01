<?php

namespace SHC\Sheduler\Tasks;

//Imports
use SHC\Sheduler\AbstractTask;

/**
 * ueberwacht Statusaenderungen und loest ereignisse aus
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EventTask extends AbstractTask {
    
    /**
     * Prioriteat
     * 
     * @var Integer 
     */
    protected $priority = 90;

    /**
     * Wartezeit zwischen 2 durchläufen
     * 
     * @var String 
     */
    protected $interval = 'PT10S';

    /**
     * fuehrt die Aufgabe aus
     * falls ein Intervall angegeben ist wird automatisch die Ausfuerung in den vogegebenen Zeitabstaenden verzoegert
     */
    public function executeTask() {

        
    }
}
