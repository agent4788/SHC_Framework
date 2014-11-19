<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\TextField;

/**
 * IP Adresse Eingabefeld
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class MacAddressInputField extends TextField {

    public function __construct($name, $ipAddress = null) {

        //Allgemeine Daten
        $this->setName($name);
        $this->setValue($ipAddress);
    }

    /**
     * prueft die Eingabedaten auf gueltigkeit
     *
     * @return Boolean
     */
    public function validate() {

        //Standard Validierung
        if(!parent::validate()) {

            return false;
        }

        //IP Adresse validieren
        $valid = true;
        RWF::getLanguage()->disableAutoHtmlEndocde();
        $mac = preg_split('#:#', $this->getValue());

        $i = 0;
        foreach($mac as $part) {

            $i++;
            if(!preg_match('#[\da-f]{2}#i', $part)) {

                $this->messages[] = RWF::getLanguage()->get('form.message.mac', $this->getTitle());
                $valid = false;
                break;
            }
        }

        //laenge der Adresse pruefen
        if($i != 6) {

            $valid = false;
        }

        if ($valid === false) {

            $this->addClass('rwf-ui-form-content-invalid');
        }
        $this->isValid = $valid;
        RWF::getLanguage()->enableAutoHtmlEndocde();
        return $valid;
    }
}