<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;

/**
 * Bedingung Schaltbares Element hat "0" Zustand
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchableStateLowCondition extends AbstractCondition {

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
        if (!isset($this->data['switchables'])) {

            throw new \Exception('switchables müssen angegeben werden', 1580);
        }

        $switchables = explode(',', $this->data['switchables']);
        foreach($switchables as $switchable) {

            $switchableObject = SwitchableEditor::getInstance()->getElementById($switchable);
            if($switchableObject instanceof Switchable) {

                if($switchableObject->getState() == Switchable::STATE_OFF) {

                    return true;
                }
            }
        }

        return false;
    }

}