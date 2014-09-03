<?php

namespace RWF\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Form\AbstractFormElement;
use RWF\Util\String;

/**
 * Mehrfach Auswahlfeld mit einem Leeren Element
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SelectMultipleWithEmptyElement extends AbstractFormElement {

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
            $inputValues = $request->getParam($this->getName(), Request::POST);

            //Leerzeichen entfernen
            foreach ($inputValues as $index => $value) {

                $inputValues[$index] = String::trim($value);
            }

            //Leeres Element in den Wert Null umwandeln
            if (in_array('null', $inputValues)) {

                $inputValues = array(null);
            }

            //Pruefen ob der Wert veraendert wurde
            $isDefault = true;
            foreach ($this->values as $key => $value) {

                if ((in_array($key, $inputValues) && is_array($value) && $value[1] != 1)         //in Ergebnismenge aber nicht vorher selektiert
                        || (in_array($key, $inputValues) && !is_array($value))                   //in Ergebnismenge aber nicht vorher selektiert
                        || (!in_array($key, $inputValues) && is_array($value) && $value[1] == 1) //Nicht in Ergebninsmenge aber vorher selektiert
                ) {

                    $isDefault = false;
                    break;
                }
            }
            $this->isDefault = $isDefault;
        } else {

            //keine Daten per POST
            $this->isDefault = true;
            $inputValues = array();
            foreach ($this->values as $value) {

                if (is_array($value) && $value[1] = 1) {

                    $inputValues[] = $value[0];
                }
            }
        }

        return $inputValues;
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

        //Size
        $size = '';
        if (isset($this->options['size'])) {

            $size = ' size="' . StringUtil::encodeHTML($this->options['size']) . '" ';
        }

        //HTML Code
        $html = '<div class="rwf-ui-form-content">' . "\n";

        //Titel
        if ($this->getTitle() != '') {

            $html .= '<div class="rwf-ui-form-content-title">' . String::encodeHTML($this->getTitle()) . ($this->isRequiredField() ? ' <span class="rwf-ui-form-content-required">*</span>' : '') . "</div>\n";
        }

        //Formularfeld
        $html .= '<div class="rwf-ui-form-content-element">';
        $html .= '<select name="' . String::encodeHTML($this->getName()) . '[]" multiple="multiple" ' . $id . $disabled . $size . ' class="rwf-ui-form-content-selectmultiple' . $class . '" >' . "\n";

        //Auswahl des leeren Elements
        $selected = '';
        $inputValue = $this->getValues();
        if (($this->isDefaultValue() && isset($this->options['emptySelected']) && $this->options['emptySelected'] == true) || (!$this->isDefaultValue() && $inputValue[0] === null)) {

            $selected = 'selected="selected"';
            if (!$this->isDefaultValue() && $inputValue[0] === null) {

                unset($inputValue[0]);
            }
        }
        $html .= '<option value="null" ' . $selected . '>' . String::encodeHTML((isset($this->options['emptyLabel']) ? $this->options['emptyLabel'] : 'keine')) . '</option>' . "\n";
        if (isset($this->options['grouped']) && $this->options['grouped'] == true) {

            //Gruppierte Auswahl
            foreach ($this->values as $group => $entrys) {

                $html .= '<optgroup label="' . String::encodeHTML($group) . '">' . "\n";
                foreach ($entrys as $value => $index) {

                    //Pruefen ob Ausgewaehlt
                    $selected = '';
                    if (($this->isDefaultValue() && is_array($index) && $index[1] == 1) || (!$this->isDefaultValue() && in_array($value, $inputValue))) {

                        $selected = 'selected="selected"';
                    }

                    $html .= '<option value="' . String::encodeHTML($value) . '" ' . $selected . '>' . String::encodeHTML((is_array($index) ? $index[0] : $index)) . '</option>' . "\n";
                }
                $html .= "</optgroup>\n";
            }
        } else {

            //Nicht Gruppierte Auswahl
            foreach ($this->values as $value => $index) {

                //Pruefen ob Ausgewaehlt
                $selected = '';
                if (($this->isDefaultValue() && is_array($index) && $index[1] == 1) || (!$this->isDefaultValue() && in_array($value, $inputValue))) {

                    $selected = 'selected="selected"';
                }

                $html .= '<option value="' . String::encodeHTML($value) . '" ' . $selected . '>' . String::encodeHTML((is_array($index) ? $index[0] : $index)) . '</option>' . "\n";
            }
        }
        $html .= "</select>\n";
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

        //Size
        $size = '';
        if (isset($this->options['size'])) {

            $size = ' size="' . StringUtil::encodeHTML($this->options['size']) . '" ';
        }

        //HTML Code
        $html = '<div class="rwf-ui-form-content">' . "\n";

        //Formularfeld
        $html .= '<div class="rwf-ui-form-content-element ui-field-contain">';
        $html .= '<label for="a' . $randomId . '">' . String::encodeHTML($this->getTitle()) . ($this->isRequiredField() ? ' <span class="rwf-ui-form-content-required">*</span>' : '') . "</label>\n";

        $html .= '<select name="' . String::encodeHTML($this->getName()) . '[]" multiple="multiple" ' . $id . $disabled . $size . ' class="rwf-ui-form-content-selectmultiple' . $class . '" data-native-menu="false">' . "\n";

        //Auswahl des leeren Elements
        $selected = '';
        $inputValue = $this->getValues();
        if (($this->isDefaultValue() && isset($this->options['emptySelected']) && $this->options['emptySelected'] == true) || (!$this->isDefaultValue() && $inputValue[0] === null)) {

            $selected = 'selected="selected"';
            if (!$this->isDefaultValue() && $inputValue[0] === null) {

                unset($inputValue[0]);
            }
        }
        $html .= '<option value="null" ' . $selected . '>' . String::encodeHTML((isset($this->options['emptyLabel']) ? $this->options['emptyLabel'] : 'keine')) . '</option>' . "\n";
        if (isset($this->options['grouped']) && $this->options['grouped'] == true) {

            //Gruppierte Auswahl
            foreach ($this->values as $group => $entrys) {

                $html .= '<optgroup label="' . String::encodeHTML($group) . '">' . "\n";
                foreach ($entrys as $value => $index) {

                    //Pruefen ob Ausgewaehlt
                    $selected = '';
                    if (($this->isDefaultValue() && is_array($index) && $index[1] == 1) || (!$this->isDefaultValue() && in_array($value, $inputValue))) {

                        $selected = 'selected="selected"';
                    }

                    $html .= '<option value="' . String::encodeHTML($value) . '" ' . $selected . '>' . String::encodeHTML((is_array($index) ? $index[0] : $index)) . '</option>' . "\n";
                }
                $html .= "</optgroup>\n";
            }
        } else {

            //Nicht Gruppierte Auswahl
            foreach ($this->values as $value => $index) {

                //Pruefen ob Ausgewaehlt
                $selected = '';
                if (($this->isDefaultValue() && is_array($index) && $index[1] == 1) || (!$this->isDefaultValue() && in_array($value, $inputValue))) {

                    $selected = 'selected="selected"';
                }

                $html .= '<option value="' . String::encodeHTML($value) . '" ' . $selected . '>' . String::encodeHTML((is_array($index) ? $index[0] : $index)) . '</option>' . "\n";
            }
        }
        $html .= "</select>\n";
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
     * prueft die Eingabedaten auf gueltigkeit
     * 
     * @return Boolean
     */
    public function validate() {

        $valid = true;
        $values = $this->getValues();

        //Pflichtfeld
        if ($this->isRequiredField() && count($values) < 1) {

            $this->messages[] = 'Das Feld ' . String::encodeHTML($this->getTitle()) . ' muss ausgefüllt werden';
            $valid = false;
        }

        //Pruefen ob die Werte existieren
        foreach ($values as $value) {

            if (!array_key_exists($value, $this->values) && $value !== null) {

                $this->messages[] = 'Ungültige Eingaben für ' . String::encodeHTML($this->getTitle());
                $valid = false;
                break;
            }
        }

        if ($valid === false) {

            $this->addClass('rwf-ui-form-content-invalid');
        }
        return $valid;
    }

}
