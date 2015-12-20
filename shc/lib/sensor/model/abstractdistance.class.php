<?php

namespace SHC\Sensor\Model;

//Imports
use RWF\Util\String;


/**
 * Entfernung Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
trait AbstractDistance  {

    /**
     * Entfernung
     *
     * @var float
     */
    protected $distance = 0;

    /**
     * Entfernung Anzeigen
     *
     * @var Integer
     */
    protected $distanceVisibility = 1;

    /**
     * Entfernung Offset
     *
     * @var Float
     */
    protected $distanceOffset = 0.0;

    /**
     * gibt die Entfernung zurueck
     *
     * @return Float
     */
    public function getDistance() {

        return $this->distance + $this->distanceOffset;
    }

    /**
     * gibt die Entfernung vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayDistance() {

        $dist = $this->getDistance() + $this->getDistanceOffset();
        if($dist >= 1000000) {

            return String::formatFloat($dist / 1000000, 1) .'km';
        } elseif($dist >= 1000) {

            return String::formatFloat($dist / 1000, 1) .'m';
        } elseif($dist >= 10) {

            return String::formatFloat($dist / 10, 1) .'cm';
        }
        return String::formatFloat($dist, 1) .'mm';
    }

    /**
     * setzt das Entfernungs Offset
     *
     * @param  Float $distanceOffset
     * @return \SHC\Sensor\Model\Distance
     */
    public function setDistanceOffset($distanceOffset) {

        $this->distanceOffset = $distanceOffset;
        return $this;
    }

    /**
     * gbit das Entfernungs Offset zurueck
     *
     * @return Float
     */
    public function getDistanceOffset() {

        return $this->distanceOffset;
    }

    /**
     * setzt die Sichtbarkeit der Entfernung
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Distance
     */
    public function distanceVisibility($visibility) {

        $this->distanceVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit der Entfernung an
     *
     * @return Boolean
     */
    public function isDistanceVisible() {

        return ($this->distanceVisibility == 1 ? true : false);
    }
}