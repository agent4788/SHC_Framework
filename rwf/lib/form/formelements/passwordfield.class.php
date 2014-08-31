<?php

namespace RWF\Form\FormElements;

//Imports
use RWF\Form\AbstractFormElement;
use RWF\Util\String;

/**
 * Passwortfeld
 * 
 * Optionen:
 * Integer minlength Minimale Eingabelaenge
 * Integer maxlength Maximale Eingabelaenge
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class PasswordField extends AbstractFormElement {

    /**
     * erzeugt das HTML Element fuer die Web View
     * 
     * @return String
     */
    protected function fetchWebView() {

        //Zufaellige ID
        $randomId = String::randomStr(64);
        $this->addId('a' . $randomId);

        //Deaktiviert
        $disabled = '';
        if ($this->isDisabled()) {

            $disabled = ' disabled="disabled" ';
            $this->addClass('disabled');
        }

        //CSS Klassen
        $class = '';
        if (count($this->classes) > 0) {

            $class = ' ' . String::encodeHTML(implode(' ', $this->classes));
        }

        //CSS IDs
        $id = '';
        if (count($this->ids) > 0) {

            $id = ' id="' . String::encodeHTML(implode(' ', $this->ids)) . '" ';
        }

        //Optionen
        $options = '';
        if (isset($this->options['maxlength'])) {

            $options .= ' maxlength="' . $this->options['maxlength'] . '" ';
        }

        //HTML Code
        $html = '<div class="rwf-ui-form-content">' . "\n";

        //Titel
        if ($this->getTitle() != '') {

            $html .= '<div class="rwf-ui-form-content-title">' . String::encodeHTML($this->getTitle()) . ($this->isRequiredField() ? ' <span class="rwf-ui-form-content-required">*</span>' : '') . "</div>\n";
        }

        //Formularfeld
        $html .= '<div class="rwf-ui-form-content-element">';
        $html .= '<input type="password" name="' . String::encodeHTML($this->getName()) . '" class="rwf-ui-form-content-passwordfield' . $class . '" value="" ' . $id . $options . $disabled . ' />';
        $html .= "</div>\n";

        //Beschreibung
        if ($this->getDescription() != '') {

            $html .= '<div class="rwf-ui-form-content-description">' . String::encodeHTML($this->getDescription()) . '</div>';
        }

        $html .= "</div>\n";

        //JavaScript ueberpruefung
        $html .= "<script type=\"text/javascript\">\n";
        $html .= "
            \$(function() {
                \$('#a" . $randomId . "').bind('keyup', function() {
                    
                    var val   = \$('#a" . $randomId . "').val();
                    var error = false;
                    
                    " . (isset($this->options['maxlength']) ? "
                    //Maximal Laenge
                    if(val.length > " . (isset($this->options['maxlength']) ? $this->options['maxlength'] : '0' ) . ") {
			error = true;
                    }" : '') . "
                        
                    " . (isset($this->options['minlength']) ? "
                    //Minimal Laenge
                    if(" . ($this->isRequiredField() === false ? 'val.length > 0 && ' : '') . "val.length < " . (isset($this->options['minlength']) ? $this->options['minlength'] : '0' ) . ") {
                        error = true;
                    }" : '') . "
                        
                    //Fehler
                    if(error == true) {
                        \$('#a" . $randomId . "').addClass('rwf-ui-form-content-invalid');
                    } else {
			\$('#a" . $randomId . "').removeClass('rwf-ui-form-content-invalid');
                    }
						
                    //Reset
                    error = false;
                });
            });\n";
        $html .= "</script>\n";

        return $html;
    }

    /**
     * erzeugt das HTML Element fuer die Mobile View
     * 
     * @return String
     */
    protected function fetchMobileView() {

        return 'not implemented';
    }

    /**
     * prueft die Eingabedaten auf gueltigkeit
     * 
     * @return Boolean
     */
    public function validate() {

        $valid = true;
        $value = $this->getValue();

        //Pflichtfeld
        if ($this->isRequiredField() && $value == '') {

            $this->messages[] = 'Das Feld ' . String::encodeHTML($this->getTitle()) . ' muss ausgefüllt werden';
            $valid = false;
        }

        //Minimale Laenge
        if (isset($this->options['minlength']) && !String::checkLength($value, $this->options['minlength'])) {

            $this->messages[] = 'Das Feld ' . String::encodeHTML($this->getTitle()) . ' muss mindestens ' . String::numberFormat($this->options['minlength']) . ' Zeichen lang sein';
            $valid = false;
        }

        //Maximale Laenge
        if (isset($this->options['maxlength']) && !String::checkLength($value, 0, $this->options['maxlength'])) {

            $this->messages[] = 'Das Feld ' . String::encodeHTML($this->getTitle()) . ' darf maximal ' . String::numberFormat($this->options['maxlength']) . ' Zeichen lang sein';
            $valid = false;
        }

        if ($valid === false) {

            $this->addClass('rwf-ui-form-content-invalid');
        }
        return $valid;
    }

}
