<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;

/**
 * Bedingung gerade Kalenderwoche
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class JustCalendarWeekCondition extends AbstractCondition {

    /**
     * gibt an ob die Bedingung erfuellt ist
     *
     * @return Boolean
     */
    public function isSatisfies() {

        return false;
    }

}