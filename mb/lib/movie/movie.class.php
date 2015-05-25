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
     * Altersfreigabe
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


    protected $rating = array();

    protected $dealer = null;

    protected $price = 0.0;

    protected $purchaseDate = null;

    protected $ean = '';

    protected $isLimitedEdition = false;

    protected $lastViewDate = null;

    protected $isLend = false;

    protected $lendTo = '';
}