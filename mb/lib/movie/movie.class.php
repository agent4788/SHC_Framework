<?php

namespace MB\Movie;

//Imports

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
     * beschreibung des Films
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
}