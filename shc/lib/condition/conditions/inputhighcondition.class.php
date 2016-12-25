<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use SHC\Switchable\Readable;
use SHC\Switchable\SwitchableEditor;

/**
 * Bedingung Eingang hat "1" Signal
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class InputHighCondition extends AbstractCondition {

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
        if (!isset($this->data['inputs']) || !is_array($this->data['inputs'])) {

            throw new \Exception('Eingänge müssen angegeben werden', 1580);
        }

        foreach($this->data['inputs'] as $inputId) {

            $input = SwitchableEditor::getInstance()->getElementById($inputId);
            if($input instanceof Readable) {

                if($input->getState() == Readable::STATE_OFF) {

                    return false;
                }
            }
        }
        return true;
    }

}