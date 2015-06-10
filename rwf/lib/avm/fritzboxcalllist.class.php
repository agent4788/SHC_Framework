<?php

namespace RWF\AVM;

//Imports
use RWF\XML\XmlEditor;

/**
 * Auslesen der Fritz Box Anruftabelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FritzBoxCallList extends FritzBox {

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
     * gibt die URL zur Anrufliste zurueck
     *
     * @return array
     * @throws \SoapFault
     */
    protected function getCallListUrl() {

        if(!isset($this->cache['call']['url'])) {

            $client = new \SoapClient(
                null,
                array(
                    'location'   => "http://". $this->address .":49000/upnp/control/x_contact",
                    'uri'        => "urn:dslforum-org:service:X_AVM-DE_OnTel:1",
                    'noroot'     => True,
                    'login'      => $this->user,
                    'password'   => $this->password
                )
            );
            $this->cache['call']['url'] = $client->GetCallList();
        }
        return $this->cache['call']['url'];
    }

    /**
     * gibt die Anrufliste als XMLEditor Objekt zurueck
     *
     * @param  int $maxEntrys Anzahl der Eintraege
     * @param  int $days      Anzahl der Tage
     * @return \RWF\XML\XmlEditor
     * @throws \RWF\XML\Exception\XmlException
     */
    public function getCallListXml($maxEntrys = 999, $days = 999) {

        $url = $this->getCallListUrl() .'&max='. $maxEntrys .'&days='. $days;
        return XmlEditor::createFromUrl($url);
    }

    /**
     * der Cache wird geloescht und neu erzeugt
     *
     * @return bool
     */
    public function rebuildCache() {

        parent::rebuildCache();
        $this->cache['call'] = array();
    }
}