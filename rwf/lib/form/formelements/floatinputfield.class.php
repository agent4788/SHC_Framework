<?php

namespace RWF\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\AbstractFormElement;
use RWF\Util\String;

/**
 * Eingabefeld fuer Gleitpunktzahlen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FloatInputField extends AbstractFormElement {

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

        //HTML Code
        $html = '<div class="rwf-ui-form-content">' . "\n";

        //Titel
        if ($this->getTitle() != '') {

            $html .= '<div class="rwf-ui-form-content-title">' . String::encodeHTML($this->getTitle()) . ($this->isRequiredField() ? ' <span class="rwf-ui-form-content-required">*</span>' : '') . "</div>\n";
        }

        //Formularfeld
        $html .= '<div class="rwf-ui-form-content-element">';
        $html .= '<input type="text" name="' . String::encodeHTML($this->getName()) . '" class="rwf-ui-form-content-integerinputfield' . $class . '" value="' . String::encodeHTML($this->getValue()) . '" ' . $id . $disabled . ' />';
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
                \$('#a" . $randomId . "').spinner({
                    min: " . (isset($this->options['min']) ? $this->options['min'] : 0.0) . ",
                    max: " . (isset($this->options['max']) ? $this->options['max'] : 100.0) . ",
                    step: " . (isset($this->options['step']) ? $this->options['step'] : 0.5) . ",
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
        if (isset($this->options['min'])) {

            $options .= ' min="' . $this->options['min'] . '" ';
        }
        if (isset($this->options['max'])) {

            $options .= ' max="' . $this->options['max'] . '" ';
        }
        if (isset($this->options['step'])) {

            $options .= ' step="' . $this->options['step'] . '" ';
        }

        //HTML Code
        $html = '<div class="rwf-ui-form-content">' . "\n";

        //Titel
        $html .= '<div class="ui-field-contain' . $class . '">' . "\n";
        $html .= '<label for="a' . $randomId . '">' . String::encodeHTML($this->getTitle()) . ($this->isRequiredField() ? ' <span class="rwf-ui-form-content-required">*</span>' : '') . "</label>\n";

        //Formularfeld
        $html .= '<input type="text" pattern="[0-9\.]*" name="' . String::encodeHTML($this->getName()) . '" class="rwf-ui-form-content-integerinputfield" value="' . String::encodeHTML($this->getValue()) . '" ' . $id . $options . $disabled . ' />';

        $html .= "</div>";

        //Pflichtfeld
        if ($this->isRequiredField() && $this->getValue() == '' && !$this->isDefaultValue()) {
            
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

        //Minimalwert
        if ((isset($this->options['min']) && $value < $this->options['min']) || (!isset($this->options['min']) && $value < 0)) {

            $this->messages[] = $lang->get('form.message.mivValueFloat', $this->getTitle(), $this->options['min']);
            $valid = false;
        }

        //Maximalwert
        if ((isset($this->options['max']) && $value > $this->options['max']) || (!isset($this->options['max']) && $value > 100)) {

            $this->messages[] = $lang->get('form.message.maxValueFloat', $this->getTitle(), $this->options['max']);
            $valid = false;
        }

        //Schritte
        $absValue = abs($value) * 1000000;
        $step = (isset($this->options['step']) ? $this->options['step'] : 0.5) * 1000000;
        if ((isset($this->options['step']) && $absValue % $step) || (!isset($this->options['step']) && floor(abs($value) / 0.5) != abs($value) / 0.5)) {

            $this->messages[] = $lang->get('form.message.stepsFloat', $this->getTitle(), (isset($this->options['step']) ? $this->options['step'] : 0.5));
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
