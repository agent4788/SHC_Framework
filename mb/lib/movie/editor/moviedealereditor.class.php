<?php

namespace MB\Movie\Editor;

//Imports
use MB\Core\MB;
use MB\Movie\MovieDealer;

/**
 * Film Haendler Editor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.2.0-0
 * @version    2.2.0-0
 */

class MovieDealerEditor {

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
     * @var \MB\Movie\Editor\MovieDealerEditor
     */
    protected static $instance = null;

    /**
     * Liste mit allen Haendlern
     *
     * @var Array
     */
    protected $dealer = array();

    /**
     * name der HashMap
     *
     * @var String
     */
    protected static $tableName = 'movieDealer';

    protected function __construct() {

        $this->loadData();
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {

    }

    /**
     * Haendler aus der Datenbank laden und Objekte erzeugen
     */
    public function loadData() {

        //alte Daten loeschen
        $this->dealer = array();

        $dealers = MB::getDatabase()->hGetAllArray(self::$tableName);
        foreach($dealers as $dealer) {

            $hash = $dealer['hash'];
            $dealerObject = new MovieDealer();
            $dealerObject->setHash($hash);
            $dealerObject->setName($dealer['name']);
            $dealerObject->setLink($dealer['link']);
            $dealerObject->setIcon($dealer['icon']);

            $this->dealer[$hash] = $dealerObject;
        }
    }

    /**
     * gibt den Haendler zugehoerig zum Hash zurueck
     *
     * @param  string $hash
     * @return \MB\Movie\MovieDealer
     */
    public function getMovieDealerByHash($hash) {

        if (isset($this->dealer[$hash])) {

            return $this->dealer[$hash];
        }
        return null;
    }

    /**
     * gibt eine Liste mir allen Haendlern zurueck
     *
     * @param  String $orderBy Art der Sortierung (
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listMovieDealer($orderBy = 'name') {

        if ($orderBy == 'name') {

            //nach Namen sortieren
            $dealer = $this->dealer;

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
            usort($dealer, $orderFunction);
            return $dealer;
        }
        return $this->dealer;
    }

    /**
     * erstellt einen neuen Haendler
     *
     * @param  string $name Name
     * @param  string $link Link
     * @param  string $icon Icon
     * @return bool
     */
    public function addDealer($name, $link, $icon) {

        $db = MB::getDatabase();
        $hash = md5(uniqid(microtime(true)));
        $newDealer = array(
            'hash' => $hash,
            'name' => $name,
            'link' => $link,
            'icon' => $icon
        );

        if($db->hSetNxArray(self::$tableName, $hash, $newDealer) == 0) {

            return false;
        }
        return true;
    }

    /**
     * bearbeitet einen Haendler
     *
     * @param  string $hash eindeutige Identifizierung
     * @param  string $name Name
     * @param  string $link Link
     * @param  string $icon Icon
     * @return bool
     */
    public function editDealer($hash, $name = null, $link = null, $icon = null) {

        $db = MB::getDatabase();
        //pruefen ob Datensatz existiert
        if($db->hExists(self::$tableName, $hash)) {

            $dealer = $db->hGetArray(self::$tableName, $hash);

            //Name
            if ($name !== null) {

                $dealer['name'] = $name;
            }

            //Link
            if ($link !== null) {

                $dealer['link'] = $link;
            }

            //Icon
            if ($icon !== null) {

                $dealer['name'] = $icon;
            }

            if($db->hSetArray(self::$tableName, $hash, $dealer) == 0) {

                return true;
            }
        }
        return false;
    }

    /**
     * loescht einen Haendler
     *
     * @param  string $hash eindeutige Identifizierung
     * @return bool
     */
    public function removeDealer($hash) {

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
     * gibt den Editor zurueck
     *
     * @return \MB\Movie\Editor\MovieDealerEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new MovieDealerEditor();
        }
        return self::$instance;
    }
}