<?php

namespace RWF\Form;

//Imports
use RWF\Util\String;
use RWF\Html\AbstractHtmlElement;

/**
 * Basisklasse fuer Formulare
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class AbstractForm extends AbstractHtmlElement implements Form {

    /**
     * ANsicht
     * 
     * @var Integer 
     */
    protected $view = 1;

    /**
     * Beschreibung des Formulars
     * 
     * @var String 
     */
    protected $description = '';

    /**
     * Ziel der Formulardaten
     * 
     * @var String
     */
    protected $action = '';

    /**
     * Zeichensatz
     * 
     * @var String
     */
    protected $encodeing = 'utf-8';

    /**
     * Formular Elemente
     * 
     * @var Array
     */
    protected $elements = array();

    /**
     * Ungueltige Elemente
     * 
     * @var Array
     */
    protected $invalidElements = array();

    /**
     * Meldungsobjekt
     * 
     * @var \RWF\Util\Message
     */
    protected $message = null;

    /**
     * stellt ein fuer Welche Oberflaeche die Elemente angezeigt werden sollen
     * 
     * @param Integer $view Konstante fuer die jeweilige View
     * @return \RWF\Form\Form
     */
    public function setView($view = self::DEFAULT_VIEW) {

        $this->view = $view;
        return $this;
    }

    /**
     * gibt die Einstellung der Ansicht zurueck
     * 
     * @return Integer
     */
    public function getView() {

        return $this->view;
    }

    /**
     * setzt die Beschreinung des Formulars
     * 
     * @param  String $description Beschreibung
     * @return @return \RWF\Form\Form
     */
    public function setDescription($description) {

        $this->description = $description;
        return $this;
    }

    /**
     * gibt die Beschreibung des Formulars zurueck
     * 
     * @return String
     */
    public function getDescription() {

        return $this->description;
    }

    /**
     * setzt das Ziel der Formulardaten
     * 
     * @param  String $action Link
     * @return @return \RWF\Form\Form
     */
    public function setAction($action) {

        $this->action = $action;
        return $this;
    }

    /**
     * gibt das Ziel der Formulardaten zurueck
     * 
     * @return String Link
     */
    public function getAction() {

        return $this->action;
    }

    /**
     * setzt den Zeichensatz des Formulars
     * 
     * @param  String $charset Zeichensatz
     * @return \RWF\Form\Form
     */
    public function setEncoding($charset) {

        $this->encodeing = $charset;
        return $this;
    }

    /**
     * gibt den Formular Zeichensatz zurueck
     * 
     * @return String Zeichensatz
     */
    public function getEncoding() {

        return $this->encodeing;
    }

    /**
     * registriert ein neues Formular Element im Formular
     * 
     * @param  FormElement $element Formular Element
     * @return Form
     */
    public function addFormElement(FormElement $element) {

        $name = String::toLower($element->getName());
        $this->elements[$name] = $element;
        return $this;
    }

    /**
     * loescht das Formular Element wieder
     * 
     * @param  FormElement $element Formular Element
     * @return Form
     */
    public function removeFormElement(FormElement $element) {

        $this->elements = array_diff($this->elements, array($element));
        return $this;
    }

    /**
     * loescht alle Formular Elemente
     * 
     * @return Form
     */
    public function removeAllElements() {

        $this->elements = array();
        return $this;
    }

    /**
     * gibt alle Formularelemente zurueck
     * 
     * @return Array
     */
    public function getAllElements() {

        return $this->elements;
    }

    /**
     * gibt das Element mit dem Namen zurueck
     * 
     * @param  String $name Element Name
     * @return FormElement
     */
    public function getElementByName($name) {

        $name = String::toLower($name);
        if (isset($this->elements[$name])) {

            return $this->elements[$name];
        }
        return null;
    }

    /**
     * gibt das Element als HTML Fragment zurueck
     * 
     * @param  String $name Element Name
     * @return String       HTML FRagment
     */
    public function fetchElementByName($name) {

        $name = String::toLower($name);
        if (isset($this->elements[$name])) {

            return $this->elements[$name]->fetch($this->getView());
        }
        return null;
    }

    /**
     * gibt alle Elmenente als HTML Fragment zurueck
     * 
     * @return String HTML Fragment
     */
    public function fetchAllElements() {

        $html = '';
        foreach ($this->elements as $element) {

            $html .= $element->fetch($this->getView());
        }
        return $html;
    }

    /**
     * validiert die Formulardaten
     * 
     * @return Boolean
     */
    public function validate() {

        $success = true;
        foreach ($this->elements as $element) {

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
     * validiert das Eingabefeld mit dem Namen 
     * 
     * @param  String  $name Formular Feld Name
     * @return Boolean
     */
    public function validateByName($name) {

        $element = $this->getElementByName($name);
        if ($element instanceof FormElement) {

            if (!$element->validate()) {

                $this->invalidElements[String::toLower($element->getName())] = $element;
                $this->message->addSubMessages($element->getMessages());
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * markiert ein Formular Element als nicht Valide
     * 
     * @param  String  $name    Formular Element Name
     * @param  String  $message Meldung
     * @return Boolean
     */
    public function markElementAsInvalid($name, $message = '') {

        $element = $this->getElementByName($name);
        if ($element instanceof FormElement) {

            $element->addClass('rwf-ui-form-content-invalid');
            $this->invalidElements[String::toLower($element->getName())] = $element;

            if ($message != '') {
                $this->message->addSubMessage($message);
            }

            return true;
        }
        return false;
    }

    /**
     * gibt nach dem Validieren ein Array mit den Invalieden Elementen zurueck
     * 
     * @return Array FormElement
     */
    public function listInvalidElements() {

        return $this->invalidElements;
    }

    /**
     * gibt eine Liste mit allen Formularelementen zurueck
     *
     * @return Array
     */
    public function listFormElements() {

        return $this->elements;
    }

    /**
     * gibt an ob das Formular abgesendet wurde
     * 
     * @return Boolean
     */
    public function isSubmitted() {

        if (count($_POST)) {

            return true;
        }
        return false;
    }
}
