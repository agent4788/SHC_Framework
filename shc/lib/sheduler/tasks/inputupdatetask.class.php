<?php

namespace SHC\Sheduler\Tasks;

//Imports
use SHC\Core\SHC;
use SHC\Sheduler\AbstractTask;
use SHC\Switchable\Readable;
use SHC\Switchable\SwitchableEditor;

/**
 * aktualisiert den Status von WOL Geraeten
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class InputUpdateTask extends AbstractTask {

    /**
     * Prioriteat
     *
     * @var Integer
     */
    protected $priority = 10;

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

        //Liste mit den Schaltbaren Elementen holen
        $switchables = SwitchableEditor::getInstance()->listElements();

        //alle Elemente durchlaufen und Pruefen ob ausfuehrbar
        foreach ($switchables as $switchable) {

            if($switchable instanceof Readable && $switchable->isEnabled()) {

                $switchable->readState(false);
            }
        }

        //Daten Persistent Speichern
        SwitchableEditor::getInstance()->updateState();
    }

}