<?php

namespace RWF\Form;

//Imports
use RWF\Util\String;

/**
 * HTML Formulat in Tabs aufgeteilt
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class TabbedHtmlForm extends DefaultHtmlForm {

    /**
     * Enthaelt die Tabs und zugehoerigen Formular Elemente
     *
     * @var Array
     */
    protected $tabs = array();

    /**
     * CSS ID des Formulars
     *
     * @var String
     */
    protected $formCssId = 'rwf-view-form-tabed';

    /**
     * setzt die CSS ID des Formulars
     *
     * @param String $formCssId
     */
    public function setFormCssId($formCssId) {

        $this->formCssId = $formCssId;
    }

    /**
     * gibt die CSS ID des Formulars zurueck
     *
     * @return String
     */
    public function getFormCssId() {

        return $this->formCssId;
    }

    /**
     * erstellt ein neues Tab
     *
     * @param  String  $tabName     Name des Tabs
     * @param  String  $title       Angezeigter Titel
     * @param  String  $description Beschreibung des Tabs
     * @return Boolean
     */
    public function addTab($tabName, $title, $description = '') {

        if (!isset($this->tabs[$tabName])) {

            $this->tabs[$tabName] = array(
                'name' => $tabName,
                'title' => $title,
                'description' => $description,
                'elements' => array()
            );

            return true;
        }
        return false;
    }

    /**
     * entfern ein Tab mit allen EIngabefeldern vom Formular
     *
     * @param  String  $tabName Name des Tabs
     * @return Boolean
     */
    public function removeTab($tabName) {

        if (isset($this->tabs[$tabName])) {

            unset($this->tabs[$tabName]);
            return true;
        }
        return false;
    }

    /**
     * entfernt alle Tabs und Formular Elemente
     */
    public function removeAllTabs() {

        $this->tabs = array();
    }

    /**
     * gibt das Element mit dem Namen zurueck
     *
     * @param  String $name Element Name
     * @return FormElement
     */
    public function getElementByName($name) {

        foreach ($this->tabs as $tab) {

            if (isset($tab['elements'][$name])) {

                return $tab['elements'][$name];
            }
        }
        return null;
    }

    /**
     * gibt alle Formularelemente zurueck
     *
     * @return Array
     */
    public function getAllElements() {

        $return = array();
        foreach ($this->tabs as $tab) {

            if (isset($tab['elements'])) {

                $return = array_merge($return, $tab['elements']);
            }
        }
        return $return;
    }

    /**
     * gibt alle Formular Elemente eines Tabs zurueck
     *
     * @param  String $tabName Tab Name
     * @return Array
     */
    public function getAllElementsFromTab($tabName) {

        if (isset($this->tabs[$tabName]['elements'])) {

            return $this->tabs[$tabName]['elements'];
        }
        return array();
    }

    /**
     * fuegt einem Tab ein neues Formular Element hinzu
     *
     * @param  String      $tabName Tab Name
     * @param  FormElement $element Formular Element
     * @return Boolean
     */
    public function addFormElementToTab($tabName, FormElement $element) {

        if (isset($this->tabs[$tabName]['elements'])) {

            $this->tabs[$tabName]['elements'][$element->getName()] = $element;
            return true;
        }
        return false;
    }

    /**
     * entfernt aus einem Tab ein Formular Element
     *
     * @param  String      $tabName Tab Name
     * @param  FormElement $element Formular Element
     * @return Boolean
     */
    public function removeFormElementFromTab($tabName, FormElement $element) {

        if (isset($this->tabs[$tabName]['elements'][$element->getName()])) {

            unset($this->tabs[$tabName]['elements'][$element->getName()]);
            return true;
        }
        return false;
    }

    /**
     * entfernt alle Formular Elemente aus dem Tab
     *
     * @param  String  $tabName Tab Name
     * @return Boolean
     */
    public function removeFormElementsFromTab($tabName) {

        if (isset($this->tabs[$tabName]['elements'])) {

            unset($this->tabs[$tabName]['elements']);
            return true;
        }
        return false;
    }

    /**
     * entfernt alle Tabs und Formular Elemente
     */
    public function removeAll() {

        $this->tabs = array();
    }

    /**
     * gibt das Container Tart Tag als HTML Fragment zrueck
     *
     * @return String
     */
    public function fetchTabContainerStart() {

        //Standard ID fuer Tab Formulare
        $html = '<div id="'. $this->formCssId .'">';
        return $html . "\n";
    }

    /**
     * gibt das Container Tart Tag als HTML Fragment zrueck
     *
     * @return String
     */
    public function fetchTabContainerEnd() {

        return "</div>\n";
    }

    /**
     * gibt das JavaScript fuer die Tabs als HTML Fragment zurueck
     *
     * @return String
     */
    public function fetchContainerJavaScript() {

        //JavaScript ueberpruefung
        $html = "<script type=\"text/javascript\">\n";
        $html .= "
            \$(function() {
                \$('#". $this->formCssId ."').tabs();
            });\n";
        $html .= "</script>\n";
        return $html;
    }

    /**
     * gibt die Tab Beschreibung als HTML Fragment zurueck
     *
     * @param  String $tabName Tab Name
     * @return String
     */
    public function fetchTabDescription($tabName) {

        if (isset($this->tabs[$tabName]['description'])) {

            return '<div class="rwf-ui-form-description-text">' . String::encodeHTML($this->tabs[$tabName]['description']) . '</div>' . "\n";
        }
        return '';
    }

    /**
     * gibt die Liste aller Tabs als HTML Fragment zurueck
     *
     * @return String
     */
    public function fetchTabList() {

        $html = "<ul>\n";
        foreach ($this->tabs as $tabName => $content) {

            $html .= '<li><a href="#rwf-view-form-tab_' . String::encodeHTML($tabName) . '">' . String::encodeHTML($content['title']) . '</a></li>' . "\n";
        }
        $html .= "</ul>\n";
        return $html;
    }

    /**
     * gibt das Tab Start Element als HTML Fragment zurueck
     *
     * @param  String $tabName Tab Name
     * @return String
     */
    public function fetchTabStart($tabName) {

        return '<div id="rwf-view-form-tab_' . String::encodeHTML($tabName) . '">' . "\n";
    }

    /**
     * gibt das Tab End Element als HTML Fragment zurueck
     *
     * @return String
     */
    public function fetchTabEnd() {

        return "</div>\n";
    }

    /**
     * gibt alle Formular Elemente eine Tabs als HTML Fragment zurueck
     *
     * @param  String $tabName Tab Name
     * @return String
     */
    public function fetchAllFormElementsFromTab($tabName) {

        $html = '';
        if (isset($this->tabs[$tabName])) {

            foreach ($this->tabs[$tabName]['elements'] as $element) {

                /* @var $element FormElement */
                $html .= $element->fetch();
            }
        }
        return $html;
    }

    /**
     * gibt ein komplettes Tab als HTML Fragment zurueck
     *
     * @param  String $tabName Tab Name
     * @return String
     */
    public function fetchTab($tabName) {

        $html = $this->fetchTabStart($tabName);
        $html .= $this->fetchTabDescription($tabName);
        $html .= $this->fetchAllFormElementsFromTab($tabName);
        $html .= $this->fetchTabEnd();
        return $html;
    }

    /**
     * gibt das komplette Formular als HTML Fragment zurueck
     *
     * @return String
     */
    public function showForm() {

        $html = $this->fetchStartTag();
        if ($this->description != '') {

            $html .= $this->fetchDescription();
        }
        $html .= $this->fetchMessages();
        $html .= $this->fetchTabContainerStart();
        $html .= $this->fetchTabList();
        foreach ($this->tabs as $tabName => $content) {

            $html .= $this->fetchTab($tabName);
        }
        $html .= $this->fetchTabContainerEnd();
        $html .= $this->fetchEndTag();
        $html .= $this->fetchContainerJavaScript();
        return $html;
    }

    /**
     * validiert die Formulardaten
     *
     * @return Boolean
     */
    public function validate() {

        $elements = $this->getAllElements();
        $success = true;
        foreach ($elements as $element) {

            /* @var $element FormElement */
            if (!$element->validate()) {

                $this->invalidElements[String::toLower($element->getName())] = $element;
                $this->message->addSubMessages($element->getMessages());
                $success = false;
            }
        }
        return $success;
    }

    /**
     * validiert alle Formular Elemente eines Tabs
     *
     * @param  String  $tabName Tab Name
     * @return Boolean
     */
    public function validateTab($tabName) {

        $elements = $this->getAllElementsFromTab($tabName);
        $success = true;
        foreach ($elements as $element) {

            /* @var $element FormElement */
            if (!$element->validate()) {

                $this->invalidElements[String::toLower($element->getName())] = $element;
                $this->message->addSubMessages($element->getMessages());
                $success = false;
            }
        }
        return $success;
    }
}
