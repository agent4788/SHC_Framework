<?php

namespace RWF\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Form\AbstractFormElement;
use RWF\Util\String;

/**
 * Check Boxen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class CheckBoxes extends AbstractFormElement {

    /**
     * gibt den Standartwert oder den Eingabewert zurueck
     * 
     * @return Mixed
     */
    public function getValue() {

        return null;
    }

    /**
     * gibt die Standartwerte oder den Eingabewerte zurueck
     * 
     * @return Array 
     */
    public function getValues() {

        $request = RWF::getRequest();
        if ($request->issetParam($this->getName(), Request::POST)) {

            //Daten per POST
            $values = $request->getParam($this->getName(), Request::POST);

            //Leerzeichen entfernen
            foreach ($values as $index => $value) {

                $values[$index] = String::trim($value);
            }

            //Pruefen ob der Wert veraendert wurde
            $isDefault = true;
            foreach ($values as $value) {

                if (!isset($this->values[$value]) || (isset($this->values[$value]) && !is_array($this->values[$value])) || (isset($this->values[$value]) && is_array($this->values[$value]) && $this->values[$value][1] != 1)) {

                    $isDefault = false;
                    break;
                }
            }
            $this->isDefault = $isDefault;
        } else {

            //keine Daten per POST
            $this->isDefault = true;
            $values = array();
            foreach ($this->values as $value) {

                if (is_array($value) && $value[1] = 1) {

                    $values[] = $value[0];
                }
            }
        }

        return $values;
    }

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
        $html .= '<div ' . $id . ' class="rwf-ui-form-content-element-checkboxes' . $class . '">' . "\n";

        $i = 0;
        foreach ($this->values as $value => $index) {

            //Pruefen ob Ausgewaehlt
            $checked = '';
            $inputValue = $this->getValues();
            if (($this->isDefaultValue() && is_array($index) && $index[1] == 1) || (!$this->isDefaultValue() && in_array($value, $inputValue))) {

                $checked = 'checked="checked"';
            }

            $html .= '<input type="checkbox" id="a' . $randomId . '_checkbox_' . $i . '" name="' . String::encodeHTML($this->getName()) . '[]" value="' . String::encodeHTML($value) . '" ' . $checked . $disabled . ' /><label for="a' . $randomId . '_checkbox_' . $i . '">' . (is_array($index) ? String::encodeHTML($index[0]) : String::encodeHTML($index)) . '</label>' . "\n";
            $i++;
        }
        $html .= "</div>\n";
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
                \$('#a" . $randomId . "').buttonset();
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

        //HTML Code
        $html = '<div class="rwf-ui-form-content ui-field-contain">' . "\n";

        //Titel
        $html .= '<fieldset data-role="controlgroup" class="' . $class . '" '. $id .'>' . "\n";
        $html .= '<legend>' . String::encodeHTML($this->getTitle()) . ($this->isRequiredField() ? ' <span class="rwf-ui-form-content-required">*</span>' : '') . "</legend>\n";

        //Formularfeld
        $i = 0;
        foreach ($this->values as $value => $index) {

            //Pruefen ob Ausgewaehlt
            $checked = '';
            $inputValue = $this->getValues();
            if (($this->isDefaultValue() && is_array($index) && $index[1] == 1) || (!$this->isDefaultValue() && in_array($value, $inputValue))) {

                $checked = 'checked="checked"';
            }

            $html .= '<input type="checkbox" id="a' . $randomId . '_checkbox_' . $i . '" name="' . String::encodeHTML($this->getName()) . '[]" value="' . String::encodeHTML($value) . '" ' . $checked . $disabled . ' /><label for="a' . $randomId . '_checkbox_' . $i . '">' . (is_array($index) ? String::encodeHTML($index[0]) : String::encodeHTML($index)) . '</label>' . "\n";
            $i++;
        }
        
        $html .= '</fieldset>' . "\n";

        //Pflichtfeld
        if ($this->isRequiredField() && !count($this->getValue())) {
            
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
        $values = $this->getValues();
        $lang = RWF::getLanguage();
        $lang->disableAutoHtmlEndocde();

        //Pflichtfeld
        if ($this->isRequiredField() && count($values) < 1) {

            $this->messages[] = $lang->get('form.message.requiredField', $this->getTitle());
            $valid = false;
        }

        //Pruefen ob die Werte existieren
        foreach ($values as $value) {

            if (!array_key_exists($value, $this->values)) {

                $this->messages[] = $lang->get('form.message.invalidField', $this->getTitle());
                $valid = false;
                break;
            }
        }

        if ($valid === false) {

            $this->addClass('rwf-ui-form-content-invalid');
        }
        $this->isValid = $valid;
        $lang->enableAutoHtmlEndocde();
        return $valid;
    }

}
