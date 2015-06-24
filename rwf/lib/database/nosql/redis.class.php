<?php

namespace RWF\Database\NoSQL;

//Imports
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

        $dbConfig = array();
        require_once(PATH_RWF .'db.config.php');
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
        $this->setOption(self::OPT_SERIALIZER, self::SERIALIZER_PHP);
        $this->setOption(self::OPT_PREFIX, 'rwf:');
        return true;
    }
}