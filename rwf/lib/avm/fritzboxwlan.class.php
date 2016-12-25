<?php

namespace RWF\AVM;

//Imports


/**
 * Auslesen der Fritz Box Wlandaten
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FritzBoxWlan extends FritzBox {

    /**
     * gibt den Status des ersten WLANs zurueck
     * das ist immer das 2,4GHz WLAN
     *
     * @return array
     * @throws \SoapFault
     */
    protected function getWlan1State() {

        if(!isset($this->cache['wlan']['wlan1'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/wlanconfig1",
                    'uri'        => "urn:dslforum-org:service:WLANConfiguration:1",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $this->cache['wlan']['wlan1'] = $client->GetInfo();
        }
        return $this->cache['wlan']['wlan1'];
    }

    /**
     * gibt den Status des zweiten WLANs zurueck
     * das ist wenn der Router ein 5GHz WLAN hat das 5GHz WLAN und wenn nicht das Gäste WLAN
     *
     * @return array
     * @throws \SoapFault
     */
    protected function getWlan2State() {

        if(!isset($this->cache['wlan']['wlan2'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/wlanconfig2",
                    'uri'        => "urn:dslforum-org:service:WLANConfiguration:2",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $this->cache['wlan']['wlan2'] = $client->GetInfo();
        }
        return $this->cache['wlan']['wlan2'];
    }

    /**
     * gibt den Status des dritten WLANs zurueck
     * das ist immer das Gast WLAN (wenn vorhanden)
     *
     * @return array
     * @throws \SoapFault
     */
    protected function getWlan3State() {

        if(!isset($this->cache['wlan']['wlan3'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/wlanconfig3",
                    'uri'        => "urn:dslforum-org:service:WLANConfiguration:3",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $this->cache['wlan']['wlan3'] = $client->GetInfo();
        }
        return $this->cache['wlan']['wlan3'];
    }

    /**
     * gibt an ob das WLAN aktiv ist
     *
     * @return bool
     */
    public function isGuestWlanEnabled() {

        if($this->has5GhzWlan == true) {

            $info = $this->getWlan3State();
        } else {

            $info = $this->getWlan2State();
        }
        return ((int) $info['NewEnable'] == 1 ? true : false);
    }

    /**
     * gibt die MAC Adresse des WLANs zurueck
     *
     * @return string
     */
    public function getGuestWlanMacAddress() {

        if($this->has5GhzWlan == true) {

            $info = $this->getWlan3State();
        } else {

            $info = $this->getWlan2State();
        }
        return (isset($info['NewBSSID']) ? $info['NewBSSID'] : null);
    }

    /**
     * gibt die SSID des WLANs zurueck
     *
     * @return string
     */
    public function getGuestWlanSSID() {

        if($this->has5GhzWlan == true) {

            $info = $this->getWlan3State();
        } else {

            $info = $this->getWlan2State();
        }
        return (isset($info['NewSSID']) ? $info['NewSSID'] : null);
    }

    /**
     * gibt an ob fie MAC Adress Kontrolle fuer das WLAN aktiv ist
     *
     * @return bool
     */
    public function isGuestWlanMacAddressControlEnabled() {

        if($this->has5GhzWlan == true) {

            $info = $this->getWlan3State();
        } else {

            $info = $this->getWlan2State();
        }
        return ((int) $info['NewMACAddressControlEnabled'] == 1 ? true : false);
    }

    /**
     * gibt den verwendetetn WLAN Standard zurueck
     *
     * @return string
     */
    public function getGuestWlanStandard() {

        if($this->has5GhzWlan == true) {

            $info = $this->getWlan3State();
        } else {

            $info = $this->getWlan2State();
        }
        return (isset($info['NewStandard']) ? $info['NewStandard'] : null);
    }

    /**
     * gibt die Maximale Bitrate des WLANs zurueck
     *
     * @return string
     */
    public function getGuestWlanMaxBitRate() {

        if($this->has5GhzWlan == true) {

            $info = $this->getWlan3State();
        } else {

            $info = $this->getWlan2State();
        }
        return (isset($info['NewMaxBitRate']) ? $info['NewMaxBitRate'] : null);
    }

    /**
     * aktiviert das Gast WLAN
     *
     * @throws \SoapFault
     */
    public function enableGuestWlan() {

        if($this->has5GhzWlan == true) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/wlanconfig3",
                    'uri'        => "urn:dslforum-org:service:WLANConfiguration:3",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $client->SetEnable(new \SoapParam(1,'NewEnable'));
        } else {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/wlanconfig2",
                    'uri'        => "urn:dslforum-org:service:WLANConfiguration:2",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $client->SetEnable(new \SoapParam(1,'NewEnable'));
        }
    }

    /**
     * deaktiviert das Gast WLAN
     *
     * @throws \SoapFault
     */
    public function disableGuestWlan() {

        if($this->has5GhzWlan == true) {

            $client = new \SoapClient(
                null,
                array(
                    'location' => "http://". $this->address .":49000/upnp/control/wlanconfig3",
                    'uri' => "urn:dslforum-org:service:WLANConfiguration:3",
                    'noroot' => True,
                    'login' => $this->user,
                    'password' => $this->password
                )
            );
            $client->SetEnable(new \SoapParam(0, 'NewEnable'));
        } else {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/wlanconfig2",
                    'uri'        => "urn:dslforum-org:service:WLANConfiguration:2",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $client->SetEnable(new \SoapParam(0,'NewEnable'));
        }
    }

    /**
     * gibt an ob das WLAN aktiv ist
     *
     * @return bool
     */
    public function is2GHzWlanEnabled() {

        $info = $this->getWlan1State();
        return ((int) $info['NewEnable'] == 1 ? true : false);
    }

    /**
     * gibt die MAC Adresse des WLANs zurueck
     *
     * @return string
     */
    public function get2GHzWlanMacAddress() {

        $info = $this->getWlan1State();
        return (isset($info['NewBSSID']) ? $info['NewBSSID'] : null);
    }

    /**
     * gibt die SSID des WLANs zurueck
     *
     * @return string
     */
    public function get2GHzWlanSSID() {

        $info = $this->getWlan1State();
        return (isset($info['NewSSID']) ? $info['NewSSID'] : null);
    }

    /**
     * gibt an ob fie MAC Adress Kontrolle fuer das WLAN aktiv ist
     *
     * @return bool
     */
    public function is2GHzWlanMacAddressControlEnabled() {

        $info = $this->getWlan1State();
        return ((int) $info['NewMACAddressControlEnabled'] == 1 ? true : false);
    }

    /**
     * gibt den verwendetetn WLAN Standard zurueck
     *
     * @return string
     */
    public function get2GHzWlanStandard() {

        $info = $this->getWlan1State();
        return (isset($info['NewStandard']) ? $info['NewStandard'] : null);
    }

    /**
     * gibt die Maximale Bitrate des WLANs zurueck
     *
     * @return string
     */
    public function get2GHzWlanMaxBitRate() {

        $info = $this->getWlan1State();
        return (isset($info['NewMaxBitRate']) ? $info['NewMaxBitRate'] : null);
    }

    /**
     * aktiviert das 2,4GHz WLAN
     *
     * @throws \SoapFault
     */
    public function enable2GHzWlan() {

        $client = new \SoapClient(
            null,
            array(
                'location'   => "http://". $this->address .":49000/upnp/control/wlanconfig1",
                'uri'        => "urn:dslforum-org:service:WLANConfiguration:1",
                'noroot'     => True,
                'login'      => $this->user,
                'password'   => $this->password
            )
        );
        $client->SetEnable(new \SoapParam(1,'NewEnable'));
    }

    /**
     * deaktiviert das 2,4GHz WLAN
     *
     * @throws \SoapFault
     */
    public function disable2GHzWlan() {

        $client = new \SoapClient(
            null,
            array(
                'location'   => "http://". $this->address .":49000/upnp/control/wlanconfig1",
                'uri'        => "urn:dslforum-org:service:WLANConfiguration:1",
                'noroot'     => True,
                'login'      => $this->user,
                'password'   => $this->password
            )
        );
        $client->SetEnable(new \SoapParam(0,'NewEnable'));
    }

    /**
     * gibt an ob das WLAN aktiv ist
     *
     * @return bool
     */
    public function is5GHzWlanEnabled() {

        $info = $this->getWlan2State();
        return ((int) $info['NewEnable'] == 1 ? true : false);
    }

    /**
     * gibt die MAC Adresse des WLANs zurueck
     *
     * @return string
     */
    public function get5GHzWlanMacAddress() {

        $info = $this->getWlan2State();
        return (isset($info['NewBSSID']) ? $info['NewBSSID'] : null);
    }

    /**
     * gibt die SSID des WLANs zurueck
     *
     * @return string
     */
    public function get5GHzWlanSSID() {

        $info = $this->getWlan2State();
        return (isset($info['NewSSID']) ? $info['NewSSID'] : null);
    }

    /**
     * gibt an ob fie MAC Adress Kontrolle fuer das WLAN aktiv ist
     *
     * @return bool
     */
    public function is5GHzWlanMacAddressControlEnabled() {

        $info = $this->getWlan2State();
        return ((int) $info['NewMACAddressControlEnabled'] == 1 ? true : false);
    }

    /**
     * gibt den verwendetetn WLAN Standard zurueck
     *
     * @return string
     */
    public function get5GHzWlanStandard() {

        $info = $this->getWlan2State();
        return (isset($info['NewStandard']) ? $info['NewStandard'] : null);
    }

    /**
     * gibt die Maximale Bitrate des WLANs zurueck
     *
     * @return string
     */
    public function get5GHzWlanMaxBitRate() {

        $info = $this->getWlan2State();
        return (isset($info['NewMaxBitRate']) ? $info['NewMaxBitRate'] : null);
    }

    /**
     * aktiviert das 5GHz WLAN
     *
     * @throws \SoapFault
     */
    public function enable5GHzWlan() {

        if($this->has5GhzWlan == true) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/wlanconfig2",
                    'uri'        => "urn:dslforum-org:service:WLANConfiguration:2",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $client->SetEnable(new \SoapParam(1,'NewEnable'));
        }
    }

    /**
     * deaktiviert das 5GHz WLAN
     *
     * @throws \SoapFault
     */
    public function disable5GHzWlan() {

        if($this->has5GhzWlan == true) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/wlanconfig2",
                    'uri'        => "urn:dslforum-org:service:WLANConfiguration:2",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $client->SetEnable(new \SoapParam(0,'NewEnable'));
        }
    }

    /**
     * der Cache wird geloescht und neu erzeugt
     *
     * @return bool
     */
    public function rebuildCache() {

        parent::rebuildCache();
        $this->cache['wlan'] = array();
    }
}