<?php

namespace PCC\Command\Smartphone;

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

        $tpl = PCC::getTemplate();

        //Headline Daten
        $tpl->assign('apps', PCC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('acpMenue', true);
        $tpl->assign('style', PCC::getStyle());
        $tpl->assign('user', PCC::getVisitor());
        $tpl->assign('device', PCC_DETECTED_DEVICE);
    }
}