<?php

namespace RWF\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\AbstractFormElement;
use RWF\Util\String;

/**
 * Textarea
 * 
 * Optionen:
 * Integer minlength Minimale Eingabelaenge
 * Integer maxlength Maximale Eingabelaenge
 * Integer cols      Zeilen
 * Integer rows      Spalten
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class TextArea extends AbstractFormElement {

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
        if (isset($this->options['cols'])) {

            $options .= ' cols="' . $this->options['cols'] . '" ';
        } else {

            $options .= ' cols="100" ';
        }
        if (isset($this->options['rows'])) {

            $options .= ' rows="' . $this->options['rows'] . '" ';
        } else {

            $options .= ' rows="10" ';
        }

        //HTML Code
        $html = '<div class="rwf-ui-form-content">' . "\n";

        //Titel
        if ($this->getTitle() != '') {

            $html .= '<div class="rwf-ui-form-content-title">' . String::encodeHTML($this->getTitle()) . ($this->isRequiredField() ? ' <span class="rwf-ui-form-content-required">*</span>' : '') . "</div>\n";
        }

        //Formularfeld
        $html .= '<div class="rwf-ui-form-content-element">';
        $html .= '<textarea type="text" name="' . String::encodeHTML($this->getName()) . '" class="rwf-ui-form-content-textarea' . $class . '" ' . $id . $options . $disabled . ' >' . String::encodeHTML($this->getValue()) . '</textarea>';
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
        $html = '<div class="ui-field-contain' . $class . '">' . "\n";
        $html .= '<label for="a' . $randomId . '">' . String::encodeHTML($this->getTitle()) . ($this->isRequiredField() ? ' <span class="rwf-ui-form-content-required">*</span>' : '') . "</label>\n";

        //Formularfeld
        $html .= '<textarea type="text" name="' . String::encodeHTML($this->getName()) . '" class="rwf-ui-form-content-textarea" ' . $id . $options . $disabled . ' >' . String::encodeHTML($this->getValue()) . '</textarea>';

        //Pflichtfeld
        if($this->isRequiredField() && $this->getValue() == '') {
            
            $html .= '<div class="rwf-ui-form-content-required">'. RWF::getLanguage()->val('form.message.mobile.required') .'</div>';
        } elseif(!$this->isValid) {
            
            $html .= '<div class="rwf-ui-form-content-required">'. RWF::getLanguage()->val('form.message.mobile.invalid') .'</div>';
        }
        
        //Beschreibung
        if ($this->getDescription() != '') {

            $html .= '<div class="rwf-ui-form-content-description">' . String::encodeHTML($this->getDescription()) . '</div>';
        }

        $html .= "</div>\n";

        return $html;
    }

    /**
     * prueft die Eingabedaten auf gueltigkeit
     * 
     * @return Boolean
     */
    public function validate() {

        $valid = true;
        $value = $this->getValue();
        $lang = RWF::getLanguage();
        $lang->disableAutoHtmlEndocde();

        //Pflichtfeld
        if ($this->isRequiredField() && $value == '') {

            $this->messages[] = $lang->get('form.message.requiredField', $this->getTitle());
            $valid = false;
        }

        //Minimale Laenge
        if (isset($this->options['minlength']) && !String::checkLength($value, $this->options['minlength'])) {

            $this->messages[] = $lang->get('form.message.minLength', $this->getTitle(), $this->options['minlength']);
            $valid = false;
        }

        //Maximale Laenge
        if (isset($this->options['maxlength']) && !String::checkLength($value, 0, $this->options['maxlength'])) {

            $this->messages[] = $lang->get('form.message.maxLength', $this->getTitle(), $this->options['minlength']);
            $valid = false;
        }

        if ($valid === false) {

            $this->addClass('rwf-ui-form-content-invalid');
        }
        $this->isValid = $valid;
        $lang->enableAutoHtmlEndocde();
        return $valid;
    }

}
