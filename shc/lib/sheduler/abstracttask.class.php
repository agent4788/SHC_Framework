<?php

namespace SHC\Sheduler;

//Imports
use RWF\Date\DateTime;

/**
 * Standard Implementierung eines Task
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class AbstractTask implements Task {

    /**
     * Prioriteat
     * 
     * @var Integer 
     */
    protected $priority = 50;

    /**
     * Wartezeit zwischen 2 durchläufen
     * 
     * @var String 
     */
    protected $interval = '';

    /**
     * Zeit der naechsten Ausfuehrung
     *  
     * @var \RWF\Date\DateTime 
     */
    protected $nextRunTime = null;

    public function __construct() {

        $this->nextRunTime = DateTime::now();
    }

    /**
     * gibt die Prioritaet der Aufgabe zurueck
     * 
     * @return Integer
     */
    public function getPriority() {

        return $this->priority;
    }

    /**
     * fuehrt die Aufgabe aus
     */
    public function execute() {

        if ($this->nextRunTime !== null && $this->interval != '' && $this->nextRunTime->isPast()) {

            $this->executeTask();
            $this->nextRunTime->add(new \DateInterval($this->interval));
        } elseif($this->nextRunTime === null) {
            
            $this->executeTask();
        }
    }

    /**
     * fuehrt die Aufgabe aus
     * falls ein Intervall angegeben ist wird automatisch die Ausfuerung in den vogegebenen Zeitabstaenden verzoegert
     */
    public abstract function executeTask();
}
