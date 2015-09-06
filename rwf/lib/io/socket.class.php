<?php

namespace RWF\IO;

/**
 * Socket
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Socket {

    /**
     * Hostname
     * 
     * @var String
     */
    protected $host = '127.0.0.1';

    /**
     * Port
     * 
     * @var Integer
     */
    protected $port = 9273;

    /**
     * Timeout
     * 
     * @var Integer
     */
    protected $timeout = 2;

    /**
     * Fehlernummer
     * 
     * @var Integer
     */
    protected $errorNumber = 0;

    /**
     * Fehlerbeschreibung
     * 
     * @var String
     */
    protected $errorString = '';

    /**
     * Socketsocket
     * 
     * @var recource
     */
    protected $socket = null;

    /**
     * @param String  $host    Hostname
     * @param Integer $port    Port
     * @param Integer $timeout Wartezeit
     */
    public function __construct($host = null, $port = null, $timeout = null) {

        if ($host !== null) {

            $this->setHost($host);
        }

        if ($port !== null) {

            $this->setPort($port);
        }

        if ($timeout !== null) {

            $this->setTimeout($timeout);
        }
    }

    /**
     * setzt den Host
     *
     * @param String $host Host
     */
    public function setHost($host) {

        $this->host = $host;
    }

    /**
     * gibt den Hostnamen zurueck
     *
     * @return String
     */
    public function getHost() {

        return $this->host;
    }

    /**
     * setzt den Port
     *
     * @param Integer $port Port
     */
    public function setPort($port) {

        $this->port = $port;
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
     * @param Integer $timeout Timeout
     */
    public function setTimeout($timeout) {

        $this->timeout = $timeout;
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
     * gibt den Errorcode zurueck
     *
     * @return Integer
     */
    public function getErrorNumber() {

        return $this->errorNumber;
    }

    /**
     * gibt die Fehlermeldung zurueck
     *
     * @return String
     */
    public function getErrorString() {

        return $this->errorString;
    }

    /**
     * Oeffnet einen Stream
     *
     * @return Boolean
     * @throws \Exception
     */
    public function open() {

        //Socket erstellen
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => $this->getTimeout(), 'usec' => 0));
        $connected = @socket_connect($this->socket, $this->host, $this->port);

        if ($this->socket === false || $connected == false) {

            throw new \Exception('Verbindung zu "' . $this->host . ':' . $this->port . '" fehlgeschalgen', 1150);
        }
    }

    /**
     * liest das naechsten 1024 Byte aus dem Stream
     *
     * @param  Integer $length Anzahl der zu Lesenden Bytes
     * @return String          
     */
    public function readBytes($length = 1024) {

        socket_recv($this->socket, $buff, $length, MSG_DONTWAIT);
        return $buff;
    }

    /**
     * liest das naechsten 1024 Byte aus dem Stream
     *
     * @param  Integer $length Anzahl der zu Lesenden Bytes
     * @return String
     */
    public function read($length = 1024) {

        return socket_read($this->socket, $length, PHP_BINARY_READ);
    }

    /**
     * liest die nächste Zeile aus dem Stream
     *
     * @return String 
     */
    public function readLine() {

        return socket_read($this->socket, 8192, PHP_NORMAL_READ);
    }

    /**
     * Schreibt eine Zeichenkette in den Stream
     *
     * @param  String  $str zu schreibende Zeichenkette
     * @return Boolean 
     */
    public function write($str) {

        return socket_send($this->socket, $str, strlen($str), MSG_EOR);
    }

    /**
     * Schreibt eine Zeichenkette in den Stream gefolgt von einem Zeilenumbruch
     *
     * @param  String  $str zu schreibende Zeichenkette
     * @return Boolean 
     */
    public function writeLn($str) {

        return socket_send($this->socket, $str . "\n", strlen($str), MSG_EOR);
    }

    /**
     * schiest den Stream
     *
     * @return Boolean 
     */
    public function close() {

        return socket_close($this->socket);
    }

}
