<?php

namespace SHC\Sensor\Model;

//Imports
use RWF\Util\String;


/**
 * Hoehe Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
trait AbstractAltitude {

    /**
     * Hoehe
     *
     * @var Float
     */
    protected $altitude = 0.0;

    /**
     * Hoehe Anzeigen
     *
     * @var Integer
     */
    protected $altitudeVisibility = 1;

    /**
     * Hoehen Offset
     *
     * @var Float
     */
    protected $altitudeOffset = 0.0;

    /**
     * gibt den Aktuellen Temperaturwert zurueck
     *
     * @return Float
     */
    public function getAltitude() {

        return $this->altitude + $this->altitudeOffset;
    }

    /**
     * gibt den Aktuellen Temperaturwert vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayAltitude() {

        return String::formatFloat($this->getAltitude(), 1) .'m';
    }

    /**
     * setzt das Temperatur Offset
     *
     * @param  Float $temperatureOffset
     * @return \SHC\Sensor\Model\Altitude
     */
    public function setAltitudeOffset($altitudeOffset) {

        $this->altitudeOffset = $altitudeOffset;
        return $this;
    }

    /**
     * gbit das Temperatur Offset zurueck
     *
     * @return Float
     */
    public function getAltitudeOffset() {

        return $this->altitudeOffset;
    }

    /**
     * setzt die Sichtbarkeit der Temperatur
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Altitude
     */
    public function altitudeVisibility($visibility) {

        $this->altitudeVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit der Temperatur an
     *
     * @return Boolean
     */
    public function isAltitudeVisible() {

        return ($this->altitudeVisibility == 1 ? true : false);
    }
}