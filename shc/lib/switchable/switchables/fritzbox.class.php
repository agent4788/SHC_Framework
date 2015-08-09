<?php

namespace SHC\Switchable\Switchables;

//Imports
use RWF\AVM\FritzBoxFactory;
use RWF\Core\RWF;
use SHC\Switchable\AbstractSwitchable;

/**
 * AVM DECT oder DLAN Steckdose
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FritzBox extends AbstractSwitchable {

    /**
     * WLan 2,4GHz an/aus schalten
     *
     * @var int
     */
    const FB_SWITCH_WLAN_2GHz = 1;

    /**
     * WLan 5GHz an/aus schalten
     *
     * @var int
     */
    const FB_SWITCH_WLAN_5GHz = 2;

    /**
     * WLan Gast an/aus schalten
     *
     * @var int
     */
    const FB_SWITCH_WLAN_Guest = 3;

    /**
     * Fritz!Box neu starten
     *
     * @var int
     */
    const FB_REBOOT = 4;

    /**
     * Internetverbindung neu starten
     *
     * @var int
     */
    const FB_RECONNECT_WAN = 5;

    /**
     * Funktion
     *
     * @var Integer
     */
    protected $function = 0;

    /**
     * @param int $function Funktion
     */
    public function __construct($function = 1) {

        $this->function = $function;
    }

    /**
     * setzt die Funktion
     *
     * @param  int $function Funktion
     * @return \SHC\Switchable\Switchables\RadioSocket
     */
    public function setFunction($function) {

        $this->function = $function;
        return $this;
    }

    /**
     * gibt die Funktion zurueck
     *
     * @return int
     */
    public function getFunction() {

        return $this->function;
    }

    /**
     * gibt den Namen des Elements zurueck
     *
     * @return String
     */
    public function getName() {

        RWF::getLanguage()->loadModul('switchableManagement');
        RWF::getLanguage()->disableAutoHtmlEndocde();
        $return = 'unknown';
        switch($this->function) {

            case self::FB_SWITCH_WLAN_2GHz:

                $return = RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function.wlan1');
                break;
            case self::FB_SWITCH_WLAN_5GHz:

                $return = RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function.wlan2');
                break;
            case self::FB_SWITCH_WLAN_Guest:

                $return = RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function.wlan3');
                break;
            case self::FB_RECONNECT_WAN:

                $return = RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function.reconnect');
                break;
            case self::FB_REBOOT:

                $return = RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.function.reboot');
                break;
        }
        RWF::getLanguage()->enableAutoHtmlEndocde();
        return $return;
    }

    /**
     * gibt den Dateinamen des Icons zurueck
     *
     * @return String
     */
    public function getIcon() {

        switch($this->function) {

            case self::FB_SWITCH_WLAN_2GHz:
            case self::FB_SWITCH_WLAN_5GHz:
            case self::FB_SWITCH_WLAN_Guest:

                return 'shc-icon-wifi';
                break;
            case self::FB_RECONNECT_WAN:

                return 'shc-icon-reconnect';
                break;
            case self::FB_REBOOT:

                return 'shc-icon-reboot';
                break;
        }
        return '';
    }

    /**
     * schaltet das Objekt ein
     *
     * @return Boolean
     */
    public function switchOn() {

        $fb = FritzBoxFactory::getFritzBox();
        switch($this->function) {

            case self::FB_SWITCH_WLAN_2GHz:

                $wlan = $fb->getWlan();
                $wlan->enable2GHzWlan();
                break;
            case self::FB_SWITCH_WLAN_5GHz:

                $wlan = $fb->getWlan();
                $wlan->enable5GHzWlan();
                break;
            case self::FB_SWITCH_WLAN_Guest:

                $wlan = $fb->getWlan();
                $wlan->enableGuestWlan();
                break;
            case self::FB_REBOOT:

                $dev = $fb->getDevice();
                $dev->reboot();
                break;
            case self::FB_RECONNECT_WAN:

                $wan = $fb->getWan();
                $wan->reconnectWan();
                break;
        }

        $this->state = self::STATE_ON;
        $this->stateModified = true;
    }

    /**
     * schaltet das Objekt aus
     *
     * @return Boolean
     */
    public function switchOff() {

        $fb = FritzBoxFactory::getFritzBox();
        switch($this->function) {

            case self::FB_SWITCH_WLAN_2GHz:

                $wlan = $fb->getWlan();
                $wlan->disable2GHzWlan();
                break;
            case self::FB_SWITCH_WLAN_5GHz:

                $wlan = $fb->getWlan();
                $wlan->disable5GHzWlan();
                break;
            case self::FB_SWITCH_WLAN_Guest:

                $wlan = $fb->getWlan();
                $wlan->disableGuestWlan();
                break;
        }

        $this->state = self::STATE_OFF;
        $this->stateModified = true;
    }

}