<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use SHC\Core\SHC;
use SHC\UserAtHome\UserAtHomeEditor;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListUsersAtHomePage extends PageCommand {

    protected $template = 'listusersathome.html';

    protected $premission = 'shc.acp.usersathomeManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'usersathomemanagement', 'acpindex');

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
        $tpl->assign('backLink', 'index.php?app=shc&m&page=acp');
        $tpl->assign('device', SHC_DETECTED_DEVICE);
        if(RWF::getSession()->getMessage() != null) {
            $tpl->assign('message', RWF::getSession()->getMessage());
            RWF::getSession()->removeMessage();
        }

        //User auflisten
        $tpl->assign('userAtHomeList', UserAtHomeEditor::getInstance()->listUsersAtHome(UserAtHomeEditor::SORT_BY_ORDER_ID));
    }

}