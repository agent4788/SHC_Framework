<?php

namespace RWF\IO;

/**
 * UDP Socket
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UDPSocket extends Socket {

    /**
     * Oeffnet einen Stream
     *
     * @return Boolean
     * @throws \Exception
     */
    public function open() {

        //Socket erstellen
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        $connected = @socket_connect($this->socket, $this->host, $this->port);
        if ($this->socket === false || $connected == false) {

            throw new \Exception('UDP Verbindung zu "' . $this->host . ':' . $this->port . '" fehlgeschalgen', 1150);
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
}