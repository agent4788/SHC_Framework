<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\Select;
use RWF\Util\FileUtil;

/**
 * Auswahlfeld des Icons
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class IconChooser extends Select {

    public function __construct($name, $icon = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Gruppen anmelden
        $values = array(
            'shc-icon-chip' => array('shc-icon-chip', ($icon == 'shc-icon-chip' ? 1 : 0)),
            'shc-icon-countdown' => array('shc-icon-countdown', ($icon == 'shc-icon-countdown' ? 1 : 0)),
            'shc-icon-lamp' => array('shc-icon-lamp', ($icon == 'shc-icon-lamp' ? 1 : 0)),
            'shc-icon-socket' => array('shc-icon-socket', ($icon == 'shc-icon-socket' ? 1 : 0))
        );
        $this->setValues($values);
    }
}