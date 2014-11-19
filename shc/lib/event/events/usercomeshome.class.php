<?php

namespace SHC\Event\Events;

//Imports
use SHC\Event\AbstractEvent;
use SHC\UserAtHome\UserAtHomeEditor;
use SHC\UserAtHome\UserAtHome;
use RWF\Date\DateTime;

/**
 * Ereignis Benutzer kommt nach Hause
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserComesHome extends AbstractEvent {
    
    /**
     * Status
     * 
     * @var Array 
     */
    protected $state = array();
    
    /**
     * gibt an ob das Ereigniss erfuellt ist
     * 
     * @return Boolean
     */
    public function isSatisfies() {
        
        //Pruefen ob Ereignis aktiv
        if($this->enabled == false) {
            
            return false;
        }
        
        //noetige Parameter pruefen
        if (!isset($this->data['users'])) {

            throw new \Exception('Eine Liste mit den Benutzern zu Hause muss angegeben werden', 1580);
        }
        
        //pruefen ob Warteintervall angegeben und noch nicht abgelaufen
        if(isset($this->data['interval'])) {
            
            $date = DateTime::now();
            $date->sub(new \DateInterval($this->data['interval']));
            
            if($this->time instanceof DateTime && $this->time > $date) {
                
                //noch in der Sperrzeit fuer weitere Events
                return false;
            }
        }
        
        //pruefen ob der Ereigniszustand erfuellt ist
        $success = false;
        $usersAtHome = UserAtHomeEditor::getInstance()->listUsersAtHome();
        foreach($usersAtHome as $userAtHome) {
            
            /* @var $userAtHome \SHC\UserAtHome\UseratHome */
            if(in_array($userAtHome->getId(), $this->data['users'])) {
                
                if(isset($this->state[$userAtHome->getId()])) {
                    
                    //Status bekannt und unveraendert
                    if($this->state[$userAtHome->getId()] != $userAtHome->getState() && $userAtHome->getState() == UserAtHome::STATE_ONLINE) {
                        
                        //neuen Status speichern
                        $this->state[$userAtHome->getId()] = $userAtHome->getState();
                        $success = true;
                    } else {
                        
                        //Status Speichern
                        $this->state[$userAtHome->getId()] = $userAtHome->getState();
                    }
                } else {
                    
                    //Status unbekannt -> Speichern
                    $this->state[$userAtHome->getId()] = $userAtHome->getState();
                }
            }
        }

        //kein Zustandswechsel erfolgt
        if($success === false) {

            return false;
        }
        
        //Bedingungen pruefen
        foreach ($this->conditions as $condition) {
            
            /* @var $condition \SHC\Condition\Condition */
            if(!$condition->isSatisfies()) {
                
                //eine Bedingung trifft nicht zu
                return false;
            }
        }
        
        //Ereignis zur ausfuehrung bereit
        $this->time = DateTime::now();
        return true;
    }

}
