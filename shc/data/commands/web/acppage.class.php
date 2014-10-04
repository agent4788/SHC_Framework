<?php

namespace SHC\Command\Web;

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
     * @var type 
     */
    protected $requiredPremission = 'shc.acp.menue';
    
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

        SHC::getTemplate()->assign('acp', true);
        SHC::getTemplate()->assign('style', SHC::getStyle());
        SHC::getTemplate()->assign('user', SHC::getVisitor());
    }
}
