<?php

namespace RWF\Edimax;

//Imports
use RWF\Core\RWF;
use RWF\XML\XmlEditor;


/**
 * Auslesen von Daten und Schalten der Edimax WLan Steckdose SP-1101W
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SP1101W {

    /**
     * die Steckdose ist an
     */
    const STATE_ON = 1;

    /**
     * die Steckdose ist aus
     */
    const STATE_OFF = 0;

    /**
     * IP Adresse der Steckdose
     *
     * @var string
     */
    protected $ipAddress = '';

    /**
     * Benutzername (Default admin)
     *
     * @var string
     */
    protected $user = 'admin';

    /**
     * Passwort (Default 1234)
     *
     * @var string
     */
    protected $password = '1234';

    /**
     * SP1101W constructor.
     * @param string $ipAddress
     * @param string $user
     * @param string $password
     */
    public function __construct($ipAddress, $user = 'admin', $password = '1234') {

        $this->ipAddress = $ipAddress;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getIpAddress() {

        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress) {

        $this->ipAddress = $ipAddress;
    }

    /**
     * @return string
     */
    public function getUser() {

        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user) {

        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getPassword() {

        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password) {

        $this->password = $password;
    }

    /**
     * prüft ob die Steckdose erreichbar ist
     *
     * @return bool
     */
    public function isPresent() {

        if($this->readState() === null) {

            return false;
        }
        return true;
    }

    /**
     * sendet einen Einschaltbefehl an die Steckdose
     *
     * @return boolean
     */
    public function switchOn() {

        $content = '<?xml version="1.0" encoding="utf-8"?>
                        <SMARTPLUG id="edimax">
                            <CMD id="setup">
                                <Device.System.Power.State>ON</Device.System.Power.State>
                            </CMD>
                        </SMARTPLUG>
        ';

        $response = $this->sendHttpCommand($content);
        if($response !== false) {

            $xml = XmlEditor::createFromString($response);
            if(isset($xml->CMD) && $xml->CMD == 'OK') {

                return true;
            }
        }
        return false;
    }

    /**
     * sendet einen Ausschaltbefehl an die Steckdose
     *
     * @return boolean
     */
    public function switchOff() {

        $content = '<?xml version="1.0" encoding="utf-8"?>
                        <SMARTPLUG id="edimax">
                            <CMD id="setup">
                                <Device.System.Power.State>OFF</Device.System.Power.State>
                            </CMD>
                        </SMARTPLUG>
        ';

        $response = $this->sendHttpCommand($content);
        if($response !== false) {

            $xml = XmlEditor::createFromString($response);
            if(isset($xml->CMD) && $xml->CMD == 'OK') {

                return true;
            }
        }
        return false;
    }

    public function readState() {

        $content = '<?xml version="1.0" encoding="utf-8"?>
                        <SMARTPLUG id="edimax">
                            <CMD id="get">
                                <Device.System.Power.State></Device.System.Power.State>
                            </CMD>
                        </SMARTPLUG>
        ';

        $response = $this->sendHttpCommand($content);
        if($response !== false) {

            $xml = XmlEditor::createFromString($response);
            if(isset($xml->CMD) && isset($xml->CMD->{'Device.System.Power.State'}) && $xml->CMD->{'Device.System.Power.State'} == 'ON') {

                return self::STATE_ON;
            } elseif(isset($xml->CMD) && isset($xml->CMD->{'Device.System.Power.State'}) && $xml->CMD->{'Device.System.Power.State'} == 'OFF') {

                return self::STATE_OFF;
            }
        }
        return null;
    }

    /**
     * Sendet ein Kommando an die Steckdose
     *
     * @param $content
     * @return string
     */
    protected function sendHttpCommand($content) {

        $http_options = stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'user_agent' => "RWF Framework Version ". RWF::VERSION,
                'header'=>"Content-Type: text/xml\r\n",
                'content' => trim($content),
                'timeout' => 1
            )
        ));
        return @file_get_contents('http://'. urlencode($this->user) .':'. urlencode($this->password) .'@'. $this->ipAddress .':10000/smartplug.cgi', false, $http_options);
    }
}