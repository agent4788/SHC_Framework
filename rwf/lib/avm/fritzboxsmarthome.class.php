<?php

namespace RWF\AVM;

//Imports
use RWF\Core\RWF;
use RWF\Util\ArrayUtil;
use RWF\XML\XmlEditor;


/**
 * Auslesen der Fritz Box SmartHome Geraete
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FritzBoxSmartHome extends FritzBox {

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
     * gibt die Geraeteliste zurueck
     *
     * @return array
     */
    public function getDeviceList() {

        if(!isset($this->cache['smarthome']['deviceList'])) {

            //HTTP Anfrage
            $http_options = stream_context_create(array(
                'http' => array(
                    'method'  => 'GET',
                    'user_agent' => "RWF Framework Version ". RWF::VERSION
                )
            ));
            $result = @file_get_contents('http://'. $this->address .'/webservices/homeautoswitch.lua?switchcmd=getswitchlist&'. $this->getSid(), false, $http_options);
            $list = explode(',', $result);
            $list = ArrayUtil::trim($list);
            $this->cache['smarthome']['deviceList'] = $list;
        }
        return $this->cache['smarthome']['deviceList'];
    }

    /**
     * gibt eine liste mit allen SmartHome Geraeten zurueck
     *
     * @return array
     * @throws \RWF\XML\Exception\XmlException
     */
    public function listDevices() {

        if(!isset($this->cache['smarthome']['devices'])) {

            $this->cache['smarthome']['devices'] = array();

            $devList = $this->getDeviceList();
            if(!isset($devList[0])) {

                //keine SmartHome Geraete
                return array();
            }

            //HTTP Anfrage
            $http_options = stream_context_create(array(
                'http' => array(
                    'method'  => 'GET',
                    'user_agent' => "RWF Framework Version ". RWF::VERSION
                )
            ));
            $result = @file_get_contents('http://'. $this->address .'/webservices/homeautoswitch.lua?switchcmd=getdevicelistinfos&ain='. urlencode($devList[0]) .'&'. $this->getSid(), false, $http_options);

            //XML Daten auswerten
            $xml = XmlEditor::createFromString($result);

            foreach($xml->device as $device) {

                $atrr = $device->attributes();
                $ain = (string) $atrr->identifier;
                $data = array(
                    'device' => array(
                        'ain' => $ain,
                        'identifier' => $ain,
                        'id' => (string) $atrr->id,
                        'functionbitmask' => (string) $atrr->functionbitmask,
                        'fwversion' => (string) $atrr->fwversion,
                        'manufacturer' => (string) $atrr->manufacturer,
                        'productname' => (string) $atrr->productname,
                    ),

                    'present' => ((int) $device->present == 1 ? true : false),
                    'name' => (string) $device->name
                );
                if(isset($device->switch)) {

                    $data['switch'] = array(
                        'state' => (string) $device->switch->state,
                        'mode' => (string) $device->switch->mode,
                        'lock' => ((int) $device->switch->lock == 1 ? true : false),
                    );
                }
                if(isset($device->powermeter)) {

                    $data['powermeter'] = array(
                        'power' => (int) $device->powermeter->power,
                        'energy' => (int) $device->powermeter->energy,
                    );
                }
                if(isset($device->temperature)) {

                    $data['temperature'] = array(
                        'temperature' => ((float) $device->temperature->celsius != 0.0 ? (float) $device->temperature->celsius / 10 : 0.0),
                        'offset' => ((float) $device->temperature->offset != 0.0 ? (float) $device->temperature->offset / 10 : 0.0)
                    );
                }
                $this->cache['smarthome']['devices'][$ain] = $data;
            }
        }
        return $this->cache['smarthome']['devices'];
    }

    /**
     * gibt die Daten des Geraetes mit der Ain zurueck
     *
     * @param  string $ain Geraete Identifikation
     * @return array
     */
    public function getDevice($ain) {

        $devices = $this->listDevices();
        if(isset($devices[$ain])) {

            return $devices[$ain];
        }
        return null;
    }

    /**
     * schaltet die Steckdose an
     *
     * @param  string $ain Geraete Identifikation
     * @return bool
     */
    public function switchOn($ain) {

        //HTTP Anfrage
        $http_options = stream_context_create(array(
            'http' => array(
                'method'  => 'GET',
                'user_agent' => "RWF Framework Version ". RWF::VERSION
            )
        ));
        $result = @file_get_contents('http://'. $this->address .'/webservices/homeautoswitch.lua?switchcmd=setswitchon&ain='. urlencode($ain) .'&'. $this->getSid(), false, $http_options);

        //erfolg pruefen
        if($result !== false) {

            if(trim($result) == 1) {

                return true;
            }
        }
        return false;
    }

    /**
     * schaltet die Steckdose aus
     *
     * @param  string $ain Geraete Identifikation
     * @return bool
     */
    public function switchOff($ain) {

        //HTTP Anfrage
        $http_options = stream_context_create(array(
            'http' => array(
                'method'  => 'GET',
                'user_agent' => "RWF Framework Version ". RWF::VERSION
            )
        ));
        $result = @file_get_contents('http://'. $this->address .'/webservices/homeautoswitch.lua?switchcmd=setswitchoff&ain='. urlencode($ain) .'&'. $this->getSid(), false, $http_options);

        //erfolg pruefen
        if($result !== false) {

            if(trim($result) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * schaltet die Steckdose um
     *
     * @param  string $ain Geraete Identifikation
     * @return bool
     */
    public function switchToggle($ain) {

        //HTTP Anfrage
        $http_options = stream_context_create(array(
            'http' => array(
                'method'  => 'GET',
                'user_agent' => "RWF Framework Version ". RWF::VERSION
            )
        ));
        $result = @file_get_contents('http://'. $this->address .'/webservices/homeautoswitch.lua?switchcmd=setswitchtoggle&ain='. urlencode($ain) .'&'. $this->getSid(), false, $http_options);

        //erfolg pruefen
        if($result !== false) {

            if(trim($result) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * der Cache wird geloescht und neu erzeugt
     *
     * @return bool
     */
    public function rebuildCache() {

        parent::rebuildCache();
        $this->cache['smarthome'] = array();
    }
}