<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use SHC\SwitchServer\SwitchServerEditor;

/**
 * Zeigt eine Liste mit allen Schaltservern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListSwitchServersAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchserverManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchservermanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();
        $tpl->assign('switchServerList', SwitchServerEditor::getInstance()->listSwitchServers(SwitchServerEditor::SORT_BY_NAME));
        $this->data = $tpl->fetchString('listswitchservers.html');
    }

}