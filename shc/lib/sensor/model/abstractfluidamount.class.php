<?php

namespace SHC\Sensor\Model;

//Imports
use RWF\Util\String;


/**
 * Menge Schnittstelle (Gas, Wasser ...)
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
trait AbstractFluidAmount  {

    /**
     * Menge
     *
     * @var Integer
     */
    protected $fluidAmount = 0;

    /**
     * Menge Anzeigen
     *
     * @var Integer
     */
    protected $fluidAmountVisibility = 1;

    /**
     * gibt die Menge zurueck
     *
     * @return Float
     */
    public function getFluidAmount() {

        return $this->fluidAmount;
    }

    /**
     * gibt die Menge vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayFluidAmount() {

        if($this->getFluidAmount() >= 1000000) {

            return String::formatFloat($this->getFluidAmount() / 1000000, 1) .'m³';
        } elseif($this->getFluidAmount() >= 1000) {

            return String::formatFloat($this->getFluidAmount() / 1000, 1) .'l';
        }
        return String::formatFloat($this->getFluidAmount(), 1) .'ml';
    }

    /**
     * setzt die Sichtbarkeit der Menge
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\FluidAmount
     */
    public function fluidAmountVisibility($visibility) {

        $this->fluidAmountVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit der Menge an
     *
     * @return Boolean
     */
    public function isFluidAmountVisible() {

        return ($this->fluidAmountVisibility == 1 ? true : false);
    }
}