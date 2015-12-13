<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\Select;
use RWF\User\User;


/**
 * Sprachauswahl
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class LanguageChooser extends Select {

    public function __construct($name, User $user = null) {

        //Allgemeine Daten
        $this->setName($name);

        if($user !== null) {

            $lang = ($user->getLanguage() !== null ? $user->getLanguage() : RWF::getSetting('rwf.language.defaultLanguage'));
        } else {

            $lang = RWF::getSetting('rwf.language.defaultLanguage');
        }

        //Sprachen anmelden
        $this->setValues(array(
            'de' => array('Deutsch', ($lang == 'de' ? 1 : 0)),
            'en' => array('Englisch', ($lang == 'en' ? 1 : 0))
        ));
    }
}