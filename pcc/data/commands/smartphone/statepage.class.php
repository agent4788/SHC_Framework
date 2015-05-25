<?php

namespace PCC\Command\Smartphone;

//Imports
use PCC\Core\PCC;
use RWF\Request\Commands\PageCommand;

/**
 * Startseite
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class StatePage extends PageCommand {

    /**
     * Template
     *
     * @var String
     */
    protected $template = 'state.html';

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'pcc.ucp.viewSysState';

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

        $tpl = PCC::getTemplate();

        //Headline Daten
        $tpl->assign('apps', PCC::listApps());
        $tpl->assign('style', PCC::getStyle());
        $tpl->assign('user', PCC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=pcc&m&page=index');
        $tpl->assign('device', PCC_DETECTED_DEVICE);
        $tpl->assign('title', PCC::getLanguage()->get('index.tabs.state'));
    }
}