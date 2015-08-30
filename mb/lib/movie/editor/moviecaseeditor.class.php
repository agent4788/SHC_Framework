<?php

namespace MB\Movie\Editor;

//Imports
use MB\Core\MB;
use MB\Movie\MovieCase;

/**
 * Film Verpackung Editor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class MovieCaseEditor {

    /**
     * nach Namen sortieren
     *
     * @var String
     */
    const SORT_BY_NAME = 'name';

    /**
     * nicht sortieren
     *
     * @var String
     */
    const SORT_NOTHING = 'unsorted';

    /**
     * Singleton Instanz
     *
     * @var \MB\Movie\Editor\MovieCaseEditor
     */
    protected static $instance = null;

    /**
     * Liste mit allen Filmverpackungen
     *
     * @var Array
     */
    protected $cases = array();

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'movieCase';

    protected function __construct() {

        $this->loadData();
    }

    /**
     * Verpackungen aus der Datenbank laden und Objekte erzeugen
     */
    public function loadData() {

        //alte Daten loeschen
        $this->cases = array();

        $cases = MB::getDatabase()->hGetAllArray(self::$tableName);
        foreach($cases as $case) {

            $hash = $case['hash'];
            $caseObject = new MovieCase();
            $caseObject->setHash($hash);
            $caseObject->setName($case['name']);

            $this->cases[$hash] = $caseObject;
        }
    }

    /**
     * gibt die Verpackung zugehoerig zum Hash zurueck
     *
     * @param  string $hash
     * @return \MB\Movie\MovieCase
     */
    public function getMovieCaseByHash($hash) {

        if (isset($this->cases[$hash])) {

            return $this->cases[$hash];
        }
        return null;
    }

    /**
     * gibt eine Liste mir allen Verpackungen zurueck
     *
     * @param  String $orderBy Art der Sortierung (
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listMovieCases($orderBy = 'name') {

        if ($orderBy == 'name') {

            //nach Namen sortieren
            $cases = $this->cases;

            //Sortierfunktion
            $orderFunction = function($a, $b) {

                if($a->getName() == $b->getName()) {

                    return 0;
                }

                if($a->getName() < $b->getName()) {

                    return -1;
                }
                return 1;
            };
            usort($cases, $orderFunction);
            return $cases;
        }
        return $this->cases;
    }

    /**
     * erstellt eine neue Verpackung
     *
     * @param  string $name Name
     * @return bool
     */
    public function addCase($name) {

        $db = MB::getDatabase();
        $hash = md5(uniqid(microtime(true)));
        $newCase = array(
            'hash' => $hash,
            'name' => $name
        );

        if($db->hSetNxArray(self::$tableName, $hash, $newCase) == 0) {

            return false;
        }
        return true;
    }

    /**
     * bearbeitet eine Verpackung
     *
     * @param  string $hash eindeutige Identifizierung
     * @param  string $name Name
     * @return bool
     */
    public function editCase($hash, $name = null) {

        $db = MB::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $hash)) {

            $case = $db->hGetArray(self::$tableName, $hash);

            //Name
            if ($name !== null) {

                $case['name'] = $name;
            }

            if($db->hSetArray(self::$tableName, $hash, $case) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * loescht eine Verpackung
     *
     * @param  string $hash eindeutige Identifizierung
     * @return bool
     */
    public function removeCase($hash) {

        $db = MB::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $hash)) {

            if($db->hDel(self::$tableName, $hash)) {

                return true;
            }
        }
        return false;
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {

    }

    /**
     * gibt den Editor zurueck
     *
     * @return \MB\Movie\Editor\MovieCaseEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new MovieCaseEditor();
        }
        return self::$instance;
    }
}