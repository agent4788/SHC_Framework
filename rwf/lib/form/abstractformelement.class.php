<?php

namespace RWF\Form;

//Imports
use RWF\Html\AbstractHtmlElement;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\String;

/**
 * Basisklasse fuer Formular Elemente
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class AbstractFormElement extends AbstractHtmlElement implements FormElement {

    /**
     * Name des Eingabefeldes
     * 
     * @var String
     */
    protected $name = '';

    /**
     * Optionen des Eingabefeldes
     * 
     * @var Array
     */
    protected $options = array();

    /**
     * Titel des Eingabefeldes
     * 
     * @var String
     */
    protected $title = '';

    /**
     * Beschreibung des Eingabefeldes
     * 
     * @var String
     */
    protected $description = '';

    /**
     * Wert des Eingabefeldes
     * 
     * @var String
     */
    protected $value = '';

    /**
     * Werte des Eingabefeldes
     * 
     * @var Array
     */
    protected $values = array();

    /**
     * Pflichtfeld?
     * 
     * @var Boolean
     */
    protected $requiresField = false;

    /**
     * gibt an ob die daten durch eine eingabe veraendert wurden
     * 
     * @var Boolean 
     */
    protected $isDefault = true;

    /**
     * Meldungen
     * 
     * @var Array
     */
    protected $messages = array();
    
    /**
     * gibt an ob die Eingabedaten Vald sind
     * 
     * @var Boolean 
     */
    protected $isValid = true;

    /**
     * @param String $name    Name
     * @param String $value   Wert
     * @param Array  $options Optionen
     */
    public function __construct($name, $value = '', $options = array()) {

        $this->setName($name);
        if (is_array($value)) {

            $this->setValues($value);
        } else {

            $this->setValue($value);
        }
        $this->setOptions($options);
    }

    /**
     * setzt den Namen des Eingabefeldes
     *
     * @param  String $name Name
     * @return \RWF\Form\FormElement
     */
    public function setName($name) {

        $this->name = $name;
        return $this;
    }

    /**
     * gibt den Namen des Eingabefeldes zurueck
     * 
     * @return String
     */
    public function getName() {

        return $this->name;
    }

    /**
     * setzt die Optionen fuer das Eingabefeld
     * 
     * @param  Array $options Optionen
     * @return \RWF\Form\FormElement
     */
    public function setOptions(array $options) {

        $this->options = $options;
        return $this;
    }

    /**
     * gibt die Optionen zurueck
     * 
     * @return Array
     */
    public function getOptions() {

        return $this->options;
    }

    /**
     * setzt den Titel des Eingabefeldes
     * 
     * @param  String $title Titel
     * @return \RWF\Form\FormElement
     */
    public function setTitle($title) {

        $this->title = $title;
        return $this;
    }

    /**
     * gibt den Titel des Eingabefeldes zurueck
     * 
     * @return String
     */
    public function getTitle() {

        return $this->title;
    }

    /**
     * setzt die Beschreibung des Eingabefeldes
     * 
     * @param  String $description Beschreibung
     * @return \RWF\Form\FormElement
     */
    public function setDescription($description) {

        $this->description = $description;
        return $this;
    }

    /**
     * gibt die Beschreibung des Eingabefeldes zurueck
     * 
     * @return String
     */
    public function getDescription() {

        return $this->description;
    }

    /**
     * setzt den Wert des Eingabefeldes
     * 
     * @param  Mixed $value Wert
     * @return \RWF\Form\FormElement
     */
    public function setValue($value) {

        $this->value = $value;
        return $this;
    }

    /**
     * setzt die Werte fuer Eingabefelder die mehrere Werte erlauben
     * array('Index' => array('Wert', 'selected'))
     * array('Index', 'Wert')
     * 
     * @param  Array $values Werte
     * @return \RWF\Form\FormElement
     */
    public function setValues(array $values) {

        $this->values = $values;
        return $this;
    }

    /**
     * setzt das Eingabefeld als Pflichtfeld (es muss beim Eingeben veraendert werden)
     * 
     * @param  Boolean $enabled Pflichtfeld wenn True
     * @return \RWF\Form\FormElement
     */
    public function requiredField($enabled) {

        $this->requiresField = $enabled;
        return $this;
    }

    /**
     * gibt an ob das Eingabefeld ein Pflichtfeld ist
     * 
     * @return Boolean
     */
    public function isRequiredField() {

        if ($this->requiresField == true) {

            return true;
        }
        return false;
    }

    /**
     * gibt true Zurueck wenn die Eingabedaten den Standartwerten entsprechen
     * 
     * @return Boolean
     */
    public function isDefaultValue() {

        return $this->isDefault;
    }

    /**
     * gibt falls Fehler aufgetreten sind eine Liste mit Fehlermeldungen zurueck
     *
     * @return Array
     */
    public function getMessages() {

        return $this->messages;
    }

    /**
     * gibt das Eingabefeld als HTML Fragment zurueck
     * 
     * @param  Integer $view gibt an fuer welche Oberflaeche das Element angezeigt werden soll
     * @return String
     */
    public function fetch($view = Form::DEFAULT_VIEW) {

        if ($view == Form::TABLET_VIEW || $view == Form::SMARTPHONE_VIEW) {

            //Mobile Ansicht
            return $this->fetchMobileView();
        } else {

            //Web Ansicht
            return $this->fetchWebView();
        }
    }

    /**
     * erzeugt das HTML Element fuer die Web View
     * 
     * @return String
     */
    protected abstract function fetchMobileView();

    /**
     * erzeugt das HTML Element fuer die Mobile View
     * 
     * @return String
     */
    protected abstract function fetchWebView();
    
    /**
     * gibt den Standartwert oder den Eingabewert zurueck
     * 
     * @return Mixed
     */
    public function getValue() {

        $request = RWF::getRequest();
        if ($request->issetParam($this->getName(), Request::POST)) {

            //Daten per POST
            $value = String::trim($request->getParam($this->getName(), Request::POST));

            //Pruefen ob 
            if ($value == $this->value) {

                //Daten nicht veraendert
                $this->isDefault = true;
            } else {

                //Daten veraendert
                $this->isDefault = false;
            }
        } else {

            //keine Daten per POST
            $this->isDefault = true;
            $value = $this->value;
        }

        return $value;
    }

    /**
     * gibt die Standartwerte oder den Eingabewerte zurueck
     * 
     * @return Array 
     */
    public function getValues() {

        return array($this->getValue());
    }
}
