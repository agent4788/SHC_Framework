<?php

namespace RWF\IO;

/**
 * Socket Server
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SocketServer {

    /**
     * Server Adresse
     * 
     * @var String
     */
    protected $address = '127.0.0.1';

    /**
     * Server Port
     * 
     * @var Integer
     */
    protected $port = 9273;

    /**
     * Sockethandle
     * 
     * @var recource
     */
    protected $socket = null;

    /**
     * @param String  $address Server Adresse
     * @param Integer $port    Server Port
     */
    public function __construct($address = null, $port = null) {

        //IP Adresse
        if ($address !== null) {

            $this->setAddress($address);
        }

        //Port
        if ($port !== null) {

            $this->setPort($port);
        }
    }

    /**
     * setzt die Server Adresse
     * 
     * @param String $address IP Adresse
     */
    public function setAddress($address) {

        //Adress Pruefung noch implementieren
        $this->address = $address;
    }

    /**
     * gibt die Server Adresse zurueck
     * 
     * @return String
     */
    public function getAddress() {

        return $this->address;
    }

    /**
     * setzt der Server Port
     * 
     * @param Integer $port Server Port
     */
    public function setPort($port) {

        $this->port = intval($port);
    }

    /**
     * gibt den Server Port zurueck
     * 
     * @return Integer
     */
    public function getPort() {

        return $this->port;
    }

    /**
     * initialisiert den Socket Server
     * 
     * @throws \Exception
     */
    public function startServer() {

        //Socket erstellen
        //TCP Server
        if (($this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {

            throw new \Exception('"' . socket_last_error() . ': ' . socket_strerror(socket_last_error()) . '"', 1151);
        }

        //Adresse an Socket Binden
        if (@socket_bind($this->socket, $this->address, $this->port) === false) {

            throw new \Exception('"' . socket_last_error() . ': ' . socket_strerror(socket_last_error()) . '"', 1151);
        }

        //Socket anweisen auf ankommende Verbindungen zu hoeren
        if (@socket_listen($this->socket, 10) === false) {

            throw new \Exception('"' . socket_last_error() . ': ' . socket_strerror(socket_last_error()) . '"', 1151);
        }
    }

    /**
     * verarbeitet ankommende Verbindungen und gibt die Verbindungsobjekte zurueck
     * 
     * @return \RWF\IO\Socket
     */
    public function accept() {

        //Ankommende Anfragen verarbeiten
        if (($incommingSocket = @socket_accept($this->socket)) === false) {

            throw new \Exception('"' . socket_last_error() . ': ' . socket_strerror(socket_last_error()) . '"', 1151);
        }

        return new SocketServerClient($incommingSocket);
    }

    /**
     * Beendet den Server
     */
    public function stopServer() {

        @socket_close($this->socket);
    }

}
