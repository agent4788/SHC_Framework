<?php

namespace SHC\SwitchServer;

//Imports
use RWF\IO\Socket;

/**
 * Schaltserver
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchServer {

    /**
     * ID
     *
     * @var Integer
     */
    protected $id = 0;

    /**
     * Name
     *
     * @var String
     */
    protected $name = '';

    /**
     * IP Adresse
     *
     * @var String
     */
    protected $address = '';

    /**
     * Port
     *
     * @var Integer
     */
    protected $port = '';

    /**
     * Timeout
     *
     * @var Integer
     */
    protected $timeout = 2;

    /**
     * Rasberry Pi Model
     *
     * @var Integer
     */
    protected $model = 0;

    /**
     * aktiviert/deaktiviert
     *
     * @var Boolean
     */
    protected $enabled = true;

    /**
     * Funksteckdosen Schalten
     *
     * @var Boolean
     */
    protected $radioSockets = false;

    /**
     * GPIOs Schalten
     *
     * @var Boolean
     */
    protected $writeGpios = false;

    /**
     * GPIOs abfragen
     *
     * @var Boolean
     */
    protected $readGpios = false;

    /**
     * @param Integer $id           ID
     * @param String  $name         Name
     * @param String  $address      IP Adresse
     * @param Integer $port         Port
     * @param Integer $model        Model ID
     * @param Boolean $radioSockets Funksteckdosen schalten
     * @param Boolean $writeGpios   GPIOs schalten
     * @param Boolean $readGpios    GPIOs lesen
     * @param Integer $timeout      Timeout
     * @param Boolean $enabled      Aktiviert
     */
    public function __construct($id, $name, $address, $port, $model, $radioSockets, $writeGpios, $readGpios, $timeout = 2, $enabled = true) {

        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->port = $port;
        $this->model = $model;
        $this->radioSockets = ($radioSockets == true ? true : false);
        $this->writeGpios = ($writeGpios == true ? true : false);
        $this->readGpios = ($readGpios == true ? true : false);
        $this->timeout = $timeout;
        $this->enabled = $enabled;
    }

    /**
     * setzt die ID
     *
     * @param  Integer $id ID
     * @return \SHC\SwitchServer\SwitchServer
     */
    public function setId($id) {

        $this->id = $id;
        return $this;
    }

    /**
     * gibt die ID zurueck
     *
     * @return Integer
     */
    public function getId() {

        return $this->id;
    }

    /**
     * setzt den Namen
     *
     * @param  String $name Name
     * @return \SHC\SwitchServer\SwitchServer
     */
    public function setName($name) {

        $this->name = $name;
        return $this;
    }

    /**
     * gibt den Namen zurueck
     *
     * @return String
     */
    public function getName() {

        return $this->name;
    }

    /**
     * setzt die IP Adresse
     *
     * @param  String $address IP Adresse
     * @return \SHC\SwitchServer\SwitchServer
     */
    public function setIpAddress($address) {

        $this->address = $address;
        return $this;
    }

    /**
     * gibt die IP Adresse zurueck
     *
     * @return String
     */
    public function getIpAddress() {

        return $this->address;
    }

    /**
     * setzt den Port
     *
     * @param  Integer $port Port
     * @return \SHC\SwitchServer\SwitchServer
     */
    public function setPort($port) {

        $this->port = $port;
        return $this;
    }

    /**
     * gibt den Port zurueck
     *
     * @return Integer
     */
    public function getPort() {

        return $this->port;
    }

    /**
     * setzt den Timeout
     *
     * @param  Integer $timeout Wartezeit
     * @return \SHC\SwitchServer\SwitchServer
     */
    public function setTimeout($timeout) {

        $this->timeout = $timeout;
        return $this;
    }

    /**
     * gibt den Timeout zurueck
     *
     * @return Integer
     */
    public function getTimeout() {

        return $this->timeout;
    }

    /**
     * setzt das RPi Model
     *
     * @param Integer $model Model ID
     * @return \SHC\SwitchServer\SwitchServer
     */
    public function setModel($model) {

        $this->model = $model;
        return $this;
    }

    /**
     * gibt die Model ID zurueck
     *
     * @return Integer
     */
    public function getModel() {

        return $this->model;
    }

    /**
     * Aktiviert/Deaktiviert den Schaltserver
     *
     * @param  Boolean $enabled Aktiviert
     * @return \SHC\SwitchServer\SwitchServer
     */
    public function enable($enabled) {

        if ($enabled == true) {

            $this->enabled = true;
        } else {

            $this->enabled = false;
        }
        return $this;
    }

    /**
     * gibt an ob der Schaltserver Aktiviert ist
     *
     * @return Boolean
     */
    public function isEnabled() {

        return $this->enabled;
    }

    /**
     * aktiviert/deaktioviert das schalten von Funksteckdosen
     *
     * @param  Boolean $enabled
     * @return \SHC\SwitchServer\SwitchServer
     */
    public function enableRadioSockets($enabled) {

        if($enabled == true) {

            $this->radioSockets = true;
            return $this;
        }
        $this->radioSockets = false;
        return $this;
    }

    /**
     * gibt an ob das schalten von Funksteckdosen aktiviert ist
     *
     * @return Boolean
     */
    public function isRadioSocketsEnabled() {

        return $this->radioSockets;
    }

    /**
     * aktiviert/deaktioviert das schalten von GPIOs
     *
     * @param  Boolean $enabled
     * @return \SHC\SwitchServer\SwitchServer
     */
    public function enableWriteGpios($enabled) {

        if($enabled == true) {

            $this->writeGpios = true;
            return $this;
        }
        $this->writeGpios = false;
        return $this;
    }

    /**
     * gibt an ob das schalten von GPIOs aktiviert ist
     *
     * @return Boolean
     */
    public function isWriteGpiosEnabled() {

        return $this->writeGpios;
    }

    /**
     * aktiviert/deaktioviert das abfragen von GPIOs
     *
     * @param  Boolean $enabled
     * @return \SHC\SwitchServer\SwitchServer
     */
    public function enableReadGpios($enabled) {

        if($enabled == true) {

            $this->readGpios = true;
            return $this;
        }
        $this->readGpios = false;
        return $this;
    }

    /**
     * gibt an ob das abfragen von GPIOs aktiviert ist
     *
     * @return Boolean
     */
    public function isReadGpiosEnabled() {

        return $this->readGpios;
    }

    /**
     * gibt eine Socket zum verbinden mit dem Schaltserver zurueck
     *
     * @return \RWF\IO\Socket
     */
    public function getSocket() {

        return new Socket($this->address, $this->port, $this->timeout);
    }

}
