<?php

namespace RWF\Form;

/**
 * Formular Schnittstelle
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Form {

    /**
     * Normale Web Ansicht
     * 
     * @var Integer
     */
    const DEFAULT_VIEW = 1;

    /**
     * Tablet Ansicht
     * 
     * @var Integer
     */
    const TABLET_VIEW = 2;

    /**
     * Smartphone Ansicht Ansicht
     * 
     * @var Integer
     */
    const SMARTPHONE_VIEW = 4;

    /**
     * stellt ein fuer Welche Oberflaeche die Elemente angezeigt werden sollen
     * 
     * @param Integer $view Konstante fuer die jeweilige View
     * @return \RWF\Form\Form
     */
    public function setView($view = self::DEFAULT_VIEW);

    /**
     * gibt die Einstellung der Ansicht zurueck
     * 
     * @return Integer
     */
    public function getView();

    /**
     * setzt die Beschreinung des Formulars
     * 
     * @param String $description Beschreibung
     * @return \RWF\Form\Form
     */
    public function setDescription($description);

    /**
     * gibt die Beschreibung des Formulars zurueck
     * 
     * @return String
     */
    public function getDescription();

    /**
     * setzt das Ziel der Formulardaten
     * 
     * @param String $action Link
     * @return \RWF\Form\Form
     */
    public function setAction($action);

    /**
     * gibt das Ziel der Formulardaten zurueck
     * 
     * @return String Link
     */
    public function getAction();

    /**
     * setzt den Zeichensatz des Formulars
     * 
     * @param String $charset Zeichensatz
     * @return \RWF\Form\Form
     */
    public function setEncoding($charset);

    /**
     * gibt den Formular Zeichensatz zurueck
     * 
     * @return String Zeichensatz
     */
    public function getEncoding();

    /**
     * gibt die Formularbeschreibung als HTML Fragment zurueck
     * 
     * @return String
     */
    public function fetchDescription();

    /**
     * gibt alle Meldungen als HTML Fragment zurueck
     * 
     * @return String
     */
    public function fetchMessages();

    /**
     * gibt das Start Tag als String zurueck
     * 
     * @return String
     */
    public function fetchStartTag();

    /**
     * gibt das End Tag als String zurueck
     * 
     * @return String
     */
    public function fetchEndTag();

    /**
     * registriert ein neues Formular Element im Formular
     * 
     * @param \RWF\Form\FormElement $element Formular Element
     */
    public function addFormElement(FormElement $element);

    /**
     * loescht das Formular Element wieder
     * 
     * @param \RWF\Form\FormElement $element Formular Element
     */
    public function removeFormElement(FormElement $element);

    /**
     * loescht alle Formular Elemente
     */
    public function removeAllElements();

    /**
     * gibt alle Formularelemente zurueck
     * 
     * @return Array Formular Elemente
     */
    public function getAllElements();

    /**
     * gibt das Element mit dem Namen zurueck
     * 
     * @param  String $name Element Name
     * @return \RWF\Form\FormElement
     */
    public function getElementByName($name);

    /**
     * gibt das Element als HTML Fragment zurueck
     * 
     * @param  String $name Element Name
     * @return String       HTML Fragment
     */
    public function fetchElementByName($name);

    /**
     * gibt alle Elmenente als HTML Fragment zurueck
     * 
     * @return String HTML Fragment
     */
    public function fetchAllElements();

    /**
     * validiert die Formulardaten
     * 
     * @return Boolean
     */
    public function validate();

    /**
     * validiert das Eingabefeld mit dem Namen 
     * 
     * @param  String $name Formular Feld Name
     * @return Boolean
     */
    public function validateByName($name);

    /**
     * markiert ein Formular Element als nicht Valide
     * 
     * @param  String  $name    Formular Element Name
     * @param  String  $message Meldung
     * @return Boolean
     */
    public function markElementAsInvalid($name, $message = '');
    
    /**
     * gibt nach dem Validieren ein Array mit den Invalieden Elementen zurueck
     * 
     * @return Array
     */
    public function listInvalidElements();

    /**
     * gibt eine Liste mit allen Formularelementen zurueck
     *
     * @return Array
     */
    public function listFormElements();
    
    /**
     * gibt an ob das Formular abgesendet wurde
     * 
     * @return Boolean
     */
    public function isSubmitted();
}
