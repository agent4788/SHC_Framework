<?php

namespace MB\Database\NoSQL;

//Imports
use MB\Core\MB;
use Redis as RedisCore;

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

        $host = MB::getSetting('mb.redis.host');
        $port = MB::getSetting('mb.redis.port');
        $timeout = MB::getSetting('mb.redis.timeout');
        $db = MB::getSetting('mb.redis.db');
        $pass = MB::getSetting('mb.redis.pass');

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
        $this->setOption(self::OPT_PREFIX, 'mb:');
        return true;
    }
}