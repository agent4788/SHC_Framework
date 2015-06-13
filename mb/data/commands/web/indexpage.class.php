<?php

namespace MB\Command\Web;

//Imports
use MB\Core\MB;
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

        $tpl = MB::getTemplate();
        $tpl->assign('apps', MB::listApps());
        $tpl->assign('style', MB::getStyle());
        $tpl->assign('user', MB::getVisitor());
    }
}