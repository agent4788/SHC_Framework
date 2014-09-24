<?php

namespace SHC\Sheduler\Tasks;

//Imports
use SHC\UserAtHome\UserAtHomeEditor;

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
    protected $priority = 50;

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

        //Liste mit den Benutzern zu Hause holen
        $usersAtHome = UserAtHomeEditor::getInstance()->listUsersAtHome();

        //Pruefen ob Benutzer zu Hause
        foreach ($usersAtHome as $userAtHome) {
            
            //Ping senden
            /* @var $userAtHome \SHC\UserAtHome\UserAtHome */
            $state = exec(sprintf('ping -c 1 -W 1 %s', escapeshellarg($userAtHome->getIpAddress())), $res, $rval);

            if (strlen($state) > 0) {

                //online
                $userAtHome->setState(Element::STATE_ON);
            } else {

                //offline
                $userAtHome->setState(Element::STATE_OFF);
            }
        }

        //Status speichern
        UserAtHomeEditor::getInstance()->updateState();
    }

}
