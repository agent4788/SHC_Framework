<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\PageCommand;
use RWF\User\UserEditor;
use SHC\Condition\ConditionEditor;
use SHC\Core\SHC;
use SHC\Event\EventEditor;
use SHC\Room\RoomEditor;
use SHC\Switchable\SwitchableEditor;
use SHC\SwitchServer\SwitchServerEditor;
use SHC\Timer\SwitchPointEditor;

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

        $tpl = SHC::getTemplate();

        //Header Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());
    }
}