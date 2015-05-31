<?php

namespace RWF\AVM;

//Imports
use RWF\Core\RWF;


/**
 * Verwaltet das Fritz!Box Objekt
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class FritzBoxFactory {

    /**
     * Fritz Box Objekt
     *
     * @var \RWF\AVM\FritzBox
     */
    protected static $fritzBox = null;

    /**
     * gibt das Fritz!Box Objekt zurueck
     *
     * @return \RWF\AVM\FritzBox
     */
    public static function getFritzBox() {

        if(self::$fritzBox === null) {

            self::$fritzBox = new FritzBox(
                RWF::getSetting('rwf.fritzBox.address'),
                RWF::getSetting('rwf.fritzBox.has5GHzWlan'),
                RWF::getSetting('rwf.fritzBox.user'),
                RWF::getSetting('rwf.fritzBox.password')
            );
        }
        return self::$fritzBox;
    }
}