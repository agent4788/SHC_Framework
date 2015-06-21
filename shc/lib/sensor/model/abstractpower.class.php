<?php

namespace SHC\Sensor\Model;

//Imports
use RWF\Util\String;


/**
 * Stromverbrauch Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
trait AbstractPower {

    /**
     * aktuell entnommene Leistung in mW
     *
     * @var int
     */
    protected $power = 0;

    /**
     * entnommene Leustung Anzeigen
     *
     * @var Integer
     */
    protected $powerVisibility = 1;

    /**
     * gibt den Aktuellen Stromverbrauch zurueck
     *
     * @return Float
     */
    public function getPower() {

        return $this->power;
    }

    /**
     * gibt den Aktuellen Stromverbrauch vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayPower() {

        return ($this->getPower() < 1000000.0 ? String::formatFloat($this->getPower() / 1000, 1) .' W' : String::formatFloat($this->getPower() / 1000 / 1000, 1) .' kW');
    }

    /**
     * setzt die Sichtbarkeit des Stromverbrauchs
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Power
     */
    public function powerVisibility($visibility) {

        $this->powerVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit des Stromverbrauchs an
     *
     * @return Boolean
     */
    public function isPowerVisible() {

        return ($this->powerVisibility == 1 ? true : false);
    }
}