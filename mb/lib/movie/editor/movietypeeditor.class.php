<?php

namespace MB\Movie\Editor;

//Imports
use MB\Core\MB;
use MB\Movie\MovieType;

/**
 * Film Medium Editor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class MovieTypeEditor {

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
     * @var \MB\Movie\Editor\MovieTypeEditor
     */
    protected static $instance = null;

    /**
     * Liste mit allen Medien
     *
     * @var Array
     */
    protected $types = array();

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'movieType';

    protected function __construct() {

        $this->loadData();
    }

    /**
     * Medien aus der Datenbank laden und Objekte erzeugen
     */
    public function loadData() {

        //alte Daten loeschen
        $this->types = array();

        $types = MB::getDatabase()->hGetAllArray(self::$tableName);
        foreach($types as $type) {

            $hash = $type['hash'];
            $typeObject = new MovieType();
            $typeObject->setHash($hash);
            $typeObject->setName($type['name']);
            $typeObject->setIcon($type['icon']);

            $this->types[$hash] = $typeObject;
        }
    }

    /**
     * gibt das Genre zugehoerig zum Hash zurueck
     *
     * @param  string $hash
     * @return \MB\Movie\MovieType
     */
    public function getMovieTypeByHash($hash) {

        if (isset($this->types[$hash])) {

            return $this->types[$hash];
        }
        return null;
    }

    /**
     * gibt eine Liste mir allen Genres zurueck
     *
     * @param  String $orderBy Art der Sortierung (
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listMovieTypes($orderBy = 'name') {

        if ($orderBy == 'name') {

            //nach Namen sortieren
            $types = $this->types;

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
            usort($types, $orderFunction);
            return $types;
        }
        return $this->types;
    }

    /**
     * erstellt ein neues Medium
     *
     * @param  string $name Name
     * @param  string $link Link
     * @param  string $icon Icon
     * @return bool
     */
    public function addType($name, $icon) {

        $db = MB::getDatabase();
        $hash = md5(uniqid(microtime(true)));
        $newType = array(
            'hash' => $hash,
            'name' => $name,
            'icon' => $icon
        );

        if($db->hSetNxArray(self::$tableName, $hash, $newType) == 0) {

            return false;
        }
        return true;
    }

    /**
     * bearbeitet ein Medium
     *
     * @param  string $hash eindeutige Identifizierung
     * @param  string $name Name
     * @param  string $icon Icon
     * @return bool
     */
    public function editType($hash, $name = null, $icon = null) {

        $db = MB::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $hash)) {

            $type = $db->hGetArray(self::$tableName, $hash);

            //Name
            if ($name !== null) {

                $type['name'] = $name;
            }

            //Icon
            if ($icon !== null) {

                $type['name'] = $icon;
            }

            if($db->hSetArray(self::$tableName, $hash, $type) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * loescht ein Medium
     *
     * @param  string $hash eindeutige Identifizierung
     * @return bool
     */
    public function removeType($hash) {

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
     * @return \MB\Movie\Editor\MovieTypeEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new MovieTypeEditor();
        }
        return self::$instance;
    }
}