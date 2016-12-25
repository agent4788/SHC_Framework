<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Request\Commands\PageCommand;
use SHC\Core\SHC;

/**
 * Startseite Administration
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
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
     * @var string
     */
    protected $requiredPremission = 'shc.acp.menu';

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

        SHC::getTemplate()->assign('apps', SHC::listApps());
        SHC::getTemplate()->assign('acp', true);
        SHC::getTemplate()->assign('acpMenue', true);
        SHC::getTemplate()->assign('style', SHC::getStyle());
        SHC::getTemplate()->assign('user', SHC::getVisitor());
    }
}