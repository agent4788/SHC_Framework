<?php

namespace SHC\Sheduler\Tasks;

//Imports
use SHC\Core\SHC;
use SHC\Sheduler\AbstractTask;
use SHC\UserAtHome\UserAtHomeEditor;
use SHC\Switchable\Element;

/**
 * aktualisiert den Status von Benutzern zu Hause
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserAtHomeUpdateTask extends AbstractTask {

    /**
     * Prioriteat
     * 
     * @var Integer 
     */
    protected $priority = 12;

    /**
     * Wartezeit zwischen 2 durchläufen
     * 
     * @var String 
     */
    protected $interval = 'PT30S';

    /**
     * fuehrt die Aufgabe aus
     * falls ein Intervall angegeben ist wird automatisch die Ausfuerung in den vogegebenen Zeitabstaenden verzoegert
     */
    public function executeTask() {

        //Intervall festlegen
        switch(SHC::getSetting('shc.shedulerDaemon.performanceProfile')) {

            case 1:

                //fast
                $this->interval = 'PT15S';
                break;
            case 2:

                //default
                $this->interval = 'PT30S';
                break;
            case 3:

                //slow
                $this->interval = 'PT60S';
                break;
        }
        
        //Liste mit den Benutzern zu Hause holen
        $usersAtHome = UserAtHomeEditor::getInstance()->listUsersAtHome();

        //Pruefen ob Benutzer zu Hause
        foreach ($usersAtHome as $userAtHome) {

            /* @var $userAtHome \SHC\UserAtHome\UserAtHome */

            //Pruefen ob Benutzer zu hause
            if($userAtHome->isEnabled()) {

                //Ping senden
                $state = exec(sprintf('ping -c 1 -W 1 %s', escapeshellarg($userAtHome->getIpAddress())), $res, $rval);

                //Auswerten
                if (strlen($state) > 0) {

                    //online
                    $userAtHome->setState(Element::STATE_ON);
                } else {

                    //offline
                    $userAtHome->setState(Element::STATE_OFF);
                }
            }
        }

        //Status speichern
        UserAtHomeEditor::getInstance()->updateState();
    }

}
