<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\Select;

/**
 * Auswahlfeld Wochentag
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DayOfWeekChooser extends Select {

    public function __construct($name, $day = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Gruppen anmelden
        $this->setValues(array(
            'mon' => array(RWF::getLanguage()->get('global.date.weekDay.mon'), ($day == 'mon' ? 1 : 0)),
            'tue' => array(RWF::getLanguage()->get('global.date.weekDay.tue'), ($day == 'tue' ? 1 : 0)),
            'wed' => array(RWF::getLanguage()->get('global.date.weekDay.wed'), ($day == 'wed' ? 1 : 0)),
            'thu' => array(RWF::getLanguage()->get('global.date.weekDay.thu'), ($day == 'thu' ? 1 : 0)),
            'fri' => array(RWF::getLanguage()->get('global.date.weekDay.fri'), ($day == 'fri' ? 1 : 0)),
            'sat' => array(RWF::getLanguage()->get('global.date.weekDay.sat'), ($day == 'sat' ? 1 : 0)),
            'sun' => array(RWF::getLanguage()->get('global.date.weekDay.sun'), ($day == 'sun' ? 1 : 0))
        ));
    }
}