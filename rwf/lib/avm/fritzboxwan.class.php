<?php

namespace RWF\AVM;

//Imports


/**
 * Auslesen der Fritz Box Wandaten
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FritzBoxWan extends FritzBox {

    /**
     * gibt die Verbindungsdaten der WAN Verbindung zurueck
     *
     * @return array
     * @throws \SoapFault
     */
    protected function getWanData() {

        if(!isset($this->cache['wan']['wanData'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => 'http://'. $this->address .':49000/igdupnp/control/WANCommonIFC1',
                    'uri'        => 'urn:schemas-upnp-org:service:WANCommonInterfaceConfig:1',
                    'noroot'     => true
                )
            );
            $this->cache['wan']['wanData'] = $client->GetCommonLinkProperties();
        }
        return $this->cache['wan']['wanData'];
    }

    /**
     * gibt den WAN Verbindungsstatus zurueck
     *
     * @return array
     * @throws \SoapFault
     */
    protected function getWanState() {

        if(!isset($this->cache['wan']['wanState'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => 'http://'. $this->address .':49000/igdupnp/control/WANIPConn1',
                    'uri'        => 'urn:schemas-upnp-org:service:WANIPConnection:1',
                    'noroot'     => true
                )
            );
            $this->cache['wan']['wanState'] = $client->GetStatusInfo();
        }
        return $this->cache['wan']['wanState'];
    }

    /**
     * gibt die DSL Konfiguration zurueck zurueck
     *
     * @return array
     * @throws \SoapFault
     */
    protected function getDSLConfig() {

        if(!isset($this->cache['wan']['dslInfo'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => 'http://'. $this->address .':49000/upnp/control/wandslifconfig1',
                    'uri'        => 'urn:dslforum-org:service:WANDSLInterfaceConfig:1',
                    'noroot'     => true,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $this->cache['wan']['dslInfo'] = $client->GetInfo();
        }
        return $this->cache['wan']['dslInfo'];
    }

    /**
     * gibt den WAN Verbindungstyp zurueck
     *
     * @return string
     */
    public function getAccessType() {

        $info = $this->getWanData();
        return (isset($info['NewWANAccessType']) ? $info['NewWANAccessType'] : null);
    }

    /**
     * gibt die max. Uploadgeschwindigkeit zurueck
     *
     * @return string
     */
    public function getUpstreamMaxBitRate() {

        $info = $this->getWanData();
        return (isset($info['NewLayer1UpstreamMaxBitRate']) ? $info['NewLayer1UpstreamMaxBitRate'] : null);
    }

    /**
     * gibt die max. Downloadgeschwindigkeit zurueck
     *
     * @return string
     */
    public function getDownstreamMaxBitRate() {

        $info = $this->getWanData();
        return (isset($info['NewLayer1DownstreamMaxBitRate']) ? $info['NewLayer1DownstreamMaxBitRate'] : null);
    }

    /**
     * gibt den Hardware Verbindungsstatus zurueck
     *
     * @return string
     */
    public function getPhysicalLinkStatus() {

        $info = $this->getWanData();
        return (isset($info['NewPhysicalLinkStatus']) ? $info['NewPhysicalLinkStatus'] : null);
    }

    /**
     * gibt den WAN Verbindungsstatus zurueck
     *
     * @return string
     */
    public function getConnectionStatus() {

        $info = $this->getWanState();
        return (isset($info['NewConnectionStatus']) ? $info['NewConnectionStatus'] : null);
    }

    /**
     * gibt den aktuellen WAN Verbindungsfehler zurueck
     *
     * @return string
     */
    public function getLastConnectionError() {

        $info = $this->getWanState();
        return (isset($info['NewLastConnectionError']) ? $info['NewLastConnectionError'] : null);
    }

    /**
     * gibt die WAN Verbindungszeit zurueck
     *
     * @return string
     */
    public function getConnectionUptime() {

        $info = $this->getWanState();
        return (isset($info['NewUptime']) ? $info['NewUptime'] : null);
    }

    /**
     * gibt die maximal moegliche Downstream Geschwindigkeit zurueck
     *
     * @return string
     */
    public function getConnectionDownstreamMaxBitRate() {

        $info = $this->getDSLConfig();
        return (isset($info['NewDownstreamMaxRate']) ? $info['NewDownstreamMaxRate'] : null);
    }

    /**
     * gibt die maximal moegliche Upstream Geschwindigkeit zurueck
     *
     * @return string
     */
    public function getConnectionUpstreamMaxBitRate() {

        $info = $this->getDSLConfig();
        return (isset($info['NewUpstreamMaxRate']) ? $info['NewUpstreamMaxRate'] : null);
    }

    /**
     * gibt die aktuelle Downstream Geschwindigkeit zurueck
     *
     * @return string
     */
    public function getConnectionDownstreamCurrentBitRate() {

        $info = $this->getDSLConfig();
        return (isset($info['NewDownstreamCurrRate']) ? $info['NewDownstreamCurrRate'] : null);
    }

    /**
     * gibt die aktuelle Upstream Geschwindigkeit zurueck
     *
     * @return string
     */
    public function getConnectionUpstreamCurrentBitRate() {

        $info = $this->getDSLConfig();
        return (isset($info['NewUpstreamCurrRate']) ? $info['NewUpstreamCurrRate'] : null);
    }

    /**
     * gibt die externe IP der Fritz box zurueck
     *
     * @return string
     * @throws \SoapFault
     */
    public function getExternalIp() {

        if(!isset($this->cache['wan']['extIp'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => 'http://'. $this->address .':49000/igdupnp/control/WANIPConn1',
                    'uri'        => 'urn:schemas-upnp-org:service:WANIPConnection:1',
                    'noroot'     => true
                )
            );
            $this->cache['wan']['extIp'] = $client->GetExternalIPAddress();
        }
        return $this->cache['wan']['extIp'];
    }

    /**
     * stoest eine neuverbindung der Fritzbox mit dem Internet an
     *
     * @throws \SoapFault
     */
    public function reconnectWan() {

        $client = new \SoapClient(
            null,
            array(
                'location'   => 'http://'. $this->address .':49000/igdupnp/control/WANIPConn1',
                'uri'        => 'urn:schemas-upnp-org:service:WANIPConnection:1',
                'noroot'     => true,
                'login'      => $this->user,
                'password'   => $this->password
            )
        );
        $client->ForceTermination();
    }

    /**
     * der Cache wird geloescht und neu erzeugt
     *
     * @return bool
     */
    public function rebuildCache() {

        parent::rebuildCache();
        $this->cache['wan'] = array();
    }
}