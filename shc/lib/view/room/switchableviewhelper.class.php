<?php

namespace SHC\View\Room;

//Imports
use RWF\Core\RWF;
use SHC\Core\SHC;
use SHC\Switchable\Switchable;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\AvmSocket;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\EdimaxSocket;
use SHC\Switchable\Switchables\FritzBox;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\Reboot;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\Script;
use SHC\Switchable\Switchables\Shutdown;
use SHC\Switchable\Switchables\VirtualSocket;
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
     * Raum ID
     *
     * @var Integer
     */
    protected static $roomId = 0;

    /**
     * erstellt das HTML Fragment zur Anzeige eines schaltbaren Elements
     *
     * @param  Integer                     $roomId     Raum ID
     * @param  \SHC\Switchable\Switchable  $switchable schaltbares Element
     * @param  bool                        $ignoreShow Anzeigen trotz abgewahlt
     * @return String
     */
    public static function showSwitchable($roomId, Switchable $switchable, $ignoreShow = false) {

        self::$roomId = $roomId;
        if ($switchable instanceof Activity) {

            return self::showActivity($switchable, $ignoreShow);
        } elseif ($switchable instanceof Countdown) {

            return self::showCountdown($switchable, $ignoreShow);
        } elseif ($switchable instanceof RadioSocket) {

            return self::showRadioSocket($switchable, $ignoreShow);
        } elseif ($switchable instanceof RpiGpioOutput) {

            return self::showRpiGpioOutput($switchable, $ignoreShow);
        } elseif ($switchable instanceof WakeOnLan) {

            return self::showWakeOnLan($switchable, $ignoreShow);
        } elseif ($switchable instanceof Reboot) {

            return self::showReboot($switchable, $ignoreShow);
        } elseif ($switchable instanceof Shutdown) {

            return self::showShutdown($switchable, $ignoreShow);
        } elseif ($switchable instanceof Script) {

            return self::showScript($switchable, $ignoreShow);
        } elseif ($switchable instanceof AvmSocket) {

            return self::showAvmSocket($switchable, $ignoreShow);
        } elseif ($switchable instanceof FritzBox) {

            return self::showFritzBox($switchable, $ignoreShow);
        } elseif ($switchable instanceof EdimaxSocket) {

            return self::showEdimanxSocket($switchable, $ignoreShow);
        } elseif ($switchable instanceof VirtualSocket) {

            return self::showVirtualSocket($switchable, $ignoreShow);
        }
        return '<span>Unbekanntes schaltbares Element</span>';
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
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileActivity.html');
            } else {
                    //Web Ansicht
                $html = $tpl->fetchString('activity.html');
            }
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
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileCountdown.html');
            } else {
                //Web Ansicht
                $html = $tpl->fetchString('countdown.html');
            }
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
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileRadiosocket.html');
            } else {
                //Web Ansicht
                $html = $tpl->fetchString('radiosocket.html');
            }
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
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileRpiGpioOutput.html');
            } else {
                //Web Ansicht
                $html = $tpl->fetchString('rpiGpioOutput.html');
            }
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
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileWakeOnLan.html');
            } else {
                //Web Ansicht
                $html = $tpl->fetchString('wakeOnLan.html');
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten einer Neustar zur Anzeige vor
     *
     * @param  \SHC\Switchable\Switchables\Reboot $switchable Neustart
     * @param  Boolean                            $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showReboot(Reboot $switchable, $ignoreShow = false) {

        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileReboot.html');
            } else {
                //Web Ansicht
                $html = $tpl->fetchString('reboot.html');
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten einer Shutdown zur Anzeige vor
     *
     * @param  \SHC\Switchable\Switchables\Shutdown $switchable Herunterfahren
     * @param  Boolean                              $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showShutdown(Shutdown $switchable, $ignoreShow = false) {

        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileShutdown.html');
            } else {
                //Web Ansicht
                $html = $tpl->fetchString('shutdown.html');
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten einer Script zur Anzeige vor
     *
     * @param  \SHC\Switchable\Switchables\Script $switchable Script
     * @param  Boolean                            $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showScript(Script $switchable, $ignoreShow = false) {

        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileScript.html');
            } else {
                //Web Ansicht
                $html = $tpl->fetchString('script.html');
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten einer AVM Steckdose zur Anzeige vor
     *
     * @param  \SHC\Switchable\Switchables\AvmSocket $switchable AVM Socket
     * @param  Boolean                               $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showAvmSocket(AvmSocket $switchable, $ignoreShow = false) {

        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileAvmSocket.html');
            } else {
                //Web Ansicht
                $html = $tpl->fetchString('avmSocket.html');
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten einer Script zur Anzeige vor
     *
     * @param  \SHC\Switchable\Switchables\FritzBox $switchable FritzBox
     * @param  Boolean                              $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showFritzBox(FritzBox $switchable, $ignoreShow = false) {

        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileFritzBox.html');
            } else {
                //Web Ansicht
                $html = $tpl->fetchString('fritzBox.html');
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten einer AVM Steckdose zur Anzeige vor
     *
     * @param  \SHC\Switchable\Switchables\EdimaxSocket $switchable AVM Socket
     * @param  Boolean                                  $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showEdimanxSocket(EdimaxSocket $switchable, $ignoreShow = false) {

        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileEdimaxSocket.html');
            } else {
                //Web Ansicht
                $html = $tpl->fetchString('edimaxSocket.html');
            }
        }
        return $html;
    }

    /**
     * bereitet die Daten einer AVM Steckdose zur Anzeige vor
     *
     * @param  \SHC\Switchable\Switchables\VirtualSocket $switchable AVM Socket
     * @param  Boolean                                   $ignoreShow Anzeigeeinstellungen ignorieren
     * @return String
     */
    protected static function showVirtualSocket(VirtualSocket $switchable, $ignoreShow = false) {

        $html = '';
        if ($switchable->isUserEntitled(RWF::getVisitor()) && ($ignoreShow == true || ($switchable->isEnabled() && $switchable->isVisible() == Switchable::SHOW))) {

            $tpl = SHC::getTemplate();
            $tpl->assign('switchable', $switchable);
            $tpl->assign('roomId', self::$roomId);
            $tpl->assign('device', SHC_DETECTED_DEVICE);
            if(defined('RWF_DEVICE') && (RWF_DEVICE == 'smartphone' || RWF_DEVICE == 'tablet')) {

                //Mobil Ansicht
                $html = $tpl->fetchString('mobileVirtualSocket.html');
            } else {
                //Web Ansicht
                $html = $tpl->fetchString('virtualSocket.html');
            }
        }
        return $html;
    }
}
