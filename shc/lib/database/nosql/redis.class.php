<?php

namespace SHC\Database\NoSQL;

//Imports
use Redis as RedisCore;
use SHC\Core\SHC;

/**
 * Redis NoSQL Dantenbank
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
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

        $host = SHC::getSetting('shc.redis.host');
        $port = SHC::getSetting('shc.redis.port');
        $timeout = SHC::getSetting('shc.redis.timeout');
        $db = SHC::getSetting('shc.redis.db');
        $pass = SHC::getSetting('shc.redis.pass');

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
        $this->setOption(self::OPT_SERIALIZER, self::SERIALIZER_PHP);
        $this->setOption(self::OPT_PREFIX, 'shc:');
        return true;
    }

    /**
     * gibt zu einem Datensatz den naechsten Index zurueck
     *
     * @param $dataSet
     * @return int
     */
    public function autoIncrement($dataSet) {

        return $this->incr('autoIncrement:'. $dataSet);
    }
}