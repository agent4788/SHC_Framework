<?php

namespace SHC\Switchable\Switchables;

//Imports
use RWF\Edimax\SP1101W;
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
class EdimaxSocket extends AbstractSwitchable {

    const EDIMAX_SP_1101W = 1;

    const EDIMAX_SP_2101W = 2;

    /**
     * IP Adresse
     *
     * @var string
     */
    protected $ipAddress = '';

    /**
     * Benutzername
     *
     * @var string
     */
    protected $username = '';

    /**
     * Passwort
     *
     * @var string
     */
    protected $password = '';

    /**
     * Typ
     *
     * @var int
     */
    protected $type = 0;

    /**
     * EdimaxSocket constructor.
     * @param string $ipAddress
     * @param int $type
     * @param string $username
     * @param string $password
     */
    public function __construct($ipAddress = '', $type = 1, $username = '', $password = '') {

        $this->ipAddress = $ipAddress;
        $this->type = $type;
        $this->username = $username;
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
     * @return int
     */
    public function getType() {

        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type) {

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getUsername() {

        return $this->username;
    }

    /**
     * @param string $username
     */

    public function setUsername($username) {
        $this->username = $username;
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
     * schaltet das Objekt ein
     *
     * @return Boolean
     */
    public function switchOn() {

        $edimax = new SP1101W($this->ipAddress, $this->username, $this->password);
        $edimax->switchOn();
        $this->state = self::STATE_ON;
        $this->stateModified = true;
    }

    /**
     * schaltet das Objekt aus
     *
     * @return Boolean
     */
    public function switchOff() {

        $edimax = new SP1101W($this->ipAddress, $this->username, $this->password);
        $edimax->switchOff();
        $this->state = self::STATE_OFF;
        $this->stateModified = true;
    }

}