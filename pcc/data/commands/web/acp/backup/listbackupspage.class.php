<?php

namespace PCC\Command\Web;

//Imports
use PCC\Core\PCC;
use RWF\Backup\BackupEditor;
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;

/**
 * listet alle Backups auf
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListBackupsPage extends PageCommand {

    protected $requiredPremission = 'pcc.acp.backupsManagement';

    protected $template = 'listbackups.html';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'backupsmanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Header Daten
        $tpl->assign('apps', PCC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', PCC::getStyle());
        $tpl->assign('user', PCC::getVisitor());

        //Meldungen
        if(RWF::getSession()->getMessage() != null) {
            $tpl->assign('message', RWF::getSession()->getMessage());
            RWF::getSession()->removeMessage();
        }

        $tpl->assign('backupList', BackupEditor::getInstance()->setPath(PATH_RWF_BACKUP)->listBackups(BackupEditor::SORT_BY_NAME));
    }

}