<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\SelectMultiple;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;

/**
 * Auswahlfeld des Raumes
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchableChooser extends SelectMultiple {

    public function __construct($name, array $switchables = array()) {

        //Allgemeine Daten
        $this->setName($name);
        $this->setOptions(array('size' => 10));

        $values = array();
        foreach(SwitchableEditor::getInstance()->listElements(SwitchableEditor::SORT_BY_NAME) as $switchableElement) {

            if($switchableElement instanceof Switchable) {

                $type = $switchableElement->getTypeName();
                $values[$switchableElement->getId()] = array(
                    $switchableElement->getName() .' ('. $type .') ['. $switchableElement->getNamedRoomList(true) .']',
                    (in_array($switchableElement->getId(), $switchables) ? 1 : 0)
                );
            }
        }
        $this->setValues($values);
    }
}