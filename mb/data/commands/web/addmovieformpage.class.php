<?php

namespace MB\Command\Web;

//Imports
use MB\Core\MB;
use MB\Form\Forms\MovieForm;
use RWF\Request\Commands\PageCommand;

/**
 * Startseite
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.4-0
 * @version    2.0.4-0
 */
class AddMovieFormPage extends PageCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var string
     */
    protected $requiredPremission = 'mb.ucp.editMovies';

    /**
     * Template
     *
     * @var String
     */
    protected $template = 'movieform.html';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'form');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = MB::getTemplate();

        //Header Daten
        $tpl->assign('apps', MB::listApps());
        $tpl->assign('style', MB::getStyle());
        $tpl->assign('user', MB::getVisitor());

        //Formular erstellen
        $movieForm = new MovieForm();
        $movieForm->setAction('index.php?app=mb&page=addmovieform');
        $movieForm->addId('shc-view-form-addRoom');

        if($movieForm->isSubmitted() && $movieForm->validate() === true) {


        } else {

            $tpl->assign('movieForm', $movieForm);
        }
    }
}