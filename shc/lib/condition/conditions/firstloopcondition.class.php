<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use SHC\Sheduler\Sheduler;

/**
 * Diese Bedingung ist nur im ersten Schleifendurchlauf des Shedulers wahr
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FirstLoopCondition extends AbstractCondition {

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

        if(Sheduler::getLoopId() === 0) {

            return true;
        }
        return false;
    }
}