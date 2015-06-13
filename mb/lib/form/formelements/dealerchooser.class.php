<?php

namespace MB\Form\FormElements;

//Imports
use MB\Movie\Editor\MovieDealerEditor;
use RWF\Form\FormElements\Select;

/**
 * Auswahlfeld der Verpackung
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DealerChooser extends Select {

    public function __construct($name, $selectedDealer = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Auswahl
        $values = array();
        $dealers = MovieDealerEditor::getInstance()->listMovieDealer(MovieDealerEditor::SORT_BY_NAME);
        foreach($dealers as $dealer) {

            /** @var $dealer \MB\Movie\MovieDealer */
            $values[$dealer->getHash()] = array($dealer->getName(), ($dealer->getHash() == $selectedDealer ? 1 : 0));
        }
        $this->setValues($values);
    }
}