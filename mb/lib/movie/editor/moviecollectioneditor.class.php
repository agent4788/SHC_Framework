<?php

namespace MB\Movie\Editor;

//Imports
use MB\Core\MB;
use MB\Movie\MovieCollection;

/**
 * Film Reihe Editor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class MovieCollectionEditor {

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
     * @var \MB\Movie\Editor\MovieCollectionEditor
     */
    protected static $instance = null;

    /**
     * Liste mit allen Filmreihen
     *
     * @var Array
     */
    protected $movieCollections = array();

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'movieCollection';

    protected function __construct() {

        $this->loadData();
    }

    /**
     * Filmreihen aus der Datenbank laden und Objekte erzeugen
     */
    public function loadData() {

        //alte Daten loeschen
        $this->movieCollections = array();

        $movieCollections = MB::getDatabase()->hGetAllArray(self::$tableName);
        foreach($movieCollections as $movieCollection) {

            $hash = $movieCollection['hash'];
            $movieCollectionObject = new MovieCollection();
            $movieCollectionObject->setHash($hash);
            $movieCollectionObject->setTitle($movieCollection['title']);
            $movieCollectionObject->setDescription($movieCollection['description']);
            $movieCollectionObject->setCase(MovieCaseEditor::getInstance()->getMovieCaseByHash($movieCollection['case']));
            $movieCollectionObject->setEan($movieCollection['ean']);
            $movieCollectionObject->enableLimitedEdition($movieCollection['isLimitedEdition']);

            //Genres
            foreach($movieCollection['movies'] as $movieHash) {

                //Genre aus Hash laden und zum Film Hinzufuegen
                $movieCollectionObject->addMovie(MovieEditor::getInstance()->getMovieByHash($movieHash));
            }

            $this->movieCollections[$hash] = $movieCollectionObject;
        }
    }

    /**
     * gibt die Filmreihe zugehoerig zum Hash zurueck
     *
     * @param  string $hash
     * @return \MB\Movie\Movie
     */
    public function getMovieByHash($hash) {

        if (isset($this->movies[$hash])) {

            return $this->movies[$hash];
        }
        return null;
    }

    /**
     * gibt eine Liste mir allen Filmreihen zurueck
     *
     * @param  String $orderBy Art der Sortierung (
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listMovieCollections($orderBy = 'name') {

        if ($orderBy == 'name') {

            //nach Namen sortieren
            $movieCollections = $this->movieCollections;

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
            usort($movieCollections, $orderFunction);
            return $movieCollections;
        }
        return $this->movieCollections;
    }

    /**
     * fuegt der Filmreihe einen Film hinzu
     *
     * @param  string $collectionHash Hash der Filmreihe
     * @param  string $movieHash      Hash des Films
     * @return bool
     */
    public function addMovieToCollection($collectionHash, $movieHash) {

        $db = MB::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $collectionHash)) {

            $movieCollection = $db->hGetArray(self::$tableName, $collectionHash);
            $movieCollection['movies'][] = $movieHash;

            if($db->hSet(self::$tableName, $collectionHash, $movieCollection) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * entfernt einen Film aus der Filmreihe
     *
     * @param  string $collectionHash Hash der Filmreihe
     * @param  string $movieHash      Hash des Films
     * @return bool
     */
    public function removeMovieFromCollection($collectionHash, $movieHash) {

        $db = MB::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $collectionHash)) {

            $movieCollection = $db->hGetArray(self::$tableName, $collectionHash);
            $movieCollection['movies'] = array_diff($movieCollection['movies'], array($movieHash));

            if($db->hSet(self::$tableName, $collectionHash, $movieCollection) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * erstellt eine Filmreihe
     *
     * @param  string             $title            Titel
     * @param  string             $description      Beschreibung
     * @param  string             $caseHash         Verpackung
     * @param  string             $ean              EAN Code
     * @param  bool               $isLimitedEdition Limitierte Auflage
     * @return bool
     */
    public function addMovie(
        $title,
        $description,
        $caseHash,
        $ean = '',
        $isLimitedEdition = false
    ) {

        $db = MB::getDatabase();
        $hash = md5(uniqid(microtime(true)));
        $newMovieCollection = array(
            'hash' => $hash,
            'title' => $title,
            'description' => $description,
            'case' => $caseHash,
            'ean' => $ean,
            'isLimitedEdition' => $isLimitedEdition
        );

        if($db->hSetNxArray(self::$tableName, $hash, $newMovieCollection) == 0) {

            return false;
        }
        return true;
    }

    /**
     * bearbeitet eine Filmreihe
     *
     * @param  string             $hash             eindeutige Identifizierung
     * @param  string             $title            Titel
     * @param  string             $description      Beschreibung
     * @param  string             $caseHash         Verpackung
     * @param  string             $ean              EAN Code
     * @param  bool               $isLimitedEdition Limitierte Auflage
     * @return bool
     */
    public function editMovieCollection(
        $hash,
        $title = null,
        $description = null,
        $caseHash = null,
        $ean = null,
        $isLimitedEdition = null
    ) {

        $db = MB::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $hash)) {

            $movieCollection = $db->hGetArray(self::$tableName, $hash);

            //Titel
            if ($title !== null) {

                $movieCollection['title'] = $title;
            }

            //Beschreibung
            if ($description !== null) {

                $movieCollection['description'] = $description;
            }

            //Verkackung
            if ($caseHash !== null) {

                $movieCollection['case'] = $caseHash;
            }

            //EAN Code
            if ($ean !== null) {

                $movieCollection['ean'] = $ean;
            }

            //Limitierung
            if ($isLimitedEdition !== null) {

                $movieCollection['isLimitedEdition'] = $isLimitedEdition;
            }

            if($db->hSetArray(self::$tableName, $hash, $movieCollection) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * Filmreihe loeschen
     *
     * @param  string $hash eindeutige Identifizierung
     * @return bool
     */
    public function removeMovieCollection($hash) {

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
     * @return \MB\Movie\Editor\MovieCollectionEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new MovieCollectionEditor();
        }
        return self::$instance;
    }
}