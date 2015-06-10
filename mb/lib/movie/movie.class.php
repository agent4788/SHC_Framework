<?php

namespace MB\Movie;

//Imports
use RWF\Date\DateTime;

/**
 * Film
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class Movie {

    /**
     * eindeutiger Hash
     *
     * @var string
     */
    protected $hash = '';

    /**
     * Titel des Films
     *
     * @var string
     */
    protected $title = '';

    /**
     * Beschreibung des Films
     *
     * @var string
     */
    protected $description = '';

    /**
     * laemnge in Minuten
     *
     * @var int
     */
    protected $length = 0;

    /**
     * Altersfreigabe (Konstante aus dem MovieFsk Interface)
     *
     * @var int
     */
    protected $fsk = 0;

    /**
     * Genres
     *
     * @var array
     */
    protected $genres = array();

    /**
     * Typ (Medium)
     *
     * @var \MB\Movie\MovieType
     */
    protected $type = null;

    /**
     * Verpackung
     *
     * @var \MB\Movie\MovieCase
     */
    protected $case = null;

    /**
     * Bewertungen
     *
     * @var array
     */
    protected $rating = array();

    /**
     * Haendler
     *
     * @var \MB\Movie\MovieDealer
     */
    protected $dealer = null;

    /**
     * Preis
     *
     * @var float
     */
    protected $price = 0.0;

    /**
     * Kaufdatum
     *
     * @var \RWF\Date\DateTime
     */
    protected $purchaseDate = null;

    /**
     * EAN Code
     *
     * @var string
     */
    protected $ean = '';

    /**
     * gibt an ob es sich um eine Limited Edition handelt
     *
     * @var bool
     */
    protected $isLimitedEdition = false;

    /**
     * Datum an dem der Film zuletzt angesehen wurde
     *
     * @var \RWF\Date\DateTime
     */
    protected $lastViewDate = null;

    /**
     * setzt den Hash
     *
     * @param  string $hash
     * @return \MB\Movie\Movie
     */
    public function setHash($hash) {

        $this->hash = $hash;
        return $this;
    }

    /**
     * gibt den Hash zurueck
     *
     * @return string
     */
    public function getHash() {

        return $this->hash;
    }

    /**
     * setzt den Titel des Films
     *
     * @param  string $title
     * @return \MB\Movie\Movie
     */
    public function setTitle($title) {

        $this->title = $title;
        return $this;
    }

    /**
     * gibt den Titel des Films zurueck
     *
     * @return string
     */
    public function getTitle() {

        return $this->title;
    }

    /**
     * setzt die Beschreibung des Films
     *
     * @param  string $description
     * @return \MB\Movie\Movie
     */
    public function setDescription($description) {

        $this->description = $description;
        return $this;
    }

    /**
     * gibt die Beschreibung des Films zurueck
     *
     * @return string
     */
    public function getDescription() {

        return $this->description;
    }

    /**
     * setzt die Laenge des Films
     *
     * @param  integer $length
     * @return \MB\Movie\Movie
     */
    public function setLength($length) {

        $this->length = $length;
        return $this;
    }

    /**
     * gibt die Laenge des Films zurueck
     *
     * @return integer
     */
    public function getLength() {

        return $this->length;
    }

    /**
     * setzt die FSK des Films
     *
     * @param  integer $fsk
     * @return \MB\Movie\Movie
     */
    public function setFsk($fsk) {

        $this->fsk = $fsk;
        return $this;
    }

    /**
     * gibt die FSK des Films zurueck
     *
     * @return integer
     */
    public function getFsk() {

        return $this->fsk;
    }

    /**
     * fuegt ein Genre hinzu
     *
     * @param  MovieGenre $genre
     * @return \MB\Movie\Movie
     */
    public function addGenre(MovieGenre $genre) {

        $this->genres[] = $genre;
        return $this;
    }

    /**
     * entfernt ein Genre
     *
     * @param  MovieGenre $genre
     * @return \MB\Movie\Movie
     */
    public function removeGenre(MovieGenre $genre) {

        $this->genres = array_diff($this->genres, array($genre));
        return $this;
    }

    /**
     * entfernt alle Genre
     *
     * @return \MB\Movie\Movie
     */
    public function removeAllGenres() {

        $this->genres = array();
        return $this;
    }

    /**
     * gibt eine Liste aller Genre zurueck
     *
     * @return array
     */
    public function listGenres() {

        return $this->genres;
    }

    /**
     * setzt das Medium
     *
     * @param  MovieType $type
     * @return \MB\Movie\Movie
     */
    public function setType(MovieType $type) {

        $this->type = $type;
        return $this;
    }

    /**
     * gibt das Medium zurueck
     *
     * @return \MB\Movie\MovieType
     */
    public function getType() {

        return $this->type;
    }

    /**
     * setzt die Verpackung
     *
     * @param  MovieCase $type
     * @return \MB\Movie\Movie
     */
    public function setCase(MovieCase $case) {

        $this->case = $case;
        return $this;
    }

    /**
     * gibt die Verpackung zurueck
     *
     * @return \MB\Movie\MovieCase
     */
    public function getCase() {

        return $this->case;
    }

    /**
     * setzt die Bewertung des Films
     *
     * @param  float $rating
     * @return \MB\Movie\Movie
     */
    public function setRating($rating) {

        $this->rating = $rating;
        return $this;
    }

    /**
     * gibt die Bewertung des Films zurueck
     *
     * @return float
     */
    public function getRating() {

        return $this->rating;
    }

    /**
     * setzt den Haendler
     *
     * @param  MovieDealer $dealer
     * @return \MB\Movie\Movie
     */
    public function setDealer(MovieDealer $dealer) {

        $this->dealer = $dealer;
        return $this;
    }

    /**
     * gibt den Haendler zurueck
     *
     * @return \MB\Movie\MovieDealer
     */
    public function getDealer() {

        return $this->dealer;
    }

    /**
     * setzt den Preis des Films
     *
     * @param  float $price
     * @return \MB\Movie\Movie
     */
    public function setPrice($price) {

        $this->price = $price;
        return $this;
    }

    /**
     * gibt den Preis des Films zurueck
     *
     * @return float
     */
    public function getPrice() {

        return $this->price;
    }

    /**
     * setzt das Kaufdatum des Films
     *
     * @param  DateTime $date
     * @return \MB\Movie\Movie
     */
    public function setPurchaseDate(DateTime $date) {

        $this->purchaseDate = $date;
        return $this;
    }

    /**
     * gibt das Kaufdatum des Films zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getPurchaseDate() {

        return $this->purchaseDate;
    }

    /**
     * setzt die EAN des Films
     *
     * @param  string $ean
     * @return \MB\Movie\Movie
     */
    public function setEan($ean) {

        $this->ean = $ean;
        return $this;
    }

    /**
     * gibt die EAN des Films zurueck
     *
     * @return string
     */
    public function getEan() {

        return $this->ean;
    }

    /**
     * gibt an ob es sich um eine Limitierte Edition handelt
     *
     * @param  bool $enabled
     * @return \MB\Movie\Movie
     */
    public function enableLimitedEdition($enabled) {

        $this->isLimitedEdition = ($enabled == true ? true : false);
        return $this;
    }

    /**
     * gibt an ob es sich um eine Limitierte Edition handelt
     *
     * @return bool
     */
    public function isLimitedEdition() {

        return $this->isLimitedEdition;
    }

    /**
     * setzt das Datum wann der Film zuletzt angeschaut wurde
     *
     * @param  DateTime $date
     * @return \MB\Movie\Movie
     */
    public function setLastViewDate(DateTime $date) {

        $this->lastViewDate = $date;
        return $this;
    }

    /**
     * gibt das Datum wann der Film zuletzt angeschaut wurde zurueck
     *
     * @return \RWF\Date\DateTime
     */
    public function getLastViewDate() {

        return $this->lastViewDate;
    }
}