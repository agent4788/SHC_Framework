<?php

namespace RWF\IO;

/**
 * Socket Server Client
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SocketServerClient {

    /**
     * Socket Verbindung
     * 
     * @var recource
     */
    protected $socket = null;

    /**
     * Stream geoeffnet
     * 
     * @var Boolean
     */
    protected $opend = false;

    /**
     * @param recource $socket Verbindungs Handle
     */
    public function __construct($socket) {

        $this->socket = $socket;
        $this->opend = true;
    }

    /**
     * liest vom Socket
     * 
     * @param  Integer $length Laenge
     * @return String
     */
    public function read($length = 1024) {

        return socket_read($this->socket, $length, PHP_BINARY_READ);
    }

    /**
     * liest vom Socket
     *
     * @param  Integer $length Laenge
     * @return String
     */
    public function readLine($length = 1024) {

        return socket_read($this->socket, $length, PHP_NORMAL_READ);
    }

    /**
     * schreibt in den Socket
     * 
     * @param  String  $buffer Daten
     * @param  Integer $length Laenge
     * @return Integer
     */
    public function write($buffer) {

        return socket_send($this->socket, $buffer, strlen($buffer), MSG_EOR);
    }

    /**
     * schreibt eine Zeile in den Socket
     *
     * @param  String  $buffer Daten
     * @param  Integer $length Laenge
     * @return Integer
     */
    public function writeLn($buffer) {

        return socket_send($this->socket, $buffer . "\n", strlen($buffer), MSG_EOR);
    }

    /**
     * schreibt die Daten auf das Zielmedium
     */
    public function flush() {

        @fflush($this->socket);
    }

    /**
     * beendet den Socket
     */
    public function close() {

        if ($this->opend) {
            
            socket_close($this->socket);
            $this->opend = false;
        }
    }

}
