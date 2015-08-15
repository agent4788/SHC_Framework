<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use SHC\UserAtHome\UserAtHomeEditor;
use SHC\UserAtHome\UserAtHome;

/**
 * Bedingung Benutzer zu Hause
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserAtHomeCondition extends AbstractCondition {
    
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

        //noetige Parameter pruefen
        if (!isset($this->data['users'])) {

            throw new \Exception('Eine Liste mit den Benutzern zu Hause muss angegeben werden', 1580);
        }
        
        //Liste mit den Benutzern zu Hause holen
        $usersAtHome = UserAtHomeEditor::getInstance()->listUsersAtHome();
        $usersAtHomeIds = explode(',', $this->data['users']);
        
        //Benutzer durchlaufen und Pruefen ob jemand Online
        foreach ($usersAtHome as $userAtHome) {

            /* @var $userAtHome \SHC\UserAtHome\UserAtHome  */

            //deaktivierte Benutzer ignorieren
            if(!$userAtHome->isEnabled()) {

                continue;
            }

            /* @var $userAtHome \SHC\UserAtHome\UserAtHome */
            if(in_array($userAtHome->getId(), $usersAtHomeIds) && $userAtHome->getState() == UserAtHome::STATE_ONLINE) {
                
                return true;
            }
        }
        return false;
    }
}
