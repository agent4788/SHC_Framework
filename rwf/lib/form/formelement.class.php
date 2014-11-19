<?php

namespace RWF\Form;

/**
 * Formular Element
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface FormElement {

    /**
     * setzt den Namen des Eingabefeldes
     * 
     * @param  String $name Name
     * @return \RWF\Form\FormElement
     */
    public function setName($name);

    /**
     * gibt den Namen des Eingabefeldes zurueck
     * 
     * @return String
     */
    public function getName();

    /**
     * setzt die Optionen fuer das Eingabefeld
     * 
     * @param  Array $options Optionen
     * @return \RWF\Form\FormElement
     */
    public function setOptions(array $options);

    /**
     * gibt die Optionen zurueck
     * 
     * @return Array Optionen
     */
    public function getOptions();

    /**
     * setzt den Titel des Eingabefeldes
     * 
     * @param  String $title Titel
     * @return \RWF\Form\FormElement
     */
    public function setTitle($title);

    /**
     * gibt den Titel des EIngabefeldes zurueck
     * 
     * @return String
     */
    public function getTitle();

    /**
     * setzt die Beschreibung des Eingabefeldes
     * 
     * @param  String $description Beschreibung
     * @return \RWF\Form\FormElement
     */
    public function setDescription($description);

    /**
     * gibt die Beschreibung des Eingabefeldes zurueck
     * 
     * @return String
     */
    public function getDescription();

    /**
     * setzt den Wert des Eingabefeldes
     * 
     * @param  Mixed $value Wert
     * @return \RWF\Form\FormElement
     */
    public function setValue($value);

    /**
     * gibt den Standartwert oder den Eingabewert zurueck
     * 
     * @return Mixed
     */
    public function getValue();

    /**
     * setzt die Werte fuer Eingabefelder die mehrere Werte erlauben
     * array('Index' => array('Wert', 'selected'))
     * array('Index', 'Wert')
     * 
     * @param  Array $values Werte
     * @return \RWF\Form\FormElement
     */
    public function setValues(array $values);

    /**
     * gibt die Standartwerte oder den Eingabewerte zurueck
     * 
     * @return Array 
     */
    public function getValues();

    /**
     * gibt true Zurueck wenn die Eingabedaten den Standartwerten entsprechen
     * 
     * @return Boolean
     */
    public function isDefaultValue();

    /**
     * setzt das Eingabefeld als Pflichtfeld (es muss beim Eingeben veraendert werden)
     * 
     * @param  Boolean $enabled Pflichtfeld wenn True
     * @return \RWF\Form\FormElement
     */
    public function requiredField($enabled);

    /**
     * gibt an ob das Eingabefeld ein Pflichtfeld ist
     * 
     * @return Boolean
     */
    public function isRequiredField();

    /**
     * gibt falls Fehler aufgetreten sind diese als Liste zurueck
     *
     * @return Array
     */
    public function getMessages();

    /**
     * gibt das Eingabefeld als HTML Fragment zurueck
     * 
     * @param  Integer $view gibt an fuer welche Oberflaeche das Element angezeigt werden soll
     * @return String
     */
    public function fetch($view = Form::DEFAULT_VIEW);

    /**
     * prueft die Eingabedaten auf gueltigkeit
     * 
     * @return Boolean
     */
    public function validate();
}
