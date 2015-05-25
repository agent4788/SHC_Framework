<?php

namespace MB\Movie;

//Imports

/**
 * Film Medium
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class MovieType {

    /**
     * eindeutiger Hash
     *
     * @var string
     */
    protected $hash = '';

    /**
     * Name des Mediums
     *
     * @var string
     */
    protected $name = '';

    /**
     * setzt den Hash
     *
     * @param  string $hash
     * @return \MB\Movie\MovieType
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
     * setzt den Name des Mediums
     *
     * @param  string $name
     * @return \MB\Movie\MovieType
     */
    public function setName($name) {

        $this->name = $name;
        return $this;
    }

    /**
     * gibt den Namen des Mediums zurueck
     *
     * @return string
     */
    public function getName() {

        return $this->name;
    }
}