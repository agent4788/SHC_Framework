<?php

namespace RWF\AVM;

//Imports


/**
 * Auslesen von Daten und Interagieren mit der Fritz Box
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FritzBox {

    /**
     * Fitz Box Adresse
     *
     * @var string
     */
    protected $address = 'fritz.box';

    /**
     * Benutzername (Fritz Box Benutzer)
     *
     * @var string
     */
    protected $user = '';

    /**
     * Passwort (Fritz Box Benutzer)
     *
     * @var string
     */
    protected $password = '';

    /**
     * gibt an ob die Fritz Box ein 5GHz Wlan Modul hat
     *
     * @var bool
     */
    protected $has5GhzWlan = true;

    /**
     * Cache
     *
     * @var array
     */
    protected $cache = array();

    /**
     * @param string $address      Fritz Box Host oder IP Adresse
     * @param bool   $has5GhzWlan  Fritz Box Host oder IP Adresse
     * @param string $user         Benutzername (Fritz Box Benutzer)
     * @param string $password     Passwort (Fritz Box Benutzer)
     */
    public function __construct($address = 'fritz.box', $has5GhzWlan = true, $user = '', $password = '') {

        $this->address = $address;
        $this->user = $user;
        $this->password = $password;
        $this->has5GhzWlan = $has5GhzWlan;
    }

    /**
     * gibt die aktuelle Session ID zurueck
     *
     * @return string
     * @throws \SoapFault
     */
    protected function getSid() {

        if(!isset($this->cache['sid']['sid'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/deviceconfig",
                    'uri'        => "urn:dslforum-org:service:DeviceConfig:1",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $this->cache['sid']['sid'] = $client->{"X_AVM-DE_CreateUrlSID"}();
        }
        return $this->cache['sid']['sid'];
    }

    /**
     * gibt das Fritz Box Device Objekt zurueck
     *
     * @return \RWF\AVM\FritzBoxDevice
     */
    public function getDevice() {

        if(!isset($this->cache['object']['device'])) {

            $this->cache['object']['device'] = new FritzBoxDevice($this->address, $this->has5GhzWlan, $this->user, $this->password);
        }
        return $this->cache['object']['device'];
    }

    /**
     * gibt das Fritz Box Wlan Objekt zurueck
     *
     * @return \RWF\AVM\FritzBoxWlan
     */
    public function getWlan() {

        if(!isset($this->cache['object']['wlan'])) {

            $this->cache['object']['wlan'] = new FritzBoxWlan($this->address, $this->has5GhzWlan, $this->user, $this->password);
        }
        return $this->cache['object']['wlan'];
    }

    /**
     * gibt das Fritz Box Wan Objekt zurueck
     *
     * @return \RWF\AVM\FritzBoxWan
     */
    public function getWan() {

        if(!isset($this->cache['object']['wan'])) {

            $this->cache['object']['wan'] = new FritzBoxWan($this->address, $this->has5GhzWlan, $this->user, $this->password);
        }
        return $this->cache['object']['wan'];
    }

    /**
     * gibt das Fritz Box Anruflisten Objekt zurueck
     *
     * @return \RWF\AVM\FritzBoxCallList
     */
    public function getCallList() {

        if(!isset($this->cache['object']['call'])) {

            $this->cache['object']['call'] = new FritzBoxCallList($this->address, $this->has5GhzWlan, $this->user, $this->password);
        }
        return $this->cache['object']['call'];
    }

    /**
     * gibt das Fritz Box Phone Objekt zurueck
     *
     * @return \RWF\AVM\FritzBoxPhone
     */
    public function getPhone() {

        if(!isset($this->cache['object']['phone'])) {

            $this->cache['object']['phone'] = new FritzBoxPhone($this->address, $this->has5GhzWlan, $this->user, $this->password);
        }
        return $this->cache['object']['phone'];
    }

    /**
     * gibt das Fritz Box Host Objekt zurueck
     *
     * @return \RWF\AVM\FritzBoxHosts
     */
    public function getHosts() {

        if(!isset($this->cache['object']['host'])) {

            $this->cache['object']['host'] = new FritzBoxHosts($this->address, $this->has5GhzWlan, $this->user, $this->password);
        }
        return $this->cache['object']['host'];
    }

    /**
     * gibt das Fritz Box SmartHome Objekt zurueck
     *
     * @return \RWF\AVM\FritzBoxSmartHome
     */
    public function getSmartHome() {

        if(!isset($this->cache['object']['smarthome'])) {

            $this->cache['object']['smarthome'] = new FritzBoxSmartHome($this->address, $this->has5GhzWlan, $this->user, $this->password);
        }
        return $this->cache['object']['smarthome'];
    }

    /**
     * der Cache wird geloescht und neu erzeugt
     *
     * @return bool
     */
    public function rebuildCache() {

        $this->cache['sid'] = array();
        $this->cache['object'] = array();
    }
}