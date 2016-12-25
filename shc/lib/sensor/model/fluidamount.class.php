<?php

namespace SHC\Sensor\Model;

//Imports


/**
 * Menge Schnittstelle (Gas, Wasser ...)
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface FluidAmount {

    /**
     * gibt die Menge zurueck
     *
     * @return Float
     */
    public function getFluidAmount();

    /**
     * gibt die Menge vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayFluidAmount();

    /**
     * setzt die Sichtbarkeit der Menge
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\FluidAmount
     */
    public function fluidAmountVisibility($visibility);

    /**
     * gibt die Sichtbarkeit der Menge an
     *
     * @return Boolean
     */
    public function isFluidAmountVisible();
}