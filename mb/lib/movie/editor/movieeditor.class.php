<?php

namespace MB\Movie\Editor;

//Imports
use MB\Core\MB;
use RWF\Date\DateTime;
use MB\Movie\Movie;

/**
 * Film Editor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class MovieEditor {

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
     * @var \MB\Movie\Editor\MovieEditor
     */
    protected static $instance = null;

    /**
     * Liste mit allen Filmen
     *
     * @var Array
     */
    protected $movies = array();

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'movie';

    protected function __construct() {

        $this->loadData();
    }

    /**
     * Filme aus der Datenbank laden und Objekte erzeugen
     */
    public function loadData() {

        //alte Daten loeschen
        $this->movies = array();

        $movies = MB::getDatabase()->hGetAllArray(self::$tableName);
        foreach($movies as $movie) {

            $hash = $movie['hash'];
            $movieObject = new Movie();
            $movieObject->setHash($hash);
            $movieObject->setTitle($movie['title']);
            $movieObject->setDescription($movie['description']);
            $movieObject->setLength($movie['length']);
            $movieObject->setFsk($movie['fsk']);
            $movieObject->setType(MovieTypeEditor::getInstance()->getMovieTypeByHash($movie['type']));
            $movieObject->setCase(MovieCaseEditor::getInstance()->getMovieCaseByHash($movie['case']));
            $movieObject->setRating($movie['rating']);
            $movieObject->setDealer(MovieDealerEditor::getInstance()->getMovieDealerByHash($movie['dealer']));
            $movieObject->setPrice($movie['price']);
            $movieObject->setPurchaseDate(DateTime::createFromDatabaseDateTime($movie['purchaseDate']));
            $movieObject->setEan($movie['ean']);
            $movieObject->enableLimitedEdition($movie['isLimitedEdition']);

            //Genres
            foreach($movie['genres'] as $genreHash) {

                //Genre aus Hash laden und zum Film Hinzufuegen
                $movieObject->addGenre(MovieGenreEditor::getInstance()->getMovieGenreByHash($genreHash));
            }

            $this->movies[$hash] = $movieObject;
        }
    }

    /**
     * gibt den Film zugehoerig zum Hash zurueck
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
     * gibt eine Liste mir allen Filmen zurueck
     *
     * @param  String $orderBy Art der Sortierung (
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listMovies($orderBy = 'name') {

        if ($orderBy == 'name') {

            //nach Namen sortieren
            $movies = $this->movies;

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
            usort($movies, $orderFunction);
            return $movies;
        }
        return $this->movies;
    }

    /**
     * erstellt einen Film
     *
     * @param  string             $title            Titel
     * @param  string             $description      Beschreibung
     * @param  float              $length           Laenge
     * @param  int                $fsk              FSK
     * @param  array              $genreHashes      Genres
     * @param  string             $typeHash         Medium
     * @param  string             $caseHash         Verpackung
     * @param  int                $rating           Bewertung
     * @param  string             $dealerHash       Haendler
     * @param  float              $price            Preis
     * @param  \RWF\Date\DateTime $purchaseDate     Kaufdatum
     * @param  string             $ean              EAN Code
     * @param  bool               $isLimitedEdition Limitierte Auflage
     * @return bool
     */
    public function addMovie(
        $title,
        $description,
        $length,
        $fsk,
        array $genreHashes,
        $typeHash,
        $caseHash,
        $rating = 0,
        $dealerHash = null,
        $price = 0.0,
        DateTime $purchaseDate = null,
        $ean = '',
        $isLimitedEdition = false
    ) {

        $db = MB::getDatabase();
        $hash = md5(uniqid(microtime(true)));
        $newMovie = array(
            'hash' => $hash,
            'title' => $title,
            'description' => $description,
            'length' => $length,
            'fsk' => $fsk,
            'genres' => $genreHashes,
            'type' => $typeHash,
            'case' => $caseHash,
            'rating' => $rating,
            'dealer' => $dealerHash,
            'price' => $price,
            'purchaseDate' => ($purchaseDate instanceof DateTime ? $purchaseDate->getDatabaseDateTime() : '2000-01-01 00:00:00'),
            'ean' => $ean,
            'isLimitedEdition' => $isLimitedEdition
        );

        if($db->hSetNxArray(self::$tableName, $hash, $newMovie) == 0) {

            return false;
        }
        return true;
    }

    /**
     * bearbeitet einen Film
     *
     * @param  string             $hash             eindeutige Identifizierung
     * @param  string             $title            Titel
     * @param  string             $description      Beschreibung
     * @param  float              $length           Laenge
     * @param  int                $fsk              FSK
     * @param  array              $genreHashes      Genres
     * @param  string             $typeHash         Medium
     * @param  string             $caseHash         Verpackung
     * @param  int                $rating           Bewertung
     * @param  string             $dealerHash       Haendler
     * @param  float              $price            Preis
     * @param  \RWF\Date\DateTime $purchaseDate     Kaufdatum
     * @param  string             $ean              EAN Code
     * @param  bool               $isLimitedEdition Limitierte Auflage
     * @return bool
     */
    public function editMovie(
        $hash,
        $title = null,
        $description = null,
        $length = null,
        $fsk = null,
        array $genreHashes = null,
        $typeHash = null,
        $caseHash = null,
        $rating = null,
        $dealerHash = null,
        $price = null,
        DateTime $purchaseDate = null,
        $ean = null,
        $isLimitedEdition = null
    ) {

        $db = MB::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $hash)) {

            $movie = $db->hGetArray(self::$tableName, $hash);

            //Titel
            if ($title !== null) {

                $movie['title'] = $title;
            }

            //Beschreibung
            if ($description !== null) {

                $movie['description'] = $description;
            }

            //Laenge
            if ($length !== null) {

                $movie['length'] = $length;
            }

            //FSK
            if ($fsk !== null) {

                $movie['fsk'] = $fsk;
            }

            //Genres
            if ($genreHashes !== null) {

                $movie['genres'] = $genreHashes;
            }

            //Medium
            if ($typeHash !== null) {

                $movie['type'] = $typeHash;
            }

            //Verkackung
            if ($caseHash !== null) {

                $movie['case'] = $caseHash;
            }

            //Bewertung
            if ($rating !== null) {

                $movie['rating'] = $rating;
            }

            //Haendler
            if ($dealerHash !== null) {

                $movie['dealer'] = $dealerHash;
            }

            //Preis
            if ($price !== null) {

                $movie['price'] = $price;
            }

            //Kaufdatum
            if ($purchaseDate !== null) {

                $movie['purchaseDate'] = $purchaseDate->getDatabaseDateTime();
            }

            //EAN Code
            if ($ean !== null) {

                $movie['ean'] = $ean;
            }

            //Limitierung
            if ($isLimitedEdition !== null) {

                $movie['isLimitedEdition'] = $isLimitedEdition;
            }

            if($db->hSetArray(self::$tableName, $hash, $movie) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * Film loeschen
     *
     * @param  string $hash eindeutige Identifizierung
     * @return bool
     */
    public function removeMovie($hash) {

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
     * gibt den Filme Editor zurueck
     *
     * @return \MB\Movie\Editor\MovieEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new MovieEditor();
        }
        return self::$instance;
    }
}