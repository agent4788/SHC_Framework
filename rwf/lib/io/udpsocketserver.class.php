<?php

namespace RWF\IO;

/**
 * UDP Socket Server
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UDPSocketServer extends SocketServer {

    /**
     * initialisiert den Socket Server
     *
     * @throws \Exception
     */
    public function startServer() {

        //Socket erstellen
        //TCP Server
        if (($this->socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) === false) {

            throw new \Exception('"' . socket_last_error() . ': ' . socket_strerror(socket_last_error()) . '"', 1151);
        }

        //Adresse an Socket Binden
        if (@socket_bind($this->socket, $this->address, $this->port) === false) {

            throw new \Exception('"' . socket_last_error() . ': ' . socket_strerror(socket_last_error()) . '"', 1151);
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