<?php

namespace MB\Movie;

//Imports

/**
 * Film Reihe
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class MovieCollection {

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
     * Filme
     *
     * @var array
     */
    protected $movies = array();

    /**
     * Verpackung
     *
     * @var \MB\Movie\MovieCase
     */
    protected $case = null;

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
     * setzt den Hash
     *
     * @param  string $hash
     * @return \MB\Movie\MovieCollection
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
     * @return \MB\Movie\MovieCollection
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
     * @return \MB\Movie\MovieCollection
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
     * fuegt einen Film hinzu
     *
     * @param  Movie $movie
     * @return \MB\Movie\MovieCollection
     */
    public function addMovie(Movie $movie) {

        $this->movies[] = $movie;
        return $this;
    }

    /**
     * entfernt einen Film
     *
     * @param  Movie $genre
     * @return \MB\Movie\MovieCollection
     */
    public function removeMovie(Movie $movie) {

        $this->movies = array_diff($this->genres, array($movie));
        return $this;
    }

    /**
     * entfernt alle Filme
     *
     * @return \MB\Movie\MovieCollection
     */
    public function removeAllMovies() {

        $this->movies = array();
        return $this;
    }

    /**
     * gibt eine Liste aller Filme zurueck
     *
     * @return array
     */
    public function listMovies() {

        return $this->movies;
    }

    /**
     * setzt die Verpackung
     *
     * @param  MovieCase $type
     * @return \MB\Movie\MovieCollection
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
     * setzt die EAN des Films
     *
     * @param  string $ean
     * @return \MB\Movie\MovieCollection
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
     * @return \MB\Movie\MovieCollection
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
     * gibt die Bewertung des Films zurueck
     *
     * @return float
     */
    public function getRating() {

        $sum = 0;
        $count = 0;
        foreach($this->movies as $movie) {

            /* @var $movie \MB\Movie\Movie */
            $sum += $movie->getRating();
            $count++;
        }
        if($count > 0) {

            return floor($sum / $count);
        }
        return 0;
    }

    /**
     * gibt die FSK des Films zurueck
     *
     * @return integer
     */
    public function getFsk() {

        $fsk = 0;
        foreach($this->movies as $movie) {

            /* @var $movie \MB\Movie\Movie */
            ($movie->getFsk() >  $fsk ? $fsk = $movie->getFsk() : null);
        }
        return $fsk;
    }

    /**
     * gibt die Laenge der Collection zurueck
     *
     * @return float
     */
    public function getLength() {

        $length = 0;
        foreach($this->movies as $movie) {

            /* @var $movie \MB\Movie\Movie */
            $length += $movie->getLength();
        }
        return $length;
    }

    /**
     * gibt den Preis des Films zurueck
     *
     * @return float
     */
    public function getPrice() {

        $price = 0.0;
        foreach($this->movies as $movie) {

            /* @var $movie \MB\Movie\Movie */
            $price += $movie->getPrice();
        }
        return $price;
    }
}