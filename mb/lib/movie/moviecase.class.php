<?php

namespace MB\Movie;

//Imports

/**
 * Film Verpackung
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class MovieCase {

    /**
     * eindeutiger Hash
     *
     * @var string
     */
    protected $hash = '';

    /**
     * Name der Verpackung
     *
     * @var string
     */
    protected $name = '';

    /**
     * setzt den Hash
     *
     * @param  string $hash
     * @return \MB\Movie\MovieCase
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
     * setzt den Name der Verpackung
     *
     * @param  string $name
     * @return \MB\Movie\MovieCase
     */
    public function setName($name) {

        $this->name = $name;
        return $this;
    }

    /**
     * gibt den Namen der Verpackung zurueck
     *
     * @return string
     */
    public function getName() {

        return $this->name;
    }
}