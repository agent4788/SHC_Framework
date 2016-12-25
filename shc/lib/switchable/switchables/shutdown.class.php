<?php

namespace SHC\Switchable\Switchables;

//Imports
use SHC\Core\SHC;
use SHC\Switchable\AbstractSwitchable;

/**
 * Shutdown
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Shutdown extends AbstractSwitchable {

    /**
     * schaltet das Objekt ein
     *
     * @return Boolean
     */
    public function switchOn() {

        //Datenbank vor dem shutdown speichern
        SHC::getDatabase()->save();

        exec('sudo halt -p');
    }

    /**
     * schaltet das Objekt aus
     *
     * @return Boolean
     */
    public function switchOff() {}

}