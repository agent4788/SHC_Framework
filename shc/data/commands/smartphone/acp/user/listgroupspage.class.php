<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\User\UserEditor;
use SHC\Core\SHC;

/**
 * Zeigt eine Liste mit allen Benutzergruppen an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListGroupsPage extends PageCommand {

    protected $template = 'groupslist.html';

    protected $premission = 'shc.acp.userManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'usermanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Headline Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=shc&m&page=listusers');
        $tpl->assign('device', SHC_DETECTED_DEVICE);
        if(RWF::getSession()->getMessage() != null) {
            $tpl->assign('message', RWF::getSession()->getMessage());
            RWF::getSession()->removeMessage();
        }

        //Benutzer Liste
        $tpl->assign('groupList', UserEditor::getInstance()->listUserGruops(UserEditor::SORT_BY_NAME));
    }

}