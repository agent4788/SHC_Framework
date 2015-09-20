<?php

namespace RWF\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\AbstractFormElement;
use RWF\Util\String;

/**
 * Auswahlfeld
 * 
 * Optionen
 * Integer size    legt fest wieviele Optionen gleichzeigig Angezeigt werden sollen
 * Boolean grouped gibt an ob die Auswahlmoeglichkeiten Gruppiert sind
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Select extends AbstractFormElement {

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

            $size = ' size="' . String::encodeHTML($this->options['size']) . '" ';
        }

        //HTML Code
        $html = '<div class="rwf-ui-form-content">' . "\n";

        //Titel
        if ($this->getTitle() != '') {

            $html .= '<div class="rwf-ui-form-content-title">' . String::encodeHTML($this->getTitle()) . ($this->isRequiredField() ? ' <span class="rwf-ui-form-content-required">*</span>' : '') . "</div>\n";
        }

        //Formularfeld
        $html .= '<div class="rwf-ui-form-content-element">';
        $html .= '<select name="' . String::encodeHTML($this->getName()) . '" ' . $id . $disabled . $size . ' class="rwf-ui-form-content-select' . $class . '" >' . "\n";
        if (isset($this->options['grouped']) && $this->options['grouped'] == true) {

            //Gruppierte Auswahl
            foreach ($this->values as $group => $entrys) {

                $html .= '<optgroup label="' . String::encodeHTML($group) . '">' . "\n";
                foreach ($entrys as $value => $index) {

                    //Pruefen ob Ausgewaehlt
                    $selected = '';
                    $inputValue = $this->getValues();
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
                $inputValue = $this->getValues();
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

            $size = ' size="' . String::encodeHTML($this->options['size']) . '" ';
        }

        //HTML Code
        $html = '<div class="rwf-ui-form-content">' . "\n";

        //Formularfeld
        $html .= '<div class="ui-field-contain">';
        if ($this->getTitle() != '') {

            $html .= '<label for="a' . $randomId . '">' . String::encodeHTML($this->getTitle()) . ($this->isRequiredField() ? ' <span class="rwf-ui-form-content-required">*</span>' : '') . "</label>\n";
        }

        $html .= '<select name="' . String::encodeHTML($this->getName()) . '" ' . $id . $disabled . $size . ' class="' . $class . '" data-native-menu="false">' . "\n";
        if (isset($this->options['grouped']) && $this->options['grouped'] == true) {

            //Gruppierte Auswahl
            foreach ($this->values as $group => $entrys) {

                $html .= '<optgroup label="' . String::encodeHTML($group) . '">' . "\n";
                foreach ($entrys as $value => $index) {

                    //Pruefen ob Ausgewaehlt
                    $selected = '';
                    $inputValue = $this->getValues();
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
                $inputValue = $this->getValues();
                if (($this->isDefaultValue() && is_array($index) && $index[1] == 1) || (!$this->isDefaultValue() && in_array($value, $inputValue))) {

                    $selected = 'selected="selected"';
                }

                $html .= '<option value="' . String::encodeHTML($value) . '" ' . $selected . '>' . String::encodeHTML((is_array($index) ? $index[0] : $index)) . '</option>' . "\n";
            }
        }
        $html .= "</select>\n";
        $html .= "</div>\n";
        
        //Pflichtfeld
        if ($this->isRequiredField() && $this->getValue() == ''  && !$this->isDefaultValue()) {
            
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

        //Pruefen ob der Wert existiert
        if(isset($this->options['grouped']) && $this->options['grouped'] == true) {

            //Gruppiert
            $found = false;
            foreach ($this->values as $group => $entrys) {

                foreach ($entrys as $entryValue => $index) {

                    if ($value == $entryValue) {

                        $found = true;
                        break;
                    }
                }
            }

            if ($found === false) {

                $this->messages[] = $lang->get('form.message.invalidField', $this->getTitle());
                $valid = false;
            }
        } else {

            //nicht Gruppiert
            if (!array_key_exists($value, $this->values)) {

                $this->messages[] = $lang->get('form.message.invalidField', $this->getTitle());
                $valid = false;
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
