<?php

namespace SHC\Switchable;

//Imports
use SHC\Timer\SwitchPoint;

/**
 * Schnittstelle eines Schaltbaren Elements
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Switchable extends Element {
    
    /**
     * fuegt einen Schaltpunkt hinzu
     * 
     * @param  \SHC\Timer\SwitchPoint $switchPoint
     * @return \SHC\Switchable\Switchable
     */
    public function addSwitchPoint(SwitchPoint $switchPoint);
    
    /**
     * loescht einen Schaltpunkt
     * 
     * @param  \SHC\Timer\SwitchPoint $switchPoint
     * @return \SHC\Switchable\Switchable
     */
    public function removeSwitchPoint(SwitchPoint $switchPoint);

    /**
     * gibt eine Liste mit allen Schaltpunkten zurueck
     *
     * @return Array
     */
    public function listSwitchPoints();

    /**
     * loescht alle Schaltpunkte
     * 
     * @return \SHC\Switchable\Switchable
     */
    public function removeAllSwitchPoints();

    /**
     * schaltet das Objekt ein
     * 
     * @return Boolean
     */
    public function switchOn();
    
    /**
     * schaltet das Objekt aus
     * 
     * @return Boolean
     */
    public function switchOff();
    
    /**
     * schaltet das Objekt um (in den jeweils gegenteiligen zustand)
     * 
     * @return Boolean
     */
    public function toggle();
    
    /**
     * gibt den aktuellen geschaltenen Zustand zurueck
     * 
     * @return Integer
     */
    public function getState();
    
    /**
     * fuehrt alle anstehenden Schaltbefehle aus und gibt true zurueck wenn eine Aktion ausgefuehrt wurde
     * 
     * @return Boolean
     */
    public function execute();
    
}
