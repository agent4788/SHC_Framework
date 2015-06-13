<?php

namespace MB\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;

/**
 * Filme auflisten
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListMoviesAjax extends AjaxCommand {

    protected $requiredPremission = 'mb.ucp.viewMovies';

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

        //Template anzeigen
        $tpl = RWF::getTemplate();
        $this->data = $tpl->fetchString('listmovies.html');
    }
}