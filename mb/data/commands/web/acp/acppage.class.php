<?php

namespace MB\Command\Web;

//Imports
use MB\Core\MB;
use RWF\Request\Commands\PageCommand;

/**
 * Startseite Administration
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AcpPage extends PageCommand {

    /**
     * Template
     *
     * @var String
     */
    protected $template = 'acppage.html';

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'mb.acp.menu';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        MB::getTemplate()->assign('apps', MB::listApps());
        MB::getTemplate()->assign('acp', true);
        MB::getTemplate()->assign('style', MB::getStyle());
        MB::getTemplate()->assign('user', MB::getVisitor());
    }
}