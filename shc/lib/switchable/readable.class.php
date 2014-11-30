<?php

namespace SHC\Switchable;

/**
 * Schnittstelle eines Lesbaren Elements
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Readable extends Element {

    /**
     * liest en aktuellen Status des Einganges ein
     *
     * @param Boolean $save gibt an ob der gelesene Status gespeichert werden soll
     */
    public function readState($save = true);

    /**
     * gibt den aktuellen geschaltenen Zustand zurueck
     * 
     * @return Integer
     */
    public function getState();
}
