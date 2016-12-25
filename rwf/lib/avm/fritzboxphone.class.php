<?php

namespace RWF\AVM;

//Imports
use RWF\XML\XmlEditor;

/**
 * Auslesen der Fritz Box Telefoniegeraete
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FritzBoxPhone extends FritzBox {

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
     * gibt die Anzahl der DECT Gereate zurueck
     *
     * @return string
     * @throws \SoapFault
     */
    protected function numberOfDevices() {

        if(!isset($this->cache['phone']['numberOfDevices'])) {

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
            $this->cache['phone']['numberOfDevices'] = $client->GetDECTHandsetList();
        }
        return $this->cache['phone']['numberOfDevices'];
    }

    /**
     * gibt die Anzahl der Telefonbuecher zurueck
     *
     * @return string
     * @throws \SoapFault
     */
    protected function numberOfPhoneBooks() {

        if(!isset($this->cache['phone']['numberOfPhonebooks'])) {

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
            $this->cache['phone']['numberOfPhonebooks'] = $client->GetPhonebookList();
        }
        return $this->cache['phone']['numberOfPhonebooks'];
    }

    /**
     * gibt eine Liste mit den DECT Telefoniegereaten zurueck
     *
     * @return array
     * @throws \SoapFault
     */
    public function listPhones() {

        if(!isset($this->cache['phone']['devices'])) {

            $numberOfDevices = $this->numberOfDevices();
            $this->cache['phone']['devices'] = array();
            for($i = 1; $i <= $numberOfDevices; $i++) {

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
                $data = $client->GetDECTHandsetInfo(new \SoapParam($i, 'NewDectID'));
                $this->cache['phone']['devices'][] = array(
                    'HandsetName' => $data['NewHandsetName'],
                    'PhonebookID' => $data['NewPhonebookID']
                );
            }
        }
        return $this->cache['phone']['devices'];
    }

    /**
     * gibt eine Liste mit den Telefonbuechern zurueck
     *
     * @return array
     * @throws \SoapFault
     */
    public function listPhoneBooks() {

        if(!isset($this->cache['phone']['phoneBooks'])) {

            $phoneBooks = explode(',', $this->numberOfPhoneBooks());
            foreach($phoneBooks as $phoneBookId) {

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
                $data = $client->GetPhonebook(new \SoapParam($phoneBookId, 'NewPhonebookID'));
                $this->cache['phone']['phoneBooks'][$phoneBookId] = array(
                    'NewPhonebookID' => $phoneBookId,
                    'PhonebookURL' => $data['NewPhonebookURL'],
                    'PhonebookName' => $data['NewPhonebookName'],
                    'PhonebookExtraID' => $data['NewPhonebookExtraID']
                );
            }
        }
        return $this->cache['phone']['phoneBooks'];
    }

    /**
     * gibt die EIntraege des Telefonbuches zurueck
     *
     * @param  int $id        Telefonbuch ID
     * @param  int $maxEntrys Anzahl der Eintraege
     * @return \RWF\XML\XmlEditor
     * @throws \RWF\XML\Exception\XmlException
     */
    public function getPhoneBook($id, $maxEntrys = 999) {

        $phoneBookList = $this->listPhoneBooks();
        if(isset($phoneBookList[$id])) {

            $url = $phoneBookList[$id]['PhonebookURL'] .'&max='. $maxEntrys;
            return XmlEditor::createFromUrl($url);
        }
        return null;
    }

    /**
     * der Cache wird geloescht und neu erzeugt
     *
     * @return bool
     */
    public function rebuildCache() {

        parent::rebuildCache();
        $this->cache['phone'] = array();
    }
}