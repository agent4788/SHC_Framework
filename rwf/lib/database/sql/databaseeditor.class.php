<?php

namespace RWF\Database\SQL;

//Imports
use RWF\Core\RWF;
use RWF\Database\SQL\Drivers\MySQLDatabase;

/**
 * Verwaltung der Datenbankverbindungen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */
class DatabaseEditor {

    /**
     * MySQL Datenbank
     *
     * @var Integer
     */
    const DATABASE_MYSQL = 1;

    /**
     * Datenbankobjekt
     *
     * @var \RWF\Database\SQL\Database
     */
    protected $database = null;

    /**
     * Singleton Instanz
     *
     * @var \RWF\Database\SQL\DatabaseEditor
     */
    protected static $instance = null;

    protected function __construct() {


    }

    /**
     * Datenbankobjekt erzeugen
     *
     * @return \RWF\Database\SQL\Database|MySQLDatabase
     * @throws \Exception
     */
    public function getDatabaseObject() {

        if($this->database === null) {

            if(RWF::getSetting('rwf.database.driver') == self::DATABASE_MYSQL) {

                if(MySQLDatabase::isSupported()) {

                    $this->database = new MySQLDatabase(
                        RWF::getSetting('rwf.database.host'),
                        RWF::getSetting('rwf.database.port'),
                        RWF::getSetting('rwf.database.user'),
                        RWF::getSetting('rwf.database.password'),
                        RWF::getSetting('rwf.database.database')
                    );
                    return $this->database;
                } else {

                    throw new \Exception('Die Datenbank wird nicht unterstützt', 1120);
                }
            }
        }
        return $this->database;
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {

    }

    /**
     * gibt den Datanbank Editor zurueck
     *
     * @return \RWF\Database\SQL\DatabaseEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new DatabaseEditor();
        }
        return self::$instance;
    }
}