<?php

namespace RWF\Database\NoSQL;

//Imports
use Redis as RedisCore;
use RWF\Util\JSON;

/**
 * Redis NoSQL Dantenbank
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */
class Redis extends RedisCore {

    /**
     * stellt die Datenbankverbindung her
     *
     * @throws \Exception
     */
    public function connect() {

        if(file_exists(PATH_RWF .'db.config.json')) {

            $dbConfig = json_decode(file_get_contents('./rwf/db.config.json'), true);
        } else {

            throw new \Exception('Die Datenbankkonfiguration fehlt (db.config.json)', 1015);
        }


        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $timeout = $dbConfig['timeout'];
        $db = $dbConfig['db'];
        $pass = $dbConfig['pass'];

        //Verbinden
        if(!parent::connect($host, $port, $timeout)) {

            throw new \Exception('Verbindung zur Datenbank fehlgeschlagen', 1200);
        }
        //Anmelden
        if($pass != '') {

            if(!$this->auth($pass)) {

                throw new \Exception('Authentifizierung Fehlgeschlagen', 1201);
            }
        }
        //Datenbank auswaehlen
        if(!$this->select($db)) {

            throw new \Exception('Auswahl der Datenbank Fehlgeschlagen', 1202);
        }

        //Optionen
        $this->setOption(self::OPT_PREFIX, 'rwf:');
        return true;
    }

    /**
     * gibt zu einem Datensatz den naechsten Index zurueck
     *
     * @param $dataSet
     */
    public function autoIncrement($dataSet) {

        return $this->incr('autoIncrement:'. $dataSet);
    }

    /**
     * gibt den Wert des Schluessels als Array zurueck
     *
     * @param  string $key Schluessel
     * @return array
     * @throws \RWF\Exception\JsonException
     */
    public function getArray($key) {

        return JSON::decode($this->get($key));
    }

    /**
     * schreibt den Wert des Arrays in die Datenbank
     *
     * @param  string $key   Schluessel
     * @param  array  $value Wert
     * @return bool
     * @throws \RWF\Exception\JsonException
     */
    public function setArray($key, array $value) {

        return $this->set($key, JSON::encode($value));
    }

    /**
     * gibt den Wert eines Hash Schluessels als Array zurueck
     *
     * @param  string $key     Schluessel
     * @param  string $hashKey Hash Schluessel
     * @return array
     * @throws \RWF\Exception\JsonException
     */
    public function hGetArray($key, $hashKey) {

        return JSON::decode($this->hGet($key, $hashKey));
    }

    /**
     * gibt die Werte alle Hasch Schluessel als Array zurueck
     *
     * @param  string $key Schluessel
     * @return array
     * @throws \RWF\Exception\JsonException
     */
    public function hGetAllArray($key) {

        $result = $this->hGetAll($key);
        $return = array();
        foreach($result as $jsonString) {

            $return[] = JSON::decode($jsonString);
        }
        return $return;
    }

    /**
     * schreibt den Wert des Arrays des Hash Schlussels in die Datenbank
     *
     * @param  string $key     Schluessel
     * @param  string $hashKey Hash Schluessel
     * @param  array  $value   Wert
     * @return int
     * @throws \RWF\Exception\JsonException
     */
    public function hSetArray($key, $hashKey, array $value) {

        return $this->hSet($key, $hashKey, JSON::encode($value));
    }

    /**
     * schreibt den Wert des Arrays des Hash Schlussels in die Datenbank
     * (nu wenn der Schluessel noch nicht vorhanden ist)
     *
     * @param  string $key     Schluessel
     * @param  string $hashKey Hash Schluessel
     * @param  array  $value   Wert
     * @return int
     * @throws \RWF\Exception\JsonException
     */
    public function hSetNxArray($key, $hashKey, array $value) {

        return $this->hSetNx($key, $hashKey, JSON::encode($value));
    }

    /**
     * erzeugt ein neues Element am Anfang der Liste
     *
     * @param  string $key   Schluessel
     * @param  array  $value Wert
     * @return int
     * @throws \RWF\Exception\JsonException
     */
    public function lPushArray($key, array $value) {

        return $this->lPush($key, JSON::encode($value));
    }

    /**
     * erzeugt ein neues Element am Ende der Liste
     *
     * @param  string $key   Schluessel
     * @param  array  $value Wert
     * @return int
     * @throws \RWF\Exception\JsonException
     */
    public function rPushArray($key, array $value) {

        return $this->rPush($key, JSON::encode($value));
    }

    /**
     * gibt alle Elemente der Liste von start bist ende zurueck
     *
     * @param  string $key   Schluessel
     * @param  int    $start Start Index
     * @param  int    $end   End Index
     * @return array
     * @throws \RWF\Exception\JsonException
     */
    public function lRangeArray($key, $start, $end) {

        $result = $this->lRange($key, $start, $end);
        $return = array();
        foreach($result as $jsonString) {

            $return[] = JSON::decode($jsonString);
        }
        return $return;
    }
}