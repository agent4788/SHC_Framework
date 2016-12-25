<?php

namespace RWF\AVM;

//Imports

/**
 * Auslesen der Fritz Box Geraetedaten
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FritzBoxDevice extends FritzBox {

    /**
     * @param string $address      Fritz Box Host oder IP Adresse
     * @param bool   $has5GhzWlan  Fritz Box Host oder IP Adresse
     * @param string $user         Benutzername (Fritz Box Benutzer)
     * @param string $password     Passwort (Fritz Box Benutzer)
     */
    public function __construct($address = 'fritz.box', $has5GhzWlan = true, $user = '', $password = '') {

        parent::__construct($address, $has5GhzWlan, $user, $password);
    }

    /**
     * gibt die Geraeteinformationen zurueck
     *
     * @return array
     * @throws \SoapFault
     */
    protected function getDeviceInfo() {

        if(!isset($this->cache['device']['info'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/deviceinfo",
                    'uri'        => "urn:dslforum-org:service:DeviceInfo:1",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $this->cache['device']['info'] = $client->GetInfo();
        }
        return $this->cache['device']['info'];
    }

    /**
     * gibt diverse zusaetzliche Statusdaten zurueck
     *
     * @return array
     * @throws \SoapFault
     */
    protected function getAddonInfo() {

        if(!isset($this->cache['device']['addonInfo'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => 'http://'. $this->address .':49000/igdupnp/control/WANCommonIFC1',
                    'uri'        => 'urn:schemas-upnp-org:service:WANCommonInterfaceConfig:1',
                    'noroot'     => true
                )
            );
            $this->cache['device']['addonInfo'] = $client->GetAddonInfos();
        }
        return $this->cache['device']['addonInfo'];
    }

    /**
     * liest den aktuell verwendetet Benutzer und dessen Berechtigungen aus
     *
     * @return array
     */
    protected function getCurrentUserInfo() {

        if(!isset($this->cache['device']['currentUser'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => 'http://'. $this->address .':49000/upnp/control/lanconfigsecurity',
                    'uri'        => 'urn:dslforum-org:service:LANConfigSecurity:1',
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $this->cache['device']['currentUser'] = $client->{"X_AVM-DE_GetCurrentUser"}();
        }
        return $this->cache['device']['currentUser'];
    }

    /**
     * gibt den Geraete Namen zurueck
     *
     * @return string
     */
    public function getModelName() {

        $info = $this->getDeviceInfo();
        return (isset($info['NewModelName']) ? $info['NewModelName'] : null);
    }

    /**
     * gibt die Software Version zurueck
     *
     * @return string
     */
    public function getSoftwareVersion() {

        $info = $this->getDeviceInfo();
        return (isset($info['NewSoftwareVersion']) ? $info['NewSoftwareVersion'] : null);
    }

    /**
     * gibt die Hardwareversion zurueck
     *
     * @return string
     */
    public function getHardwareVersion() {

        $info = $this->getDeviceInfo();
        return (isset($info['NewHardwareVersion']) ? $info['NewHardwareVersion'] : null);
    }

    /**
     * gibt die Laufzeit in Sekunden zurueck
     *
     * @return string
     */
    public function getUpTime() {

        $info = $this->getDeviceInfo();
        return (isset($info['NewUpTime']) ? $info['NewUpTime'] : null);
    }

    /**
     * gibt das Logbuch zurueck
     *
     * @return string
     */
    public function getLog() {

        $info = $this->getDeviceInfo();
        return (isset($info['NewDeviceLog']) ? $info['NewDeviceLog'] : null);
    }

    /**
     * gibt die Anzahl der gesendeten Bytes zurueck
     *
     * @return string
     */
    public function getTotalBytesSent() {

        $info = $this->getAddonInfo();
        return (isset($info['NewTotalBytesSent']) ? $info['NewTotalBytesSent'] : null);
    }

    /**
     * gibt die Anzahl der empfangenen Bytes zurueck
     *
     * @return string
     */
    public function getTotalBytesReceived() {

        $info = $this->getAddonInfo();
        return (isset($info['NewTotalBytesReceived']) ? $info['NewTotalBytesReceived'] : null);
    }

    /**
     * gibt die IP des 1. DNS Servers zurueck
     *
     * @return string
     */
    public function getDNSServer1() {

        $info = $this->getAddonInfo();
        return (isset($info['NewDNSServer1']) ? $info['NewDNSServer1'] : null);
    }

    /**
     * gibt die IP des 2. DNS Servers zurueck
     *
     * @return string
     */
    public function getDNSServer2() {

        $info = $this->getAddonInfo();
        return (isset($info['NewDNSServer2']) ? $info['NewDNSServer2'] : null);
    }

    /**
     * gibt die IP des 1. VOIP DNS Servers zurueck
     *
     * @return string
     */
    public function getVoipDNSServer1() {

        $info = $this->getAddonInfo();
        return (isset($info['NewVoipDNSServer1']) ? $info['NewVoipDNSServer1'] : null);
    }

    /**
     * gibt die IP des 2. VOIP DNS Servers zurueck
     *
     * @return string
     */
    public function getVoipDNSServer2() {

        $info = $this->getAddonInfo();
        return (isset($info['NewVoipDNSServer2']) ? $info['NewVoipDNSServer2'] : null);
    }

    /**
     * gibt den Namen des aktuell angemeldeten Benutzers zurueck
     *
     * @return string
     */
    public function getCurrentUserName() {

        $info = $this->getCurrentUserInfo();
        return (isset($info['NewX_AVM-DE_CurrentUsername']) ? $info['NewX_AVM-DE_CurrentUsername'] : null);
    }

    /**
     * gibt die Berechtigungen des aktuell angemeldeten Benutzers zurueck
     *
     * @return array
     */
    public function getCurrentUserPermissions() {

        $info = $this->getCurrentUserInfo();
        if(!isset($info['NewX_AVM-DE_CurrentUserRights'])) {

            $xml = new \SimpleXMLElement($info['NewX_AVM-DE_CurrentUserRights']);
            $permissions = array();
            $index = 0;
            foreach($xml->path as $value) {

                $permissions[(string) $value] = (string) $xml->access[$index++];
            }
            return $permissions;
        }
        return null;
    }

    /**
     * gibt an ob die Anonyme Anmeldung aktiv ist
     *
     * @throws \SoapFault
     */
    public function isAnonymusLoginEnabled() {

        if(!isset($this['dev']['anonymusLogin'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => 'http://'. $this->address .':49000/upnp/control/lanconfigsecurity',
                    'uri'        => 'urn:dslforum-org:service:LANConfigSecurity:1',
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $this['dev']['anonymusLogin'] = $client->{"X_AVM-DE_GetAnonymousLogin"}();
        }
        return ($this['dev']['anonymusLogin'] == 1 ? true : false);
    }

    /**
     * startet die Fritz Box neu
     *
     * @throws \SoapFault
     */
    public function reboot() {

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
        $client->Reboot();
    }

    /**
     * der Cache wird geloescht und neu erzeugt
     *
     * @return bool
     */
    public function rebuildCache() {

        parent::rebuildCache();
        $this->cache['device'] = array();
    }
}