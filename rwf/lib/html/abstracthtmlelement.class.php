<?php

namespace RWF\Html;

/**
 * Grundeigenschaften fuer alle HTML Elemente
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class AbstractHtmlElement {

    /**
     * IDs
     * 
     * @var Array
     */
    protected $ids = array();

    /**
     * Klassen
     * 
     * @var Array
     */
    protected $classes = array();

    /**
     * Deaktiviert
     * 
     * @var Boolean
     */
    protected $disabled = false;

    /**
     * registriert eine neue ID fuer das Element
     * 
     * @param String $id ID
     */
    public function addId($id) {

        $this->ids[] = $id;
    }

    /**
     * loescht die ID fuer das Element
     * 
     * @param String $id ID
     */
    public function removeId($id) {

        $this->ids = array_diff($this->ids, array($id));
    }

    /**
     * gibt alle IDs fuer das Element zurueck
     * 
     * @return Array
     */
    public function listIds() {

        return $this->ids;
    }

    /**
     * registriert eine neue Klasse fuer das Element
     * 
     * @param String $class Klasse
     */
    public function addClass($class) {

        $this->classes[] = $class;
    }

    /**
     * loescht die Klasse fuer das Element
     * 
     * @param String $class Klasse
     */
    public function removeClass($class) {

        $this->classes = array_diff($this->classes, array($class));
    }

    /**
     * gibt alle Klassen fuer das Element zurueck
     * 
     * @return Array
     */
    public function listClasses() {

        return $this->classes;
    }

    /**
     * deaktiviert das Element
     * 
     * @param Boolean $disabled Dekativiert wenn True
     */
    public function disable($disabled) {

        $this->disabled = $disabled;
    }

    /**
     * gibt an ob das Element deaktiviert ist
     * 
     * @return Boolean
     */
    public function isDisabled() {

        if ($this->disabled == true) {

            return true;
        }
        return false;
    }

}
