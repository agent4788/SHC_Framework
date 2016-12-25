<?php

namespace SHC\Switchable\Switchables;

//Imports
use SHC\Switchable\AbstractSwitchable;

/**
 * Virtuelle Steckdose (nur zum vorfählen für Bedingungen)
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class VirtualSocket extends AbstractSwitchable {

    /**
     * schaltet das Objekt ein
     *
     * @return Boolean
     */
    public function switchOn() {

        $this->state = self::STATE_ON;
        $this->stateModified = true;
    }

    /**
     * schaltet das Objekt aus
     *
     * @return Boolean
     */
    public function switchOff() {

        $this->state = self::STATE_OFF;
        $this->stateModified = true;
    }

}