<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use SHC\UserAtHome\UserAtHomeEditor;
use SHC\UserAtHome\UserAtHome;

/**
 * Bedingung niemand zu Hause
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class NobodyAtHomeCondition extends AbstractCondition {
    
    /**
     * gibt an ob die Bedingung erfuellt ist
     * 
     * @return Boolean
     */
    public function isSatisfies() {
        
        //wenn deaktiviert immer True
        if (!$this->isEnabled()) {

            return true;
        }
        
        //Liste mit den Benutzern zu Hause holen
        $usersAtHome = UserAtHomeEditor::getInstance()->listUsersAtHome();
        
        //Benutzer durchlaufen und Pruefen ob jemand Online
        foreach ($usersAtHome as $userAtHome) {
            
            /* @var $userAtHome \SHC\UserAtHome\UserAtHome */
            if($userAtHome->getState() == UserAtHome::STATE_ONLINE) {
                
                return false;
            }
        }
        return true;
    }
}
