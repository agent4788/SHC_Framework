<?php 

namespace SHC\Sheduler\Tasks;

//Imports
use SHC\Sheduler\AbstractTask;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\WakeOnLan;
use SHC\Switchable\Element;

/**
 * aktualisiert den Status von WOL Geraeten
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class WakeOnLanUpdateTask extends AbstractTask {

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
    protected $interval = 'PT60S';

    /**
     * fuehrt die Aufgabe aus
     * falls ein Intervall angegeben ist wird automatisch die Ausfuerung in den vogegebenen Zeitabstaenden verzoegert
     */
    public function executeTask() {

        //Liste mit den Schaltbaren Elementen holen
        $switchables = SwitchableEditor::getInstance()->listElements();

        //alle Elemente durchlaufen und Pruefen ob ausfuehrbar
        foreach ($switchables as $switchable) {

            if($switchable instanceof WakeOnLan) {
                
                //Ping senden
                $state = exec(sprintf('ping -c 1 -W 1 %s', escapeshellarg($switchable->getIpAddress())), $res, $rval);
                
                if (strlen($state) > 0) {
                    
                    //online
                    $switchable->setState(Element::STATE_ON);
                } else {
                    
                    //offline
                    $switchable->setState(Element::STATE_OFF);
                }
            } 
        }
        
        //Daten Persistent Speichern
        SwitchableEditor::getInstance()->updateState();
    }

}
