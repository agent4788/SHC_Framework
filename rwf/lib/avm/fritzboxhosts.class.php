<?php
namespace RWF\AVM;

//Imports

/**
 * Auslesen der Fritz Box Netzwerk Geraeteliste
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FritzBoxHosts extends FritzBox {

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
     * gibt die Anzahl der bekannten Geraete zurueck
     *
     * @return array
     * @throws \SoapFault
     */
    protected function getNumberOfEntrys() {

        if(!isset($this->cache['host']['numberOfEntrys'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/hosts",
                    'uri'        => "urn:dslforum-org:service:Hosts:1",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $this->cache['host']['numberOfEntrys'] = $client->GetHostNumberOfEntries();
        }
        return $this->cache['host']['numberOfEntrys'];
    }

    public function listEntrys() {

        if(!isset($this->cache['host']['entrys'])) {

            $numberOfEntrys = $this->getNumberOfEntrys();
            for($i = 0; $i < $numberOfEntrys; $i++) {

                $client = new \SoapClient(
                    null,
                    array(
                        'location'   => "http://". $this->address .":49000/upnp/control/hosts",
                        'uri'        => "urn:dslforum-org:service:Hosts:1",
                        'noroot'     => True,
                        'login'      => $this->user,
                        'password'   => $this->password
                    )
                );
                $this->cache['host']['entrys'][$i] = $client->GetGenericHostEntry(new \SoapParam($i, 'NewIndex'));
            }
        }
        return $this->cache['host']['entrys'];
    }

    /**
     * der Cache wird geloescht und neu erzeugt
     *
     * @return bool
     */
    public function rebuildCache() {

        parent::rebuildCache();
        $this->cache['host'] = array();
    }
}