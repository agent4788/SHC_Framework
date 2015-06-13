<?php

namespace MB\Form\FormElements;

//Imports
use MB\Movie\Editor\MovieGenreEditor;
use RWF\Form\FormElements\SelectMultiple;

/**
 * Auswahlfeld des Genres
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class GenreChooser extends SelectMultiple {

    public function __construct($name, array $selectedGenres = array()) {

        //Allgemeine Daten
        $this->setName($name);

        //Genre Liste Vorbereiten
        $selectedGenreHashes = array();
        foreach($selectedGenres as $genre) {

            /** @var $genre \MB\Movie\MovieGenre */
            $selectedGenreHashes[] = $genre->getHash();
        }

        //Auswahl
        $values = array();
        $genres = MovieGenreEditor::getInstance()->listGenres(MovieGenreEditor::SORT_BY_NAME);
        foreach($genres as $genre) {

            /** @var $genre \MB\Movie\MovieGenre */
            $values[$genre->getHash()] = array($genre->getName(), in_array($genre->getHash(), $selectedGenreHashes));
        }
        $this->setValues($values);
    }
}