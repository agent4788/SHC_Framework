<?php

namespace SHC\Sheduler\Tasks;

//Imports
use SHC\Sheduler\AbstractTask;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchable;

/**
 * Timer Task prueft regelmäßig ob Schaltpunkte zur ausfuehrung bereit stehen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class TimerTask extends AbstractTask {
    
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
    protected $interval = 'PT20S';
    
    /**
     * fuehrt die Aufgabe aus
     * falls ein Intervall angegeben ist wird automatisch die Ausfuerung in den vogegebenen Zeitabstaenden verzoegert
     */
    public function executeTask() {
        
        //Liste mit den Schaltbaren Elementen holen
        $switchables = SwitchableEditor::getInstance()->listElements();
        
        //alle Elemente durchlaufen und Pruefen ob ausfuehrbar
        foreach ($switchables as $switchable) {
            
            if($switchable instanceof Switchable) {
                
                $switchable->execute();
            }
        }
    }
}
