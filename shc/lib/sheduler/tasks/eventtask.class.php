<?php

namespace SHC\Sheduler\Tasks;

//Imports
use SHC\Core\SHC;
use SHC\Event\EventEditor;
use SHC\Sensor\SensorPointEditor;
use SHC\Sheduler\AbstractTask;
use SHC\Switchable\SwitchableEditor;
use SHC\UserAtHome\UserAtHomeEditor;

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
    protected $priority = 71;

    /**
     * Wartezeit zwischen 2 durchläufen
     * 
     * @var String 
     */
    protected $interval = 'PT10S';
    
    public function __construct() {
        
        parent::__construct();
    }

    /**
     * fuehrt die Aufgabe aus
     * falls ein Intervall angegeben ist wird automatisch die Ausfuerung in den vogegebenen Zeitabstaenden verzoegert
     */
    public function executeTask() {

        //Intervall festlegen
        switch(SHC::getSetting('shc.shedulerDaemon.performanceProfile')) {

            case 1:

                //fast
                $this->interval = 'PT1S';
                break;
            case 2:

                //default
                $this->interval = 'PT10S';
                break;
            case 3:

                //slow
                $this->interval = 'PT20S';
                break;
        }

        //Daten aktualisieren
        UserAtHomeEditor::getInstance()->loadData();
        SensorPointEditor::getInstance()->loadData();
        SwitchableEditor::getInstance()->loadData();
        EventEditor::getInstance()->loadData();

        foreach(EventEditor::getInstance()->listEvents(EventEditor::SORT_NOTHING) as $event) {

            /* @var $event \SHC\Event\Event */
            $event->run();
        }

        //neuen Status der Elemente Speichern
        SwitchableEditor::getInstance()->updateState();
    }
}
