<?php

namespace MB\Movie\Editor;

//Imports
use MB\Core\MB;
use MB\Movie\MovieGenre;

/**
 * Film Genre Editor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class MovieGenreEditor {

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
     * @var \MB\Movie\Editor\MovieGenreEditor
     */
    protected static $instance = null;

    /**
     * Liste mit allen Genres
     *
     * @var Array
     */
    protected $genres = array();

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'movieGenre';

    protected function __construct() {

        $this->loadData();
    }

    /**
     * Genres aus der Datenbank laden und Objekte erzeugen
     */
    public function loadData() {

        //alte Daten loeschen
        $this->genres = array();

        $genres = MB::getDatabase()->hGetAllArray(self::$tableName);
        foreach($genres as $genre) {

            $hash = $genre['hash'];
            $genreObject = new MovieGenre();
            $genreObject->setHash($hash);
            $genreObject->setName($genre['name']);
            $genreObject->setIcon($genre['icon']);

            $this->genres[$hash] = $genreObject;
        }
    }

    /**
     * gibt das Genre zugehoerig zum Hash zurueck
     *
     * @param  string $hash
     * @return \MB\Movie\MovieGenre
     */
    public function getMovieGenreByHash($hash) {

        if (isset($this->genres[$hash])) {

            return $this->genres[$hash];
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
    public function listGenres($orderBy = 'name') {

        if ($orderBy == 'name') {

            //nach Namen sortieren
            $genres = $this->genres;

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
            usort($genres, $orderFunction);
            return $genres;
        }
        return $this->genres;
    }

    /**
     * erstellt ein neues Genre
     *
     * @param  string $name Name
     * @param  string $link Link
     * @param  string $icon Icon
     * @return bool
     */
    public function addGenre($name, $icon) {

        $db = MB::getDatabase();
        $hash = md5(uniqid(microtime(true)));
        $newGenre = array(
            'hash' => $hash,
            'name' => $name,
            'icon' => $icon
        );

        if($db->hSetNxArray(self::$tableName, $hash, $newGenre) == 0) {

            return false;
        }
        return true;
    }

    /**
     * bearbeitet ein Genre
     *
     * @param  string $hash eindeutige Identifizierung
     * @param  string $name Name
     * @param  string $icon Icon
     * @return bool
     */
    public function editGenre($hash, $name = null, $icon = null) {

        $db = MB::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $hash)) {

            $genre = $db->hGetArray(self::$tableName, $hash);

            //Name
            if ($name !== null) {

                $genre['name'] = $name;
            }

            //Icon
            if ($icon !== null) {

                $genre['name'] = $icon;
            }

            if($db->hSetArray(self::$tableName, $hash, $genre) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * loescht ein Genre
     *
     * @param  string $hash eindeutige Identifizierung
     * @return bool
     */
    public function removeGenre($hash) {

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
     * @return \MB\Movie\Editor\MovieGenreEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new MovieGenreEditor();
        }
        return self::$instance;
    }
}