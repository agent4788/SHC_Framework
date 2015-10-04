<?php

namespace MB\Form\FormElements;

//Imports
use RWF\Form\FormElements\Select;

/**
 * Auswahlfeld des Web Styles
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class WebStyleChooser extends Select {

    public function __construct($name, $style = '') {

        //Allgemeine Daten
        $this->setName($name);

        //Styles setzen
        $this->setValues(array(
            'blitzer' => array('Blitzer', ($style == 'blitzer' ? 1 : 0)),
            'dark_hive' => array('Dark Hive', ($style == 'dark_hive' ? 1 : 0)),
            'flick' => array('Flick', ($style == 'flick' ? 1 : 0)),
            'redmond' => array('Redmond', ($style == 'redmond' ? 1 : 0)),
            'ui_darkness' => array('UI Darkness', ($style == 'ui_darkness' ? 1 : 0)),
            'ui_lightness' => array('UI Lightness', ($style == 'ui_lightness' ? 1 : 0))
        ));
    }
}