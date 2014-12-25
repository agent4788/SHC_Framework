<?php

namespace PCC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\User\UserEditor;


/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListUsersAjax extends AjaxCommand {

    protected $premission = 'pcc.acp.userManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('usermanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();
        $tpl->assign('userList', UserEditor::getInstance()->listUsers(UserEditor::SORT_BY_NAME));
        $this->data = $tpl->fetchString('userlist.html');
    }

}