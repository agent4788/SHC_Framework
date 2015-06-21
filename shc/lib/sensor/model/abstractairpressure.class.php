<?php

namespace SHC\Sensor\Model;

//Imports
use RWF\Util\String;


/**
 * Luftdruck Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
trait AbstractAirPressure {

    /**
     * Luftdruck
     *
     * @var Float
     */
    protected $airPressure = 0.0;

    /**
     * Luftdruck Anzeigen
     *
     * @var Integer
     */
    protected $airPressureVisibility = 1;

    /**
     * Luftdruck Offset
     *
     * @var Float
     */
    protected $airPressureOffset = 0.0;

    /**
     * gibt den Aktuellen Luftdruck zurueck
     *
     * @return Float
     */
    public function getAirPressure() {

        return $this->airPressure + $this->airPressureOffset;
    }

    /**
     * gibt den Aktuellen Luftdruck vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayAirPressure() {

        return String::formatFloat($this->getAirPressure(), 1) .' hPa';
    }

    /**
     * setzt das Luftdruck Offset
     *
     * @param  Float $airPressureOffset
     * @return \SHC\Sensor\Model\AirPressure
     */
    public function setAirPressureOffset($airPressureOffset) {

        $this->airPressureOffset = $airPressureOffset;
        return $this;
    }

    /**
     * gbit das Luftdruck Offset zurueck
     *
     * @return Float
     */
    public function getAirPressureOffset() {

        return $this->airPressureOffset;
    }

    /**
     * setzt die Sichtbarkeit der Luftdruck
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\AirPressure
     */
    public function airPressureVisibility($visibility) {

        $this->airPressureVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit der Luftdruck an
     *
     * @return Boolean
     */
    public function isAirPressureVisible() {

        return ($this->airPressureVisibility == 1 ? true : false);
    }
}