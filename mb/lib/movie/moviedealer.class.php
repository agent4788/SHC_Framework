<?php

namespace MB\Movie;

//Imports

/**
 * Film Haendler
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class MovieDealer {

    /**
     * eindeutiger Hash
     *
     * @var string
     */
    protected $hash = '';

    /**
     * Name des Haendlers
     *
     * @var string
     */
    protected $name = '';

    /**
     * Link zur Webseite
     *
     * @var string
     */
    protected $link = '';

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
     * @return \MB\Movie\MovieDealer
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
     * setzt den Name des Haendlers
     *
     * @param  string $name
     * @return \MB\Movie\MovieDealer
     */
    public function setName($name) {

        $this->name = $name;
        return $this;
    }

    /**
     * gibt den Namen des Haendlers zurueck
     *
     * @return string
     */
    public function getName() {

        return $this->name;
    }

    /**
     * setzt den Link des Haendlers
     *
     * @param  string $link
     * @return \MB\Movie\MovieDealer
     */
    public function setLink($link) {

        $this->link = $link;
        return $this;
    }

    /**
     * gibt den Link des Haendlers zurueck
     *
     * @return string
     */
    public function getLink() {

        return $this->link;
    }

    /**
     * setzt das Icon des Haendlers
     *
     * @param  string $icon
     * @return \MB\Movie\MovieDealer
     */
    public function setIcon($icon) {

        $this->icon = $icon;
        return $this;
    }

    /**
     * gibt das Icon des Haendlers zurueck
     *
     * @return string
     */
    public function getIcon() {

        return $this->icon;
    }
}