<?php

namespace MB\Form\Forms;

//Imports
use MB\Form\FormElements\CaseChooser;
use MB\Form\FormElements\DealerChooser;
use MB\Form\FormElements\GenreChooser;
use MB\Form\FormElements\TypeChooser;
use MB\Movie\Movie;
use MB\Movie\MovieCase;
use MB\Movie\MovieDealer;
use MB\Movie\MovieFsk;
use MB\Movie\MovieType;
use RWF\Core\RWF;
use RWF\Form\DefaultHtmlForm;
use RWF\Form\FormElements\FloatInputField;
use RWF\Form\FormElements\IntegerInputField;
use RWF\Form\FormElements\Select;
use RWF\Form\FormElements\TextArea;
use RWF\Form\FormElements\TextField;

/**
 * Film Formular
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class MovieForm extends DefaultHtmlForm {

    /**
     * @param Movie $movie
     */
    public function __construct(Movie $movie = null) {

        //Konstruktor von TabbedHtmlForm aufrufen
        parent::__construct();

        RWF::getLanguage()->disableAutoHtmlEndocde();

        //Titel
        $title = new TextField('title', ($movie instanceof Movie ? $movie->getTitle() : ''), array('minlength' => 1, 'maxlength' => 50));
        $title->setTitle(RWF::getLanguage()->get('index.movieForm.title'));
        $title->setDescription(RWF::getLanguage()->get('index.movieForm.title.description'));
        $title->requiredField(true);
        $this->addFormElement($title);

        //Beschreibung
        $description = new TextArea('description', ($movie instanceof Movie ? $movie->getDescription() : ''), array('minlength' => 1, 'maxlength' => 50));
        $description->setTitle(RWF::getLanguage()->get('index.movieForm.description'));
        $description->setDescription(RWF::getLanguage()->get('index.movieForm.description.description'));
        $description->requiredField(true);
        $this->addFormElement($description);

        //Laenge
        $length = new IntegerInputField('length', ($movie instanceof Movie ? $movie->getTitle() : 100), array('min' => 1, 'max' => 960));
        $length->setTitle(RWF::getLanguage()->get('index.movieForm.length'));
        $length->setDescription(RWF::getLanguage()->get('index.movieForm.length.description'));
        $length->requiredField(true);
        $this->addFormElement($length);

        //FSK
        $fsk = new Select('fsk');
        $fsk->setValues(array(
            MovieFsk::FSK_0 => array('keine Altersbeschränkung', ($movie instanceof Movie && $movie->getFsk() == MovieFsk::FSK_0 ? 1 : 1)),
            MovieFsk::FSK_6 => array('ab 6 Jahren', ($movie instanceof Movie && $movie->getFsk() == MovieFsk::FSK_6 ? 1 : 0)),
            MovieFsk::FSK_12 => array('ab 12 Jahren', ($movie instanceof Movie && $movie->getFsk() == MovieFsk::FSK_12 ? 1 : 0)),
            MovieFsk::FSK_16 => array('ab 16 Jahren', ($movie instanceof Movie && $movie->getFsk() == MovieFsk::FSK_16 ? 1 : 0)),
            MovieFsk::FSK_18 => array('ab 18 Jahren', ($movie instanceof Movie && $movie->getFsk() == MovieFsk::FSK_18 ? 1 : 0)),
            MovieFsk::FSK_INDEX => array('Indexiert', ($movie instanceof Movie && $movie->getFsk() == MovieFsk::FSK_INDEX ? 1 : 0)),
            MovieFsk::FSK_SPIO_JK => array('strafrechtlich unbedenklich (SPIO JK)', ($movie instanceof Movie && $movie->getFsk() == MovieFsk::FSK_SPIO_JK ? 1 : 0))
        ));
        $fsk->setTitle(RWF::getLanguage()->get('index.movieForm.fsk'));
        $fsk->setDescription(RWF::getLanguage()->get('index.movieForm.fsk.description'));
        $fsk->requiredField(true);
        $this->addFormElement($fsk);

        //Genre
        $genre = new GenreChooser('genre', ($movie instanceof Movie ? $movie->listGenres() : array()), array('size' => 10));
        $genre->setTitle(RWF::getLanguage()->get('index.movieForm.genre'));
        $genre->setDescription(RWF::getLanguage()->get('index.movieForm.genre.description'));
        $genre->requiredField(true);
        $this->addFormElement($genre);

        //Medium
        $type = new TypeChooser('type', ($movie instanceof Movie && $movie->getType() instanceof MovieType ? $movie->getType()->getHash() : null));
        $type->setTitle(RWF::getLanguage()->get('index.movieForm.type'));
        $type->setDescription(RWF::getLanguage()->get('index.movieForm.type.description'));
        $type->requiredField(true);
        $this->addFormElement($type);

        //Verpackung
        $case = new CaseChooser('case', ($movie instanceof Movie && $movie->getCase() instanceof MovieCase ? $movie->getCase()->getHash() : null));
        $case->setTitle(RWF::getLanguage()->get('index.movieForm.case'));
        $case->setDescription(RWF::getLanguage()->get('index.movieForm.case.description'));
        $case->requiredField(true);
        $this->addFormElement($case);

        //Bewertung
        $rating = new Select('rating');
        $rating->setValues(array(
            0 => array('nicht Bewertet', ($movie instanceof Movie && $movie->getRating() == 0 ? 1 : 1)),
            1 => array('1', ($movie instanceof Movie && $movie->getRating() == 1 ? 1 : 0)),
            2 => array('2', ($movie instanceof Movie && $movie->getRating() == 1 ? 1 : 0)),
            3 => array('3', ($movie instanceof Movie && $movie->getRating() == 1 ? 1 : 0)),
            4 => array('4', ($movie instanceof Movie && $movie->getRating() == 1 ? 1 : 0)),
            5 => array('5', ($movie instanceof Movie && $movie->getRating() == 1 ? 1 : 0))
        ));
        $rating->setTitle(RWF::getLanguage()->get('index.movieForm.rating'));
        $rating->setDescription(RWF::getLanguage()->get('index.movieForm.rating.description'));
        $rating->requiredField(true);
        $this->addFormElement($rating);

        //Haendler
        $dealer = new DealerChooser('dealer', ($movie instanceof Movie && $movie->getDealer() instanceof MovieDealer ? $movie->getDealer()->getHash() : null));
        $dealer->setTitle(RWF::getLanguage()->get('index.movieForm.dealer'));
        $dealer->setDescription(RWF::getLanguage()->get('index.movieForm.dealer.description'));
        $dealer->requiredField(true);
        $this->addFormElement($dealer);

        //Preis
        $price = new FloatInputField('price', ($movie instanceof Movie ? $movie->getPrice() : 0.0), array('min' => 0.0, 'max' => 200.0, 'step' => 0,01));
        $price->setTitle(RWF::getLanguage()->get('index.movieForm.price'));
        $price->setDescription(RWF::getLanguage()->get('index.movieForm.price.description'));
        $price->requiredField(true);
        $this->addFormElement($price);

        RWF::getLanguage()->enableAutoHtmlEndocde();
    }
}