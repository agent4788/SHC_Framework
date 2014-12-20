<?php

namespace PCC\Command\Web;

//Imports
use RWF\Request\Commands\PageCommand;
use PCC\Core\PCC;

/**
 * Startseite
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.4-0
 * @version    2.0.4-0
 */
class IndexPage extends PageCommand {

    /**
     * Template
     *
     * @var String
     */
    protected $template = 'indexpage.html';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        PCC::getTemplate()->assign('style', PCC::getStyle());
        PCC::getTemplate()->assign('user', PCC::getVisitor());
    }
}