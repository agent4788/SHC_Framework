<?php

namespace SHC\View\Room;

//Imports
use RWF\Core\RWF;
use RWF\Util\String;
use SHC\Switchable\Readable;
use SHC\Switchable\Readables\ArduinoInput;
use SHC\Switchable\Readables\RpiGpioInput;

/**
 * erstellt aus lesbaren Elementen HTML Fragmente
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ReadableViewHelper {

    /**
     * erstellt das HTML Fragment zur Anzeige eines schaltbaren Elements
     * 
     * @param  \SHC\Switchable\Readable  $readable   lesbares Element
     * @param  Booelan                   $ignoreShow Anzeigen trotz abgewahlt
     * @return String
     */
    public static function showReadable(Readable $readable, $ignoreShow = false) {

        if ($readable instanceof ArduinoInput) {

            return self::showArduinoInput($readable, $ignoreShow);
        } elseif ($readable instanceof RpiGpioInput) {

            return self::showRpiGpioInput($readable, $ignoreShow);
        }
        return '<span>Unbekanntes lesbares Element</span>';
    }

    /**
     * bereitet die Daten einer Aktivitaet zur Anzeige vor
     *  
     * @param  \SHC\Switchable\Readables\ArduinoInput $readable ArduinoInput
     * @param  Boolean                              $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showArduinoInput(ArduinoInput $readable, $ignoreShow = false) {

        $html = '';
        if ($readable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($readable->isEnabled() && $readable->isVisible() == Readable::SHOW))) {

            $html = '<div class="shc-contentbox-body-row shc-view-readable">';
            $html .= '<span class="shc-contentbox-body-row-title">'. String::encodeHtml($readable->getName()) .'</span>';
            $html .= '<span class="shc-icon '. ($readable->getState() == Readable::STATE_ON ? 'shc-icon-high' : 'shc-icon-low') . '"></span>';
            $html .= '<div class="shc-contentbox-body-row-content"></div>';
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * bereitet die Daten eines Arduino Ausgangs zur Anzeige vor
     *  
     * @param  \SHC\Switchable\Readables\RpiGpioInput $readable RpiGpioInput
     * @param  Boolean                                   $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showRpiGpioInput(RpiGpioInput $readable, $ignoreShow = false) {

        $html = '';
        if ($readable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($readable->isEnabled() && $readable->isVisible() == Readable::SHOW))) {

            $html = '<div class="shc-contentbox-body-row shc-view-readable">';
            $html .= '<span class="shc-contentbox-body-row-title">'. String::encodeHtml($readable->getName()) .'</span>';
            $html .= '<span class="shc-icon '. ($readable->getState() == Readable::STATE_ON ? 'shc-icon-high' : 'shc-icon-low') . '"></span>';
            $html .= '<div class="shc-contentbox-body-row-content"></div>';
            $html .= '</div>';
        }
        return $html;
    }

}
