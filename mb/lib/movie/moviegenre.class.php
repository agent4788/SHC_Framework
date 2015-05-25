<?php

namespace MB\Movie;

//Imports

/**
 * Film Genres
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class MovieGenre {

    /**
     * eindeutiger Hash
     *
     * @var string
     */
    protected $hash = '';

    /**
     * Name des Genres
     *
     * @var string
     */
    protected $name = '';

    /**
     * Icon
     *
     * @var string
     */
    protected $icon = '';

    /**
     * setzt den Hash
     *
     * @param  string $hash
     * @return \MB\Movie\MovieGenre
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
     * setzt den Name des Genres
     *
     * @param  string $name
     * @return \MB\Movie\MovieGenre
     */
    public function setName($name) {

        $this->name = $name;
        return $this;
    }

    /**
     * gibt den Namen des Genres zurueck
     *
     * @return string
     */
    public function getName() {

        return $this->name;
    }

    /**
     * setzt das Icon des Genres
     *
     * @param  string $icon
     * @return \MB\Movie\MovieGenre
     */
    public function setIcon($icon) {

        $this->icon = $icon;
        return $this;
    }

    /**
     * gibt das Icon des Genres zurueck
     *
     * @return string
     */
    public function getIcon() {

        return $this->icon;
    }
}