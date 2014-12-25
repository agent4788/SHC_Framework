<?php

namespace PCC\Command\Web;

//Imports
use PCC\Core\PCC;
use RWF\Request\Commands\PageCommand;

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
     * @var type
     */
    protected $requiredPremission = 'pcc.acp.menu';

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

        PCC::getTemplate()->assign('apps', PCC::listApps());
        PCC::getTemplate()->assign('acp', true);
        PCC::getTemplate()->assign('style', PCC::getStyle());
        PCC::getTemplate()->assign('user', PCC::getVisitor());
    }
}