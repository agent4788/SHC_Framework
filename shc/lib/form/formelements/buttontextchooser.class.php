<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\Select;
use SHC\Switchable\Element;

/**
 * Auswahl des Button Text
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ButtonTextChooser extends Select {

    /**
     * @param String  $name         Feld Name
     * @param Integer $buttonText   Text ID
     */
    public function __construct($name, $buttonText = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Auswahl
        $values = array(
            Element::BUTTONS_ON_OFF => array(RWF::getLanguage()->get('global.on') .'/'. RWF::getLanguage()->get('global.off'), ($buttonText == Element::BUTTONS_ON_OFF ? 1 : ($buttonText === null ? 1 : 0))),
            Element::BUTTONS_UP_DOWN => array(RWF::getLanguage()->get('global.up') .'/'. RWF::getLanguage()->get('global.down'), ($buttonText == Element::BUTTONS_UP_DOWN ? 1 : 0)),
            Element::BUTTONS_OPEN_CLOSED => array(RWF::getLanguage()->get('global.open') .'/'. RWF::getLanguage()->get('global.closed'), ($buttonText == Element::BUTTONS_OPEN_CLOSED ? 1 : 0)),
            Element::BUTTONS_START_STOP => array(RWF::getLanguage()->get('global.start') .'/'. RWF::getLanguage()->get('global.stop'), ($buttonText == Element::BUTTONS_START_STOP ? 1 : 0))
        );
        $this->setValues($values);
    }
}