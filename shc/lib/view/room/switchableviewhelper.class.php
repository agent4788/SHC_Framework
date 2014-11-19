<?php

namespace SHC\View\Room;

//Imports
use RWF\Core\RWF;
use SHC\Core\SHC;
use SHC\Switchable\Switchable;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\ArduinoOutput;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\WakeOnLan;

/**
 * erstellt aus schaltbaren Elementen HTML Fragmente
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchableViewHelper {
    
    /**
     * erstellt das HTML Fragment zur Anzeige eines schaltbaren Elements
     * 
     * @param  \SHC\Switchable\Switchable  $switchable schaltbares Element
     * @param  Booelan                     $ignoreShow Anzeigen trotz abgewahlt
     * @return String
     */
    public static function showSwitchable(Switchable $switchable, $ignoreShow = false) {

        if ($switchable instanceof Activity) {

            return self::showActivity($switchable, $ignoreShow);
        } elseif ($switchable instanceof ArduinoOutput) {

            return self::showArduinoOutput($switchable, $ignoreShow);
        } elseif ($switchable instanceof Countdown) {

            return self::showCountdown($switchable, $ignoreShow);
        } elseif ($switchable instanceof RadioSocket) {

            return self::showRadioSocket($switchable, $ignoreShow);
        } elseif ($switchable instanceof RpiGpioOutput) {

            return self::showRpiGpioOutput($switchable, $ignoreShow);
        } elseif ($switchable instanceof WakeOnLan) {

            return self::showWakeOnLan($switchable, $ignoreShow);
        }
        return '<span>Unbekannter schaltbares Element</span>';
    }
    
    /**
     * bereitet die Daten einer Aktivitaet zur Anzeige vor
     *  
     * @param  \SHC\Switchable\Switchables\Activity $switchable Aktivitaet
     * @param  Boolean                              $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showActivity(Activity $switchable, $ignoreShow = false) {
        
        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $html = $tpl->fetchString('activity.html');
        }
        return $html;
    }
    
    /**
     * bereitet die Daten eines Arduino Ausgangs zur Anzeige vor
     *  
     * @param  \SHC\Switchable\Switchables\ArduinoOutput $switchable ArduinoOutput
     * @param  Boolean                                   $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showArduinoOutput(ArduinoOutput $switchable, $ignoreShow = false) {
        
        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $html = $tpl->fetchString('arduinoOutput.html');
        }
        return $html;
    }
    
    /**
     * bereitet die Daten eines Countdowns zur Anzeige vor
     *  
     * @param  \SHC\Switchable\Switchables\Countdown $switchable Countdown
     * @param  Boolean                               $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showCountdown(Countdown $switchable, $ignoreShow = false) {
        
        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $html = $tpl->fetchString('countdown.html');
        }
        return $html;
    }
    
    /**
     * bereitet die Daten einer Funksteckdose zur Anzeige vor
     *  
     * @param  \SHC\Switchable\Switchables\RadioSocket $switchable Funksteckdose
     * @param  Boolean                                 $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showRadioSocket(RadioSocket $switchable, $ignoreShow = false) {
        
        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $html = $tpl->fetchString('radiosocket.html');
        }
        return $html;
    }
    
    /**
     * bereitet die Daten eines Rpi GPIO AUsgangs zur Anzeige vor
     *  
     * @param  \SHC\Switchable\Switchables\RpiGpioOutput $switchable RpiGpioOutput
     * @param  Boolean                                   $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showRpiGpioOutput(RpiGpioOutput $switchable, $ignoreShow = false) {
        
        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $html = $tpl->fetchString('rpiGpioOutput.html');
        }
        return $html;
    }
    
    /**
     * bereitet die Daten einer WakeOnLan zur Anzeige vor
     *  
     * @param  \SHC\Switchable\Switchables\WakeOnLan $switchable WakeOnLan
     * @param  Boolean                               $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showWakeOnLan(WakeOnLan $switchable, $ignoreShow = false) {
        
        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $html = $tpl->fetchString('wakeOnLan.html');
        }
        return $html;
    }
}
